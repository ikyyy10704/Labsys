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
        
        // TAMBAHAN: Payment Method Stats & Examination Stats (PINDAHKAN KE SINI)
        $data['payment_method_stats'] = $this->Administrasi_laporan_model->get_payment_method_statistics([
            'start_date' => date('Y-m-01'), // Awal bulan ini
            'end_date' => date('Y-m-d')      // Hari ini
        ]);
        
        // Get examination statistics for Total Permintaan card
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
        $search = $this->input->get('search');
        $page = $this->input->get('page') ? (int)$this->input->get('page') : 1;
        $limit = 10;
        $offset = ($page - 1) * $limit;
        
        try {
            $total_patients = $this->Administrasi_model->count_patients($search);
            $data['patients'] = $this->Administrasi_model->get_patients_paginated($search, $limit, $offset);
            
            // Pagination configuration
            $config['base_url'] = base_url('administrasi/patient_history');
            $config['total_rows'] = $total_patients;
            $config['per_page'] = $limit;
            $config['use_page_numbers'] = TRUE;
            $config['page_query_string'] = TRUE;
            $config['query_string_segment'] = 'page';
            $config['reuse_query_string'] = TRUE;
            
            // Pagination styling
            $config['full_tag_open'] = '<div class="flex space-x-2">';
            $config['full_tag_close'] = '</div>';
            $config['first_link'] = 'First';
            $config['last_link'] = 'Last';
            $config['next_link'] = 'Next';
            $config['prev_link'] = 'Prev';
            $config['num_tag_open'] = '<button class="px-3 py-1 border border-gray-300 rounded hover:bg-gray-100">';
            $config['num_tag_close'] = '</button>';
            $config['cur_tag_open'] = '<button class="px-3 py-1 border border-blue-500 bg-blue-500 text-white rounded">';
            $config['cur_tag_close'] = '</button>';
            $config['next_tag_open'] = '<button class="px-3 py-1 border border-gray-300 rounded hover:bg-gray-100">';
            $config['next_tag_close'] = '</button>';
            $config['prev_tag_open'] = '<button class="px-3 py-1 border border-gray-300 rounded hover:bg-gray-100">';
            $config['prev_tag_close'] = '</button>';
            
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

    public function check_nik_unique($nik, $patient_id)
    {
        if ($this->Administrasi_model->check_nik_exists($nik, $patient_id)) {
            $this->form_validation->set_message('check_nik_unique', 'NIK sudah terdaftar');
            return FALSE;
        }
        return TRUE;
    }

    public function delete_patient($patient_id)
    {
        // Check if patient exists
        $patient = $this->Administrasi_model->get_patient_by_id($patient_id);
        if (!$patient) {
            $this->session->set_flashdata('error', 'Pasien tidak ditemukan');
            redirect('administrasi/patient_history');
        }
        
        // Check if patient has related records
        $has_examinations = $this->Administrasi_model->check_patient_has_examinations($patient_id);
        $has_invoices = $this->Administrasi_model->check_patient_has_invoices($patient_id);
        
        if ($has_examinations || $has_invoices) {
            $this->session->set_flashdata('error', 'Tidak dapat menghapus pasien karena memiliki data pemeriksaan atau invoice terkait');
            redirect('administrasi/patient_detail/' . $patient_id);
        }
        
        if ($this->Administrasi_model->delete_patient($patient_id)) {
            $this->User_model->log_activity($this->session->userdata('user_id'), 'Patient deleted', 'pasien', $patient_id);
            $this->session->set_flashdata('success', 'Pasien berhasil dihapus');
        } else {
            $this->session->set_flashdata('error', 'Gagal menghapus pasien');
        }
        
        redirect('administrasi/patient_history');
    }

    public function patient_requests()
    {
        $data['title'] = 'Permintaan Pasien';
        
        // Get status filter
        $status = $this->input->get('status');
        
        try {
            // Get counts for all statuses
            $data['counts'] = $this->Administrasi_model->get_requests_count_by_status();
            
            // Get requests based on status filter
            $data['requests'] = $this->Administrasi_model->get_patient_requests($status);
            
            $data['total_requests'] = count($data['requests']);
            $data['current_status'] = $status;
            
            $data['pagination'] = '';
            
        } catch (Exception $e) {
            log_message('error', 'Error getting patient requests: ' . $e->getMessage());
            $data['counts'] = [
                'pending' => 0,
                'approved' => 0,
                'rejected' => 0,
                'completed' => 0
            ];
            $data['requests'] = array();
            $data['total_requests'] = 0;
            $data['current_status'] = $status;
            $data['pagination'] = '';
        }
        
        $this->load->view('template/header', $data);
        $this->load->view('template/sidebar', $data);
        $this->load->view('administrasi/patient_requests', $data);
        $this->load->view('template/footer');
    }

    public function view_request($request_id)
    {
        $request = $this->Administrasi_model->get_request_by_id($request_id);
        
        if (!$request) {
            $this->session->set_flashdata('error', 'Permintaan tidak ditemukan');
            redirect('administrasi/patient_requests');
        }
        
        $data['title'] = 'Detail Permintaan: REQ' . str_pad($request_id, 4, '0', STR_PAD_LEFT);
        $data['request'] = $request;
        
        $this->load->view('template/header', $data);
        $this->load->view('template/sidebar', $data);
        $this->load->view('administrasi/view_request', $data);
        $this->load->view('template/footer');
    }

    public function process_request($request_id)
    {
        $request = $this->Administrasi_model->get_request_by_id($request_id);
        
        if (!$request) {
            $this->session->set_flashdata('error', 'Permintaan tidak ditemukan');
            redirect('administrasi/patient_requests');
        }
        
        if ($this->input->method() === 'post') {
            $this->_handle_process_request($request_id);
        }
        
        $data['title'] = 'Proses Permintaan';
        $data['request'] = $request;
        
        $this->load->view('template/header', $data);
        $this->load->view('template/sidebar', $data);
        $this->load->view('administrasi/process_request', $data);
        $this->load->view('template/footer');
    }

    private function _handle_process_request($request_id)
    {
        $this->form_validation->set_rules('status', 'Status', 'required|in_list[approved,rejected,completed]');
        
        if ($this->form_validation->run() === TRUE) {
            $update_data = array(
                'status' => $this->input->post('status'),
                'catatan' => $this->input->post('catatan'),
                'processed_by' => $this->session->userdata('user_id'),
                'processed_at' => date('Y-m-d H:i:s')
            );
            
            if ($this->Administrasi_model->update_request_status($request_id, $update_data)) {
                $this->User_model->log_activity(
                    $this->session->userdata('user_id'), 
                    'Patient request processed', 
                    'patient_requests', 
                    $request_id
                );
                $this->session->set_flashdata('success', 'Permintaan berhasil diproses');
                redirect('administrasi/patient_requests');
            } else {
                $this->session->set_flashdata('error', 'Gagal memproses permintaan');
            }
        }
    }

    public function create_request()
    {
        $data['title'] = 'Buat Permintaan Baru';
        
        try {
            $data['patients'] = $this->Administrasi_model->get_all_patients();
        } catch (Exception $e) {
            log_message('error', 'Error loading patients: ' . $e->getMessage());
            $data['patients'] = array();
        }
        
        if ($this->input->method() === 'post') {
            $this->_handle_create_request();
        }
        
        $this->load->view('template/header', $data);
        $this->load->view('template/sidebar', $data);
        $this->load->view('administrasi/create_request', $data);
        $this->load->view('template/footer');
    }

    private function _handle_create_request()
    {
        $this->form_validation->set_rules('pasien_id', 'Pasien', 'required|numeric');
        $this->form_validation->set_rules('judul_permintaan', 'Judul Permintaan', 'required');
        $this->form_validation->set_rules('deskripsi_permintaan', 'Deskripsi', 'required');
        $this->form_validation->set_rules('prioritas', 'Prioritas', 'required|in_list[low,medium,high,urgent]');
        
        if ($this->form_validation->run() === TRUE) {
            $request_data = array(
                'pasien_id' => $this->input->post('pasien_id'),
                'judul_permintaan' => $this->input->post('judul_permintaan'),
                'deskripsi_permintaan' => $this->input->post('deskripsi_permintaan'),
                'prioritas' => $this->input->post('prioritas'),
                'status' => 'pending',
                'created_by' => $this->session->userdata('user_id'),
                'created_at' => date('Y-m-d H:i:s')
            );
            
            $this->db->insert('patient_requests', $request_data);
            $request_id = $this->db->insert_id();
            
            if ($request_id) {
                $this->User_model->log_activity(
                    $this->session->userdata('user_id'), 
                    'Patient request created', 
                    'patient_requests', 
                    $request_id
                );
                $this->session->set_flashdata('success', 'Permintaan berhasil dibuat');
                redirect('administrasi/patient_requests');
            } else {
                $this->session->set_flashdata('error', 'Gagal membuat permintaan');
            }
        }
    }

    // Invoice functions - keeping only if views exist
    public function invoice_umum()
    {
        $data['title'] = 'Buat Invoice Umum';
        
        try {
            $data['pending_exams'] = $this->Administrasi_model->get_patients_with_pending_exams();
        } catch (Exception $e) {
            log_message('error', 'Error getting pending exams: ' . $e->getMessage());
            $data['pending_exams'] = array();
        }
        
        if ($this->input->method() === 'post') {
            $this->_handle_create_invoice('umum');
        }
        
        $this->load->view('template/header', $data);
        $this->load->view('template/sidebar', $data);
        $this->load->view('administrasi/invoice_umum', $data);
        $this->load->view('template/footer');
    }

    public function invoice_bpjs()
    {
        $data['title'] = 'Buat Invoice BPJS';
        
        try {
            $data['pending_exams'] = $this->Administrasi_model->get_patients_with_pending_exams();
        } catch (Exception $e) {
            log_message('error', 'Error getting pending exams: ' . $e->getMessage());
            $data['pending_exams'] = array();
        }
        
        if ($this->input->method() === 'post') {
            $this->_handle_create_invoice('bpjs');
        }
        
        $this->load->view('template/header', $data);
        $this->load->view('template/sidebar', $data);
        $this->load->view('administrasi/invoice_bpjs', $data);
        $this->load->view('template/footer');
    }

    public function view_invoice($invoice_id)
    {
        $invoice = $this->Administrasi_model->get_invoice_by_id($invoice_id);
        
        if (!$invoice) {
            $this->session->set_flashdata('error', 'Invoice tidak ditemukan');
            redirect('administrasi/financial_reports');
        }
        
        $data['title'] = 'Invoice: ' . $invoice['nomor_invoice'];
        $data['invoice'] = $invoice;
        
        $this->load->view('template/header', $data);
        $this->load->view('template/sidebar', $data);
        $this->load->view('administrasi/view_invoice', $data);
        $this->load->view('template/footer');
    }

    public function print_invoice($invoice_id)
    {
        $invoice = $this->Administrasi_model->get_invoice_by_id($invoice_id);
        
        if (!$invoice) {
            $this->session->set_flashdata('error', 'Invoice tidak ditemukan');
            redirect('administrasi/financial_reports');
        }
        
        $data['invoice'] = $invoice;
        $this->load->view('administrasi/print_invoice', $data);
    }

    public function process_payment($invoice_id)
    {
        $invoice = $this->Administrasi_model->get_invoice_by_id($invoice_id);
        
        if (!$invoice) {
            $this->session->set_flashdata('error', 'Invoice tidak ditemukan');
            redirect('administrasi/financial_reports');
        }
        
        if ($invoice['status_pembayaran'] === 'lunas') {
            $this->session->set_flashdata('info', 'Invoice sudah lunas');
            redirect('administrasi/view_invoice/' . $invoice_id);
        }
        
        $data['title'] = 'Proses Pembayaran';
        $data['invoice'] = $invoice;
        
        if ($this->input->method() === 'post') {
            $this->_handle_process_payment($invoice_id);
        }
        
        $this->load->view('template/header', $data);
        $this->load->view('template/sidebar', $data);
        $this->load->view('administrasi/process_payment', $data);
        $this->load->view('template/footer');
    }

    private function _handle_create_invoice($payment_type)
    {
        $this->form_validation->set_rules('pemeriksaan_id', 'Pemeriksaan', 'required|numeric');
        $this->form_validation->set_rules('total_biaya', 'Total Biaya', 'required|numeric|greater_than[0]');
        
        if ($payment_type === 'bpjs') {
            $this->form_validation->set_rules('nomor_kartu_bpjs', 'Nomor Kartu BPJS', 'required');
            $this->form_validation->set_rules('nomor_sep', 'Nomor SEP', 'required');
        }
        
        if ($this->form_validation->run() === TRUE) {
            // Generate invoice number
            $prefix = 'INV' . date('Ymd');
            $last_invoice = $this->Administrasi_model->get_last_invoice_number($prefix);
            
            if ($last_invoice) {
                $last_number = intval(substr($last_invoice['nomor_invoice'], -3));
                $new_number = $last_number + 1;
            } else {
                $new_number = 1;
            }
            
            $invoice_number = $prefix . str_pad($new_number, 3, '0', STR_PAD_LEFT);
            
            $invoice_data = array(
                'pemeriksaan_id' => $this->input->post('pemeriksaan_id'),
                'nomor_invoice' => $invoice_number,
                'tanggal_invoice' => date('Y-m-d'),
                'jenis_pembayaran' => $payment_type,
                'total_biaya' => $this->input->post('total_biaya'),
                'status_pembayaran' => 'belum_bayar',
                'keterangan' => $this->input->post('keterangan'),
                'created_at' => date('Y-m-d H:i:s')
            );
            
            if ($payment_type === 'bpjs') {
                $invoice_data['nomor_kartu_bpjs'] = $this->input->post('nomor_kartu_bpjs');
                $invoice_data['nomor_sep'] = $this->input->post('nomor_sep');
            }
            
            $invoice_id = $this->Administrasi_model->create_invoice($invoice_data);
            
            if ($invoice_id) {
                $this->User_model->log_activity($this->session->userdata('user_id'), 'Invoice created', 'invoice', $invoice_id);
                $this->session->set_flashdata('success', 'Invoice berhasil dibuat dengan nomor: ' . $invoice_number);
                redirect('administrasi/view_invoice/' . $invoice_id);
            } else {
                $this->session->set_flashdata('error', 'Gagal membuat invoice');
            }
        }
    }

    private function _handle_process_payment($invoice_id)
    {
        $this->form_validation->set_rules('metode_pembayaran', 'Metode Pembayaran', 'required');
        $this->form_validation->set_rules('tanggal_pembayaran', 'Tanggal Pembayaran', 'required');
        
        if ($this->form_validation->run() === TRUE) {
            $payment_data = array(
                'status_pembayaran' => 'lunas',
                'metode_pembayaran' => $this->input->post('metode_pembayaran'),
                'tanggal_pembayaran' => $this->input->post('tanggal_pembayaran'),
                'keterangan' => $this->input->post('keterangan_pembayaran')
            );
            
            if ($this->Administrasi_model->update_payment_status($invoice_id, $payment_data)) {
                $this->User_model->log_activity($this->session->userdata('user_id'), 'Payment processed', 'invoice', $invoice_id);
                $this->session->set_flashdata('success', 'Pembayaran berhasil diproses');
                redirect('administrasi/view_invoice/' . $invoice_id);
            } else {
                $this->session->set_flashdata('error', 'Gagal memproses pembayaran');
            }
        }
    }

    public function financial_reports()
    {
        $data['title'] = 'Laporan Keuangan';
        
        try {
            $data['financial_summary'] = $this->Administrasi_model->get_financial_summary();
            $data['recent_invoices'] = $this->Administrasi_model->get_recent_payments();
            $data['pending_payments'] = $this->Administrasi_model->get_pending_invoices();
            
        } catch (Exception $e) {
            log_message('error', 'Error getting financial reports: ' . $e->getMessage());
            $data['financial_summary'] = array();
            $data['recent_invoices'] = array();
            $data['pending_payments'] = array();
        }
        
        $this->load->view('template/header', $data);
        $this->load->view('template/sidebar', $data);
        $this->load->view('administrasi/financial_reports', $data);
        $this->load->view('template/footer');
    }

    public function export_data()
    {
        $data['title'] = 'Export Data';
        
        $this->load->view('template/header', $data);
        $this->load->view('template/sidebar', $data);
        $this->load->view('administrasi/export_data', $data);
        $this->load->view('template/footer');
    }

    // Schedule functions - only if views exist
    public function schedule()
    {
        $data['title'] = 'Jadwal Pemeriksaan';
        
        try {
            $data['appointments'] = $this->Administrasi_model->get_appointments();
        } catch (Exception $e) {
            log_message('error', 'Error getting appointments: ' . $e->getMessage());
            $data['appointments'] = array();
        }
        
        $this->load->view('template/header', $data);
        $this->load->view('template/sidebar', $data);
        $this->load->view('administrasi/schedule', $data);
        $this->load->view('template/footer');
    }
    public function examination_request()
{
    $data['title'] = 'Permintaan Pemeriksaan';
    
    // Get status filter
    $status = $this->input->get('status');
    
    try {
        // Get all patients for dropdown
        $data['patients'] = $this->Administrasi_model->get_all_patients();
        
        // Get examination status counts
        $data['counts'] = [
            'pending' => $this->db->where('status_pemeriksaan', 'pending')->count_all_results('pemeriksaan_lab'),
            'progress' => $this->db->where('status_pemeriksaan', 'progress')->count_all_results('pemeriksaan_lab'),
            'selesai' => $this->db->where('status_pemeriksaan', 'selesai')->count_all_results('pemeriksaan_lab'),
        ];
        
        // Get requests based on status filter
        if ($status) {
            $this->db->where('pl.status_pemeriksaan', $status);
        }
        
        $this->db->select('pl.*, p.nama as nama_pasien, p.nik, p.nomor_registrasi');
        $this->db->from('pemeriksaan_lab pl');
        $this->db->join('pasien p', 'pl.pasien_id = p.pasien_id');
        $this->db->order_by('pl.created_at', 'DESC');
        $data['requests'] = $this->db->get()->result_array();
        
        $data['total_requests'] = count($data['requests']);
        $data['current_status'] = $status;
        $data['pagination'] = '';
        
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

private function _handle_create_examination()
{
    $this->form_validation->set_rules('pasien_id', 'Pasien', 'required|numeric');
    $this->form_validation->set_rules('jenis_pemeriksaan', 'Jenis Pemeriksaan', 'required');
    $this->form_validation->set_rules('tanggal_pemeriksaan', 'Tanggal Pemeriksaan', 'required');
    $this->form_validation->set_rules('biaya', 'Biaya', 'required|numeric|greater_than[0]');
    
    if ($this->form_validation->run() === TRUE) {
        // Generate examination number
        $prefix = 'LAB' . date('Y');
        $last_exam = $this->Administrasi_model->get_last_examination_number($prefix);
        
        if ($last_exam) {
            $last_number = intval(substr($last_exam['nomor_pemeriksaan'], -4));
            $new_number = $last_number + 1;
        } else {
            $new_number = 1;
        }
        
        $examination_number = $prefix . str_pad($new_number, 4, '0', STR_PAD_LEFT);
        
        $exam_data = array(
            'pasien_id' => $this->input->post('pasien_id'),
            'nomor_pemeriksaan' => $examination_number,
            'tanggal_pemeriksaan' => $this->input->post('tanggal_pemeriksaan'),
            'jenis_pemeriksaan' => $this->input->post('jenis_pemeriksaan'),
            'status_pemeriksaan' => 'pending',
            'biaya' => $this->input->post('biaya'),
            'keterangan' => $this->input->post('keterangan'),
            'created_at' => date('Y-m-d H:i:s')
        );
        
        $exam_id = $this->Administrasi_model->create_examination($exam_data);
        
        if ($exam_id) {
            $this->User_model->log_activity(
                $this->session->userdata('user_id'), 
                'Examination request created', 
                'pemeriksaan_lab', 
                $exam_id
            );
            $this->session->set_flashdata('success', 'Permintaan pemeriksaan berhasil dibuat dengan nomor: ' . $examination_number);
        } else {
            $this->session->set_flashdata('error', 'Gagal membuat permintaan pemeriksaan');
        }
        
        echo json_encode(['success' => $exam_id ? true : false]);
        return;
    } else {
        echo json_encode(['success' => false, 'errors' => validation_errors()]);
        return;
    }
}

public function update_examination_status($exam_id)
{
    $status = $this->input->post('status');
    
    if ($this->Administrasi_model->update_examination_status($exam_id, $status)) {
        $this->User_model->log_activity(
            $this->session->userdata('user_id'), 
            'Examination status updated', 
            'pemeriksaan_lab', 
            $exam_id
        );
        echo json_encode(['success' => true]);
    } else {
        echo json_encode(['success' => false]);
    }
}

public function view_examination($exam_id)
{
    $exam = $this->Administrasi_model->get_examination_by_id($exam_id);
    
    if (!$exam) {
        $this->session->set_flashdata('error', 'Pemeriksaan tidak ditemukan');
        redirect('administrasi/examination_request');
    }
    
    $data['title'] = 'Detail Pemeriksaan: ' . $exam['nomor_pemeriksaan'];
    $data['exam'] = $exam;
    
    $this->load->view('template/header', $data);
    $this->load->view('template/sidebar', $data);
    $this->load->view('administrasi/view_examination', $data);
    $this->load->view('template/footer');
}
// Get examination data for edit/detail
public function get_examination_data($exam_id)
{
    $this->db->select('pl.*, p.nama as nama_pasien, p.nik, p.nomor_registrasi');
    $this->db->from('pemeriksaan_lab pl');
    $this->db->join('pasien p', 'pl.pasien_id = p.pasien_id');
    $this->db->where('pl.pemeriksaan_id', $exam_id);
    
    $examination = $this->db->get()->row_array();
    
    if ($examination) {
        echo json_encode([
            'success' => true,
            'examination' => $examination
        ]);
    } else {
        echo json_encode([
            'success' => false,
            'message' => 'Pemeriksaan tidak ditemukan'
        ]);
    }
}

// Edit examination
public function edit_examination($exam_id)
{
    $this->form_validation->set_rules('pasien_id', 'Pasien', 'required|numeric');
    $this->form_validation->set_rules('jenis_pemeriksaan', 'Jenis Pemeriksaan', 'required');
    $this->form_validation->set_rules('tanggal_pemeriksaan', 'Tanggal Pemeriksaan', 'required');
    $this->form_validation->set_rules('biaya', 'Biaya', 'required|numeric|greater_than[0]');
    
    if ($this->form_validation->run() === TRUE) {
        $exam_data = array(
            'pasien_id' => $this->input->post('pasien_id'),
            'tanggal_pemeriksaan' => $this->input->post('tanggal_pemeriksaan'),
            'jenis_pemeriksaan' => $this->input->post('jenis_pemeriksaan'),
            'biaya' => $this->input->post('biaya'),
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
            echo json_encode(['success' => true]);
        } else {
            echo json_encode(['success' => false]);
        }
    } else {
        echo json_encode(['success' => false, 'errors' => validation_errors()]);
    }
}
// ==================== PATIENT MANAGEMENT FUNCTIONS ====================

/**
 * Patient Management - Main page
 */
public function patient_management()
{
    $data['title'] = 'Kelola Pasien';
    
    // Search functionality
    $search = $this->input->get('search');
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
        $config['base_url'] = base_url('administrasi/patient_management');
        $config['total_rows'] = $total_patients;
        $config['per_page'] = $limit;
        $config['use_page_numbers'] = TRUE;
        $config['page_query_string'] = TRUE;
        $config['query_string_segment'] = 'page';
        $config['reuse_query_string'] = TRUE;
        
        // Pagination styling
        $config['full_tag_open'] = '<div class="flex space-x-2">';
        $config['full_tag_close'] = '</div>';
        $config['first_link'] = 'First';
        $config['last_link'] = 'Last';
        $config['next_link'] = 'Next';
        $config['prev_link'] = 'Prev';
        $config['num_tag_open'] = '<button class="px-3 py-1 border border-gray-300 rounded hover:bg-gray-100">';
        $config['num_tag_close'] = '</button>';
        $config['cur_tag_open'] = '<button class="px-3 py-1 border border-blue-500 bg-blue-500 text-white rounded">';
        $config['cur_tag_close'] = '</button>';
        $config['next_tag_open'] = '<button class="px-3 py-1 border border-gray-300 rounded hover:bg-gray-100">';
        $config['next_tag_close'] = '</button>';
        $config['prev_tag_open'] = '<button class="px-3 py-1 border border-gray-300 rounded hover:bg-gray-100">';
        $config['prev_tag_close'] = '</button>';
        
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
    
    // Handle POST request for creating new patient
    if ($this->input->method() === 'post') {
        $this->_handle_create_patient_ajax();
        return;
    }
    
    $this->load->view('template/header', $data);
    $this->load->view('template/sidebar', $data);
    $this->load->view('administrasi/patient_management', $data);
    $this->load->view('template/footer');
}

/**
 * Handle create patient via AJAX
 */
private function _handle_create_patient_ajax()
{
    // Validation rules - sama dengan add_patient
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
            $this->User_model->log_activity(
                $this->session->userdata('user_id'), 
                'Patient created', 
                'pasien', 
                $patient_id
            );
            $this->session->set_flashdata('success', 'Pasien berhasil didaftarkan dengan nomor registrasi: ' . $registration_number);
            echo json_encode(['success' => true]);
        } else {
            echo json_encode(['success' => false]);
        }
    } else {
        echo json_encode(['success' => false, 'errors' => validation_errors()]);
    }
}

/**
 * Get patient data for detail/edit
 */
public function get_patient_data($patient_id)
{
    $patient = $this->Administrasi_model->get_patient_by_id($patient_id);
    
    if ($patient) {
        echo json_encode([
            'success' => true,
            'patient' => $patient
        ]);
    } else {
        echo json_encode([
            'success' => false,
            'message' => 'Pasien tidak ditemukan'
        ]);
    }
}

/**
 * Edit patient data
 */
public function edit_patient_data($patient_id)
{
    $patient = $this->Administrasi_model->get_patient_by_id($patient_id);
    
    if (!$patient) {
        echo json_encode([
            'success' => false,
            'message' => 'Pasien tidak ditemukan'
        ]);
        return;
    }
    
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
// Tambahkan di Administrasi.php
public function ajax_get_revenue_chart_data() {
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
}