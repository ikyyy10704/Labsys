<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Data_karyawan_model extends CI_Model {
    public function __construct() {
        parent::__construct();
        $this->load->database();
    }
    
    public function get_next_id() {
        $this->db->select_max('id_krywn');
        $query = $this->db->get('data_karyawan');
        $last_id = $query->row()->id_krywn;
        return $last_id ? $last_id + 1 : 1;
    }
    
    public function get_all_karyawan() {
        $this->db->order_by('id_krywn', 'ASC');
        return $this->db->get('data_karyawan')->result_array();
    }

    public function get_active_karyawan() {
        $this->db->where('status', 'Aktif');
        $this->db->order_by('nama_krywn', 'ASC');
        return $this->db->get('data_karyawan')->result_array();
    }

    public function get_karyawan_by_id($id) {
        return $this->db->get_where('data_karyawan', ['id_krywn' => $id])->row_array();
    }

    public function create_karyawan($data) {
        // Ensure id_krywn is auto-incremented
        $data['id_krywn'] = $this->get_next_id();
        return $this->db->insert('data_karyawan', $data);
    }

    public function update_karyawan($id, $data) {
        $this->db->where('id_krywn', $id);
        return $this->db->update('data_karyawan', $data);
    }

    public function delete_karyawan($id) {
        $this->db->trans_start();
        
        // Delete from dependent tables first
        $tables = [
            'log_status_karyawan',
            'log_perubahan_gaji',
            'gaji_karyawan',
            'absensi'
        ];
        
        foreach ($tables as $table) {
            $this->db->where('id_krywn', $id);
            $this->db->delete($table);
        }
        
        // Finally delete the employee record
        $this->db->where('id_krywn', $id);
        $result = $this->db->delete('data_karyawan');
        
        $this->db->trans_complete();
        
        if ($this->db->trans_status() === FALSE) {
            // Something went wrong
            $this->db->trans_rollback();
            return false;
        } else {
            $this->db->trans_commit();
            return $result;
        }
    }

    public function check_email_exists($email, $id = null) {
        $this->db->where('email', $email);
        if ($id) {
            $this->db->where('id_krywn !=', $id);
        }
        return $this->db->get('data_karyawan')->num_rows() > 0;
    }

    public function get_karyawan_count() {
        return $this->db->count_all('data_karyawan');
    }

    public function get_active_karyawan_count() {
        $this->db->where('status', 'Aktif');
        return $this->db->count_all_results('data_karyawan');
    }

    public function get_karyawan_by_position($position) {
        $this->db->where('posisi', $position);
        $this->db->order_by('nama_krywn', 'ASC');
        return $this->db->get('data_karyawan')->result_array();
    }

    public function search_karyawan($keyword) {
        $this->db->like('nama_krywn', $keyword);
        $this->db->or_like('email', $keyword);
        $this->db->or_like('posisi', $keyword);
        $this->db->order_by('nama_krywn', 'ASC');
        return $this->db->get('data_karyawan')->result_array();
    }

    public function check_duplicate_data($data, $id = null) {
        if ($id) {
            $this->db->where('id_krywn !=', $id);
        }
        $this->db->where('email', $data['email']);
        return $this->db->get('data_karyawan')->num_rows() > 0;
    }

    public function validate_foreign_keys($id) {
        $tables = ['gaji_karyawan', 'absensi', 'log_status_karyawan', 'log_perubahan_gaji'];
        foreach ($tables as $table) {
            $this->db->where('id_krywn', $id);
            if ($this->db->count_all_results($table) > 0) {
                return false;
            }
        }
        return true;
    }
}