<?php
defined('BASEPATH') or exit('No direct script access allowed');

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

/**
 * Laporan nilai aset: harga perolehan, akumulasi penyusutan (garis lurus per kategori),
 * dan nilai buku per tanggal acuan. Bisa diekspor ke Excel dan PDF dengan filter yang sama.
 */
class Valuation extends CI_Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->load->model('Auth_model', 'Auth');
        $this->load->model('Valuation_model', 'Valuation');
        is_logged_in();
    }

    public function index()
    {
        $data          = $this->_report();
        $data['title'] = 'Laporan Nilai Aset';
        $data['buildings']  = $this->Valuation->get_buildings();
        $data['categories'] = $this->Valuation->get_categories();
        $data['query']      = http_build_query(array_filter($data['filters']));

        $this->load->view('templates/header', $data);
        $this->load->view('templates/sidebar', $data);
        $this->load->view('templates/topbar', $data);
        $this->load->view('valuation/index', $data);
        $this->load->view('templates/footer');
    }

    public function export_pdf()
    {
        $data          = $this->_report();
        $data['title'] = 'Laporan Nilai Aset';

        $this->load->library('pdf');
        $this->pdf->setPaper('A4', 'landscape');
        $this->pdf->filename = 'laporan-nilai-aset-' . $data['filters']['as_of'] . '.pdf';
        $this->audit->log('report.export', 'Ekspor laporan nilai aset per ' . $data['filters']['as_of'] . ' (PDF, ' . count($data['rows']) . ' aset)');
        $this->pdf->load_view('valuation/print', $data);
    }

    public function export_excel()
    {
        $data  = $this->_report();
        $as_of = $data['filters']['as_of'];

        $spreadsheet = new Spreadsheet();
        $spreadsheet->getProperties()->setTitle('Laporan Nilai Aset ' . $as_of);

        // --- Sheet 1: ringkasan per kategori ---
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setTitle('Ringkasan');
        $sheet->setCellValue('A1', 'Laporan Nilai Aset per ' . date('d-m-Y', strtotime($as_of)));
        $sheet->setCellValue('A2', $data['scope_label']);
        $sheet->getStyle('A1')->getFont()->setBold(true)->setSize(13);

        $head = ['Kategori', 'Masa Manfaat (th)', 'Nilai Sisa (%)', 'Jumlah Aset', 'Dengan Data Beli', 'Harga Perolehan', 'Akumulasi Penyusutan', 'Nilai Buku'];
        $sheet->fromArray($head, null, 'A4');
        $r = 5;
        foreach ($data['summary'] as $s) {
            $sheet->fromArray([
                $s['name'], $s['life_years'], $s['residual_percent'], $s['count'], $s['with_price'],
                $s['cost'], $s['accumulated'], $s['book_value'],
            ], null, 'A' . $r, true);
            $r++;
        }
        $sheet->fromArray(['TOTAL', null, null, $data['totals']['count'], $data['totals']['with_price'],
            $data['totals']['cost'], $data['totals']['accumulated'], $data['totals']['book_value']], null, 'A' . $r, true);
        $sheet->getStyle('A' . $r . ':H' . $r)->getFont()->setBold(true);
        $this->_style_table($sheet, 'A4:H4', 'F5:H' . $r);

        // --- Sheet 2: detail per aset ---
        $detail = $spreadsheet->createSheet();
        $detail->setTitle('Detail');
        $head = ['No', 'Nama Aset', 'Merk', 'Nomor Seri', 'Kategori', 'Gedung', 'Ruangan', 'Kondisi', 'Vendor',
            'Tgl Perolehan', 'Garansi s.d.', 'Umur (bln)', 'Masa Manfaat (bln)', 'Harga Perolehan',
            'Penyusutan/Bulan', 'Akumulasi Penyusutan', 'Nilai Buku'];
        $detail->fromArray($head, null, 'A1');
        $r = 2;
        foreach ($data['rows'] as $i => $row) {
            $d = $row['depreciation'];
            $detail->fromArray([
                $i + 1, $row['asset_name'], $row['merk'], $row['serial_number'], $row['category_name'] ?? '-',
                $row['building_name'], $row['room_name'], asset_condition_label($row['asset_condition']), $row['vendor'],
                $row['purchase_date'], $row['warranty_until'],
                $d ? $d['age_months'] : null, $d ? $d['life_months'] : null,
                $d ? $d['price'] : null, $d ? $d['monthly'] : null, $d ? $d['accumulated'] : null, $d ? $d['book_value'] : null,
            ], null, 'A' . $r, true);
            $detail->getCell('D' . $r)->setValueExplicit($row['serial_number'], \PhpOffice\PhpSpreadsheet\Cell\DataType::TYPE_STRING);
            $r++;
        }
        $this->_style_table($detail, 'A1:Q1', 'N2:Q' . max(2, $r - 1));
        $detail->freezePane('C2');

        $spreadsheet->setActiveSheetIndex(0);

        $filename = 'laporan-nilai-aset-' . $as_of . '.xlsx';
        $this->audit->log('report.export', 'Ekspor laporan nilai aset per ' . $as_of . ' (Excel, ' . count($data['rows']) . ' aset)');
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment; filename="' . $filename . '"');
        header('Cache-Control: max-age=0');
        (new Xlsx($spreadsheet))->save('php://output');
        exit;
    }

    // ------------------------------------------------------------------

    /**
     * Ambil data + hitung penyusutan per aset, ringkasan per kategori, dan total.
     */
    private function _report()
    {
        $user = $this->Auth->get_active_user($this->session->userdata('username'));

        $as_of = (string) $this->input->get('as_of');
        $valid = DateTime::createFromFormat('Y-m-d', $as_of);
        if (!$valid || $valid->format('Y-m-d') !== $as_of) {
            $as_of = date('Y-m-d');
        }

        $category = (string) $this->input->get('category_id');
        $filters  = [
            'building_id' => $user['role_id'] === '1' ? (int) $this->input->get('building_id') : 0,
            'category_id' => $category === 'none' ? 'none' : (int) $category,
            'as_of'       => $as_of,
        ];

        $rows    = $this->Valuation->get_assets($user['role_id'], $user['building_id'], $filters);
        $summary = [];
        $totals  = ['count' => 0, 'with_price' => 0, 'cost' => 0.0, 'accumulated' => 0.0, 'book_value' => 0.0, 'missing' => 0, 'warranty_soon' => 0];

        foreach ($rows as &$row) {
            $row['depreciation'] = asset_depreciation(
                $row['purchase_price'],
                $row['purchase_date'],
                $row['useful_life_years'],
                $row['residual_percent'] ?? 0,
                $as_of
            );
            $row['warranty'] = warranty_status($row['warranty_until'], $as_of);

            $key = $row['category_id'] ?? 'none';
            if (!isset($summary[$key])) {
                $summary[$key] = [
                    'name'             => $row['category_name'] ?? 'Tanpa kategori',
                    'life_years'       => $row['useful_life_years'],
                    'residual_percent' => $row['category_id'] ? (float) $row['residual_percent'] : null,
                    'count' => 0, 'with_price' => 0, 'cost' => 0.0, 'accumulated' => 0.0, 'book_value' => 0.0,
                ];
            }

            $summary[$key]['count']++;
            $totals['count']++;

            $d = $row['depreciation'];
            if ($d) {
                foreach (['cost' => 'price', 'accumulated' => 'accumulated', 'book_value' => 'book_value'] as $to => $from) {
                    $summary[$key][$to] += $d[$from];
                    $totals[$to]        += $d[$from];
                }
                $summary[$key]['with_price']++;
                $totals['with_price']++;
            } else {
                $totals['missing']++;
            }

            if ($row['warranty'] && $row['warranty']['color'] === 'warning') {
                $totals['warranty_soon']++;
            }
        }
        unset($row);

        $scope = 'Semua gedung';
        if ($user['role_id'] !== '1') {
            $scope = 'Gedung ' . ($rows[0]['building_name'] ?? '(milik pengguna)');
        } elseif ($filters['building_id']) {
            foreach ($this->Valuation->get_buildings() as $b) {
                if ((int) $b['id'] === $filters['building_id']) {
                    $scope = $b['name'];
                }
            }
        }

        return [
            'user'        => $user,
            'filters'     => $filters,
            'rows'        => $rows,
            'summary'     => array_values($summary),
            'totals'      => $totals,
            'scope_label' => $scope,
        ];
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
