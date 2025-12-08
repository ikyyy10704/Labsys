<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Pasien extends CI_Controller {

    public function __construct()
    {
        parent::__construct();
        
        if (!$this->session->userdata('logged_in')) {
            $this->session->set_flashdata('error', 'Silakan login terlebih dahulu.');
            redirect('auth/login');
        }
        
        $this->load->model(['Pasien_model', 'Admin_model']);
        $this->load->library(['form_validation', 'upload']);
        $this->load->helper(['form', 'url', 'date']);
    }

    // ==========================================
    // MAIN PAGES
    // ==========================================

    public function index()
    {
        // Redirect to kelola for consistency
        redirect('pasien/kelola');
    }

    public function kelola()
    {
        // Set fullwidth layout untuk halaman ini
        $data['fullwidth'] = true;
        $data['title'] = 'Kelola Pasien';
        
        try {
            $data['patients'] = $this->Pasien_model->get_all_patients();
            $data['patient_stats'] = $this->Pasien_model->get_patient_statistics();
        } catch (Exception $e) {
            log_message('error', 'Error getting patients data: ' . $e->getMessage());
            $data['patients'] = array();
            $data['patient_stats'] = $this->_get_default_patient_stats();
        }
        
        // Load view dengan fullwidth
        $this->load->view('template/header', $data);
        $this->load->view('template/sidebar', $data);
        $this->_load_fullwidth_view('admin/Patient_management', $data);
        $this->load->view('template/footer', $data);
    }

    // ==========================================
    // AJAX ENDPOINTS
    // ==========================================

    public function get_patients_data()
    {
        $this->output->set_content_type('application/json');
        
        try {
            $patients = $this->Pasien_model->get_all_patients();
            $stats = $this->Pasien_model->get_patient_statistics();
            
            $response = array(
                'success' => true,
                'patients' => $patients,
                'stats' => $stats
            );
        } catch (Exception $e) {
            log_message('error', 'Error getting patients data: ' . $e->getMessage());
            $response = array(
                'success' => false,
                'message' => 'Gagal mengambil data pasien'
            );
        }
        
        $this->output->set_output(json_encode($response));
    }

    public function ajax_create_patient()
    {
        $this->output->set_content_type('application/json');
        
        if ($this->input->method() !== 'post') {
            $this->output->set_output(json_encode(array(
                'success' => false,
                'message' => 'Method not allowed'
            )));
            return;
        }
        
        // Set validation rules - TANPA permit_empty
        $this->form_validation->set_rules('nama', 'Nama Lengkap', 'required|min_length[2]|max_length[100]');
        $this->form_validation->set_rules('nik', 'NIK', 'required|exact_length[16]|numeric|is_unique[pasien.nik]');
        $this->form_validation->set_rules('jenis_kelamin', 'Jenis Kelamin', 'required|in_list[L,P]');
        $this->form_validation->set_rules('tanggal_lahir', 'Tanggal Lahir', 'required|callback_valid_date');
        $this->form_validation->set_rules('telepon', 'Telepon', 'required|min_length[10]|max_length[20]');
        
        // Field optional - cukup trim dan max_length
        $this->form_validation->set_rules('tempat_lahir', 'Tempat Lahir', 'trim|max_length[50]');
        $this->form_validation->set_rules('umur', 'Umur', 'trim|integer|greater_than[0]|less_than[200]');
        $this->form_validation->set_rules('pekerjaan', 'Pekerjaan', 'trim|max_length[100]');
        $this->form_validation->set_rules('kontak_darurat', 'Kontak Darurat', 'trim|max_length[100]');
        $this->form_validation->set_rules('dokter_perujuk', 'Dokter Perujuk', 'trim|max_length[100]');
        $this->form_validation->set_rules('asal_rujukan', 'Asal Rujukan', 'trim|max_length[100]');
        $this->form_validation->set_rules('nomor_rujukan', 'Nomor Rujukan', 'trim|max_length[50]');
        $this->form_validation->set_rules('tanggal_rujukan', 'Tanggal Rujukan', 'trim|callback_valid_date');
        
        if ($this->form_validation->run() === FALSE) {
            $this->output->set_output(json_encode(array(
                'success' => false,
                'message' => 'Validasi gagal',
                'errors' => $this->form_validation->error_array()
            )));
            return;
        }
        
        try {
            // Generate nomor registrasi
            $nomor_registrasi = $this->Pasien_model->generate_registration_number();
            
            // Calculate age if birth date provided
            $umur = $this->input->post('umur');
            if (empty($umur) && !empty($this->input->post('tanggal_lahir'))) {
                $birth_date = $this->input->post('tanggal_lahir');
                $umur = $this->Pasien_model->calculate_age($birth_date);
            }
            
            $patient_data = array(
                'nama' => $this->input->post('nama'),
                'nik' => $this->input->post('nik'),
                'jenis_kelamin' => $this->input->post('jenis_kelamin'),
                'tempat_lahir' => !empty($this->input->post('tempat_lahir')) ? $this->input->post('tempat_lahir') : null,
                'tanggal_lahir' => $this->input->post('tanggal_lahir'),
                'umur' => $umur,
                'alamat_domisili' => !empty($this->input->post('alamat_domisili')) ? $this->input->post('alamat_domisili') : null,
                'pekerjaan' => !empty($this->input->post('pekerjaan')) ? $this->input->post('pekerjaan') : null,
                'telepon' => $this->input->post('telepon'),
                'kontak_darurat' => !empty($this->input->post('kontak_darurat')) ? $this->input->post('kontak_darurat') : null,
                'riwayat_pasien' => !empty($this->input->post('riwayat_pasien')) ? $this->input->post('riwayat_pasien') : null,
                'permintaan_pemeriksaan' => !empty($this->input->post('permintaan_pemeriksaan')) ? $this->input->post('permintaan_pemeriksaan') : null,
                'dokter_perujuk' => !empty($this->input->post('dokter_perujuk')) ? $this->input->post('dokter_perujuk') : null,
                'asal_rujukan' => !empty($this->input->post('asal_rujukan')) ? $this->input->post('asal_rujukan') : null,
                'nomor_rujukan' => !empty($this->input->post('nomor_rujukan')) ? $this->input->post('nomor_rujukan') : null,
                'tanggal_rujukan' => !empty($this->input->post('tanggal_rujukan')) ? $this->input->post('tanggal_rujukan') : null,
                'diagnosis_awal' => !empty($this->input->post('diagnosis_awal')) ? $this->input->post('diagnosis_awal') : null,
                'rekomendasi_pemeriksaan' => !empty($this->input->post('rekomendasi_pemeriksaan')) ? $this->input->post('rekomendasi_pemeriksaan') : null,
                'nomor_registrasi' => $nomor_registrasi,
                'created_at' => date('Y-m-d H:i:s')
            );
            
            $patient_id = $this->Pasien_model->create_patient($patient_data);
            
            if ($patient_id) {
                // Log activity
                $this->Admin_model->log_activity(
                    $this->session->userdata('user_id'),
                    'Pasien baru ditambahkan: ' . $this->input->post('nama'),
                    'pasien',
                    $patient_id
                );
                
                $this->output->set_output(json_encode(array(
                    'success' => true,
                    'message' => 'Pasien berhasil ditambahkan dengan nomor registrasi: ' . $nomor_registrasi,
                    'patient_id' => $patient_id,
                    'nomor_registrasi' => $nomor_registrasi
                )));
            } else {
                $this->output->set_output(json_encode(array(
                    'success' => false,
                    'message' => 'Gagal menambahkan pasien'
                )));
            }
            
        } catch (Exception $e) {
            log_message('error', 'Error creating patient: ' . $e->getMessage());
            $this->output->set_output(json_encode(array(
                'success' => false,
                'message' => 'Terjadi kesalahan sistem: ' . $e->getMessage()
            )));
        }
    }

    public function ajax_get_patient_details($patient_id)
    {
        $this->output->set_content_type('application/json');
        
        try {
            if (empty($patient_id) || !is_numeric($patient_id)) {
                $this->output->set_output(json_encode(array(
                    'success' => false,
                    'message' => 'ID pasien tidak valid'
                )));
                return;
            }
            
            $patient = $this->Pasien_model->get_patient_by_id($patient_id);
            
            if (!$patient) {
                $this->output->set_output(json_encode(array(
                    'success' => false,
                    'message' => 'Pasien tidak ditemukan'
                )));
                return;
            }
            
            $this->output->set_output(json_encode(array(
                'success' => true,
                'patient' => $patient
            )));
            
        } catch (Exception $e) {
            log_message('error', 'Error getting patient details: ' . $e->getMessage());
            $this->output->set_output(json_encode(array(
                'success' => false,
                'message' => 'Gagal mengambil detail pasien'
            )));
        }
    }

    public function ajax_update_patient($patient_id)
    {
        $this->output->set_content_type('application/json');
        
        if ($this->input->method() !== 'post') {
            $this->output->set_output(json_encode(array(
                'success' => false,
                'message' => 'Method not allowed'
            )));
            return;
        }
        
        try {
            if (empty($patient_id) || !is_numeric($patient_id)) {
                $this->output->set_output(json_encode(array(
                    'success' => false,
                    'message' => 'ID pasien tidak valid'
                )));
                return;
            }
            
            $patient = $this->Pasien_model->get_patient_by_id($patient_id);
            
            if (!$patient) {
                $this->output->set_output(json_encode(array(
                    'success' => false,
                    'message' => 'Pasien tidak ditemukan'
                )));
                return;
            }
            
            // Set validation rules for update - TANPA permit_empty
            $this->form_validation->set_rules('nama', 'Nama Lengkap', 'required|min_length[2]|max_length[100]');
            
            // NIK validation for update
            if ($this->input->post('nik') && $this->input->post('nik') != $patient['nik']) {
                $this->form_validation->set_rules('nik', 'NIK', 'required|exact_length[16]|numeric|callback_check_nik_unique[' . $patient_id . ']');
            }
            
            $this->form_validation->set_rules('jenis_kelamin', 'Jenis Kelamin', 'required|in_list[L,P]');
            $this->form_validation->set_rules('tanggal_lahir', 'Tanggal Lahir', 'required|callback_valid_date');
            $this->form_validation->set_rules('telepon', 'Telepon', 'required|min_length[10]|max_length[20]');
            
            // Field optional
            $this->form_validation->set_rules('tempat_lahir', 'Tempat Lahir', 'trim|max_length[50]');
            $this->form_validation->set_rules('umur', 'Umur', 'trim|integer|greater_than[0]|less_than[200]');
            $this->form_validation->set_rules('pekerjaan', 'Pekerjaan', 'trim|max_length[100]');
            $this->form_validation->set_rules('kontak_darurat', 'Kontak Darurat', 'trim|max_length[100]');
            $this->form_validation->set_rules('dokter_perujuk', 'Dokter Perujuk', 'trim|max_length[100]');
            $this->form_validation->set_rules('asal_rujukan', 'Asal Rujukan', 'trim|max_length[100]');
            $this->form_validation->set_rules('nomor_rujukan', 'Nomor Rujukan', 'trim|max_length[50]');
            $this->form_validation->set_rules('tanggal_rujukan', 'Tanggal Rujukan', 'trim|callback_valid_date');
            
            if ($this->form_validation->run() === FALSE) {
                $this->output->set_output(json_encode(array(
                    'success' => false,
                    'message' => 'Validasi gagal',
                    'errors' => $this->form_validation->error_array()
                )));
                return;
            }
            
            // Calculate age if birth date provided
            $umur = $this->input->post('umur');
            if (empty($umur) && !empty($this->input->post('tanggal_lahir'))) {
                $birth_date = $this->input->post('tanggal_lahir');
                $umur = $this->Pasien_model->calculate_age($birth_date);
            }
            
            $patient_data = array(
                'nama' => $this->input->post('nama'),
                'nik' => $this->input->post('nik'),
                'jenis_kelamin' => $this->input->post('jenis_kelamin'),
                'tempat_lahir' => !empty($this->input->post('tempat_lahir')) ? $this->input->post('tempat_lahir') : null,
                'tanggal_lahir' => $this->input->post('tanggal_lahir'),
                'umur' => $umur,
                'alamat_domisili' => !empty($this->input->post('alamat_domisili')) ? $this->input->post('alamat_domisili') : null,
                'pekerjaan' => !empty($this->input->post('pekerjaan')) ? $this->input->post('pekerjaan') : null,
                'telepon' => $this->input->post('telepon'),
                'kontak_darurat' => !empty($this->input->post('kontak_darurat')) ? $this->input->post('kontak_darurat') : null,
                'riwayat_pasien' => !empty($this->input->post('riwayat_pasien')) ? $this->input->post('riwayat_pasien') : null,
                'permintaan_pemeriksaan' => !empty($this->input->post('permintaan_pemeriksaan')) ? $this->input->post('permintaan_pemeriksaan') : null,
                'dokter_perujuk' => !empty($this->input->post('dokter_perujuk')) ? $this->input->post('dokter_perujuk') : null,
                'asal_rujukan' => !empty($this->input->post('asal_rujukan')) ? $this->input->post('asal_rujukan') : null,
                'nomor_rujukan' => !empty($this->input->post('nomor_rujukan')) ? $this->input->post('nomor_rujukan') : null,
                'tanggal_rujukan' => !empty($this->input->post('tanggal_rujukan')) ? $this->input->post('tanggal_rujukan') : null,
                'diagnosis_awal' => !empty($this->input->post('diagnosis_awal')) ? $this->input->post('diagnosis_awal') : null,
                'rekomendasi_pemeriksaan' => !empty($this->input->post('rekomendasi_pemeriksaan')) ? $this->input->post('rekomendasi_pemeriksaan') : null
            );
            
            if ($this->Pasien_model->update_patient($patient_id, $patient_data)) {
                // Log activity
                $this->Admin_model->log_activity(
                    $this->session->userdata('user_id'),
                    'Data pasien diperbarui: ' . $patient['nama'],
                    'pasien',
                    $patient_id
                );
                
                $this->output->set_output(json_encode(array(
                    'success' => true,
                    'message' => 'Data pasien berhasil diperbarui'
                )));
            } else {
                $this->output->set_output(json_encode(array(
                    'success' => false,
                    'message' => 'Gagal memperbarui data pasien'
                )));
            }
            
        } catch (Exception $e) {
            log_message('error', 'Error updating patient: ' . $e->getMessage());
            $this->output->set_output(json_encode(array(
                'success' => false,
                'message' => 'Terjadi kesalahan sistem: ' . $e->getMessage()
            )));
        }
    }

    public function ajax_delete_patient($patient_id = null)
    {
        $this->output->set_content_type('application/json');
        
        $method = $this->input->method();
        if (!in_array($method, ['post', 'delete'])) {
            $this->output->set_output(json_encode(array(
                'success' => false,
                'message' => 'Method tidak diizinkan'
            )));
            return;
        }
        
        try {
            if (empty($patient_id) || !is_numeric($patient_id)) {
                $this->output->set_output(json_encode(array(
                    'success' => false,
                    'message' => 'ID pasien tidak valid'
                )));
                return;
            }
            
            $patient = $this->Pasien_model->get_patient_by_id($patient_id);
            
            if (!$patient) {
                $this->output->set_output(json_encode(array(
                    'success' => false,
                    'message' => 'Pasien tidak ditemukan'
                )));
                return;
            }
            
            // Check if patient has examination records
            $has_examinations = $this->Pasien_model->check_patient_examinations($patient_id);
            
            if ($has_examinations) {
                $this->output->set_output(json_encode(array(
                    'success' => false,
                    'message' => 'Tidak dapat menghapus pasien yang sudah memiliki riwayat pemeriksaan'
                )));
                return;
            }
            
            if ($this->Pasien_model->delete_patient($patient_id)) {
                // Log activity
                $this->Admin_model->log_activity(
                    $this->session->userdata('user_id'),
                    'Pasien dihapus: ' . $patient['nama'],
                    'pasien',
                    $patient_id
                );
                
                $this->output->set_output(json_encode(array(
                    'success' => true,
                    'message' => 'Pasien berhasil dihapus'
                )));
            } else {
                $this->output->set_output(json_encode(array(
                    'success' => false,
                    'message' => 'Gagal menghapus pasien'
                )));
            }
            
        } catch (Exception $e) {
            log_message('error', 'Error deleting patient: ' . $e->getMessage());
            $this->output->set_output(json_encode(array(
                'success' => false,
                'message' => 'Terjadi kesalahan sistem: ' . $e->getMessage()
            )));
        }
    }

    public function ajax_search_patients()
    {
        $this->output->set_content_type('application/json');
        
        try {
            $search_term = $this->input->get('term');
            $limit = $this->input->get('limit') ? (int)$this->input->get('limit') : 10;
            
            if (empty($search_term) || strlen($search_term) < 2) {
                $this->output->set_output(json_encode(array(
                    'success' => false,
                    'message' => 'Minimal 2 karakter untuk pencarian'
                )));
                return;
            }
            
            $patients = $this->Pasien_model->search_patients($search_term, $limit);
            
            $this->output->set_output(json_encode(array(
                'success' => true,
                'patients' => $patients
            )));
            
        } catch (Exception $e) {
            log_message('error', 'Error searching patients: ' . $e->getMessage());
            $this->output->set_output(json_encode(array(
                'success' => false,
                'message' => 'Gagal mencari pasien'
            )));
        }
    }

    // ==========================================

    // ==========================================

    public function check_nik_unique($nik, $patient_id)
    {
        if (empty($nik)) {
            return TRUE; // NIK is optional in update
        }
        
        if ($this->Pasien_model->check_nik_exists($nik, $patient_id)) {
            $this->form_validation->set_message('check_nik_unique', 'NIK sudah terdaftar');
            return FALSE;
        }
        return TRUE;
    }

    public function valid_date($date)
    {
        // Allow empty for optional fields
        if (empty($date)) {
            return TRUE;
        }
        
        $d = DateTime::createFromFormat('Y-m-d', $date);
        if ($d && $d->format('Y-m-d') === $date) {
            return TRUE;
        }
        
        $this->form_validation->set_message('valid_date', 'Format tanggal tidak valid (YYYY-MM-DD)');
        return FALSE;
    }

    // ==========================================
    // PATIENT REPORTS & ANALYTICS
    // ==========================================

    public function reports()
    {
        // Set fullwidth layout
        $data['fullwidth'] = true;
        $data['title'] = 'Laporan Pasien';
        
        try {
            $data['stats'] = $this->Pasien_model->get_detailed_patient_statistics();
            $data['chart_data'] = $this->Pasien_model->get_patient_chart_data();
            
            // Log activity
            $this->Admin_model->log_activity(
                $this->session->userdata('user_id'),
                'Mengakses laporan pasien',
                'system',
                null
            );
            
        } catch (Exception $e) {
            log_message('error', 'Error loading patient reports: ' . $e->getMessage());
            $data['stats'] = array();
            $data['chart_data'] = array();
        }
        
        $this->load->view('template/header', $data);
        $this->load->view('template/sidebar', $data);
        $this->_load_fullwidth_view('pasien/reports', $data);
        $this->load->view('template/footer', $data);
    }

    public function ajax_get_patient_reports()
    {
        $this->output->set_content_type('application/json');
        
        try {
            $filters = array(
                'start_date' => $this->input->get('start_date'),
                'end_date' => $this->input->get('end_date'),
                'gender' => $this->input->get('gender'),
                'age_range' => $this->input->get('age_range'),
                'search' => $this->input->get('search')
            );
            
            $per_page = $this->input->get('per_page') ? (int)$this->input->get('per_page') : 20;
            $page = $this->input->get('page') ? (int)$this->input->get('page') : 1;
            $offset = ($page - 1) * $per_page;
            
            $patients = $this->Pasien_model->get_patient_reports($per_page, $offset, $filters);
            $total_records = $this->Pasien_model->count_patient_reports($filters);
            $stats = $this->Pasien_model->get_patient_statistics($filters);
            
            $response = array(
                'success' => true,
                'patients' => $patients,
                'total_records' => $total_records,
                'stats' => $stats,
                'pagination' => array(
                    'current_page' => $page,
                    'per_page' => $per_page,
                    'total_pages' => ceil($total_records / $per_page),
                    'total_records' => $total_records
                )
            );
            
        } catch (Exception $e) {
            log_message('error', 'Error getting patient reports: ' . $e->getMessage());
            $response = array(
                'success' => false,
                'message' => 'Gagal mengambil laporan pasien'
            );
        }
        
        $this->output->set_output(json_encode($response));
    }

    /**
     * AJAX endpoint untuk check NIK exists
     * Dipanggil oleh JavaScript untuk validasi real-time
     * Sesuai dengan pattern di Administrasi controller
     */
    public function check_nik_exists()
    {
        $this->output->set_content_type('application/json');
        
        try {
            $nik = $this->input->get('nik');
            $exclude_id = $this->input->get('exclude_id'); // untuk edit, exclude ID sendiri
            
            // Validasi input
            if (empty($nik)) {
                $this->output->set_output(json_encode(array(
                    'success' => false,
                    'exists' => false,
                    'message' => 'NIK tidak boleh kosong'
                )));
                return;
            }
            
            // Validasi panjang NIK
            if (strlen($nik) !== 16) {
                $this->output->set_output(json_encode(array(
                    'success' => false,
                    'exists' => false,
                    'message' => 'NIK harus 16 digit'
                )));
                return;
            }
            
            // Validasi numeric
            if (!is_numeric($nik)) {
                $this->output->set_output(json_encode(array(
                    'success' => false,
                    'exists' => false,
                    'message' => 'NIK harus berupa angka'
                )));
                return;
            }
            
            // Check apakah NIK exists di database
            $exists = $this->Pasien_model->check_nik_exists($nik, $exclude_id);
            
            if ($exists) {
                // NIK sudah terdaftar - ambil data pasien
                $this->db->where('nik', $nik);
                if (!empty($exclude_id)) {
                    $this->db->where('pasien_id !=', $exclude_id);
                }
                $patient = $this->db->get('pasien')->row_array();
                
                if ($patient) {
                    $this->output->set_output(json_encode(array(
                        'success' => true,
                        'exists' => true,
                        'message' => 'NIK sudah terdaftar',
                        'patient' => array(
                            'pasien_id' => $patient['pasien_id'],
                            'nama' => $patient['nama'],
                            'nomor_registrasi' => $patient['nomor_registrasi'],
                            'telepon' => $patient['telepon']
                        )
                    )));
                } else {
                    // Seharusnya tidak terjadi, tapi handle just in case
                    $this->output->set_output(json_encode(array(
                        'success' => true,
                        'exists' => true,
                        'message' => 'NIK sudah terdaftar'
                    )));
                }
            } else {
                // NIK tersedia
                $this->output->set_output(json_encode(array(
                    'success' => true,
                    'exists' => false,
                    'message' => 'NIK tersedia'
                )));
            }
            
        } catch (Exception $e) {
            log_message('error', 'Error checking NIK exists: ' . $e->getMessage());
            $this->output->set_output(json_encode(array(
                'success' => false,
                'exists' => false,
                'message' => 'Terjadi kesalahan sistem'
            )));
        }
    }

    // ==========================================
    // HELPER METHODS
    // ==========================================

    private function _load_fullwidth_view($view, $data = array())
    {
        if (!empty($data) && isset($data['fullwidth']) && $data['fullwidth'] === true) {
            $this->load->view($view, $data);
        } else {
            $this->load->view('template/header', $data);
            $this->load->view('template/sidebar', $data);
            $this->load->view($view, $data);
            $this->load->view('template/footer');
        }
    }

    private function _get_default_patient_stats()
    {
        return array(
            'total' => 0,
            'today' => 0,
            'male' => 0,
            'female' => 0,
            'by_age_group' => array(),
            'by_month' => array()
        );
    }
}