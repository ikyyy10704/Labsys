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
        $this->load->view('kinerja/kinerja_list.php', $data);
        $this->load->view('template/footer');
    }

    public function tambah() {
        $data['title'] = 'Tambah Data Kinerja';
        $data['karyawan'] = $this->Kinerja_model->get_active_karyawan();
        $data['manajer'] = $this->Kinerja_model->get_all_manajer();
        
        $this->load->view('template/header', $data);
        $this->load->view('template/sidebar');
        $this->load->view('kinerja/kinerja_tambah', $data);
        $this->load->view('template/footer');
    }

    public function simpan() {
        $this->form_validation->set_rules('nilai_kerja', 'Nilai Kerja', 'required|numeric|greater_than[0]|less_than[101]');
        $this->form_validation->set_rules('id_krywn', 'Karyawan', 'required');
        $this->form_validation->set_rules('id_manajer', 'Manajer', 'required');

        if ($this->form_validation->run() == FALSE) {
            $this->tambah();
            return;
        }

        $data = array(
            'id_krywn' => $this->input->post('id_krywn'),
            'id_absensi' => $this->input->post('id_absensi'),
            'tgl_pengelolaan' => date('Y-m-d'),
            'nilai_kerja' => $this->input->post('nilai_kerja'),
            'id_manajer' => $this->input->post('id_manajer')
        );

        if ($this->Kinerja_model->insert_kinerja($data)) {
            $this->session->set_flashdata('success', 'Data kinerja berhasil ditambahkan');
        } else {
            $this->session->set_flashdata('error', 'Gagal menambahkan data kinerja');
        }
        redirect('kinerja');
    }

    public function edit($id) {
        $data['title'] = 'Edit Data Kinerja';
        $data['kinerja'] = $this->Kinerja_model->get_kinerja_by_id($id);
        $data['manajer'] = $this->Kinerja_model->get_all_manajer();
        
        if (!$data['kinerja']) {
            $this->session->set_flashdata('error', 'Data kinerja tidak ditemukan');
            redirect('kinerja');
        }
        
        $this->load->view('template/header', $data);
        $this->load->view('template/sidebar');
        $this->load->view('kinerja/kinerja_edit', $data);
        $this->load->view('template/footer');
    }

    public function update($id) {
        $this->form_validation->set_rules('nilai_kerja', 'Nilai Kerja', 'required|numeric|greater_than[0]|less_than[101]');
        $this->form_validation->set_rules('id_manajer', 'Manajer', 'required');
    
        if ($this->form_validation->run() == FALSE) {
            $this->edit($id);
            return;
        }
    
        $data = array(
            'nilai_kerja' => $this->input->post('nilai_kerja'),
            'id_manajer' => $this->input->post('id_manajer'),
            'tgl_pengelolaan' => date('Y-m-d') // Update tanggal pengelolaan
        );
    
        if ($this->Kinerja_model->update_kinerja($id, $data)) {
            $this->session->set_flashdata('success', 'Data kinerja berhasil diupdate');
        } else {
            $this->session->set_flashdata('error', 'Gagal mengupdate data kinerja');
        }
        redirect('kinerja');
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