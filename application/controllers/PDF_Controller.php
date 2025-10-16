<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class PDF_Controller extends CI_Controller {

    public function __construct()
    {
        parent::__construct();
        
        if (!$this->session->userdata('logged_in')) {
            $this->session->set_flashdata('error', 'Akses ditolak. Silakan login terlebih dahulu.');
            redirect('auth/login');
        }
        
        $this->load->model(['PDF_model']);
        $this->load->helper(['form', 'url', 'date']);
    }
    public function print_examination_result($examination_id)
    {
        try {
            if (empty($examination_id) || !is_numeric($examination_id)) {
                show_404();
                return;
            }
            
            $examination_id = (int)$examination_id;
            
            $examination = $this->PDF_model->get_examination_detail($examination_id);
            
            if (!$examination) {
                show_404();
                return;
            }
            
            $results = $this->PDF_model->get_examination_results($examination_id, $examination['jenis_pemeriksaan']);
            $invoice_data = $this->PDF_model->get_invoice_data_safe($examination_id);
            
            if (!$invoice_data) {
                $invoice_data = $this->PDF_model->create_invoice_for_examination_safe($examination_id);
            }
            
            $logo_info = $this->PDF_model->get_logo_info();
            $lab_info = $this->PDF_model->get_lab_info();
            
            $data = array(
                'title' => 'Hasil Pemeriksaan - ' . $examination['nomor_pemeriksaan'],
                'examination' => $examination,
                'results' => $results,
                'invoice' => $invoice_data,
                'logo_info' => $logo_info,
                'lab_info' => $lab_info,
                'print_date' => date('d F Y, H:i:s'),
                'current_user' => $this->session->userdata('username')
            );
            $completeness = $this->PDF_model->check_results_completeness(
    $examination_id, 
    $examination['jenis_pemeriksaan']
);

$data['completeness'] = $completeness;
            
            $this->PDF_model->log_activity(
                $this->session->userdata('user_id'),
                'Hasil pemeriksaan dicetak: ' . $examination['nomor_pemeriksaan'],
                'pemeriksaan_lab',
                $examination_id
            );
            
            $this->load->view('admin/print_examination_result', $data);
            
        } catch (Exception $e) {
            log_message('error', 'Error printing examination result: ' . $e->getMessage());
            show_error('Terjadi kesalahan saat memuat hasil pemeriksaan: ' . $e->getMessage(), 500);
        }
    }

    public function print_invoice($invoice_id)
    {
        try {
            if (empty($invoice_id) || !is_numeric($invoice_id)) {
                show_404();
                return;
            }
            
            $invoice_id = (int)$invoice_id;
            
            $invoice = $this->PDF_model->get_invoice_detail($invoice_id);
            
            if (!$invoice) {
                show_404();
                return;
            }
            
            $examination = $this->PDF_model->get_examination_by_invoice($invoice_id);
            $logo_info = $this->PDF_model->get_logo_info();
            $lab_info = $this->PDF_model->get_lab_info();
            
            $data = array(
                'title' => 'Invoice - ' . $invoice['nomor_invoice'],
                'invoice' => $invoice,
                'examination' => $examination,
                'logo_info' => $logo_info,
                'lab_info' => $lab_info,
                'print_date' => date('d F Y, H:i:s'),
                'current_user' => $this->session->userdata('username')
            );
            
            $this->PDF_model->log_activity(
                $this->session->userdata('user_id'),
                'Invoice dicetak: ' . $invoice['nomor_invoice'],
                'invoice',
                $invoice_id
            );
            
            $this->load->view('admin/print_invoice', $data);
            
        } catch (Exception $e) {
            log_message('error', 'Error printing invoice: ' . $e->getMessage());
            show_error('Terjadi kesalahan saat memuat invoice: ' . $e->getMessage(), 500);
        }
    }
}