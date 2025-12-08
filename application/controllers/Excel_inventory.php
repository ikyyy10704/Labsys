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

class Excel_inventory extends CI_Controller {

    public function __construct()
    {
        parent::__construct();
        
        // Check if user is logged in
        if (!$this->session->userdata('logged_in')) {
            $this->session->set_flashdata('error', 'Akses ditolak. Silakan login terlebih dahulu.');
            redirect('auth/login');
        }
        
        $this->load->model(['Inventory_model', 'Admin_model']);
        $this->load->library('session');
        $this->load->helper(['url', 'download']);
    }

    // ==========================================
    // INVENTORY EXPORT METHODS
    // ==========================================

    public function export_inventory()
    {
        try {
            $filters = array(
                'type' => $this->input->get('type'),
                'export_type' => $this->input->get('export_type'),
                'search' => $this->input->get('search')
            );

            $export_type = $filters['export_type'];
            
            switch ($export_type) {
                case 'all':
                    $this->_export_all_inventory($filters);
                    break;
                case 'alat':
                    $filters['type'] = 'alat';
                    $this->_export_equipment($filters);
                    break;
                case 'reagen':
                    $filters['type'] = 'reagen';
                    $this->_export_reagents($filters);
                    break;
                case 'alerts':
                    $this->_export_alert_items($filters);
                    break;
                default:
                    $this->_export_all_inventory($filters);
                    break;
            }

        } catch (Exception $e) {
            log_message('error', 'Error exporting inventory: ' . $e->getMessage());
            $this->session->set_flashdata('error', 'Gagal mengekspor data inventory: ' . $e->getMessage());
            redirect('inventory/kelola');
        }
    }

    public function ajax_export_inventory()
    {
        $this->output->set_content_type('application/json');
        
        try {
            $filters = array(
                'type' => $this->input->post('type'),
                'export_type' => $this->input->post('export_type'),
                'search' => $this->input->post('search')
            );

            $count = $this->Inventory_model->count_inventory_for_export($filters);
            
            if ($count == 0) {
                $this->output->set_output(json_encode([
                    'success' => false,
                    'message' => 'Tidak ada data inventory untuk diekspor'
                ]));
                return;
            }

            $params = http_build_query($filters);
            $download_url = base_url('excel_inventory/export_inventory?' . $params);
            
            $this->output->set_output(json_encode([
                'success' => true,
                'message' => "Siap mengekspor {$count} data inventory",
                'download_url' => $download_url,
                'record_count' => $count
            ]));
            
        } catch (Exception $e) {
            log_message('error', 'Error in ajax_export_inventory: ' . $e->getMessage());
            $this->output->set_output(json_encode([
                'success' => false,
                'message' => 'Terjadi kesalahan saat mempersiapkan export'
            ]));
        }
    }

    // ==========================================
    // PRIVATE EXPORT METHODS
    // ==========================================

    private function _export_all_inventory($filters)
    {
        $inventory_data = $this->Inventory_model->get_inventory_for_export($filters);
        $stats = $this->Inventory_model->get_inventory_statistics();
        
        if (empty($inventory_data)) {
            $this->session->set_flashdata('error', 'Tidak ada data inventory untuk diekspor');
            redirect('inventory/kelola');
            return;
        }

        $spreadsheet = $this->_create_all_inventory_excel($inventory_data, $stats, $filters);
        $filename = $this->_generate_filename('Inventory_Lengkap', $filters);
        $this->_output_excel($spreadsheet, $filename);
        
        $this->Admin_model->log_activity(
            $this->session->userdata('user_id'),
            'Export data inventory lengkap ke Excel: ' . $filename,
            'inventory',
            null
        );
    }

    private function _export_equipment($filters)
    {
        $equipment_data = $this->Inventory_model->get_inventory_for_export($filters);
        
        if (empty($equipment_data)) {
            $this->session->set_flashdata('error', 'Tidak ada data alat laboratorium untuk diekspor');
            redirect('inventory/kelola');
            return;
        }

        $spreadsheet = $this->_create_equipment_excel($equipment_data, $filters);
        $filename = $this->_generate_filename('Alat_Laboratorium', $filters);
        $this->_output_excel($spreadsheet, $filename);
        
        $this->Admin_model->log_activity(
            $this->session->userdata('user_id'),
            'Export data alat laboratorium ke Excel: ' . $filename,
            'alat_laboratorium',
            null
        );
    }

