<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Kinerja extends CI_Controller {

    public function __construct() {
        parent::__construct();
        $this->load->model('Kinerja_model');
        $this->load->helper(['url', 'form']);
        $this->load->library(['form_validation', 'session']);
    }

    public function index() {
        $data['title'] = 'Data Kinerja Karyawan';
        $data['kinerja'] = $this->Kinerja_model->get_kinerja_karyawan();
        $this->load->view('template/header', $data);
        $this->load->view('template/sidebar');
        $this->load->view('kinerja/kinerja_list', $data);
        $this->load->view('template/footer');
    }

    public function tambah() {
        $data['title'] = 'Tambah Data Kinerja';
        $data['karyawan'] = $this->Kinerja_model->get_active_karyawan();
        $data['manajer'] = $this->Kinerja_model->get_all_manajer();
    
        $this->form_validation->set_rules('id_krywn', 'Karyawan', 'required');
        $this->form_validation->set_rules('nilai_kerja', 'Nilai Kerja', 'required|numeric|greater_than_equal_to[0]|less_than_equal_to[100]');
        $this->form_validation->set_rules('status_pengelolaan', 'Status Pengelolaan', 'required');
        $this->form_validation->set_rules('id_manajer', 'Manajer', 'required');
        $this->form_validation->set_rules('tgl_pengelolaan', 'Tanggal Pengelolaan', 'required');
    
        if ($this->form_validation->run() === FALSE) {
            $this->load->view('template/header', $data);
            $this->load->view('template/sidebar');
            $this->load->view('kinerja/kinerja_tambah', $data);
            $this->load->view('template/footer');
        } else {
            // Data untuk tabel kinerja_karyawan
            $kinerja_data = [
                'nilai_kerja' => $this->input->post('nilai_kerja'),
                'status_pengelolaan' => $this->input->post('status_pengelolaan'),
                'tgl_pengelolaan' => $this->input->post('tgl_pengelolaan'),
                'id_manajer' => $this->input->post('id_manajer')
            ];
            
            // ID karyawan untuk update di tabel data_karyawan
            $id_krywn = $this->input->post('id_krywn');
    
            if ($this->Kinerja_model->insert_kinerja($kinerja_data, $id_krywn)) {
                $this->session->set_flashdata('success', 'Data kinerja berhasil ditambahkan');
            } else {
                $this->session->set_flashdata('error', 'Gagal menambahkan data kinerja');
            }
            redirect('kinerja');
        }
    }

    public function get_departemen_by_manajer() {
        $id_manajer = $this->input->post('id_manajer');
        $departemen = $this->Kinerja_model->get_departemen_by_manajer($id_manajer);
        header('Content-Type: application/json');
        echo json_encode(['departemen' => $departemen ? $departemen : '']);
    }
    
    public function edit($id = null) {
        // Ambil tanggal dari URL
        $selected_date = $this->input->get('date');
    
        if (!$id) {
            $this->session->set_flashdata('error', 'ID Pengelolaan tidak ditemukan');
            redirect('kinerja');
        }
    
        // Set data untuk view
        $data['title'] = 'Edit Data Kinerja';
        $data['kinerja'] = $this->Kinerja_model->get_kinerja_by_id($id);
        $data['manajer'] = $this->Kinerja_model->get_all_manajer();
    
        // Jika ada tanggal yang dipilih
        if ($selected_date) {
            $data['kinerja_by_date'] = $this->Kinerja_model->get_kinerja_by_date($selected_date);
        }
    
        if (!$data['kinerja']) {
            $this->session->set_flashdata('error', 'Data kinerja tidak ditemukan');
            redirect('kinerja');
        }
    
        // Validasi form
        $this->form_validation->set_rules('nilai_kerja', 'Nilai Kerja', 'required|numeric');
        $this->form_validation->set_rules('status_pengelolaan', 'Status Pengelolaan', 'required');
        $this->form_validation->set_rules('id_manajer', 'Manajer', 'required');
        $this->form_validation->set_rules('tgl_pengelolaan', 'Tanggal Pengelolaan', 'required');
    
        if ($this->form_validation->run() === FALSE) {
            $this->load->view('template/header', $data);
            $this->load->view('template/sidebar');
            $this->load->view('kinerja/kinerja_edit', $data);
            $this->load->view('template/footer');
        } else {
            $update_data = [
                'nilai_kerja' => $this->input->post('nilai_kerja'),
                'status_pengelolaan' => $this->input->post('status_pengelolaan'),
                'tgl_pengelolaan' => $this->input->post('tgl_pengelolaan'),
                'id_manajer' => $this->input->post('id_manajer')
            ];
    
            if ($this->Kinerja_model->update_kinerja($id, $update_data)) {
                $this->session->set_flashdata('success', 'Data kinerja berhasil diperbarui');
                // Redirect dengan tanggal yang sama
                redirect('kinerja?date=' . $selected_date);
            } else {
                $this->session->set_flashdata('error', 'Gagal memperbarui data kinerja');
                redirect('kinerja/edit/' . $id);
            }
        }
    }
    public function hapus($id) {
        if ($this->Kinerja_model->delete_kinerja($id)) {
            $this->session->set_flashdata('success', 'Data kinerja berhasil dihapus');
        } else {
            $this->session->set_flashdata('error', 'Gagal menghapus data kinerja. Pastikan tidak ada data terkait.');
        }
        redirect('kinerja');
    }
}