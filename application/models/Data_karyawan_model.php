<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Data_karyawan_model extends CI_Model {

    public function __construct() {
        parent::__construct();
        $this->load->database(); 
    }

    // Mendapatkan semua data karyawan
    public function get_all_karyawan() {
        return $this->db->get('data_karyawan')->result_array();
    }

    // Mendapatkan data karyawan berdasarkan ID
    public function get_karyawan_by_id($id) {
        return $this->db->get_where('data_karyawan', ['id_krywn' => $id])->row_array();
    }

    // Menambahkan data karyawan baru
    public function create_karyawan($data) {
        return $this->db->insert('data_karyawan', $data);
    }

    // Mengupdate data karyawan berdasarkan ID
    public function update_karyawan($id, $data) {
        $this->db->where('id_krywn', $id);
        return $this->db->update('data_karyawan', $data);
    }

    // Menghapus data karyawan berdasarkan ID
    public function delete_karyawan($id) {
        $this->db->where('id_krywn', $id);
        return $this->db->delete('data_karyawan');
    }
}