    private function _export_reagents($filters)
    {
        $reagent_data = $this->Inventory_model->get_inventory_for_export($filters);
        
        if (empty($reagent_data)) {
            $this->session->set_flashdata('error', 'Tidak ada data reagen untuk diekspor');
            redirect('inventory/kelola');
            return;
        }

        $spreadsheet = $this->_create_reagents_excel($reagent_data, $filters);
        $filename = $this->_generate_filename('Reagen', $filters);
        $this->_output_excel($spreadsheet, $filename);
        
        $this->Admin_model->log_activity(
            $this->session->userdata('user_id'),
            'Export data reagen ke Excel: ' . $filename,
            'reagen',
            null
        );
    }

    private function _export_alert_items($filters)
    {
        $alert_data = $this->Inventory_model->get_inventory_alerts_for_export();
        
        if (empty($alert_data)) {
            $this->session->set_flashdata('error', 'Tidak ada item yang memerlukan perhatian');
            redirect('inventory/kelola');
            return;
        }

        $spreadsheet = $this->_create_alert_items_excel($alert_data, $filters);
        $filename = $this->_generate_filename('Item_Alert', $filters);
        $this->_output_excel($spreadsheet, $filename);
        
        $this->Admin_model->log_activity(
            $this->session->userdata('user_id'),
            'Export data item alert ke Excel: ' . $filename,
            'inventory',
            null
        );
    }

    // ==========================================
    // EXCEL CREATION METHODS
    // ==========================================

    private function _create_all_inventory_excel($inventory_data, $stats, $filters)
    {
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        
        $sheet->setTitle('Inventory Lengkap');
        
        $spreadsheet->getProperties()
            ->setCreator('LabSy - Sistem Informasi Laboratorium')
            ->setTitle('Laporan Inventory Lengkap')
            ->setSubject('Export Data Inventory')
            ->setDescription('Laporan data inventory laboratorium lengkap')
            ->setKeywords('laboratorium inventory alat reagen export excel')
            ->setCategory('Inventory Reports');

        $this->_create_report_header($sheet, $filters, 'LAPORAN INVENTORY LABORATORIUM LENGKAP', 'P');
        $this->_create_inventory_statistics_section($sheet, $stats, $filters);
        $this->_create_all_inventory_table_headers($sheet);
        $this->_fill_all_inventory_data($sheet, $inventory_data);
        $this->_apply_all_inventory_formatting($sheet, count($inventory_data));
        $this->_auto_size_columns($sheet, 'A', 'P');
        
        return $spreadsheet;
    }

    private function _create_equipment_excel($equipment_data, $filters)
    {
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        
        $sheet->setTitle('Alat Laboratorium');
        
        $this->_create_report_header($sheet, $filters, 'LAPORAN ALAT LABORATORIUM', 'N');
        $this->_create_equipment_table_headers($sheet);
        $this->_fill_equipment_data($sheet, $equipment_data);
        $this->_apply_equipment_formatting($sheet, count($equipment_data));
        $this->_auto_size_columns($sheet, 'A', 'N');
        
        return $spreadsheet;
    }

    private function _create_reagents_excel($reagent_data, $filters)
    {
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        
        $sheet->setTitle('Reagen');
        
        $this->_create_report_header($sheet, $filters, 'LAPORAN REAGEN LABORATORIUM', 'M');
        $this->_create_reagents_table_headers($sheet);
        $this->_fill_reagents_data($sheet, $reagent_data);
        $this->_apply_reagents_formatting($sheet, count($reagent_data));
        $this->_auto_size_columns($sheet, 'A', 'M');
        
        return $spreadsheet;
    }

    private function _create_alert_items_excel($alert_data, $filters)
    {
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        
        $sheet->setTitle('Item Alert');
        
        $this->_create_report_header($sheet, $filters, 'LAPORAN ITEM YANG MEMERLUKAN PERHATIAN', 'J');
        $this->_create_alert_table_headers($sheet);
        $this->_fill_alert_data($sheet, $alert_data);
        $this->_apply_alert_formatting($sheet, count($alert_data));
        $this->_auto_size_columns($sheet, 'A', 'J');
        
        return $spreadsheet;
    }

    // ==========================================
    // HEADER AND TABLE CREATION METHODS
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

