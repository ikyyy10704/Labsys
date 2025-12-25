<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Quality_control extends CI_Controller {

    public function __construct()
    {
        parent::__construct();
        
        // Check if user is logged in and has petugas_lab role
        if (!$this->session->userdata('logged_in') || $this->session->userdata('role') !== 'petugas_lab') {
            $this->session->set_flashdata('error', 'Akses ditolak. Anda tidak memiliki izin untuk mengakses halaman ini.');
            redirect('auth/login');
        }
        
        $this->load->model(['Quality_control_model', 'User_model']);
        $this->load->library(['form_validation']);
        $this->load->helper(['form', 'url', 'date']);
    }

    /**
     * Main QC page - input form
     */
    public function index()
    {
        $data['title'] = 'Quality Control Alat';
        $data['alat_list'] = $this->Quality_control_model->get_active_equipment();
        
        // Load views
        $this->load->view('template/header', $data);
        $this->load->view('template/sidebar', $data);
        $this->load->view('laboratorium/quality_control/index', $data);
    }

    /**
     * History page
     */
    public function riwayat()
    {
        $data['title'] = 'Riwayat Quality Control';
        $data['qc_history'] = $this->Quality_control_model->get_qc_history();
        
        // Load views
        $this->load->view('template/header', $data);
        $this->load->view('template/sidebar', $data);
        $this->load->view('laboratorium/quality_control/riwayat', $data);
        $this->load->view('template/footer');
    }

    /**
     * Get equipment info - AJAX
     */
    public function get_equipment_info($alat_id)
    {
        try {
            $equipment = $this->Quality_control_model->get_equipment_by_id($alat_id);
            
            if (!$equipment) {
                echo json_encode(['success' => false, 'message' => 'Alat tidak ditemukan']);
                return;
            }
            
            // Check calibration status
            $calibration_status = $this->Quality_control_model->get_calibration_status($equipment);
            
            // Get parameters
            $parameters = $this->Quality_control_model->get_qc_parameters($alat_id);
            
            echo json_encode([
                'success' => true,
                'equipment' => $equipment,
                'calibration_status' => $calibration_status,
                'parameters' => $parameters
            ]);
            
        } catch (Exception $e) {
            log_message('error', 'Error getting equipment info: ' . $e->getMessage());
            echo json_encode(['success' => false, 'message' => 'Terjadi kesalahan']);
        }
    }

    /**
     * Submit QC form
     */
    public function submit_qc()
    {
        if ($this->input->method() !== 'post') {
            show_404();
            return;
        }
        
        $this->form_validation->set_rules('alat_id', 'Alat', 'required|numeric');
        $this->form_validation->set_rules('tanggal_qc', 'Tanggal QC', 'required');
        $this->form_validation->set_rules('waktu_qc', 'Waktu QC', 'required');
        $this->form_validation->set_rules('teknisi', 'Teknisi', 'required|max_length[100]');
        
        if ($this->form_validation->run() === FALSE) {
            echo json_encode(['success' => false, 'message' => strip_tags(validation_errors())]);
            return;
        }
        
        try {
            $alat_id = $this->input->post('alat_id');
            
            // Verify equipment exists
            $equipment = $this->Quality_control_model->get_equipment_by_id($alat_id);
            if (!$equipment) {
                echo json_encode(['success' => false, 'message' => 'Alat tidak ditemukan']);
                return;
            }
            
            // Check calibration status
            $cal_status = $this->Quality_control_model->get_calibration_status($equipment);
            if ($cal_status['status'] === 'EXPIRED') {
                echo json_encode(['success' => false, 'message' => 'Tidak dapat melakukan QC. Kalibrasi alat sudah expired!']);
                return;
            }
            
            // Get parameters and results
            $parameter_names = $this->input->post('parameter_name');
            $parameter_units = $this->input->post('parameter_unit');
            $results = $this->input->post('result_value');
            $min_values = $this->input->post('min_value');
            $max_values = $this->input->post('max_value');
            
            if (empty($parameter_names) || !is_array($parameter_names)) {
                echo json_encode(['success' => false, 'message' => 'Parameter QC tidak valid']);
                return;
            }
            
            // Build arrays
            $param_qc = [];
            $nilai_hasil = [];
            $nilai_standar = [];
            $overall_status = 'Passed';
            
            foreach ($parameter_names as $idx => $param_name) {
                $result_val = floatval($results[$idx]);
                $min_val = floatval($min_values[$idx]);
                $max_val = floatval($max_values[$idx]);
                
                $param_qc[] = [
                    'name' => $param_name,
                    'unit' => $parameter_units[$idx]
                ];
                
                $nilai_hasil[] = $result_val;
                
                $nilai_standar[] = [
                    'min' => $min_val,
                    'max' => $max_val
                ];
                
                // Check if failed
                if ($result_val < $min_val || $result_val > $max_val) {
                    $overall_status = 'Failed';
                }
            }
            
            // Validate catatan if failed
            $catatan = $this->input->post('catatan');
            if ($overall_status === 'Failed' && empty($catatan)) {
                echo json_encode(['success' => false, 'message' => 'Catatan wajib diisi jika QC Failed']);
                return;
            }
            
            $data = [
                'alat_id' => $alat_id,
                'tanggal_qc' => $this->input->post('tanggal_qc'),
                'waktu_qc' => $this->input->post('waktu_qc'),
                'parameter_qc' => json_encode($param_qc),
                'nilai_hasil' => json_encode($nilai_hasil),
                'nilai_standar' => json_encode($nilai_standar),
                'hasil_qc' => $overall_status,
                'teknisi' => $this->input->post('teknisi'),
                'supervisor' => $this->input->post('supervisor'),
                'catatan' => $catatan,
                'tindakan_korektif' => $this->input->post('tindakan_korektif'),
                'qc_type' => $this->input->post('qc_type') ?: 'routine',
                'batch_number' => $this->input->post('batch_number'),
                'user_id' => $this->session->userdata('user_id')
            ];
            
            $qc_id = $this->Quality_control_model->save_qc($data);
            
            if ($qc_id) {
                $this->User_model->log_activity(
                    $this->session->userdata('user_id'),
                    'QC performed: ' . $equipment['nama_alat'] . ' - ' . $overall_status,
                    'quality_control',
                    $qc_id
                );
                
                echo json_encode([
                    'success' => true,
                    'message' => 'Quality Control berhasil disimpan',
                    'qc_id' => $qc_id,
                    'status' => $overall_status
                ]);
            } else {
                echo json_encode(['success' => false, 'message' => 'Gagal menyimpan QC']);
            }
            
        } catch (Exception $e) {
            log_message('error', 'Error submitting QC: ' . $e->getMessage());
            echo json_encode(['success' => false, 'message' => 'Terjadi kesalahan']);
        }
    }

    /**
     * Get QC detail - AJAX
     */
    public function get_qc_detail($qc_id)
    {
        try {
            $qc = $this->Quality_control_model->get_qc_by_id($qc_id);
            
            if (!$qc) {
                echo json_encode(['success' => false, 'message' => 'Data tidak ditemukan']);
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
}
