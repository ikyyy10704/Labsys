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

    /**
     * Administrasi Dashboard
     */
    public function dashboard()
    {
        $data['title'] = 'Dashboard Administrasi';
        
        try {
            // Get financial summary
            $data['financial_summary'] = $this->Administrasi_model->get_financial_summary();
            
            // Get registration statistics
            $data['registration_stats'] = $this->Administrasi_model->get_registration_statistics();
            
            // Get payment statistics
            $data['payment_stats'] = $this->Administrasi_model->get_payment_statistics();
            
            // Get recent registrations
            $data['recent_registrations'] = $this->Administrasi_model->get_recent_registrations(5);
            
            // Get pending payments
            $data['pending_payments'] = $this->Administrasi_model->get_pending_invoices();
            
            // Get recent payments
            $data['recent_payments'] = $this->Administrasi_model->get_recent_payments();
            
        } catch (Exception $e) {
            log_message('error', 'Error loading administrasi dashboard: ' . $e->getMessage());
            $data = array_merge($data, $this->_get_default_admin_data());
        }
        
        $this->load->view('template/header', $data);
        $this->load->view('template/sidebar', $data);
        $this->load->view('administrasi/index', $data);
        $this->load->view('template/footer');
    }

    /**
     * Add Patient Data
     */
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

    /**
     * Patient History
     */
    public function patient_history()
    {
        $data['title'] = 'Riwayat Pasien';
        
        // Search functionality
        $search = $this->input->get('search');
        
        try {
            if ($search) {
                $data['patients'] = $this->Administrasi_model->search_patients($search);
            } else {
                $data['patients'] = $this->Administrasi_model->get_all_patients();
            }
        } catch (Exception $e) {
            log_message('error', 'Error getting patient history: ' . $e->getMessage());
            $data['patients'] = array();
        }
        
        $data['search'] = $search;
        
        $this->load->view('template/header', $data);
        $this->load->view('template/sidebar', $data);
        $this->load->view('administrasi/patient_history', $data);
        $this->load->view('template/footer');
    }

    /**
     * View Patient Detail
     */
    public function patient_detail($patient_id)
    {
        $patient = $this->Administrasi_model->get_patient_by_id($patient_id);
        
        if (!$patient) {
            $this->session->set_flashdata('error', 'Pasien tidak ditemukan');
            redirect('administrasi/patient_history');
        }
        
        $data['title'] = 'Detail Pasien: ' . $patient['nama'];
        $data['patient'] = $patient;
        
        // Get examination history
        try {
            $data['examinations'] = $this->Administrasi_model->get_patient_examination_history($patient_id);
        } catch (Exception $e) {
            log_message('error', 'Error getting patient examinations: ' . $e->getMessage());
            $data['examinations'] = array();
        }
        
        $this->load->view('template/header', $data);
        $this->load->view('template/sidebar', $data);
        $this->load->view('administrasi/patient_detail', $data);
        $this->load->view('template/footer');
    }

    /**
     * Edit Patient
     */
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

    /**
     * Create Invoice - Umum
     */
    public function invoice_umum()
    {
        $data['title'] = 'Buat Invoice Umum';
        
        // Get patients with pending examinations
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

    /**
     * Create Invoice - BPJS
     */
    public function invoice_bpjs()
    {
        $data['title'] = 'Buat Invoice BPJS';
        
        // Get patients with pending examinations
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

    /**
     * View Invoice
     */
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

    /**
     * Print Invoice
     */
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

    /**
     * Process Payment
     */
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

    /**
     * Financial Reports
     */
    public function financial_reports()
    {
        $data['title'] = 'Laporan Keuangan';
        
        try {
            // Get financial summary
            $data['financial_summary'] = $this->Administrasi_model->get_financial_summary();
            
            // Get recent invoices
            $data['recent_invoices'] = $this->Administrasi_model->get_recent_payments();
            
            // Get pending payments
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

    /**
     * Schedule Management
     */
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

    /**
     * Export Data
     */
    public function export_data()
    {
        $data['title'] = 'Export Data';
        
        $this->load->view('template/header', $data);
        $this->load->view('template/sidebar', $data);
        $this->load->view('administrasi/export_data', $data);
        $this->load->view('template/footer');
    }

    /**
     * Export Patients to Excel
     */
    public function export_patients()
    {
        $date_range = $this->input->get('date_range');
        
        // Load PHPSpreadsheet
        require_once APPPATH . '../vendor/autoload.php';
        
        try {
            $query = $this->Administrasi_model->get_patients_for_export($date_range);
            
            $spreadsheet = new \PhpOffice\PhpSpreadsheet\Spreadsheet();
            $sheet = $spreadsheet->getActiveSheet();
            
            // Set headers
            $headers = ['No', 'Nama', 'NIK', 'Jenis Kelamin', 'Tempat Lahir', 'Tanggal Lahir', 'Telepon', 'Alamat', 'Tanggal Registrasi'];
            $col = 1;
            foreach ($headers as $header) {
                $sheet->setCellValueByColumnAndRow($col, 1, $header);
                $col++;
            }
            
            // Add data
            $row = 2;
            $no = 1;
            foreach ($query->result_array() as $patient) {
                $sheet->setCellValueByColumnAndRow(1, $row, $no);
                $sheet->setCellValueByColumnAndRow(2, $row, $patient['nama']);
                $sheet->setCellValueByColumnAndRow(3, $row, $patient['nik']);
                $sheet->setCellValueByColumnAndRow(4, $row, $patient['jenis_kelamin'] === 'L' ? 'Laki-laki' : 'Perempuan');
                $sheet->setCellValueByColumnAndRow(5, $row, $patient['tempat_lahir']);
                $sheet->setCellValueByColumnAndRow(6, $row, $patient['tanggal_lahir']);
                $sheet->setCellValueByColumnAndRow(7, $row, $patient['telepon']);
                $sheet->setCellValueByColumnAndRow(8, $row, $patient['alamat_domisili']);
                $sheet->setCellValueByColumnAndRow(9, $row, $patient['created_at']);
                $row++;
                $no++;
            }
            
            // Download
            $filename = 'Data_Pasien_' . date('Y-m-d_H-i-s') . '.xlsx';
            header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
            header('Content-Disposition: attachment;filename="' . $filename . '"');
            header('Cache-Control: max-age=0');
            
            $writer = new \PhpOffice\PhpSpreadsheet\Writer\Xlsx($spreadsheet);
            $writer->save('php://output');
            
        } catch (Exception $e) {
            log_message('error', 'Error exporting patients: ' . $e->getMessage());
            $this->session->set_flashdata('error', 'Gagal mengexport data pasien');
            redirect('administrasi/export_data');
        }
    }

    // =============================
    // PRIVATE METHODS
    // =============================

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
                $this->User_model->log_activity($this->session->userdata('user_id'), 'New patient registered', 'pasien', $patient_id);
                $this->session->set_flashdata('success', 'Pasien berhasil didaftarkan dengan nomor registrasi: ' . $registration_number);
                redirect('administrasi/add_patient_data');
            } else {
                $this->session->set_flashdata('error', 'Gagal mendaftarkan pasien');
            }
        }
    }

    private function _handle_edit_patient($patient_id)
    {
        // Validation rules (exclude current patient from NIK unique check)
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
                $this->User_model->log_activity($this->session->userdata('user_id'), 'Patient updated', 'pasien', $patient_id);
                $this->session->set_flashdata('success', 'Data pasien berhasil diperbarui');
                redirect('administrasi/patient_detail/' . $patient_id);
            } else {
                $this->session->set_flashdata('error', 'Gagal memperbarui data pasien');
            }
        }
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

    public function check_nik_unique($nik, $patient_id)
    {
        if ($this->Administrasi_model->check_nik_exists($nik, $patient_id)) {
            $this->form_validation->set_message('check_nik_unique', 'NIK sudah terdaftar');
            return FALSE;
        }
        return TRUE;
    }

    private function _get_default_admin_data()
    {
        return array(
            'financial_summary' => array(),
            'registration_stats' => array(),
            'payment_stats' => array(),
            'recent_registrations' => array(),
            'pending_payments' => array(),
            'recent_payments' => array()
        );
    }
}