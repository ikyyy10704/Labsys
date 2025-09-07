<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Laboratorium extends CI_Controller {

    public function __construct()
    {
        parent::__construct();
        
        // Check if user is logged in and has petugas_lab role
        if (!$this->session->userdata('logged_in') || $this->session->userdata('role') !== 'petugas_lab') {
            $this->session->set_flashdata('error', 'Akses ditolak. Anda tidak memiliki izin untuk mengakses halaman ini.');
            redirect('auth/login');
        }
        
        $this->load->model(['User_model', 'Laboratorium_model']);
        $this->load->library(['form_validation']);
        $this->load->helper(['form', 'url', 'date']);
    }

 public function dashboard()
{
    $data['title'] = 'Dashboard Laboratorium';
    
    try {
        // Get dashboard statistics dengan error handling
        $data['stats'] = $this->Laboratorium_model->get_lab_dashboard_stats();
        
        // Get performance statistics
        $data['performance'] = $this->Laboratorium_model->get_lab_performance_stats();
        
        // Get pending requests dengan limit untuk dashboard
        $filters = array('status' => 'pending');
        $data['pending_requests'] = $this->Laboratorium_model->get_incoming_requests_paginated($filters, 5, 0);
        
        // Get samples in progress
        $filters_progress = array('status' => 'progress');
        $data['samples_in_progress'] = $this->Laboratorium_model->get_samples_data_enhanced($filters_progress, 5, 0);
        
        // Get inventory alerts
        $data['inventory_alerts'] = $this->Laboratorium_model->get_inventory_alerts();
        
        // Get stats untuk sidebar
        $data['stats_sidebar'] = $this->_get_lab_stats_for_sidebar();
        
    } catch (Exception $e) {
        log_message('error', 'Error loading laboratorium dashboard: ' . $e->getMessage());
        $data = array_merge($data, $this->_get_default_lab_data());
    }
    
    $this->load->view('template/header', $data);
    $this->load->view('template/sidebar', $data);
    $this->load->view('laboratorium/index', $data);
    $this->load->view('template/footer');
}
   
    public function accept_request($examination_id)
    {
        try {
            $petugas_id = $this->Laboratorium_model->get_petugas_id_by_user_id($this->session->userdata('user_id'));
            
            if ($this->Laboratorium_model->accept_examination_request($examination_id, $petugas_id)) {
                $this->User_model->log_activity($this->session->userdata('user_id'), 'Lab request accepted', 'pemeriksaan_lab', $examination_id);
                $this->session->set_flashdata('success', 'Permintaan pemeriksaan berhasil diterima');
            } else {
                $this->session->set_flashdata('error', 'Gagal menerima permintaan');
            }
        } catch (Exception $e) {
            log_message('error', 'Error accepting request: ' . $e->getMessage());
            $this->session->set_flashdata('error', 'Terjadi kesalahan saat menerima permintaan');
        }
        
        redirect('laboratorium/incoming_requests');
    }
    public function input_results_form($examination_id)
    {
        $examination = $this->Laboratorium_model->get_examination_by_id($examination_id);
        
        if (!$examination || $examination['status_pemeriksaan'] !== 'progress') {
            $this->session->set_flashdata('error', 'Pemeriksaan tidak ditemukan atau tidak dalam status progress');
            redirect('laboratorium/input_results');
        }
        
        $data['title'] = 'Input Hasil: ' . $examination['jenis_pemeriksaan'];
        $data['examination'] = $examination;
        
        if ($this->input->method() === 'post') {
            $this->_handle_input_results_with_model($examination_id, $examination['jenis_pemeriksaan']);
        }
        
        $this->load->view('template/header', $data);
        $this->load->view('template/sidebar', $data);
        $this->load->view('laboratorium/input_results_form', $data);
        $this->load->view('template/footer');
    }


    /**
     * Inventory List
     */
    public function inventory_list()
    {
        $data['title'] = 'Inventori Laboratorium';
        
        try {
            $data['reagents'] = $this->Laboratorium_model->get_reagent_inventory();
            $data['equipment'] = $this->Laboratorium_model->get_equipment_inventory();
            $data['alerts'] = $this->Laboratorium_model->get_inventory_alerts();
        } catch (Exception $e) {
            log_message('error', 'Error getting inventory: ' . $e->getMessage());
            $data['reagents'] = array();
            $data['equipment'] = array();
            $data['alerts'] = array();
        }
        
        $this->load->view('template/header', $data);
        $this->load->view('template/sidebar', $data);
        $this->load->view('laboratorium/inventory_list', $data);
        $this->load->view('template/footer');
    }

    /**
     * Inventory Edit
     */
    public function inventory_edit()
    {
        $data['title'] = 'Edit Inventori';
        
        $type = $this->input->get('type') ?: 'reagen';
        
        try {
            if ($type === 'reagen') {
                $data['items'] = $this->_get_reagent_inventory();
            } else {
                $data['items'] = $this->_get_equipment_inventory();
            }
        } catch (Exception $e) {
            log_message('error', 'Error getting inventory for edit: ' . $e->getMessage());
            $data['items'] = array();
        }
        
        $data['type'] = $type;
        
        $this->load->view('template/header', $data);
        $this->load->view('template/sidebar', $data);
        $this->load->view('laboratorium/inventory_edit', $data);
        $this->load->view('template/footer');
    }

    /**
     * Update Reagent Stock
     */
    public function update_reagent_stock($reagent_id)
    {
        if ($this->input->method() === 'post') {
            $this->form_validation->set_rules('jumlah_stok', 'Jumlah Stok', 'required|numeric|greater_than_equal_to[0]');
            $this->form_validation->set_rules('expired_date', 'Tanggal Kadaluarsa', 'required');
            
            if ($this->form_validation->run() === TRUE) {
                $stock = $this->input->post('jumlah_stok');
                $expired_date = $this->input->post('expired_date');
                
                // Determine status based on stock level
                $this->db->select('stok_minimal');
                $this->db->where('reagen_id', $reagent_id);
                $reagent = $this->db->get('reagen')->row_array();
                
                $status = 'Tersedia';
                if ($stock <= $reagent['stok_minimal']) {
                    $status = 'Hampir Habis';
                } elseif (strtotime($expired_date) <= strtotime('+30 days')) {
                    $status = 'Kadaluarsa';
                }
                
                $update_data = array(
                    'jumlah_stok' => $stock,
                    'expired_date' => $expired_date,
                    'status' => $status,
                    'updated_at' => date('Y-m-d H:i:s')
                );
                
                $this->db->where('reagen_id', $reagent_id);
                if ($this->db->update('reagen', $update_data)) {
                    $this->User_model->log_activity($this->session->userdata('user_id'), 'Reagent stock updated', 'reagen', $reagent_id);
                    echo json_encode(['success' => true, 'message' => 'Stok berhasil diperbarui']);
                } else {
                    echo json_encode(['success' => false, 'message' => 'Gagal memperbarui stok']);
                }
            } else {
                echo json_encode(['success' => false, 'message' => validation_errors()]);
            }
        }
    }

    /**
     * Update Equipment Status
     */
    public function update_equipment_status($equipment_id)
    {
        if ($this->input->method() === 'post') {
            $this->form_validation->set_rules('status_alat', 'Status Alat', 'required|in_list[Normal,Perlu Kalibrasi,Rusak,Sedang Kalibrasi]');
            
            if ($this->form_validation->run() === TRUE) {
                $update_data = array(
                    'status_alat' => $this->input->post('status_alat'),
                    'riwayat_perbaikan' => $this->input->post('riwayat_perbaikan'),
                    'updated_at' => date('Y-m-d H:i:s')
                );
                
                if ($this->input->post('tanggal_kalibrasi_terakhir')) {
                    $update_data['tanggal_kalibrasi_terakhir'] = $this->input->post('tanggal_kalibrasi_terakhir');
                }
                
                if ($this->input->post('jadwal_kalibrasi')) {
                    $update_data['jadwal_kalibrasi'] = $this->input->post('jadwal_kalibrasi');
                }
                
                $this->db->where('alat_id', $equipment_id);
                if ($this->db->update('alat_laboratorium', $update_data)) {
                    $this->User_model->log_activity($this->session->userdata('user_id'), 'Equipment status updated', 'alat_laboratorium', $equipment_id);
                    echo json_encode(['success' => true, 'message' => 'Status alat berhasil diperbarui']);
                } else {
                    echo json_encode(['success' => false, 'message' => 'Gagal memperbarui status alat']);
                }
            } else {
                echo json_encode(['success' => false, 'message' => validation_errors()]);
            }
        }
    }
    private function _handle_input_results_with_model($examination_id, $examination_type)
    {
        $data = array(
            'pemeriksaan_id' => $examination_id,
            'created_at' => date('Y-m-d H:i:s')
        );
        
        $success = false;
        $result_type = '';
        
        // Based on examination type, save to appropriate result table using model
        switch (strtolower($examination_type)) {
            case 'kimia darah':
                $data = array_merge($data, array(
                    'gula_darah_sewaktu' => $this->input->post('gula_darah_sewaktu'),
                    'gula_darah_puasa' => $this->input->post('gula_darah_puasa'),
                    'gula_darah_2jam_pp' => $this->input->post('gula_darah_2jam_pp'),
                    'cholesterol_total' => $this->input->post('cholesterol_total'),
                    'cholesterol_hdl' => $this->input->post('cholesterol_hdl'),
                    'cholesterol_ldl' => $this->input->post('cholesterol_ldl'),
                    'trigliserida' => $this->input->post('trigliserida'),
                    'asam_urat' => $this->input->post('asam_urat'),
                    'ureum' => $this->input->post('ureum'),
                    'creatinin' => $this->input->post('creatinin'),
                    'sgpt' => $this->input->post('sgpt'),
                    'sgot' => $this->input->post('sgot')
                ));
                $success = $this->Laboratorium_model->save_kimia_darah_results($data);
                $result_type = 'kimia darah';
                break;
                
            case 'hematologi':
                $data = array_merge($data, array(
                    'hemoglobin' => $this->input->post('hemoglobin'),
                    'hematokrit' => $this->input->post('hematokrit'),
                    'laju_endap_darah' => $this->input->post('laju_endap_darah'),
                    'clotting_time' => $this->input->post('clotting_time'),
                    'bleeding_time' => $this->input->post('bleeding_time'),
                    'golongan_darah' => $this->input->post('golongan_darah'),
                    'rhesus' => $this->input->post('rhesus'),
                    'malaria' => $this->input->post('malaria')
                ));
                $success = $this->Laboratorium_model->save_hematologi_results($data);
                $result_type = 'hematologi';
                break;
                
            case 'urinologi':
                $data = array_merge($data, array(
                    'makroskopis' => $this->input->post('makroskopis'),
                    'mikroskopis' => $this->input->post('mikroskopis'),
                    'kimia_ph' => $this->input->post('kimia_ph'),
                    'protein' => $this->input->post('protein'),
                    'tes_kehamilan' => $this->input->post('tes_kehamilan')
                ));
                $success = $this->Laboratorium_model->save_urinologi_results($data);
                $result_type = 'urinologi';
                break;
                
            case 'serologi':
            case 'serologi imunologi':
                $data = array_merge($data, array(
                    'rdt_antigen' => $this->input->post('rdt_antigen'),
                    'widal' => $this->input->post('widal'),
                    'hbsag' => $this->input->post('hbsag'),
                    'ns1' => $this->input->post('ns1'),
                    'hiv' => $this->input->post('hiv')
                ));
                $success = $this->Laboratorium_model->save_serologi_results($data);
                $result_type = 'serologi';
                break;
                
            case 'tbc':
                $data = array_merge($data, array(
                    'dahak' => $this->input->post('dahak'),
                    'tcm' => $this->input->post('tcm')
                ));
                $success = $this->Laboratorium_model->save_tbc_results($data);
                $result_type = 'tbc';
                break;
                
            case 'ims':
                $data = array_merge($data, array(
                    'sifilis' => $this->input->post('sifilis'),
                    'duh_tubuh' => $this->input->post('duh_tubuh')
                ));
                $success = $this->Laboratorium_model->save_ims_results($data);
                $result_type = 'ims';
                break;
                
            default:
                $data = array_merge($data, array(
                    'jenis_tes' => $this->input->post('jenis_tes'),
                    'hasil' => $this->input->post('hasil'),
                    'nilai_rujukan' => $this->input->post('nilai_rujukan'),
                    'satuan' => $this->input->post('satuan'),
                    'metode' => $this->input->post('metode')
                ));
                $success = $this->Laboratorium_model->save_mls_results($data);
                $result_type = 'mls';
                break;
        }
        
        if ($success) {
            $this->User_model->log_activity($this->session->userdata('user_id'), "Lab results saved: {$result_type}", 'pemeriksaan_lab', $examination_id);
            $this->session->set_flashdata('success', 'Hasil pemeriksaan berhasil disimpan');
        } else {
            $this->session->set_flashdata('error', 'Gagal menyimpan hasil pemeriksaan');
        }
        
        redirect('laboratorium/input_results');
    }

    private function _get_default_lab_data()
    {
        return array(
            'stats' => array(
                'pending_requests' => 0,
                'samples_in_progress' => 0,
                'completed_today' => 0,
                'completed_this_month' => 0,
                'low_stock_items' => 0,
                'equipment_maintenance_due' => 0
            ),
            'performance' => array(
                'daily_completions' => array(),
                'test_distribution' => array(),
                'avg_processing_hours' => 0
            ),
            'pending_requests' => array(),
            'samples_in_progress' => array(),
            'inventory_alerts' => array()
        );
    }
    // Ganti method incoming_requests dan sample_data di Laboratorium.php dengan kode berikut:

/**
 * Incoming Lab Requests - Enhanced View
 */
public function incoming_requests()
{
    $data['title'] = 'Permintaan Pemeriksaan Masuk';
    
    // Pagination setup
    $limit = 10;
    $page = $this->input->get('page') ?: 1;
    $offset = ($page - 1) * $limit;
    
    // Get filters from URL
    $filters = array(
        'date_from' => $this->input->get('date_from'),
        'date_to' => $this->input->get('date_to'),
        'jenis_pemeriksaan' => $this->input->get('jenis_pemeriksaan'),
        'priority' => $this->input->get('priority'),
        'search' => $this->input->get('search')
    );
    
    try {
        // Get paginated data
        $data['requests'] = $this->Laboratorium_model->get_incoming_requests_paginated($filters, $limit, $offset);
        $data['total_requests'] = $this->Laboratorium_model->count_incoming_requests($filters);
        
        // Add priority information to each request
        foreach ($data['requests'] as &$request) {
            $request['priority_info'] = $this->Laboratorium_model->get_priority_level($request['hours_waiting']);
        }
        
        // Pagination info
        $data['current_page'] = $page;
        $data['total_pages'] = ceil($data['total_requests'] / $limit);
        $data['has_prev'] = $page > 1;
        $data['has_next'] = $page < $data['total_pages'];
        
        // Get examination types for filter
        $data['examination_types'] = $this->Laboratorium_model->get_examination_type_options();
        
    } catch (Exception $e) {
        log_message('error', 'Error getting incoming requests: ' . $e->getMessage());
        $data['requests'] = array();
        $data['total_requests'] = 0;
        $data['current_page'] = 1;
        $data['total_pages'] = 0;
        $data['has_prev'] = false;
        $data['has_next'] = false;
        $data['examination_types'] = array();
    }
    
    $data['filters'] = $filters;
    
    $this->load->view('template/header', $data);
    $this->load->view('template/sidebar', $data);
    $this->load->view('laboratorium/permintaan_masuk', $data);
    $this->load->view('template/footer');
}

/**
 * Sample Data / Specimen Tracking - Enhanced View
 */
public function sample_data()
{
    $data['title'] = 'Data Sampel / Pelacakan Spesimen';
    
    // Pagination setup
    $limit = 10;
    $page = $this->input->get('page') ?: 1;
    $offset = ($page - 1) * $limit;
    
    // Get filters from URL
    $filters = array(
        'status' => $this->input->get('status') ?: 'progress',
        'date_from' => $this->input->get('date_from'),
        'date_to' => $this->input->get('date_to'),
        'jenis_pemeriksaan' => $this->input->get('jenis_pemeriksaan'),
        'petugas_id' => $this->input->get('petugas_id'),
        'search' => $this->input->get('search')
    );
    
    try {
        // Get paginated data
        $data['samples'] = $this->Laboratorium_model->get_samples_data_enhanced($filters, $limit, $offset);
        $data['total_samples'] = $this->Laboratorium_model->count_samples_data($filters);
        
        // Add latest timeline status to each sample
        foreach ($data['samples'] as &$sample) {
            $sample['latest_status'] = $this->Laboratorium_model->get_latest_timeline_status($sample['pemeriksaan_id']);
        }
        
        // Pagination info
        $data['current_page'] = $page;
        $data['total_pages'] = ceil($data['total_samples'] / $limit);
        $data['has_prev'] = $page > 1;
        $data['has_next'] = $page < $data['total_pages'];
        
        // Get options for filters
        $data['examination_types'] = $this->Laboratorium_model->get_examination_type_options();
        $data['petugas_list'] = $this->Laboratorium_model->get_all_petugas_lab();
        $data['status_options'] = array(
            'progress' => 'Sedang Diproses',
            'selesai' => 'Selesai',
            'cancelled' => 'Dibatalkan'
        );
        
    } catch (Exception $e) {
        log_message('error', 'Error getting sample data: ' . $e->getMessage());
        $data['samples'] = array();
        $data['total_samples'] = 0;
        $data['current_page'] = 1;
        $data['total_pages'] = 0;
        $data['has_prev'] = false;
        $data['has_next'] = false;
        $data['examination_types'] = array();
        $data['petugas_list'] = array();
        $data['status_options'] = array();
    }
    
    $data['filters'] = $filters;
    
    $this->load->view('template/header', $data);
    $this->load->view('template/sidebar', $data);
    $this->load->view('laboratorium/sample_data', $data);
    $this->load->view('template/footer');
}

public function accept_multiple_requests()
{
    if ($this->input->method() === 'post') {
        $request_ids = $this->input->post('request_ids');
        
        if (empty($request_ids)) {
            echo json_encode(['success' => false, 'message' => 'Tidak ada permintaan yang dipilih']);
            return;
        }
        
        try {
            $petugas_id = $this->Laboratorium_model->get_petugas_id_by_user_id($this->session->userdata('user_id'));
            $success_count = 0;
            
            foreach ($request_ids as $examination_id) {
                if ($this->Laboratorium_model->accept_examination_request($examination_id, $petugas_id)) {
                    $this->User_model->log_activity($this->session->userdata('user_id'), 'Lab request accepted', 'pemeriksaan_lab', $examination_id);
                    $success_count++;
                }
            }
            
            echo json_encode([
                'success' => true, 
                'message' => "{$success_count} permintaan berhasil diterima"
            ]);
            
        } catch (Exception $e) {
            log_message('error', 'Error accepting multiple requests: ' . $e->getMessage());
            echo json_encode(['success' => false, 'message' => 'Terjadi kesalahan saat memproses permintaan']);
        }
    }
}

/**
 * Update sample status
 */
public function update_sample_status($examination_id)
{
    if ($this->input->method() === 'post') {
        $this->form_validation->set_rules('status', 'Status', 'required|in_list[progress,selesai,cancelled]');
        $this->form_validation->set_rules('keterangan', 'Keterangan', 'required');
        
        if ($this->form_validation->run() === TRUE) {
            $status = $this->input->post('status');
            $keterangan = $this->input->post('keterangan');
            
            try {
                $petugas_id = $this->Laboratorium_model->get_petugas_id_by_user_id($this->session->userdata('user_id'));
                
                // Update status
                if ($this->Laboratorium_model->update_sample_status($examination_id, $status, $keterangan)) {
                    // Add timeline entry
                    $status_label = array(
                        'progress' => 'Status Diperbarui',
                        'selesai' => 'Pemeriksaan Selesai',
                        'cancelled' => 'Pemeriksaan Dibatalkan'
                    );
                    
                    $this->Laboratorium_model->add_sample_timeline(
                        $examination_id,
                        $status_label[$status],
                        $keterangan,
                        $petugas_id
                    );
                    
                    $this->User_model->log_activity($this->session->userdata('user_id'), "Sample status updated to {$status}", 'pemeriksaan_lab', $examination_id);
                    echo json_encode(['success' => true, 'message' => 'Status sampel berhasil diperbarui']);
                } else {
                    echo json_encode(['success' => false, 'message' => 'Gagal memperbarui status sampel']);
                }
            } catch (Exception $e) {
                log_message('error', 'Error updating sample status: ' . $e->getMessage());
                echo json_encode(['success' => false, 'message' => 'Terjadi kesalahan saat memperbarui status']);
            }
        } else {
            echo json_encode(['success' => false, 'message' => validation_errors()]);
        }
    }
}

public function view_examination_detail($examination_id)
{
    $examination = $this->Laboratorium_model->get_examination_by_id($examination_id);
    
    if (!$examination) {
        $this->session->set_flashdata('error', 'Pemeriksaan tidak ditemukan');
        redirect('laboratorium/incoming_requests');
    }
    
    $data['title'] = 'Detail Pemeriksaan: ' . $examination['nomor_pemeriksaan'];
    $data['examination'] = $examination;
    
    $this->load->view('template/header', $data);
    $this->load->view('template/sidebar', $data);
    $this->load->view('laboratorium/examination_detail', $data);
    $this->load->view('template/footer');
}

/**
 * Get stats for sidebar notification
 */
private function _get_lab_stats_for_sidebar()
{
    try {
        $stats = array();
        
        // Pending requests count
        $this->db->where('status_pemeriksaan', 'pending');
        $stats['pending_requests'] = $this->db->count_all_results('pemeriksaan_lab');
        
        // Low stock items count
        $this->db->where('jumlah_stok <=', 'stok_minimal', FALSE);
        $stats['low_stock_items'] = $this->db->count_all_results('reagen');
        
        return $stats;
    } catch (Exception $e) {
        log_message('error', 'Error getting sidebar stats: ' . $e->getMessage());
        return array(
            'pending_requests' => 0,
            'low_stock_items' => 0
        );
    }
}

public function view_sample_timeline($examination_id)
{
    // Validasi examination ID
    if (!is_numeric($examination_id)) {
        $this->session->set_flashdata('error', 'ID pemeriksaan tidak valid');
        redirect('laboratorium/sample_data');
    }

    try {
        $examination = $this->Laboratorium_model->get_examination_by_id($examination_id);
        
        if (!$examination) {
            $this->session->set_flashdata('error', 'Pemeriksaan tidak ditemukan');
            redirect('laboratorium/sample_data');
        }
        
        $data['title'] = 'Timeline Sampel: ' . $examination['nomor_pemeriksaan'];
        $data['examination'] = $examination;
        $data['timeline'] = $this->Laboratorium_model->get_sample_timeline($examination_id);
        
        // Get timeline statistics
        $data['timeline_stats'] = $this->_get_timeline_stats($examination_id);
        
        $this->load->view('template/header', $data);
        $this->load->view('template/sidebar', $data);
        $this->load->view('laboratorium/sample_timeline', $data);
        $this->load->view('template/footer');
        
    } catch (Exception $e) {
        log_message('error', 'Error viewing sample timeline: ' . $e->getMessage());
        $this->session->set_flashdata('error', 'Terjadi kesalahan saat memuat timeline');
        redirect('laboratorium/sample_data');
    }
}

/**
 * Add Timeline Entry
 */
public function add_timeline_entry($examination_id)
{
    // Hanya terima POST request
    if ($this->input->method() !== 'post') {
        show_404();
        return;
    }
    
    // Validasi input
    $this->form_validation->set_rules('status', 'Status', 'required|max_length[100]');
    $this->form_validation->set_rules('keterangan', 'Keterangan', 'required|max_length[500]');
    $this->form_validation->set_rules('tanggal_update', 'Tanggal Update', 'valid_date');
    
    if ($this->form_validation->run() === FALSE) {
        echo json_encode([
            'success' => false, 
            'message' => strip_tags(validation_errors())
        ]);
        return;
    }
    
    try {
        // Validasi examination exists
        $examination = $this->Laboratorium_model->get_examination_by_id($examination_id);
        if (!$examination) {
            echo json_encode(['success' => false, 'message' => 'Pemeriksaan tidak ditemukan']);
            return;
        }
        
        // Get petugas ID
        $petugas_id = $this->Laboratorium_model->get_petugas_id_by_user_id($this->session->userdata('user_id'));
        if (!$petugas_id) {
            echo json_encode(['success' => false, 'message' => 'User tidak terdaftar sebagai petugas lab']);
            return;
        }
        
        // Prepare data
        $status = $this->input->post('status');
        $keterangan = $this->input->post('keterangan');
        $tanggal_update = $this->input->post('tanggal_update');
        
        // Jika tanggal tidak diisi, gunakan waktu sekarang
        if (empty($tanggal_update)) {
            $tanggal_update = date('Y-m-d H:i:s');
        } else {
            // Validasi format tanggal
            $datetime = DateTime::createFromFormat('Y-m-d\TH:i', $tanggal_update);
            if ($datetime) {
                $tanggal_update = $datetime->format('Y-m-d H:i:s');
            } else {
                $tanggal_update = date('Y-m-d H:i:s');
            }
        }
        
        // Insert timeline entry
        $timeline_data = array(
            'pemeriksaan_id' => $examination_id,
            'status' => $status,
            'keterangan' => $keterangan,
            'petugas_id' => $petugas_id,
            'tanggal_update' => $tanggal_update
        );
        
        if ($this->db->insert('timeline_progres', $timeline_data)) {
            // Log activity
            $this->User_model->log_activity(
                $this->session->userdata('user_id'), 
                'Timeline entry added: ' . $status, 
                'timeline_progres', 
                $examination_id
            );
            
            echo json_encode([
                'success' => true, 
                'message' => 'Timeline berhasil ditambahkan',
                'timeline_id' => $this->db->insert_id()
            ]);
        } else {
            echo json_encode(['success' => false, 'message' => 'Gagal menambahkan timeline ke database']);
        }
        
    } catch (Exception $e) {
        log_message('error', 'Error adding timeline entry: ' . $e->getMessage());
        echo json_encode(['success' => false, 'message' => 'Terjadi kesalahan sistem saat menambah timeline']);
    }
}

/**
 * Update Timeline Entry
 */
public function update_timeline_entry($timeline_id)
{
    if ($this->input->method() !== 'post') {
        show_404();
        return;
    }
    
    $this->form_validation->set_rules('status', 'Status', 'required|max_length[100]');
    $this->form_validation->set_rules('keterangan', 'Keterangan', 'required|max_length[500]');
    
    if ($this->form_validation->run() === FALSE) {
        echo json_encode(['success' => false, 'message' => strip_tags(validation_errors())]);
        return;
    }
    
    try {
        // Cek apakah timeline entry exists
        $this->db->where('timeline_id', $timeline_id);
        $timeline = $this->db->get('timeline_progres')->row_array();
        
        if (!$timeline) {
            echo json_encode(['success' => false, 'message' => 'Entry timeline tidak ditemukan']);
            return;
        }
        
        // Cek permission - hanya petugas yang membuat yang bisa edit
        $current_petugas_id = $this->Laboratorium_model->get_petugas_id_by_user_id($this->session->userdata('user_id'));
        if ($timeline['petugas_id'] != $current_petugas_id) {
            echo json_encode(['success' => false, 'message' => 'Anda tidak memiliki izin untuk mengedit entry ini']);
            return;
        }
        
        // Update data
        $update_data = array(
            'status' => $this->input->post('status'),
            'keterangan' => $this->input->post('keterangan')
        );
        
        $this->db->where('timeline_id', $timeline_id);
        if ($this->db->update('timeline_progres', $update_data)) {
            $this->User_model->log_activity(
                $this->session->userdata('user_id'), 
                'Timeline entry updated', 
                'timeline_progres', 
                $timeline_id
            );
            
            echo json_encode(['success' => true, 'message' => 'Timeline berhasil diperbarui']);
        } else {
            echo json_encode(['success' => false, 'message' => 'Gagal memperbarui timeline']);
        }
        
    } catch (Exception $e) {
        log_message('error', 'Error updating timeline entry: ' . $e->getMessage());
        echo json_encode(['success' => false, 'message' => 'Terjadi kesalahan saat memperbarui timeline']);
    }
}

/**
 * Delete Timeline Entry
 */
public function delete_timeline_entry($timeline_id)
{
    if ($this->input->method() !== 'post') {
        show_404();
        return;
    }
    
    try {
        // Cek apakah timeline entry exists
        $this->db->where('timeline_id', $timeline_id);
        $timeline = $this->db->get('timeline_progres')->row_array();
        
        if (!$timeline) {
            echo json_encode(['success' => false, 'message' => 'Entry timeline tidak ditemukan']);
            return;
        }
        
        // Cek permission - hanya petugas yang membuat yang bisa hapus
        $current_petugas_id = $this->Laboratorium_model->get_petugas_id_by_user_id($this->session->userdata('user_id'));
        if ($timeline['petugas_id'] != $current_petugas_id) {
            echo json_encode(['success' => false, 'message' => 'Anda tidak memiliki izin untuk menghapus entry ini']);
            return;
        }
        
        // Jangan hapus jika ini adalah satu-satunya entry
        $this->db->where('pemeriksaan_id', $timeline['pemeriksaan_id']);
        $total_entries = $this->db->count_all_results('timeline_progres');
        
        if ($total_entries <= 1) {
            echo json_encode(['success' => false, 'message' => 'Tidak dapat menghapus entry terakhir dalam timeline']);
            return;
        }
        
        // Delete entry
        $this->db->where('timeline_id', $timeline_id);
        if ($this->db->delete('timeline_progres')) {
            $this->User_model->log_activity(
                $this->session->userdata('user_id'), 
                'Timeline entry deleted', 
                'timeline_progres', 
                $timeline_id
            );
            
            echo json_encode(['success' => true, 'message' => 'Entry timeline berhasil dihapus']);
        } else {
            echo json_encode(['success' => false, 'message' => 'Gagal menghapus entry timeline']);
        }
        
    } catch (Exception $e) {
        log_message('error', 'Error deleting timeline entry: ' . $e->getMessage());
        echo json_encode(['success' => false, 'message' => 'Terjadi kesalahan saat menghapus timeline']);
    }
}

/**
 * Get Timeline Data (AJAX)
 */
public function get_timeline_data($examination_id)
{
    try {
        $timeline = $this->Laboratorium_model->get_sample_timeline($examination_id);
        $stats = $this->_get_timeline_stats($examination_id);
        
        echo json_encode([
            'success' => true,
            'timeline' => $timeline,
            'stats' => $stats
        ]);
        
    } catch (Exception $e) {
        log_message('error', 'Error getting timeline data: ' . $e->getMessage());
        echo json_encode(['success' => false, 'message' => 'Gagal memuat data timeline']);
    }
}

public function bulk_add_timeline()
{
    if ($this->input->method() !== 'post') {
        show_404();
        return;
    }
    
    $examination_ids = $this->input->post('examination_ids');
    $status = $this->input->post('status');
    $keterangan = $this->input->post('keterangan');
    
    if (empty($examination_ids) || empty($status) || empty($keterangan)) {
        echo json_encode(['success' => false, 'message' => 'Data tidak lengkap']);
        return;
    }
    
    try {
        $petugas_id = $this->Laboratorium_model->get_petugas_id_by_user_id($this->session->userdata('user_id'));
        $success_count = 0;
        $error_count = 0;
        
        foreach ($examination_ids as $examination_id) {
            $timeline_data = array(
                'pemeriksaan_id' => $examination_id,
                'status' => $status,
                'keterangan' => $keterangan,
                'petugas_id' => $petugas_id,
                'tanggal_update' => date('Y-m-d H:i:s')
            );
            
            if ($this->db->insert('timeline_progres', $timeline_data)) {
                $success_count++;
            } else {
                $error_count++;
            }
        }
        
        if ($success_count > 0) {
            $this->User_model->log_activity(
                $this->session->userdata('user_id'), 
                "Bulk timeline added: {$success_count} entries", 
                'timeline_progres', 
                null
            );
        }
        
        echo json_encode([
            'success' => true,
            'message' => "Berhasil menambahkan {$success_count} timeline" . ($error_count > 0 ? ", {$error_count} gagal" : ""),
            'success_count' => $success_count,
            'error_count' => $error_count
        ]);
        
    } catch (Exception $e) {
        log_message('error', 'Error bulk adding timeline: ' . $e->getMessage());
        echo json_encode(['success' => false, 'message' => 'Terjadi kesalahan saat menambah timeline']);
    }
}

/**
 * Get Timeline Templates (preset status)
 */
public function get_timeline_templates()
{
    $templates = array(
        'incoming' => array(
            array('status' => 'Sampel Diterima', 'keterangan' => 'Sampel diterima dalam kondisi baik dan siap untuk diproses'),
            array('status' => 'Verifikasi Identitas', 'keterangan' => 'Identitas pasien dan sampel telah diverifikasi sesuai'),
            array('status' => 'Registrasi Sampel', 'keterangan' => 'Sampel telah diregistrasi ke dalam sistem laboratorium')
        ),
        'processing' => array(
            array('status' => 'Preparasi Sampel', 'keterangan' => 'Sampel sedang dipreparasi untuk analisis'),
            array('status' => 'Quality Control', 'keterangan' => 'Menjalankan quality control instrumen dan reagen'),
            array('status' => 'Analisis Dimulai', 'keterangan' => 'Proses analisis laboratorium telah dimulai'),
            array('status' => 'Analisis Berlangsung', 'keterangan' => 'Analisis sedang berlangsung menggunakan metode standar'),
            array('status' => 'Review Hasil', 'keterangan' => 'Hasil analisis sedang direview oleh petugas senior')
        ),
        'completion' => array(
            array('status' => 'Analisis Selesai', 'keterangan' => 'Analisis laboratorium telah selesai dilakukan'),
            array('status' => 'Validasi Hasil', 'keterangan' => 'Hasil telah divalidasi dan memenuhi standar kualitas'),
            array('status' => 'Hasil Siap', 'keterangan' => 'Hasil pemeriksaan siap untuk diserahkan atau dikirim'),
            array('status' => 'Hasil Diserahkan', 'keterangan' => 'Hasil pemeriksaan telah diserahkan kepada pasien/dokter')
        ),
        'issues' => array(
            array('status' => 'Sampel Hemolisis', 'keterangan' => 'Sampel mengalami hemolisis, perlu pengambilan ulang'),
            array('status' => 'Sampel Lipemik', 'keterangan' => 'Sampel lipemik, mungkin mempengaruhi hasil'),
            array('status' => 'Volume Tidak Cukup', 'keterangan' => 'Volume sampel tidak mencukupi untuk analisis'),
            array('status' => 'Instrumen Error', 'keterangan' => 'Terjadi error pada instrumen, sedang diperbaiki'),
            array('status' => 'Perlu Pengulangan', 'keterangan' => 'Hasil perlu diulang karena hasil tidak konsisten')
        )
    );
    
    echo json_encode(['success' => true, 'templates' => $templates]);
}

private function _get_timeline_stats($examination_id)
{
    $stats = array();
    
    // Total entries
    $this->db->where('pemeriksaan_id', $examination_id);
    $stats['total_entries'] = $this->db->count_all_results('timeline_progres');
    
    // First and last entry
    $this->db->select('MIN(tanggal_update) as first_entry, MAX(tanggal_update) as last_entry');
    $this->db->where('pemeriksaan_id', $examination_id);
    $result = $this->db->get('timeline_progres')->row_array();
    
    $stats['first_entry'] = $result['first_entry'];
    $stats['last_entry'] = $result['last_entry'];
    
    // Time span
    if ($result['first_entry'] && $result['last_entry']) {
        $first = new DateTime($result['first_entry']);
        $last = new DateTime($result['last_entry']);
        $diff = $first->diff($last);
        $stats['time_span_hours'] = ($diff->days * 24) + $diff->h + ($diff->i / 60);
    } else {
        $stats['time_span_hours'] = 0;
    }
    
    // Entries by petugas
    $this->db->select('pt.nama_petugas, COUNT(*) as count');
    $this->db->from('timeline_progres tp');
    $this->db->join('petugas_lab pt', 'tp.petugas_id = pt.petugas_id', 'left');
    $this->db->where('tp.pemeriksaan_id', $examination_id);
    $this->db->group_by('tp.petugas_id');
    $stats['entries_by_petugas'] = $this->db->get()->result_array();
    
    return $stats;
}



public function get_examination_data($examination_id = null)
{
    if ($this->input->method() !== 'post' && !$examination_id) {
        echo json_encode(['success' => false, 'message' => 'Invalid request method']);
        return;
    }
    
    // Get examination_id from URL parameter or POST data
    if (!$examination_id) {
        $examination_id = $this->input->post('examination_id');
    }
    
    if (!$examination_id) {
        echo json_encode(['success' => false, 'message' => 'Examination ID required']);
        return;
    }
    
    try {
        $examination = $this->Laboratorium_model->get_examination_by_id($examination_id);
        
        if (!$examination || $examination['status_pemeriksaan'] !== 'progress') {
            echo json_encode(['success' => false, 'message' => 'Pemeriksaan tidak ditemukan atau tidak dalam status progress']);
            return;
        }
        
        // Check if current user is assigned to this examination
        $petugas_id = $this->Laboratorium_model->get_petugas_id_by_user_id($this->session->userdata('user_id'));
        if ($examination['petugas_id'] != $petugas_id) {
            echo json_encode(['success' => false, 'message' => 'Anda tidak memiliki akses untuk pemeriksaan ini']);
            return;
        }
        
        // Get existing results if any
        $existing_results = $this->Laboratorium_model->get_existing_results($examination_id, $examination['jenis_pemeriksaan']);
        
        echo json_encode([
            'success' => true,
            'examination' => $examination,
            'existing_results' => $existing_results
        ]);
        
    } catch (Exception $e) {
        log_message('error', 'Error getting examination data: ' . $e->getMessage());
        echo json_encode(['success' => false, 'message' => 'Terjadi kesalahan saat memuat data']);
    }
}

/**
 * Save examination results (AJAX) - Improved Version
 */
public function save_examination_results()
{
    if ($this->input->method() !== 'post') {
        echo json_encode(['success' => false, 'message' => 'Invalid request method']);
        return;
    }
    
    $examination_id = $this->input->post('examination_id');
    $result_type = $this->input->post('result_type');
    
    if (!$examination_id || !$result_type) {
        echo json_encode(['success' => false, 'message' => 'Data tidak lengkap']);
        return;
    }
    
    try {
        // Verify examination exists and user has access
        $examination = $this->Laboratorium_model->get_examination_by_id($examination_id);
        if (!$examination) {
            echo json_encode(['success' => false, 'message' => 'Pemeriksaan tidak ditemukan']);
            return;
        }
        
        $petugas_id = $this->Laboratorium_model->get_petugas_id_by_user_id($this->session->userdata('user_id'));
        if ($examination['petugas_id'] != $petugas_id) {
            echo json_encode(['success' => false, 'message' => 'Akses ditolak']);
            return;
        }
        
        // Prepare data
        $data = array(
            'pemeriksaan_id' => $examination_id,
            'created_at' => date('Y-m-d H:i:s')
        );
        
        $success = false;
        
        // Save based on result type
        switch (strtolower($result_type)) {
            case 'kimia_darah':
                $data = array_merge($data, $this->_get_kimia_darah_data());
                $success = $this->Laboratorium_model->save_or_update_kimia_darah_results($examination_id, $data);
                break;
                
            case 'hematologi':
                $data = array_merge($data, $this->_get_hematologi_data());
                $success = $this->Laboratorium_model->save_or_update_hematologi_results($examination_id, $data);
                break;
                
            case 'urinologi':
                $data = array_merge($data, $this->_get_urinologi_data());
                $success = $this->Laboratorium_model->save_or_update_urinologi_results($examination_id, $data);
                break;
                
            case 'serologi':
                $data = array_merge($data, $this->_get_serologi_data());
                $success = $this->Laboratorium_model->save_or_update_serologi_results($examination_id, $data);
                break;
                
            case 'tbc':
                $data = array_merge($data, $this->_get_tbc_data());
                $success = $this->Laboratorium_model->save_or_update_tbc_results($examination_id, $data);
                break;
                
            case 'ims':
                $data = array_merge($data, $this->_get_ims_data());
                $success = $this->Laboratorium_model->save_or_update_ims_results($examination_id, $data);
                break;
                
            case 'mls':
                $data = array_merge($data, $this->_get_mls_data());
                $success = $this->Laboratorium_model->save_or_update_mls_results($examination_id, $data);
                break;
                
            default:
                echo json_encode(['success' => false, 'message' => 'Jenis pemeriksaan tidak valid']);
                return;
        }
        
        if ($success) {
            // Add timeline entry
            $this->Laboratorium_model->add_sample_timeline(
                $examination_id,
                'Hasil Diinput',
                'Hasil pemeriksaan telah diinput dan siap untuk divalidasi',
                $petugas_id
            );
            
            $this->User_model->log_activity(
                $this->session->userdata('user_id'), 
                "Lab results saved: {$result_type}", 
                'pemeriksaan_lab', 
                $examination_id
            );
            
            echo json_encode(['success' => true, 'message' => 'Hasil berhasil disimpan']);
        } else {
            echo json_encode(['success' => false, 'message' => 'Gagal menyimpan hasil']);
        }
        
    } catch (Exception $e) {
        log_message('error', 'Error saving examination results: ' . $e->getMessage());
        echo json_encode(['success' => false, 'message' => 'Terjadi kesalahan saat menyimpan']);
    }
}

/**
 * Input results method - Updated to load dynamic data
 */
public function input_results()
{
    $data['title'] = 'Input Hasil Pemeriksaan';
    
    try {
        $petugas_id = $this->Laboratorium_model->get_petugas_id_by_user_id($this->session->userdata('user_id'));
        
        // Get examinations ready for results with additional info
        $data['ready_examinations'] = $this->Laboratorium_model->get_examinations_ready_for_results_enhanced($petugas_id);
        
        // Add debug information
        if (empty($data['ready_examinations'])) {
            log_message('info', 'No ready examinations found for petugas_id: ' . $petugas_id);
        } else {
            log_message('info', 'Found ' . count($data['ready_examinations']) . ' ready examinations');
        }
        
    } catch (Exception $e) {
        log_message('error', 'Error getting ready examinations: ' . $e->getMessage());
        $data['ready_examinations'] = array();
    }
    
    $this->load->view('template/header', $data);
    $this->load->view('template/sidebar', $data);
    $this->load->view('laboratorium/input_results', $data);
    $this->load->view('template/footer');
}

// Helper methods untuk extract data dari POST
private function _get_kimia_darah_data()
{
    return array(
        'gula_darah_sewaktu' => $this->input->post('gula_darah_sewaktu') ?: null,
        'gula_darah_puasa' => $this->input->post('gula_darah_puasa') ?: null,
        'gula_darah_2jam_pp' => $this->input->post('gula_darah_2jam_pp') ?: null,
        'cholesterol_total' => $this->input->post('cholesterol_total') ?: null,
        'cholesterol_hdl' => $this->input->post('cholesterol_hdl') ?: null,
        'cholesterol_ldl' => $this->input->post('cholesterol_ldl') ?: null,
        'trigliserida' => $this->input->post('trigliserida') ?: null,
        'asam_urat' => $this->input->post('asam_urat') ?: null,
        'ureum' => $this->input->post('ureum') ?: null,
        'creatinin' => $this->input->post('creatinin') ?: null,
        'sgpt' => $this->input->post('sgpt') ?: null,
        'sgot' => $this->input->post('sgot') ?: null
    );
}

private function _get_hematologi_data()
{
    return array(
        'hemoglobin' => $this->input->post('hemoglobin') ?: null,
        'hematokrit' => $this->input->post('hematokrit') ?: null,
        'laju_endap_darah' => $this->input->post('laju_endap_darah') ?: null,
        'clotting_time' => $this->input->post('clotting_time') ?: null,
        'bleeding_time' => $this->input->post('bleeding_time') ?: null,
        'golongan_darah' => $this->input->post('golongan_darah') ?: null,
        'rhesus' => $this->input->post('rhesus') ?: null,
        'malaria' => $this->input->post('malaria') ?: null
    );
}

private function _get_urinologi_data()
{
    return array(
        'makroskopis' => $this->input->post('makroskopis') ?: null,
        'mikroskopis' => $this->input->post('mikroskopis') ?: null,
        'kimia_ph' => $this->input->post('kimia_ph') ?: null,
        'protein' => $this->input->post('protein') ?: null,
        'tes_kehamilan' => $this->input->post('tes_kehamilan') ?: null
    );
}

private function _get_serologi_data()
{
    return array(
        'rdt_antigen' => $this->input->post('rdt_antigen') ?: null,
        'widal' => $this->input->post('widal') ?: null,
        'hbsag' => $this->input->post('hbsag') ?: null,
        'ns1' => $this->input->post('ns1') ?: null,
        'hiv' => $this->input->post('hiv') ?: null
    );
}

private function _get_tbc_data()
{
    return array(
        'dahak' => $this->input->post('dahak') ?: null,
        'tcm' => $this->input->post('tcm') ?: null
    );
}

private function _get_ims_data()
{
    return array(
        'sifilis' => $this->input->post('sifilis') ?: null,
        'duh_tubuh' => $this->input->post('duh_tubuh') ?: null
    );
}

private function _get_mls_data()
{
    return array(
        'jenis_tes' => $this->input->post('jenis_tes') ?: null,
        'hasil' => $this->input->post('hasil') ?: null,
        'nilai_rujukan' => $this->input->post('nilai_rujukan') ?: null,
        'satuan' => $this->input->post('satuan') ?: null,
        'metode' => $this->input->post('metode') ?: null
    );
}
public function quality_control()
{
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
    $this->load->view('laboratorium/quality_control', $data);
    $this->load->view('template/footer');
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
 * Validate Result - Enhanced with proper error handling
 */
public function validate_result($examination_id)
{
    if ($this->input->method() !== 'post') {
        echo json_encode(['success' => false, 'message' => 'Invalid request method']);
        return;
    }
    
    try {
        $petugas_id = $this->Laboratorium_model->get_petugas_id_by_user_id($this->session->userdata('user_id'));
        
        if (!$petugas_id) {
            echo json_encode(['success' => false, 'message' => 'User tidak terdaftar sebagai petugas lab']);
            return;
        }
        
        if ($this->Laboratorium_model->validate_examination_result_enhanced($examination_id, $petugas_id)) {
            $this->User_model->log_activity($this->session->userdata('user_id'), 'Examination result validated', 'pemeriksaan_lab', $examination_id);
            echo json_encode(['success' => true, 'message' => 'Hasil pemeriksaan berhasil divalidasi']);
        } else {
            echo json_encode(['success' => false, 'message' => 'Gagal memvalidasi hasil']);
        }
        
    } catch (Exception $e) {
        log_message('error', 'Error validating result: ' . $e->getMessage());
        echo json_encode(['success' => false, 'message' => 'Terjadi kesalahan saat validasi']);
    }
}

/**
 * Batch validate results
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
        $petugas_id = $this->Laboratorium_model->get_petugas_id_by_user_id($this->session->userdata('user_id'));
        
        if (!$petugas_id) {
            echo json_encode(['success' => false, 'message' => 'User tidak terdaftar sebagai petugas lab']);
            return;
        }
        
        $success_count = 0;
        $failed_count = 0;
        
        foreach ($examination_ids as $examination_id) {
            if ($this->Laboratorium_model->validate_examination_result_enhanced($examination_id, $petugas_id)) {
                $success_count++;
                $this->User_model->log_activity(
                    $this->session->userdata('user_id'), 
                    'Batch validation', 
                    'pemeriksaan_lab', 
                    $examination_id
                );
            } else {
                $failed_count++;
            }
        }
        
        $message = "Berhasil memvalidasi {$success_count} pemeriksaan";
        if ($failed_count > 0) {
            $message .= ", {$failed_count} gagal";
        }
        
        echo json_encode([
            'success' => true,
            'message' => $message,
            'success_count' => $success_count,
            'failed_count' => $failed_count
        ]);
        
    } catch (Exception $e) {
        log_message('error', 'Error in batch validation: ' . $e->getMessage());
        echo json_encode(['success' => false, 'message' => 'Terjadi kesalahan saat validasi batch']);
    }
}

/**
 * Get QC dashboard data
 */
public function get_qc_dashboard_data()
{
    try {
        $data = array(
            'pending_validation' => $this->Laboratorium_model->count_pending_validation(),
            'validated_today' => $this->Laboratorium_model->count_validated_today(),
            'validated_this_month' => $this->Laboratorium_model->count_validated_this_month(),
            'avg_validation_time' => $this->Laboratorium_model->get_avg_validation_time()
        );
        
        echo json_encode(['success' => true, 'data' => $data]);
        
    } catch (Exception $e) {
        log_message('error', 'Error getting QC dashboard data: ' . $e->getMessage());
        echo json_encode(['success' => false, 'message' => 'Gagal memuat data dashboard']);
    }
}
}