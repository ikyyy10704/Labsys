<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Kinerja_model extends CI_Model {
    private $table = 'kinerja_karyawan';

    public function __construct() {
        parent::__construct();
        $this->load->database();
    }

    public function get_kinerja_karyawan() {
        $this->db->select('kk.id_pengelolaan, kk.nilai_kerja, kk.status_pengelolaan, 
                      kk.tgl_pengelolaan, dk.nama_krywn, m.nama_manajer, 
                      m.departemen');
        $this->db->from('kinerja_karyawan kk');
        $this->db->join('data_karyawan dk', 'kk.id_pengelolaan = dk.id_pengelolaan');
        $this->db->join('manajer m', 'kk.id_manajer = m.id_manajer');
        return $this->db->get()->result();
        return $this->db->get()->result();
        $query = $this->db->get('vw_kinerja_detail');
        return $query->result();
    }

    public function get_kinerja_by_id($id) {
        $this->db->select('kk.id_pengelolaan, kk.nilai_kerja, kk.status_pengelolaan, 
                      kk.tgl_pengelolaan, kk.id_manajer, dk.nama_krywn, 
                      m.nama_manajer, m.departemen');
    $this->db->from('kinerja_karyawan kk');
    $this->db->join('data_karyawan dk', 'kk.id_pengelolaan = dk.id_pengelolaan');
    $this->db->join('manajer m', 'kk.id_manajer = m.id_manajer');
    $this->db->where('kk.id_pengelolaan', $id);
    return $this->db->get()->row();
    }
    public function get_kinerja_by_date($date) {
        $this->db->select('kk.*, dk.nama_krywn, m.nama_manajer, m.departemen');
        $this->db->from('kinerja_karyawan kk');
        $this->db->join('data_karyawan dk', 'kk.id_pengelolaan = dk.id_pengelolaan');
        $this->db->join('manajer m', 'kk.id_manajer = m.id_manajer');
        $this->db->where('DATE(kk.tgl_pengelolaan)', $date);
        return $this->db->get()->result();
    }

    public function insert_kinerja($data, $id_krywn) {
        $this->db->trans_start();
        try {
            $this->db->insert('kinerja_karyawan', $data);
            $id_pengelolaan = $this->db->insert_id();
            $this->db->where('id_krywn', $id_krywn);
            $this->db->update('data_karyawan', ['id_pengelolaan' => $id_pengelolaan]);
            $this->db->where('id_krywn', $id_krywn);
            $this->db->order_by('tanggal', 'DESC');
            $this->db->limit(1);
            $absensi = $this->db->get('absensi');
            
            if ($absensi->num_rows() > 0) {
                $this->db->where('id_pengelolaan', $id_pengelolaan);
                $this->db->update('kinerja_karyawan', [
                    'id_absensi' => $absensi->row()->id_absensi
                ]);
            }
    
            $this->db->trans_complete();
            return $this->db->trans_status();
    
        } catch (Exception $e) {
            $this->db->trans_rollback();
            log_message('error', 'Error inserting kinerja: ' . $e->getMessage());
            return false;
        }
    }
    public function update_kinerja($id_pengelolaan, $data) {
        $this->db->trans_start();
        try {
            if (!is_numeric($data['nilai_kerja']) || $data['nilai_kerja'] < 0 || $data['nilai_kerja'] > 100) {
                return false;
            }

            $this->db->where('id_pengelolaan', $id_pengelolaan);
            $this->db->update('kinerja_karyawan', $data);
            $karyawan = $this->db->get_where('data_karyawan', ['id_pengelolaan' => $id_pengelolaan])->row();
            
            if ($karyawan) {
                $kehadiran = $this->db->query(
                    "SELECT COUNT(*) as total FROM absensi 
                    WHERE id_krywn = ? AND keterangan = 'Hadir'", 
                    array($karyawan->id_krywn)
                )->row()->total;
                $status = $this->db->query(
                    "SELECT fn_status_kinerja(?, ?) as status",
                    array($kehadiran, $data['nilai_kerja'])
                )->row()->status;
                
                $this->db->where('id_pengelolaan', $id_pengelolaan);
                $this->db->update('kinerja_karyawan', ['status_pengelolaan' => $status]);
            }
    
            $this->db->trans_complete();
            return $this->db->trans_status();
    
        } catch (Exception $e) {
            $this->db->trans_rollback();
            log_message('error', 'Error updating kinerja: ' . $e->getMessage());
            return false;
        }
    }
    
    public function get_all_manajer() {
        return $this->db->get('manajer')->result();
    }

    public function get_active_karyawan() {
        $this->db->select('id_krywn, nama_krywn, posisi');
        $this->db->from('data_karyawan');
        $this->db->where('status', 'Aktif');
        $this->db->order_by('nama_krywn', 'ASC');
        return $this->db->get()->result();
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