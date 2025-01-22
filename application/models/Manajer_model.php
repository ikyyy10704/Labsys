<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Manajer_model extends CI_Model {
    
    private $table = 'manajer';
    private $login_table = 'login';
    private $view_table = 'vw_manajer_login';
    
    public function __construct() {
        parent::__construct();
        $this->load->database();
    }
    
    public function get_all() {
        $this->db->from($this->view_table);
        $query = $this->db->get();
        return $query->result();
    }
    
    public function get_by_id($id) {
        $this->db->from($this->view_table);
        $this->db->where('id_manajer', $id);
        $query = $this->db->get();
        return $query->row();
    }
    
    public function create($manajer_data, $login_data) {
        $this->db->trans_begin();

        try {
            // Sisipkan data manajer dan dapatkan id yang disisipkan
            $this->db->insert($this->table, $manajer_data);
            $id_manajer = $this->db->insert_id();

            // Tambahkan id_manajer ke dalam data login
            $login_data['id_manajer'] = $id_manajer;

            // Sisipkan data login
            $this->db->insert($this->login_table, $login_data);

            if ($this->db->trans_status() === FALSE) {
                throw new Exception('Gagal menyimpan data');
            }

            $this->db->trans_commit();
            return $id_manajer; // Kembalikan id manajer yang disisipkan
        } catch (Exception $e) {
            $this->db->trans_rollback();
            return false;
        }
    }
    
    // Lanjutkan dengan fungsi update, delete, dan lainnya sesuai kebutuhan
}