<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Profile extends CI_Controller {
    
    public function __construct() {
        parent::__construct();
        $this->load->model('Profile_model');
        $this->load->library(['form_validation', 'session']);
        $this->load->helper(['url', 'form']);
        
        if (!$this->session->userdata('logged_in')) {
            redirect('login');
        }
    }
    
    
    public function index() {
        $username = $this->session->userdata('username');
        $data['title'] = 'Profile';
        $data['profile'] = $this->Profile_model->get_profile_by_username($username);
        
        $this->load->view('template/header', $data);
        $this->load->view('template/sidebar');
        $this->load->view('template/footer');
        $this->load->view('profile/index', $data);
    }
    
    public function update() {
        $username = $this->session->userdata('username');
        $profile = $this->Profile_model->get_profile_by_username($username);
        
        $this->form_validation->set_rules('email', 'Email', 'required|valid_email');
        
        if (!empty($this->input->post('new_password'))) {
            $this->form_validation->set_rules('current_password', 'Current Password', 'required');
            $this->form_validation->set_rules('new_password', 'New Password', 'required|min_length[6]');
            $this->form_validation->set_rules('confirm_password', 'Confirm Password', 'required|matches[new_password]');
        }
        
        if ($this->form_validation->run() === FALSE) {
            $this->session->set_flashdata('error', validation_errors());
            redirect('profile');
            return;
        }
        
        // Verifikasi password saat ini jika akan mengubah password
        if (!empty($this->input->post('new_password'))) {
            if (!$this->_verify_password($username, $this->input->post('current_password'))) {
                $this->session->set_flashdata('error', 'Password saat ini salah');
                redirect('profile');
                return;
            }
        }
        
        $update_data = [
            'email' => $this->input->post('email'),
            'id_manajer' => $this->input->post('id_manajer'),
            'nama_manajer' => $this->input->post('nama_manajer'),
            'departemen' => $this->input->post('departemen')
        ];
        
        // Tambahkan password baru jika diisi
        if (!empty($this->input->post('new_password'))) {
            $update_data['new_password'] = password_hash($this->input->post('new_password'), PASSWORD_DEFAULT);
        }
        
        // Handle foto upload
        if (!empty($_FILES['foto']['name'])) {
            $config['upload_path'] = './uploads/manajer/profile/';
            $config['allowed_types'] = 'gif|jpg|jpeg|png';
            $config['max_size'] = 2048; // 2MB max
            $config['encrypt_name'] = TRUE;
            
            $this->load->library('upload', $config);
            
            if ($this->upload->do_upload('foto')) {
                $foto_data = $this->upload->data();
                $update_data['foto'] = $foto_data['file_name'];
                
                // Hapus foto lama jika bukan default
                if ($profile->foto && $profile->foto != 'default.jpg') {
                    $old_foto_path = './uploads/manajer/profile/' . $profile->foto;
                    if (file_exists($old_foto_path)) {
                        unlink($old_foto_path);
                    }
                }
            } else {
                $this->session->set_flashdata('error', $this->upload->display_errors());
                redirect('profile');
                return;
            }
        }
        
        
        if ($this->Profile_model->update_profile($username, $update_data)) {
            // Update session jika perlu
            $updated_profile = $this->Profile_model->get_profile_by_username($username);
            $this->session->set_userdata([
                'email' => $updated_profile->email,
                // Tambahkan data lain yang perlu diupdate di session
            ]);
            
            $this->session->set_flashdata('success', 'Profile berhasil diperbarui');
        } else {
            $this->session->set_flashdata('error', 'Gagal memperbarui profile');
        }
        redirect('profile');
    }
    
    // Tambahan method untuk verifikasi password
    private function _verify_password($username, $current_password) {
        $this->db->where('username', $username);
        $user = $this->db->get('login')->row();
        
        if ($user && password_verify($current_password, $user->password)) {
            return true;
        }
        return false;
    }
}