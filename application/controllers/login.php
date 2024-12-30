<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class login extends CI_Controller {
    public function __construct() {
        parent::__construct();
        $this->load->model('Auth_model');
        $this->load->library(['form_validation', 'session']);
        $this->load->helper(['url', 'form']);
    }
    public function register() {
        $this->form_validation->set_rules('username', 'Username', 'required|is_unique[login.username]');
        $this->form_validation->set_rules('email', 'Email', 'required|valid_email|is_unique[login.email]');
        $this->form_validation->set_rules('password', 'Password', 'required|min_length[6]');

        if($this->form_validation->run() === FALSE) {
            $this->load->view('auth/register');
        } else {
            $data = [
                'username' => $this->input->post('username'),
                'email' => $this->input->post('email'),
                'password' => password_hash($this->input->post('password'), PASSWORD_DEFAULT)
            ];

            if($this->Auth_model->register($data)) {
                $this->session->set_flashdata('success', 'Registrasi berhasil! Silakan login.');
                redirect('auth/login');
            } else {
                $this->session->set_flashdata('error', 'Gagal melakukan registrasi.');
                redirect('auth/register');
            }
        }
    }

    public function login() {
        $this->form_validation->set_rules('username', 'Username', 'required');
        $this->form_validation->set_rules('password', 'Password', 'required');

        if($this->form_validation->run() === FALSE) {
            $this->load->view('auth/login');
        } else {
            $username = $this->input->post('username');
            $password = $this->input->post('password');

            $user = $this->Auth_model->get_user($username);

            if($user && password_verify($password, $user->password)) {
                $data = [
                    'username' => $user->username,
                    'email' => $user->email,
                    'logged_in' => TRUE
                ];
                $this->session->set_userdata($data);
                redirect('index.php/beranda');
            } else {
                $this->session->set_flashdata('error', 'Username atau password salah!');
                redirect('auth/login');
            }
        }
    }
    public function forgot_password() {
        $this->form_validation->set_rules('email', 'Email', 'required|valid_email');
    
        if($this->form_validation->run() === FALSE) {
            $this->load->view('auth/forgot_password');
        } else {
            $email = $this->input->post('email');
            $user = $this->Auth_model->get_user_by_email($email);
    
            if($user) {
                $reset_token = bin2hex(random_bytes(32));
                $reset_expires = date('Y-m-d H:i:s', strtotime('+1 hour'));
                
                // Simpan token
                $this->Auth_model->save_reset_token($user->username, $reset_token, $reset_expires);
                
                // Link reset
                $reset_link = base_url('auth/reset_password/' . $reset_token);
                
                // Kirim email (bisa disesuaikan dengan sistem email Anda)
                // ... kode kirim email
    
                $this->session->set_flashdata('success', 'Link reset password telah dikirim ke email Anda.');
                redirect('auth/login');
            } else {
                $this->session->set_flashdata('error', 'Email tidak ditemukan.');
                redirect('auth/forgot_password');
            }
        }
    }
    
    public function reset_password($token = NULL) {
        if(!$token) {
            show_404();
        }
    
        $user = $this->Auth_model->get_user_by_reset_token($token);
        if(!$user || strtotime($user->reset_expires) < time()) {
            $this->session->set_flashdata('error', 'Token tidak valid atau sudah kadaluarsa.');
            redirect('auth/login');
        }
    
        $this->form_validation->set_rules('password', 'Password', 'required|min_length[6]');
        $this->form_validation->set_rules('confirm_password', 'Konfirmasi Password', 'required|matches[password]');
    
        if($this->form_validation->run() === FALSE) {
            $this->load->view('auth/edit', ['token' => $token]);
        } else {
            $new_password = $this->input->post('password');
            
            if($this->Auth_model->reset_password($user->username, $new_password)) {
                $this->session->set_flashdata('success', 'Password berhasil direset. Silakan login.');
                redirect('auth/login');
            } else {
                $this->session->set_flashdata('error', 'Gagal mereset password.');
                redirect('auth/reset_password/' . $token);
            }
        }
    }
}