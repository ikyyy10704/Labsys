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
           $id_manajer = $this->generate_id_manajer();
           $manajer_data['id_manajer'] = $id_manajer;
           
           $this->db->insert($this->table, $manajer_data);
           
           $login_data['id_manajer'] = $id_manajer;
           $login_data['level'] = 'manajer';
           $this->db->insert($this->login_table, $login_data);
           
           $this->db->trans_commit();
           return $id_manajer;
       } catch (Exception $e) {
           $this->db->trans_rollback();
           return false;
       }
   }
   
   private function generate_id_manajer() {
        $this->db->select('MAX(CASE 
        WHEN id_manajer REGEXP "^M[0-9]+$" 
        THEN CAST(SUBSTRING(id_manajer, 2) AS UNSIGNED) 
        ELSE 0 END) as max_id');
    $query = $this->db->get($this->table);
    $result = $query->row();
    $next_number = (int)$result->max_id + 1;
    return 'M' . $next_number;
   }
   
   public function update($id, $manajer_data, $login_data = []) {
       $this->db->trans_begin();
       try {
           $this->db->where('id_manajer', $id);
           $this->db->update($this->table, $manajer_data);

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
           // Get id_pengelolaan yang terkait dengan manajer
           $this->db->select('id_pengelolaan')
               ->from('kinerja_karyawan')
               ->where('id_manajer', $id);
           $pengelolaan = $this->db->get()->result();
           
           if(!empty($pengelolaan)) {
               $ids = array_column($pengelolaan, 'id_pengelolaan');
               // Hapus gaji terkait
               $this->db->where_in('id_pengelolaan', $ids)
                        ->delete('gaji_karyawan');
               
               // Update id_pengelolaan di data_karyawan menjadi null
               $this->db->where_in('id_pengelolaan', $ids)
                        ->update('data_karyawan', ['id_pengelolaan' => null]);
           }

           // Hapus data berurutan
           $this->db->where('id_manajer', $id)->delete('kinerja_karyawan');
           $this->db->where('id_manajer', $id)->delete($this->login_table);
           $this->db->where('id_manajer', $id)->delete($this->table);
           
           $this->db->trans_commit();
           return true;
       } catch (Exception $e) {
           $this->db->trans_rollback();
           log_message('error', 'Error deleting manajer: ' . $e->getMessage());
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
       $this->db->select('kk.id_pengelolaan');
       $this->db->from('kinerja_karyawan kk');
       $this->db->join('data_karyawan dk', 'kk.id_pengelolaan = dk.id_pengelolaan');
       $this->db->where('kk.id_manajer', $id);
       return $this->db->get()->num_rows() > 0;
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