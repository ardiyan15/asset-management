<?php
defined('BASEPATH') or exit('No direct script access allowed');

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Cell\DataType;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

/**
 * Laporan rekap aset: dikelompokkan per gedung / ruangan / kategori / kondisi,
 * dengan filter yang sama untuk tampilan, ekspor Excel, dan ekspor PDF.
 *
 * (Menggantikan controller Report lama yang rusak dan tidak memeriksa login.)
 */
class Report extends CI_Controller
{
    const GROUPS = [
        'building'  => 'Gedung',
        'room'      => 'Ruangan',
        'category'  => 'Kategori',
        'condition' => 'Kondisi',
    ];

    const STATUSES = [
        'active'   => 'Aset aktif',
        'disposed' => 'Sudah dihapus',
        'all'      => 'Semua',
    ];

    public function __construct()
    {
        parent::__construct();
        $this->load->model('Auth_model', 'Auth');
        $this->load->model('Recap_model', 'Recap');
        is_logged_in();
    }

    public function index()
    {
        $data               = $this->_report();
        $data['title']      = 'Rekap Aset';
        $data['buildings']  = $this->Recap->get_buildings();
        $data['categories'] = $this->Recap->get_categories();
        $data['group_options']  = self::GROUPS;
        $data['status_options'] = self::STATUSES;
        $data['query']      = http_build_query(array_filter($data['filters']));

        $this->load->view('templates/header', $data);
        $this->load->view('templates/sidebar', $data);
        $this->load->view('templates/topbar', $data);
        $this->load->view('report/index', $data);
        $this->load->view('templates/footer');
    }

    public function export_pdf()
    {
        $data          = $this->_report();
        $data['title'] = 'Rekap Aset';

        $this->load->library('pdf');
        $this->pdf->setPaper('A4', 'landscape');
        $this->pdf->filename = 'rekap-aset-per-' . $data['filters']['group'] . '-' . date('Ymd') . '.pdf';
        $this->audit->log('report.export', 'Ekspor rekap aset per ' . strtolower($data['group_label']) . ' (PDF, ' . count($data['rows']) . ' aset)');
        $this->pdf->load_view('report/print', $data);
    }

    public function export_excel()
    {
        $data = $this->_report();

        $spreadsheet = new Spreadsheet();
        $spreadsheet->getProperties()->setTitle('Rekap Aset');

        // --- Sheet 1: rekap ---
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setTitle('Rekap');
        $sheet->setCellValue('A1', 'Rekap Aset per ' . $data['group_label'] . ' · ' . date('d-m-Y'));
        $sheet->setCellValue('A2', implode(' · ', $data['filter_labels']));
        $sheet->getStyle('A1')->getFont()->setBold(true)->setSize(13);

        $conditions = asset_condition_options();
        $head       = array_merge([$data['group_label'], 'Jumlah Aset'], array_values($conditions), ['Harga Perolehan', 'Nilai Buku']);
        $sheet->fromArray($head, null, 'A4');
        $r = 5;
        foreach ($data['groups'] as $g) {
            $row = [$g['label'], $g['total']];
            foreach (array_keys($conditions) as $c) {
                $row[] = $g['conditions'][$c];
            }
            $row[] = $g['cost'];
            $row[] = $g['book_value'];
            $sheet->fromArray($row, null, 'A' . $r, true);
            $r++;
        }
        $totals = ['TOTAL', $data['totals']['total']];
        foreach (array_keys($conditions) as $c) {
            $totals[] = $data['totals']['conditions'][$c];
        }
        $totals[] = $data['totals']['cost'];
        $totals[] = $data['totals']['book_value'];
        $sheet->fromArray($totals, null, 'A' . $r, true);
        $sheet->getStyle('A' . $r . ':G' . $r)->getFont()->setBold(true);
        $this->_style_table($sheet, 'A4:G4', 'F5:G' . $r);

        // --- Sheet 2: detail aset ---
        $detail = $spreadsheet->createSheet();
        $detail->setTitle('Detail');
        $detail->fromArray(['No', 'Nama Aset', 'Merk', 'Nomor Seri', 'Kategori', 'Gedung', 'Lantai', 'Ruangan',
            'Kondisi', 'Status', 'Tgl Input', 'Tgl Perolehan', 'Harga Perolehan', 'Nilai Buku'], null, 'A1');
        $r = 2;
        foreach ($data['rows'] as $i => $row) {
            $d = $row['depreciation'];
            $detail->fromArray([
                $i + 1, $row['asset_name'], $row['merk'], $row['serial_number'], $row['category_name'] ?? '-',
                $row['building_name'], $row['floor_name'], $row['room_name'],
                asset_condition_label($row['asset_condition']), $this->_status_label($row),
                $row['created'], $row['purchase_date'],
                $d ? $d['price'] : null, $d ? $d['book_value'] : null,
            ], null, 'A' . $r, true);
            $detail->getCell('D' . $r)->setValueExplicit($row['serial_number'], DataType::TYPE_STRING);
            $r++;
        }
        $this->_style_table($detail, 'A1:N1', 'M2:N' . max(2, $r - 1));
        $detail->freezePane('C2');

        $spreadsheet->setActiveSheetIndex(0);

        $filename = 'rekap-aset-per-' . $data['filters']['group'] . '-' . date('Ymd') . '.xlsx';
        $this->audit->log('report.export', 'Ekspor rekap aset per ' . strtolower($data['group_label']) . ' (Excel, ' . count($data['rows']) . ' aset)');
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment; filename="' . $filename . '"');
        header('Cache-Control: max-age=0');
        (new Xlsx($spreadsheet))->save('php://output');
        exit;
    }

