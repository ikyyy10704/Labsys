<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Profile_model extends CI_Model {
    
    public function __construct() {
        parent::__construct();
        $this->load->database();
    }
    
    public function get_profile_by_username($username) {
        $this->db->select('l.*, m.nama_manajer, m.departemen');
        $this->db->from('login l');
        $this->db->join('manajer m', 'm.id_manajer = l.id_manajer', 'left');
        $this->db->where('l.username', $username);
        return $this->db->get()->row();
    }
    
    public function update_profile($username, $data) {
        $this->db->trans_start();
        try {
            // Update tabel login
            $login_data = [
                'email' => $data['email']
            ];
            
            // Jika password diubah
            if (!empty($data['new_password'])) {
                $login_data['password'] = $data['new_password'];
            }
            
            $this->db->where('username', $username);
            $this->db->update('login', $login_data);
            
            // Update tabel manajer jika user adalah manajer
            if (!empty($data['id_manajer'])) {
                $manajer_data = [
                    'nama_manajer' => $data['nama_manajer'],
                    'departemen' => $data['departemen']
                ];
                
                $this->db->where('id_manajer', $data['id_manajer']);
                $this->db->update('manajer', $manajer_data);
            }
            
            $this->db->trans_complete();
            return $this->db->trans_status();
            
        } catch (Exception $e) {
            $this->db->trans_rollback();
            return false;
        }
    }
}