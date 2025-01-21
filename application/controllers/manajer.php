<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Manajer extends CI_Controller {

    public function __construct() {
        parent::__construct();
        $this->load->model('Manajer_model');
        $this->load->library(['form_validation', 'session']);
        $this->load->helper(['url', 'form']);
    }

    public function index() {
        $data['title'] = 'Data Manajer';
        $data['manajer'] = $this->Manajer_model->get_all();
        
        $this->load->view('template/header', $data);
        $this->load->view('template/sidebar');
        $this->load->view('manajer/index', $data);
        $this->load->view('template/footer');
    }

    public function create() {
        $data['title'] = 'Tambah Manajer';

        // Set validation rules
        $this->form_validation->set_rules('nama_manajer', 'Nama Manajer', 'required|trim');
        $this->form_validation->set_rules('departemen', 'Departemen', 'required|trim');

        if ($this->form_validation->run() === FALSE) {
            $this->load->view('template/header', $data);
            $this->load->view('template/sidebar');
            $this->load->view('manajer/create');
            $this->load->view('template/footer');
        } else {
            $data = [
                'nama_manajer' => $this->input->post('nama_manajer'),
                'departemen' => $this->input->post('departemen')
            ];

            if ($this->Manajer_model->insert($data)) {
                $this->session->set_flashdata('success', 'Data manajer berhasil ditambahkan');
            } else {
                $this->session->set_flashdata('error', 'Terjadi kesalahan saat menambah data');
            }
            redirect('manajer');
        }
    }

    public function edit($id) {
        $data['title'] = 'Edit Manajer';
        $data['manajer'] = $this->Manajer_model->get_by_id($id);

        if (empty($data['manajer'])) {
            show_404();
        }

        // Set validation rules
        $this->form_validation->set_rules('nama_manajer', 'Nama Manajer', 'required|trim');
        $this->form_validation->set_rules('departemen', 'Departemen', 'required|trim');

        if ($this->form_validation->run() === FALSE) {
            $this->load->view('template/header', $data);
            $this->load->view('template/sidebar');
            $this->load->view('manajer/edit', $data);
            $this->load->view('template/footer');
        } else {
            $data = [
                'nama_manajer' => $this->input->post('nama_manajer'),
                'departemen' => $this->input->post('departemen')
            ];

            if ($this->Manajer_model->update($id, $data)) {
                $this->session->set_flashdata('success', 'Data manajer berhasil diperbarui');
            } else {
                $this->session->set_flashdata('error', 'Terjadi kesalahan saat memperbarui data');
            }
            redirect('manajer');
        }
    }

    public function delete($id) {
        // Check if manajer exists
        if (!$this->Manajer_model->check_exists($id)) {
            show_404();
        }

        // Check if manajer has related records in kinerja_karyawan
        $this->load->database();
        $query = $this->db->get_where('kinerja_karyawan', ['id_manajer' => $id]);
        
        if ($query->num_rows() > 0) {
            $this->session->set_flashdata('error', 'Manajer tidak dapat dihapus karena masih memiliki data kinerja karyawan');
            redirect('manajer');
            return;
        }

        if ($this->Manajer_model->delete($id)) {
            $this->session->set_flashdata('success', 'Data manajer berhasil dihapus');
        } else {
            $this->session->set_flashdata('error', 'Terjadi kesalahan saat menghapus data');
        }
        redirect('manajer');
    }
}