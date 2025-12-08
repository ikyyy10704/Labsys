<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class User_model extends CI_Model {

    public function __construct()
    {
        parent::__construct();
        $this->load->database();
    }

    public function validate_login($username, $password)
    {
        $this->db->where('username', $username);
        $this->db->where('is_active', 1);
        $query = $this->db->get('users');
        
        if ($query->num_rows() == 1) {
            $user = $query->row_array();
            
            // Check password (MD5 hash based on your database data)
            if (md5($password) === $user['password']) {
                // Get additional user info based on role
                $user_info = $this->get_user_details($user);
                return $user_info;
            }
        }
        
        return false;
    }

    public function get_user_details($user)
    {
        $user_details = $user;
        
        switch($user['role']) {
            case 'admin':
                $this->db->select('nama_admin as nama_lengkap');
                $this->db->where('user_id', $user['user_id']);
                $query = $this->db->get('administrator');
                if ($query->num_rows() == 1) {
                    $admin_info = $query->row_array();
                    $user_details['nama_lengkap'] = $admin_info['nama_lengkap'];
                }
                break;
                
            case 'administrasi':
                $this->db->select('nama_admin as nama_lengkap, telepon');
                $this->db->where('user_id', $user['user_id']);
                $query = $this->db->get('administrasi');
                if ($query->num_rows() == 1) {
                    $admin_info = $query->row_array();
                    $user_details['nama_lengkap'] = $admin_info['nama_lengkap'];
                    $user_details['telepon'] = $admin_info['telepon'];
                }
                break;
                
            case 'petugas_lab':
                $this->db->select('nama_petugas as nama_lengkap, jenis_keahlian, telepon, alamat');
                $this->db->where('user_id', $user['user_id']);
                $query = $this->db->get('petugas_lab');
                if ($query->num_rows() == 1) {
                    $lab_info = $query->row_array();
                    $user_details['nama_lengkap'] = $lab_info['nama_lengkap'];
                    $user_details['jenis_keahlian'] = $lab_info['jenis_keahlian'];
                    $user_details['telepon'] = $lab_info['telepon'];
                    $user_details['alamat'] = $lab_info['alamat'];
                }
                break;
            case 'supervisor':
                $this->db->select('nama_supervisor as nama_lengkap, jenis_keahlian, telepon, alamat');
                $this->db->where('user_id', $user['user_id']);
                $query = $this->db->get('supervisor');
                if ($query->num_rows() == 1) {
                    $lab_info = $query->row_array();
                    $user_details['nama_lengkap'] = $lab_info['nama_lengkap'];
                    $user_details['jenis_keahlian'] = $lab_info['jenis_keahlian'];
                    $user_details['telepon'] = $lab_info['telepon'];
                    $user_details['alamat'] = $lab_info['alamat'];
                }
                break;
                

        }
        
        return $user_details;
    }

    public function get_user_by_id($user_id)
    {
        $this->db->where('user_id', $user_id);
        $query = $this->db->get('users');
        return $query->row_array();
    }

    public function get_all_users()
    {
        $this->db->select('u.*, 
                          CASE 
                              WHEN u.role = "admin" THEN a.nama_admin
                              WHEN u.role = "administrasi" THEN adm.nama_admin  
                              WHEN u.role = "petugas_lab" THEN pl.nama_petugas
                              ELSE u.username
                          END as nama_lengkap');
        $this->db->from('users u');
        $this->db->join('administrator a', 'u.user_id = a.user_id', 'left');
        $this->db->join('administrasi adm', 'u.user_id = adm.user_id', 'left');
        $this->db->join('petugas_lab pl', 'u.user_id = pl.user_id', 'left');
        $this->db->join('supervisor s', 'u.user_id = s.user_id', 'left');
        $this->db->order_by('u.created_at', 'DESC');
        $query = $this->db->get();
        return $query->result_array();
    }

    public function create_user($user_data, $role_data = null)
    {
        $this->db->trans_start();
        
        // Insert into users table
        $this->db->insert('users', $user_data);
        $user_id = $this->db->insert_id();
        
        // Insert into role-specific table
        if ($role_data && $user_id) {
            $role_data['user_id'] = $user_id;
            
            switch($user_data['role']) {
                case 'admin':
                    $this->db->insert('administrator', $role_data);
                    break;
                case 'administrasi':
                    $this->db->insert('administrasi', $role_data);
                    break;
                case 'petugas_lab':
                    $this->db->insert('petugas_lab', $role_data);
                    break;
                case 'supervisor':
                    $this->db->insert('supervisor', $role_data);
                    break;
            }
        }
        
        $this->db->trans_complete();
        
        if ($this->db->trans_status() === FALSE) {
            return false;
        }
        
        return $user_id;
    }

    public function update_user($user_id, $user_data, $role_data = null)
    {
        $this->db->trans_start();
        
        // Update users table
        $this->db->where('user_id', $user_id);
        $this->db->update('users', $user_data);
        
        // Update role-specific table
        if ($role_data) {
            $user = $this->get_user_by_id($user_id);
            
            switch($user['role']) {
                case 'admin':
                    $this->db->where('user_id', $user_id);
                    $this->db->update('administrator', $role_data);
                    break;
                case 'administrasi':
                    $this->db->where('user_id', $user_id);
                    $this->db->update('administrasi', $role_data);
                    break;
                case 'petugas_lab':
                    $this->db->where('user_id', $user_id);
                    $this->db->update('petugas_lab', $role_data);
                    break;
                case 'supervisor':
                    $this->db->where('user_id', $user_id);
                    $this->db->update('supervisor', $role_data);
                    break;
            }
        }
        
        $this->db->trans_complete();
        
        return $this->db->trans_status() !== FALSE;
    }

    public function delete_user($user_id)
    {
        $this->db->trans_start();
        
        $user = $this->get_user_by_id($user_id);
        
        // Delete from role-specific table first (foreign key constraint)
        switch($user['role']) {
            case 'admin':
                $this->db->where('user_id', $user_id);
                $this->db->delete('administrator');
                break;
            case 'administrasi':
                $this->db->where('user_id', $user_id);
                $this->db->delete('administrasi');
                break;
            case 'petugas_lab':
                $this->db->where('user_id', $user_id);
                $this->db->delete('petugas_lab');
                break;
            case 'supervisor':
                $this->db->where('user_id', $user_id);
                $this->db->delete('survervisor');
                break;
        }
        
        // Delete from users table
        $this->db->where('user_id', $user_id);
        $this->db->delete('users');
        
        $this->db->trans_complete();
        
        return $this->db->trans_status() !== FALSE;
    }

    public function change_password($user_id, $new_password)
    {
        $data = array('password' => md5($new_password));
        $this->db->where('user_id', $user_id);
        return $this->db->update('users', $data);
    }

    public function log_activity($user_id, $activity, $table_affected = null, $record_id = null)
    {
        $data = array(
            'user_id' => $user_id,
            'activity' => $activity,
            'table_affected' => $table_affected,
            'record_id' => $record_id,
            'ip_address' => $this->input->ip_address(),
            'created_at' => date('Y-m-d H:i:s')
        );
        
        return $this->db->insert('activity_log', $data);
    }

    public function get_user_stats()
    {
        $stats = array();
        
        // Total users by role
        $this->db->select('role, COUNT(*) as count');
        $this->db->where('is_active', 1);
        $this->db->group_by('role');
        $query = $this->db->get('users');
        
        foreach ($query->result_array() as $row) {
            $stats['by_role'][$row['role']] = $row['count'];
        }
        
        // Total active users
        $stats['total_active'] = $this->db->where('is_active', 1)->count_all_results('users');
        
        // Total inactive users
        $stats['total_inactive'] = $this->db->where('is_active', 0)->count_all_results('users');
        
        return $stats;
    }

    public function check_username_exists($username, $exclude_user_id = null)
    {
        $this->db->where('username', $username);
        if ($exclude_user_id) {
            $this->db->where('user_id !=', $exclude_user_id);
        }
        
        return $this->db->count_all_results('users') > 0;
    }
}