<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Manajer extends CI_Controller {
    
    public function __construct() {
        parent::__construct();
        $this->load->model('Manajer_model');
        $this->load->library(['form_validation', 'session']);
        $this->load->helper(['url', 'form']);
        
        // Check if user is logged in
        if (!$this->session->userdata('logged_in')) {
            redirect('auth/login');
        }
    }

    public function index() {
        $data['title'] = 'Data Manajer';
        $data['manajer'] = $this->Manajer_model->get_all();
        
        $this->load->view('template/header', $data);
        $this->load->view('template/sidebar');
        $this->load->view('template/footer');
        $this->load->view('manajer/index', $data);
    }

    public function create() {
        $data['title'] = 'Tambah Manajer';

        $this->form_validation->set_rules('nama_manajer', 'Nama Manajer', 'required|trim');
        $this->form_validation->set_rules('departemen', 'Departemen', 'required|trim');
        $this->form_validation->set_rules('username', 'Username', 'required|trim|is_unique[login.username]');
        $this->form_validation->set_rules('email', 'Email', 'required|trim|valid_email|is_unique[login.email]');
        $this->form_validation->set_rules('password', 'Password', 'required|min_length[6]');
        $this->form_validation->set_rules('confirm_password', 'Konfirmasi Password', 'required|matches[password]');

        if ($this->form_validation->run() === FALSE) {
            $this->load->view('template/header', $data);
            $this->load->view('template/sidebar');
            $this->load->view('template/footer');
            $this->load->view('manajer/create');
        } else {
            // Prepare manajer data
            $manajer_data = [
                'nama_manajer' => $this->input->post('nama_manajer'),
                'departemen' => $this->input->post('departemen'),
                'foto' => 'default.jpg'
            ];

            // Prepare login data
            $login_data = [
                'username' => $this->input->post('username'),
                'password' => password_hash($this->input->post('password'), PASSWORD_DEFAULT),
                'email' => $this->input->post('email')
            ];

            // Insert data
            if ($this->Manajer_model->create($manajer_data, $login_data)) {
                $this->session->set_flashdata('success', 'Data manajer berhasil ditambahkan');
                redirect('manajer');
            } else {
                $this->session->set_flashdata('error', 'Terjadi kesalahan saat menambah data');
                redirect('manajer/create');
            }
        }
    }

    public function edit($id) {
        $data['title'] = 'Edit Manajer';
        $data['manajer'] = $this->Manajer_model->get_by_id($id);

        if (empty($data['manajer'])) {
            show_404();
        }

        $this->form_validation->set_rules('nama_manajer', 'Nama Manajer', 'required|trim');
        $this->form_validation->set_rules('departemen', 'Departemen', 'required|trim');
        $this->form_validation->set_rules('email', 'Email', 'required|trim|valid_email');
        
        if ($this->input->post('password')) {
            $this->form_validation->set_rules('password', 'Password', 'min_length[6]');
            $this->form_validation->set_rules('confirm_password', 'Konfirmasi Password', 'matches[password]');
        }

        if ($this->form_validation->run() === FALSE) {
            $this->load->view('template/header', $data);
            $this->load->view('template/sidebar');
            $this->load->view('template/footer');
            $this->load->view('manajer/edit', $data);
        } else {
            // Prepare manajer data
            $manajer_data = [
                'nama_manajer' => $this->input->post('nama_manajer'),
                'departemen' => $this->input->post('departemen')
            ];

            // Prepare login data
            $login_data = ['email' => $this->input->post('email')];
            
            // Add password to login data if it was changed
            if ($this->input->post('password')) {
                $login_data['password'] = password_hash($this->input->post('password'), PASSWORD_DEFAULT);
            }

            // Update data
            if ($this->Manajer_model->update($id, $manajer_data, $login_data)) {
                $this->session->set_flashdata('success', 'Data manajer berhasil diperbarui');
            } else {
                $this->session->set_flashdata('error', 'Terjadi kesalahan saat memperbarui data');
            }
            redirect('manajer');
        }
    }

    public function delete($id) {
        // Check if manajer exists
        $manajer = $this->Manajer_model->get_by_id($id);
        if (empty($manajer)) {
            show_404();
        }

        // Check for related records
        if ($this->Manajer_model->has_kinerja_records($id)) {
            $this->session->set_flashdata('error', 'Manajer tidak dapat dihapus karena masih memiliki data kinerja karyawan');
            redirect('manajer');
            return;
        }

        // Delete manajer
        if ($this->Manajer_model->delete($id)) {
            $this->session->set_flashdata('success', 'Data manajer berhasil dihapus');
        } else {
            $this->session->set_flashdata('error', 'Terjadi kesalahan saat menghapus data');
        }
        redirect('manajer');
    }

    public function upload_foto($id) {
        $config['upload_path'] = './uploads/manajer/';
        $config['allowed_types'] = 'gif|jpg|png|jpeg';
        $config['max_size'] = 2048; // 2MB
        $config['file_name'] = 'manajer_' . $id;
        $config['overwrite'] = true;

        $this->load->library('upload', $config);

        if (!$this->upload->do_upload('foto')) {
            $error = $this->upload->display_errors();
            $this->session->set_flashdata('error', $error);
        } else {
            $upload_data = $this->upload->data();
            if ($this->Manajer_model->upload_foto($id, $upload_data['file_name'])) {
                $this->session->set_flashdata('success', 'Foto berhasil diupload');
            } else {
                $this->session->set_flashdata('error', 'Gagal mengupdate data foto');
            }
        }
        redirect('manajer/edit/' . $id);
    }
}