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
    
    // Di Dashboard_model.php, tambahkan/modifikasi method untuk kinerja:

public function count_kinerja() {
    $this->db->select('kk.id_pengelolaan');
    $this->db->from('kinerja_karyawan kk');
    $this->db->join('data_karyawan dk', 'kk.id_pengelolaan = dk.id_pengelolaan');
    $this->db->join('manajer m', 'kk.id_manajer = m.id_manajer');
    return $this->db->count_all_results();
}

public function count_kinerja_by_status($status) {
    $this->db->select('kk.id_pengelolaan');
    $this->db->from('kinerja_karyawan kk');
    $this->db->join('data_karyawan dk', 'kk.id_pengelolaan = dk.id_pengelolaan');
    $this->db->join('manajer m', 'kk.id_manajer = m.id_manajer');
    $this->db->where('kk.status_pengelolaan', $status);
    return $this->db->count_all_results();
}

public function get_kinerja_statistics() {
    return [
        'total' => $this->count_kinerja(),
        'sangat_baik' => $this->count_kinerja_by_status('Sangat Baik'),
        'baik' => $this->count_kinerja_by_status('Baik'),
        'cukup' => $this->count_kinerja_by_status('Cukup'),
        'perlu_peningkatan' => $this->count_kinerja_by_status('Perlu Peningkatan')
    ];
}
    
    public function count_gaji() {
        return $this->db->count_all('gaji_karyawan');
    }
    
    public function count_departemen() {
        return $this->db->distinct()->count_all_results('manajer');
    }
}