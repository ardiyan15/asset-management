<?php
defined('BASEPATH') or exit('No direct script access allowed');

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

/**
 * Halaman Audit Log (khusus admin): siapa melakukan apa, kapan, dan dari mana.
 * Log hanya bisa dibaca & diekspor; tidak ada ubah/hapus dari aplikasi.
 * (Nama controller "Activity" supaya tidak bentrok dengan library Audit.)
 */
class Activity extends CI_Controller
{
    const EXPORT_LIMIT = 50000;

    public function __construct()
    {
        parent::__construct();
        $this->load->model('Auth_model', 'Auth');
        $this->load->model('Audit_model', 'AuditLog');
        is_logged_in();
        is_allowed();
    }

    public function index()
    {
        $filters = $this->_filters();
        $data = [
            'title'     => 'Audit Log',
            'user'      => $this->Auth->get_active_user($this->session->userdata('username')),
            'filters'   => $filters,
            'summary'   => $this->AuditLog->summary($filters),
            'modules'   => Audit_model::MODULES,
            'actions'   => $this->AuditLog->actions(),
            'users'     => $this->AuditLog->users(),
            'buildings' => $this->db->order_by('name')->get_where('buildings', ['status' => 1])->result_array(),
            'query'     => http_build_query(array_filter($filters)),
        ];

        $this->load->view('templates/header', $data);
        $this->load->view('templates/sidebar', $data);
        $this->load->view('templates/topbar', $data);
        $this->load->view('activity/index', $data);
        $this->load->view('templates/footer');
    }

    /**
     * DataTables server-side (log bisa sangat banyak, jadi tidak dimuat sekaligus).
     */
    public function data()
    {
        $filters = $this->_filters();
        $search  = $this->input->post('search');
        if (!empty($search['value'])) {
            $filters['q'] = trim($search['value']);
        }

        $start  = max(0, (int) $this->input->post('start'));
        $length = (int) $this->input->post('length');
        $length = $length > 0 && $length <= 200 ? $length : 50;

        $rows = [];
        foreach ($this->AuditLog->get($filters, $length, $start) as $log) {
            $rows[] = [
                'time'        => date('d M Y H:i:s', strtotime($log['created_at'])),
                'user'        => html_escape($log['username'] ?? '-'),
                'role'        => $log['role_id'] == 1 ? 'admin' : ($log['role_id'] ? 'user' : ''),
                'action'      => html_escape($log['action']),
                'module'      => html_escape(Audit_model::MODULES[$log['module']] ?? $log['module']),
                'description' => html_escape($log['description']),
                'entity'      => html_escape(trim(($log['entity_type'] ?? '') . ' ' . ($log['entity_label'] ?? ''))),
                'building'    => html_escape($log['building_name'] ?? ''),
                'ip'          => html_escape($log['ip_address'] ?? ''),
                'agent'       => html_escape($log['user_agent'] ?? ''),
                'url'         => html_escape($log['url'] ?? ''),
                'changes'     => $log['changes'] ? json_decode($log['changes'], true) : null,
            ];
        }

        $this->output->set_content_type('application/json')->set_output(json_encode([
            'draw'            => (int) $this->input->post('draw'),
            'recordsTotal'    => $this->db->count_all('audit_logs'),
            'recordsFiltered' => $this->AuditLog->count($filters),
            'data'            => $rows,
        ]));
    }

    public function export()
    {
        $filters = $this->_filters();
        $total   = $this->AuditLog->count($filters);
        $logs    = $this->AuditLog->get($filters, self::EXPORT_LIMIT, 0);

        // Ekspor audit log juga dicatat
        $this->audit->log('report.export', 'Ekspor audit log ' . $filters['date_from'] . ' s.d. ' . $filters['date_to'] . ' (Excel, ' . count($logs) . ' baris)');

        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setTitle('Audit Log');
        $sheet->fromArray(['Waktu', 'User', 'Aksi', 'Modul', 'Keterangan', 'Data', 'Gedung', 'Perubahan', 'IP', 'User Agent', 'URL'], null, 'A1');

        $r = 2;
        foreach ($logs as $log) {
            $changes = '';
            if ($log['changes']) {
                $parts = [];
                foreach (json_decode($log['changes'], true) ?: [] as $field => $c) {
                    $old = is_scalar($c['old'] ?? null) ? (string) $c['old'] : json_encode($c['old'] ?? null);
                    $new = is_scalar($c['new'] ?? null) ? (string) $c['new'] : json_encode($c['new'] ?? null);
                    $parts[] = $field . ': ' . ($old === '' ? '-' : $old) . ' → ' . ($new === '' ? '-' : $new);
                }
                $changes = implode("\n", $parts);
            }
            $sheet->fromArray([
                $log['created_at'], $log['username'], $log['action'], Audit_model::MODULES[$log['module']] ?? $log['module'],
                $log['description'], trim(($log['entity_type'] ?? '') . ' ' . ($log['entity_label'] ?? '')), $log['building_name'],
                $changes, $log['ip_address'], $log['user_agent'], $log['url'],
            ], null, 'A' . $r, true);
            $r++;
        }
        if ($total > self::EXPORT_LIMIT) {
            $sheet->setCellValue('A' . ($r + 1), 'Dibatasi ' . self::EXPORT_LIMIT . ' baris terbaru dari ' . $total . '. Persempit filter tanggal untuk ekspor lengkap.');
        }

        $sheet->getStyle('A1:K1')->getFont()->setBold(true)->getColor()->setRGB('FFFFFF');
        $sheet->getStyle('A1:K1')->getFill()->setFillType(Fill::FILL_SOLID)->getStartColor()->setRGB('4E73DF');
        $sheet->getStyle('H2:H' . max(2, $r))->getAlignment()->setWrapText(true);
        foreach (['A' => 20, 'B' => 16, 'C' => 26, 'D' => 18, 'E' => 60, 'F' => 26, 'G' => 16, 'H' => 45, 'I' => 15, 'J' => 30, 'K' => 30] as $col => $w) {
            $sheet->getColumnDimension($col)->setWidth($w);
        }
        $sheet->freezePane('A2');

        $filename = 'audit-log-' . $filters['date_from'] . '-sd-' . $filters['date_to'] . '.xlsx';
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment; filename="' . $filename . '"');
        header('Cache-Control: max-age=0');
        (new Xlsx($spreadsheet))->save('php://output');
        exit;
    }

    /**
     * Filter dari query string (GET halaman/ekspor; POST dari DataTables membawa query string yang sama).
     * Default 7 hari terakhir.
     */
    private function _filters()
    {
        $date = function ($v, $default) {
            $d = DateTime::createFromFormat('Y-m-d', (string) $v);
            return $d && $d->format('Y-m-d') === $v ? $v : $default;
        };
        $module = (string) $this->input->get('module');

        $f = [
            'date_from'   => $date($this->input->get('date_from'), date('Y-m-d', strtotime('-6 days'))),
            'date_to'     => $date($this->input->get('date_to'), date('Y-m-d')),
            'user_id'     => (int) $this->input->get('user_id'),
            'module'      => array_key_exists($module, Audit_model::MODULES) ? $module : '',
            'action'      => preg_match('/^[a-z_.]{1,60}$/', (string) $this->input->get('action')) ? (string) $this->input->get('action') : '',
            'building_id' => (int) $this->input->get('building_id'),
            'q'           => mb_substr(trim((string) $this->input->get('q', true)), 0, 100),
        ];
        if ($f['date_from'] > $f['date_to']) {
            [$f['date_from'], $f['date_to']] = [$f['date_to'], $f['date_from']];
        }
        return $f;
    }
}
