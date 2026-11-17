<?php
defined('BASEPATH') or exit('No direct script access allowed');

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Cell\DataType;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

/**
 * Jadwal maintenance aset (mis. servis AC tiap 3 bulan) + riwayat perawatan/perbaikan & biayanya.
 * Admin mengelola semua gedung, user mengelola aset di gedungnya sendiri.
 * Pengingat email dikirim oleh Cron::maintenance_reminders (CLI).
 */
class Maintenance extends CI_Controller
{
    const STATUS_TABS = [
        'due'      => 'Perlu dikerjakan',
        'upcoming' => 'Terjadwal',
        'inactive' => 'Nonaktif',
        'all'      => 'Semua',
    ];

    public function __construct()
    {
        parent::__construct();
        $this->load->model('Auth_model', 'Auth');
        $this->load->model('Maintenance_model', 'Maintenance');
        $this->load->library('encryption');
        $this->load->helper(['url_crypto', 'maintenance']);
        is_logged_in();
    }

    public function index()
    {
        $user   = $this->Auth->get_active_user($this->session->userdata('username'));
        $status = (string) $this->input->get('status');
        $status = array_key_exists($status, self::STATUS_TABS) ? $status : 'due';
        $building_filter = $user['role_id'] === '1' ? (int) $this->input->get('building_id') : 0;

        $schedules = $this->Maintenance->get_schedules($user['role_id'], $user['building_id'], $status, $building_filter);
        foreach ($schedules as &$s) {
            $s['token'] = $this->_token($s['id']);
            $s['due']   = maintenance_due_status($s['next_due_date'], $s['remind_days_before'], $s['is_active']);
        }
        unset($s);

        $year_logs = $this->Maintenance->get_logs($user['role_id'], $user['building_id'], ['year' => date('Y'), 'building_id' => $building_filter]);

        $data = [
            'title'           => 'Jadwal Maintenance',
            'user'            => $user,
            'status'          => $status,
            'tabs'            => self::STATUS_TABS,
            'counts'          => $this->Maintenance->count_by_status($user['role_id'], $user['building_id'], $building_filter),
            'schedules'       => $schedules,
            'assets'          => $this->Maintenance->assets_for_select($user['role_id'], $user['building_id']),
            'units'           => maintenance_interval_units(),
            'types'           => Maintenance_model::TYPES,
            'year_cost'       => array_sum(array_column($year_logs, 'cost')),
            'year_count'      => count($year_logs),
            'building_filter' => $building_filter,
            'buildings'       => $user['role_id'] === '1' ? $this->db->order_by('name')->get_where('buildings', ['status' => 1])->result_array() : [],
        ];

        $this->_render('maintenance/index', $data);
    }

    public function store_schedule()
    {
        $user  = $this->Auth->get_active_user($this->session->userdata('username'));
        $input = $this->_schedule_input();
        $asset = $input ? $this->_authorized_asset($input['asset_id'], $user) : null;

        if (!$input || !$asset) {
            $this->session->set_flashdata('message', 'maintenance_invalid');
            redirect('maintenance');
        }

        $input['created_by'] = $user['id_user'];
        $schedule_id = $this->Maintenance->create_schedule($input);
        $this->audit->log('maintenance.schedule_create', 'Buat jadwal "' . $input['title'] . '" ' . maintenance_interval_label($input['interval_value'], $input['interval_unit'])
            . ' untuk ' . $asset['asset_name'], [
            'entity_type'  => 'maintenance_schedule',
            'entity_id'    => $schedule_id,
            'entity_label' => $asset['serial_number'],
            'building_id'  => $asset['building_id'],
            'changes'      => ['jatuh_tempo' => ['old' => null, 'new' => $input['next_due_date']]],
        ]);

        $this->session->set_flashdata('message', 'maintenanceScheduled');
        redirect('maintenance?status=all');
    }