    // ------------------------------------------------------------------

    /**
     * Ambil aset sesuai filter, lalu kelompokkan sesuai dimensi rekap.
     */
    private function _report()
    {
        $user = $this->Auth->get_active_user($this->session->userdata('username'));

        $group     = (string) $this->input->get('group');
        $status    = (string) $this->input->get('status');
        $condition = (int) $this->input->get('condition');
        $category  = (string) $this->input->get('category_id');

        $filters = [
            'group'       => array_key_exists($group, self::GROUPS) ? $group : 'building',
            'building_id' => $user['role_id'] === '1' ? (int) $this->input->get('building_id') : 0,
            'category_id' => $category === 'none' ? 'none' : (int) $category,
            'condition'   => is_valid_asset_condition($condition) ? $condition : 0,
            'status'      => array_key_exists($status, self::STATUSES) ? $status : 'active',
        ];
        // Non-admin hanya punya satu gedung: rekap per gedung kurang berguna, default ke ruangan
        if ($user['role_id'] !== '1' && $group === '') {
            $filters['group'] = 'room';
        }

        $rows   = $this->Recap->get_assets($user['role_id'], $user['building_id'], $filters);
        $groups = [];
        $totals = $this->_empty_group('TOTAL');

        foreach ($rows as &$row) {
            $row['depreciation'] = asset_depreciation(
                $row['purchase_price'],
                $row['purchase_date'],
                $row['useful_life_years'],
                $row['residual_percent'] ?? 0
            );

            [$key, $label] = $this->_group_key($filters['group'], $row);
            if (!isset($groups[$key])) {
                $groups[$key] = $this->_empty_group($label);
            }
            $this->_add_to_group($groups[$key], $row);
            $this->_add_to_group($totals, $row);
        }
        unset($row);

        if ($filters['group'] === 'condition') {
            ksort($groups);
        }

        return [
            'user'          => $user,
            'filters'       => $filters,
            'group_label'   => self::GROUPS[$filters['group']],
            'filter_labels' => $this->_filter_labels($user, $filters),
            'rows'          => $rows,
            'groups'        => array_values($groups),
            'totals'        => $totals,
        ];
    }

    private function _group_key($group, array $row)
    {
        switch ($group) {
            case 'room':
                return [$row['room_id'], $row['room_name'] . ' — ' . $row['building_name']];
            case 'category':
                return [$row['category_id'] ?? 0, $row['category_name'] ?? 'Tanpa kategori'];
            case 'condition':
                return [(int) $row['asset_condition'], asset_condition_label($row['asset_condition'])];
            default:
                return [$row['building_id'], $row['building_name']];
        }
    }

    private function _empty_group($label)
    {
        return [
            'label'      => $label,
            'total'      => 0,
            'conditions' => array_fill_keys(array_keys(asset_condition_options()), 0),
            'disposed'   => 0,
            'with_price' => 0,
            'cost'       => 0.0,
            'book_value' => 0.0,
        ];
    }

    private function _add_to_group(array &$group, array $row)
    {
        $group['total']++;
        $condition = (int) $row['asset_condition'];
        if (isset($group['conditions'][$condition])) {
            $group['conditions'][$condition]++;
        }
        if ((int) $row['is_disposed'] === 1) {
            $group['disposed']++;
        }
        $d = $row['depreciation'];
        if ($d) {
            $group['with_price']++;
            $group['cost']       += $d['price'];
            // Aset yang sudah dihapus tidak punya nilai buku lagi
            $group['book_value'] += (int) $row['is_disposed'] === 1 ? 0 : $d['book_value'];
        }
    }

    private function _filter_labels($user, array $filters)
    {
        $labels = [self::STATUSES[$filters['status']]];

        if ($user['role_id'] !== '1') {
            $labels[] = 'Gedung sendiri';
        } elseif ($filters['building_id']) {
            foreach ($this->Recap->get_buildings() as $b) {
                if ((int) $b['id'] === $filters['building_id']) {
                    $labels[] = $b['name'];
                }
            }
        } else {
            $labels[] = 'Semua gedung';
        }

        if ($filters['category_id'] === 'none') {
            $labels[] = 'Tanpa kategori';
        } elseif ($filters['category_id']) {
            foreach ($this->Recap->get_categories() as $c) {
                if ((int) $c['id'] === $filters['category_id']) {
                    $labels[] = 'Kategori ' . $c['name'];
                }
            }
        }

        if ($filters['condition']) {
            $labels[] = 'Kondisi ' . asset_condition_label($filters['condition']);
        }
        return $labels;
    }

    private function _status_label(array $row)
    {
        if ((int) $row['is_disposed'] === 1) {
            return 'Dihapus';
        }
        return (int) $row['placement_status'] === 1 ? 'Di ruangan' : 'Sedang dipindahkan';
    }

    private function _style_table($sheet, $header_range, $money_range)
    {
        $sheet->getStyle($header_range)->getFont()->setBold(true)->getColor()->setRGB('FFFFFF');
        $sheet->getStyle($header_range)->getFill()->setFillType(Fill::FILL_SOLID)->getStartColor()->setRGB('4E73DF');
        $sheet->getStyle($money_range)->getNumberFormat()->setFormatCode('#,##0');
        $last = preg_replace('/[0-9]/', '', explode(':', $header_range)[1]);
        foreach (range('A', $last) as $col) {
            $sheet->getColumnDimension($col)->setAutoSize(true);
        }
    }
}
