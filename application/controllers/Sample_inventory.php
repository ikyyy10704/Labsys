<?php
defined('BASEPATH') OR exit('No direct script access allowed');

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;

class Sample_inventory extends CI_Controller {

    public function __construct() {
        parent::__construct();
        
        // Check if user is logged in
        if (!$this->session->userdata('logged_in')) {
            redirect('auth/login');
        }
        
        $this->load->model(['Sample_inventory_model', 'User_model', 'Admin_model']);
        $this->load->helper(['url', 'form']);
    }

    /**
     * Main inventory page
     */
    public function index() {
        try {
            $data['title'] = 'Inventory Sampel';
            
            // Get all samples in storage
            $data['samples'] = $this->Sample_inventory_model->get_all_samples_in_storage();
            
            // Get summary statistics
            $data['summary'] = $this->Sample_inventory_model->get_inventory_summary();
            
            // Get storage locations
            $data['storage_locations'] = $this->Sample_inventory_model->get_storage_locations();
            
            // Get expiring samples (next 2 days)
            $data['expiring_samples'] = $this->Sample_inventory_model->get_expiring_samples(2);
            
            // Load views
            $this->load->view('template/header', $data);
            $this->load->view('template/sidebar', $data);
            $this->load->view('laboratorium/sample_inventory', $data);
            $this->load->view('template/footer');
            
        } catch (Exception $e) {
            log_message('error', 'Error loading sample inventory: ' . $e->getMessage());
            $this->session->set_flashdata('error', 'Terjadi kesalahan saat memuat inventory sampel');
            redirect('dashboard');
        }
    }

    /**
     * Add sample to storage
     */
    public function add_to_storage() {
        if ($this->input->method() !== 'post') {
            show_404();
            return;
        }
        
        $this->form_validation->set_rules('sampel_id', 'Sampel ID', 'required|numeric');
        $this->form_validation->set_rules('lokasi_penyimpanan', 'Lokasi Penyimpanan', 'required|max_length[100]');
        $this->form_validation->set_rules('suhu_penyimpanan', 'Suhu Penyimpanan', 'numeric');
        $this->form_validation->set_rules('volume_sampel', 'Volume Sampel', 'numeric');
        
        if ($this->form_validation->run() === FALSE) {
            echo json_encode(['success' => false, 'message' => strip_tags(validation_errors())]);
            return;
        }
        
        try {
            $data = [
                'sampel_id' => $this->input->post('sampel_id'),
                'lokasi_penyimpanan' => $this->input->post('lokasi_penyimpanan'),
                'suhu_penyimpanan' => $this->input->post('suhu_penyimpanan'),
                'volume_sampel' => $this->input->post('volume_sampel'),
                'satuan_volume' => $this->input->post('satuan_volume', true) ?: 'ml',
                'keterangan' => $this->input->post('keterangan'),
                'petugas_id' => $this->session->userdata('user_id')
            ];
            
            $storage_id = $this->Sample_inventory_model->add_to_storage($data);
            
            if ($storage_id) {
                $this->User_model->log_activity(
                    $this->session->userdata('user_id'),
                    'Sample added to storage',
                    'sampel_storage',
                    $storage_id
                );
                
                echo json_encode([
                    'success' => true,
                    'message' => 'Sampel berhasil ditambahkan ke inventory',
                    'storage_id' => $storage_id
                ]);
            } else {
                echo json_encode(['success' => false, 'message' => 'Gagal menambahkan sampel ke inventory']);
            }
            
        } catch (Exception $e) {
            log_message('error', 'Error adding sample to storage: ' . $e->getMessage());
            echo json_encode(['success' => false, 'message' => 'Terjadi kesalahan saat menyimpan data']);
        }
    }

