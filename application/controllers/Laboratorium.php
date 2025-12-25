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
        
        $this->load->model(['User_model', 'Laboratorium_model', 'Sample_inventory_model']);
        
        $this->load->library(['form_validation']);
        $this->load->helper(['form', 'url', 'date']);
    }
public function dashboard()
{
    $data['title'] = 'Dashboard Laboratorium';
    
    try {
        // 1. OVERVIEW STATS
        $data['stats'] = $this->Laboratorium_model->get_lab_dashboard_stats();
        
        // 2. INCOMING REQUESTS (5 terakhir)
        $filters = array('status' => 'pending');
        $data['pending_requests'] = $this->Laboratorium_model->get_incoming_requests_paginated($filters, 5, 0);
        
        // 3. SAMPLES IN PROGRESS (5 terakhir)
        $filters_progress = array('status' => 'progress');
        $data['samples_in_progress'] = $this->Laboratorium_model->get_samples_data_enhanced($filters_progress, 5, 0);
        
        // 4. VALIDATION PENDING
        $data['pending_validation'] = $this->Laboratorium_model->get_results_pending_validation_enhanced();
        
        // 5. RECENT VALIDATIONS
        $data['recent_validations'] = $this->Laboratorium_model->get_recent_validations_enhanced(5);
        
        // 6. INVENTORY ALERTS
        $data['inventory_alerts'] = $this->Laboratorium_model->get_inventory_alerts();
        
        // 7. PERFORMANCE STATS
        $data['performance'] = $this->Laboratorium_model->get_lab_performance_stats(30);
        
        // 8. QC STATS
        $data['qc_stats'] = $this->Laboratorium_model->get_qc_dashboard_stats();
        
        // 9. EXAMINATION STATUS DISTRIBUTION
        $data['status_distribution'] = $this->Laboratorium_model->get_examination_status_distribution();
        
        // 10. RECENT TIMELINE ACTIVITIES
        $petugas_id = $this->Laboratorium_model->get_petugas_id_by_user_id($this->session->userdata('user_id'));
        $data['recent_activities'] = $this->Laboratorium_model->get_recent_timeline_activities(10, $petugas_id);
        
    } catch (Exception $e) {
        log_message('error', 'Error loading laboratorium dashboard: ' . $e->getMessage());
        // Set default empty data
        $data['stats'] = array(
            'pending_requests' => 0,
            'samples_in_progress' => 0,
            'completed_today' => 0,
            'completed_this_month' => 0,
            'low_stock_items' => 0,
            'equipment_maintenance_due' => 0
        );
        $data['pending_requests'] = array();
        $data['samples_in_progress'] = array();
        $data['pending_validation'] = array();
        $data['recent_validations'] = array();
        $data['inventory_alerts'] = array();
        $data['performance'] = array();
        $data['qc_stats'] = array();
        $data['status_distribution'] = array();
        $data['recent_activities'] = array();
    }
    
    $this->load->view('template/header', $data);
    $this->load->view('template/sidebar', $data);
    $this->load->view('laboratorium/index', $data);
    $this->load->view('template/footer');
}
/**
 * Get examination detail for modal (AJAX)
 */
public function get_examination_detail($examination_id)
{
    try {
        $examination = $this->Laboratorium_model->get_examination_by_id($examination_id);
        
        if (!$examination) {
            echo json_encode(['success' => false, 'message' => 'Pemeriksaan tidak ditemukan']);
            return;
        }
        
        // Add priority level calculation
        $hours_waiting = 0;
        if ($examination['tanggal_pemeriksaan']) {
            $hours_waiting = round((time() - strtotime($examination['tanggal_pemeriksaan'])) / 3600, 1);
        }
        $examination['hours_waiting'] = $hours_waiting;
        $examination['priority_level'] = $this->_calculate_priority_level($hours_waiting);
        
        echo json_encode([
            'success' => true,
            'examination' => $examination
        ]);
        
    } catch (Exception $e) {
        log_message('error', 'Error getting examination detail: ' . $e->getMessage());
        echo json_encode(['success' => false, 'message' => 'Terjadi kesalahan saat memuat data']);
    }
}

