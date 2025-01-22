<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Absensi extends CI_Controller {
    public function __construct() {
        parent::__construct();
        $this->load->model('Absensi_model');
        $this->load->library(['form_validation', 'session']);
    }

    public function index() {
        $data['title'] = 'Data Absensi';
        $data['absensi'] = $this->Absensi_model->get_all_absensi();
        
        $this->load->view('template/header', $data);
        $this->load->view('template/sidebar');
        $this->load->view('absensi/index', $data);
        $this->load->view('template/footer');
    }

    public function tambah() {
        $data['title'] = 'Tambah Absensi';
        $data['karyawan'] = $this->Absensi_model->get_active_employees();

        $this->form_validation->set_rules('id_krywn', 'Karyawan', 'required');
        $this->form_validation->set_rules('tanggal', 'Tanggal', 'required');
        $this->form_validation->set_rules('shift', 'Shift', 'required');
        $this->form_validation->set_rules('keterangan', 'Keterangan', 'required');

        if ($this->form_validation->run() === TRUE) {
            if ($this->Absensi_model->create_absensi()) {
                $this->session->set_flashdata('success', 'Data absensi berhasil ditambahkan');
                redirect('absensi');
            } else {
                $this->session->set_flashdata('error', 'Gagal menambahkan data absensi');
            }
        }

        $this->load->view('template/header', $data);
        $this->load->view('template/sidebar');
        $this->load->view('template/footer');
        $this->load->view('absensi/tambah', $data);
    }

    public function edit($id) {
        $data['title'] = 'Edit Absensi';
        $data['karyawan'] = $this->Absensi_model->get_active_employees();
        $data['absensi'] = $this->Absensi_model->get_absensi_by_id($id);
        
        if (!$data['absensi']) {
            show_404();
        }

        $this->form_validation->set_rules('id_krywn', 'Karyawan', 'required');
        $this->form_validation->set_rules('tanggal', 'Tanggal', 'required');
        $this->form_validation->set_rules('shift', 'Shift', 'required');
        $this->form_validation->set_rules('keterangan', 'Keterangan', 'required');

        if ($this->form_validation->run() === TRUE) {
            if ($this->Absensi_model->update_absensi($id)) {
                $this->session->set_flashdata('success', 'Data absensi berhasil diperbarui');
                redirect('absensi');
            } else {
                $this->session->set_flashdata('error', 'Gagal memperbarui data absensi');
            }
        }

        $this->load->view('template/header', $data);
        $this->load->view('template/sidebar');
        $this->load->view('template/footer');
        $this->load->view('absensi/edit', $data);
    }

    public function hapus($id) {
        $absensi = $this->Absensi_model->get_absensi_by_id($id);
        
        if (!$absensi) {
            show_404();
        }

        if ($this->Absensi_model->delete_absensi($id)) {
            $this->session->set_flashdata('success', 'Data absensi berhasil dihapus');
        } else {
            $this->session->set_flashdata('error', 'Gagal menghapus data absensi');
        }
        
        redirect('absensi');
    }

    public function get_monthly_report($month = null, $year = null) {
        if (!$month) $month = date('m');
        if (!$year) $year = date('Y');

        $data['title'] = 'Laporan Absensi Bulanan';
        $data['report'] = $this->Absensi_model->get_monthly_report($month, $year);
        
        $this->load->view('template/header', $data);
        $this->load->view('template/sidebar');
        $this->load->view('absensi/report', $data);
        $this->load->view('template/footer');
    }
}