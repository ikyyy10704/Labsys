<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Absensi extends CI_Controller {
    public function __construct() {
        parent::__construct();
        $this->load->model('Absensi_model');
    }

    // Menampilkan semua absensi
    public function index() {
        $data['absensi'] = $this->Absensi_model->get_all_absensi();
        $this->load->view('template/header', $data);
        $this->load->view('template/sidebar');
        $this->load->view('absensi/index', $data);
        $this->load->view('template/footer');
    }

    // Menambah data absensi
    public function tambah() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $this->Absensi_model->create_absensi();
            redirect('absensi');
        } else {
            $data['karyawan'] = $this->Absensi_model->get_all_karyawan();
            $this->load->view('absensi/tambah', $data);
        }
    }

    // Mengedit data absensi
    public function edit($id) {
        // Periksa jika ID tidak kosong
        if (empty($id)) {
            show_404();  // Jika ID kosong, tampilkan error 404
        }
    
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $this->Absensi_model->update_absensi($id);
            $this->session->set_flashdata('success', 'Data absensi berhasil diperbarui.');
            redirect('absensi');
        } else {
            $data['absensi'] = $this->Absensi_model->get_absensi_by_id($id);
            $this->load->view('absensi/edit', $data);
        }
    }

    public function hapus($id) {
        // Pastikan ID tidak kosong
        if (empty($id)) {
            show_404();  // Menampilkan halaman error 404 jika ID tidak valid
        }
    
        // Menghapus data absensi berdasarkan ID
        if ($this->Absensi_model->delete_absensi($id)) {
            $this->session->set_flashdata('success', 'Data absensi berhasil dihapus.');
        } else {
            $this->session->set_flashdata('error', 'Terjadi kesalahan saat menghapus data.');
        }
    
        redirect('absensi');
    }
    
}
?>
