<?php
defined('BASEPATH') OR exit('No direct script access allowed');

// Try multiple possible vendor paths
$vendor_paths = [
    FCPATH . 'vendor/autoload.php',  // Root folder
    APPPATH . 'third_party/vendor/autoload.php',  // Application third_party
    APPPATH . 'vendor/autoload.php',  // Application folder
    APPPATH . '../vendor/autoload.php'  // Parent of application
];

$autoload_found = false;
foreach ($vendor_paths as $path) {
    if (file_exists($path)) {
        require_once $path;
        $autoload_found = true;
        break;
    }
}

if (!$autoload_found) {
    show_error('PhpSpreadsheet library not found. Please install it using: composer require phpoffice/phpspreadsheet');
}

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Style\Color;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Font;

class Excel_controller extends CI_Controller {

    public function __construct()
    {
        parent::__construct();
        
        // Check if user is logged in
        if (!$this->session->userdata('logged_in')) {
            $this->session->set_flashdata('error', 'Akses ditolak. Silakan login terlebih dahulu.');
            redirect('auth/login');
        }
        
        $this->load->model(['Excel_model', 'Admin_model', 'Pasien_model']);
        $this->load->library('session');
        $this->load->helper(['url', 'download']);
    }

    // ==========================================
    // EXAMINATION REPORTS EXPORT
    // ==========================================

    public function export_examination_reports()
    {
        try {
            $filters = array(
                'start_date' => $this->input->get('start_date'),
                'end_date' => $this->input->get('end_date'),
                'status' => $this->input->get('status'),
                'jenis_pemeriksaan' => $this->input->get('jenis_pemeriksaan'),
                'search' => $this->input->get('search')
            );

            $examinations = $this->Excel_model->get_examination_data_for_export($filters);
            
            if (empty($examinations)) {
                $this->session->set_flashdata('error', 'Tidak ada data pemeriksaan untuk diekspor');
                redirect('admin/examination_reports');
                return;
            }

            $spreadsheet = $this->_create_examination_excel($examinations, $filters);
            $filename = $this->_generate_filename('Laporan_Pemeriksaan', $filters);
            $this->_output_excel($spreadsheet, $filename);
            
            $this->Admin_model->log_activity(
                $this->session->userdata('user_id'),
                'Export data pemeriksaan ke Excel: ' . $filename,
                'pemeriksaan_lab',
                null
            );

        } catch (Exception $e) {
            log_message('error', 'Error exporting examination reports to Excel: ' . $e->getMessage());
            $this->session->set_flashdata('error', 'Gagal mengekspor data ke Excel: ' . $e->getMessage());
            redirect('admin/examination_reports');
        }
    }

    public function export_examination_summary()
    {
        try {
            $filters = array(
                'start_date' => $this->input->get('start_date'),
                'end_date' => $this->input->get('end_date'),
                'status' => $this->input->get('status'),
                'jenis_pemeriksaan' => $this->input->get('jenis_pemeriksaan')
            );

            $summary = $this->Excel_model->get_examination_summary_for_export($filters);
            $spreadsheet = $this->_create_examination_summary_excel($summary, $filters);
            $filename = $this->_generate_filename('Ringkasan_Pemeriksaan', $filters);
            $this->_output_excel($spreadsheet, $filename);
            
            $this->Admin_model->log_activity(
                $this->session->userdata('user_id'),
                'Export ringkasan pemeriksaan ke Excel: ' . $filename,
                'pemeriksaan_lab',
                null
            );

        } catch (Exception $e) {
            log_message('error', 'Error exporting examination summary to Excel: ' . $e->getMessage());
            $this->session->set_flashdata('error', 'Gagal mengekspor ringkasan ke Excel: ' . $e->getMessage());
            redirect('admin/examination_reports');
        }
    }

    public function export_detailed_examinations()
    {
        try {
            $filters = array(
                'start_date' => $this->input->get('start_date'),
                'end_date' => $this->input->get('end_date'),
                'status' => $this->input->get('status'),
                'jenis_pemeriksaan' => $this->input->get('jenis_pemeriksaan'),
                'search' => $this->input->get('search')
            );

            $examinations = $this->Excel_model->get_detailed_examination_data($filters);
            
            if (empty($examinations)) {
                $this->session->set_flashdata('error', 'Tidak ada data pemeriksaan untuk diekspor');
                redirect('admin/examination_reports');
                return;
            }

            $spreadsheet = $this->_create_detailed_examination_excel($examinations, $filters);
            $filename = $this->_generate_filename('Detail_Pemeriksaan', $filters);
            $this->_output_excel($spreadsheet, $filename);
            
            $this->Admin_model->log_activity(
                $this->session->userdata('user_id'),
                'Export detail pemeriksaan ke Excel: ' . $filename,
                'pemeriksaan_lab',
                null
            );

        } catch (Exception $e) {
            log_message('error', 'Error exporting detailed examinations to Excel: ' . $e->getMessage());
            $this->session->set_flashdata('error', 'Gagal mengekspor detail pemeriksaan ke Excel: ' . $e->getMessage());
            redirect('admin/examination_reports');
        }
    }

    // ==========================================
    // FINANCIAL REPORTS EXPORT
    // ==========================================

    public function export_financial_reports()
    {
        try {
            $filters = array(
                'start_date' => $this->input->get('start_date'),
                'end_date' => $this->input->get('end_date'),
                'status' => $this->input->get('status'),
                'jenis_pembayaran' => $this->input->get('jenis_pembayaran'),
                'metode_pembayaran' => $this->input->get('metode_pembayaran'),
                'search' => $this->input->get('search')
            );

            $financial_data = $this->Excel_model->get_financial_data_for_export($filters);
            
            if (empty($financial_data)) {
                $this->session->set_flashdata('error', 'Tidak ada data keuangan untuk diekspor');
                redirect('admin/financial_reports');
                return;
            }

            $spreadsheet = $this->_create_financial_excel($financial_data, $filters);
            $filename = $this->_generate_filename('Laporan_Keuangan', $filters);
            $this->_output_excel($spreadsheet, $filename);
            
            $this->Admin_model->log_activity(
                $this->session->userdata('user_id'),
                'Export data keuangan ke Excel: ' . $filename,
                'invoice',
                null
            );

        } catch (Exception $e) {
            log_message('error', 'Error exporting financial reports to Excel: ' . $e->getMessage());
            $this->session->set_flashdata('error', 'Gagal mengekspor data ke Excel: ' . $e->getMessage());
            redirect('admin/financial_reports');
        }
    }

    public function export_financial_summary()
    {
        try {
            $filters = array(
                'start_date' => $this->input->get('start_date'),
                'end_date' => $this->input->get('end_date'),
                'jenis_pembayaran' => $this->input->get('jenis_pembayaran'),
                'metode_pembayaran' => $this->input->get('metode_pembayaran')
            );

            $statistics = $this->Excel_model->get_financial_statistics_for_export($filters);
            $payment_methods = $this->Excel_model->get_payment_method_breakdown($filters);
            $monthly_summary = $this->Excel_model->get_monthly_revenue_summary($filters);
            $top_patients = $this->Excel_model->get_top_patients_by_revenue($filters, 10);
            $examination_revenue = $this->Excel_model->get_examination_type_revenue($filters);
            
            $spreadsheet = $this->_create_financial_summary_excel($statistics, $payment_methods, $monthly_summary, $top_patients, $examination_revenue, $filters);
            $filename = $this->_generate_filename('Ringkasan_Keuangan', $filters);
            $this->_output_excel($spreadsheet, $filename);
            
            $this->Admin_model->log_activity(
                $this->session->userdata('user_id'),
                'Export ringkasan keuangan ke Excel: ' . $filename,
                'invoice',
                null
            );

        } catch (Exception $e) {
            log_message('error', 'Error exporting financial summary to Excel: ' . $e->getMessage());
            $this->session->set_flashdata('error', 'Gagal mengekspor ringkasan ke Excel: ' . $e->getMessage());
            redirect('admin/financial_reports');
        }
    }

