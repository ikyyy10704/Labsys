<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Auth_model extends CI_Model {
    public function getUserByUsername($username) {
        return $this->db->get_where('login', ['username' => $username])->row_array();
    }
}
