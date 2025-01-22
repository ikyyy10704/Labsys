<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Manajer_model extends CI_Model {
    
    private $table = 'manajer';
    private $login_table = 'login';
    private $view_table = 'vw_manajer_login';
    
    public function __construct() {
        parent::__construct();
        $this->load->database();
    }
    
    public function get_all() {
        $this->db->from($this->view_table);
        $query = $this->db->get();
        return $query->result();
    }
    
    public function get_by_id($id) {
        $this->db->from($this->view_table);
        $this->db->where('id_manajer', $id);
        $query = $this->db->get();
        return $query->row();
    }
    
    public function create($manajer_data, $login_data) {
        $this->db->trans_begin();

        try {
            // Sisipkan data manajer dan dapatkan id yang disisipkan
            $this->db->insert($this->table, $manajer_data);
            $id_manajer = $this->db->insert_id();

            // Tambahkan id_manajer ke dalam data login
            $login_data['id_manajer'] = $id_manajer;

            // Sisipkan data login
            $this->db->insert($this->login_table, $login_data);

            if ($this->db->trans_status() === FALSE) {
                throw new Exception('Gagal menyimpan data');
            }

            $this->db->trans_commit();
            return $id_manajer; // Kembalikan id manajer yang disisipkan
        } catch (Exception $e) {
            $this->db->trans_rollback();
            return false;
        }
    }
    
    public function update($id, $manajer_data, $login_data = []) {
        $this->db->trans_begin();

        try {
            // Perbarui data manajer
            $this->db->where('id_manajer', $id);
            $this->db->update($this->table, $manajer_data);

            // Perbarui data login jika diberikan
            if (!empty($login_data)) {
                $this->db->where('id_manajer', $id);
                $this->db->update($this->login_table, $login_data);
            }

            if ($this->db->trans_status() === FALSE) {
                throw new Exception('Gagal memperbarui data');
            }

            $this->db->trans_commit();
            return true;
        } catch (Exception $e) {
            $this->db->trans_rollback();
            return false;
        }
    }
    
    public function delete($id) {
        $this->db->trans_begin();

        try {
            // Periksa apakah manajer memiliki catatan kinerja terkait
            if ($this->has_kinerja_records($id)) {
                throw new Exception('Manajer memiliki catatan kinerja terkait');
            }

            // Hapus dari tabel login (akan terkaskat ke tabel manajer)
            $this->db->where('id_manajer', $id);
            $this->db->delete($this->login_table);

            if ($this->db->trans_status() === FALSE) {
                throw new Exception('Gagal menghapus data');
            }

            $this->db->trans_commit();
            return true;
        } catch (Exception $e) {
            $this->db->trans_rollback();
            return false;
        }
    }
    
    public function is_unique_username($username, $id = null) {
        $this->db->where('username', $username);
        if ($id) {
            $this->db->where('id_manajer !=', $id);
        }
        return $this->db->get($this->login_table)->num_rows() === 0;
    }
    
    public function is_unique_email($email, $id = null) {
        $this->db->where('email', $email);
        if ($id) {
            $this->db->where('id_manajer !=', $id);
        }
        return $this->db->get($this->login_table)->num_rows() === 0;
    }
    
    public function upload_foto($id, $foto) {
        $this->db->where('id_manajer', $id);
        return $this->db->update($this->table, ['foto' => $foto]);
    }
    
    public function has_kinerja_records($id) {
        $this->db->where('id_manajer', $id);
        return $this->db->get('kinerja_karyawan')->num_rows() > 0;
    }
    
    public function get_dropdown() {
        $this->db->select('id_manajer, nama_manajer');
        $this->db->from($this->table);
        $this->db->order_by('nama_manajer', 'ASC');
        $query = $this->db->get();
        
        $dropdown = array('' => '-- Pilih Manajer --');
        foreach ($query->result() as $row) {
            $dropdown[$row->id_manajer] = $row->nama_manajer;
        }
        
        return $dropdown;
    }
}