    private function _create_inventory_statistics_section($sheet, $stats, $filters)
    {
        $start_row = empty($this->_build_filter_info($filters)) ? 5 : 6;
        
        $sheet->setCellValue('A' . $start_row, 'RINGKASAN STATISTIK');
        $sheet->getStyle('A' . $start_row)->getFont()->setBold(true)->setSize(12);
        $sheet->mergeCells('A' . $start_row . ':P' . $start_row);
        $start_row++;
        
        $stats_data = [
            ['Total Alat Laboratorium', $stats['total_alat']],
            ['Total Reagen', $stats['total_reagen']],
            ['Item Memerlukan Perhatian', $stats['total_alerts']],
            ['Item Kritis', $stats['total_critical']]
        ];
        
        $col1 = 'A'; $col2 = 'B'; $col3 = 'E'; $col4 = 'F';
        
        for ($i = 0; $i < count($stats_data); $i++) {
            if ($i < 2) {
                $sheet->setCellValue($col1 . ($start_row + $i), $stats_data[$i][0]);
                $sheet->setCellValue($col2 . ($start_row + $i), $stats_data[$i][1]);
            } else {
                $sheet->setCellValue($col3 . ($start_row + ($i - 2)), $stats_data[$i][0]);
                $sheet->setCellValue($col4 . ($start_row + ($i - 2)), $stats_data[$i][1]);
            }
        }
        
        $stats_range = 'A' . $start_row . ':F' . ($start_row + 1);
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

    private function _create_all_inventory_table_headers($sheet)
    {
        $start_row = empty($this->_build_filter_info($this->input->get())) ? 9 : 10;
        
        $headers = [
            'A' => 'No', 'B' => 'Kode', 'C' => 'Nama Item', 'D' => 'Tipe',
            'E' => 'Status', 'F' => 'Stok/Kondisi', 'G' => 'Lokasi',
            'H' => 'Merek/Model', 'I' => 'Satuan', 'J' => 'Stok Minimal',
            'K' => 'Tanggal Exp/Kalibrasi', 'L' => 'Kalibrasi Terakhir',
            'M' => 'Tanggal Mulai Dipakai', 'N' => 'Catatan/Riwayat',
            'O' => 'Dibuat', 'P' => 'Diperbarui'
        ];
        
        foreach ($headers as $column => $header) {
            $sheet->setCellValue($column . $start_row, $header);
        }
        
        $this->_apply_blue_header_style($sheet, 'A' . $start_row . ':P' . $start_row);
        $sheet->getRowDimension($start_row)->setRowHeight(25);
    }

    private function _create_equipment_table_headers($sheet)
    {
        $start_row = 6;
        
        $headers = [
            'A' => 'No', 'B' => 'Kode Unik', 'C' => 'Nama Alat', 'D' => 'Merek/Model',
            'E' => 'Lokasi', 'F' => 'Status', 'G' => 'Jadwal Kalibrasi',
            'H' => 'Kalibrasi Terakhir', 'I' => 'Riwayat Perbaikan',
            'J' => 'Kondisi', 'K' => 'Tanggal Dibuat', 'L' => 'Terakhir Diperbarui',
            'M' => 'Catatan Tambahan', 'N' => 'Alert Level'
        ];
        
        foreach ($headers as $column => $header) {
            $sheet->setCellValue($column . $start_row, $header);
        }
        
        $this->_apply_blue_header_style($sheet, 'A' . $start_row . ':N' . $start_row);
        $sheet->getRowDimension($start_row)->setRowHeight(25);
    }

    private function _create_reagents_table_headers($sheet)
    {
        $start_row = 6;
        
        $headers = [
            'A' => 'No', 'B' => 'Kode Unik', 'C' => 'Nama Reagen', 'D' => 'Jumlah Stok',
            'E' => 'Satuan', 'F' => 'Stok Minimal', 'G' => 'Status', 'H' => 'Lokasi Penyimpanan',
            'I' => 'Tanggal Mulai Dipakai', 'J' => 'Tanggal Expired', 'K' => 'Hari ke Expired',
            'L' => 'Catatan', 'M' => 'Tanggal Dibuat'
        ];
        
        foreach ($headers as $column => $header) {
            $sheet->setCellValue($column . $start_row, $header);
        }
        
        $this->_apply_blue_header_style($sheet, 'A' . $start_row . ':M' . $start_row);
        $sheet->getRowDimension($start_row)->setRowHeight(25);
    }

    private function _create_alert_table_headers($sheet)
    {
        $start_row = 6;
        
        $headers = [
            'A' => 'No', 'B' => 'Kode', 'C' => 'Nama Item', 'D' => 'Tipe',
            'E' => 'Status', 'F' => 'Alert Level', 'G' => 'Stok/Kondisi',
            'H' => 'Tanggal Expired', 'I' => 'Hari ke Expired', 'J' => 'Keterangan'
        ];
        
        foreach ($headers as $column => $header) {
            $sheet->setCellValue($column . $start_row, $header);
        }
        
        $this->_apply_blue_header_style($sheet, 'A' . $start_row . ':J' . $start_row);
        $sheet->getRowDimension($start_row)->setRowHeight(25);
    }

    // ==========================================
    // DATA FILLING METHODS
    // ==========================================

    private function _fill_all_inventory_data($sheet, $inventory_data)
    {
        $start_row = empty($this->_build_filter_info($this->input->get())) ? 10 : 11;
        $row = $start_row;
        $no = 1;
        
        foreach ($inventory_data as $item) {
            $sheet->setCellValue('A' . $row, $no);
            $sheet->setCellValue('B' . $row, $item['kode_unik']);
            $sheet->setCellValue('C' . $row, $item['nama_item']);
            $sheet->setCellValue('D' . $row, strtoupper($item['tipe_inventory']));
            $sheet->setCellValue('E' . $row, $item['status']);
            
            // Stok/Kondisi
            if ($item['tipe_inventory'] === 'reagen') {
                $sheet->setCellValue('F' . $row, $item['stok_info'] ?? '-');
            } else {
                $sheet->setCellValue('F' . $row, $item['status']);
            }
            
            $sheet->setCellValue('G' . $row, $item['lokasi'] ?? '-');
            $sheet->setCellValue('H' . $row, $item['merek_model'] ?? '-');
            
            // Specific fields based on type
            if ($item['tipe_inventory'] === 'reagen') {
                $sheet->setCellValue('I' . $row, $item['satuan'] ?? '-');
                $sheet->setCellValue('J' . $row, $item['stok_minimal'] ?? '-');
                $sheet->setCellValue('M' . $row, $item['tanggal_dipakai'] ? date('d/m/Y', strtotime($item['tanggal_dipakai'])) : '-');
            } else {
                $sheet->setCellValue('I' . $row, '-');
                $sheet->setCellValue('J' . $row, '-');
                $sheet->setCellValue('L' . $row, $item['tanggal_kalibrasi_terakhir'] ? date('d/m/Y', strtotime($item['tanggal_kalibrasi_terakhir'])) : '-');
                $sheet->setCellValue('M' . $row, '-');
            }
            
            $sheet->setCellValue('K' . $row, $item['exp_date'] ? date('d/m/Y', strtotime($item['exp_date'])) : '-');
            $sheet->setCellValue('N' . $row, $item['catatan'] ?? '-');
            $sheet->setCellValue('O' . $row, date('d/m/Y H:i', strtotime($item['created_at'])));
            $sheet->setCellValue('P' . $row, $item['updated_at'] ? date('d/m/Y H:i', strtotime($item['updated_at'])) : '-');
            
            $row++;
            $no++;
        }
    }

    private function _fill_equipment_data($sheet, $equipment_data)
    {
        $start_row = 7;
        $row = $start_row;
        $no = 1;
        
        foreach ($equipment_data as $item) {
            if ($item['tipe_inventory'] !== 'alat') continue;
            
            $sheet->setCellValue('A' . $row, $no);
            $sheet->setCellValue('B' . $row, $item['kode_unik']);
            $sheet->setCellValue('C' . $row, $item['nama_item']);
            $sheet->setCellValue('D' . $row, $item['merek_model'] ?? '-');
            $sheet->setCellValue('E' . $row, $item['lokasi'] ?? '-');
            $sheet->setCellValue('F' . $row, $item['status']);
            $sheet->setCellValue('G' . $row, $item['exp_date'] ? date('d/m/Y', strtotime($item['exp_date'])) : '-');
            $sheet->setCellValue('H' . $row, $item['tanggal_kalibrasi_terakhir'] ? date('d/m/Y', strtotime($item['tanggal_kalibrasi_terakhir'])) : '-');
            $sheet->setCellValue('I' . $row, $item['catatan'] ?? '-');
            $sheet->setCellValue('J' . $row, $this->_get_condition_status($item['status']));
            $sheet->setCellValue('K' . $row, date('d/m/Y H:i', strtotime($item['created_at'])));
            $sheet->setCellValue('L' . $row, $item['updated_at'] ? date('d/m/Y H:i', strtotime($item['updated_at'])) : '-');
            $sheet->setCellValue('M' . $row, '-');
            $sheet->setCellValue('N' . $row, $this->_get_alert_level_by_status($item['status']));
            
            $row++;
            $no++;
        }
    }

    private function _fill_reagents_data($sheet, $reagent_data)
    {
        $start_row = 7;
        $row = $start_row;
        $no = 1;
        
        foreach ($reagent_data as $item) {
            if ($item['tipe_inventory'] !== 'reagen') continue;
            
            $sheet->setCellValue('A' . $row, $no);
            $sheet->setCellValue('B' . $row, $item['kode_unik']);
            $sheet->setCellValue('C' . $row, $item['nama_item']);
            $sheet->setCellValue('D' . $row, $item['stok_info'] ?? '-');
            $sheet->setCellValue('E' . $row, $item['satuan'] ?? '-');
            $sheet->setCellValue('F' . $row, $item['stok_minimal'] ?? '-');
            $sheet->setCellValue('G' . $row, $item['status']);
            $sheet->setCellValue('H' . $row, $item['lokasi'] ?? '-');
            $sheet->setCellValue('I' . $row, $item['tanggal_dipakai'] ? date('d/m/Y', strtotime($item['tanggal_dipakai'])) : '-');
            $sheet->setCellValue('J' . $row, $item['exp_date'] ? date('d/m/Y', strtotime($item['exp_date'])) : '-');
            
            // Calculate days to expire
            if ($item['exp_date']) {
                $days_to_expire = $this->_calculate_days_to_expire($item['exp_date']);
                $sheet->setCellValue('K' . $row, $days_to_expire);
            } else {
                $sheet->setCellValue('K' . $row, '-');
            }
            
            $sheet->setCellValue('L' . $row, $item['catatan'] ?? '-');
            $sheet->setCellValue('M' . $row, date('d/m/Y H:i', strtotime($item['created_at'])));
            
            $row++;
            $no++;
        }
    }

    private function _fill_alert_data($sheet, $alert_data)
    {
        $start_row = 7;
        $row = $start_row;
        $no = 1;
        
        foreach ($alert_data as $item) {
            $sheet->setCellValue('A' . $row, $no);
            $sheet->setCellValue('B' . $row, $item['kode_unik']);
            $sheet->setCellValue('C' . $row, $item['nama_item']);
            $sheet->setCellValue('D' . $row, strtoupper($item['tipe_inventory']));
            $sheet->setCellValue('E' . $row, $item['status']);
            $sheet->setCellValue('F' . $row, $item['alert_level']);
            $sheet->setCellValue('G' . $row, $item['stok_info'] ?? '-');
            $sheet->setCellValue('H' . $row, $item['expired_date'] ? date('d/m/Y', strtotime($item['expired_date'])) : '-');
            $sheet->setCellValue('I' . $row, $item['days_to_expire'] ?? '-');
            $sheet->setCellValue('J' . $row, $this->_get_alert_description($item['alert_level'], $item['tipe_inventory']));
            
            $row++;
            $no++;
        }
    }

    // ==========================================
    // FORMATTING METHODS
    // ==========================================

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

    private function _apply_all_inventory_formatting($sheet, $data_count)
    {
        $start_row = empty($this->_build_filter_info($this->input->get())) ? 10 : 11;
        $end_row = $start_row + $data_count - 1;
        $data_range = 'A' . $start_row . ':P' . $end_row;
        
        $this->_apply_standard_formatting($sheet, $data_range, $start_row, $end_row, 'P');
    }

    private function _apply_equipment_formatting($sheet, $data_count)
    {
        $start_row = 7;
        $end_row = $start_row + $data_count - 1;
        $data_range = 'A' . $start_row . ':N' . $end_row;
        
        $this->_apply_standard_formatting($sheet, $data_range, $start_row, $end_row, 'N');
    }

    private function _apply_reagents_formatting($sheet, $data_count)
    {
        $start_row = 7;
        $end_row = $start_row + $data_count - 1;
        $data_range = 'A' . $start_row . ':M' . $end_row;
        
        $this->_apply_standard_formatting($sheet, $data_range, $start_row, $end_row, 'M');
    }

    private function _apply_alert_formatting($sheet, $data_count)
    {
        $start_row = 7;
        $end_row = $start_row + $data_count - 1;
        $data_range = 'A' . $start_row . ':J' . $end_row;
        
        $this->_apply_standard_formatting($sheet, $data_range, $start_row, $end_row, 'J');
        
        // Apply special formatting for alert levels
        for ($i = $start_row; $i <= $end_row; $i++) {
            $alert_level = $sheet->getCell('F' . $i)->getValue();
            $color = $this->_get_alert_color($alert_level);
            
            $sheet->getStyle('F' . $i)->applyFromArray([
                'fill' => [
                    'fillType' => Fill::FILL_SOLID,
                    'color' => ['rgb' => $color]
                ],
                'font' => [
                    'color' => ['rgb' => 'FFFFFF'],
                    'bold' => true
                ]
            ]);
        }
    }

    private function _apply_standard_formatting($sheet, $data_range, $start_row, $end_row, $end_col)
    {
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
        
        // Alternating row colors
        for ($i = $start_row; $i <= $end_row; $i++) {
            if (($i - $start_row) % 2 == 1) {
                $sheet->getStyle('A' . $i . ':' . $end_col . $i)->applyFromArray([
                    'fill' => [
                        'fillType' => Fill::FILL_SOLID,
                        'color' => ['rgb' => 'F8FAFC']
                    ]
                ]);
            }
        }
        
        // Center align first column (No)
        $sheet->getStyle('A' . $start_row . ':A' . $end_row)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
    }

    // ==========================================
    // UTILITY METHODS
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
        
        if (!empty($filters['type'])) {
            $info_parts[] = 'Tipe: ' . strtoupper($filters['type']);
        }
        
        if (!empty($filters['search'])) {
            $info_parts[] = 'Pencarian: ' . $filters['search'];
        }
        
        return implode(' | ', $info_parts);
    }

