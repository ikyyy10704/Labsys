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
        $this->load->view('manajer/index', $data);
        $this->load->view('template/footer');
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
            $this->load->view('manajer/create');
            $this->load->view('template/footer');
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

    // Lanjutkan dengan fungsi edit, delete, dan lainnya sesuai kebutuhan
}