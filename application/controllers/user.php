<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class User extends CI_Controller {

    public function __construct() {
        parent::__construct();
        $this->load->model('User_login');
        $this->load->library('session');
    }

    public function login() {
        $this->load->view('login/login');
    }

    public function login_process() {
        $email = $this->input->post('email');
        $password = $this->input->post('password');

        if(!$email || !$password) {
            $this->session->set_flashdata('error', 'Please fill in all fields!');
            redirect('user/login');
            return;
        }

        $user_data = $this->User_login->get_user($email);

        if ($user_data) {
            if ($this->User_login->verify_password($password, $user_data['password'])) {
                $session_data = array(
                    'username' => $user_data['username'],
                    'email' => $user_data['email'],
                    'logged_in' => TRUE
                );
                
                $this->session->set_userdata($session_data);
                redirect('beranda');
                return;
            }
        }
        if (!$this->session->has_userdata('login_attempts')) {
            $this->session->set_userdata('login_attempts', 1);
        } else {
            $attempts = $this->session->userdata('login_attempts') + 1;
            $this->session->set_userdata('login_attempts', $attempts);
        }

        if ($this->session->userdata('login_attempts') >= 3) {
            $this->session->set_userdata('login_attempts', 0);
            $this->session->set_flashdata('error', 'Too many failed attempts! Please register if you don\'t have an account.');
            redirect('user/register');
        } else {
            $this->session->set_flashdata('error', 'Invalid email or password!');
            redirect('user/login');
        }
    }

    public function logout() {
        $this->session->unset_userdata(array('username', 'email', 'logged_in'));
        $this->session->sess_destroy();
        redirect('user/login');
    }
}
?>