    private function _calculate_days_to_expire($expire_date)
    {
        $expire_timestamp = strtotime($expire_date);
        $today_timestamp = strtotime(date('Y-m-d'));
        $diff = $expire_timestamp - $today_timestamp;
        $days = floor($diff / (60 * 60 * 24));
        
        if ($days < 0) {
            return 'Expired ' . abs($days) . ' hari';
        } else {
            return $days . ' hari';
        }
    }

    private function _get_condition_status($status)
    {
        switch ($status) {
            case 'Normal': return 'Baik';
            case 'Perlu Kalibrasi': return 'Perlu Perawatan';
            case 'Rusak': return 'Rusak';
            case 'Sedang Kalibrasi': return 'Dalam Perawatan';
            default: return 'Tidak Diketahui';
        }
    }

    private function _get_alert_level_by_status($status)
    {
        switch ($status) {
            case 'Normal': return 'OK';
            case 'Perlu Kalibrasi': return 'Warning';
            case 'Rusak': return 'Urgent';
            case 'Sedang Kalibrasi': return 'Info';
            default: return 'Unknown';
        }
    }

    private function _get_alert_description($alert_level, $type)
    {
        $descriptions = [
            'OK' => 'Kondisi normal',
            'Low Stock' => 'Stok di bawah minimum',
            'Warning' => $type === 'alat' ? 'Perlu kalibrasi' : 'Mendekati expired',
            'Urgent' => $type === 'alat' ? 'Rusak atau kritis' : 'Expired atau habis',
            'Calibration Due' => 'Jadwal kalibrasi telah tiba'
        ];
        
        return $descriptions[$alert_level] ?? 'Tidak diketahui';
    }

    private function _get_alert_color($alert_level)
    {
        $colors = [
            'OK' => '22C55E',
            'Low Stock' => 'F59E0B',
            'Warning' => 'F59E0B',
            'Urgent' => 'EF4444',
            'Calibration Due' => '3B82F6'
        ];
        
        return $colors[$alert_level] ?? '6B7280';
    }

    private function _generate_filename($prefix, $filters)
    {
        $date_suffix = '_' . date('d-m-Y_His');
        
        if (!empty($filters['search'])) {
            $search_suffix = '_search_' . substr(preg_replace('/[^a-zA-Z0-9]/', '', $filters['search']), 0, 10);
            return $prefix . $search_suffix . $date_suffix . '.xlsx';
        }
        
        return $prefix . $date_suffix . '.xlsx';
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
}