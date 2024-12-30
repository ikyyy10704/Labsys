<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class User extends CI_Controller {

    public function __construct() {
        parent::__construct();
        $this->load->model('User_login'); 
    }

    public function login() {
        $this->load->view('login/login');
    }

    public function login_process() {
        $email = $this->input->post('email');
        $password = $this->input->post('password');
        $user = $this->User_login->get_user($email, $password);
        if ($this->session->userdata('user_id')) {
            // Pengguna sedang login
            $user_id = $this->session->userdata('user_id');
            // Redirect to beranda page
            redirect('http://localhost/Karyawan/index.php/beranda');
        } else {
            // User not found, redirect back to login
            $this->session->set_flashdata('error', 'Invalid email or password');
            redirect('http://localhost/Karyawan/index.php/beranda');
        }
    }

    public function logout() {
        $this->session->unset_userdata('user_id');
        redirect('user/login');
    }
}
?>
