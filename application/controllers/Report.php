<?php
defined('BASEPATH') or exit('No direct script access allowed');

require('./application/third_party/phpexcel/vendor/autoload.php');

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Style\Alignment;

class Report extends CI_Controller
{

    public function __construct()
    {
        parent::__construct();
        $this->load->model('Assets_model', 'Assets');
    }

    public function index()
    {
        $data['user'] = $this->Assets->login_model($this->session->userdata('email'));

        $data = [
            'title' => 'Report Aset Books&Beyond',
            'asset' => $this->Assets->get_report_model()
        ];

        $asset = $this->Assets->get_report_model();

        $spreadSheet = new Spreadsheet;

        $style = [
            'alignment' => [
                'horizontal' => Alignment::HORIZONTAL_CENTER,
            ]
        ];

        $styleFont = [
            'font' => [
                'bold' => false,
                'size' => 10,
                'name' => 'Verdana',
            ],
            'alignment' => [
                'horizontal' => Alignment::HORIZONTAL_CENTER,
                'vertical' => Alignment::VERTICAL_CENTER
            ]
        ];

        $spreadSheet->getActiveSheet()->getStyle('A1')->getFill()->getStartColor()->setRGB('FFDBE2F1');


        $spreadSheet->setActiveSheetIndex(0)->mergeCells('A1:H3');
        $spreadSheet->getDefaultStyle()->applyFromArray($styleFont);
        $spreadSheet->getActiveSheet()->getCell('A1')->setValue('Books&Beyond Report Asset');

        $spreadSheet->getActiveSheet()->getStyle("A4:H500")->applyFromArray($style);
        $spreadSheet->getActiveSheet()->getHeaderFooter()->setFirstHeader('abcd');

        $spreadSheet->getActiveSheet()->getColumnDimension('B')->setWidth('20');
        $spreadSheet->getActiveSheet()->getColumnDimension('C')->setWidth('20');
        $spreadSheet->getActiveSheet()->getColumnDimension('D')->setWidth('12');
        $spreadSheet->getActiveSheet()->getColumnDimension('E')->setWidth('20');
        $spreadSheet->getActiveSheet()->getColumnDimension('F')->setWidth('20');
        $spreadSheet->getActiveSheet()->getColumnDimension('G')->setWidth('20');
        $spreadSheet->getActiveSheet()->getColumnDimension('H')->setWidth('20');

        $spreadSheet->setActiveSheetIndex(0)
            ->setAutoFilter('A5:H5')
            ->setCellValue('A5', 'No')
            ->setCellValue('B5', 'Asset Name')
            ->setCellValue('C5', 'Merk')
            ->setCellValue('D5', 'Qty')
            ->setCellValue('E5', 'Serial Number')
            ->setCellValue('F5', 'Origin Location')
            ->setCellValue('G5', 'Current Location')
            ->setCellValue('H5', 'Condition Asset');


        $kolom = 6;
        $nomor = 1;

        foreach ($asset as $ast) {
            if ($ast['status'] == 0) {
                $status = 'Good';
            } else {
                $status = 'Bad';
            }
            $spreadSheet->setActiveSheetIndex(0)
                ->setCellValue('A' . $kolom, $nomor)
                ->setCellValue('B' . $kolom, $ast['asset_name'])
                ->setCellValue('C' . $kolom, $ast['merk'])
                ->setCellValue('D' . $kolom, $ast['qty'])
                ->setCellValue('E' . $kolom, $ast['serial_number'])
                ->setCellValue('F' . $kolom, $ast['origin_location'])
                ->setCellValue('G' . $kolom, $ast['asset_location'])
                ->setCellValue('H' . $kolom, $status);
            $kolom++;
            $nomor++;
        }
        $writer = new Xlsx($spreadSheet);

        header('Content-Type: application/vnd.ms-excel');
        header('Content-Disposition: attachment;filename="Books&Beyond Report Aset.xlsx"');
        header('Cache-Control: max-age=0');

        $writer->save('php://output');
    }
}
