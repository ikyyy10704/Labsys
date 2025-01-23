<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Absensi_model extends CI_Model {
    
    public function __construct() {
        parent::__construct();
        $this->load->database();
    }
    
    public function get_all_absensi() {
        $this->db->select('a.*, k.nama_krywn');
        $this->db->from('absensi a');
        $this->db->join('data_karyawan k', 'k.id_krywn = a.id_krywn');
        $this->db->order_by('a.tanggal', 'DESC');
        $query = $this->db->get();
        return $query->result();
    }
    
    public function get_absensi_by_id($id) {
        $this->db->select('a.*, k.nama_krywn');
        $this->db->from('absensi a');
        $this->db->join('data_karyawan k', 'k.id_krywn = a.id_krywn');
        $this->db->where('a.id_absensi', $id);
        $query = $this->db->get();
        return $query->row();
    }

    public function get_active_employees() {
        $this->db->select('id_krywn, nama_krywn');
        $this->db->from('data_karyawan');
        $this->db->where('status', 'Aktif');
        $this->db->order_by('nama_krywn', 'ASC');
        $query = $this->db->get();
        return $query->result();
    }
    
    public function create_absensi() {
        $data = array(
            'id_krywn' => $this->input->post('id_krywn'),
            'tanggal' => $this->input->post('tanggal'),
            'shift' => $this->input->post('shift'),
            'keterangan' => $this->input->post('keterangan')
        );
        
        return $this->db->insert('absensi', $data);
    }
    
    public function update_absensi($id) {
        $data = array(
            'id_krywn' => $this->input->post('id_krywn'),
            'tanggal' => $this->input->post('tanggal'),
            'shift' => $this->input->post('shift'),
            'keterangan' => $this->input->post('keterangan')
        );
        
        $this->db->where('id_absensi', $id);
        return $this->db->update('absensi', $data);
    }
    
    public function delete_absensi($id) {
        $this->db->where('id_absensi', $id);
        return $this->db->delete('absensi');
    }

    public function get_monthly_report($month, $year) {
        $this->db->select('a.*, k.nama_krywn, k.posisi');
        $this->db->from('absensi a');
        $this->db->join('data_karyawan k', 'k.id_krywn = a.id_krywn');
        $this->db->where('MONTH(a.tanggal)', $month);
        $this->db->where('YEAR(a.tanggal)', $year);
        $this->db->order_by('k.nama_krywn', 'ASC');
        $this->db->order_by('a.tanggal', 'ASC');
        $query = $this->db->get();
        return $query->result();
    }

    public function check_duplicate_attendance($id_krywn, $tanggal) {
        $this->db->where('id_krywn', $id_krywn);
        $this->db->where('tanggal', $tanggal);
        return $this->db->count_all_results('absensi') > 0;
    }
}