    public function update_schedule()
    {
        $user     = $this->Auth->get_active_user($this->session->userdata('username'));
        $schedule = $this->_authorized_schedule($this->input->post('token'), $user);
        $input    = $this->_schedule_input(false);

        if (!$input) {
            $this->session->set_flashdata('message', 'maintenance_invalid');
            redirect('maintenance?status=all');
        }

        $this->Maintenance->update_schedule($schedule['id'], $input);
        $changes = Audit::diff($schedule, $input, ['title', 'interval_value', 'interval_unit', 'next_due_date', 'remind_days_before', 'notes']);
        if ($changes) {
            $this->audit->log('maintenance.schedule_update', 'Ubah jadwal "' . $input['title'] . '" untuk ' . $schedule['asset_name'], [
                'entity_type'  => 'maintenance_schedule',
                'entity_id'    => $schedule['id'],
                'entity_label' => $schedule['serial_number'],
                'building_id'  => $schedule['building_id'],
                'changes'      => $changes,
            ]);
        }
        $this->session->set_flashdata('message', 'maintenanceUpdated');
        redirect($this->_back());
    }

    public function toggle_schedule()
    {
        $user     = $this->Auth->get_active_user($this->session->userdata('username'));
        $schedule = $this->_authorized_schedule($this->input->post('token'), $user);

        $this->Maintenance->update_schedule($schedule['id'], ['is_active' => (int) $schedule['is_active'] === 1 ? 0 : 1]);
        $this->audit->log('maintenance.schedule_toggle', ((int) $schedule['is_active'] === 1 ? 'Nonaktifkan' : 'Aktifkan') . ' jadwal "' . $schedule['title'] . '" untuk ' . $schedule['asset_name'], [
            'entity_type'  => 'maintenance_schedule',
            'entity_id'    => $schedule['id'],
            'entity_label' => $schedule['serial_number'],
            'building_id'  => $schedule['building_id'],
        ]);
        $this->session->set_flashdata('message', (int) $schedule['is_active'] === 1 ? 'maintenanceDeactivated' : 'maintenanceActivated');
        redirect($this->_back());
    }

    /**
     * Catat perawatan/perbaikan. Dengan token jadwal = servis rutin (jadwal digeser);
     * tanpa token = perbaikan/perawatan insidental untuk aset yang dipilih.
     */
    public function record()
    {
        $user     = $this->Auth->get_active_user($this->session->userdata('username'));
        $token    = (string) $this->input->post('token');
        $schedule = $token !== '' ? $this->_authorized_schedule($token, $user) : null;
        $asset_id = $schedule ? (int) $schedule['asset_id'] : (int) $this->input->post('asset_id');
        $asset    = $this->_authorized_asset($asset_id, $user);
        $log      = $this->_log_input();

        if (!$asset || !$log) {
            $this->session->set_flashdata('message', 'maintenance_invalid');
            redirect($this->_back());
        }

        $log['asset_id']     = $asset_id;
        $log['schedule_id']  = $schedule ? (int) $schedule['id'] : null;
        $log['type']         = $schedule ? 'rutin' : ($log['type'] ?? 'perbaikan');
        $log['performed_by'] = $user['id_user'];

        $ok = $this->Maintenance->record($log, $schedule);
        if ($ok) {
            $this->audit->log('maintenance.record', ($schedule ? 'Catat servis "' . $schedule['title'] . '"' : 'Catat ' . (Maintenance_model::TYPES[$log['type']] ?? $log['type']))
                . ' untuk ' . $asset['asset_name'] . ', biaya ' . rupiah($log['cost']), [
                'entity_type'  => 'asset',
                'entity_id'    => $asset_id,
                'entity_label' => $asset['serial_number'],
                'building_id'  => $asset['building_id'],
                'changes'      => [
                    'tanggal'    => ['old' => null, 'new' => $log['performed_at']],
                    'biaya'      => ['old' => null, 'new' => (float) $log['cost']],
                    'keterangan' => ['old' => null, 'new' => mb_substr($log['description'], 0, 200)],
                ],
            ]);
        }
        $this->session->set_flashdata('message', $ok ? 'maintenanceRecorded' : 'maintenance_failed');
        redirect($this->_back());
    }

