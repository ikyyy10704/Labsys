<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Data_karyawan extends CI_Controller {
    public function __construct() {
        parent::__construct();
        $this->load->model('Data_karyawan_model');
        $this->load->helper('url');
        $this->load->library(['form_validation', 'session']);
    }

    public function index() {
        $data['title'] = 'Data Karyawan';
        $data['karyawan'] = $this->Data_karyawan_model->get_all_karyawan();
        $this->load->view('template/header', $data);
        $this->load->view('template/sidebar');
        $this->load->view('data_karyawan/index', $data);
        $this->load->view('template/footer');
    }

    public function create() {
        $this->form_validation->set_rules('nama_krywn', 'Nama Karyawan', 'required');
        $this->form_validation->set_rules('jenis_kelamin', 'Jenis Kelamin', 'required|in_list[L,P]');
        $this->form_validation->set_rules('alamat', 'Alamat', 'required');
        $this->form_validation->set_rules('email', 'Email', 'required|valid_email');
        $this->form_validation->set_rules('status', 'Status', 'required');
        $this->form_validation->set_rules('posisi', 'Posisi', 'required');

        if ($this->form_validation->run() === FALSE) {
            $data['title'] = 'Tambah Data Karyawan';
            $this->load->view('template/header', $data);
            $this->load->view('template/sidebar');
            $this->load->view('data_karyawan/create', $data);
            $this->load->view('template/footer');
        } else {
            $this->Data_karyawan_model->create_karyawan($this->input->post());
            $this->session->set_flashdata('success', 'Data karyawan berhasil ditambahkan');
            redirect('data_karyawan');
        }
    }

    public function edit($id) {
        $this->form_validation->set_rules('nama_krywn', 'Nama Karyawan', 'required');
        $this->form_validation->set_rules('jenis_kelamin', 'Jenis Kelamin', 'required|in_list[L,P]');
        $this->form_validation->set_rules('alamat', 'Alamat', 'required');
        $this->form_validation->set_rules('email', 'Email', 'required|valid_email');
        $this->form_validation->set_rules('status', 'Status', 'required');
        $this->form_validation->set_rules('posisi', 'Posisi', 'required');

        $data['title'] = 'Edit Data Karyawan';
        $data['karyawan'] = $this->Data_karyawan_model->get_karyawan_by_id($id);

        if (!$data['karyawan']) {
            show_404();
        }

        if ($this->form_validation->run() === FALSE) {
            $this->load->view('template/header', $data);
            $this->load->view('template/sidebar');
            $this->load->view('data_karyawan/edit', $data);
            $this->load->view('template/footer');
        } else {
            $this->Data_karyawan_model->update_karyawan($id, $this->input->post());
            $this->session->set_flashdata('success', 'Data karyawan berhasil diperbarui');
            redirect('data_karyawan');
        }
    }

    public function delete() {
        $id = $this->input->post('id_krywn');
        if ($id) {
            $this->Data_karyawan_model->delete_karyawan($id);
            $this->session->set_flashdata('success', 'Data karyawan berhasil dihapus');
        } else {
            $this->session->set_flashdata('error', 'Data karyawan gagal dihapus');
        }
        redirect('data_karyawan');
    }
}
?>