    /**
     * Update storage status
     */
    public function update_storage_status($storage_id) {
        if ($this->input->method() !== 'post') {
            show_404();
            return;
        }
        
        $this->form_validation->set_rules('status_penyimpanan', 'Status', 'required|in_list[tersimpan,diproses,dibuang,dikembalikan]');
        
        if ($this->form_validation->run() === FALSE) {
            echo json_encode(['success' => false, 'message' => strip_tags(validation_errors())]);
            return;
        }
        
        try {
            $status = $this->input->post('status_penyimpanan');
            $keterangan = $this->input->post('keterangan');
            
            $result = $this->Sample_inventory_model->update_storage_status($storage_id, $status, $keterangan);
            
            if ($result) {
                $this->User_model->log_activity(
                    $this->session->userdata('user_id'),
                    "Storage status updated to: {$status}",
                    'sampel_storage',
                    $storage_id
                );
                
                echo json_encode(['success' => true, 'message' => 'Status berhasil diperbarui']);
            } else {
                echo json_encode(['success' => false, 'message' => 'Gagal memperbarui status']);
            }
            
        } catch (Exception $e) {
            log_message('error', 'Error updating storage status: ' . $e->getMessage());
            echo json_encode(['success' => false, 'message' => 'Terjadi kesalahan']);
        }
    }

    /**
     * Get sample detail
     */
    public function get_sample_detail($storage_id) {
        try {
            $sample = $this->Sample_inventory_model->get_storage_detail($storage_id);
            
            if ($sample) {
                echo json_encode(['success' => true, 'sample' => $sample]);
            } else {
                echo json_encode(['success' => false, 'message' => 'Sampel tidak ditemukan']);
            }
            
        } catch (Exception $e) {
            log_message('error', 'Error getting sample detail: ' . $e->getMessage());
            echo json_encode(['success' => false, 'message' => 'Terjadi kesalahan']);
        }
    }

    /**
     * Update temperature log
     */
    public function log_temperature() {
        if ($this->input->method() !== 'post') {
            show_404();
            return;
        }
        
        $this->form_validation->set_rules('lokasi_storage', 'Lokasi', 'required');
        $this->form_validation->set_rules('suhu_tercatat', 'Suhu', 'required|numeric');
        
        if ($this->form_validation->run() === FALSE) {
            echo json_encode(['success' => false, 'message' => strip_tags(validation_errors())]);
            return;
        }
        
        try {
            $data = [
                'lokasi_storage' => $this->input->post('lokasi_storage'),
                'suhu_tercatat' => $this->input->post('suhu_tercatat'),
                'kelembaban' => $this->input->post('kelembaban'),
                'keterangan' => $this->input->post('keterangan'),
                'petugas_id' => $this->session->userdata('user_id')
            ];
            
            $log_id = $this->Sample_inventory_model->log_temperature($data);
            
            if ($log_id) {
                echo json_encode(['success' => true, 'message' => 'Log suhu berhasil disimpan']);
            } else {
                echo json_encode(['success' => false, 'message' => 'Gagal menyimpan log suhu']);
            }
            
        } catch (Exception $e) {
            log_message('error', 'Error logging temperature: ' . $e->getMessage());
            echo json_encode(['success' => false, 'message' => 'Terjadi kesalahan']);
        }
    }

