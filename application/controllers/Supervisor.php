<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Supervisor extends CI_Controller {

    public function __construct()
    {
        parent::__construct();
        
        // Set timezone to Jakarta/Asia
        date_default_timezone_set('Asia/Jakarta');
        
        // Check if user is logged in and has supervisor role
        if (!$this->session->userdata('logged_in') || $this->session->userdata('role') !== 'supervisor') {
            $this->session->set_flashdata('error', 'Akses ditolak. Anda tidak memiliki izin untuk mengakses halaman ini.');
            redirect('auth/login');
        }
        
        $this->load->model(['User_model', 'Laboratorium_model']);
        $this->load->library(['form_validation']);
        $this->load->helper(['form', 'url', 'date']);
    }

    /**
     * Quality Control Dashboard
     */
    public function quality_control()
    {
        // Clear any existing flashdata to prevent unwanted notifications
        $this->session->unset_userdata('flashdata');
        
        $data['title'] = 'Validasi Hasil (Quality Control)';
        
        try {
            // Get pending validation dengan proper JOIN untuk nama_petugas
            $data['pending_validation'] = $this->Laboratorium_model->get_results_pending_validation_enhanced();
            
            // Get recent validations dengan proper JOIN
            $data['recent_validations'] = $this->Laboratorium_model->get_recent_validations_enhanced();
            
            // Get QC stats
            $data['qc_stats'] = $this->Laboratorium_model->get_qc_dashboard_stats();
            
        } catch (Exception $e) {
            log_message('error', 'Error getting QC data: ' . $e->getMessage());
            $data['pending_validation'] = array();
            $data['recent_validations'] = array();
            $data['qc_stats'] = array(
                'pending_validation' => 0,
                'validated_today' => 0,
                'validated_this_month' => 0,
                'avg_validation_time' => 0
            );
        }
        
        $this->load->view('template/header', $data);
        $this->load->view('template/sidebar', $data);
        $this->load->view('supervisor/quality_control', $data);
        $this->load->view('template/footer');
    }

    /**
     * Get QC Dashboard Data (AJAX)
     */
    public function get_qc_dashboard_data()
    {
        // Set JSON header
        header('Content-Type: application/json');
        
        try {
            $stats = $this->Laboratorium_model->get_qc_dashboard_stats();
            
            if (!$stats) {
                $stats = array(
                    'pending_validation' => 0,
                    'validated_today' => 0,
                    'validated_this_month' => 0,
                    'avg_validation_time' => 0
                );
            }
            
            echo json_encode([
                'success' => true, 
                'data' => $stats
            ]);
            exit; // Important: stop execution after JSON output
            
        } catch (Exception $e) {
            log_message('error', 'Error getting QC dashboard data: ' . $e->getMessage());
            echo json_encode([
                'success' => false, 
                'message' => 'Gagal memuat data dashboard'
            ]);
            exit;
        }
    }
  
    public function get_invoice_details($examination_id)
    {
        try {
            $invoice = $this->Laboratorium_model->get_invoice_by_examination($examination_id);
            
            if (!$invoice) {
                echo json_encode(['success' => false, 'message' => 'Invoice tidak ditemukan']);
                return;
            }
            
            // Get examination details
            $examination = $this->Laboratorium_model->get_examination_by_id($examination_id);
            
            echo json_encode([
                'success' => true,
                'invoice' => $invoice,
                'examination' => $examination
            ]);
            
        } catch (Exception $e) {
            log_message('error', 'Error getting invoice details: ' . $e->getMessage());
            echo json_encode(['success' => false, 'message' => 'Gagal memuat detail invoice']);
        }
    }

   /**
 * Batch Validate Results (SIMPLE VERSION)
 */
public function batch_validate_results()
{
    // Pastikan ini adalah POST request
    if ($this->input->method() !== 'post') {
        echo json_encode(['success' => false, 'message' => 'Invalid request method']);
        return;
    }
    
    // Dapatkan data JSON
    $input = json_decode(file_get_contents('php://input'), true);
    $examination_ids = isset($input['examination_ids']) ? $input['examination_ids'] : [];
    
    if (empty($examination_ids) || !is_array($examination_ids)) {
        echo json_encode(['success' => false, 'message' => 'Tidak ada pemeriksaan yang dipilih']);
        return;
    }
    
    try {
        $user_id = $this->session->userdata('user_id');
        $petugas_id = $this->Laboratorium_model->get_petugas_id_by_user_id($user_id);
        
        // FORCE TIMEZONE JAKARTA - Gunakan MySQL untuk mendapatkan waktu Jakarta
        // Karena server hosting mungkin menggunakan UTC
        $jakarta_time = $this->db->query("SELECT DATE_ADD(UTC_TIMESTAMP(), INTERVAL 7 HOUR) as jakarta_time")->row()->jakarta_time;
        $current_time = $jakarta_time;
        $current_date = substr($jakarta_time, 0, 10); // Y-m-d
        $current_year = substr($jakarta_time, 0, 4);  // Y
        
        $success_count = 0;
        $failed_count = 0;
        $already_validated = 0;
        
        foreach ($examination_ids as $exam_id) {
            // Cek apakah sudah divalidasi
            $examination = $this->db->get_where('pemeriksaan_lab', ['pemeriksaan_id' => $exam_id])->row_array();
            
            if (!$examination) {
                $failed_count++;
                continue;
            }
            
            if ($examination['status_pemeriksaan'] === 'selesai') {
                $already_validated++;
                continue;
            }
            
            // Proses validasi
            $update_data = [
                'status_pemeriksaan' => 'selesai',
                'completed_at' => $current_time,
                'updated_at' => $current_time
            ];
            
            $this->db->where('pemeriksaan_id', $exam_id);
            $result = $this->db->update('pemeriksaan_lab', $update_data);
            
            if ($result) {
                // Buat timeline
                if ($petugas_id) {
                    $timeline_data = [
                        'pemeriksaan_id' => $exam_id,
                        'status' => 'Hasil Divalidasi',
                        'keterangan' => 'Hasil divalidasi batch oleh supervisor',
                        'petugas_id' => $petugas_id,
                        'tanggal_update' => $current_time
                    ];
                    $this->db->insert('timeline_progres', $timeline_data);
                }
                
                // Buat invoice jika belum ada
                $existing_invoice = $this->db->get_where('invoice', ['pemeriksaan_id' => $exam_id])->row_array();
                if (!$existing_invoice) {
                    $invoice_number = 'INV-' . $current_year . '-' . str_pad($exam_id, 4, '0', STR_PAD_LEFT);
                    $invoice_data = [
                        'pemeriksaan_id' => $exam_id,
                        'nomor_invoice' => $invoice_number,
                        'tanggal_invoice' => $current_date,
                        'jenis_pembayaran' => isset($examination['jenis_pembayaran']) ? $examination['jenis_pembayaran'] : 'umum',
                        'total_biaya' => isset($examination['biaya']) ? floatval($examination['biaya']) : 0,
                        'status_pembayaran' => 'belum_bayar',
                        'keterangan' => 'Invoice generated from batch validation',
                        'created_by' => $user_id,
                        'created_at' => $current_time
                    ];
                    $this->db->insert('invoice', $invoice_data);
                }
                
                $success_count++;
            } else {
                $failed_count++;
            }
        }
        
        $message = "Berhasil memvalidasi {$success_count} pemeriksaan";
        if ($already_validated > 0) {
            $message .= ", {$already_validated} sudah divalidasi sebelumnya";
        }
        if ($failed_count > 0) {
            $message .= ", {$failed_count} gagal";
        }
        
        echo json_encode([
            'success' => true,
            'message' => $message,
            'stats' => [
                'success' => $success_count,
                'already_validated' => $already_validated,
                'failed' => $failed_count
            ]
        ]);
        
    } catch (Exception $e) {
        log_message('error', 'Batch validation error: ' . $e->getMessage());
        echo json_encode(['success' => false, 'message' => 'Terjadi kesalahan: ' . $e->getMessage()]);
    }
}
/**
 * Validate Single Result - FIXED JSON RESPONSE
 */
public function validate_result($examination_id)
{
    // 1. MATIKAN SEMUA OUTPUT BUFFER
    while (ob_get_level()) {
        ob_end_clean();
    }
    
    // 2. SET HEADER PERTAMA KALI
    header('Content-Type: application/json; charset=utf-8');
    header('Cache-Control: no-cache, must-revalidate');
    
    // 3. MATIKAN ERROR DISPLAY
    $old_error_reporting = error_reporting();
    $old_display_errors = ini_get('display_errors');
    error_reporting(0);
    ini_set('display_errors', 0);
    
    try {
        // 4. VALIDASI METHOD
        if (!$this->input->is_ajax_request() && $this->input->method() !== 'post') {
            echo json_encode([
                'success' => false,
                'message' => 'Request harus menggunakan POST atau AJAX'
            ], JSON_UNESCAPED_UNICODE);
            exit;
        }

        // 5. VALIDASI EXAMINATION ID
        if (!is_numeric($examination_id) || $examination_id <= 0) {
            echo json_encode([
                'success' => false,
                'message' => 'ID pemeriksaan tidak valid'
            ], JSON_UNESCAPED_UNICODE);
            exit;
        }

        // 6. CEK SESSION
        $user_id = $this->session->userdata('user_id');
        if (!$user_id) {
            echo json_encode([
                'success' => false,
                'message' => 'Sesi telah berakhir. Silakan login kembali.'
            ], JSON_UNESCAPED_UNICODE);
            exit;
        }

        // 7. CEK ROLE
        if ($this->session->userdata('role') !== 'supervisor') {
            echo json_encode([
                'success' => false,
                'message' => 'Anda tidak memiliki izin untuk melakukan validasi'
            ], JSON_UNESCAPED_UNICODE);
            exit;
        }

        // 8. DAPATKAN PETUGAS ID
        $this->load->model('Laboratorium_model');
        $petugas_id = $this->Laboratorium_model->get_petugas_id_by_user_id($user_id);
        
        // 9. CEK APAKAH EXAMINATION ADA
        $this->db->where('pemeriksaan_id', $examination_id);
        $examination = $this->db->get('pemeriksaan_lab')->row_array();
        
        if (!$examination) {
            echo json_encode([
                'success' => false,
                'message' => 'Pemeriksaan tidak ditemukan'
            ], JSON_UNESCAPED_UNICODE);
            exit;
        }

        // 10. CEK APAKAH SUDAH DIVALIDASI
        if ($examination['status_pemeriksaan'] === 'selesai') {
            echo json_encode([
                'success' => true,
                'already_validated' => true,
                'message' => 'Pemeriksaan sudah divalidasi sebelumnya'
            ], JSON_UNESCAPED_UNICODE);
            exit;
        }

        // 11. PROSES VALIDASI - FORCE TIMEZONE JAKARTA via MySQL
        $jakarta_time = $this->db->query("SELECT DATE_ADD(UTC_TIMESTAMP(), INTERVAL 7 HOUR) as jakarta_time")->row()->jakarta_time;
        $current_time = $jakarta_time;
        $current_date = substr($jakarta_time, 0, 10);
        
        $update_data = [
            'status_pemeriksaan' => 'selesai',
            'completed_at' => $current_time,
            'updated_at' => $current_time
        ];
        
        $this->db->where('pemeriksaan_id', $examination_id);
        $update_result = $this->db->update('pemeriksaan_lab', $update_data);
        
        if (!$update_result) {
            throw new Exception('Gagal mengupdate status pemeriksaan');
        }

        // 12. TAMBAHKAN TIMELINE
        if ($petugas_id) {
            $timeline_data = [
                'pemeriksaan_id' => $examination_id,
                'status' => 'Hasil Divalidasi',
                'keterangan' => 'Hasil pemeriksaan telah divalidasi oleh supervisor',
                'petugas_id' => $petugas_id,
                'tanggal_update' => $current_time
            ];
            $this->db->insert('timeline_progres', $timeline_data);
        }

        // 13. BUAT INVOICE JIKA BELUM ADA
        $this->db->where('pemeriksaan_id', $examination_id);
        $existing_invoice = $this->db->get('invoice')->row_array();
        
        $invoice_created = false;
        if (!$existing_invoice) {
            $invoice_number = 'INV-' . date('Ymd') . '-' . str_pad($examination_id, 4, '0', STR_PAD_LEFT);
            
            $invoice_data = [
                'pemeriksaan_id' => $examination_id,
                'nomor_invoice' => $invoice_number,
                'tanggal_invoice' => $current_date,
                'jenis_pembayaran' => isset($examination['jenis_pembayaran']) ? $examination['jenis_pembayaran'] : 'umum',
                'total_biaya' => isset($examination['biaya']) ? floatval($examination['biaya']) : 0,
                'status_pembayaran' => 'belum_bayar',
                'keterangan' => 'Invoice otomatis dibuat setelah validasi',
                'created_by' => $user_id,
                'created_at' => $current_time
            ];
            
            $this->db->insert('invoice', $invoice_data);
            $invoice_created = true;
        }

        // 14. LOG AKTIVITAS
        $this->load->model('User_model');
        $this->User_model->log_activity(
            $user_id, 
            'Validasi hasil pemeriksaan', 
            'pemeriksaan_lab', 
            $examination_id
        );

        // 15. RESPONSE SUKSES
        $response = [
            'success' => true,
            'message' => 'Hasil pemeriksaan berhasil divalidasi.',
            'invoice_created' => $invoice_created,
            'examination_id' => $examination_id
        ];
        
        if ($invoice_created) {
            $response['message'] .= ' Invoice telah dibuat.';
            $response['invoice_number'] = $invoice_number;
        }
        
        echo json_encode($response, JSON_UNESCAPED_UNICODE);
        
    } catch (Exception $e) {
        // 16. ERROR HANDLING
        log_message('error', 'VALIDATION ERROR - Exam ID: ' . $examination_id . ' - ' . $e->getMessage());
        
        echo json_encode([
            'success' => false,
            'message' => 'Terjadi kesalahan sistem: ' . $e->getMessage()
        ], JSON_UNESCAPED_UNICODE);
        
    } finally {
        // 17. RESTORE ERROR SETTINGS
        error_reporting($old_error_reporting);
        ini_set('display_errors', $old_display_errors);
        
        // 18. PASTIKAN TIDAK ADA OUTPUT LAIN
        exit;
    }
}
    public function dashboard()
    {
        $data['title'] = 'Dashboard Supervisor';
        
        try {
            // Get QC statistics
            $data['qc_stats'] = $this->Laboratorium_model->get_qc_dashboard_stats();
            
            // Get lab performance stats
            $data['performance'] = $this->Laboratorium_model->get_lab_performance_stats(30);
            
            // Get pending validation
            $data['pending_validation'] = $this->Laboratorium_model->get_results_pending_validation_enhanced();
            
            // Get recent validations
            $data['recent_validations'] = $this->Laboratorium_model->get_recent_validations_enhanced(10);
            
            // Get examination status distribution
            $data['status_distribution'] = $this->Laboratorium_model->get_examination_status_distribution();
            
        } catch (Exception $e) {
            log_message('error', 'Error loading supervisor dashboard: ' . $e->getMessage());
            $data['qc_stats'] = array(
                'pending_validation' => 0,
                'validated_today' => 0,
                'validated_this_month' => 0,
                'avg_validation_time' => 0
            );
            $data['performance'] = array();
            $data['pending_validation'] = array();
            $data['recent_validations'] = array();
            $data['status_distribution'] = array();
        }
        
        $this->load->view('template/header', $data);
        $this->load->view('template/sidebar', $data);
        $this->load->view('supervisor/index', $data);
        $this->load->view('template/footer');
    }

    /**
     * History of all validated results
     */
    public function histori()
    {
        $data['title'] = 'Histori Validasi Pemeriksaan';
        
        // Pagination setup
        $limit = 20;
        $page = $this->input->get('page') ?: 1;
        $offset = ($page - 1) * $limit;
        
        // Get filters from URL
        $filters = array(
            'date_from' => $this->input->get('date_from'),
            'date_to' => $this->input->get('date_to'),
            'jenis_pemeriksaan' => $this->input->get('jenis_pemeriksaan'),
            'validator' => $this->input->get('validator'),
            'search' => $this->input->get('search')
        );
        
        try {
            // Get validated results with pagination
            $data['results'] = $this->Laboratorium_model->get_validated_results_paginated($filters, $limit, $offset);
            $data['total_results'] = $this->Laboratorium_model->count_validated_results($filters);
            
            // Pagination info
            $data['current_page'] = $page;
            $data['total_pages'] = ceil($data['total_results'] / $limit);
            $data['has_prev'] = $page > 1;
            $data['has_next'] = $page < $data['total_pages'];
            
            // Get filter options
            $data['examination_types'] = $this->Laboratorium_model->get_examination_type_options();
            $data['validators'] = $this->Laboratorium_model->get_all_petugas_lab();
            
        } catch (Exception $e) {
            log_message('error', 'Error getting history results: ' . $e->getMessage());
            $data['results'] = array();
            $data['total_results'] = 0;
            $data['current_page'] = 1;
            $data['total_pages'] = 0;
            $data['has_prev'] = false;
            $data['has_next'] = false;
            $data['examination_types'] = array();
            $data['validators'] = array();
        }
        
        $data['filters'] = $filters;
        
        $this->load->view('template/header', $data);
        $this->load->view('template/sidebar', $data);
        $this->load->view('supervisor/histori', $data);
        $this->load->view('template/footer');
    }

    /**
     * View all validated results with filters
     */
    public function validated_results()
    {
        $data['title'] = 'Hasil yang Telah Divalidasi';
        
        // Pagination setup
        $limit = 20;
        $page = $this->input->get('page') ?: 1;
        $offset = ($page - 1) * $limit;
        
        // Get filters from URL
        $filters = array(
            'date_from' => $this->input->get('date_from'),
            'date_to' => $this->input->get('date_to'),
            'jenis_pemeriksaan' => $this->input->get('jenis_pemeriksaan'),
            'validator' => $this->input->get('validator'),
            'search' => $this->input->get('search')
        );
        
        try {
            // Get validated results with pagination
            $data['results'] = $this->Laboratorium_model->get_validated_results_paginated($filters, $limit, $offset);
            $data['total_results'] = $this->Laboratorium_model->count_validated_results($filters);
            
            // Pagination info
            $data['current_page'] = $page;
            $data['total_pages'] = ceil($data['total_results'] / $limit);
            $data['has_prev'] = $page > 1;
            $data['has_next'] = $page < $data['total_pages'];
            
            // Get filter options
            $data['examination_types'] = $this->Laboratorium_model->get_examination_type_options();
            $data['validators'] = $this->Laboratorium_model->get_all_petugas_lab();
            
        } catch (Exception $e) {
            log_message('error', 'Error getting validated results: ' . $e->getMessage());
            $data['results'] = array();
            $data['total_results'] = 0;
            $data['current_page'] = 1;
            $data['total_pages'] = 0;
            $data['has_prev'] = false;
            $data['has_next'] = false;
            $data['examination_types'] = array();
            $data['validators'] = array();
        }
        
        $data['filters'] = $filters;
        
        $this->load->view('template/header', $data);
        $this->load->view('template/sidebar', $data);
        $this->load->view('supervisor/validated_results', $data);
        $this->load->view('template/footer');
    }

    /**
     * Quality Control Reports
     */
    public function reports()
    {
        $data['title'] = 'Laporan Quality Control';
        
        $period = $this->input->get('period') ?: 'month';
        
        try {
            // Get QC performance metrics
            $data['metrics'] = $this->Laboratorium_model->get_qc_performance_metrics($period);
            
            // Get validation trends
            $data['trends'] = $this->Laboratorium_model->get_validation_trends($period);
            
            // Get validator performance
            $data['validator_performance'] = $this->Laboratorium_model->get_validator_performance($period);
            
        } catch (Exception $e) {
            log_message('error', 'Error getting QC reports: ' . $e->getMessage());
            $data['metrics'] = array();
            $data['trends'] = array();
            $data['validator_performance'] = array();
        }
        
        $data['period'] = $period;
        
        $this->load->view('template/header', $data);
        $this->load->view('template/sidebar', $data);
        $this->load->view('supervisor/reports', $data);
        $this->load->view('template/footer');
    }

    /**
     * Reject/Return examination for correction
     */
    public function reject_examination($examination_id)
    {
        if ($this->input->method() !== 'post') {
            echo json_encode(['success' => false, 'message' => 'Invalid request method']);
            return;
        }
        
        $this->form_validation->set_rules('rejection_reason', 'Alasan Penolakan', 'required|min_length[10]');
        
        if ($this->form_validation->run() === FALSE) {
            echo json_encode(['success' => false, 'message' => strip_tags(validation_errors())]);
            return;
        }
        
        try {
            $rejection_reason = $this->input->post('rejection_reason');
            $petugas_id = $this->Laboratorium_model->get_petugas_id_by_user_id($this->session->userdata('user_id'));
            
            // Update status back to progress
            $this->db->where('pemeriksaan_id', $examination_id);
            $this->db->update('pemeriksaan_lab', array(
                'status_pemeriksaan' => 'progress',
                'updated_at' => date('Y-m-d H:i:s')
            ));
            
            // Add timeline entry
            $this->Laboratorium_model->add_sample_timeline(
                $examination_id,
                'Hasil Dikembalikan untuk Perbaikan',
                'Supervisor: ' . $rejection_reason,
                $petugas_id
            );
            
            $this->User_model->log_activity(
                $this->session->userdata('user_id'),
                'Examination rejected by supervisor',
                'pemeriksaan_lab',
                $examination_id
            );
            
            echo json_encode(['success' => true, 'message' => 'Pemeriksaan berhasil dikembalikan untuk perbaikan']);
            
        } catch (Exception $e) {
            log_message('error', 'Error rejecting examination: ' . $e->getMessage());
            echo json_encode(['success' => false, 'message' => 'Terjadi kesalahan saat menolak pemeriksaan']);
        }
    }

public function test_json()
{
    // Stop output buffering
    while (ob_get_level()) {
        ob_end_clean();
    }
    
    // Set headers
    header('Content-Type: application/json; charset=utf-8');
    header('Cache-Control: no-cache, must-revalidate');
    
    // Simple test response
    echo json_encode([
        'success' => true,
        'message' => 'JSON endpoint is working!',
        'timestamp' => date('Y-m-d H:i:s'),
        'base_url' => base_url(),
        'ci_version' => CI_VERSION
    ], JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
    
    exit;
}

/**
 * TEST ENDPOINT - Check examination data
 * Access: http://yoursite.com/supervisor/test_exam/39
 */
public function test_exam($examination_id = null)
{
    // Stop output buffering
    while (ob_get_level()) {
        ob_end_clean();
    }
    
    header('Content-Type: application/json; charset=utf-8');
    
    if (!$examination_id) {
        echo json_encode(['error' => 'No examination_id provided']);
        exit;
    }
    
    try {
        $examination = $this->Laboratorium_model->get_examination_by_id($examination_id);
        
        echo json_encode([
            'success' => true,
            'examination_id' => $examination_id,
            'found' => $examination ? true : false,
            'data' => $examination,
            'has_details' => !empty($examination['examination_details']),
            'detail_count' => empty($examination['examination_details']) ? 0 : count($examination['examination_details'])
        ], JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
        
    } catch (Exception $e) {
        echo json_encode([
            'success' => false,
            'error' => $e->getMessage()
        ]);
    }
    
    exit;
}

/**
 * FIXED VERSION - Get result details for quality control
 * REPLACE YOUR EXISTING get_result_details METHOD WITH THIS
 */
public function get_result_details($examination_id)
{
    // CRITICAL: Clear any output buffers
    while (ob_get_level()) {
        ob_end_clean();
    }
    
    // CRITICAL: Set headers FIRST
    header('Content-Type: application/json; charset=utf-8');
    header('Cache-Control: no-cache, must-revalidate');
    
    // Disable error display that might corrupt JSON
    $old_error_setting = ini_get('display_errors');
    ini_set('display_errors', 0);
    
    try {
        // Validate examination_id
        if (!$examination_id || !is_numeric($examination_id)) {
            echo json_encode([
                'success' => false, 
                'message' => 'ID pemeriksaan tidak valid'
            ], JSON_UNESCAPED_UNICODE);
            exit;
        }
        
        // Get examination with details
        $examination = $this->Laboratorium_model->get_examination_by_id($examination_id);
        
        if (!$examination) {
            echo json_encode([
                'success' => false, 
                'message' => 'Pemeriksaan tidak ditemukan'
            ], JSON_UNESCAPED_UNICODE);
            exit;
        }
        
        // Check if examination has results
        $has_results = $this->Laboratorium_model->examination_has_results($examination_id);
        
        if (!$has_results) {
            echo json_encode([
                'success' => false, 
                'message' => 'Hasil pemeriksaan belum tersedia'
            ], JSON_UNESCAPED_UNICODE);
            exit;
        }
        
        // Get examination details
        $details = isset($examination['examination_details']) ? $examination['examination_details'] : array();
        $is_multiple = !empty($details) && count($details) > 1;
        
        if ($is_multiple) {
            // Multiple examinations - get results for each type
            $results = array();
            
            foreach ($details as $detail) {
                $jenis = $detail['jenis_pemeriksaan'];
                $jenis_results = $this->Laboratorium_model->get_existing_results($examination_id, $jenis);
                
                if ($jenis_results && !empty($jenis_results)) {
                    $formatted = $this->_format_results_for_display($jenis_results, $jenis);
                    if (!empty($formatted)) {
                        $results[$jenis] = $formatted;
                    }
                }
            }
            
            echo json_encode([
                'success' => true,
                'is_multiple' => true,
                'examination' => $examination,
                'results' => $results
            ], JSON_UNESCAPED_UNICODE);
            
        } else {
            // Single examination
            $jenis = $examination['jenis_pemeriksaan'];
            
            // Handle comma-separated jenis (legacy)
            if (strpos($jenis, ',') !== false) {
                $jenis = trim(explode(',', $jenis)[0]);
            }
            
            $results = $this->Laboratorium_model->get_existing_results($examination_id, $jenis);
            
            if (!$results || empty($results)) {
                echo json_encode([
                    'success' => false,
                    'message' => 'Hasil pemeriksaan belum tersedia untuk jenis: ' . $jenis
                ], JSON_UNESCAPED_UNICODE);
                exit;
            }
            
            // Format results
            $formatted_results = $this->_format_results_for_display($results, $jenis);
            
            echo json_encode([
                'success' => true,
                'is_multiple' => false,
                'examination' => $examination,
                'results' => $formatted_results
            ], JSON_UNESCAPED_UNICODE);
        }
        
    } catch (Exception $e) {
        log_message('error', 'Error in get_result_details: ' . $e->getMessage());
        log_message('error', 'Stack trace: ' . $e->getTraceAsString());
        
        echo json_encode([
            'success' => false, 
            'message' => 'Terjadi kesalahan sistem',
            'error' => $e->getMessage(),
            'trace' => ENVIRONMENT === 'development' ? $e->getTraceAsString() : null
        ], JSON_UNESCAPED_UNICODE);
    } finally {
        // Restore error setting
        ini_set('display_errors', $old_error_setting);
    }
    
    exit; // CRITICAL: Stop execution
}

/**
 * Helper: Format results untuk display
 */
private function _format_results_for_display($results, $jenis_pemeriksaan)
{
    if (!is_array($results)) {
        return array();
    }
    
    $formatted = array();
    $jenis = strtolower(trim($jenis_pemeriksaan));
    
    // Define field mappings
    $field_maps = array(
        'kimia darah' => array(
            'gula_darah_sewaktu' => 'Gula Darah Sewaktu (mg/dL)',
            'gula_darah_puasa' => 'Gula Darah Puasa (mg/dL)',
            'gula_darah_2jam_pp' => 'Gula Darah 2 Jam PP (mg/dL)',
            'cholesterol_total' => 'Kolesterol Total (mg/dL)',
            'cholesterol_hdl' => 'Kolesterol HDL (mg/dL)',
            'cholesterol_ldl' => 'Kolesterol LDL (mg/dL)',
            'trigliserida' => 'Trigliserida (mg/dL)',
            'asam_urat' => 'Asam Urat (mg/dL)',
            'ureum' => 'Ureum (mg/dL)',
            'creatinin' => 'Kreatinin (mg/dL)',
            'sgpt' => 'SGPT (U/L)',
            'sgot' => 'SGOT (U/L)'
        ),
        'hematologi' => array(
            'hemoglobin' => 'Hemoglobin (g/dL)',
            'hematokrit' => 'Hematokrit (%)',
            'leukosit' => 'Leukosit (/µL)',
            'trombosit' => 'Trombosit (/µL)',
            'eritrosit' => 'Eritrosit (juta/µL)',
            'mcv' => 'MCV (fL)',
            'mch' => 'MCH (pg)',
            'mchc' => 'MCHC (g/dL)',
            'neutrofil' => 'Neutrofil (%)',
            'limfosit' => 'Limfosit (%)',
            'monosit' => 'Monosit (%)',
            'eosinofil' => 'Eosinofil (%)',
            'basofil' => 'Basofil (%)',
            'laju_endap_darah' => 'LED (mm/jam)',
            'golongan_darah' => 'Golongan Darah',
            'rhesus' => 'Rhesus',
            'clotting_time' => 'Clotting Time (detik)',
            'bleeding_time' => 'Bleeding Time (detik)',
            'malaria' => 'Malaria'
        ),
        'urinologi' => array(
            'makroskopis' => 'Makroskopis',
            'mikroskopis' => 'Mikroskopis',
            'berat_jenis' => 'Berat Jenis',
            'kimia_ph' => 'pH',
            'protein' => 'Protein',
            'glukosa' => 'Glukosa',
            'keton' => 'Keton',
            'bilirubin' => 'Bilirubin',
            'urobilinogen' => 'Urobilinogen',
            'tes_kehamilan' => 'Tes Kehamilan'
        ),
        'serologi' => array(
            'rdt_antigen' => 'RDT Antigen',
            'widal' => 'Widal',
            'hbsag' => 'HBsAg',
            'ns1' => 'NS1 (Dengue)',
            'hiv' => 'HIV'
        ),
        'serologi imunologi' => array(
            'rdt_antigen' => 'RDT Antigen',
            'widal' => 'Widal',
            'hbsag' => 'HBsAg',
            'ns1' => 'NS1 (Dengue)',
            'hiv' => 'HIV'
        ),
        'tbc' => array(
            'dahak' => 'Dahak (BTA)',
            'tcm' => 'TCM (GeneXpert)'
        ),
        'ims' => array(
            'sifilis' => 'Sifilis',
            'duh_tubuh' => 'Duh Tubuh'
        )
    );
    
    $fields = isset($field_maps[$jenis]) ? $field_maps[$jenis] : array();
    
    // Filter only filled values
    foreach ($fields as $key => $label) {
        if (isset($results[$key]) && $results[$key] !== null && $results[$key] !== '') {
            $formatted[$label] = $results[$key];
        }
    }
    
    return $formatted;
}
// Tambahkan method ini di Controller Supervisor setelah method test_exam()

/**
 * Get validated results with pagination (for validated_results page)
 */
public function get_validated_results_paginated($filters = array(), $limit = 20, $offset = 0)
{
    $this->db->select('pl.*, p.nama as nama_pasien, p.nik, pt.nama_petugas, 
                      i.nomor_invoice, i.status_pembayaran');
    $this->db->from('pemeriksaan_lab pl');
    $this->db->join('pasien p', 'pl.pasien_id = p.pasien_id', 'left');
    $this->db->join('petugas_lab pt', 'pl.petugas_id = pt.petugas_id', 'left');
    $this->db->join('invoice i', 'pl.pemeriksaan_id = i.pemeriksaan_id', 'left');
    $this->db->where('pl.status_pemeriksaan', 'selesai');
    
    // Apply filters
    if (isset($filters['date_from']) && $filters['date_from']) {
        $this->db->where('DATE(pl.completed_at) >=', $filters['date_from']);
    }
    
    if (isset($filters['date_to']) && $filters['date_to']) {
        $this->db->where('DATE(pl.completed_at) <=', $filters['date_to']);
    }
    
    if (isset($filters['jenis_pemeriksaan']) && $filters['jenis_pemeriksaan']) {
        $this->db->like('pl.jenis_pemeriksaan', $filters['jenis_pemeriksaan']);
    }
    
    if (isset($filters['validator']) && $filters['validator']) {
        $this->db->where('pl.petugas_id', $filters['validator']);
    }
    
    if (isset($filters['search']) && $filters['search']) {
        $this->db->group_start();
        $this->db->like('p.nama', $filters['search']);
        $this->db->or_like('p.nik', $filters['search']);
        $this->db->or_like('pl.nomor_pemeriksaan', $filters['search']);
        $this->db->or_like('i.nomor_invoice', $filters['search']);
        $this->db->group_end();
    }
    
    $this->db->order_by('pl.completed_at', 'DESC');
    $this->db->limit($limit, $offset);
    
    return $this->db->get()->result_array();
}

/**
 * Count validated results
 */
public function count_validated_results($filters = array())
{
    $this->db->from('pemeriksaan_lab pl');
    $this->db->join('pasien p', 'pl.pasien_id = p.pasien_id', 'left');
    $this->db->join('invoice i', 'pl.pemeriksaan_id = i.pemeriksaan_id', 'left');
    $this->db->where('pl.status_pemeriksaan', 'selesai');
    
    // Apply same filters
    if (isset($filters['date_from']) && $filters['date_from']) {
        $this->db->where('DATE(pl.completed_at) >=', $filters['date_from']);
    }
    
    if (isset($filters['date_to']) && $filters['date_to']) {
        $this->db->where('DATE(pl.completed_at) <=', $filters['date_to']);
    }
    
    if (isset($filters['jenis_pemeriksaan']) && $filters['jenis_pemeriksaan']) {
        $this->db->like('pl.jenis_pemeriksaan', $filters['jenis_pemeriksaan']);
    }
    
    if (isset($filters['validator']) && $filters['validator']) {
        $this->db->where('pl.petugas_id', $filters['validator']);
    }
    
    if (isset($filters['search']) && $filters['search']) {
        $this->db->group_start();
        $this->db->like('p.nama', $filters['search']);
        $this->db->or_like('p.nik', $filters['search']);
        $this->db->or_like('pl.nomor_pemeriksaan', $filters['search']);
        $this->db->or_like('i.nomor_invoice', $filters['search']);
        $this->db->group_end();
    }
    
    return $this->db->count_all_results();
}

/**
 * Get QC performance metrics
 */
public function get_qc_performance_metrics($period = 'month')
{
    $date_condition = '';
    switch ($period) {
        case 'week':
            $date_condition = "DATE(completed_at) >= DATE_SUB(CURDATE(), INTERVAL 7 DAY)";
            break;
        case 'month':
            $date_condition = "DATE(completed_at) >= DATE_SUB(CURDATE(), INTERVAL 30 DAY)";
            break;
        case 'quarter':
            $date_condition = "DATE(completed_at) >= DATE_SUB(CURDATE(), INTERVAL 90 DAY)";
            break;
        default:
            $date_condition = "DATE(completed_at) >= DATE_SUB(CURDATE(), INTERVAL 30 DAY)";
    }
    
    $metrics = array();
    
    // Total validations
    $this->db->select('COUNT(*) as total');
    $this->db->where('status_pemeriksaan', 'selesai');
    $this->db->where($date_condition, NULL, FALSE);
    $total_query = $this->db->get('pemeriksaan_lab');
    $metrics['total_validations'] = $total_query->row()->total;
    
    // Average validation time
    $this->db->select('AVG(TIMESTAMPDIFF(HOUR, started_at, completed_at)) as avg_hours');
    $this->db->where('status_pemeriksaan', 'selesai');
    $this->db->where('started_at IS NOT NULL');
    $this->db->where('completed_at IS NOT NULL');
    $this->db->where($date_condition, NULL, FALSE);
    $avg_query = $this->db->get('pemeriksaan_lab');
    $metrics['avg_validation_hours'] = round($avg_query->row()->avg_hours, 1);
    
    // Validations by type
    $this->db->select('jenis_pemeriksaan, COUNT(*) as count');
    $this->db->where('status_pemeriksaan', 'selesai');
    $this->db->where($date_condition, NULL, FALSE);
    $this->db->group_by('jenis_pemeriksaan');
    $type_query = $this->db->get('pemeriksaan_lab');
    $metrics['validations_by_type'] = $type_query->result_array();
    
    return $metrics;
}

/**
 * Get validation trends
 */
public function get_validation_trends($period = 'month')
{
    $interval = $period === 'week' ? 'DAY' : ($period === 'quarter' ? 'WEEK' : 'DAY');
    $group_format = $period === 'week' ? '%Y-%m-%d' : ($period === 'quarter' ? '%Y-%u' : '%Y-%m-%d');
    
    $this->db->select("DATE_FORMAT(completed_at, '{$group_format}') as date, COUNT(*) as count");
    $this->db->where('status_pemeriksaan', 'selesai');
    $this->db->where('completed_at IS NOT NULL');
    
    switch ($period) {
        case 'week':
            $this->db->where('completed_at >=', date('Y-m-d', strtotime('-7 days')));
            break;
        case 'month':
            $this->db->where('completed_at >=', date('Y-m-d', strtotime('-30 days')));
            break;
        case 'quarter':
            $this->db->where('completed_at >=', date('Y-m-d', strtotime('-90 days')));
            break;
    }
    
    $this->db->group_by("DATE_FORMAT(completed_at, '{$group_format}')");
    $this->db->order_by('date', 'ASC');
    
    return $this->db->get('pemeriksaan_lab')->result_array();
}

/**
 * Get validator performance
 */
public function get_validator_performance($period = 'month')
{
    $date_condition = '';
    switch ($period) {
        case 'week':
            $date_condition = "DATE(pl.completed_at) >= DATE_SUB(CURDATE(), INTERVAL 7 DAY)";
            break;
        case 'month':
            $date_condition = "DATE(pl.completed_at) >= DATE_SUB(CURDATE(), INTERVAL 30 DAY)";
            break;
        case 'quarter':
            $date_condition = "DATE(pl.completed_at) >= DATE_SUB(CURDATE(), INTERVAL 90 DAY)";
            break;
    }
    
    $this->db->select('pt.nama_petugas, COUNT(pl.pemeriksaan_id) as validation_count, 
                      AVG(TIMESTAMPDIFF(HOUR, pl.started_at, pl.completed_at)) as avg_hours');
    $this->db->from('pemeriksaan_lab pl');
    $this->db->join('petugas_lab pt', 'pl.petugas_id = pt.petugas_id', 'left');
    $this->db->where('pl.status_pemeriksaan', 'selesai');
    $this->db->where($date_condition, NULL, FALSE);
    $this->db->where('pl.petugas_id IS NOT NULL');
    $this->db->group_by('pt.petugas_id');
    $this->db->order_by('validation_count', 'DESC');
    
    return $this->db->get()->result_array();
}
/**
 * Test endpoint untuk debug validasi
 */
public function test_validation_endpoint($examination_id = null)
{
    // Clean semua output buffer
    while (ob_get_level()) {
        ob_end_clean();
    }
    
    header('Content-Type: application/json; charset=utf-8');
    
    $test_data = [
        'examination_id' => $examination_id,
        'session_user_id' => $this->session->userdata('user_id'),
        'session_role' => $this->session->userdata('role'),
        'server_time' => date('Y-m-d H:i:s'),
        'test_json' => 'This is a JSON response'
    ];
    
    echo json_encode($test_data, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
    exit;
}

/**
 * Test validasi sederhana
 */
public function test_simple_validate($examination_id)
{
    // Clean output
    while (ob_get_level()) {
        ob_end_clean();
    }
    
    header('Content-Type: application/json; charset=utf-8');
    
    try {
        // Cek apakah examination ada
        $this->db->where('pemeriksaan_id', $examination_id);
        $exam = $this->db->get('pemeriksaan_lab')->row_array();
        
        if (!$exam) {
            echo json_encode([
                'success' => false,
                'message' => 'Examination not found'
            ]);
            exit;
        }
        
        // Update status - FORCE TIMEZONE JAKARTA via MySQL
        $jakarta_time = $this->db->query("SELECT DATE_ADD(UTC_TIMESTAMP(), INTERVAL 7 HOUR) as jakarta_time")->row()->jakarta_time;
        $this->db->where('pemeriksaan_id', $examination_id);
        $result = $this->db->update('pemeriksaan_lab', [
            'status_pemeriksaan' => 'selesai',
            'completed_at' => $jakarta_time
        ]);
        
        echo json_encode([
            'success' => $result,
            'message' => $result ? 'Success' : 'Failed',
            'examination_id' => $examination_id,
            'previous_status' => $exam['status_pemeriksaan']
        ]);
        
    } catch (Exception $e) {
        echo json_encode([
            'success' => false,
            'message' => $e->getMessage()
        ]);
    }
    
    exit;
}
/**
 * Get validation history for a patient
 */
public function get_validation_history($examination_id = null)
{
    // Clean output buffers
    while (ob_get_level()) {
        ob_end_clean();
    }
    
    header('Content-Type: application/json; charset=utf-8');
    header('Cache-Control: no-cache, must-revalidate');
    
    try {
        // Validate examination ID
        if (!$examination_id || !is_numeric($examination_id)) {
            echo json_encode([
                'success' => false,
                'message' => 'ID pemeriksaan tidak valid'
            ], JSON_UNESCAPED_UNICODE);
            exit;
        }
        
        // Get current examination details
        $current_exam = $this->db->get_where('pemeriksaan_lab', ['pemeriksaan_id' => $examination_id])->row_array();
        
        if (!$current_exam) {
            echo json_encode([
                'success' => false,
                'message' => 'Pemeriksaan tidak ditemukan'
            ], JSON_UNESCAPED_UNICODE);
            exit;
        }
        
        // Get patient info
        $patient = $this->db->get_where('pasien', ['pasien_id' => $current_exam['pasien_id']])->row_array();
        
        if (!$patient) {
            echo json_encode([
                'success' => false,
                'message' => 'Data pasien tidak ditemukan'
            ], JSON_UNESCAPED_UNICODE);
            exit;
        }
        
        // Get validation history for this patient (excluding current examination)
        $this->db->select('pl.*, p.nama as nama_pasien, p.nik, pt.nama_petugas, 
                          DATE(pl.completed_at) as tanggal_validasi,
                          TIMESTAMPDIFF(DAY, pl.completed_at, NOW()) as days_ago');
        $this->db->from('pemeriksaan_lab pl');
        $this->db->join('pasien p', 'pl.pasien_id = p.pasien_id', 'left');
        $this->db->join('petugas_lab pt', 'pl.petugas_id = pt.petugas_id', 'left');
        $this->db->where('pl.pasien_id', $current_exam['pasien_id']);
        $this->db->where('pl.status_pemeriksaan', 'selesai');
        $this->db->where('pl.pemeriksaan_id !=', $examination_id);
        $this->db->order_by('pl.completed_at', 'DESC');
        $this->db->limit(20); // Limit to 20 most recent
        
        $history = $this->db->get()->result_array();
        
        // Get statistics
        $stats = [
            'total_validations' => count($history),
            'first_validation' => null,
            'last_validation' => null,
            'most_common_test' => null
        ];
        
        if (!empty($history)) {
            $stats['first_validation'] = end($history)['completed_at'];
            $stats['last_validation'] = $history[0]['completed_at'];
            
            // Count test types
            $test_counts = [];
            foreach ($history as $item) {
                $test_type = $item['jenis_pemeriksaan'];
                if (!isset($test_counts[$test_type])) {
                    $test_counts[$test_type] = 0;
                }
                $test_counts[$test_type]++;
            }
            
            if (!empty($test_counts)) {
                arsort($test_counts);
                $stats['most_common_test'] = key($test_counts) . ' (' . current($test_counts) . 'x)';
            }
        }
        
        echo json_encode([
            'success' => true,
            'patient' => [
                'nama' => $patient['nama'],
                'nik' => $patient['nik'],
                'pasien_id' => $patient['pasien_id'],
                'umur' => $patient['umur'],
                'jenis_kelamin' => $patient['jenis_kelamin']
            ],
            'current_examination' => [
                'id' => $current_exam['pemeriksaan_id'],
                'nomor_pemeriksaan' => $current_exam['nomor_pemeriksaan'],
                'jenis_pemeriksaan' => $current_exam['jenis_pemeriksaan'],
                'tanggal_pemeriksaan' => $current_exam['tanggal_pemeriksaan']
            ],
            'history' => $history,
            'stats' => $stats,
            'has_history' => !empty($history)
        ], JSON_UNESCAPED_UNICODE);
        
    } catch (Exception $e) {
        log_message('error', 'Error getting validation history: ' . $e->getMessage());
        echo json_encode([
            'success' => false,
            'message' => 'Terjadi kesalahan saat mengambil histori'
        ], JSON_UNESCAPED_UNICODE);
    }
    
    exit;
}
/**
 * Get historical result details
 */
public function get_historical_result_details($examination_id)
{
    // Clean output buffers
    while (ob_get_level()) {
        ob_end_clean();
    }
    
    header('Content-Type: application/json; charset=utf-8');
    
    try {
        // Same as get_result_details but for historical data
        return $this->get_result_details($examination_id);
        
    } catch (Exception $e) {
        echo json_encode([
            'success' => false,
            'message' => 'Gagal memuat detail hasil historis'
        ], JSON_UNESCAPED_UNICODE);
        exit;
    }
}
public function validate_result_simple($examination_id = null)
{
    // 1. Clean ALL output buffers
    while (ob_get_level()) {
        ob_end_clean();
    }
    
    // 2. Start fresh buffer
    ob_start();
    
    // 3. Disable error display
    ini_set('display_errors', 0);
    error_reporting(0);
    
    try {
        // 4. Validate method
        if ($this->input->method() !== 'post') {
            $this->_send_json(['success' => false, 'message' => 'Invalid request method']);
            return;
        }

        // 5. Validate examination ID - PERBAIKAN: cek null dan empty
        if (empty($examination_id) || !is_numeric($examination_id) || $examination_id <= 0) {
            $this->_send_json(['success' => false, 'message' => 'ID pemeriksaan tidak valid (ID: ' . var_export($examination_id, true) . ')']);
            return;
        }

        // 6. Check session
        $user_id = $this->session->userdata('user_id');
        if (!$user_id) {
            $this->_send_json(['success' => false, 'message' => 'Sesi berakhir. Silakan login kembali.']);
            return;
        }

        // 7. Check role
        if ($this->session->userdata('role') !== 'supervisor') {
            $this->_send_json(['success' => false, 'message' => 'Tidak memiliki izin validasi']);
            return;
        }

        // 8. Get petugas ID
        $petugas_id = $this->Laboratorium_model->get_petugas_id_by_user_id($user_id);
        
        // 9. Check examination exists
        $examination = $this->db->get_where('pemeriksaan_lab', ['pemeriksaan_id' => $examination_id])->row_array();
        
        if (!$examination) {
            $this->_send_json(['success' => false, 'message' => 'Pemeriksaan tidak ditemukan']);
            return;
        }

        // 10. Check if already validated
        if ($examination['status_pemeriksaan'] === 'selesai') {
            $this->_send_json([
                'success' => true,
                'already_validated' => true,
                'message' => 'Pemeriksaan sudah divalidasi sebelumnya'
            ]);
            return;
        }

        // 11. Update status - FORCE TIMEZONE JAKARTA via MySQL
        $jakarta_time = $this->db->query("SELECT DATE_ADD(UTC_TIMESTAMP(), INTERVAL 7 HOUR) as jakarta_time")->row()->jakarta_time;
        $current_time = $jakarta_time;
        
        $update_data = [
            'status_pemeriksaan' => 'selesai',
            'completed_at' => $current_time,
            'updated_at' => $current_time
        ];
        
        $this->db->where('pemeriksaan_id', $examination_id);
        $update_result = $this->db->update('pemeriksaan_lab', $update_data);
        
        if (!$update_result) {
            $this->_send_json(['success' => false, 'message' => 'Gagal mengupdate status']);
            return;
        }

        // 12. Add timeline
        if ($petugas_id) {
            $timeline_data = [
                'pemeriksaan_id' => $examination_id,
                'status' => 'Hasil Divalidasi',
                'keterangan' => 'Hasil pemeriksaan telah divalidasi oleh supervisor',
                'petugas_id' => $petugas_id,
                'tanggal_update' => date('Y-m-d H:i:s')
            ];
            $this->db->insert('timeline_progres', $timeline_data);
        }

        // 13. Create invoice (non-blocking)
        $invoice_created = false;
        try {
            $existing_invoice = $this->db->get_where('invoice', ['pemeriksaan_id' => $examination_id])->row_array();
            
            if (!$existing_invoice) {
                $invoice_number = 'INV-' . date('Ymd') . '-' . str_pad($examination_id, 4, '0', STR_PAD_LEFT);
                
                $invoice_data = [
                    'pemeriksaan_id' => $examination_id,
                    'nomor_invoice' => $invoice_number,
                    'tanggal_invoice' => date('Y-m-d'),
                    'jenis_pembayaran' => $examination['jenis_pembayaran'] ?? 'umum',
                    'total_biaya' => floatval($examination['biaya'] ?? 0),
                    'status_pembayaran' => 'belum_bayar',
                    'keterangan' => 'Invoice otomatis dibuat setelah validasi',
                    'created_by' => $user_id,
                    'created_at' => date('Y-m-d H:i:s')
                ];
                
                $this->db->insert('invoice', $invoice_data);
                $invoice_created = true;
            }
        } catch (Exception $e) {
            log_message('error', 'Invoice creation failed (non-blocking): ' . $e->getMessage());
        }

        // 14. Log activity
        $this->User_model->log_activity(
            $user_id, 
            'Validasi hasil pemeriksaan', 
            'pemeriksaan_lab', 
            $examination_id
        );

        // 15. Success response
        $response = [
            'success' => true,
            'message' => 'Hasil pemeriksaan berhasil divalidasi.',
            'invoice_created' => $invoice_created,
            'examination_id' => $examination_id
        ];
        
        $this->_send_json($response);
        
    } catch (Exception $e) {
        log_message('error', 'Validation error: ' . $e->getMessage());
        $this->_send_json([
            'success' => false,
            'message' => 'Terjadi kesalahan sistem'
        ]);
    }
}

/**
 * Helper method to send clean JSON response
 */
private function _send_json($data)
{
    // Clean any existing output
    ob_clean();
    
    // Set headers
    header('Content-Type: application/json; charset=utf-8');
    header('Cache-Control: no-cache, must-revalidate');
    
    // Output JSON
    echo json_encode($data, JSON_UNESCAPED_UNICODE);
    
    // Exit cleanly
    exit;
}

/**
 * AJAX: Get Validation Trend Data (7 days)
 * Returns daily validation counts for examinations and equipment
 */
public function ajax_get_validation_trend()
{
    header('Content-Type: application/json');
    
    try {
        $days = $this->input->get('days') ? intval($this->input->get('days')) : 7;
        $days = min(30, max(7, $days)); // Limit between 7-30 days
        
        // Get examination validation trend
        $exam_trend = $this->db->query("
            SELECT 
                DATE(completed_at) as date,
                COUNT(*) as validated
            FROM pemeriksaan_lab
            WHERE status_pemeriksaan = 'selesai'
            AND completed_at >= DATE_SUB(CURDATE(), INTERVAL ? DAY)
            AND completed_at IS NOT NULL
            GROUP BY DATE(completed_at)
            ORDER BY date ASC
        ", array($days))->result_array();
        
        // Get equipment QC validation trend
        $this->load->model('Quality_control_model');
        $alat_trend = $this->db->query("
            SELECT 
                DATE(validated_at) as date,
                COUNT(*) as validated
            FROM quality_control
            WHERE validated_by IS NOT NULL
            AND validated_at >= DATE_SUB(CURDATE(), INTERVAL ? DAY)
            AND validated_at IS NOT NULL
            GROUP BY DATE(validated_at)
            ORDER BY date ASC
        ", array($days))->result_array();
        
        // Generate dates for the past X days
        $dates = array();
        for ($i = $days - 1; $i >= 0; $i--) {
            $dates[] = date('Y-m-d', strtotime("-$i days"));
        }
        
        // Map data to dates
        $exam_data = array();
        $alat_data = array();
        
        foreach ($dates as $date) {
            // Examination validations
            $found = false;
            foreach ($exam_trend as $row) {
                if ($row['date'] == $date) {
                    $exam_data[] = intval($row['validated']);
                    $found = true;
                    break;
                }
            }
            if (!$found) $exam_data[] = 0;
            
            // Equipment QC validations
            $found = false;
            foreach ($alat_trend as $row) {
                if ($row['date'] == $date) {
                    $alat_data[] = intval($row['validated']);
                    $found = true;
                    break;
                }
            }
            if (!$found) $alat_data[] = 0;
        }
        
        echo json_encode([
            'success' => true,
            'data' => [
                'labels' => $dates,
                'pemeriksaan' => $exam_data,
                'alat' => $alat_data
            ]
        ]);
        
    } catch (Exception $e) {
        log_message('error', 'Error getting validation trend: ' . $e->getMessage());
        echo json_encode([
            'success' => false,
            'message' => 'Gagal memuat data tren'
        ]);
    }
    exit;
}

// ==========================================
// QC ALAT VALIDATION METHODS
// ==========================================

/**
 * QC Alat Validation Page (for Equipment Quality Control)
 */
public function qc_alat_validation()
{
    $this->load->model('Quality_control_model');
    
    $data['title'] = 'Validasi Quality Control Alat';
    
    try {
        // Get pending QC (supervisor IS NULL)
        $data['pending_qc'] = $this->Quality_control_model->get_pending_validation();
        
        // Get validated QC 
        $data['validated_qc'] = $this->Quality_control_model->get_validated_qc(10);
        
        // Stats
        $total_pending = count($data['pending_qc']);
        $passed_pending = count(array_filter($data['pending_qc'], fn($qc) => $qc['hasil_qc'] === 'Passed'));
        $failed_pending = count(array_filter($data['pending_qc'], fn($qc) => $qc['hasil_qc'] === 'Failed'));
        
        $data['stats'] = [
            'total_pending' => $total_pending,
            'passed_pending' => $passed_pending,
            'failed_pending' => $failed_pending
        ];
        
    } catch (Exception $e) {
        log_message('error', 'Error loading QC Alat validation: ' . $e->getMessage());
        $data['pending_qc'] = array();
        $data['validated_qc'] = array();
        $data['stats'] = [
            'total_pending' => 0,
            'passed_pending' => 0,
            'failed_pending' => 0
        ];
    }
    
    $this->load->view('template/header', $data);
    $this->load->view('template/sidebar', $data);
    $this->load->view('supervisor/qc_alat_validation', $data);
    $this->load->view('template/footer');
}

/**
 * Get QC detail for validation (AJAX)
 */
public function get_qc_alat_detail($qc_id)
{
    $this->load->model('Quality_control_model');
    
    try {
        $qc = $this->Quality_control_model->get_qc_by_id($qc_id);
        
        if (!$qc) {
            echo json_encode(['success' => false, 'message' => 'Data QC tidak ditemukan']);
            return;
        }
        
        echo json_encode([
            'success' => true,
            'qc' => $qc
        ]);
        
    } catch (Exception $e) {
        log_message('error', 'Error getting QC detail: ' . $e->getMessage());
        echo json_encode(['success' => false, 'message' => 'Terjadi kesalahan']);
    }
}

/**
 * Validate QC Alat (approve/reject)
 */
public function validate_qc_alat()
{
    if ($this->input->method() !== 'post') {
        echo json_encode(['success' => false, 'message' => 'Invalid request']);
        return;
    }
    
    $this->load->model('Quality_control_model');
    
    $qc_id = $this->input->post('qc_id');
    $validation_note = $this->input->post('validation_note');
    
    if (!$qc_id) {
        echo json_encode(['success' => false, 'message' => 'ID QC tidak valid']);
        return;
    }
    
    try {
        $supervisor_name = $this->session->userdata('nama_lengkap') ?: $this->session->userdata('username');
        
        $result = $this->Quality_control_model->validate_qc($qc_id, $supervisor_name, $validation_note);
        
        if ($result) {
            // Log activity
            $this->User_model->log_activity(
                $this->session->userdata('user_id'),
                'Validated QC Alat ID: ' . $qc_id,
                'quality_control',
                $qc_id
            );
            
            echo json_encode([
                'success' => true,
                'message' => 'QC berhasil divalidasi'
            ]);
        } else {
            echo json_encode([
                'success' => false,
                'message' => 'Gagal memvalidasi QC'
            ]);
        }
        
    } catch (Exception $e) {
        log_message('error', 'Error validating QC Alat: ' . $e->getMessage());
        echo json_encode(['success' => false, 'message' => 'Terjadi kesalahan']);
    }
}

    /**
     * History of validated QC Alat
     */
    public function qc_alat_history()
    {
        $this->load->model('Quality_control_model');
        
        $data['title'] = 'Histori Validasi QC Alat';
        
        // Pagination
        $limit = 20;
        $page = $this->input->get('page') ?: 1;
        $offset = ($page - 1) * $limit;
        
        // Filters
        $filters = array(
            'start_date' => $this->input->get('start_date'),
            'end_date' => $this->input->get('end_date'),
            'search' => $this->input->get('search')
        );
        
        try {
            // Get history
            $data['history_qc'] = $this->Quality_control_model->get_qc_history($limit, $offset, $filters);
            $data['total_rows'] = $this->Quality_control_model->count_qc_history($filters);
            
            // Pagination info
            $data['current_page'] = $page;
            $data['total_pages'] = ceil($data['total_rows'] / $limit);
            $data['has_prev'] = $page > 1;
            $data['has_next'] = $page < $data['total_pages'];
            
        } catch (Exception $e) {
            log_message('error', 'Error getting QC history: ' . $e->getMessage());
            $data['history_qc'] = array();
            $data['total_rows'] = 0;
            $data['current_page'] = 1;
            $data['total_pages'] = 0;
        }
        
        $data['filters'] = $filters;
        
        $this->load->view('template/header', $data);
        $this->load->view('template/sidebar', $data);
        $this->load->view('supervisor/qc_alat_history', $data);
        $this->load->view('template/footer');
    }

}