    public function export_overdue_payments()
    {
        try {
            $filters = array(
                'start_date' => $this->input->get('start_date'),
                'end_date' => $this->input->get('end_date'),
                'jenis_pembayaran' => $this->input->get('jenis_pembayaran')
            );

            $days_overdue = $this->input->get('days_overdue') ? (int)$this->input->get('days_overdue') : 30;
            $overdue_data = $this->Excel_model->get_overdue_payments($filters, $days_overdue);
            
            if (empty($overdue_data)) {
                $this->session->set_flashdata('error', 'Tidak ada data tunggakan untuk diekspor');
                redirect('admin/financial_reports');
                return;
            }

            $spreadsheet = $this->_create_overdue_payments_excel($overdue_data, $filters, $days_overdue);
            $filename = $this->_generate_filename('Tunggakan_Pembayaran', $filters);
            $this->_output_excel($spreadsheet, $filename);
            
            $this->Admin_model->log_activity(
                $this->session->userdata('user_id'),
                'Export data tunggakan ke Excel: ' . $filename,
                'invoice',
                null
            );

        } catch (Exception $e) {
            log_message('error', 'Error exporting overdue payments to Excel: ' . $e->getMessage());
            $this->session->set_flashdata('error', 'Gagal mengekspor data tunggakan ke Excel: ' . $e->getMessage());
            redirect('admin/financial_reports');
        }
    }

    // ==========================================
    // AJAX METHODS
    // ==========================================

    public function ajax_export_examination_reports()
    {
        $this->output->set_content_type('application/json');
        
        try {
            $filters = array(
                'start_date' => $this->input->post('start_date'),
                'end_date' => $this->input->post('end_date'),
                'status' => $this->input->post('status'),
                'jenis_pemeriksaan' => $this->input->post('jenis_pemeriksaan'),
                'search' => $this->input->post('search')
            );

            $count = $this->Excel_model->count_examination_data_for_export($filters);
            
            if ($count == 0) {
                $this->output->set_output(json_encode([
                    'success' => false,
                    'message' => 'Tidak ada data pemeriksaan untuk diekspor'
                ]));
                return;
            }

            $params = http_build_query($filters);
            $download_url = base_url('excel_controller/export_examination_reports?' . $params);
            
            $this->output->set_output(json_encode([
                'success' => true,
                'message' => "Siap mengekspor {$count} data pemeriksaan",
                'download_url' => $download_url,
                'record_count' => $count
            ]));
            
        } catch (Exception $e) {
            log_message('error', 'Error in ajax_export_examination_reports: ' . $e->getMessage());
            $this->output->set_output(json_encode([
                'success' => false,
                'message' => 'Terjadi kesalahan saat mempersiapkan export'
            ]));
        }
    }

    public function ajax_export_financial_reports()
    {
        $this->output->set_content_type('application/json');
        
        try {
            $filters = array(
                'start_date' => $this->input->post('start_date'),
                'end_date' => $this->input->post('end_date'),
                'status' => $this->input->post('status'),
                'jenis_pembayaran' => $this->input->post('jenis_pembayaran'),
                'metode_pembayaran' => $this->input->post('metode_pembayaran'),
                'search' => $this->input->post('search')
            );

            $count = $this->Excel_model->count_financial_data_for_export($filters);
            
            if ($count == 0) {
                $this->output->set_output(json_encode([
                    'success' => false,
                    'message' => 'Tidak ada data keuangan untuk diekspor'
                ]));
                return;
            }

            $params = http_build_query($filters);
            $download_url = base_url('excel_controller/export_financial_reports?' . $params);
            
            $this->output->set_output(json_encode([
                'success' => true,
                'message' => "Siap mengekspor {$count} data keuangan",
                'download_url' => $download_url,
                'record_count' => $count
            ]));
            
        } catch (Exception $e) {
            log_message('error', 'Error in ajax_export_financial_reports: ' . $e->getMessage());
            $this->output->set_output(json_encode([
                'success' => false,
                'message' => 'Terjadi kesalahan saat mempersiapkan export'
            ]));
        }
    }

    // ==========================================
    // PRIVATE EXCEL CREATION METHODS
    // ==========================================

    private function _create_examination_excel($examinations, $filters)
    {
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        
        $sheet->setTitle('Laporan Pemeriksaan');
        
        $spreadsheet->getProperties()
            ->setCreator('LabSy - Sistem Informasi Laboratorium')
            ->setTitle('Laporan Data Pemeriksaan')
            ->setSubject('Export Data Pemeriksaan')
            ->setDescription('Laporan data pemeriksaan laboratorium')
            ->setKeywords('laboratorium pemeriksaan export excel')
            ->setCategory('Reports');

        $this->_create_report_header($sheet, $filters, 'LAPORAN DATA PEMERIKSAAN LABORATORIUM', 'O');
        $this->_create_examination_table_headers($sheet);
        $this->_fill_examination_data($sheet, $examinations);
        $this->_apply_examination_formatting($sheet, count($examinations));
        $this->_auto_size_columns($sheet, 'A', 'O');
        
        return $spreadsheet;
    }

    private function _create_examination_summary_excel($summary, $filters)
    {
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        
        $sheet->setTitle('Ringkasan Pemeriksaan');
        $this->_create_examination_summary_content($sheet, $summary, $filters);
        
        return $spreadsheet;
    }

    private function _create_detailed_examination_excel($examinations, $filters)
    {
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        
        $sheet->setTitle('Detail Pemeriksaan');
        $this->_create_report_header($sheet, $filters, 'LAPORAN DETAIL PEMERIKSAAN LABORATORIUM', 'T');
        $this->_create_detailed_examination_headers($sheet);
        $this->_fill_detailed_examination_data($sheet, $examinations);
        $this->_apply_detailed_examination_formatting($sheet, count($examinations));
        $this->_auto_size_columns($sheet, 'A', 'T');
        
        return $spreadsheet;
    }

    private function _create_financial_excel($financial_data, $filters)
    {
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        
        $sheet->setTitle('Laporan Keuangan');
        
        $spreadsheet->getProperties()
            ->setCreator('LabSy - Sistem Informasi Laboratorium')
            ->setTitle('Laporan Data Keuangan')
            ->setSubject('Export Data Keuangan')
            ->setDescription('Laporan data keuangan laboratorium')
            ->setKeywords('laboratorium keuangan invoice export excel')
            ->setCategory('Financial Reports');

        $this->_create_report_header($sheet, $filters, 'LAPORAN DATA KEUANGAN LABORATORIUM', 'P');
        $this->_create_financial_statistics_section($sheet, $filters);
        $this->_create_financial_table_headers($sheet);
        $this->_fill_financial_data($sheet, $financial_data);
        $this->_apply_financial_formatting($sheet, count($financial_data));
        $this->_auto_size_columns($sheet, 'A', 'P');
        
        return $spreadsheet;
    }

    private function _create_financial_summary_excel($statistics, $payment_methods, $monthly_summary, $top_patients, $examination_revenue, $filters)
    {
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        
        $sheet->setTitle('Ringkasan Keuangan');
        $this->_create_comprehensive_summary_content($sheet, $statistics, $payment_methods, $monthly_summary, $top_patients, $examination_revenue, $filters);
        
        return $spreadsheet;
    }

    private function _create_overdue_payments_excel($overdue_data, $filters, $days_overdue)
    {
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        
        $sheet->setTitle('Tunggakan Pembayaran');
        $this->_create_report_header($sheet, $filters, 'LAPORAN TUNGGAKAN PEMBAYARAN (>' . $days_overdue . ' HARI)', 'H');
        $this->_create_overdue_table_headers($sheet);
        $this->_fill_overdue_data($sheet, $overdue_data);
        $this->_apply_overdue_formatting($sheet, count($overdue_data));
        $this->_auto_size_columns($sheet, 'A', 'H');
        
        return $spreadsheet;
    }

    // ==========================================
    // SHARED HEADER AND FORMATTING METHODS
    // ==========================================

    private function _create_report_header($sheet, $filters, $title, $max_col)
    {
        $sheet->setCellValue('A1', 'SISTEM INFORMASI LABORATORIUM');
        $sheet->setCellValue('A2', $title);
        $sheet->setCellValue('A3', 'Tanggal Export: ' . date('d F Y, H:i:s'));
        
        $filter_info = $this->_build_filter_info($filters);
        if (!empty($filter_info)) {
            $sheet->setCellValue('A4', 'Filter: ' . $filter_info);
        }
        
        $sheet->getStyle('A1:A2')->getFont()->setBold(true)->setSize(14);
        $sheet->getStyle('A1')->getFont()->setSize(16);
        $sheet->getStyle('A3:A4')->getFont()->setSize(10)->setItalic(true);
        
        $sheet->mergeCells('A1:' . $max_col . '1');
        $sheet->mergeCells('A2:' . $max_col . '2');
        $sheet->mergeCells('A3:' . $max_col . '3');
        if (!empty($filter_info)) {
            $sheet->mergeCells('A4:' . $max_col . '4');
        }
        
        $sheet->getStyle('A1:A4')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
    }