private function _calculate_priority_level($hours_waiting)
{
    if ($hours_waiting >= 48) return 'urgent';
    if ($hours_waiting >= 24) return 'high';
    return 'normal';
}
// AJAX untuk chart data
public function ajax_get_completion_trend()
{
    $this->output->set_content_type('application/json');
    
    try {
        $performance = $this->Laboratorium_model->get_lab_performance_stats(7);
        $data = $performance['daily_completions'] ?? array();
        
        echo json_encode(['success' => true, 'data' => $data]);
    } catch (Exception $e) {
        echo json_encode(['success' => false, 'message' => $e->getMessage()]);
    }
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

public function get_sample_timeline_data($examination_id)
{
    try {
        $examination = $this->Laboratorium_model->get_examination_by_id($examination_id);
        
        if (!$examination) {
            echo json_encode(['success' => false, 'message' => 'Pemeriksaan tidak ditemukan']);
            return;
        }
        
        $timeline = $this->Laboratorium_model->get_sample_timeline($examination_id);
        
        echo json_encode([
            'success' => true,
            'examination' => $examination,
            'timeline' => $timeline
        ]);
        
    } catch (Exception $e) {
        log_message('error', 'Error getting timeline data: ' . $e->getMessage());
        echo json_encode(['success' => false, 'message' => 'Terjadi kesalahan saat memuat timeline']);
    }
}
    public function update_reagent_stock($reagent_id)
    {
        if ($this->input->method() === 'post') {
            $this->form_validation->set_rules('jumlah_stok', 'Jumlah Stok', 'required|numeric|greater_than_equal_to[0]');
            $this->form_validation->set_rules('expired_date', 'Tanggal Kadaluarsa', 'required');
            
            if ($this->form_validation->run() === TRUE) {
                $stock = $this->input->post('jumlah_stok');
                $expired_date = $this->input->post('expired_date');
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
                    'malaria' => $this->input->post('malaria'),
                    'leukosit' => $this->input->post('leukosit'),
                    'trombosit' => $this->input->post('trombosit'),
                    'eritrosit' => $this->input->post('eritrosit'),
                    'mcv' => $this->input->post('mcv'),
                    'mch' => $this->input->post('mch'),
                    'mchc' => $this->input->post('mchc'),
                    'eosinofil' => $this->input->post('eosinofil'),
                    'basofil' => $this->input->post('basofil'),
                    'neutrofil' => $this->input->post('neutrofil'),
                    'limfosit' => $this->input->post('limfosit'),
                    'monosit' => $this->input->post('monosit')
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
                    'tes_kehamilan' => $this->input->post('tes_kehamilan'),
                    'berat_jenis' => $this->input->post('berat_jenis'),
                    'glukosa' => $this->input->post('glukosa'),
                    'keton' => $this->input->post('keton') ,
                    'bilirubin' => $this->input->post('bilirubin'),
                    'urobilinogen' => $this->input->post('urobilinogen')
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
        
        // Check if this is a multiple examination
        $this->db->where('pemeriksaan_id', $examination_id);
        $examination_details = $this->db->get('pemeriksaan_detail')->result_array();
        $is_multiple = count($examination_details) > 1;
        
        // Ensure sub_pemeriksaan is available - ambil dari detail jika kosong di main
        if (empty($examination['sub_pemeriksaan']) && count($examination_details) == 1) {
            // Untuk single examination, ambil sub_pemeriksaan dari detail
            if (!empty($examination_details[0]['sub_pemeriksaan'])) {
                $examination['sub_pemeriksaan'] = $examination_details[0]['sub_pemeriksaan'];
            }
        }
        
        if (empty($examination['sub_pemeriksaan'])) {
            $examination['sub_pemeriksaan'] = null;
        }
        
        echo json_encode([
            'success' => true,
            'examination' => $examination,
            'existing_results' => $existing_results,
            'is_multiple' => $is_multiple,
            'examination_details' => $examination_details
        ]);
        
    } catch (Exception $e) {
        log_message('error', 'Error getting examination data: ' . $e->getMessage());
        echo json_encode(['success' => false, 'message' => 'Terjadi kesalahan saat memuat data']);
    }
}
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
    $this->load->view('laboratorium/quality_control', $data);
    $this->load->view('template/footer');
}
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

public function get_inventory_data()
{
    try {
        // Get filters from request
        $filters = array(
            'type' => $this->input->get('type'),
            'status' => $this->input->get('status'), 
            'location' => $this->input->get('location'),
            'alert' => $this->input->get('alert'),
            'search' => $this->input->get('search')
        );
        
        // Get reagent inventory
        $reagents = $this->Laboratorium_model->get_reagent_inventory($filters);
        
        // Get equipment inventory  
        $equipment = $this->Laboratorium_model->get_equipment_inventory($filters);
        
        // Combine and format data
        $inventory = array();
        
        // Process reagents
        foreach ($reagents as $reagent) {
            $days_to_expiry = null;
            if ($reagent['expired_date']) {
                $days_to_expiry = ceil((strtotime($reagent['expired_date']) - time()) / (60 * 60 * 24));
            }
            
            $alert_level = 'OK';
            if ($reagent['status'] == 'Kadaluarsa') {
                $alert_level = 'Urgent';
            } elseif ($reagent['jumlah_stok'] <= $reagent['stok_minimal']) {
                $alert_level = 'Low Stock';
            } elseif ($days_to_expiry !== null && $days_to_expiry <= 30) {
                $alert_level = 'Warning';
            }
            
            $inventory[] = array(
                'id' => $reagent['reagen_id'],
                'type' => 'reagen',
                'nama' => $reagent['nama_reagen'],
                'kode' => $reagent['kode_unik'] ?: 'REA' . str_pad($reagent['reagen_id'], 3, '0', STR_PAD_LEFT),
                'status' => $reagent['status'],
                'stock_info' => $reagent['jumlah_stok'] . ' ' . $reagent['satuan'],
                'location' => $reagent['lokasi_penyimpanan'],
                'alert_level' => $alert_level,
                'expired_date' => $reagent['expired_date'],
                'expired_days' => $days_to_expiry,
                'stok_minimal' => $reagent['stok_minimal'],
                'satuan' => $reagent['satuan']
            );
        }
        
        // Process equipment
        foreach ($equipment as $item) {
            $days_to_calibration = null;
            if ($item['jadwal_kalibrasi']) {
                $days_to_calibration = ceil((strtotime($item['jadwal_kalibrasi']) - time()) / (60 * 60 * 24));
            }
            
            $alert_level = 'OK';
            if ($item['status_alat'] == 'Rusak') {
                $alert_level = 'Urgent';
            } elseif ($item['status_alat'] == 'Perlu Kalibrasi' || ($days_to_calibration !== null && $days_to_calibration <= 0)) {
                $alert_level = 'Calibration Due';
            } elseif ($days_to_calibration !== null && $days_to_calibration <= 30) {
                $alert_level = 'Warning';
            }
            
            $inventory[] = array(
                'id' => $item['alat_id'],
                'type' => 'alat',
                'nama' => $item['nama_alat'],
                'kode' => $item['kode_unik'] ?: 'ALT' . str_pad($item['alat_id'], 3, '0', STR_PAD_LEFT),
                'status' => $item['status_alat'],
                'stock_info' => $item['merek_model'] ?: 'N/A',
                'location' => $item['lokasi'],
                'alert_level' => $alert_level,
                'jadwal_kalibrasi' => $item['jadwal_kalibrasi'],
                'calibration_days' => $days_to_calibration,
                'tanggal_kalibrasi_terakhir' => $item['tanggal_kalibrasi_terakhir'],
                'riwayat_perbaikan' => $item['riwayat_perbaikan']
            );
        }
        
        // Get statistics
        $stats = array(
            'total_alat' => count($equipment),
            'total_reagen' => count($reagents),
            'total_alerts' => count(array_filter($inventory, function($item) {
                return in_array($item['alert_level'], ['Warning', 'Urgent', 'Low Stock', 'Calibration Due']);
            })),
            'calibration_due' => count(array_filter($inventory, function($item) {
                return $item['alert_level'] == 'Calibration Due';
            }))
        );
        
        echo json_encode([
            'success' => true,
            'data' => $inventory,
            'stats' => $stats
        ]);
        
    } catch (Exception $e) {
        log_message('error', 'Error getting inventory data: ' . $e->getMessage());
        echo json_encode(['success' => false, 'message' => 'Gagal memuat data inventory']);
    }
}

/**
 * Create new inventory item
 */
public function create_inventory_item()
{
    if ($this->input->method() !== 'post') {
        echo json_encode(['success' => false, 'message' => 'Invalid request method']);
        return;
    }
    
    $type = $this->input->post('item_type');
    $nama = $this->input->post('nama_item');
    
    if (!$type || !$nama) {
        echo json_encode(['success' => false, 'message' => 'Data tidak lengkap']);
        return;
    }
    
    try {
        $success = false;
        $item_id = null;
        
        if ($type === 'alat') {
            // Create equipment
            $data = array(
                'nama_alat' => $nama,
                'kode_unik' => $this->input->post('kode_unik'),
                'merek_model' => $this->input->post('merek_model'),
                'lokasi' => $this->input->post('lokasi'),
                'status_alat' => $this->input->post('status_alat') ?: 'Normal',
                'jadwal_kalibrasi' => $this->input->post('jadwal_kalibrasi'),
                'tanggal_kalibrasi_terakhir' => $this->input->post('tanggal_kalibrasi_terakhir'),
                'riwayat_perbaikan' => $this->input->post('riwayat_perbaikan')
            );
            
            $success = $this->Laboratorium_model->create_equipment($data);
            if ($success) {
                $item_id = $this->db->insert_id();
                $this->User_model->log_activity(
                    $this->session->userdata('user_id'), 
                    'Item inventory baru ditambahkan: ' . $nama, 
                    'alat_laboratorium', 
                    $item_id
                );
            }
            
        } elseif ($type === 'reagen') {
            // Create reagent
            $data = array(
                'nama_reagen' => $nama,
                'kode_unik' => $this->input->post('kode_unik'),
                'jumlah_stok' => $this->input->post('jumlah_stok') ?: 0,
                'satuan' => $this->input->post('satuan'),
                'lokasi_penyimpanan' => $this->input->post('lokasi_penyimpanan'),
                'expired_date' => $this->input->post('expired_date'),
                'stok_minimal' => $this->input->post('stok_minimal') ?: 10,
                'status' => $this->input->post('status') ?: 'Tersedia',
                'catatan' => $this->input->post('catatan')
            );
            
            $success = $this->Laboratorium_model->create_reagent($data);
            if ($success) {
                $item_id = $this->db->insert_id();
                $this->User_model->log_activity(
                    $this->session->userdata('user_id'), 
                    'Item inventory baru ditambahkan: ' . $nama, 
                    'reagen', 
                    $item_id
                );
            }
        }
        
        if ($success) {
            echo json_encode(['success' => true, 'message' => 'Item berhasil ditambahkan', 'item_id' => $item_id]);
        } else {
            echo json_encode(['success' => false, 'message' => 'Gagal menambahkan item']);
        }
        
    } catch (Exception $e) {
        log_message('error', 'Error creating inventory item: ' . $e->getMessage());
        echo json_encode(['success' => false, 'message' => 'Terjadi kesalahan saat menambah item']);
    }
}
public function get_inventory_item($type, $id)
{
    try {
        $item = null;
        
        if ($type === 'alat') {
            $item = $this->Laboratorium_model->get_equipment_by_id($id);
            if ($item) {
                $item['item_type'] = 'alat';
                $item['nama_item'] = $item['nama_alat'];
            }
        } elseif ($type === 'reagen') {
            $item = $this->Laboratorium_model->get_reagent_by_id($id);
            if ($item) {
                $item['item_type'] = 'reagen';
                $item['nama_item'] = $item['nama_reagen'];
            }
        }
        
        if ($item) {
            echo json_encode(['success' => true, 'data' => $item]);
        } else {
            echo json_encode(['success' => false, 'message' => 'Item tidak ditemukan']);
        }
        
    } catch (Exception $e) {
        log_message('error', 'Error getting inventory item: ' . $e->getMessage());
        echo json_encode(['success' => false, 'message' => 'Gagal memuat data item']);
    }
}

/**
 * Update inventory item
 */
public function update_inventory_item()
{
    if ($this->input->method() !== 'post') {
        echo json_encode(['success' => false, 'message' => 'Invalid request method']);
        return;
    }
    
    $type = $this->input->post('item_type');
    $id = $this->input->post('item_id');
    $nama = $this->input->post('nama_item');
    
    if (!$type || !$id || !$nama) {
        echo json_encode(['success' => false, 'message' => 'Data tidak lengkap']);
        return;
    }
    
    try {
        $success = false;
        
        if ($type === 'alat') {
            // Update equipment
            $data = array(
                'nama_alat' => $nama,
                'kode_unik' => $this->input->post('kode_unik'),
                'merek_model' => $this->input->post('merek_model'),
                'lokasi' => $this->input->post('lokasi'),
                'status_alat' => $this->input->post('status_alat') ?: 'Normal',
                'jadwal_kalibrasi' => $this->input->post('jadwal_kalibrasi'),
                'tanggal_kalibrasi_terakhir' => $this->input->post('tanggal_kalibrasi_terakhir'),
                'riwayat_perbaikan' => $this->input->post('riwayat_perbaikan')
            );
            
            $success = $this->Laboratorium_model->update_equipment($id, $data);
            $table = 'alat_laboratorium';
            
        } elseif ($type === 'reagen') {
            // Update reagent
            $data = array(
                'nama_reagen' => $nama,
                'kode_unik' => $this->input->post('kode_unik'),
                'jumlah_stok' => $this->input->post('jumlah_stok') ?: 0,
                'satuan' => $this->input->post('satuan'),
                'lokasi_penyimpanan' => $this->input->post('lokasi_penyimpanan'),
                'expired_date' => $this->input->post('expired_date'),
                'stok_minimal' => $this->input->post('stok_minimal') ?: 10,
                'status' => $this->input->post('status') ?: 'Tersedia',
                'catatan' => $this->input->post('catatan')
            );
            
            $success = $this->Laboratorium_model->update_reagent($id, $data);
            $table = 'reagen';
        }
        
        if ($success) {
            $this->User_model->log_activity(
                $this->session->userdata('user_id'), 
                'Item inventory diperbarui: ' . $nama, 
                $table, 
                $id
            );
            echo json_encode(['success' => true, 'message' => 'Item berhasil diperbarui']);
        } else {
            echo json_encode(['success' => false, 'message' => 'Gagal memperbarui item']);
        }
        
    } catch (Exception $e) {
        log_message('error', 'Error updating inventory item: ' . $e->getMessage());
        echo json_encode(['success' => false, 'message' => 'Terjadi kesalahan saat memperbarui item']);
    }
}

/**
 * Delete inventory item
 */
public function delete_inventory_item($type, $id)
{
    if ($this->input->method() !== 'post') {
        echo json_encode(['success' => false, 'message' => 'Invalid request method']);
        return;
    }
    
    try {
        $success = false;
        $item_name = '';
        $table = '';
        
        if ($type === 'alat') {
            // Get item name first
            $item = $this->Laboratorium_model->get_equipment_by_id($id);
            $item_name = $item ? $item['nama_alat'] : 'Unknown';
            
            $this->db->where('alat_id', $id);
            $success = $this->db->delete('alat_laboratorium');
            $table = 'alat_laboratorium';
            
        } elseif ($type === 'reagen') {
            // Get item name first
            $item = $this->Laboratorium_model->get_reagent_by_id($id);
            $item_name = $item ? $item['nama_reagen'] : 'Unknown';
            
            $this->db->where('reagen_id', $id);
            $success = $this->db->delete('reagen');
            $table = 'reagen';
        }
        
        if ($success) {
            $this->User_model->log_activity(
                $this->session->userdata('user_id'), 
                'Item inventory dihapus: ' . $item_name, 
                $table, 
                $id
            );
            echo json_encode(['success' => true, 'message' => 'Item berhasil dihapus']);
        } else {
            echo json_encode(['success' => false, 'message' => 'Gagal menghapus item']);
        }
        
    } catch (Exception $e) {
        log_message('error', 'Error deleting inventory item: ' . $e->getMessage());
        echo json_encode(['success' => false, 'message' => 'Terjadi kesalahan saat menghapus item']);
    }
}

/**
 * Save calibration data
 */
public function save_calibration()
{
    if ($this->input->method() !== 'post') {
        echo json_encode(['success' => false, 'message' => 'Invalid request method']);
        return;
    }
    
    $this->form_validation->set_rules('item_id', 'Item ID', 'required|numeric');
    $this->form_validation->set_rules('tanggal_kalibrasi', 'Tanggal Kalibrasi', 'required');
    $this->form_validation->set_rules('hasil_kalibrasi', 'Hasil Kalibrasi', 'required|in_list[Passed,Failed,Conditional]');
    
    if ($this->form_validation->run() === FALSE) {
        echo json_encode(['success' => false, 'message' => strip_tags(validation_errors())]);
        return;
    }
    
    try {
        $item_id = $this->input->post('item_id');
        $tanggal_kalibrasi = $this->input->post('tanggal_kalibrasi');
        $hasil_kalibrasi = $this->input->post('hasil_kalibrasi');
        $teknisi = $this->input->post('teknisi');
        $catatan = $this->input->post('catatan');
        $next_calibration = $this->input->post('next_calibration_date');
        
        // Start transaction
        $this->db->trans_start();
        
        // Save to calibration history
        $calibration_data = array(
            'alat_id' => $item_id,
            'tanggal_kalibrasi' => $tanggal_kalibrasi,
            'hasil_kalibrasi' => $hasil_kalibrasi,
            'teknisi' => $teknisi,
            'next_calibration_date' => $next_calibration,
            'status' => $hasil_kalibrasi,
            'catatan' => $catatan,
            'user_id' => $this->Laboratorium_model->get_petugas_id_by_user_id($this->session->userdata('user_id'))
        );
        
        $this->db->insert('calibration_history', $calibration_data);
        
        // Update equipment status
        $equipment_update = array(
            'tanggal_kalibrasi_terakhir' => $tanggal_kalibrasi,
            'status_alat' => $hasil_kalibrasi === 'Passed' ? 'Normal' : 'Perlu Kalibrasi'
        );
        
        if ($next_calibration) {
            $equipment_update['jadwal_kalibrasi'] = $next_calibration;
        }
        
        $this->db->where('alat_id', $item_id);
        $this->db->update('alat_laboratorium', $equipment_update);
        
        $this->db->trans_complete();
        
        if ($this->db->trans_status() === FALSE) {
            echo json_encode(['success' => false, 'message' => 'Gagal menyimpan data kalibrasi']);
        } else {
            $this->User_model->log_activity(
                $this->session->userdata('user_id'), 
                'Equipment calibration saved: ' . $hasil_kalibrasi, 
                'alat_laboratorium', 
                $item_id
            );
            echo json_encode(['success' => true, 'message' => 'Kalibrasi berhasil disimpan']);
        }
        
    } catch (Exception $e) {
        $this->db->trans_rollback();
        log_message('error', 'Error saving calibration: ' . $e->getMessage());
        echo json_encode(['success' => false, 'message' => 'Terjadi kesalahan saat menyimpan kalibrasi']);
    }
}

/**
 * Get calibration schedule
 */
public function get_calibration_schedule()
{
    try {
        $this->db->select('al.*, ch.tanggal_kalibrasi as last_calibration, 
                          DATEDIFF(al.jadwal_kalibrasi, CURDATE()) as days_until_calibration');
        $this->db->from('alat_laboratorium al');
        $this->db->join('calibration_history ch', 'al.alat_id = ch.alat_id AND ch.tanggal_kalibrasi = al.tanggal_kalibrasi_terakhir', 'left');
        $this->db->where('al.jadwal_kalibrasi IS NOT NULL');
        $this->db->order_by('al.jadwal_kalibrasi', 'ASC');
        
        $schedule = $this->db->get()->result_array();
        
        // Categorize by status
        $categorized = array(
            'overdue' => array(),
            'due_soon' => array(),
            'up_to_date' => array()
        );
        
        foreach ($schedule as $item) {
            $days = $item['days_until_calibration'];
            
            if ($days < 0) {
                $item['status'] = 'overdue';
                $item['days_label'] = 'Overdue (' . abs($days) . ' hari)';
                $categorized['overdue'][] = $item;
            } elseif ($days <= 30) {
                $item['status'] = 'due_soon';
                $item['days_label'] = 'Due in ' . $days . ' days';
                $categorized['due_soon'][] = $item;
            } else {
                $item['status'] = 'up_to_date';
                $item['days_label'] = 'Due in ' . $days . ' days';
                $categorized['up_to_date'][] = $item;
            }
        }
        
        $stats = array(
            'overdue' => count($categorized['overdue']),
            'due_soon' => count($categorized['due_soon']),
            'up_to_date' => count($categorized['up_to_date'])
        );
        
        echo json_encode([
            'success' => true, 
            'data' => $categorized,
            'stats' => $stats,
            'all' => $schedule
        ]);
        
    } catch (Exception $e) {
        log_message('error', 'Error getting calibration schedule: ' . $e->getMessage());
        echo json_encode(['success' => false, 'message' => 'Gagal memuat jadwal kalibrasi']);
    }
}

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
        $invoice_created = 0;
        
        foreach ($examination_ids as $examination_id) {
            if ($this->Laboratorium_model->validate_examination_result_enhanced($examination_id, $petugas_id)) {
                $success_count++;
                $invoice = $this->Laboratorium_model->get_invoice_by_examination($examination_id);
                if ($invoice) {
                    $invoice_created++;
                }
                
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
        $examination = $this->Laboratorium_model->get_examination_by_id($examination_id);
        if (!$examination) {
            echo json_encode(['success' => false, 'message' => 'Pemeriksaan tidak ditemukan']);
            exit;
        }
        if ($examination['status_pemeriksaan'] === 'selesai') {
            $existing_invoice = $this->Laboratorium_model->get_invoice_by_examination($examination_id);
            $invoice_message = $existing_invoice ? ' Invoice sudah ada.' : '';
            
            echo json_encode([
                'success' => false, 
                'message' => 'Pemeriksaan sudah divalidasi sebelumnya.' . $invoice_message
            ]);
            exit;
        }
        log_message('info', "Starting validation for examination: {$examination_id} by user: {$petugas_id}");
        if ($this->Laboratorium_model->validate_examination_result_enhanced($examination_id, $petugas_id)) {
            
            $this->User_model->log_activity($this->session->userdata('user_id'), 'Examination result validated', 'pemeriksaan_lab', $examination_id);
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
public function get_result_details($examination_id = null)
{
    // Get from URL or POST
    if ($examination_id === null) {
        $examination_id = $this->input->get('id') ?: $this->input->post('id');
    }
    
    @ob_clean();
    header('Content-Type: application/json; charset=utf-8');
    
    if (!$examination_id) {
        echo json_encode(['success' => false, 'message' => 'ID required']);
        exit;
    }
    
    try {
        $examination = $this->Laboratorium_model->get_examination_by_id($examination_id);
        
        if (!$examination) {
            echo json_encode(['success' => false, 'message' => 'Not found']);
            exit;
        }
        
        $jenis = $examination['jenis_pemeriksaan'];
        $results = $this->Laboratorium_model->get_existing_results($examination_id, $jenis);
        
        if ($results) {
            $formatted = $this->_format_results_for_display($results, $jenis);
        } else {
            $formatted = array();
        }
        
        echo json_encode([
            'success' => true,
            'examination' => $examination,
            'results' => $formatted,
            'is_multiple' => false
        ]);
        
    } catch (Exception $e) {
        echo json_encode(['success' => false, 'message' => $e->getMessage()]);
    }
    
    exit;
}

private function _format_results_for_display($results, $jenis)
{
    $formatted = array();
    
    foreach ($results as $key => $value) {
        if ($value !== null && $value !== '') {
            $label = ucwords(str_replace('_', ' ', $key));
            $formatted[$label] = $value;
        }
    }
    
    return $formatted;
}
}