    /**
     * Riwayat perawatan & perbaikan beserta biaya.
     */
    public function history()
    {
        $data          = $this->_history_data();
        $data['title'] = 'Riwayat Maintenance';
        $data['years'] = $this->Maintenance->get_log_years();
        $data['types'] = Maintenance_model::TYPES;
        $data['query'] = http_build_query(array_filter($data['filters']));
        $data['buildings'] = $data['user']['role_id'] === '1' ? $this->db->order_by('name')->get_where('buildings', ['status' => 1])->result_array() : [];

        $this->_render('maintenance/history', $data);
    }

    public function export_history()
    {
        $data = $this->_history_data();

        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setTitle('Riwayat Maintenance');
        $sheet->fromArray(['Tanggal', 'Jenis', 'Aset', 'Nomor Seri', 'Gedung', 'Ruangan', 'Jadwal', 'Keterangan', 'Vendor', 'Kondisi Setelah', 'Dicatat oleh', 'Biaya'], null, 'A1');
        $r = 2;
        foreach ($data['logs'] as $log) {
            $sheet->fromArray([
                $log['performed_at'], Maintenance_model::TYPES[$log['type']] ?? $log['type'], $log['asset_name'], $log['serial_number'],
                $log['building_name'], $log['room_name'], $log['schedule_title'], $log['description'], $log['vendor'],
                $log['condition_after'] ? asset_condition_label($log['condition_after']) : null, $log['performed_by_name'], (float) $log['cost'],
            ], null, 'A' . $r, true);
            $sheet->getCell('D' . $r)->setValueExplicit($log['serial_number'], DataType::TYPE_STRING);
            $r++;
        }
        $sheet->setCellValue('K' . $r, 'TOTAL');
        $sheet->setCellValue('L' . $r, $data['total_cost']);
        $sheet->getStyle('K' . $r . ':L' . $r)->getFont()->setBold(true);
        $sheet->getStyle('A1:L1')->getFont()->setBold(true)->getColor()->setRGB('FFFFFF');
        $sheet->getStyle('A1:L1')->getFill()->setFillType(Fill::FILL_SOLID)->getStartColor()->setRGB('4E73DF');
        $sheet->getStyle('L2:L' . $r)->getNumberFormat()->setFormatCode('#,##0');
        foreach (range('A', 'L') as $col) {
            $sheet->getColumnDimension($col)->setAutoSize(true);
        }

        $filename = 'riwayat-maintenance-' . ($data['filters']['year'] ?: 'semua') . '.xlsx';
        $this->audit->log('report.export', 'Ekspor riwayat maintenance ' . ($data['filters']['year'] ?: 'semua tahun') . ' (Excel, ' . count($data['logs']) . ' baris)');
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment; filename="' . $filename . '"');
        header('Cache-Control: max-age=0');
        (new Xlsx($spreadsheet))->save('php://output');
        exit;
    }

    // ------------------------------------------------------------------

    private function _history_data()
    {
        $user    = $this->Auth->get_active_user($this->session->userdata('username'));
        $year    = $this->input->get('year');
        $filters = [
            'year'        => $year === 'all' ? 0 : ((int) $year ?: (int) date('Y')),
            'type'        => array_key_exists((string) $this->input->get('type'), Maintenance_model::TYPES) ? (string) $this->input->get('type') : '',
            'building_id' => $user['role_id'] === '1' ? (int) $this->input->get('building_id') : 0,
        ];
        $logs = $this->Maintenance->get_logs($user['role_id'], $user['building_id'], $filters);

        // Biaya per bulan (untuk chart) & per jenis
        $by_month = array_fill(1, 12, 0.0);
        $by_type  = array_fill_keys(array_keys(Maintenance_model::TYPES), 0.0);
        foreach ($logs as $log) {
            $by_month[(int) date('n', strtotime($log['performed_at']))] += (float) $log['cost'];
            $by_type[$log['type']] = ($by_type[$log['type']] ?? 0) + (float) $log['cost'];
        }

        return [
            'user'       => $user,
            'filters'    => $filters,
            'logs'       => $logs,
            'total_cost' => array_sum(array_column($logs, 'cost')),
            'by_month'   => array_values($by_month),
            'by_type'    => $by_type,
        ];
    }