    private function _apply_blue_header_style($sheet, $range)
    {
        $sheet->getStyle($range)->applyFromArray([
            'font' => [
                'bold' => true,
                'color' => ['rgb' => 'FFFFFF'],
                'size' => 11
            ],
            'fill' => [
                'fillType' => Fill::FILL_SOLID,
                'color' => ['rgb' => '2563EB'] // Blue-600
            ],
            'alignment' => [
                'horizontal' => Alignment::HORIZONTAL_CENTER,
                'vertical' => Alignment::VERTICAL_CENTER
            ],
            'borders' => [
                'allBorders' => [
                    'borderStyle' => Border::BORDER_THIN,
                    'color' => ['rgb' => '000000']
                ]
            ]
        ]);
    }

    // ==========================================
    // EXAMINATION SPECIFIC METHODS
    // ==========================================

    private function _create_examination_table_headers($sheet)
    {
        $start_row = 6;
        
        $headers = [
            'A' => 'No', 'B' => 'Nomor Pemeriksaan', 'C' => 'Tanggal Pemeriksaan',
            'D' => 'Nama Pasien', 'E' => 'NIK', 'F' => 'Jenis Kelamin',
            'G' => 'Umur', 'H' => 'Alamat', 'I' => 'Telepon',
            'J' => 'Jenis Pemeriksaan', 'K' => 'Status', 'L' => 'Nama Petugas',
            'M' => 'Dokter Perujuk', 'N' => 'Biaya', 'O' => 'Keterangan'
        ];
        
        foreach ($headers as $column => $header) {
            $sheet->setCellValue($column . $start_row, $header);
        }
        
        $this->_apply_blue_header_style($sheet, 'A' . $start_row . ':O' . $start_row);
        $sheet->getRowDimension($start_row)->setRowHeight(25);
    }

    private function _create_detailed_examination_headers($sheet)
    {
        $start_row = 6;
        
        $headers = [
            'A' => 'No', 'B' => 'Nomor Pemeriksaan', 'C' => 'Tanggal',
            'D' => 'Nama Pasien', 'E' => 'NIK', 'F' => 'Jenis Kelamin',
            'G' => 'Umur', 'H' => 'Jenis Pemeriksaan', 'I' => 'Status',
            'J' => 'Petugas', 'K' => 'Dokter Perujuk', 'L' => 'Asal Rujukan',
            'M' => 'Diagnosis Awal', 'N' => 'Hasil Pemeriksaan', 'O' => 'Nilai Normal',
            'P' => 'Catatan', 'Q' => 'Tanggal Selesai', 'R' => 'Biaya',
            'S' => 'Status Pembayaran', 'T' => 'Keterangan'
        ];
        
        foreach ($headers as $column => $header) {
            $sheet->setCellValue($column . $start_row, $header);
        }
        
        $this->_apply_blue_header_style($sheet, 'A' . $start_row . ':T' . $start_row);
        $sheet->getRowDimension($start_row)->setRowHeight(25);
    }

    private function _fill_examination_data($sheet, $examinations)
    {
        $start_row = 7;
        $row = $start_row;
        $no = 1;
        
        foreach ($examinations as $exam) {
            $sheet->setCellValue('A' . $row, $no);
            $sheet->setCellValue('B' . $row, $exam['nomor_pemeriksaan']);
            $sheet->setCellValue('C' . $row, date('d/m/Y', strtotime($exam['tanggal_pemeriksaan'])));
            $sheet->setCellValue('D' . $row, $exam['nama_pasien']);
            $sheet->setCellValue('E' . $row, $exam['nik'] ?: '-');
            $sheet->setCellValue('F' . $row, $this->_format_gender($exam['jenis_kelamin']));
            $sheet->setCellValue('G' . $row, $exam['umur'] ? $exam['umur'] . ' tahun' : '-');
            $sheet->setCellValue('H' . $row, $exam['alamat_domisili'] ?: '-');
            $sheet->setCellValue('I' . $row, $exam['telepon'] ?: '-');
            $sheet->setCellValue('J' . $row, $exam['jenis_pemeriksaan']);
            $sheet->setCellValue('K' . $row, $this->_format_status($exam['status_pemeriksaan']));
            $sheet->setCellValue('L' . $row, $exam['nama_petugas'] ?: 'Belum ditugaskan');
            $sheet->setCellValue('M' . $row, $exam['dokter_perujuk'] ?: '-');
            $sheet->setCellValue('N' . $row, $exam['biaya'] ? 'Rp ' . number_format($exam['biaya'], 0, ',', '.') : '-');
            $sheet->setCellValue('O' . $row, $exam['keterangan'] ?: '-');
            
            $row++;
            $no++;
        }
    }

    private function _fill_detailed_examination_data($sheet, $examinations)
    {
        $start_row = 7;
        $row = $start_row;
        $no = 1;
        
        foreach ($examinations as $exam) {
            $sheet->setCellValue('A' . $row, $no);
            $sheet->setCellValue('B' . $row, $exam['nomor_pemeriksaan']);
            $sheet->setCellValue('C' . $row, date('d/m/Y', strtotime($exam['tanggal_pemeriksaan'])));
            $sheet->setCellValue('D' . $row, $exam['nama_pasien']);
            $sheet->setCellValue('E' . $row, $exam['nik'] ?: '-');
            $sheet->setCellValue('F' . $row, $this->_format_gender($exam['jenis_kelamin']));
            $sheet->setCellValue('G' . $row, $exam['umur'] ? $exam['umur'] . ' tahun' : '-');
            $sheet->setCellValue('H' . $row, $exam['jenis_pemeriksaan']);
            $sheet->setCellValue('I' . $row, $this->_format_status($exam['status_pemeriksaan']));
            $sheet->setCellValue('J' . $row, $exam['nama_petugas'] ?: 'Belum ditugaskan');
            $sheet->setCellValue('K' . $row, $exam['dokter_perujuk'] ?: '-');
            $sheet->setCellValue('L' . $row, $exam['asal_rujukan'] ?: '-');
            $sheet->setCellValue('M' . $row, $exam['diagnosis_awal'] ?: '-');
            
            $hasil_pemeriksaan = $this->_format_examination_results($exam['hasil_pemeriksaan'], $exam['jenis_pemeriksaan']);
            $sheet->setCellValue('N' . $row, $hasil_pemeriksaan['hasil']);
            $sheet->setCellValue('O' . $row, $hasil_pemeriksaan['normal']);
            $sheet->setCellValue('P' . $row, $hasil_pemeriksaan['catatan']);
            
            $sheet->setCellValue('Q' . $row, $exam['completed_at'] ? date('d/m/Y H:i', strtotime($exam['completed_at'])) : '-');
            $sheet->setCellValue('R' . $row, $exam['biaya'] ? 'Rp ' . number_format($exam['biaya'], 0, ',', '.') : '-');
            $sheet->setCellValue('S' . $row, 'Belum Lunas');
            $sheet->setCellValue('T' . $row, $exam['keterangan'] ?: '-');
            
            $row++;
            $no++;
        }
    }

