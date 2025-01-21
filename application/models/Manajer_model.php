<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Manajer_model extends CI_Model {
    
    private $table = 'manajer';
    
    public function __construct() {
        parent::__construct();
        $this->load->database(); // Menambahkan load database
    }

    public function get_all() {
        return $this->db->get($this->table)->result();
    }
    
    public function get_by_id($id) {
        return $this->db->get_where($this->table, ['id_manajer' => $id])->row();
    }

    public function insert($data) {
        return $this->db->insert($this->table, $data);
    }

    public function update($id, $data) {
        return $this->db->update($this->table, $data, ['id_manajer' => $id]);
    }
    
    public function delete($id) {
        return $this->db->delete($this->table, ['id_manajer' => $id]);
    }

    public function check_exists($id) {
        $query = $this->db->get_where($this->table, ['id_manajer' => $id]);
        return $query->num_rows() > 0;
    }
    
    public function get_by_departemen($departemen) {
        return $this->db->get_where($this->table, ['departemen' => $departemen])->result();
    }

    public function count_all() {
        return $this->db->count_all($this->table);
    }
}