    /**
     * @return array|false
     */
    private function _schedule_input($with_asset = true)
    {
        $title    = trim((string) $this->input->post('title', true));
        $value    = (int) $this->input->post('interval_value');
        $unit     = (string) $this->input->post('interval_unit');
        $due      = (string) $this->input->post('next_due_date');
        $remind   = trim((string) $this->input->post('remind_days_before'));
        $notes    = trim((string) $this->input->post('notes', true));
        $asset_id = (int) $this->input->post('asset_id');

        $date = DateTime::createFromFormat('Y-m-d', $due);
        $valid = $title !== '' && $value >= 1 && $value <= 365
            && array_key_exists($unit, maintenance_interval_units())
            && $date && $date->format('Y-m-d') === $due
            && ($remind === '' || (ctype_digit($remind) && (int) $remind <= 365))
            && (!$with_asset || $asset_id > 0);

        if (!$valid) {
            return false;
        }

        $data = [
            'title'              => mb_substr($title, 0, 100),
            'interval_value'     => $value,
            'interval_unit'      => $unit,
            'next_due_date'      => $due,
            'remind_days_before' => $remind === '' ? 7 : (int) $remind,
            'notes'              => $notes !== '' ? mb_substr($notes, 0, 255) : null,
            'last_notified_at'   => null,
        ];
        if ($with_asset) {
            $data['asset_id'] = $asset_id;
        }
        return $data;
    }

    /**
     * @return array|false
     */
    private function _log_input()
    {
        $date        = (string) $this->input->post('performed_at');
        $description = trim((string) $this->input->post('description', true));
        $vendor      = trim((string) $this->input->post('vendor', true));
        $cost        = preg_replace('/[^0-9]/', '', (string) $this->input->post('cost'));
        $condition   = (int) $this->input->post('condition_after');
        $type        = (string) $this->input->post('type');

        $parsed = DateTime::createFromFormat('Y-m-d', $date);
        if (!$parsed || $parsed->format('Y-m-d') !== $date || $date > date('Y-m-d') || $description === '' || strlen($cost) > 13) {
            return false;
        }

        return [
            'performed_at'    => $date,
            'description'     => mb_substr($description, 0, 2000),
            'vendor'          => $vendor !== '' ? mb_substr($vendor, 0, 100) : null,
            'cost'            => $cost !== '' ? $cost : 0,
            'condition_after' => is_valid_asset_condition($condition) ? $condition : null,
            'type'            => array_key_exists($type, Maintenance_model::TYPES) ? $type : null,
        ];
    }

    private function _authorized_asset($asset_id, $user)
    {
        $asset = $asset_id ? $this->Maintenance->get_asset_location($asset_id) : null;
        $ok = $asset && (int) $asset['is_disposed'] === 0
            && ($user['role_id'] === '1' || (string) $user['building_id'] === (string) $asset['building_id']);
        return $ok ? $asset : null;
    }

    private function _authorized_schedule($token, $user)
    {
        $id       = $token ? $this->encryption->decrypt(urlsafe_b64decode((string) $token)) : false;
        $schedule = $id ? $this->Maintenance->get_schedule((int) $id) : null;
        $ok = $schedule && ($user['role_id'] === '1' || (string) $user['building_id'] === (string) $schedule['building_id']);

        if (!$ok) {
            $this->session->set_flashdata('message', 'maintenance_failed');
            redirect('maintenance');
        }
        return $schedule;
    }

    /** Kembali ke halaman asal form (maintenance atau scan/<serial>), hanya path internal. */
    private function _back()
    {
        $back = (string) $this->input->post('back');
        return preg_match('#^(maintenance|scan/)[A-Za-z0-9_\-/?=&%.]*$#', $back) ? $back : 'maintenance';
    }

    private function _token($id)
    {
        return urlsafe_b64encode($this->encryption->encrypt((string) $id));
    }

    private function _render($view, array $data)
    {
        $this->load->view('templates/header', $data);
        $this->load->view('templates/sidebar', $data);
        $this->load->view('templates/topbar', $data);
        $this->load->view($view, $data);
        $this->load->view('templates/footer');
    }
}
