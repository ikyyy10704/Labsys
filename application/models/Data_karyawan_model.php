<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Data_karyawan_model extends CI_Model {
    public function get_all_karyawan() {
        return $this->db->get('data_karyawan')->result_array();
    }

    public function get_karyawan_by_id($id) {
        return $this->db->get_where('data_karyawan', ['id_krywn' => $id])->row_array();
    }

    public function create_karyawan($data) {
        return $this->db->insert('data_karyawan', $data);
    }

    public function update_karyawan($id, $data) {
        $this->db->where('id_krywn', $id);
        return $this->db->update('data_karyawan', $data);
    }

    public function delete_karyawan($id) {
        $this->db->where('id_krywn', $id);
        return $this->db->delete('data_karyawan');
    }
}
