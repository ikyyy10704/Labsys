<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class User_login extends CI_Model {

    public function __construct() {
        parent::__construct();
        $this->load->database();
    }

    public function get_user($email, $password) {
        $this->db->where('email', $email);
        $this->db->where('password', md5($password)); // assuming the password is stored as an MD5 hash
        $query = $this->db->get('login'); // replace 'users' with your actual users table name
        return $query->row();
    }
}
?>
