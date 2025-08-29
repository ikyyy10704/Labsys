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

    /**
     * Laboratory Dashboard
     */
    public function dashboard()
    {
        $data['title'] = 'Dashboard Laboratorium';
        
        try {
            // Get dashboard statistics
            $data['stats'] = $this->Laboratorium_model->get_lab_dashboard_stats();
            
            // Get performance statistics
            $data['performance'] = $this->Laboratorium_model->get_lab_performance_stats();
            
            // Get pending requests
            $data['pending_requests'] = $this->Laboratorium_model->get_pending_requests(5);
            
            // Get samples in progress
            $data['samples_in_progress'] = $this->Laboratorium_model->get_samples_by_status('progress');
            
            // Get inventory alerts
            $data['inventory_alerts'] = $this->Laboratorium_model->get_inventory_alerts();
            
            // Get recent activities (from User_model activity log)
            $data['recent_activities'] = $this->User_model->log_activity($this->session->userdata('user_id'), 'Dashboard accessed', 'system');
            
        } catch (Exception $e) {
            log_message('error', 'Error loading laboratorium dashboard: ' . $e->getMessage());
            $data = array_merge($data, $this->_get_default_lab_data());
        }
        
        $this->load->view('template/header', $data);
        $this->load->view('template/sidebar', $data);
        $this->load->view('laboratorium/index', $data);
        $this->load->view('template/footer');
    }

    /**
     * Incoming Lab Requests
     */
    public function incoming_requests()
    {
        $data['title'] = 'Permintaan Pemeriksaan Masuk';
        
        $filters = array(
            'status' => $this->input->get('status') ?: 'pending',
            'date' => $this->input->get('date') ?: date('Y-m-d'),
            'search' => $this->input->get('search')
        );
        
        try {
            $data['requests'] = $this->Laboratorium_model->get_lab_requests_by_filter($filters);
        } catch (Exception $e) {
            log_message('error', 'Error getting incoming requests: ' . $e->getMessage());
            $data['requests'] = array();
        }
        
        $data['filters'] = $filters;
        
        $this->load->view('template/header', $data);
        $this->load->view('template/sidebar', $data);
        $this->load->view('laboratorium/incoming_requests', $data);
        $this->load->view('template/footer');
    }

    /**
     * Accept Lab Request
     */
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

    /**
     * Sample Data / Specimen Tracking
     */
    public function sample_data()
    {
        $data['title'] = 'Data Sampel / Pelacakan Spesimen';
        
        $search = $this->input->get('search');
        $status = $this->input->get('status') ?: 'progress';
        
        try {
            $data['samples'] = $this->Laboratorium_model->get_samples_by_status($status, $search);
        } catch (Exception $e) {
            log_message('error', 'Error getting sample data: ' . $e->getMessage());
            $data['samples'] = array();
        }
        
        $data['search'] = $search;
        $data['status_filter'] = $status;
        
        $this->load->view('template/header', $data);
        $this->load->view('template/sidebar', $data);
        $this->load->view('laboratorium/sample_data', $data);
        $this->load->view('template/footer');
    }

    /**
     * Input Examination Results
     */
    public function input_results()
    {
        $data['title'] = 'Input Hasil Pemeriksaan';
        
        try {
            $petugas_id = $this->Laboratorium_model->get_petugas_id_by_user_id($this->session->userdata('user_id'));
            $data['ready_examinations'] = $this->Laboratorium_model->get_examinations_ready_for_results($petugas_id);
        } catch (Exception $e) {
            log_message('error', 'Error getting ready examinations: ' . $e->getMessage());
            $data['ready_examinations'] = array();
        }
        
        $this->load->view('template/header', $data);
        $this->load->view('template/sidebar', $data);
        $this->load->view('laboratorium/input_results', $data);
        $this->load->view('template/footer');
    }

    /**
     * Input Results Form
     */
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
     * Quality Control & Validation
     */
    public function quality_control()
    {
        $data['title'] = 'Validasi Hasil (Quality Control)';
        
        try {
            $data['pending_validation'] = $this->Laboratorium_model->get_results_pending_validation();
            $data['recent_validations'] = $this->Laboratorium_model->get_recent_validations();
        } catch (Exception $e) {
            log_message('error', 'Error getting QC data: ' . $e->getMessage());
            $data['pending_validation'] = array();
            $data['recent_validations'] = array();
        }
        
        $this->load->view('template/header', $data);
        $this->load->view('template/sidebar', $data);
        $this->load->view('laboratorium/quality_control', $data);
        $this->load->view('template/footer');
    }

    /**
     * Validate Result
     */
    public function validate_result($examination_id)
    {
        try {
            $petugas_id = $this->Laboratorium_model->get_petugas_id_by_user_id($this->session->userdata('user_id'));
            
            if ($this->Laboratorium_model->validate_examination_result($examination_id, $petugas_id)) {
                $this->User_model->log_activity($this->session->userdata('user_id'), 'Examination result validated', 'pemeriksaan_lab', $examination_id);
                $this->session->set_flashdata('success', 'Hasil pemeriksaan berhasil divalidasi');
            } else {
                $this->session->set_flashdata('error', 'Gagal memvalidasi hasil');
            }
        } catch (Exception $e) {
            log_message('error', 'Error validating result: ' . $e->getMessage());
            $this->session->set_flashdata('error', 'Terjadi kesalahan saat validasi');
        }
        
        redirect('laboratorium/quality_control');
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

    // =============================
    // PRIVATE METHODS
    // =============================

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
}