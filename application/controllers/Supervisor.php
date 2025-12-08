<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Supervisor extends CI_Controller {

    public function __construct()
    {
        parent::__construct();
        
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

    /**
     * Get result details for quality control (AJAX) - Enhanced
     */
    public function get_result_details($examination_id)
    {
        try {
            $examination = $this->Laboratorium_model->get_examination_by_id($examination_id);
            
            if (!$examination) {
                echo json_encode(['success' => false, 'message' => 'Pemeriksaan tidak ditemukan']);
                return;
            }
            
            // Get results based on examination type
            $results = $this->Laboratorium_model->get_formatted_results_by_examination($examination_id);
            
            echo json_encode([
                'success' => true,
                'examination' => $examination,
                'results' => $results
            ]);
            
        } catch (Exception $e) {
            log_message('error', 'Error getting result details: ' . $e->getMessage());
            echo json_encode(['success' => false, 'message' => 'Gagal memuat detail hasil']);
        }
    }

    /**
     * Get invoice details (AJAX)
     */
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
     * Batch Validate Results
     */
    public function batch_validate_results()
    {
        if ($this->input->method() !== 'post') {
            echo json_encode(['success' => false, 'message' => 'Invalid request method']);
            return;
        }
        
        $json_data = json_decode(file_get_contents('php://input'), true);
        $examination_ids = isset($json_data['examination_ids']) ? $json_data['examination_ids'] : array();
        
        if (empty($examination_ids) || !is_array($examination_ids)) {
            echo json_encode(['success' => false, 'message' => 'Tidak ada pemeriksaan yang dipilih']);
            return;
        }
        
        try {
            // Get supervisor's petugas_id
            $petugas_id = $this->Laboratorium_model->get_petugas_id_by_user_id($this->session->userdata('user_id'));
            
            if (!$petugas_id) {
                echo json_encode(['success' => false, 'message' => 'User tidak terdaftar sebagai petugas lab']);
                return;
            }
            
            $success_count = 0;
            $failed_count = 0;
            $invoice_created = 0;
            
            foreach ($examination_ids as $examination_id) {
                if ($this->Laboratorium_model->validate_examination_result_enhanced($examination_id, $petugas_id)) {
                    $success_count++;
                    
                    // Check if invoice was created
                    $invoice = $this->Laboratorium_model->get_invoice_by_examination($examination_id);
                    if ($invoice) {
                        $invoice_created++;
                    }
                    
                    $this->User_model->log_activity(
                        $this->session->userdata('user_id'), 
                        'Batch validation by supervisor', 
                        'pemeriksaan_lab', 
                        $examination_id
                    );
                } else {
                    $failed_count++;
                }
            }
            
            $message = "Berhasil memvalidasi {$success_count} pemeriksaan";
            if ($invoice_created > 0) {
                $message .= ", {$invoice_created} invoice dibuat";
            }
            if ($failed_count > 0) {
                $message .= ", {$failed_count} gagal";
            }
            
            echo json_encode([
                'success' => true,
                'message' => $message,
                'success_count' => $success_count,
                'failed_count' => $failed_count,
                'invoice_created' => $invoice_created
            ]);
            
        } catch (Exception $e) {
            log_message('error', 'Error in batch validation: ' . $e->getMessage());
            echo json_encode(['success' => false, 'message' => 'Terjadi kesalahan saat validasi batch']);
        }
    }

    /**
     * Validate Single Result
     */
    public function validate_result($examination_id)
    {
        header('Content-Type: application/json');
        
        if ($this->input->method() !== 'post') {
            echo json_encode(['success' => false, 'message' => 'Invalid request method']);
            exit;
        }

        try {
            if (!is_numeric($examination_id)) {
                echo json_encode(['success' => false, 'message' => 'ID pemeriksaan tidak valid']);
                exit;
            }

            $petugas_id = $this->Laboratorium_model->get_petugas_id_by_user_id($this->session->userdata('user_id'));
            
            if (!$petugas_id) {
                echo json_encode(['success' => false, 'message' => 'User tidak terdaftar sebagai petugas lab']);
                exit;
            }

            // Validasi apakah examination exists
            $examination = $this->Laboratorium_model->get_examination_by_id($examination_id);
            if (!$examination) {
                echo json_encode(['success' => false, 'message' => 'Pemeriksaan tidak ditemukan']);
                exit;
            }

            // Validasi status
            if ($examination['status_pemeriksaan'] === 'selesai') {
                $existing_invoice = $this->Laboratorium_model->get_invoice_by_examination($examination_id);
                $invoice_message = $existing_invoice ? ' Invoice sudah ada.' : '';
                
                echo json_encode([
                    'success' => false, 
                    'message' => 'Pemeriksaan sudah divalidasi sebelumnya.' . $invoice_message
                ]);
                exit;
            }

            log_message('info', "Starting validation for examination: {$examination_id} by supervisor: {$petugas_id}");

            // Panggil model untuk validasi
            if ($this->Laboratorium_model->validate_examination_result_enhanced($examination_id, $petugas_id)) {
                
                $this->User_model->log_activity(
                    $this->session->userdata('user_id'), 
                    'Examination result validated by supervisor', 
                    'pemeriksaan_lab', 
                    $examination_id
                );
                
                // Cek apakah invoice berhasil dibuat
                $invoice = $this->Laboratorium_model->get_invoice_by_examination($examination_id);
                $invoice_message = $invoice ? ' Invoice telah dibuat.' : ' (Gagal membuat invoice)';
                
                echo json_encode([
                    'success' => true, 
                    'message' => 'Hasil pemeriksaan berhasil divalidasi.' . $invoice_message
                ]);
            } else {
                echo json_encode([
                    'success' => false, 
                    'message' => 'Gagal memvalidasi hasil. Silakan coba lagi.'
                ]);
            }
            
        } catch (Exception $e) {
            log_message('error', 'Error validating result: ' . $e->getMessage());
            log_message('error', 'Stack trace: ' . $e->getTraceAsString());
            
            echo json_encode([
                'success' => false, 
                'message' => 'Terjadi kesalahan sistem saat validasi: ' . $e->getMessage()
            ]);
        }
        
        exit; 
    }

    /**
     * Supervisor Dashboard
     */
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
}