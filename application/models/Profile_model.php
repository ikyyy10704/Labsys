<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Profile_model extends CI_Model {
    
    public function __construct() {
        parent::__construct();
        $this->load->database();
    }
    
    public function get_profile_by_username($username) {
        $this->db->select('l.*, m.nama_manajer, m.departemen, m.foto');
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
            
            // Pastikan password diupdate dengan benar
            if (!empty($data['new_password'])) {
                $login_data['password'] = password_hash($data['new_password'], PASSWORD_DEFAULT);
            }
            
            $this->db->where('username', $username);
            $this->db->update('login', $login_data);
            
            // Update tabel manajer jika user adalah manajer
            if (!empty($data['id_manajer'])) {
                $manajer_data = [
                    'nama_manajer' => $data['nama_manajer'],
                    'departemen' => $data['departemen']
                ];
                
                // Pastikan foto diupdate jika ada
                if (isset($data['foto']) && !empty($data['foto'])) {
                    $manajer_data['foto'] = $data['foto'];
                }
                
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

    public function get_foto($id_manajer) {
        $this->db->select('foto');
        $this->db->where('id_manajer', $id_manajer);
        $result = $this->db->get('manajer')->row();
        return $result ? $result->foto : 'default.jpg';
    }
}