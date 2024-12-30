<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Absensi_model extends CI_Model {
    public function __construct() {
        parent::__construct();
        $this->load->database();
    }

    // Ambil semua data absensi
    public function get_all_absensi() {
        $this->db->select('absensi.*, data_karyawan.nama_krywn');
        $this->db->from('absensi');
        $this->db->join('data_karyawan', 'data_karyawan.id_krywn = absensi.id_krywn');
        $query = $this->db->get();
        return $query->result();
    }

    // Ambil data absensi berdasarkan ID
    public function get_absensi_by_id($id) {
        $this->db->where('id_absensi', $id);
        $query = $this->db->get('absensi');
        return $query->row();
    }

    // Tambah data absensi
    public function create_absensi() {
        $data = array(
            'id_krywn' => $this->input->post('id_krywn'),
            'tanggal' => $this->input->post('tanggal'),
            'shift' => $this->input->post('shift'),
            'keterangan' => $this->input->post('keterangan'),
        );
        return $this->db->insert('absensi', $data);
    }

    // Update data absensi
    public function update_absensi($id) {
        $data = array(
            'id_krywn' => $this->input->post('id_krywn'),
            'tanggal' => $this->input->post('tanggal'),
            'shift' => $this->input->post('shift'),
            'keterangan' => $this->input->post('keterangan'),
        );
        $this->db->where('id_absensi', $id);
        return $this->db->update('absensi', $data);
    }

    // Hapus data absensi
    public function delete_absensi($id) {
        $this->db->where('id_absensi', $id);
        return $this->db->delete('absensi');
    }
}