    /**
     * Export inventory to Excel - Using PhpSpreadsheet
     */
    public function export_excel() {
        // Load PhpSpreadsheet
        $vendor_paths = [
            FCPATH . 'vendor/autoload.php',
            APPPATH . 'third_party/vendor/autoload.php',
            APPPATH . 'vendor/autoload.php',
            APPPATH . '../vendor/autoload.php'
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
            $this->session->set_flashdata('error', 'PhpSpreadsheet library not found');
            redirect('sample_inventory');
            return;
        }
        
        try {
            $filters = array(
                'start_date' => $this->input->get('start_date'),
                'end_date' => $this->input->get('end_date'),
                'status' => $this->input->get('status'),
                'lokasi' => $this->input->get('lokasi'),
                'search' => $this->input->get('search')
            );

            $samples = $this->Sample_inventory_model->get_all_samples_in_storage();
            $summary = $this->Sample_inventory_model->get_inventory_summary();
            
            if (empty($samples)) {
                $this->session->set_flashdata('error', 'Tidak ada data inventory sampel untuk diekspor');
                redirect('sample_inventory');
                return;
            }

            $spreadsheet = new Spreadsheet();
            $sheet = $spreadsheet->getActiveSheet();
            
            // ==========================================
            // SET UP SHEET PROPERTIES
            // ==========================================
            
            $sheet->setTitle('Inventory Sampel');
            
            // Set default font
            $spreadsheet->getDefaultStyle()->getFont()->setName('Arial')->setSize(10);
            
            // Set document properties
            $spreadsheet->getProperties()
                ->setCreator('LabSy - Sistem Informasi Laboratorium')
                ->setTitle('Laporan Inventory Sampel')
                ->setSubject('Export Data Inventory Sampel')
                ->setDescription('Laporan data inventory sampel laboratorium')
                ->setKeywords('laboratorium sampel inventory export excel')
                ->setCategory('Sample Reports');
            
            // ==========================================
            // HEADER SECTION
            // ==========================================
            
            // Title with blue background
            $sheet->mergeCells('A1:L3');
            $sheet->setCellValue('A1', 'LAPORAN INVENTORY SAMPEL LABORATORIUM');
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
            if (!empty($filters['start_date']) || !empty($filters['end_date']) || !empty($filters['status']) || !empty($filters['lokasi']) || !empty($filters['search'])) {
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
                
                if (!empty($filters['status'])) {
                    $row++;
                    $sheet->setCellValue("A{$row}", '• Status: ' . ucfirst($filters['status']));
                }
                
                if (!empty($filters['lokasi'])) {
                    $row++;
                    $sheet->setCellValue("A{$row}", '• Lokasi: ' . $filters['lokasi']);
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
            $sheet->setCellValue("A{$row}", 'STATISTIK INVENTORY');
            $sheet->getStyle("A{$row}")->getFont()->setSize(12)->setBold(true)->getColor()->setRGB('2563EB');
            
            $row++;
            $stats_data = [
                ['Total Sampel Tersimpan', $summary['total_samples'] ?? 0],
                ['Sampel Aktif', $summary['active_samples'] ?? 0],
                ['Sampel Kadaluarsa', $summary['expired_samples'] ?? 0],
                ['Total Lokasi Penyimpanan', $summary['total_locations'] ?? 0]
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
                'B' => 'Nomor Pemeriksaan',
                'C' => 'Nama Pasien',
                'D' => 'Jenis Sampel',
                'E' => 'Lokasi Penyimpanan',
                'F' => 'Suhu (°C)',
                'G' => 'Volume',
                'H' => 'Satuan',
                'I' => 'Tanggal Masuk',
                'J' => 'Tanggal Kadaluarsa',
                'K' => 'Status',
                'L' => 'Keterangan'
            ];
            
            foreach ($headers as $col => $header) {
                $sheet->setCellValue($col . $header_row, $header);
            }
            
            // Style headers with blue-600 theme
            $headerRange = 'A' . $header_row . ':L' . $header_row;
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
            
            $sheet->getRowDimension($header_row)->setRowHeight(25);
            
            // ==========================================
            // SAMPLE DATA
            // ==========================================
            
            $row++;
            $data_start_row = $row;
            $no = 1;
            
            foreach ($samples as $sample) {
                $sheet->setCellValue("A{$row}", $no);
                $sheet->setCellValue("B{$row}", $sample['nomor_pemeriksaan'] ?? '-');
                $sheet->setCellValue("C{$row}", $sample['nama_pasien'] ?? '-');
                $sheet->setCellValue("D{$row}", $sample['jenis_sampel'] ?? '-');
                $sheet->setCellValue("E{$row}", $sample['lokasi_penyimpanan'] ?? '-');
                $sheet->setCellValue("F{$row}", $sample['suhu_penyimpanan'] ?? '-');
                $sheet->setCellValue("G{$row}", $sample['volume_sampel'] ?? '-');
                $sheet->setCellValue("H{$row}", $sample['satuan_volume'] ?? 'ml');
                $sheet->setCellValue("I{$row}", !empty($sample['tanggal_masuk']) ? date('d/m/Y H:i', strtotime($sample['tanggal_masuk'])) : '-');
                $sheet->setCellValue("J{$row}", !empty($sample['tanggal_kadaluarsa']) ? date('d/m/Y', strtotime($sample['tanggal_kadaluarsa'])) : '-');
                $sheet->setCellValue("K{$row}", $this->_format_status($sample['status_penyimpanan'] ?? 'tersimpan'));
                $sheet->setCellValue("L{$row}", $sample['keterangan'] ?? '-');
                
                $no++;
                $row++;
            }
            
            $data_end_row = $row - 1;
            
            // ==========================================
            // STYLING DATA ROWS
            // ==========================================
            
            if ($data_end_row >= $data_start_row) {
                $dataRange = 'A' . $data_start_row . ':L' . $data_end_row;
                
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
                        $sheet->getStyle('A' . $i . ':L' . $i)->getFill()
                              ->setFillType(Fill::FILL_SOLID)
                              ->getStartColor()->setRGB('F8FAFC');
                    }
                }
                
                // Center align specific columns
                $sheet->getStyle('A' . $data_start_row . ':A' . $data_end_row)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
                $sheet->getStyle('F' . $data_start_row . ':F' . $data_end_row)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
                $sheet->getStyle('G' . $data_start_row . ':G' . $data_end_row)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
                $sheet->getStyle('H' . $data_start_row . ':H' . $data_end_row)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
                $sheet->getStyle('I' . $data_start_row . ':I' . $data_end_row)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
                $sheet->getStyle('J' . $data_start_row . ':J' . $data_end_row)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
                $sheet->getStyle('K' . $data_start_row . ':K' . $data_end_row)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
            }
            
            // ==========================================
            // SUMMARY SECTION
            // ==========================================
            
            $row += 2;
            $sheet->setCellValue("A{$row}", 'RINGKASAN');
            $sheet->getStyle("A{$row}")->getFont()->setSize(12)->setBold(true)->getColor()->setRGB('2563EB');
            
            $row++;
            $sheet->setCellValue("A{$row}", "Total data yang diekspor: " . count($samples) . " sampel");
            $sheet->getStyle("A{$row}")->getFont()->setBold(true);
            
            // ==========================================
            // AUTO SIZE COLUMNS
            // ==========================================
            
            foreach (range('A', 'L') as $col) {
                $sheet->getColumnDimension($col)->setAutoSize(true);
            }
            
            // Set minimum widths for specific columns
            $sheet->getColumnDimension('C')->setWidth(25); // Nama Pasien
            $sheet->getColumnDimension('D')->setWidth(20); // Jenis Sampel
            $sheet->getColumnDimension('E')->setWidth(25); // Lokasi
            $sheet->getColumnDimension('L')->setWidth(30); // Keterangan
            
            // Set row heights
            $sheet->getDefaultRowDimension()->setRowHeight(20);
            
            // ==========================================
            // DOWNLOAD FILE
            // ==========================================
            
            $filename = 'Inventory_Sampel_' . date('Y-m-d_H-i-s') . '.xlsx';
            
            header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
            header('Content-Disposition: attachment;filename="' . $filename . '"');
            header('Cache-Control: max-age=0');
            header('Cache-Control: max-age=1');
            header('Expires: Mon, 26 Jul 1997 05:00:00 GMT');
            header('Last-Modified: ' . gmdate('D, d M Y H:i:s') . ' GMT');
            header('Cache-Control: cache, must-revalidate');
            header('Pragma: public');
            
            $writer = new Xlsx($spreadsheet);
            $writer->save('php://output');
            
            // Log activity
            $this->Admin_model->log_activity(
                $this->session->userdata('user_id'),
                'Export inventory sampel ke Excel: ' . $filename,
                'sampel_storage',
                null
            );
            
            // Clean up
            $spreadsheet->disconnectWorksheets();
            unset($spreadsheet);
            
            exit();
            
        } catch (Exception $e) {
            log_message('error', 'Error exporting inventory: ' . $e->getMessage());
            $this->session->set_flashdata('error', 'Gagal mengekspor data: ' . $e->getMessage());
            redirect('sample_inventory');
        }
    }

    /**
     * Format status display
     */
    private function _format_status($status) {
        $status_map = [
            'tersimpan' => 'Tersimpan',
            'diproses' => 'Diproses',
            'dibuang' => 'Dibuang',
            'dikembalikan' => 'Dikembalikan'
        ];
        
        return $status_map[strtolower($status)] ?? ucfirst($status);
    }
}