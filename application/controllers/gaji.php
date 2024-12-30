<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Gaji extends CI_Controller {
    public function __construct() {
        parent::__construct();
        $this->load->model('Gaji_model');
        $this->load->helper(['url', 'form']);
        $this->load->library(['form_validation', 'session']);
    }

    public function index() {
        $data['title'] = 'Pengelolaan Gaji';
        $data['gaji'] = $this->Gaji_model->get_all_gaji();
        
        $this->load->view('template/header', $data);
        $this->load->view('template/sidebar');
        $this->load->view('gaji/index', $data);
        $this->load->view('template/footer');
    }
    public function create() {
            $this->form_validation->set_rules('id_krywn', 'ID Karyawan', 'required');
            $this->form_validation->set_rules('tgl_gaji', 'Tanggal', 'required'); 
            $this->form_validation->set_rules('gaji_pokok', 'Gaji Pokok', 'required|numeric');
            $this->form_validation->set_rules('tunjangan', 'Tunjangan', 'required|numeric');
            $this->form_validation->set_rules('bonus', 'Bonus', 'required|numeric');
            $this->form_validation->set_rules('potongan', 'Potongan', 'required|numeric');

        if ($this->form_validation->run() === FALSE) {
            $data['title'] = 'Tambah Data Gaji';
            $data['karyawan'] = $this->Gaji_model->get_all_karyawan();
            
            $this->load->view('template/header', $data);
            $this->load->view('template/sidebar');
            $this->load->view('template/footer');
            $this->load->view('gaji/create', $data);
        } else {
            $this->Gaji_model->create_gaji();
            $this->session->set_flashdata('success', 'Data gaji berhasil ditambahkan');
            redirect('gaji');
        }
    }

    public function edit($id = null, $tanggal = null) {
        if($id === null || $tanggal === null) {
            $this->session->set_flashdata('error', 'Data tidak lengkap');
            redirect('gaji');
        }
    
        $this->form_validation->set_rules('tgl_gaji', 'Tanggal', 'required');
        $this->form_validation->set_rules('gaji_pokok', 'Gaji Pokok', 'required|numeric');
        $this->form_validation->set_rules('tunjangan', 'Tunjangan', 'required|numeric');
        $this->form_validation->set_rules('bonus', 'Bonus', 'required|numeric');
        $this->form_validation->set_rules('potongan', 'Potongan', 'required|numeric');
    
        if ($this->form_validation->run() === FALSE) {
            $data['title'] = 'Edit Data Gaji';
            $data['gaji'] = $this->Gaji_model->get_gaji_by_id_and_date($id, urldecode($tanggal));
            
            if(!$data['gaji']) {
                $this->session->set_flashdata('error', 'Data gaji tidak ditemukan');
                redirect('gaji'); 
            }
            $this->load->view('template/header', $data);
            $this->load->view('template/sidebar');
            $this->load->view('template/footer');
            $this->load->view('gaji/edit', $data);
        } else {
            $update_data = array(
                'tgl_gaji' => $this->input->post('tgl_gaji'),
                'gaji_pokok' => $this->input->post('gaji_pokok'),
                'tunjangan' => $this->input->post('tunjangan'),
                'bonus' => $this->input->post('bonus'),
                'potongan' => $this->input->post('potongan')
            );
    
            if($this->Gaji_model->update_gaji_by_id_and_date($id, $tanggal, $update_data)) {
                $this->session->set_flashdata('success', 'Data gaji berhasil diupdate');
            } else {
                $this->session->set_flashdata('error', 'Gagal mengupdate data gaji');
            }
            redirect('gaji'); 
        }
    }

    public function delete($id = null, $tanggal = null) {
        if($id === null || $tanggal === null) {
            $this->session->set_flashdata('error', 'Data tidak lengkap');
            redirect('gaji');
        }
    
        $gaji = $this->Gaji_model->get_gaji_by_id_and_date($id, urldecode($tanggal));
        if (!$gaji) {
            $this->session->set_flashdata('error', 'Data gaji tidak ditemukan');
            redirect('index.php/gaji');
        }
    
        // Cek ketergantungan data
        if (!empty($gaji->id_pengelolaan)) {
            $this->session->set_flashdata('error', 'Data gaji tidak dapat dihapus karena terkait dengan data kinerja');
            redirect('gaji');
        }
    
        // Proses delete
        if ($this->Gaji_model->delete_gaji($id, urldecode($tanggal))) {
            $this->session->set_flashdata('success', 'Data gaji berhasil dihapus');
        } else {
            $this->session->set_flashdata('error', 'Gagal menghapus data gaji');
        }
        redirect('gaji');
    }
}
