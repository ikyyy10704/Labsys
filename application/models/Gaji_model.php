<?php
defined('BASEPATH') OR exit('No direct script access allowed');
class Gaji_model extends CI_Model {
    public function __construct() {
        parent::__construct();
        $this->load->database(); 
    }

    public function get_all_gaji() {
        $this->db->select('gaji_karyawan.*, data_karyawan.nama_krywn');
        $this->db->from('gaji_karyawan');
        $this->db->join('data_karyawan', 'data_karyawan.id_krywn = gaji_karyawan.id_krywn');
        $query = $this->db->get();
        return $query->result();
    }

    public function get_gaji_by_id_and_date($id, $tanggal) {
        $this->db->select('gaji_karyawan.*, data_karyawan.nama_krywn');
        $this->db->from('gaji_karyawan');
        $this->db->join('data_karyawan', 'data_karyawan.id_krywn = gaji_karyawan.id_krywn');
        $this->db->where('gaji_karyawan.id_krywn', $id);
        $this->db->where('gaji_karyawan.tgl_gaji', $tanggal);
        $query = $this->db->get();
        return $query->row();
    }

    public function get_all_karyawan() {
        $query = $this->db->get('data_karyawan');
        return $query->result();
    }

    public function create_gaji() {
        $data = array(
            'id_krywn' => $this->input->post('id_krywn'),
            'tgl_gaji' => $this->input->post('tgl_gaji'), 
            'gaji_pokok' => $this->input->post('gaji_pokok'),
            'tunjangan' => $this->input->post('tunjangan'),
            'bonus' => $this->input->post('bonus'),
            'potongan' => $this->input->post('potongan')
        );
        return $this->db->insert('gaji_karyawan', $data);
    }
    public function update_gaji_by_id_and_date($id, $tanggal, $data) {
        try {
            $this->db->trans_start();
            
            $this->db->where('id_krywn', $id);
            $this->db->where('tgl_gaji', $tanggal);
            $this->db->update('gaji_karyawan', $data);
            
            $this->db->trans_complete();
            
            return $this->db->trans_status();
        } catch (Exception $e) {
            log_message('error', 'Error updating gaji: ' . $e->getMessage());
            return false;
        }
    }
    // Di Model (Gaji_model.php)
public function delete_gaji($id, $tanggal) {
    try {
        $this->db->trans_start();
        
        // Dapatkan data sebelum dihapus untuk log jika diperlukan
        $gaji = $this->get_gaji_by_id_and_date($id, $tanggal);
        
        // Hapus data dengan kondisi spesifik
        $this->db->where('id_krywn', $id);
        $this->db->where('tgl_gaji', $tanggal);
        
        // Jika ada id_pengelolaan, set NULL dulu untuk menghindari constraint
        $update_data = array(
            'id_pengelolaan' => NULL
        );
        $this->db->update('gaji_karyawan', $update_data);
        $this->db->where('id_krywn', $id);
        $this->db->where('tgl_gaji', $tanggal);
        $this->db->delete('gaji_karyawan');
        
        $this->db->trans_complete();
        
        if ($this->db->trans_status() === FALSE) {
            // Rollback akan otomatis terjadi
            log_message('error', 'Failed to delete gaji: Transaction failed');
            return false;
        }
        
        return true;
    } catch (Exception $e) {
        log_message('error', 'Error deleting gaji: ' . $e->getMessage());
        return false;
    }
}
}

