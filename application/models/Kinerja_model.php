<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Kinerja_model extends CI_Model {

    public function get_kinerja_karyawan() {
        return $this->db->get('kinerja')->result();
    }

    public function get_nama_karyawan($id_krywn) {
        $this->db->select('nama');
        $this->db->from('karyawan');
        $this->db->where('id_krywn', $id_krywn);
        $query = $this->db->get();
        return ($query->num_rows() > 0) ? $query->row()->nama : 'Tidak tersedia';
    }

    public function get_nama_manajer($id_manajer) {
        $this->db->select('nama_manajer');
        $this->db->from('manajer');
        $this->db->where('id_manajer', $id_manajer);
        $query = $this->db->get();
        return ($query->num_rows() > 0) ? $query->row()->nama_manajer : 'Tidak tersedia';
    }

    public function get_departemen_by_manajer($id_manajer) {
        $this->db->select('departemen');
        $this->db->from('departemen');
        $this->db->where('id_manajer', $id_manajer);
        $query = $this->db->get();
        return ($query->num_rows() > 0) ? $query->row()->departemen : 'Tidak tersedia';
    }

    // Implementasi fungsi lainnya...
}
?>