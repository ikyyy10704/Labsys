<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Auth_model extends CI_Model {
    
    public function register($data) {
        return $this->db->insert('login', $data);
    }

    public function get_user($username) {
        $query = $this->db->get_where('login', ['username' => $username]);
        return $query->row();
    }

    public function change_password($username, $new_password) {
        $hashed_password = password_hash($new_password, PASSWORD_DEFAULT);
        $this->db->where('username', $username);
        return $this->db->update('login', ['password' => $hashed_password]);
    }
    public function get_user_by_email($email) {
        $query = $this->db->get_where('login', ['email' => $email]);
        return $query->row();
    }
    
    public function save_reset_token($username, $token, $expires) {
        $this->db->where('username', $username);
        return $this->db->update('login', [
            'reset_token' => $token,
            'reset_expires' => $expires
        ]);
    }
    
    public function get_user_by_reset_token($token) {
        $query = $this->db->get_where('login', ['reset_token' => $token]);
        return $query->row();
    }
    
    public function reset_password($username, $new_password) {
        $this->db->where('username', $username);
        return $this->db->update('login', [
            'password' => password_hash($new_password, PASSWORD_DEFAULT),
            'reset_token' => NULL,
            'reset_expires' => NULL
        ]);
    }
}