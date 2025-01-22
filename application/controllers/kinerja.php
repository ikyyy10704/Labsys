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
<<<<<<< HEAD
        $data['kinerja'] = $this->Kinerja_model->get_kinerja_karyawan();
        
        $this->load->view('template/header', $data);
        $this->load->view('template/sidebar');
        $this->load->view('template/footer');
        $this->load->view('kinerja/kinerja_list', $data);
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
                $this->load->view('template/footer');
                $this->load->view('kinerja/kinerja_tambah', $data);
            } else {
                $kinerja_data = [
                    'nilai_kerja' => $this->input->post('nilai_kerja'),
                    'nilai_kerja' => $this->input->post('nilai_kerja'),
                    'status_pengelolaan' => $this->input->post('status_pengelolaan'),
                    'tgl_pengelolaan' => $this->input->post('tgl_pengelolaan'),
                    'id_manajer' => $this->input->post('id_manajer')
                ];
                $id_krywn = $this->input->post('id_krywn');
        
                if ($this->Kinerja_model->insert_kinerja($kinerja_data, $id_krywn)) {
                    $this->session->set_flashdata('success', 'Data kinerja berhasil ditambahkan');
                } else {
                    $this->session->set_flashdata('error', 'Gagal menambahkan data kinerja');
                }
                redirect('kinerja');
            }
        }
=======
        $data['kinerja'] = $this->Kinerja_model->get_kinerja_karyawan(); 
        $this->load->view('template/header', $data);
        $this->load->view('template/sidebar');
        $this->load->view('kinerja/kinerja_list.php', $data);
        $this->load->view('template/footer');
    }

    public function tambah() {
        $data['title'] = 'Tambah Data Kinerja';
        $data['karyawan'] = $this->Kinerja_model->get_active_karyawan();
        $data['manajer1'] = $this->Kinerja_model->get_managers();
        $data['manajer'] = $this->Kinerja_model->get_all_manajer();
        
>>>>>>> 45f8a1b3af6ae2880fabb8fee4b4c65009d7926e

        $this->form_validation->set_rules('status_pengelolaan', 'Status Pengelolaan', 'required');
        $this->form_validation->set_rules('tgl_pengelolaan', 'Tanggal Pengelolaan', 'required');
        
        $data = array(
            'id_krywn' => $this->input->post('id_krywn'),
            'id_absensi' => $this->input->post('id_absensi'),
            'tgl_pengelolaan' => $this->input->post('tgl_pengelolaan'),
            'nilai_kerja' => $this->input->post('nilai_kerja'),
            'status_pengelolaan' => $this->input->post('status_pengelolaan'),
            'id_manajer' => $this->input->post('id_manajer')
        );
        
        $this->load->view('template/header', $data);
        $this->load->view('template/sidebar');
        $this->load->view('template/footer');
        $this->load->view('kinerja/kinerja_tambah', $data);
    }
    

    public function edit($id) {
     $this->form_validation->set_rules('status_pengelolaan', 'Status Pengelolaan', 'required');
    $this->form_validation->set_rules('tgl_pengelolaan', 'Tanggal Pengelolaan', 'required');
    $data = array(
        'nilai_kerja' => $this->input->post('nilai_kerja'),
        'status_pengelolaan' => $this->input->post('status_pengelolaan'),
        'tgl_pengelolaan' => $this->input->post('tgl_pengelolaan'),
        'id_manajer' => $this->input->post('id_manajer')
    );
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
    public function hapus($id) {
        if ($this->Kinerja_model->delete_kinerja($id)) {
            $this->session->set_flashdata('success', 'Data kinerja berhasil dihapus');
        } else {
            $this->session->set_flashdata('error', 'Gagal menghapus data kinerja. Pastikan tidak ada data terkait.');
        }
        redirect('kinerja');
    }
}