    private function _apply_examination_formatting($sheet, $data_count)
    {
        $start_row = 7;
        $end_row = $start_row + $data_count - 1;
        $data_range = 'A' . $start_row . ':O' . $end_row;
        
        $sheet->getStyle($data_range)->applyFromArray([
            'borders' => [
                'allBorders' => [
                    'borderStyle' => Border::BORDER_THIN,
                    'color' => ['rgb' => '000000']
                ]
            ],
            'alignment' => [
                'vertical' => Alignment::VERTICAL_CENTER
            ]
        ]);
        
        for ($i = $start_row; $i <= $end_row; $i++) {
            if (($i - $start_row) % 2 == 1) {
                $sheet->getStyle('A' . $i . ':O' . $i)->applyFromArray([
                    'fill' => [
                        'fillType' => Fill::FILL_SOLID,
                        'color' => ['rgb' => 'F8FAFC']
                    ]
                ]);
            }
        }
        
        $sheet->getStyle('A' . $start_row . ':A' . $end_row)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
        $sheet->getStyle('C' . $start_row . ':C' . $end_row)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
        $sheet->getStyle('F' . $start_row . ':F' . $end_row)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
        $sheet->getStyle('G' . $start_row . ':G' . $end_row)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
        $sheet->getStyle('K' . $start_row . ':K' . $end_row)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
        $sheet->getStyle('N' . $start_row . ':N' . $end_row)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_RIGHT);
    }

    private function _apply_detailed_examination_formatting($sheet, $data_count)
    {
        $start_row = 7;
        $end_row = $start_row + $data_count - 1;
        $data_range = 'A' . $start_row . ':T' . $end_row;
        
        $sheet->getStyle($data_range)->applyFromArray([
            'borders' => [
                'allBorders' => [
                    'borderStyle' => Border::BORDER_THIN,
                    'color' => ['rgb' => '000000']
                ]
            ],
            'alignment' => [
                'vertical' => Alignment::VERTICAL_CENTER
            ]
        ]);
        
        for ($i = $start_row; $i <= $end_row; $i++) {
            if (($i - $start_row) % 2 == 1) {
                $sheet->getStyle('A' . $i . ':T' . $i)->applyFromArray([
                    'fill' => [
                        'fillType' => Fill::FILL_SOLID,
                        'color' => ['rgb' => 'F8FAFC']
                    ]
                ]);
            }
        }
        
        $sheet->getStyle('A' . $start_row . ':A' . $end_row)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
        $sheet->getStyle('C' . $start_row . ':C' . $end_row)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
        $sheet->getStyle('F' . $start_row . ':F' . $end_row)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
        $sheet->getStyle('I' . $start_row . ':I' . $end_row)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
        $sheet->getStyle('R' . $start_row . ':R' . $end_row)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_RIGHT);
    }

    private function _create_examination_summary_content($sheet, $summary, $filters)
    {
        $sheet->setCellValue('A1', 'RINGKASAN PEMERIKSAAN LABORATORIUM');
        $sheet->setCellValue('A2', 'Periode: ' . $this->_format_date_range($filters));
        
        $sheet->getStyle('A1:B1')->getFont()->setBold(true)->setSize(14);
        $sheet->mergeCells('A1:E1');
        $sheet->getStyle('A1')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
        
        $row = 4;
        $sheet->setCellValue('A' . $row, 'STATISTIK PEMERIKSAAN');
        $sheet->getStyle('A' . $row)->getFont()->setBold(true)->setSize(12);
        $row++;
        
        $sheet->setCellValue('A' . $row, 'Keterangan');
        $sheet->setCellValue('B' . $row, 'Jumlah');
        $this->_apply_blue_header_style($sheet, 'A' . $row . ':B' . $row);
        $row++;
        
        $stats = [
            'Total Pemeriksaan' => $summary['total_examinations'],
            'Selesai' => $summary['completed'],
            'Dalam Proses' => $summary['in_progress'],
            'Pending' => $summary['pending'],
            'Dibatalkan' => $summary['cancelled']
        ];
        
        foreach ($stats as $label => $value) {
            $sheet->setCellValue('A' . $row, $label);
            $sheet->setCellValue('B' . $row, $value);
            $sheet->getStyle('A' . $row . ':B' . $row)->applyFromArray([
                'borders' => [
                    'allBorders' => [
                        'borderStyle' => Border::BORDER_THIN,
                        'color' => ['rgb' => '000000']
                    ]
                ]
            ]);
            $row++;
        }
        
        $row += 2;
        $sheet->setCellValue('A' . $row, 'BERDASARKAN JENIS PEMERIKSAAN');
        $sheet->getStyle('A' . $row)->getFont()->setBold(true)->setSize(12);
        $row++;
        
        $sheet->setCellValue('A' . $row, 'Jenis Pemeriksaan');
        $sheet->setCellValue('B' . $row, 'Jumlah');
        $this->_apply_blue_header_style($sheet, 'A' . $row . ':B' . $row);
        $row++;
        
        if (!empty($summary['by_type'])) {
            foreach ($summary['by_type'] as $type => $count) {
                $sheet->setCellValue('A' . $row, $type);
                $sheet->setCellValue('B' . $row, $count);
                $sheet->getStyle('A' . $row . ':B' . $row)->applyFromArray([
                    'borders' => [
                        'allBorders' => [
                            'borderStyle' => Border::BORDER_THIN,
                            'color' => ['rgb' => '000000']
                        ]
                    ]
                ]);
                $row++;
            }
        }
        
        $sheet->getColumnDimension('A')->setAutoSize(true);
        $sheet->getColumnDimension('B')->setAutoSize(true);
    }

    // ==========================================
    // FINANCIAL SPECIFIC METHODS
    // ==========================================

    private function _create_financial_statistics_section($sheet, $filters)
    {
        $statistics = $this->Excel_model->get_financial_statistics_for_export($filters);
        
        $start_row = empty($this->_build_filter_info($filters)) ? 5 : 6;
        
        $sheet->setCellValue('A' . $start_row, 'RINGKASAN STATISTIK');
        $sheet->getStyle('A' . $start_row)->getFont()->setBold(true)->setSize(12);
        $sheet->mergeCells('A' . $start_row . ':P' . $start_row);
        $start_row++;
        
        $stats_data = [
            ['Total Invoice', $statistics['total_invoices']],
            ['Total Pendapatan', 'Rp ' . number_format($statistics['total_revenue'], 0, ',', '.')],
            ['Pendapatan Lunas', 'Rp ' . number_format($statistics['paid_revenue'], 0, ',', '.')],
            ['Piutang', 'Rp ' . number_format($statistics['unpaid_revenue'], 0, ',', '.')],
            ['Cicilan', 'Rp ' . number_format($statistics['installment_revenue'], 0, ',', '.')],
            ['Tingkat Pelunasan', $statistics['payment_rate'] . '%']
        ];
        
        $col1 = 'A'; $col2 = 'B'; $col3 = 'E'; $col4 = 'F';
        
        for ($i = 0; $i < count($stats_data); $i++) {
            if ($i < 3) {
                $sheet->setCellValue($col1 . ($start_row + $i), $stats_data[$i][0]);
                $sheet->setCellValue($col2 . ($start_row + $i), $stats_data[$i][1]);
            } else {
                $sheet->setCellValue($col3 . ($start_row + ($i - 3)), $stats_data[$i][0]);
                $sheet->setCellValue($col4 . ($start_row + ($i - 3)), $stats_data[$i][1]);
            }
        }
        
        $stats_range = 'A' . $start_row . ':F' . ($start_row + 2);
        $sheet->getStyle($stats_range)->applyFromArray([
            'font' => ['bold' => true],
            'fill' => [
                'fillType' => Fill::FILL_SOLID,
                'color' => ['rgb' => 'F3F4F6']
            ],
            'borders' => [
                'allBorders' => [
                    'borderStyle' => Border::BORDER_THIN,
                    'color' => ['rgb' => '000000']
                ]
            ]
        ]);
    }

    private function _create_financial_table_headers($sheet)
    {
        $start_row = empty($this->_build_filter_info($this->input->get())) ? 10 : 11;
        
        $headers = [
            'A' => 'No', 'B' => 'Nomor Invoice', 'C' => 'Tanggal Invoice',
            'D' => 'Nama Pasien', 'E' => 'NIK', 'F' => 'Nomor Pemeriksaan',
            'G' => 'Jenis Pemeriksaan', 'H' => 'Tanggal Pemeriksaan', 'I' => 'Jenis Pembayaran',
            'J' => 'Total Biaya', 'K' => 'Status Pembayaran', 'L' => 'Metode Pembayaran',
            'M' => 'Tanggal Pembayaran', 'N' => 'Nomor BPJS', 'O' => 'Telepon', 'P' => 'Keterangan'
        ];
        
        foreach ($headers as $column => $header) {
            $sheet->setCellValue($column . $start_row, $header);
        }
        
        $this->_apply_blue_header_style($sheet, 'A' . $start_row . ':P' . $start_row);
        $sheet->getRowDimension($start_row)->setRowHeight(25);
    }

    private function _create_overdue_table_headers($sheet)
    {
        $start_row = 6;
        
        $headers = [
            'A' => 'No', 'B' => 'Nomor Invoice', 'C' => 'Tanggal Invoice',
            'D' => 'Nama Pasien', 'E' => 'Telepon', 'F' => 'Total Biaya',
            'G' => 'Hari Terlambat', 'H' => 'Nomor Pemeriksaan'
        ];
        
        foreach ($headers as $column => $header) {
            $sheet->setCellValue($column . $start_row, $header);
        }
        
        $this->_apply_blue_header_style($sheet, 'A' . $start_row . ':H' . $start_row);
        $sheet->getRowDimension($start_row)->setRowHeight(25);
    }

    private function _fill_financial_data($sheet, $financial_data)
    {
        $start_row = empty($this->_build_filter_info($this->input->get())) ? 11 : 12;
        $row = $start_row;
        $no = 1;
        
        foreach ($financial_data as $invoice) {
            $sheet->setCellValue('A' . $row, $no);
            $sheet->setCellValue('B' . $row, $invoice['nomor_invoice']);
            $sheet->setCellValue('C' . $row, date('d/m/Y', strtotime($invoice['tanggal_invoice'])));
            $sheet->setCellValue('D' . $row, $invoice['nama_pasien']);
            $sheet->setCellValue('E' . $row, $invoice['nik'] ?: '-');
            $sheet->setCellValue('F' . $row, $invoice['nomor_pemeriksaan']);
            $sheet->setCellValue('G' . $row, $invoice['jenis_pemeriksaan']);
            $sheet->setCellValue('H' . $row, date('d/m/Y', strtotime($invoice['tanggal_pemeriksaan'])));
            $sheet->setCellValue('I' . $row, strtoupper($invoice['jenis_pembayaran']));
            $sheet->setCellValue('J' . $row, 'Rp ' . number_format($invoice['total_biaya'], 0, ',', '.'));
            $sheet->setCellValue('K' . $row, $this->_format_payment_status($invoice['status_pembayaran']));
            $sheet->setCellValue('L' . $row, $invoice['metode_pembayaran'] ?: '-');
            $sheet->setCellValue('M' . $row, $invoice['tanggal_pembayaran'] ? date('d/m/Y', strtotime($invoice['tanggal_pembayaran'])) : '-');
            $sheet->setCellValue('N' . $row, $invoice['nomor_kartu_bpjs'] ?: '-');
            $sheet->setCellValue('O' . $row, $invoice['telepon'] ?: '-');
            $sheet->setCellValue('P' . $row, $invoice['invoice_keterangan'] ?: '-');
            
            $row++;
            $no++;
        }
    }

    private function _fill_overdue_data($sheet, $overdue_data)
    {
        $start_row = 7;
        $row = $start_row;
        $no = 1;
        
        foreach ($overdue_data as $item) {
            $sheet->setCellValue('A' . $row, $no);
            $sheet->setCellValue('B' . $row, $item['nomor_invoice']);
            $sheet->setCellValue('C' . $row, date('d/m/Y', strtotime($item['tanggal_invoice'])));
            $sheet->setCellValue('D' . $row, $item['nama_pasien']);
            $sheet->setCellValue('E' . $row, $item['telepon'] ?: '-');
            $sheet->setCellValue('F' . $row, 'Rp ' . number_format($item['total_biaya'], 0, ',', '.'));
            $sheet->setCellValue('G' . $row, $item['hari_terlambat'] . ' hari');
            $sheet->setCellValue('H' . $row, $item['nomor_pemeriksaan']);
            
            $row++;
            $no++;
        }
    }

    private function _apply_financial_formatting($sheet, $data_count)
    {
        $start_row = empty($this->_build_filter_info($this->input->get())) ? 11 : 12;
        $end_row = $start_row + $data_count - 1;
        $data_range = 'A' . $start_row . ':P' . $end_row;
        
        $sheet->getStyle($data_range)->applyFromArray([
            'borders' => [
                'allBorders' => [
                    'borderStyle' => Border::BORDER_THIN,
                    'color' => ['rgb' => '000000']
                ]
            ],
            'alignment' => [
                'vertical' => Alignment::VERTICAL_CENTER
            ]
        ]);
        
        for ($i = $start_row; $i <= $end_row; $i++) {
            if (($i - $start_row) % 2 == 1) {
                $sheet->getStyle('A' . $i . ':P' . $i)->applyFromArray([
                    'fill' => [
                        'fillType' => Fill::FILL_SOLID,
                        'color' => ['rgb' => 'F8FAFC']
                    ]
                ]);
            }
        }
        
        $sheet->getStyle('A' . $start_row . ':A' . $end_row)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
        $sheet->getStyle('C' . $start_row . ':C' . $end_row)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
        $sheet->getStyle('H' . $start_row . ':H' . $end_row)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
        $sheet->getStyle('I' . $start_row . ':I' . $end_row)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
        $sheet->getStyle('J' . $start_row . ':J' . $end_row)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_RIGHT);
        $sheet->getStyle('K' . $start_row . ':K' . $end_row)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
        $sheet->getStyle('M' . $start_row . ':M' . $end_row)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
    }

    private function _apply_overdue_formatting($sheet, $data_count)
    {
        $start_row = 7;
        $end_row = $start_row + $data_count - 1;
        $data_range = 'A' . $start_row . ':H' . $end_row;
        
        $sheet->getStyle($data_range)->applyFromArray([
            'borders' => [
                'allBorders' => [
                    'borderStyle' => Border::BORDER_THIN,
                    'color' => ['rgb' => '000000']
                ]
            ],
            'alignment' => [
                'vertical' => Alignment::VERTICAL_CENTER
            ]
        ]);
        
        for ($i = $start_row; $i <= $end_row; $i++) {
            if (($i - $start_row) % 2 == 1) {
                $sheet->getStyle('A' . $i . ':H' . $i)->applyFromArray([
                    'fill' => [
                        'fillType' => Fill::FILL_SOLID,
                        'color' => ['rgb' => 'FEF2F2']
                    ]
                ]);
            }
        }
        
        $sheet->getStyle('A' . $start_row . ':A' . $end_row)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
        $sheet->getStyle('C' . $start_row . ':C' . $end_row)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
        $sheet->getStyle('F' . $start_row . ':F' . $end_row)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_RIGHT);
        $sheet->getStyle('G' . $start_row . ':G' . $end_row)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
    }

    private function _create_comprehensive_summary_content($sheet, $statistics, $payment_methods, $monthly_summary, $top_patients, $examination_revenue, $filters)
    {
        $sheet->setCellValue('A1', 'RINGKASAN KEUANGAN LABORATORIUM');
        $sheet->setCellValue('A2', 'Periode: ' . $this->_format_date_range($filters));
        
        $sheet->getStyle('A1:B1')->getFont()->setBold(true)->setSize(14);
        $sheet->mergeCells('A1:E1');
        $sheet->getStyle('A1')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
        
        $row = 4;
        
        $sheet->setCellValue('A' . $row, 'STATISTIK KESELURUHAN');
        $sheet->getStyle('A' . $row)->getFont()->setBold(true)->setSize(12);
        $row++;
        
        $this->_create_statistics_table($sheet, $row, $statistics);
        $row += 8;
        
        if (!empty($payment_methods)) {
            $sheet->setCellValue('A' . $row, 'METODE PEMBAYARAN');
            $sheet->getStyle('A' . $row)->getFont()->setBold(true)->setSize(12);
            $row++;
            
            $this->_create_payment_methods_table($sheet, $row, $payment_methods);
            $row += count($payment_methods) + 3;
        }
        
        if (!empty($top_patients)) {
            $sheet->setCellValue('A' . $row, 'TOP 10 PASIEN BERDASARKAN PENDAPATAN');
            $sheet->getStyle('A' . $row)->getFont()->setBold(true)->setSize(12);
            $row++;
            
            $this->_create_top_patients_table($sheet, $row, $top_patients);
            $row += count($top_patients) + 3;
        }
        
        if (!empty($examination_revenue)) {
            $sheet->setCellValue('A' . $row, 'PENDAPATAN BERDASARKAN JENIS PEMERIKSAAN');
            $sheet->getStyle('A' . $row)->getFont()->setBold(true)->setSize(12);
            $row++;
            
            $this->_create_examination_revenue_table($sheet, $row, $examination_revenue);
        }
        
        foreach(range('A','E') as $col) {
            $sheet->getColumnDimension($col)->setAutoSize(true);
        }
    }

    private function _create_statistics_table($sheet, $start_row, $statistics)
    {
        $sheet->setCellValue('A' . $start_row, 'Keterangan');
        $sheet->setCellValue('B' . $start_row, 'Nilai');
        
        $this->_apply_blue_header_style($sheet, 'A' . $start_row . ':B' . $start_row);
        $start_row++;
        
        $stats_data = [
            ['Total Invoice', $statistics['total_invoices']],
            ['Total Pendapatan', 'Rp ' . number_format($statistics['total_revenue'], 0, ',', '.')],
            ['Pendapatan Lunas', 'Rp ' . number_format($statistics['paid_revenue'], 0, ',', '.')],
            ['Piutang', 'Rp ' . number_format($statistics['unpaid_revenue'], 0, ',', '.')],
            ['Cicilan', 'Rp ' . number_format($statistics['installment_revenue'], 0, ',', '.')],
            ['Tingkat Pelunasan', $statistics['payment_rate'] . '%']
        ];
        
        foreach ($stats_data as $index => $data) {
            $sheet->setCellValue('A' . ($start_row + $index), $data[0]);
            $sheet->setCellValue('B' . ($start_row + $index), $data[1]);
            
            $sheet->getStyle('A' . ($start_row + $index) . ':B' . ($start_row + $index))->applyFromArray([
                'borders' => [
                    'allBorders' => [
                        'borderStyle' => Border::BORDER_THIN,
                        'color' => ['rgb' => '000000']
                    ]
                ]
            ]);
        }
    }

    private function _create_payment_methods_table($sheet, $start_row, $payment_methods)
    {
        $sheet->setCellValue('A' . $start_row, 'Metode Pembayaran');
        $sheet->setCellValue('B' . $start_row, 'Jumlah Transaksi');
        $sheet->setCellValue('C' . $start_row, 'Total Nilai');
        
        $this->_apply_blue_header_style($sheet, 'A' . $start_row . ':C' . $start_row);
        $start_row++;
        
        foreach ($payment_methods as $index => $method) {
            $sheet->setCellValue('A' . ($start_row + $index), $method['metode_pembayaran'] ?: 'Belum Ditentukan');
            $sheet->setCellValue('B' . ($start_row + $index), $method['jumlah']);
            $sheet->setCellValue('C' . ($start_row + $index), 'Rp ' . number_format($method['total_nilai'], 0, ',', '.'));
            
            $sheet->getStyle('A' . ($start_row + $index) . ':C' . ($start_row + $index))->applyFromArray([
                'borders' => [
                    'allBorders' => [
                        'borderStyle' => Border::BORDER_THIN,
                        'color' => ['rgb' => '000000']
                    ]
                ]
            ]);
        }
    }

    private function _create_top_patients_table($sheet, $start_row, $top_patients)
    {
        $sheet->setCellValue('A' . $start_row, 'Nama Pasien');
        $sheet->setCellValue('B' . $start_row, 'NIK');
        $sheet->setCellValue('C' . $start_row, 'Jumlah Invoice');
        $sheet->setCellValue('D' . $start_row, 'Total Pendapatan');
        
        $this->_apply_blue_header_style($sheet, 'A' . $start_row . ':D' . $start_row);
        $start_row++;
        
        foreach ($top_patients as $index => $patient) {
            $sheet->setCellValue('A' . ($start_row + $index), $patient['nama_pasien']);
            $sheet->setCellValue('B' . ($start_row + $index), $patient['nik'] ?: '-');
            $sheet->setCellValue('C' . ($start_row + $index), $patient['jumlah_invoice']);
            $sheet->setCellValue('D' . ($start_row + $index), 'Rp ' . number_format($patient['total_pendapatan'], 0, ',', '.'));
            
            $sheet->getStyle('A' . ($start_row + $index) . ':D' . ($start_row + $index))->applyFromArray([
                'borders' => [
                    'allBorders' => [
                        'borderStyle' => Border::BORDER_THIN,
                        'color' => ['rgb' => '000000']
                    ]
                ]
            ]);
        }
    }

    private function _create_examination_revenue_table($sheet, $start_row, $examination_revenue)
    {
        $sheet->setCellValue('A' . $start_row, 'Jenis Pemeriksaan');
        $sheet->setCellValue('B' . $start_row, 'Jumlah Invoice');
        $sheet->setCellValue('C' . $start_row, 'Total Pendapatan');
        $sheet->setCellValue('D' . $start_row, 'Rata-rata Biaya');
        
        $this->_apply_blue_header_style($sheet, 'A' . $start_row . ':D' . $start_row);
        $start_row++;
        
        foreach ($examination_revenue as $index => $exam) {
            $sheet->setCellValue('A' . ($start_row + $index), $exam['jenis_pemeriksaan']);
            $sheet->setCellValue('B' . ($start_row + $index), $exam['jumlah_invoice']);
            $sheet->setCellValue('C' . ($start_row + $index), 'Rp ' . number_format($exam['total_pendapatan'], 0, ',', '.'));
            $sheet->setCellValue('D' . ($start_row + $index), 'Rp ' . number_format($exam['rata_rata_biaya'], 0, ',', '.'));
            
            $sheet->getStyle('A' . ($start_row + $index) . ':D' . ($start_row + $index))->applyFromArray([
                'borders' => [
                    'allBorders' => [
                        'borderStyle' => Border::BORDER_THIN,
                        'color' => ['rgb' => '000000']
                    ]
                ]
            ]);
        }
    }

    // ==========================================
    // SHARED UTILITY METHODS
    // ==========================================

    private function _auto_size_columns($sheet, $start_col, $end_col)
    {
        $columns = range($start_col, $end_col);
        foreach ($columns as $column) {
            $sheet->getColumnDimension($column)->setAutoSize(true);
        }
    }

    private function _build_filter_info($filters)
    {
        $info_parts = [];
        
        if (!empty($filters['start_date'])) {
            $info_parts[] = 'Dari: ' . date('d/m/Y', strtotime($filters['start_date']));
        }
        
        if (!empty($filters['end_date'])) {
            $info_parts[] = 'Sampai: ' . date('d/m/Y', strtotime($filters['end_date']));
        }
        
        if (!empty($filters['status'])) {
            $info_parts[] = 'Status: ' . (isset($filters['jenis_pembayaran']) ? $this->_format_payment_status($filters['status']) : $this->_format_status($filters['status']));
        }
        
        if (!empty($filters['jenis_pemeriksaan'])) {
            $info_parts[] = 'Jenis Pemeriksaan: ' . $filters['jenis_pemeriksaan'];
        }
        
        if (!empty($filters['jenis_pembayaran'])) {
            $info_parts[] = 'Jenis Pembayaran: ' . strtoupper($filters['jenis_pembayaran']);
        }
        
        if (!empty($filters['metode_pembayaran'])) {
            $info_parts[] = 'Metode: ' . $filters['metode_pembayaran'];
        }
        
        if (!empty($filters['search'])) {
            $info_parts[] = 'Pencarian: ' . $filters['search'];
        }
        
        return implode(' | ', $info_parts);
    }

    private function _format_date_range($filters)
    {
        if (!empty($filters['start_date']) && !empty($filters['end_date'])) {
            return date('d/m/Y', strtotime($filters['start_date'])) . ' - ' . date('d/m/Y', strtotime($filters['end_date']));
        } elseif (!empty($filters['start_date'])) {
            return 'Dari ' . date('d/m/Y', strtotime($filters['start_date']));
        } elseif (!empty($filters['end_date'])) {
            return 'Sampai ' . date('d/m/Y', strtotime($filters['end_date']));
        } else {
            return 'Semua Data';
        }
    }

    private function _format_gender($gender)
    {
        switch(strtoupper($gender)) {
            case 'L': return 'Laki-laki';
            case 'P': return 'Perempuan';
            default: return 'Tidak Diketahui';
        }
    }

    private function _format_status($status)
    {
        switch(strtolower($status)) {
            case 'pending': return 'Pending';
            case 'progress': return 'Dalam Proses';
            case 'selesai': return 'Selesai';
            case 'cancelled': return 'Dibatalkan';
            default: return ucfirst($status);
        }
    }

    private function _format_payment_status($status)
    {
        switch(strtolower($status)) {
            case 'lunas': return 'Lunas';
            case 'belum_bayar': return 'Belum Bayar';
            case 'cicilan': return 'Cicilan';
            default: return ucfirst($status);
        }
    }

    private function _format_examination_results($results, $jenis_pemeriksaan)
    {
        $formatted = array(
            'hasil' => 'Belum ada hasil',
            'normal' => '',
            'catatan' => ''
        );

        if (empty($results)) {
            return $formatted;
        }

        switch(strtolower($jenis_pemeriksaan)) {
            case 'kimia darah':
                $hasil_parts = [];
                if (!empty($results['gula_darah_puasa'])) $hasil_parts[] = "GDP: {$results['gula_darah_puasa']} mg/dL";
                if (!empty($results['cholesterol_total'])) $hasil_parts[] = "Kolesterol: {$results['cholesterol_total']} mg/dL";
                if (!empty($results['asam_urat'])) $hasil_parts[] = "Asam Urat: {$results['asam_urat']} mg/dL";
                if (!empty($results['ureum'])) $hasil_parts[] = "Ureum: {$results['ureum']} mg/dL";
                if (!empty($results['creatinin'])) $hasil_parts[] = "Kreatinin: {$results['creatinin']} mg/dL";
                
                $formatted['hasil'] = !empty($hasil_parts) ? implode('; ', $hasil_parts) : 'Hasil belum lengkap';
                $formatted['normal'] = 'GDP: 70-100 mg/dL; Kolesterol: <200 mg/dL; Asam Urat: 3.5-7.2 mg/dL';
                break;

            case 'hematologi':
                $hasil_parts = [];
                if (!empty($results['hemoglobin'])) $hasil_parts[] = "Hb: {$results['hemoglobin']} g/dL";
                if (!empty($results['hematokrit'])) $hasil_parts[] = "Ht: {$results['hematokrit']}%";
                if (!empty($results['laju_endap_darah'])) $hasil_parts[] = "LED: {$results['laju_endap_darah']} mm/jam";
                if (!empty($results['golongan_darah'])) $hasil_parts[] = "Gol.Darah: {$results['golongan_darah']}{$results['rhesus']}";
                
                $formatted['hasil'] = !empty($hasil_parts) ? implode('; ', $hasil_parts) : 'Hasil belum lengkap';
                $formatted['normal'] = 'Hb: 12-16 g/dL; Ht: 37-47%; LED: <20 mm/jam';
                break;

            default:
                if (is_array($results)) {
                    $hasil_parts = [];
                    foreach ($results as $key => $value) {
                        if (!in_array($key, ['created_at', 'pemeriksaan_id']) && !empty($value)) {
                            $hasil_parts[] = ucfirst(str_replace('_', ' ', $key)) . ": " . $value;
                        }
                    }
                    $formatted['hasil'] = !empty($hasil_parts) ? implode('; ', $hasil_parts) : 'Hasil belum tersedia';
                }
                break;
        }

        return $formatted;
    }

    private function _generate_filename($prefix, $filters)
    {
        $date_suffix = '';
        
        if (!empty($filters['start_date']) && !empty($filters['end_date'])) {
            $start = date('d-m-Y', strtotime($filters['start_date']));
            $end = date('d-m-Y', strtotime($filters['end_date']));
            $date_suffix = "_{$start}_to_{$end}";
        } elseif (!empty($filters['start_date'])) {
            $date_suffix = '_from_' . date('d-m-Y', strtotime($filters['start_date']));
        } elseif (!empty($filters['end_date'])) {
            $date_suffix = '_until_' . date('d-m-Y', strtotime($filters['end_date']));
        } else {
            $date_suffix = '_' . date('d-m-Y');
        }
        
        return $prefix . $date_suffix . '_' . date('His') . '.xlsx';
    }

    private function _output_excel($spreadsheet, $filename)
    {
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment; filename="' . $filename . '"');
        header('Cache-Control: max-age=0');
        header('Cache-Control: max-age=1');
        header('Expires: Mon, 26 Jul 1997 05:00:00 GMT');
        header('Last-Modified: ' . gmdate('D, d M Y H:i:s') . ' GMT');
        header('Cache-Control: cache, must-revalidate');
        header('Pragma: public');

        $writer = new Xlsx($spreadsheet);
        $writer->save('php://output');
        
        $spreadsheet->disconnectWorksheets();
        unset($spreadsheet);
        
        exit();
    }
      public function export_patients()
    {
        try {
            $filters = array(
                'start_date' => $this->input->get('start_date'),
                'end_date' => $this->input->get('end_date'),
                'gender' => $this->input->get('gender'),
                'search' => $this->input->get('search')
            );

            $patients_data = $this->Excel_model->get_patients_for_export($filters);
            
            if (empty($patients_data['patients'])) {
                $this->session->set_flashdata('error', 'Tidak ada data pasien untuk diekspor');
                redirect($_SERVER['HTTP_REFERER']);
                return;
            }

            $this->_generate_patients_excel($patients_data['patients'], $patients_data['stats'], $filters);

        } catch (Exception $e) {
            log_message('error', 'Error exporting patients: ' . $e->getMessage());
            $this->session->set_flashdata('error', 'Gagal mengekspor data pasien: ' . $e->getMessage());
            redirect($_SERVER['HTTP_REFERER']);
        }
    }

    private function _generate_patients_excel($patients, $stats, $filters)
    {
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        
        // ==========================================
        // SET UP SHEET PROPERTIES
        // ==========================================
        
        $sheet->setTitle('Data Pasien');
        
        // Set default font
        $spreadsheet->getDefaultStyle()->getFont()->setName('Arial')->setSize(10);
        
        // Set document properties
        $spreadsheet->getProperties()
            ->setCreator('LabSy - Sistem Informasi Laboratorium')
            ->setTitle('Laporan Data Pasien')
            ->setSubject('Export Data Pasien')
            ->setDescription('Laporan data pasien laboratorium')
            ->setKeywords('laboratorium pasien export excel')
            ->setCategory('Patient Reports');
        
        // ==========================================
        // HEADER SECTION
        // ==========================================
        
        // Logo and Title
        $sheet->mergeCells('A1:T3');
        $sheet->setCellValue('A1', 'LAPORAN DATA PASIEN');
        $sheet->getStyle('A1')->getFont()->setSize(20)->setBold(true);
        $sheet->getStyle('A1')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
        $sheet->getStyle('A1')->getFill()->setFillType(Fill::FILL_SOLID)->getStartColor()->setRGB('2563EB');
        $sheet->getStyle('A1')->getFont()->getColor()->setRGB('FFFFFF');
        
        // Lab Information
        $row = 5;
        $sheet->setCellValue("A{$row}", 'Laboratorium LabSy');
        $sheet->getStyle("A{$row}")->getFont()->setSize(14)->setBold(true);
        
        $row++;
        $sheet->setCellValue("A{$row}", 'Jl. Tata Bumi No.3, Area Sawah, Banyuraden, Kec. Gamping, Kabupaten Sleman, DI Yogyakarta 55293');
        
        $row++;
        $sheet->setCellValue("A{$row}", 'Telepon: (0274) 617601 | Email: info@labsy.com');
        
        // Export Information
        $row += 2;
        $sheet->setCellValue("A{$row}", 'Tanggal Export: ' . date('d F Y, H:i:s'));
        $sheet->getStyle("A{$row}")->getFont()->setBold(true);
        
        $row++;
        $exported_by = $this->session->userdata('username');
        $sheet->setCellValue("A{$row}", 'Diekspor oleh: ' . $exported_by);
        
        // Filter Information
        if (!empty($filters['start_date']) || !empty($filters['end_date']) || !empty($filters['gender']) || !empty($filters['search'])) {
            $row += 2;
            $sheet->setCellValue("A{$row}", 'FILTER YANG DITERAPKAN:');
            $sheet->getStyle("A{$row}")->getFont()->setBold(true)->getColor()->setRGB('2563EB');
            
            if (!empty($filters['start_date'])) {
                $row++;
                $sheet->setCellValue("A{$row}", '• Tanggal Mulai: ' . date('d F Y', strtotime($filters['start_date'])));
            }
            
            if (!empty($filters['end_date'])) {
                $row++;
                $sheet->setCellValue("A{$row}", '• Tanggal Akhir: ' . date('d F Y', strtotime($filters['end_date'])));
            }
            
            if (!empty($filters['gender'])) {
                $gender_text = $filters['gender'] === 'L' ? 'Laki-laki' : 'Perempuan';
                $row++;
                $sheet->setCellValue("A{$row}", '• Jenis Kelamin: ' . $gender_text);
            }
            
            if (!empty($filters['search'])) {
                $row++;
                $sheet->setCellValue("A{$row}", '• Pencarian: ' . $filters['search']);
            }
        }
        
        // ==========================================
        // STATISTICS SECTION
        // ==========================================
        
        $row += 3;
        $sheet->setCellValue("A{$row}", 'STATISTIK DATA');
        $sheet->getStyle("A{$row}")->getFont()->setSize(12)->setBold(true)->getColor()->setRGB('2563EB');
        
        $row++;
        $stats_data = [
            ['Total Pasien', $stats['total']],
            ['Pendaftar Hari Ini', $stats['today']],
            ['Laki-laki', $stats['male']],
            ['Perempuan', $stats['female']]
        ];
        
        foreach ($stats_data as $stat) {
            $row++;
            $sheet->setCellValue("A{$row}", $stat[0] . ':');
            $sheet->setCellValue("B{$row}", $stat[1]);
            $sheet->getStyle("A{$row}")->getFont()->setBold(true);
        }
        
        // ==========================================
        // TABLE HEADERS
        // ==========================================
        
        $row += 3;
        $header_row = $row;
        
        $headers = [
            'A' => 'No',
            'B' => 'No. Registrasi',
            'C' => 'Nama Lengkap',
            'D' => 'NIK',
            'E' => 'Jenis Kelamin',
            'F' => 'Tempat Lahir',
            'G' => 'Tanggal Lahir',
            'H' => 'Umur',
            'I' => 'Alamat Domisili',
            'J' => 'Pekerjaan',
            'K' => 'Telepon',
            'L' => 'Kontak Darurat',
            'M' => 'Riwayat Penyakit',
            'N' => 'Permintaan Pemeriksaan',
            'O' => 'Dokter Perujuk',
            'P' => 'Asal Rujukan',
            'Q' => 'No. Rujukan',
            'R' => 'Tgl. Rujukan',
            'S' => 'Diagnosis Awal',
            'T' => 'Tanggal Daftar'
        ];
        
        foreach ($headers as $col => $header) {
            $sheet->setCellValue($col . $header_row, $header);
        }
        
        // Style headers with blue-600 theme
        $headerRange = 'A' . $header_row . ':T' . $header_row;
        $sheet->getStyle($headerRange)->applyFromArray([
            'font' => [
                'bold' => true,
                'color' => ['rgb' => 'FFFFFF'],
                'size' => 11
            ],
            'fill' => [
                'fillType' => Fill::FILL_SOLID,
                'startColor' => ['rgb' => '2563EB']
            ],
            'alignment' => [
                'horizontal' => Alignment::HORIZONTAL_CENTER,
                'vertical' => Alignment::VERTICAL_CENTER
            ],
            'borders' => [
                'allBorders' => [
                    'borderStyle' => Border::BORDER_THIN,
                    'color' => ['rgb' => '000000']
                ]
            ]
        ]);
        
        // ==========================================
        // PATIENT DATA
        // ==========================================
        
        $row++;
        $data_start_row = $row;
        $no = 1;
        
        foreach ($patients as $patient) {
            $sheet->setCellValue("A{$row}", $no);
            $sheet->setCellValue("B{$row}", $patient['nomor_registrasi'] ?: '-');
            $sheet->setCellValue("C{$row}", $patient['nama']);
            $sheet->setCellValue("D{$row}", $patient['nik'] ?: '-');
            $sheet->setCellValue("E{$row}", $patient['jenis_kelamin'] === 'L' ? 'Laki-laki' : 'Perempuan');
            $sheet->setCellValue("F{$row}", $patient['tempat_lahir'] ?: '-');
            $sheet->setCellValue("G{$row}", $patient['tanggal_lahir'] ? date('d/m/Y', strtotime($patient['tanggal_lahir'])) : '-');
            $sheet->setCellValue("H{$row}", $patient['umur'] ? $patient['umur'] . ' tahun' : '-');
            $sheet->setCellValue("I{$row}", $patient['alamat_domisili'] ?: '-');
            $sheet->setCellValue("J{$row}", $patient['pekerjaan'] ?: '-');
            $sheet->setCellValue("K{$row}", $patient['telepon'] ?: '-');
            $sheet->setCellValue("L{$row}", $patient['kontak_darurat'] ?: '-');
            $sheet->setCellValue("M{$row}", $patient['riwayat_pasien'] ?: '-');
            $sheet->setCellValue("N{$row}", $patient['permintaan_pemeriksaan'] ?: '-');
            $sheet->setCellValue("O{$row}", $patient['dokter_perujuk'] ?: '-');
            $sheet->setCellValue("P{$row}", $patient['asal_rujukan'] ?: '-');
            $sheet->setCellValue("Q{$row}", $patient['nomor_rujukan'] ?: '-');
            $sheet->setCellValue("R{$row}", $patient['tanggal_rujukan'] ? date('d/m/Y', strtotime($patient['tanggal_rujukan'])) : '-');
            $sheet->setCellValue("S{$row}", $patient['diagnosis_awal'] ?: '-');
            $sheet->setCellValue("T{$row}", date('d/m/Y H:i', strtotime($patient['created_at'])));
            
            $no++;
            $row++;
        }
        
        $data_end_row = $row - 1;
        
        // ==========================================
        // STYLING DATA ROWS
        // ==========================================
        
        if ($data_end_row >= $data_start_row) {
            $dataRange = 'A' . $data_start_row . ':T' . $data_end_row;
            
            // Apply borders to all data
            $sheet->getStyle($dataRange)->applyFromArray([
                'borders' => [
                    'allBorders' => [
                        'borderStyle' => Border::BORDER_THIN,
                        'color' => ['rgb' => 'CCCCCC']
                    ]
                ],
                'alignment' => [
                    'vertical' => Alignment::VERTICAL_TOP,
                    'wrapText' => true
                ]
            ]);
            
            // Alternating row colors
            for ($i = $data_start_row; $i <= $data_end_row; $i++) {
                if (($i - $data_start_row) % 2 == 1) {
                    $sheet->getStyle('A' . $i . ':T' . $i)->getFill()
                          ->setFillType(Fill::FILL_SOLID)
                          ->getStartColor()->setRGB('F8FAFC');
                }
            }
            
            // Center align specific columns
            $sheet->getStyle('A' . $data_start_row . ':A' . $data_end_row)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
            $sheet->getStyle('E' . $data_start_row . ':E' . $data_end_row)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
            $sheet->getStyle('G' . $data_start_row . ':G' . $data_end_row)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
            $sheet->getStyle('H' . $data_start_row . ':H' . $data_end_row)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
            $sheet->getStyle('R' . $data_start_row . ':R' . $data_end_row)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
            $sheet->getStyle('T' . $data_start_row . ':T' . $data_end_row)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
        }
        
        // ==========================================
        // SUMMARY SECTION
        // ==========================================
        
        $row += 2;
        $sheet->setCellValue("A{$row}", 'RINGKASAN');
        $sheet->getStyle("A{$row}")->getFont()->setSize(12)->setBold(true)->getColor()->setRGB('2563EB');
        
        $row++;
        $sheet->setCellValue("A{$row}", "Total data yang diekspor: " . count($patients) . " pasien");
        $sheet->getStyle("A{$row}")->getFont()->setBold(true);
        
        // ==========================================
        // AUTO SIZE COLUMNS
        // ==========================================
        
        foreach (range('A', 'T') as $col) {
            $sheet->getColumnDimension($col)->setAutoSize(true);
        }
        
        // Set minimum widths for specific columns
        $sheet->getColumnDimension('C')->setWidth(25); // Nama
        $sheet->getColumnDimension('I')->setWidth(30); // Alamat
        $sheet->getColumnDimension('M')->setWidth(25); // Riwayat
        $sheet->getColumnDimension('N')->setWidth(25); // Permintaan
        $sheet->getColumnDimension('S')->setWidth(25); // Diagnosis
        
        // Set row heights
        $sheet->getDefaultRowDimension()->setRowHeight(20);
        
        // ==========================================
        // DOWNLOAD FILE
        // ==========================================
        
        $filename = 'Data_Pasien_' . date('Y-m-d_H-i-s') . '.xlsx';
        
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment;filename="' . $filename . '"');
        header('Cache-Control: max-age=0');
        header('Cache-Control: max-age=1');
        
        $writer = new Xlsx($spreadsheet);
        $writer->save('php://output');
        
        // Log activity
        $this->Admin_model->log_activity(
            $this->session->userdata('user_id'),
            'Data pasien diekspor ke Excel: ' . $filename,
            'pasien',
            null
        );
        
        exit();
    }
}