<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Administrasi extends CI_Controller {

    public function __construct()
    {
        parent::__construct();
        
        // Check if user is logged in and has administrasi role
        if (!$this->session->userdata('logged_in') || $this->session->userdata('role') !== 'administrasi') {
            $this->session->set_flashdata('error', 'Akses ditolak. Anda tidak memiliki izin untuk mengakses halaman ini.');
            redirect('auth/login');
        }
        
        $this->load->model(['Administrasi_model', 'User_model']);
        $this->load->library(['form_validation']);
        $this->load->helper(['form', 'url', 'date']);
    }
public function dashboard()
{
    $data['title'] = 'Dashboard Administrasi';
    
    // Load model yang diperlukan
    $this->load->model('Administrasi_laporan_model');
    
    try {
        // Financial & Registration Stats
        $data['financial_summary'] = $this->Administrasi_model->get_dashboard_financial_summary();
        $data['registration_stats'] = $this->Administrasi_model->get_registration_statistics();
        $data['payment_stats'] = $this->Administrasi_model->get_payment_statistics();
        
        // Recent Data
        $data['recent_registrations'] = $this->Administrasi_model->get_recent_registrations(5);
        $data['pending_payments'] = $this->Administrasi_model->get_pending_invoices();
        $data['recent_payments'] = $this->Administrasi_model->get_recent_payments();
        
        // Revenue Data
        $data['today_revenue'] = $this->Administrasi_model->get_today_revenue();
        $data['weekly_revenue'] = $this->Administrasi_model->get_weekly_revenue();
        $data['monthly_revenue'] = $this->Administrasi_model->get_monthly_revenue_summary();
        
        // Payment Method Stats & Examination Stats
        $data['payment_method_stats'] = $this->Administrasi_laporan_model->get_payment_method_statistics([
            'start_date' => date('Y-m-01'),
            'end_date' => date('Y-m-d')
        ]);
        
        $data['examination_stats'] = $this->Administrasi_laporan_model->get_examination_statistics([
            'start_date' => date('Y-m-01'),
            'end_date' => date('Y-m-d')
        ]);
        
    } catch (Exception $e) {
        log_message('error', 'Error loading administrasi dashboard: ' . $e->getMessage());
        
        // Default values jika terjadi error
        $data['financial_summary'] = [
            'total_invoices' => 0,
            'total_revenue' => 0,
            'pending_revenue' => 0,
            'paid_invoices' => 0,
            'unpaid_invoices' => 0,
            'average_invoice_amount' => 0,
            'highest_invoice_amount' => 0
        ];
        
        $data['registration_stats'] = [
            'total_pasien' => 0,
            'registrasi_hari_ini' => 0,
            'registrasi_minggu_ini' => 0,
            'registrasi_bulan_ini' => 0
        ];
        
        $data['payment_stats'] = [
            'pembayaran_umum' => 0,
            'pembayaran_bpjs' => 0
        ];
        
        $data['payment_method_stats'] = [];
        $data['examination_stats'] = [
            'total' => 0,
            'pending' => 0,
            'progress' => 0,
            'selesai' => 0
        ];
        
        $data['recent_registrations'] = [];
        $data['pending_payments'] = [];
        $data['recent_payments'] = [];
        
        $data['today_revenue'] = 0;
        $data['weekly_revenue'] = 0;
        $data['monthly_revenue'] = ['revenue' => 0, 'invoice_count' => 0, 'average_amount' => 0];
    }
    
    $this->load->view('template/header', $data);
    $this->load->view('template/sidebar', $data);
    $this->load->view('administrasi/index', $data);
    $this->load->view('template/footer');
}

public function ajax_get_revenue_chart_data() 
{
    $this->output->set_content_type('application/json');
    
    // Get 7 days revenue data
    $data = [];
    for ($i = 6; $i >= 0; $i--) {
        $date = date('Y-m-d', strtotime("-$i days"));
        
        $this->db->select('SUM(total_biaya) as total');
        $this->db->where('DATE(tanggal_pembayaran)', $date);
        $this->db->where('status_pembayaran', 'lunas');
        $result = $this->db->get('invoice')->row_array();
        
        $data[] = [
            'date' => $date,
            'total' => $result['total'] ?? 0
        ];
    }
    
    echo json_encode(['success' => true, 'data' => $data]);
}

// ==================== PATIENT MANAGEMENT ====================

public function patient_management()
{
    // Handle AJAX POST request FIRST before any output
    if ($this->input->method() === 'post') {
        $this->_handle_create_patient_ajax();
        return;
    }
    
    $data['title'] = 'Kelola Pasien';
    
    // Search functionality
    $search = $this->input->get('search') ?? '';
    $page = $this->input->get('page') ? (int)$this->input->get('page') : 1;
    $limit = 10;
    $offset = ($page - 1) * $limit;
    
    try {
        // Get statistics
        $data['stats'] = [
            'total' => $this->Administrasi_model->count_patients(''),
            'today' => $this->db->where('DATE(created_at)', date('Y-m-d'))->count_all_results('pasien'),
            'male' => $this->db->where('jenis_kelamin', 'L')->count_all_results('pasien'),
            'female' => $this->db->where('jenis_kelamin', 'P')->count_all_results('pasien'),
        ];
        
        // Get total count for pagination
        $total_patients = $this->Administrasi_model->count_patients($search);
        
        // Get paginated patients
        $data['patients'] = $this->Administrasi_model->get_patients_paginated($search, $limit, $offset);
        
        // Pagination configuration
        $config = [
            'base_url' => base_url('administrasi/patient_management'),
            'total_rows' => (int)$total_patients,
            'per_page' => (int)$limit,
            'use_page_numbers' => TRUE,
            'page_query_string' => TRUE,
            'query_string_segment' => 'page',
            'reuse_query_string' => TRUE,
            'num_links' => 2,
            
            'first_link' => '‹‹',
            'last_link' => '››',
            'next_link' => '›',
            'prev_link' => '‹',
            'full_tag_open' => '<div class="flex space-x-2">',
            'full_tag_close' => '</div>',
            'num_tag_open' => '<button class="px-3 py-1 border border-gray-300 rounded hover:bg-gray-100">',
            'num_tag_close' => '</button>',
            'cur_tag_open' => '<button class="px-3 py-1 border border-blue-500 bg-blue-500 text-white rounded">',
            'cur_tag_close' => '</button>',
            'next_tag_open' => '<button class="px-3 py-1 border border-gray-300 rounded hover:bg-gray-100">',
            'next_tag_close' => '</button>',
            'prev_tag_open' => '<button class="px-3 py-1 border border-gray-300 rounded hover:bg-gray-100">',
            'prev_tag_close' => '</button>',
            'first_tag_open' => '<button class="px-3 py-1 border border-gray-300 rounded hover:bg-gray-100">',
            'first_tag_close' => '</button>',
            'last_tag_open' => '<button class="px-3 py-1 border border-gray-300 rounded hover:bg-gray-100">',
            'last_tag_close' => '</button>',
        ];
        
        $this->load->library('pagination');
        $this->pagination->initialize($config);
        
        $data['pagination'] = $this->pagination->create_links();
        $data['total_patients'] = $total_patients;
        
    } catch (Exception $e) {
        log_message('error', 'Error getting patient management data: ' . $e->getMessage());
        $data['stats'] = ['total' => 0, 'today' => 0, 'male' => 0, 'female' => 0];
        $data['patients'] = array();
        $data['pagination'] = '';
        $data['total_patients'] = 0;
    }
    
    $data['search'] = $search;
    $data['current_page'] = $page;
    $data['limit'] = $limit;
    
    $this->load->view('template/header', $data);
    $this->load->view('template/sidebar', $data);
    $this->load->view('administrasi/patient_management', $data);
    $this->load->view('template/footer');
}

private function _handle_create_patient_ajax()
{
    $this->output->set_content_type('application/json');
    
    // Validation rules
    $this->form_validation->set_rules('nama', 'Nama', 'required|min_length[2]');
    $this->form_validation->set_rules('nik', 'NIK', 'required|exact_length[16]|numeric|is_unique[pasien.nik]');
    $this->form_validation->set_rules('jenis_kelamin', 'Jenis Kelamin', 'required|in_list[L,P]');
    $this->form_validation->set_rules('tanggal_lahir', 'Tanggal Lahir', 'required');
    $this->form_validation->set_rules('telepon', 'Telepon', 'required|min_length[10]');
    
    if ($this->form_validation->run() === TRUE) {
        // Generate registration number
        $prefix = 'REG' . date('Y');
        $last_reg = $this->Administrasi_model->get_last_registration_number($prefix);
        
        if ($last_reg) {
            $last_number = intval(substr($last_reg['nomor_registrasi'], -4));
            $new_number = $last_number + 1;
        } else {
            $new_number = 1;
        }
        
        $registration_number = $prefix . str_pad($new_number, 4, '0', STR_PAD_LEFT);
        
        // Calculate age
        $birth_date = $this->input->post('tanggal_lahir');
        $age = date_diff(date_create($birth_date), date_create('today'))->y;
        
        $patient_data = array(
            'nama' => trim($this->input->post('nama')),
            'nik' => trim($this->input->post('nik')),
            'jenis_kelamin' => $this->input->post('jenis_kelamin'),
            'tempat_lahir' => !empty($this->input->post('tempat_lahir')) ? trim($this->input->post('tempat_lahir')) : null,
            'tanggal_lahir' => $birth_date,
            'umur' => $age,
            'alamat_domisili' => !empty($this->input->post('alamat_domisili')) ? trim($this->input->post('alamat_domisili')) : null,
            'pekerjaan' => !empty($this->input->post('pekerjaan')) ? trim($this->input->post('pekerjaan')) : null,
            'telepon' => trim($this->input->post('telepon')),
            'kontak_darurat' => !empty($this->input->post('kontak_darurat')) ? trim($this->input->post('kontak_darurat')) : null,
            'riwayat_pasien' => !empty($this->input->post('riwayat_pasien')) ? trim($this->input->post('riwayat_pasien')) : null,
            'permintaan_pemeriksaan' => !empty($this->input->post('permintaan_pemeriksaan')) ? trim($this->input->post('permintaan_pemeriksaan')) : null,
            'dokter_perujuk' => !empty($this->input->post('dokter_perujuk')) ? trim($this->input->post('dokter_perujuk')) : null,
            'asal_rujukan' => !empty($this->input->post('asal_rujukan')) ? trim($this->input->post('asal_rujukan')) : null,
            'nomor_rujukan' => !empty($this->input->post('nomor_rujukan')) ? trim($this->input->post('nomor_rujukan')) : null,
            'tanggal_rujukan' => !empty($this->input->post('tanggal_rujukan')) ? $this->input->post('tanggal_rujukan') : null,
            'diagnosis_awal' => !empty($this->input->post('diagnosis_awal')) ? trim($this->input->post('diagnosis_awal')) : null,
            'rekomendasi_pemeriksaan' => !empty($this->input->post('rekomendasi_pemeriksaan')) ? trim($this->input->post('rekomendasi_pemeriksaan')) : null,
            'nomor_registrasi' => $registration_number,
            'created_at' => date('Y-m-d H:i:s')
        );
        
        $patient_id = $this->Administrasi_model->create_patient($patient_data);
        
        if ($patient_id && $patient_id > 0) {
            if (isset($this->User_model)) {
                $this->User_model->log_activity(
                    $this->session->userdata('user_id'), 
                    'Patient created: ' . $patient_data['nama'], 
                    'pasien', 
                    $patient_id
                );
            }
            
            log_message('info', 'Patient created successfully: ID=' . $patient_id . ', RegNo=' . $registration_number);
            
            $this->output
                ->set_status_header(200)
                ->set_content_type('application/json', 'utf-8')
                ->set_output(json_encode([
                    'success' => true, 
                    'message' => 'Pasien berhasil didaftarkan dengan nomor registrasi: ' . $registration_number,
                    'patient_id' => (int)$patient_id,
                    'nomor_registrasi' => $registration_number,
                    'timestamp' => date('Y-m-d H:i:s')
                ], JSON_PRETTY_PRINT));
        } else {
            $error = $this->db->error();
            log_message('error', 'Database error creating patient: ' . json_encode($error));
            
            $this->output
                ->set_status_header(500)
                ->set_content_type('application/json', 'utf-8')
                ->set_output(json_encode([
                    'success' => false, 
                    'message' => 'Gagal menyimpan data pasien. Silakan coba lagi atau hubungi administrator.',
                    'error_code' => $error['code'] ?? 'UNKNOWN'
                ], JSON_PRETTY_PRINT));
        }
    } else {
        $errors = validation_errors();
        $this->output->set_output(json_encode([
            'success' => false, 
            'message' => 'Validasi gagal',
            'errors' => strip_tags($errors)
        ]));
    }
}

public function add_patient_data()
{
    $data['title'] = 'Tambah Data Pasien';
    
    if ($this->input->method() === 'post') {
        $this->_handle_add_patient();
    }
    
    $this->load->view('template/header', $data);
    $this->load->view('template/sidebar', $data);
    $this->load->view('administrasi/add_patient', $data);
    $this->load->view('template/footer');
}

private function _handle_add_patient()
{
    // Validation rules
    $this->form_validation->set_rules('nama', 'Nama', 'required|min_length[2]');
    $this->form_validation->set_rules('nik', 'NIK', 'required|exact_length[16]|numeric|is_unique[pasien.nik]');
    $this->form_validation->set_rules('jenis_kelamin', 'Jenis Kelamin', 'required|in_list[L,P]');
    $this->form_validation->set_rules('tanggal_lahir', 'Tanggal Lahir', 'required');
    $this->form_validation->set_rules('telepon', 'Telepon', 'required|min_length[10]');
    
    if ($this->form_validation->run() === TRUE) {
        // Generate registration number
        $prefix = 'REG' . date('Y');
        $last_reg = $this->Administrasi_model->get_last_registration_number($prefix);
        
        if ($last_reg) {
            $last_number = intval(substr($last_reg['nomor_registrasi'], -4));
            $new_number = $last_number + 1;
        } else {
            $new_number = 1;
        }
        
        $registration_number = $prefix . str_pad($new_number, 4, '0', STR_PAD_LEFT);
        
        // Calculate age
        $birth_date = $this->input->post('tanggal_lahir');
        $age = date_diff(date_create($birth_date), date_create('today'))->y;
        
        $patient_data = array(
            'nama' => $this->input->post('nama'),
            'nik' => $this->input->post('nik'),
            'jenis_kelamin' => $this->input->post('jenis_kelamin'),
            'tempat_lahir' => $this->input->post('tempat_lahir'),
            'tanggal_lahir' => $birth_date,
            'umur' => $age,
            'alamat_domisili' => $this->input->post('alamat_domisili'),
            'pekerjaan' => $this->input->post('pekerjaan'),
            'telepon' => $this->input->post('telepon'),
            'kontak_darurat' => $this->input->post('kontak_darurat'),
            'riwayat_pasien' => $this->input->post('riwayat_pasien'),
            'permintaan_pemeriksaan' => $this->input->post('permintaan_pemeriksaan'),
            'dokter_perujuk' => $this->input->post('dokter_perujuk'),
            'asal_rujukan' => $this->input->post('asal_rujukan'),
            'nomor_rujukan' => $this->input->post('nomor_rujukan'),
            'tanggal_rujukan' => $this->input->post('tanggal_rujukan'),
            'diagnosis_awal' => $this->input->post('diagnosis_awal'),
            'rekomendasi_pemeriksaan' => $this->input->post('rekomendasi_pemeriksaan'),
            'nomor_registrasi' => $registration_number,
            'created_at' => date('Y-m-d H:i:s')
        );
        
        $patient_id = $this->Administrasi_model->create_patient($patient_data);
        
        if ($patient_id) {
            $this->session->set_flashdata('success', 'Pasien berhasil didaftarkan dengan nomor registrasi: ' . $registration_number);
            redirect('administrasi/add_patient_data');
        } else {
            $this->session->set_flashdata('error', 'Gagal mendaftarkan pasien');
        }
    }
}

public function patient_history()
{
    $data['title'] = 'Riwayat Pasien';
    
    // Search functionality
    $search = $this->input->get('search') ?? '';
    $page = $this->input->get('page') ? (int)$this->input->get('page') : 1;
    $limit = 10;
    $offset = ($page - 1) * $limit;
    
    try {
        $total_patients = $this->Administrasi_model->count_patients($search);
        $data['patients'] = $this->Administrasi_model->get_patients_paginated($search, $limit, $offset);
        
        // Pagination configuration
        $config = array(
            'base_url' => base_url('administrasi/patient_history'),
            'total_rows' => (int)$total_patients,
            'per_page' => (int)$limit,
            'use_page_numbers' => TRUE,
            'page_query_string' => TRUE,
            'query_string_segment' => 'page',
            'reuse_query_string' => TRUE,
            'num_links' => 2,
            'uri_segment' => 0,
            
            'full_tag_open' => '<div class="flex space-x-2">',
            'full_tag_close' => '</div>',
            'first_link' => 'First',
            'last_link' => 'Last',
            'next_link' => 'Next',
            'prev_link' => 'Prev',
            'num_tag_open' => '<button class="px-3 py-1 border border-gray-300 rounded hover:bg-gray-100">',
            'num_tag_close' => '</button>',
            'cur_tag_open' => '<button class="px-3 py-1 border border-blue-500 bg-blue-500 text-white rounded">',
            'cur_tag_close' => '</button>',
            'next_tag_open' => '<button class="px-3 py-1 border border-gray-300 rounded hover:bg-gray-100">',
            'next_tag_close' => '</button>',
            'prev_tag_open' => '<button class="px-3 py-1 border border-gray-300 rounded hover:bg-gray-100">',
            'prev_tag_close' => '</button>',
        );
        
        $this->load->library('pagination');
        $this->pagination->initialize($config);
        
        $data['pagination'] = $this->pagination->create_links();
        $data['total_patients'] = $total_patients;
        
    } catch (Exception $e) {
        log_message('error', 'Error getting patient history: ' . $e->getMessage());
        $data['patients'] = array();
        $data['pagination'] = '';
        $data['total_patients'] = 0;
    }
    
    $data['search'] = $search;
    $data['current_page'] = $page;
    $data['limit'] = $limit;
    
    $this->load->view('template/header', $data);
    $this->load->view('template/sidebar', $data);
    $this->load->view('administrasi/patient_history', $data);
    $this->load->view('template/footer');
}

public function patient_detail($patient_id)
{
    $patient = $this->Administrasi_model->get_patient_by_id($patient_id);
    
    if (!$patient) {
        $this->session->set_flashdata('error', 'Pasien tidak ditemukan');
        redirect('administrasi/patient_history');
    }
    
    $data['title'] = 'Detail Pasien: ' . $patient['nama'];
    $data['patient'] = $patient;
    
    $this->load->view('template/header', $data);
    $this->load->view('template/sidebar', $data);
    $this->load->view('administrasi/patient_detail', $data);
    $this->load->view('template/footer');
}

public function edit_patient($patient_id)
{
    $patient = $this->Administrasi_model->get_patient_by_id($patient_id);
    
    if (!$patient) {
        $this->session->set_flashdata('error', 'Pasien tidak ditemukan');
        redirect('administrasi/patient_history');
    }
    
    $data['title'] = 'Edit Pasien: ' . $patient['nama'];
    $data['patient'] = $patient;
    
    if ($this->input->method() === 'post') {
        $this->_handle_edit_patient($patient_id);
    }
    
    $this->load->view('template/header', $data);
    $this->load->view('template/sidebar', $data);
    $this->load->view('administrasi/edit_patient', $data);
    $this->load->view('template/footer');
}

private function _handle_edit_patient($patient_id)
{
    // Validation rules
    $this->form_validation->set_rules('nama', 'Nama', 'required|min_length[2]');
    $this->form_validation->set_rules('nik', 'NIK', 'required|exact_length[16]|numeric|callback_check_nik_unique[' . $patient_id . ']');
    $this->form_validation->set_rules('jenis_kelamin', 'Jenis Kelamin', 'required|in_list[L,P]');
    $this->form_validation->set_rules('tanggal_lahir', 'Tanggal Lahir', 'required');
    $this->form_validation->set_rules('telepon', 'Telepon', 'required|min_length[10]');
    
    if ($this->form_validation->run() === TRUE) {
        // Calculate age
        $birth_date = $this->input->post('tanggal_lahir');
        $age = date_diff(date_create($birth_date), date_create('today'))->y;
        
        $patient_data = array(
            'nama' => $this->input->post('nama'),
            'nik' => $this->input->post('nik'),
            'jenis_kelamin' => $this->input->post('jenis_kelamin'),
            'tempat_lahir' => $this->input->post('tempat_lahir'),
            'tanggal_lahir' => $birth_date,
            'umur' => $age,
            'alamat_domisili' => $this->input->post('alamat_domisili'),
            'pekerjaan' => $this->input->post('pekerjaan'),
            'telepon' => $this->input->post('telepon'),
            'kontak_darurat' => $this->input->post('kontak_darurat'),
            'riwayat_pasien' => $this->input->post('riwayat_pasien'),
            'permintaan_pemeriksaan' => $this->input->post('permintaan_pemeriksaan'),
            'dokter_perujuk' => $this->input->post('dokter_perujuk'),
            'asal_rujukan' => $this->input->post('asal_rujukan'),
            'nomor_rujukan' => $this->input->post('nomor_rujukan'),
            'tanggal_rujukan' => $this->input->post('tanggal_rujukan'),
            'diagnosis_awal' => $this->input->post('diagnosis_awal'),
            'rekomendasi_pemeriksaan' => $this->input->post('rekomendasi_pemeriksaan')
        );
        
        if ($this->Administrasi_model->update_patient($patient_id, $patient_data)) {
            $this->session->set_flashdata('success', 'Data pasien berhasil diperbarui');
            redirect('administrasi/patient_detail/' . $patient_id);
        } else {
            $this->session->set_flashdata('error', 'Gagal memperbarui data pasien');
        }
    }
}

public function edit_patient_data($patient_id)
{
    $this->form_validation->set_rules('nama', 'Nama', 'required|min_length[2]');
    $this->form_validation->set_rules('nik', 'NIK', 'required|exact_length[16]|numeric|callback_check_nik_unique[' . $patient_id . ']');
    $this->form_validation->set_rules('jenis_kelamin', 'Jenis Kelamin', 'required|in_list[L,P]');
    $this->form_validation->set_rules('tanggal_lahir', 'Tanggal Lahir', 'required');
    $this->form_validation->set_rules('telepon', 'Telepon', 'required|min_length[10]');
    
    if ($this->form_validation->run() === TRUE) {
        // Calculate age
        $birth_date = $this->input->post('tanggal_lahir');
        $age = date_diff(date_create($birth_date), date_create('today'))->y;
        
        $patient_data = array(
            'nama' => $this->input->post('nama'),
            'nik' => $this->input->post('nik'),
            'jenis_kelamin' => $this->input->post('jenis_kelamin'),
            'tempat_lahir' => $this->input->post('tempat_lahir'),
            'tanggal_lahir' => $birth_date,
            'umur' => $age,
            'alamat_domisili' => $this->input->post('alamat_domisili'),
            'pekerjaan' => $this->input->post('pekerjaan'),
            'telepon' => $this->input->post('telepon'),
            'kontak_darurat' => $this->input->post('kontak_darurat'),
            'riwayat_pasien' => $this->input->post('riwayat_pasien'),
            'permintaan_pemeriksaan' => $this->input->post('permintaan_pemeriksaan'),
'dokter_perujuk' => $this->input->post('dokter_perujuk'),
'asal_rujukan' => $this->input->post('asal_rujukan'),
'nomor_rujukan' => $this->input->post('nomor_rujukan'),
'tanggal_rujukan' => $this->input->post('tanggal_rujukan'),
'diagnosis_awal' => $this->input->post('diagnosis_awal'),
'rekomendasi_pemeriksaan' => $this->input->post('rekomendasi_pemeriksaan')
);
        if ($this->Administrasi_model->update_patient($patient_id, $patient_data)) {
            $this->User_model->log_activity(
                $this->session->userdata('user_id'), 
                'Patient updated', 
                'pasien', 
                $patient_id
            );
            $this->session->set_flashdata('success', 'Data pasien berhasil diperbarui');
            echo json_encode(['success' => true]);
        } else {
            echo json_encode(['success' => false]);
        }
    } else {
        echo json_encode(['success' => false, 'errors' => validation_errors()]);
    }
}

public function check_nik_unique($nik, $patient_id)
{
    if ($this->Administrasi_model->check_nik_exists($nik, $patient_id)) {
        $this->form_validation->set_message('check_nik_unique', 'NIK sudah terdaftar');
        return FALSE;
    }
    return TRUE;
}

public function check_nik_exists()
{
    $this->output->set_content_type('application/json');
    
    $nik = $this->input->get('nik');
    $exclude_id = $this->input->get('exclude_id');
    
    if (empty($nik)) {
        $this->output->set_output(json_encode([
            'exists' => false
        ]));
        return;
    }
    
    try {
        $this->db->where('nik', $nik);
        
        if ($exclude_id) {
            $this->db->where('pasien_id !=', $exclude_id);
        }
        
        $patient = $this->db->get('pasien')->row_array();
        
        if ($patient) {
            $this->output->set_output(json_encode([
                'exists' => true,
                'patient' => [
                    'pasien_id' => $patient['pasien_id'],
                    'nama' => $patient['nama'],
                    'nomor_registrasi' => $patient['nomor_registrasi'],
                    'telepon' => $patient['telepon']
                ]
            ]));
        } else {
            $this->output->set_output(json_encode([
                'exists' => false
            ]));
        }
        
    } catch (Exception $e) {
        log_message('error', 'Error checking NIK: ' . $e->getMessage());
        $this->output->set_output(json_encode([
            'exists' => false,
            'error' => 'Gagal memeriksa NIK'
        ]));
    }
}

public function get_patient_data($patient_id = null)
{
    $this->output->set_content_type('application/json');
    
    if (!$patient_id) {
        $patient_id = $this->input->get('patient_id');
    }
    
    if (empty($patient_id) || !is_numeric($patient_id)) {
        $this->output->set_output(json_encode([
            'success' => false,
            'message' => 'ID pasien tidak valid'
        ]));
        return;
    }
    
    try {
        $patient = $this->Administrasi_model->get_patient_by_id($patient_id);
        
        if ($patient) {
            // Format dates for display
            if (!empty($patient['tanggal_lahir'])) {
                $patient['tanggal_lahir_formatted'] = date('d M Y', strtotime($patient['tanggal_lahir']));
            }
            if (!empty($patient['tanggal_rujukan'])) {
                $patient['tanggal_rujukan_formatted'] = date('d M Y', strtotime($patient['tanggal_rujukan']));
            }
            if (!empty($patient['created_at'])) {
                $patient['created_at_formatted'] = date('d M Y H:i', strtotime($patient['created_at']));
            }
            
            $this->output->set_output(json_encode([
                'success' => true,
                'patient' => $patient
            ]));
        } else {
            $this->output->set_output(json_encode([
                'success' => false,
                'message' => 'Pasien tidak ditemukan'
            ]));
        }
    } catch (Exception $e) {
        log_message('error', 'Error getting patient data: ' . $e->getMessage());
        $this->output->set_output(json_encode([
            'success' => false,
            'message' => 'Terjadi kesalahan saat mengambil data'
        ]));
    }
}

// ==================== EXAMINATION REQUEST ====================

public function examination_request()
{
    $data['title'] = 'Permintaan Pemeriksaan';
    
    // Get status filter
    $status = $this->input->get('status') ?? '';
    $page = $this->input->get('page') ? (int)$this->input->get('page') : 1;
    $limit = 10;
    $offset = ($page - 1) * $limit;
    
    try {
        // Get all patients for dropdown
        $data['patients'] = $this->Administrasi_model->get_all_patients();
        
        // Get examination status counts
        $data['counts'] = [
            'pending' => $this->db->where('status_pemeriksaan', 'pending')->count_all_results('pemeriksaan_lab'),
            'progress' => $this->db->where('status_pemeriksaan', 'progress')->count_all_results('pemeriksaan_lab'),
            'selesai' => $this->db->where('status_pemeriksaan', 'selesai')->count_all_results('pemeriksaan_lab'),
        ];
        
        // Count total with filter
        $this->db->select('COUNT(*) as total');
        $this->db->from('pemeriksaan_lab pl');
        if ($status) {
            $this->db->where('pl.status_pemeriksaan', $status);
        }
        $total_result = $this->db->get()->row_array();
        $total_requests = (int)$total_result['total'];
        
        // Get requests based on status filter with pagination
        $this->db->select('pl.*, p.nama as nama_pasien, p.nik, p.nomor_registrasi');
        $this->db->from('pemeriksaan_lab pl');
        $this->db->join('pasien p', 'pl.pasien_id = p.pasien_id');
        
        if ($status) {
            $this->db->where('pl.status_pemeriksaan', $status);
        }
        
        $this->db->order_by('pl.created_at', 'DESC');
        $this->db->limit($limit, $offset);
        $data['requests'] = $this->db->get()->result_array();
        
        // Pagination configuration
        $config = array(
            'base_url' => base_url('administrasi/examination_request'),
            'total_rows' => $total_requests,
            'per_page' => $limit,
            'use_page_numbers' => TRUE,
            'page_query_string' => TRUE,
            'query_string_segment' => 'page',
            'reuse_query_string' => TRUE,
            'num_links' => 2,
            
            'first_link' => 'First',
            'last_link' => 'Last',
            'next_link' => 'Next',
            'prev_link' => 'Prev',
            'full_tag_open' => '<div class="flex space-x-2">',
            'full_tag_close' => '</div>',
            'num_tag_open' => '<button class="px-3 py-1 border border-gray-300 rounded hover:bg-gray-100">',
            'num_tag_close' => '</button>',
            'cur_tag_open' => '<button class="px-3 py-1 border border-blue-500 bg-blue-500 text-white rounded">',
            'cur_tag_close' => '</button>',
            'next_tag_open' => '<button class="px-3 py-1 border border-gray-300 rounded hover:bg-gray-100">',
            'next_tag_close' => '</button>',
            'prev_tag_open' => '<button class="px-3 py-1 border border-gray-300 rounded hover:bg-gray-100">',
            'prev_tag_close' => '</button>',
            'first_tag_open' => '<button class="px-3 py-1 border border-gray-300 rounded hover:bg-gray-100">',
            'first_tag_close' => '</button>',
            'last_tag_open' => '<button class="px-3 py-1 border border-gray-300 rounded hover:bg-gray-100">',
            'last_tag_close' => '</button>',
        );
        
        $this->load->library('pagination');
        $this->pagination->initialize($config);
        
        $data['pagination'] = $this->pagination->create_links();
        $data['total_requests'] = $total_requests;
        $data['current_status'] = $status;
        
    } catch (Exception $e) {
        log_message('error', 'Error getting examination requests: ' . $e->getMessage());
        $data['patients'] = array();
        $data['counts'] = ['pending' => 0, 'progress' => 0, 'selesai' => 0];
        $data['requests'] = array();
        $data['total_requests'] = 0;
        $data['current_status'] = $status;
        $data['pagination'] = '';
    }
    
    // Handle POST request for creating new examination
    if ($this->input->method() === 'post') {
        $this->_handle_create_examination();
        return;
    }
    
    $this->load->view('template/header', $data);
    $this->load->view('template/sidebar', $data);
    $this->load->view('administrasi/examination_request', $data);
    $this->load->view('template/footer');
}

public function edit_examination($exam_id)
{
    $this->form_validation->set_rules('pasien_id', 'Pasien', 'required|numeric');
    $this->form_validation->set_rules('jenis_pemeriksaan', 'Jenis Pemeriksaan', 'required');
    $this->form_validation->set_rules('tanggal_pemeriksaan', 'Tanggal Pemeriksaan', 'required');
    
    if ($this->form_validation->run() === TRUE) {
        $exam_data = array(
            'pasien_id' => $this->input->post('pasien_id'),
            'tanggal_pemeriksaan' => $this->input->post('tanggal_pemeriksaan'),
            'jenis_pemeriksaan' => $this->input->post('jenis_pemeriksaan'),
            'keterangan' => $this->input->post('keterangan'),
            'updated_at' => date('Y-m-d H:i:s')
        );
        
        $this->db->where('pemeriksaan_id', $exam_id);
        $update = $this->db->update('pemeriksaan_lab', $exam_data);
        
        if ($update) {
            $this->User_model->log_activity(
                $this->session->userdata('user_id'), 
                'Examination updated', 
                'pemeriksaan_lab', 
                $exam_id
            );
            
            $this->output
                ->set_content_type('application/json')
                ->set_output(json_encode([
                    'success' => true,
                    'message' => 'Permintaan pemeriksaan berhasil diperbarui'
                ]));
        } else {
            $this->output
                ->set_content_type('application/json')
                ->set_output(json_encode([
                    'success' => false,
                    'message' => 'Gagal memperbarui permintaan pemeriksaan'
                ]));
        }
    } else {
        $this->output
            ->set_content_type('application/json')
            ->set_output(json_encode([
                'success' => false,
                'message' => 'Validasi gagal',
                'errors' => validation_errors()
            ]));
    }
}

private function _handle_create_examination()
{
    $this->form_validation->set_rules('pasien_id', 'Pasien', 'required|numeric');
    $this->form_validation->set_rules('tanggal_pemeriksaan', 'Tanggal Pemeriksaan', 'required');
    $this->form_validation->set_rules('status_pasien', 'Status Pasien', 'required|in_list[puasa,belum_puasa,minum_obat]');
    
    // Validasi jenis pemeriksaan (array)
    $jenis_pemeriksaan = $this->input->post('jenis_pemeriksaan');
    if (empty($jenis_pemeriksaan) || !is_array($jenis_pemeriksaan)) {
        $this->output
            ->set_content_type('application/json')
            ->set_output(json_encode([
                'success' => false,
                'message' => 'Minimal pilih 1 jenis pemeriksaan'
            ]));
        return;
    }
    
    // Validasi sampel (array)
    $sampel = $this->input->post('sampel');
    if (empty($sampel) || !is_array($sampel)) {
        $this->output
            ->set_content_type('application/json')
            ->set_output(json_encode([
                'success' => false,
                'message' => 'Minimal pilih 1 jenis sampel'
            ]));
        return;
    }
    
    if ($this->form_validation->run() === TRUE) {
        $this->db->trans_start();
        
        try {
            // Generate examination number
            $examination_number = $this->Administrasi_model->generate_examination_number();
            
            // Data pemeriksaan utama
            $exam_data = array(
                'pasien_id' => $this->input->post('pasien_id'),
                'nomor_pemeriksaan' => $examination_number,
                'tanggal_pemeriksaan' => $this->input->post('tanggal_pemeriksaan'),
                'jenis_pemeriksaan' => '', // Akan diisi otomatis oleh trigger
                'status_pemeriksaan' => 'pending',
                'status_pasien' => $this->input->post('status_pasien'),
                'keterangan_obat' => $this->input->post('keterangan_obat'),
                'keterangan' => $this->input->post('keterangan'),
                'created_at' => date('Y-m-d H:i:s')
            );
            
            $exam_id = $this->Administrasi_model->create_examination($exam_data);
            
            if (!$exam_id || $exam_id <= 0) {
                throw new Exception('Gagal membuat pemeriksaan');
            }
            
            // Insert detail pemeriksaan (multiple)
            $urutan = 1;
            foreach ($jenis_pemeriksaan as $jenis) {
                $sub_pemeriksaan_key = 'sub_pemeriksaan_' . $urutan;
                $sub_pemeriksaan = $this->input->post($sub_pemeriksaan_key);
                
                $detail_data = array(
                    'pemeriksaan_id' => $exam_id,
                    'jenis_pemeriksaan' => $jenis,
                    'sub_pemeriksaan' => $sub_pemeriksaan ? json_encode($sub_pemeriksaan) : null,
                    'urutan' => $urutan
                );
                
                $this->db->insert('pemeriksaan_detail', $detail_data);
                $urutan++;
            }
            
            // Insert sampel
            foreach ($sampel as $jenis_sampel) {
                $sampel_data = array(
                    'pemeriksaan_id' => $exam_id,
                    'jenis_sampel' => $jenis_sampel
                );
                
                // Jika sampel lain, ambil keterangan
                if ($jenis_sampel === 'lain') {
                    $sampel_data['keterangan_sampel'] = $this->input->post('keterangan_sampel_lain');
                }
                
                $this->db->insert('pemeriksaan_sampel', $sampel_data);
            }
            
            $this->db->trans_complete();
            
            if ($this->db->trans_status() === FALSE) {
                throw new Exception('Transaction failed');
            }
            
            $this->User_model->log_activity(
                $this->session->userdata('user_id'), 
                'Examination request created: ' . $examination_number, 
                'pemeriksaan_lab', 
                $exam_id
            );
            
            $this->output
                ->set_content_type('application/json')
                ->set_output(json_encode([
                    'success' => true,
                    'message' => 'Permintaan pemeriksaan berhasil dibuat dengan nomor: ' . $examination_number,
                    'exam_id' => $exam_id,
                    'nomor_pemeriksaan' => $examination_number
                ]));
                
        } catch (Exception $e) {
            $this->db->trans_rollback();
            log_message('error', 'Exception in _handle_create_examination: ' . $e->getMessage());
            
            $this->output
                ->set_content_type('application/json')
                ->set_output(json_encode([
                    'success' => false,
                    'message' => 'Terjadi kesalahan sistem: ' . $e->getMessage()
                ]));
        }
    } else {
        $this->output
            ->set_content_type('application/json')
            ->set_output(json_encode([
                'success' => false,
                'message' => 'Validasi gagal',
                'errors' => validation_errors()
            ]));
    }
}

// Method untuk get examination data dengan detail lengkap
public function get_examination_data($exam_id)
{
    $this->output->set_content_type('application/json');
    
    try {
        // Get main examination data
        $this->db->select('pl.*, p.nama as nama_pasien, p.nik, p.nomor_registrasi');
        $this->db->from('pemeriksaan_lab pl');
        $this->db->join('pasien p', 'pl.pasien_id = p.pasien_id');
        $this->db->where('pl.pemeriksaan_id', $exam_id);
        
        $examination = $this->db->get()->row_array();
        
        if (!$examination) {
            echo json_encode([
                'success' => false,
                'message' => 'Pemeriksaan tidak ditemukan'
            ]);
            return;
        }
        
        // Get detail pemeriksaan
        $this->db->select('*');
        $this->db->from('pemeriksaan_detail');
        $this->db->where('pemeriksaan_id', $exam_id);
        $this->db->order_by('urutan', 'ASC');
        $examination['detail'] = $this->db->get()->result_array();
        
        // Get sampel
        $this->db->select('*');
        $this->db->from('pemeriksaan_sampel');
        $this->db->where('pemeriksaan_id', $exam_id);
        $examination['sampel'] = $this->db->get()->result_array();
        
        echo json_encode([
            'success' => true,
            'examination' => $examination
        ]);
        
    } catch (Exception $e) {
        log_message('error', 'Error getting examination data: ' . $e->getMessage());
        echo json_encode([
            'success' => false,
            'message' => 'Terjadi kesalahan saat mengambil data'
        ]);
    }
}
}