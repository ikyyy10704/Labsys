<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Kinerja_model extends CI_Model {
    private $table = 'kinerja_karyawan';

    public function __construct() {
        parent::__construct();
        $this->load->database();
    }

    public function get_kinerja_karyawan() {
        // Menggunakan view yang sudah dibuat di database
        $this->db->select('nama_krywn, nilai_kerja, status_pengelolaan, nama_manajer, departemen, tgl_pengelolaan');
        $this->db->from('vw_kinerja_detail');
        return $this->db->get()->result();
        $query = $this->db->get('vw_kinerja_detail');
        return $query->result();
    }

    public function get_kinerja_by_id($id) {
        $this->db->where('id_pengelolaan', $id);
        $query = $this->db->get('vw_kinerja_detail');
        return $query->row();
    }

    public function insert_kinerja($data) {
        // Validasi nilai kerja sebelum insert
        if (!is_numeric($data['nilai_kerja']) || $data['nilai_kerja'] < 0 || $data['nilai_kerja'] > 100) {
            return false;
        }
        
        // Set status pengelolaan berdasarkan fn_status_kinerja
        $kehadiran = $this->db->query("SELECT COUNT(*) as total FROM absensi WHERE id_krywn = ? AND keterangan = 'Hadir'", 
            array($data['id_krywn']))->row()->total;
            
        $this->db->select("fn_status_kinerja($kehadiran, {$data['nilai_kerja']}) as status");
        $status = $this->db->get()->row()->status;
        $data['status_pengelolaan'] = $status;

        return $this->db->insert($this->table, $data);
    }

    public function update_kinerja($id, $data) {
        // Validasi nilai kerja sebelum update
        if (!is_numeric($data['nilai_kerja']) || $data['nilai_kerja'] < 0 || $data['nilai_kerja'] > 100) {
            return false;
        }

        // Update status pengelolaan menggunakan fungsi database
        $kehadiran = $this->db->query("SELECT COUNT(*) as total FROM absensi WHERE id_krywn = ? AND keterangan = 'Hadir'", 
            array($data['id_krywn']))->row()->total;
            
        $this->db->select("fn_status_kinerja($kehadiran, {$data['nilai_kerja']}) as status");
        $status = $this->db->get()->row()->status;
        $data['status_pengelolaan'] = $status;

        $this->db->where('id_pengelolaan', $id);
        return $this->db->update($this->table, $data);
    }

    public function delete_kinerja($id) {
        // Cek relasi di gaji_karyawan sebelum hapus
        $this->db->where('id_pengelolaan', $id);
        $check = $this->db->get('gaji_karyawan')->num_rows();
        
        if ($check > 0) {
            return false; // Tidak bisa hapus karena masih ada relasi
        }

        $this->db->where('id_pengelolaan', $id);
        return $this->db->delete($this->table);
    }

    // Method tambahan untuk mendapatkan data pendukung
    public function get_all_manajer() {
        return $this->db->get('manajer')->result();
    }

    public function get_active_karyawan() {
        $this->db->where('status', 'Aktif');
        return $this->db->get('data_karyawan')->result();
    }

    public function get_kinerja_grade($nilai) {
        $this->db->select("fn_grade_kinerja($nilai) as grade");
        return $this->db->get()->row()->grade;
    }

    public function hitung_bonus_tahunan($masa_kerja, $nilai_kinerja) {
        $this->db->select("fn_hitung_bonus_tahunan($masa_kerja, $nilai_kinerja) as bonus");
        return $this->db->get()->row()->bonus;
    }
}