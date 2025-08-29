<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Auth extends CI_Controller {

    public function __construct()
    {
        parent::__construct();
        $this->load->model('User_model');
        $this->load->library('form_validation');
        $this->load->helper(['form', 'url']);
    }

    public function index()
    {
        redirect('auth/login');
    }

    public function login()
    {
        // Check if already logged in
        if ($this->session->userdata('user_id') && $this->session->userdata('logged_in')) {
            $this->_redirect_by_role();
        }

        $this->form_validation->set_rules('username', 'Username', 'required|trim');
        $this->form_validation->set_rules('password', 'Password', 'required');

        if ($this->form_validation->run() == FALSE) {
            $data['title'] = 'Login - Laboratory Management System';
            $this->load->view('auth/login', $data);
        } else {
            $username = $this->input->post('username', TRUE);
            $password = $this->input->post('password', TRUE);
            
            $user = $this->User_model->validate_login($username, $password);
            
            if ($user) {
                // Set session data
                $session_data = array(
                    'user_id' => $user['user_id'],
                    'username' => $user['username'],
                    'nama_lengkap' => isset($user['nama_lengkap']) ? $user['nama_lengkap'] : $user['username'],
                    'role' => $user['role'],
                    'logged_in' => TRUE
                );
                
                // Add role-specific data
                switch($user['role']) {
                    case 'administrasi':
                        if (isset($user['telepon'])) {
                            $session_data['telepon'] = $user['telepon'];
                        }
                        break;
                    case 'petugas_lab':
                        if (isset($user['jenis_keahlian'])) {
                            $session_data['jenis_keahlian'] = $user['jenis_keahlian'];
                        }
                        if (isset($user['telepon'])) {
                            $session_data['telepon'] = $user['telepon'];
                        }
                        break;
                }
                
                $this->session->set_userdata($session_data);
                
                // Log activity
                $this->User_model->log_activity($user['user_id'], 'User logged in', 'users');
                
                // Set success message
                $this->session->set_flashdata('success', 'Login berhasil! Selamat datang ' . $session_data['nama_lengkap']);
                
                // Redirect based on role
                $this->_redirect_by_role();
            } else {
                $data['error'] = 'Username atau password salah!';
                $data['title'] = 'Login - Laboratory Management System';
                $this->load->view('auth/login', $data);
            }
        }
    }

    public function logout()
    {
        // Log activity before destroying session
        if ($this->session->userdata('user_id')) {
            $this->User_model->log_activity($this->session->userdata('user_id'), 'User logged out', 'users');
        }
        
        // Destroy all session data
        $this->session->unset_userdata([
            'user_id', 'username', 'nama_lengkap', 'role', 'logged_in', 
            'telepon', 'jenis_keahlian'
        ]);
        
        // Destroy session completely
        $this->session->sess_destroy();
        
        $this->session->set_flashdata('success', 'Anda telah keluar dari sistem');
        redirect('auth/login');
    }

    public function check_session()
    {
        // AJAX endpoint to check if session is still valid
        if (!$this->session->userdata('logged_in')) {
            echo json_encode(['valid' => false]);
        } else {
            echo json_encode(['valid' => true]);
        }
    }

    /**
     * Redirect user based on their role
     */
    private function _redirect_by_role()
    {
        $role = $this->session->userdata('role');
        
        switch($role) {
            case 'admin':
                redirect('admin/dashboard');
                break;
            case 'administrasi':
                redirect('administrasi/dashboard');
                break;
            case 'petugas_lab':
                redirect('laboratorium/dashboard');
                break;
            default:
                // Fallback for unknown roles
                $this->session->set_flashdata('error', 'Role tidak dikenali. Hubungi administrator.');
                redirect('auth/logout');
                break;
        }
    }

    /**
     * Check if user has permission to access a specific role
     */
    public function check_role_permission($required_role)
    {
        if (!$this->session->userdata('logged_in')) {
            return false;
        }
        
        $user_role = $this->session->userdata('role');
        
        // Admin can access everything
        if ($user_role === 'admin') {
            return true;
        }
        
        // Other roles can only access their own pages
        return $user_role === $required_role;
    }

    /**
     * Middleware to check authentication
     */
    public function require_login()
    {
        if (!$this->session->userdata('logged_in')) {
            $this->session->set_flashdata('error', 'Anda harus login terlebih dahulu');
            redirect('auth/login');
        }
    }

    /**
     * Middleware to check role permission
     */
    public function require_role($required_role)
    {
        $this->require_login();
        
        if (!$this->check_role_permission($required_role)) {
            $this->session->set_flashdata('error', 'Anda tidak memiliki akses ke halaman tersebut');
            $this->_redirect_by_role();
        }
    }

    /**
     * Change password (for logged in users)
     */
    public function change_password()
    {
        $this->require_login();
        
        if ($this->input->method() === 'post') {
            $this->form_validation->set_rules('current_password', 'Password Lama', 'required');
            $this->form_validation->set_rules('new_password', 'Password Baru', 'required|min_length[6]');
            $this->form_validation->set_rules('confirm_password', 'Konfirmasi Password', 'required|matches[new_password]');
            
            if ($this->form_validation->run() === TRUE) {
                $user_id = $this->session->userdata('user_id');
                $current_password = $this->input->post('current_password');
                $new_password = $this->input->post('new_password');
                
                // Verify current password
                $user = $this->User_model->get_user_by_id($user_id);
                if (md5($current_password) === $user['password']) {
                    // Update password
                    if ($this->User_model->change_password($user_id, $new_password)) {
                        $this->User_model->log_activity($user_id, 'Password changed', 'users');
                        $this->session->set_flashdata('success', 'Password berhasil diubah');
                    } else {
                        $this->session->set_flashdata('error', 'Gagal mengubah password');
                    }
                } else {
                    $this->session->set_flashdata('error', 'Password lama tidak sesuai');
                }
                
                $this->_redirect_by_role();
            }
        }
        
        $data['title'] = 'Ubah Password';
        $this->load->view('template/header', $data);
        $this->load->view('template/sidebar', $data);
        $this->load->view('auth/change_password', $data);
        $this->load->view('template/footer');
    }
}