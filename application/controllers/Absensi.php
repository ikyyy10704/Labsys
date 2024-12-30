<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Absensi extends CI_Controller {
    public function __construct() {
        parent::__construct();
        $this->load->model('Absensi_model');
    }

    // Tampilkan semua data absensi
    public function index() {
        $data['absensi'] = $this->Absensi_model->get_all_absensi();
        $this->load->view('absensi/index', $data);
    }

    // Form tambah absensi
    public function tambah() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $this->Absensi_model->create_absensi();
            redirect('absensi');
        } else {
            $this->load->view('absensi/tambah');
        }
    }

    // Form edit absensi
    public function edit($id) {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $this->Absensi_model->update_absensi($id);
            redirect('absensi');
        } else {
            $data['absensi'] = $this->Absensi_model->get_absensi_by_id($id);
            $this->load->view('absensi/edit', $data);
        }
    }

    // Hapus absensi
    public function hapus($id) {
        $this->Absensi_model->delete_absensi($id);
        redirect('absensi');
    }
}