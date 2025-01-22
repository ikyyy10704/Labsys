<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Absensi_model extends CI_Model {
    public function __construct() {
        parent::__construct();
        $this->load->database();
    }

    public function generate_absensi_id() {
        // Mendapatkan ID terakhir yang digunakan
        $this->db->select('id_absensi');
        $this->db->from('absensi');
        $this->db->order_by('id_absensi', 'DESC');
        $this->db->limit(1);
        $query = $this->db->get();
    
        if ($last_id) {
            $last_number = substr($last_id->id_manajer, 2); // Mengambil angka setelah "M-"
            $new_number = $last_number + 1; // Menambah angka
        } else {
            // Jika tidak ada data sebelumnya, ID pertama adalah M-102
            $new_number = 102; // Mulai dari 102
        }
    
        return 'A-' . $new_number; // Menggabungkan 'M-' dan angka baru
    
    }
       
    // Mendapatkan semua absensi dan menggabungkan dengan data karyawan
    public function get_all_absensi() {
        $this->db->select('absensi.*, data_karyawan.nama_krywn');
        $this->db->from('absensi');
        $this->db->join('data_karyawan', 'data_karyawan.id_krywn = absensi.id_krywn');
        $query = $this->db->get();
        return $query->result();
    }

    // Mendapatkan absensi berdasarkan ID
    public function get_absensi_by_id($id) {
        $this->db->where('id_absensi', $id);
        $query = $this->db->get('absensi');
        return $query->row();
    }

    // Membuat data absensi baru
    public function create_absensi() {
        $data = array(
            'id_absensi' => $this->generate_absensi_id(),  // Menambahkan ID dengan format A-<angka>
            'id_krywn' => $this->input->post('id_krywn'),
            'tanggal' => $this->input->post('tanggal'),
            'shift' => $this->input->post('shift'),
            'keterangan' => $this->input->post('keterangan'),
        );
        // Insert data absensi baru
        return $this->db->insert('absensi', $data);
    }

    // Memperbarui data absensi
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

    // Menghapus data absensi
    public function delete_absensi($id) {
        // Pastikan ID absensi tidak kosong
        if (empty($id)) {
            return false;
        }
    
        // Menghapus data absensi berdasarkan ID
        $this->db->where('id_absensi', $id);
        return $this->db->delete('absensi');
    }
    

    // Mengambil semua data karyawan
    public function get_all_karyawan() {
        $this->db->select('id_krywn, nama_krywn');
        $this->db->from('data_karyawan');
        $query = $this->db->get();
        return $query->result();
    }
}
?>
