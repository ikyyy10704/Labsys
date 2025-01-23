<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Dashboard_model extends CI_Model {
    
    public function __construct() {
        parent::__construct();
        $this->load->database();
    }
    
    public function count_karyawan() {
        return $this->db->count_all('data_karyawan');
    }
    
    public function count_manajer() {
        return $this->db->count_all('manajer');
    }
    
    public function count_absensi() {
        return $this->db->count_all('absensi');
    }
    
    public function count_kinerja() {
        return $this->db->count_all('kinerja_karyawan');
    }

    public function get_salary_by_department() {
        $this->db->select('m.departemen, COUNT(dk.id_krywn) as jumlah_karyawan, 
                          SUM(gk.gaji_pokok) as total_gaji, 
                          AVG(gk.gaji_pokok) as rata_rata_gaji');
        $this->db->from('manajer m');
        $this->db->join('kinerja_karyawan kk', 'm.id_manajer = kk.id_manajer', 'left');
        $this->db->join('data_karyawan dk', 'kk.id_pengelolaan = dk.id_pengelolaan', 'left');
        $this->db->join('gaji_karyawan gk', 'dk.id_krywn = gk.id_krywn', 'left');
        $this->db->group_by('m.departemen');
        return $this->db->get()->result();
    }

    public function get_top_performers($limit = 5) {
        $this->db->select('dk.nama_krywn, kk.nilai_kerja, m.departemen');
        $this->db->from('kinerja_karyawan kk');
        $this->db->join('data_karyawan dk', 'kk.id_pengelolaan = dk.id_pengelolaan');
        $this->db->join('manajer m', 'kk.id_manajer = m.id_manajer');
        $this->db->order_by('kk.nilai_kerja', 'DESC');
        $this->db->limit($limit);
        return $this->db->get()->result();
    }

    public function get_performance_distribution() {
        $this->db->select('status_pengelolaan, COUNT(*) as total');
        $this->db->from('kinerja_karyawan');
        $this->db->group_by('status_pengelolaan');
        return $this->db->get()->result();
    }
}