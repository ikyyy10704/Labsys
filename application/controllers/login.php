<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Auth extends CI_Controller {
    public function __construct() {
        parent::__construct();
        $this->load->model('Auth_model');
        $this->load->library('form_validation');
    }

    public function login() {
        $this->form_validation->set_rules('username', 'Username', 'required');
        $this->form_validation->set_rules('password', 'Password', 'required');

        if ($this->form_validation->run() == FALSE) {
            $this->load->view('auth/login');
        } else {
            $username = $this->input->post('username');
            $password = $this->input->post('password');
            $user = $this->Auth_model->getUserByUsername($username);

            if ($user && password_verify($password, $user['password'])) {
                // Set session
                $this->session->set_userdata('user_id', $user['id_login']);
                redirect('dashboard');
            } else {
                $this->session->set_flashdata('error', 'Username atau Password salah.');
                redirect('auth/login');
            }
        }
    }

    public function logout() {
        $this->session->unset_userdata('user_id');
        redirect('auth/login');
    }
}
