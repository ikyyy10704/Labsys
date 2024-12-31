<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class User_login extends CI_Model {

    public function __construct() {
        parent::__construct();
        $this->load->database();
    }

    public function get_user($email) {
        $this->db->where('email', $email);
        $query = $this->db->get('login');
        return $query->row_array();
    }

    public function verify_password($password, $stored_password) {

        if ($password === $stored_password) {
            return true;
        }
        return md5($password) === $stored_password;
    }
}
?>
