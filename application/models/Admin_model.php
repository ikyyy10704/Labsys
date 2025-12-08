<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Admin_model extends CI_Model {

    public function __construct() {
        parent::__construct();
        $this->load->database();
    }

    // ==========================================
    // DASHBOARD FUNCTIONS
    // ==========================================

    public function get_system_health() {
        $health = array();
        
        try {
            $this->db->get('users', 1);
            $health['database'] = 'online';
        } catch (Exception $e) {
            $health['database'] = 'offline';
        }
        
        $health['storage'] = array(
            'used' => 78,
            'total' => 100,
            'status' => 'warning'
        );
        
        $health['active_users'] = $this->db->where('is_active', 1)->count_all_results('users');
        
        return $health;
    }

    public function get_operational_stats() {
        $stats = array();
        
        // Today's statistics
        $today = date('Y-m-d');
        $stats['today'] = array(
            'new_patients' => $this->db->where('DATE(created_at)', $today)->count_all_results('pasien'),
            'examinations' => $this->db->where('DATE(tanggal_pemeriksaan)', $today)->count_all_results('pemeriksaan_lab'),
            'completed_tests' => $this->db->where('status_pemeriksaan', 'selesai')->where('DATE(created_at)', $today)->count_all_results('pemeriksaan_lab'),
            'revenue' => $this->_get_today_revenue()
        );
        
        // This month's statistics
        $this_month = date('Y-m');
        $stats['this_month'] = array(
            'new_patients' => $this->db->where("DATE_FORMAT(created_at, '%Y-%m') = ", $this_month, FALSE)->count_all_results('pasien'),
            'examinations' => $this->db->where("DATE_FORMAT(tanggal_pemeriksaan, '%Y-%m') = ", $this_month, FALSE)->count_all_results('pemeriksaan_lab'),
            'total_revenue' => $this->_get_month_revenue($this_month)
        );
        
        // Equipment status
        $this->db->select('status_alat, COUNT(*) as count');
        $this->db->group_by('status_alat');
        $equipment_query = $this->db->get('alat_laboratorium');
        $stats['equipment'] = array();
        foreach ($equipment_query->result_array() as $row) {
            $stats['equipment'][$row['status_alat']] = $row['count'];
        }
        
        return $stats;
    }

    public function count_all_activities() {
        return $this->db->count_all('activity_log');
    }

    public function get_recent_examinations($limit = 20) {
        $this->db->select('pl.*, p.nama as nama_pasien, p.dokter_perujuk, p.asal_rujukan');
        $this->db->from('pemeriksaan_lab pl');
        $this->db->join('pasien p', 'pl.pasien_id = p.pasien_id');
        $this->db->order_by('pl.created_at', 'DESC');
        $this->db->limit($limit);
        
        return $this->db->get()->result_array();
    }

    // ==========================================
    // USER MANAGEMENT
    // ==========================================

    public function get_user_by_id($user_id) {
        $this->db->where('user_id', $user_id);
        $query = $this->db->get('users');
        
        return $query->row_array();
    }

    public function get_user_details($user) {
        $details = array();
        
        switch($user['role']) {
            case 'admin':
                $this->db->select('nama_admin as nama_lengkap');
                $this->db->where('user_id', $user['user_id']);
                $query = $this->db->get('administrator');
                if ($query->num_rows() > 0) {
                    $details = $query->row_array();
                }
                break;
                
            case 'administrasi':
                $this->db->select('nama_admin as nama_lengkap, telepon');
                $this->db->where('user_id', $user['user_id']);
                $query = $this->db->get('administrasi');
                if ($query->num_rows() > 0) {
                    $details = $query->row_array();
                }
                break;
                
            case 'petugas_lab':
                $this->db->select('nama_petugas as nama_lengkap, jenis_keahlian, telepon, alamat');
                $this->db->where('user_id', $user['user_id']);
                $query = $this->db->get('petugas_lab');
                if ($query->num_rows() > 0) {
                    $details = $query->row_array();
                }
                break;
        }
        
        return $details;
    }

    public function create_user($user_data, $role_data) {
        $this->db->trans_start();
        
        try {
            $this->db->insert('users', $user_data);
            $user_id = $this->db->insert_id();
            
            if ($user_id) {
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
                }
            }
            
            $this->db->trans_complete();
            
            if ($this->db->trans_status() === FALSE) {
                return FALSE;
            }
            
            return $user_id;
            
        } catch (Exception $e) {
            $this->db->trans_rollback();
            log_message('error', 'Error creating user: ' . $e->getMessage());
            return FALSE;
        }
    }

    public function update_user($user_id, $user_data, $role_data = array()) {
        $this->db->trans_start();
        
        try {
            $this->db->where('user_id', $user_id);
            $this->db->update('users', $user_data);
            
            if (!empty($role_data)) {
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
                }
            }
            
            $this->db->trans_complete();
            
            return $this->db->trans_status() !== FALSE;
            
        } catch (Exception $e) {
            $this->db->trans_rollback();
            log_message('error', 'Error updating user: ' . $e->getMessage());
            return FALSE;
        }
    }

    public function check_username_exists($username, $exclude_user_id = null) {
        $this->db->where('username', $username);
        
        if ($exclude_user_id) {
            $this->db->where('user_id !=', $exclude_user_id);
        }
        
        $query = $this->db->get('users');
        return $query->num_rows() > 0;
    }

    public function get_all_users() {
        try {
            $sql = "
                SELECT 
                    u.*,
                    CASE 
                        WHEN u.role = 'admin' THEN a.nama_admin
                        WHEN u.role = 'administrasi' THEN ad.nama_admin  
                        WHEN u.role = 'petugas_lab' THEN p.nama_petugas
                        ELSE u.username
                    END as nama_lengkap
                FROM users u
                LEFT JOIN administrator a ON u.user_id = a.user_id AND u.role = 'admin'
                LEFT JOIN administrasi ad ON u.user_id = ad.user_id AND u.role = 'administrasi'
                LEFT JOIN petugas_lab p ON u.user_id = p.user_id AND u.role = 'petugas_lab'
                WHERE u.role != 'dokter'
                ORDER BY u.created_at DESC
            ";
            
            $query = $this->db->query($sql);
            
            if ($this->db->error()['code'] != 0) {
                log_message('error', 'Database error in get_all_users: ' . $this->db->error()['message']);
                return $this->get_all_users_fallback();
            }
            
            return $query->result_array();
            
        } catch (Exception $e) {
            log_message('error', 'Exception in get_all_users: ' . $e->getMessage());
            return $this->get_all_users_fallback();
        }
    }

    private function get_all_users_fallback() {
        try {
            $this->db->select('*');
            $this->db->from('users');
            $this->db->where('role !=', 'dokter');
            $this->db->order_by('created_at', 'DESC');
            
            $users = $this->db->get()->result_array();
            
            if (empty($users)) {
                return array();
            }
            
            foreach ($users as $key => $user) {
                $nama_lengkap = $user['username'];
                
                if ($user['role'] == 'admin') {
                    $this->db->select('nama_admin');
                    $this->db->where('user_id', $user['user_id']);
                    $query = $this->db->get('administrator');
                    if ($query->num_rows() > 0) {
                        $row = $query->row_array();
                        $nama_lengkap = $row['nama_admin'];
                    }
                } elseif ($user['role'] == 'administrasi') {
                    $this->db->select('nama_admin');
                    $this->db->where('user_id', $user['user_id']);
                    $query = $this->db->get('administrasi');
                    if ($query->num_rows() > 0) {
                        $row = $query->row_array();
                        $nama_lengkap = $row['nama_admin'];
                    }
                } elseif ($user['role'] == 'petugas_lab') {
                    $this->db->select('nama_petugas');
                    $this->db->where('user_id', $user['user_id']);
                    $query = $this->db->get('petugas_lab');
                    if ($query->num_rows() > 0) {
                        $row = $query->row_array();
                        $nama_lengkap = $row['nama_petugas'];
                    }
                }
                
                $users[$key]['nama_lengkap'] = $nama_lengkap;
            }
            
            return $users;
            
        } catch (Exception $e) {
            log_message('error', 'Exception in get_all_users_fallback: ' . $e->getMessage());
            return array();
        }
    }

    public function get_user_statistics() {
        try {
            $stats = array();
            
            $this->db->where('role !=', 'dokter');
            $stats['total'] = $this->db->count_all_results('users');
            
            $this->db->where('is_active', 1);
            $this->db->where('role !=', 'dokter');
            $stats['active'] = $this->db->count_all_results('users');
            
            $stats['by_role'] = array(
                'admin' => 0,
                'administrasi' => 0,
                'petugas_lab' => 0
            );
            
            $this->db->where('role', 'admin');
            $this->db->where('is_active', 1);
            $stats['by_role']['admin'] = $this->db->count_all_results('users');
            
            $this->db->where('role', 'administrasi');
            $this->db->where('is_active', 1);
            $stats['by_role']['administrasi'] = $this->db->count_all_results('users');
            
            $this->db->where('role', 'petugas_lab');
            $this->db->where('is_active', 1);
            $stats['by_role']['petugas_lab'] = $this->db->count_all_results('users');
            
            return $stats;
            
        } catch (Exception $e) {
            log_message('error', 'Exception in get_user_statistics: ' . $e->getMessage());
            return array(
                'total' => 0,
                'active' => 0,
                'by_role' => array(
                    'admin' => 0,
                    'administrasi' => 0,
                    'petugas_lab' => 0
                )
            );
        }
    }

    public function update_user_status($user_id, $status) {
        $this->db->where('user_id', $user_id);
        return $this->db->update('users', array('is_active' => $status));
    }

    public function delete_user($user_id) {
        $this->db->trans_start();
        
        try {
            $user = $this->get_user_by_id($user_id);
            
            if (!$user) {
                log_message('error', 'Attempt to delete non-existent user: ' . $user_id);
                return FALSE;
            }
            
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
            }
            
            $this->db->where('user_id', $user_id);
            $this->db->delete('activity_log');
            
            $this->db->where('user_id', $user_id);
            $this->db->delete('users');
            
            $this->db->trans_complete();
            
            if ($this->db->trans_status() === FALSE) {
                return FALSE;
            }
            
            return TRUE;
            
        } catch (Exception $e) {
            $this->db->trans_rollback();
            log_message('error', 'Exception when deleting user ' . $user_id . ': ' . $e->getMessage());
            return FALSE;
        }
    }

    public function count_activity_logs($filters = array()) {
        try {
            $this->db->from('activity_log al');
            $this->db->join('users u', 'al.user_id = u.user_id', 'left');
            
            if (!empty($filters['start_date'])) {
                $this->db->where('DATE(al.created_at) >=', $filters['start_date']);
            }
            
            if (!empty($filters['end_date'])) {
                $this->db->where('DATE(al.created_at) <=', $filters['end_date']);
            }
            
            if (!empty($filters['user_id'])) {
                $this->db->where('al.user_id', $filters['user_id']);
            }
            
            if (!empty($filters['activity_type'])) {
                $this->db->like('al.activity', $filters['activity_type']);
            }
            
            if (!empty($filters['table_affected'])) {
                $this->db->where('al.table_affected', $filters['table_affected']);
            }
            
            if (!empty($filters['search'])) {
                $this->db->group_start();
                $this->db->like('al.activity', $filters['search']);
                $this->db->or_like('u.username', $filters['search']);
                $this->db->or_like('al.table_affected', $filters['search']);
                $this->db->group_end();
            }
            
            return $this->db->count_all_results();
            
        } catch (Exception $e) {
            log_message('error', 'Error counting activity logs: ' . $e->getMessage());
            return 0;
        }
    }


    public function get_activity_tables() {
        try {
            $this->db->distinct();
            $this->db->select('table_affected');
            $this->db->where('table_affected IS NOT NULL');
            $this->db->where('table_affected !=', '');
            $this->db->order_by('table_affected', 'ASC');
            $query = $this->db->get('activity_log');
            
            $result = array();
            foreach ($query->result_array() as $row) {
                $result[] = $row['table_affected'];
            }
            
            return $result;
            
        } catch (Exception $e) {
            log_message('error', 'Error getting activity tables: ' . $e->getMessage());
            return array();
        }
    }

    public function get_examination_statistics($filters = array()) {
        try {
            $stats = array();
            
            $where_conditions = $this->_build_examination_where_conditions($filters);
            
            $this->db->from('pemeriksaan_lab pl');
            $this->db->join('pasien p', 'pl.pasien_id = p.pasien_id', 'left');
            $this->db->join('petugas_lab pt', 'pl.petugas_id = pt.petugas_id', 'left');
            
            foreach ($where_conditions as $condition) {
                $this->db->where($condition['field'], $condition['value'], $condition['escape']);
            }
            
            $stats['total'] = $this->db->count_all_results();
            
            $status_list = ['pending', 'progress', 'selesai', 'cancelled'];
            foreach ($status_list as $status) {
                $this->db->from('pemeriksaan_lab pl');
                $this->db->join('pasien p', 'pl.pasien_id = p.pasien_id', 'left');
                $this->db->join('petugas_lab pt', 'pl.petugas_id = pt.petugas_id', 'left');
                $this->db->where('pl.status_pemeriksaan', $status);
                
                foreach ($where_conditions as $condition) {
                    $this->db->where($condition['field'], $condition['value'], $condition['escape']);
                }
                
                $stats[$status] = $this->db->count_all_results();
            }
            
            $stats['completion_rate'] = $stats['total'] > 0 ? round(($stats['selesai'] / $stats['total']) * 100, 2) : 0;
            $stats['pending_rate'] = $stats['total'] > 0 ? round(($stats['pending'] / $stats['total']) * 100, 2) : 0;
            
            return $stats;
            
        } catch (Exception $e) {
            log_message('error', 'Error getting examination statistics: ' . $e->getMessage());
            return array(
                'total' => 0,
                'pending' => 0,
                'progress' => 0,
                'selesai' => 0,
                'cancelled' => 0,
                'completion_rate' => 0,
                'pending_rate' => 0
            );
        }
    }

    public function get_examination_chart_data($filters = array()) {
        try {
            $chart_data = array();
            
            $where_conditions = $this->_build_examination_where_conditions($filters);
            
            $this->db->select('DATE(pl.tanggal_pemeriksaan) as date, COUNT(*) as count');
            $this->db->from('pemeriksaan_lab pl');
            $this->db->join('pasien p', 'pl.pasien_id = p.pasien_id', 'left');
            
            if (empty($filters['start_date']) && empty($filters['end_date'])) {
                $this->db->where('pl.tanggal_pemeriksaan >=', date('Y-m-d', strtotime('-7 days')));
            }
            
            foreach ($where_conditions as $condition) {
                $this->db->where($condition['field'], $condition['value'], $condition['escape']);
            }
            
            $this->db->group_by('DATE(pl.tanggal_pemeriksaan)');
            $this->db->order_by('DATE(pl.tanggal_pemeriksaan)', 'ASC');
            
            $trend_result = $this->db->get()->result_array();
            $chart_data['trend'] = $trend_result;
            
            $this->db->select('pl.status_pemeriksaan, COUNT(*) as count');
            $this->db->from('pemeriksaan_lab pl');
            $this->db->join('pasien p', 'pl.pasien_id = p.pasien_id', 'left');
            
            foreach ($where_conditions as $condition) {
                $this->db->where($condition['field'], $condition['value'], $condition['escape']);
            }
            
            $this->db->group_by('pl.status_pemeriksaan');
            $status_result = $this->db->get()->result_array();
            
            $chart_data['status'] = array();
            foreach ($status_result as $row) {
                $chart_data['status'][$row['status_pemeriksaan']] = $row['count'];
            }
            
            return $chart_data;
            
        } catch (Exception $e) {
            log_message('error', 'Error getting examination chart data: ' . $e->getMessage());
            return array();
        }
    }

    public function get_examination_reports($limit = 20, $offset = 0, $filters = array()) {
        try {
            $this->db->select('
                pl.*,
                p.nama as nama_pasien,
                p.nik,
                p.umur,
                p.jenis_kelamin,
                p.dokter_perujuk,
                p.asal_rujukan,
                pt.nama_petugas
            ');
            $this->db->from('pemeriksaan_lab pl');
            $this->db->join('pasien p', 'pl.pasien_id = p.pasien_id', 'left');
            $this->db->join('petugas_lab pt', 'pl.petugas_id = pt.petugas_id', 'left');
            
            $where_conditions = $this->_build_examination_where_conditions($filters);
            foreach ($where_conditions as $condition) {
                $this->db->where($condition['field'], $condition['value'], $condition['escape']);
            }
            
            if (!empty($filters['search'])) {
                $search_term = $filters['search'];
                $this->db->group_start();
                $this->db->like('pl.nomor_pemeriksaan', $search_term);
                $this->db->or_like('p.nama', $search_term);
                $this->db->or_like('p.nik', $search_term);
                $this->db->or_like('pl.jenis_pemeriksaan', $search_term);
                $this->db->or_like('pt.nama_petugas', $search_term);
                $this->db->group_end();
            }
            
            $this->db->order_by('pl.tanggal_pemeriksaan', 'DESC');
            $this->db->order_by('pl.created_at', 'DESC');
            $this->db->limit($limit, $offset);
            
            return $this->db->get()->result_array();
            
        } catch (Exception $e) {
            log_message('error', 'Error getting examination reports: ' . $e->getMessage());
            return array();
        }
    }

    public function count_examination_reports($filters = array()) {
        try {
            $this->db->from('pemeriksaan_lab pl');
            $this->db->join('pasien p', 'pl.pasien_id = p.pasien_id', 'left');
            $this->db->join('petugas_lab pt', 'pl.petugas_id = pt.petugas_id', 'left');
            
            $where_conditions = $this->_build_examination_where_conditions($filters);
            foreach ($where_conditions as $condition) {
                $this->db->where($condition['field'], $condition['value'], $condition['escape']);
            }
            
            if (!empty($filters['search'])) {
                $search_term = $filters['search'];
                $this->db->group_start();
                $this->db->like('pl.nomor_pemeriksaan', $search_term);
                $this->db->or_like('p.nama', $search_term);
                $this->db->or_like('p.nik', $search_term);
                $this->db->or_like('pl.jenis_pemeriksaan', $search_term);
                $this->db->or_like('pt.nama_petugas', $search_term);
                $this->db->group_end();
            }
            
            return $this->db->count_all_results();
            
        } catch (Exception $e) {
            log_message('error', 'Error counting examination reports: ' . $e->getMessage());
            return 0;
        }
    }

    public function get_examination_detail($examination_id) {
        try {
            $this->db->select('
                pl.*,
                p.nama as nama_pasien,
                p.nik,
                p.jenis_kelamin,
                p.tempat_lahir,
                p.tanggal_lahir,
                p.umur,
                p.alamat_domisili,
                p.telepon,
                p.riwayat_pasien,
                p.dokter_perujuk,
                p.asal_rujukan,
                p.nomor_rujukan,
                p.tanggal_rujukan,
                p.diagnosis_awal,
                p.rekomendasi_pemeriksaan,
                pt.nama_petugas,
                pt.jenis_keahlian,
                l.nama as nama_lab
            ');
            $this->db->from('pemeriksaan_lab pl');
            $this->db->join('pasien p', 'pl.pasien_id = p.pasien_id', 'left');
            $this->db->join('petugas_lab pt', 'pl.petugas_id = pt.petugas_id', 'left');
            $this->db->join('lab l', 'pl.lab_id = l.lab_id', 'left');
            $this->db->where('pl.pemeriksaan_id', $examination_id);
            
            return $this->db->get()->row_array();
            
        } catch (Exception $e) {
            log_message('error', 'Error getting examination detail: ' . $e->getMessage());
            return null;
        }
    }

    public function get_examination_results($examination_id, $examination_type) {
        try {
            $results = null;
            
            switch(strtolower($examination_type)) {
                case 'kimia darah':
                    $this->db->where('pemeriksaan_id', $examination_id);
                    $query = $this->db->get('kimia_darah');
                    if ($query->num_rows() > 0) {
                        $results = $query->row_array();
                    }
                    break;
                    
                case 'hematologi':
                    $this->db->where('pemeriksaan_id', $examination_id);
                    $query = $this->db->get('hematologi');
                    if ($query->num_rows() > 0) {
                        $results = $query->row_array();
                    }
                    break;
                    
                case 'urinologi':
                    $this->db->where('pemeriksaan_id', $examination_id);
                    $query = $this->db->get('urinologi');
                    if ($query->num_rows() > 0) {
                        $results = $query->row_array();
                    }
                    break;
                    
                case 'serologi':
                case 'serologi imunologi':
                    $this->db->where('pemeriksaan_id', $examination_id);
                    $query = $this->db->get('serologi_imunologi');
                    if ($query->num_rows() > 0) {
                        $results = $query->row_array();
                    }
                    break;
                    
                case 'tbc':
                    $this->db->where('pemeriksaan_id', $examination_id);
                    $query = $this->db->get('tbc');
                    if ($query->num_rows() > 0) {
                        $results = $query->row_array();
                    }
                    break;
                    
                case 'ims':
                    $this->db->where('pemeriksaan_id', $examination_id);
                    $query = $this->db->get('ims');
                    if ($query->num_rows() > 0) {
                        $results = $query->row_array();
                    }
                    break;
                    
                case 'mls':
                    $this->db->where('pemeriksaan_id', $examination_id);
                    $query = $this->db->get('mls');
                    if ($query->num_rows() > 0) {
                        $results = $query->row_array();
                    }
                    break;
            }
            
            return $results;
            
        } catch (Exception $e) {
            log_message('error', 'Error getting examination results: ' . $e->getMessage());
            return null;
        }
    }

    public function get_examination_timeline($examination_id) {
        try {
            $this->db->select('
                tp.*,
                pt.nama_petugas
            ');
            $this->db->from('timeline_progres tp');
            $this->db->join('petugas_lab pt', 'tp.petugas_id = pt.petugas_id', 'left');
            $this->db->where('tp.pemeriksaan_id', $examination_id);
            $this->db->order_by('tp.tanggal_update', 'DESC');
            
            return $this->db->get()->result_array();
            
        } catch (Exception $e) {
            log_message('error', 'Error getting examination timeline: ' . $e->getMessage());
            return array();
        }
    }

    // ==========================================
    // FINANCIAL REPORTS FUNCTIONS
    // ==========================================

    public function get_financial_statistics($filters = array()) {
        try {
            $stats = array();
            
            $where_conditions = $this->_build_financial_where_conditions($filters);
            
            // Total revenue
            $this->db->select('SUM(total_biaya) as total_revenue');
            $this->db->from('invoice i');
            $this->db->join('pemeriksaan_lab pl', 'i.pemeriksaan_id = pl.pemeriksaan_id', 'left');
            $this->db->join('pasien p', 'pl.pasien_id = p.pasien_id', 'left');
            
            foreach ($where_conditions as $condition) {
                $this->db->where($condition['field'], $condition['value'], $condition['escape']);
            }
            
            $result = $this->db->get()->row_array();
            $stats['total_revenue'] = (float)($result['total_revenue'] ?? 0);
            
            // Revenue by payment status
            $payment_statuses = ['lunas', 'belum_bayar', 'cicilan'];
            foreach ($payment_statuses as $status) {
                $this->db->select('SUM(total_biaya) as revenue');
                $this->db->from('invoice i');
                $this->db->join('pemeriksaan_lab pl', 'i.pemeriksaan_id = pl.pemeriksaan_id', 'left');
                $this->db->join('pasien p', 'pl.pasien_id = p.pasien_id', 'left');
                $this->db->where('i.status_pembayaran', $status);
                
                foreach ($where_conditions as $condition) {
                    $this->db->where($condition['field'], $condition['value'], $condition['escape']);
                }
                
                $result = $this->db->get()->row_array();
                $stats[$status . '_revenue'] = (float)($result['revenue'] ?? 0);
            }
            
            // Total invoices count
            $this->db->from('invoice i');
            $this->db->join('pemeriksaan_lab pl', 'i.pemeriksaan_id = pl.pemeriksaan_id', 'left');
            $this->db->join('pasien p', 'pl.pasien_id = p.pasien_id', 'left');
            
            foreach ($where_conditions as $condition) {
                $this->db->where($condition['field'], $condition['value'], $condition['escape']);
            }
            
            $stats['total_invoices'] = $this->db->count_all_results();
            
            // Payment rate calculation
            if ($stats['total_revenue'] > 0) {
                $stats['payment_rate'] = round(($stats['lunas_revenue'] / $stats['total_revenue']) * 100, 2);
            } else {
                $stats['payment_rate'] = 0;
            }
            
            // Additional stats
            $stats['paid_revenue'] = $stats['lunas_revenue'];
            $stats['unpaid_revenue'] = $stats['belum_bayar_revenue'];
            $stats['installment_revenue'] = $stats['cicilan_revenue'];
            
            return $stats;
            
        } catch (Exception $e) {
            log_message('error', 'Error getting financial statistics: ' . $e->getMessage());
            return array(
                'total_revenue' => 0,
                'paid_revenue' => 0,
                'unpaid_revenue' => 0,
                'installment_revenue' => 0,
                'total_invoices' => 0,
                'payment_rate' => 0
            );
        }
    }

    public function get_financial_chart_data($filters = array()) {
        try {
            $chart_data = array();
            
            $where_conditions = $this->_build_financial_where_conditions($filters);
            
            // Revenue trend data (daily)
            $this->db->select('DATE(i.tanggal_invoice) as date, SUM(i.total_biaya) as total');
            $this->db->from('invoice i');
            $this->db->join('pemeriksaan_lab pl', 'i.pemeriksaan_id = pl.pemeriksaan_id', 'left');
            $this->db->join('pasien p', 'pl.pasien_id = p.pasien_id', 'left');
            
            // If no date filter specified, default to last 7 days
            if (empty($filters['start_date']) && empty($filters['end_date'])) {
                $this->db->where('i.tanggal_invoice >=', date('Y-m-d', strtotime('-7 days')));
            }
            
            foreach ($where_conditions as $condition) {
                $this->db->where($condition['field'], $condition['value'], $condition['escape']);
            }
            
            $this->db->group_by('DATE(i.tanggal_invoice)');
            $this->db->order_by('DATE(i.tanggal_invoice)', 'ASC');
            
            $revenue_trend = $this->db->get()->result_array();
            $chart_data['revenue'] = $revenue_trend;
            
            // Payment status distribution
            $this->db->select('i.status_pembayaran, COUNT(*) as count');
            $this->db->from('invoice i');
            $this->db->join('pemeriksaan_lab pl', 'i.pemeriksaan_id = pl.pemeriksaan_id', 'left');
            $this->db->join('pasien p', 'pl.pasien_id = p.pasien_id', 'left');
            
            foreach ($where_conditions as $condition) {
                $this->db->where($condition['field'], $condition['value'], $condition['escape']);
            }
            
            $this->db->group_by('i.status_pembayaran');
            $payment_status_result = $this->db->get()->result_array();
            
            $chart_data['payment_status'] = array();
            foreach ($payment_status_result as $row) {
                $chart_data['payment_status'][$row['status_pembayaran']] = $row['count'];
            }
            
            return $chart_data;
            
        } catch (Exception $e) {
            log_message('error', 'Error getting financial chart data: ' . $e->getMessage());
            return array();
        }
    }

    public function get_financial_reports($limit = 20, $offset = 0, $filters = array()) {
        try {
            $this->db->select('
                i.*,
                p.nama as nama_pasien,
                p.nik,
                pl.nomor_pemeriksaan,
                pl.jenis_pemeriksaan
            ');
            $this->db->from('invoice i');
            $this->db->join('pemeriksaan_lab pl', 'i.pemeriksaan_id = pl.pemeriksaan_id', 'left');
            $this->db->join('pasien p', 'pl.pasien_id = p.pasien_id', 'left');
            
            $where_conditions = $this->_build_financial_where_conditions($filters);
            foreach ($where_conditions as $condition) {
                $this->db->where($condition['field'], $condition['value'], $condition['escape']);
            }
            
            if (!empty($filters['search'])) {
                $search_term = $filters['search'];
                $this->db->group_start();
                $this->db->like('i.nomor_invoice', $search_term);
                $this->db->or_like('p.nama', $search_term);
                $this->db->or_like('p.nik', $search_term);
                $this->db->or_like('pl.nomor_pemeriksaan', $search_term);
                $this->db->or_like('pl.jenis_pemeriksaan', $search_term);
                $this->db->group_end();
            }
            
            $this->db->order_by('i.tanggal_invoice', 'DESC');
            $this->db->order_by('i.created_at', 'DESC');
            $this->db->limit($limit, $offset);
            
            return $this->db->get()->result_array();
            
        } catch (Exception $e) {
            log_message('error', 'Error getting financial reports: ' . $e->getMessage());
            return array();
        }
    }

    public function count_financial_reports($filters = array()) {
        try {
            $this->db->from('invoice i');
            $this->db->join('pemeriksaan_lab pl', 'i.pemeriksaan_id = pl.pemeriksaan_id', 'left');
            $this->db->join('pasien p', 'pl.pasien_id = p.pasien_id', 'left');
            
            $where_conditions = $this->_build_financial_where_conditions($filters);
            foreach ($where_conditions as $condition) {
                $this->db->where($condition['field'], $condition['value'], $condition['escape']);
            }
            
            if (!empty($filters['search'])) {
                $search_term = $filters['search'];
                $this->db->group_start();
                $this->db->like('i.nomor_invoice', $search_term);
                $this->db->or_like('p.nama', $search_term);
                $this->db->or_like('p.nik', $search_term);
                $this->db->or_like('pl.nomor_pemeriksaan', $search_term);
                $this->db->or_like('pl.jenis_pemeriksaan', $search_term);
                $this->db->group_end();
            }
            
            return $this->db->count_all_results();
            
        } catch (Exception $e) {
            log_message('error', 'Error counting financial reports: ' . $e->getMessage());
            return 0;
        }
    }

    public function get_invoice_detail($invoice_id) {
        try {
            $this->db->select('
                i.*,
                p.nama as nama_pasien,
                p.nik,
                p.jenis_kelamin,
                p.umur,
                p.alamat_domisili,
                p.telepon,
                pl.nomor_pemeriksaan,
                pl.jenis_pemeriksaan,
                pl.tanggal_pemeriksaan,
                pl.status_pemeriksaan
            ');
            $this->db->from('invoice i');
            $this->db->join('pemeriksaan_lab pl', 'i.pemeriksaan_id = pl.pemeriksaan_id', 'left');
            $this->db->join('pasien p', 'pl.pasien_id = p.pasien_id', 'left');
            $this->db->where('i.invoice_id', $invoice_id);
            
            return $this->db->get()->row_array();
            
        } catch (Exception $e) {
            log_message('error', 'Error getting invoice detail: ' . $e->getMessage());
            return null;
        }
    }

    public function get_examination_by_invoice($invoice_id) {
        try {
            $this->db->select('pl.*');
            $this->db->from('invoice i');
            $this->db->join('pemeriksaan_lab pl', 'i.pemeriksaan_id = pl.pemeriksaan_id', 'left');
            $this->db->where('i.invoice_id', $invoice_id);
            
            return $this->db->get()->row_array();
            
        } catch (Exception $e) {
            log_message('error', 'Error getting examination by invoice: ' . $e->getMessage());
            return null;
        }
    }

    public function update_invoice_payment($invoice_id, $update_data) {
        try {
            $this->db->where('invoice_id', $invoice_id);
            return $this->db->update('invoice', $update_data);
            
        } catch (Exception $e) {
            log_message('error', 'Error updating invoice payment: ' . $e->getMessage());
            return false;
        }
    }

    // Get monthly revenue summary
    public function get_monthly_revenue_summary($year = null) {
        try {
            if (!$year) {
                $year = date('Y');
            }
            
            $this->db->select('
                MONTH(tanggal_invoice) as month,
                SUM(CASE WHEN status_pembayaran = "lunas" THEN total_biaya ELSE 0 END) as paid_revenue,
                SUM(CASE WHEN status_pembayaran = "belum_bayar" THEN total_biaya ELSE 0 END) as unpaid_revenue,
                SUM(CASE WHEN status_pembayaran = "cicilan" THEN total_biaya ELSE 0 END) as installment_revenue,
                SUM(total_biaya) as total_revenue,
                COUNT(*) as total_invoices
            ');
            $this->db->from('invoice');
            $this->db->where('YEAR(tanggal_invoice)', $year);
            $this->db->group_by('MONTH(tanggal_invoice)');
            $this->db->order_by('MONTH(tanggal_invoice)', 'ASC');
            
            return $this->db->get()->result_array();
            
        } catch (Exception $e) {
            log_message('error', 'Error getting monthly revenue summary: ' . $e->getMessage());
            return array();
        }
    }

    // Get top paying patients
    public function get_top_paying_patients($limit = 10, $filters = array()) {
        try {
            $this->db->select('
                p.nama as nama_pasien,
                p.nik,
                SUM(i.total_biaya) as total_spent,
                COUNT(i.invoice_id) as total_invoices,
                AVG(i.total_biaya) as avg_invoice_amount
            ');
            $this->db->from('invoice i');
            $this->db->join('pemeriksaan_lab pl', 'i.pemeriksaan_id = pl.pemeriksaan_id');
            $this->db->join('pasien p', 'pl.pasien_id = p.pasien_id');
            
            $where_conditions = $this->_build_financial_where_conditions($filters);
            foreach ($where_conditions as $condition) {
                $this->db->where($condition['field'], $condition['value'], $condition['escape']);
            }
            
            $this->db->group_by('p.pasien_id');
            $this->db->order_by('total_spent', 'DESC');
            $this->db->limit($limit);
            
            return $this->db->get()->result_array();
            
        } catch (Exception $e) {
            log_message('error', 'Error getting top paying patients: ' . $e->getMessage());
            return array();
        }
    }

    // Get overdue payments
    public function get_overdue_payments($days_overdue = 30) {
        try {
            $overdue_date = date('Y-m-d', strtotime("-{$days_overdue} days"));
            
            $this->db->select('
                i.*,
                p.nama as nama_pasien,
                p.nik,
                p.telepon,
                pl.nomor_pemeriksaan,
                pl.jenis_pemeriksaan,
                DATEDIFF(CURDATE(), i.tanggal_invoice) as days_overdue
            ');
            $this->db->from('invoice i');
            $this->db->join('pemeriksaan_lab pl', 'i.pemeriksaan_id = pl.pemeriksaan_id');
            $this->db->join('pasien p', 'pl.pasien_id = p.pasien_id');
            $this->db->where('i.status_pembayaran', 'belum_bayar');
            $this->db->where('i.tanggal_invoice <=', $overdue_date);
            $this->db->order_by('i.tanggal_invoice', 'ASC');
            
            return $this->db->get()->result_array();
            
        } catch (Exception $e) {
            log_message('error', 'Error getting overdue payments: ' . $e->getMessage());
            return array();
        }
    }

    // Get payment method statistics
    public function get_payment_method_statistics($filters = array()) {
        try {
            $this->db->select('
                metode_pembayaran,
                COUNT(*) as count,
                SUM(total_biaya) as total_amount
            ');
            $this->db->from('invoice i');
            $this->db->join('pemeriksaan_lab pl', 'i.pemeriksaan_id = pl.pemeriksaan_id', 'left');
            $this->db->join('pasien p', 'pl.pasien_id = p.pasien_id', 'left');
            $this->db->where('i.status_pembayaran', 'lunas');
            $this->db->where('i.metode_pembayaran IS NOT NULL');
            
            $where_conditions = $this->_build_financial_where_conditions($filters);
            foreach ($where_conditions as $condition) {
                $this->db->where($condition['field'], $condition['value'], $condition['escape']);
            }
            
            $this->db->group_by('metode_pembayaran');
            $this->db->order_by('total_amount', 'DESC');
            
            return $this->db->get()->result_array();
            
        } catch (Exception $e) {
            log_message('error', 'Error getting payment method statistics: ' . $e->getMessage());
            return array();
        }
    }

    // ==========================================
    // PRINT & INVOICE FUNCTIONS
    // ==========================================

    public function get_invoice_data_safe($examination_id) {
        try {
            return $this->get_invoice_data_by_examination($examination_id);
        } catch (Exception $e) {
            log_message('error', 'Error in get_invoice_data_safe: ' . $e->getMessage());
            return null;
        }
    }

    public function create_invoice_for_examination_safe($examination_id) {
        try {
            return $this->create_invoice_for_examination($examination_id);
        } catch (Exception $e) {
            log_message('error', 'Error in create_invoice_for_examination_safe: ' . $e->getMessage());
            return null;
        }
    }

    // ==========================================
    // DATABASE BACKUP FUNCTIONS
    // ==========================================

    public function get_database_info() {
        try {
            $info = array(
                'size' => 'Unknown',
                'table_count' => 0,
                'record_count' => 0
            );
            
            // Check if database connection is working
            if (!$this->db->conn_id) {
                log_message('error', 'Database connection not available');
                return $info;
            }
            
            $db_name = $this->db->database;
            
            // Validate database name
            if (empty($db_name)) {
                log_message('error', 'Database name is empty');
                return $info;
            }
            
            // Get database size with error handling
            try {
                $size_query = $this->db->query("
                    SELECT 
                        ROUND(SUM(data_length + index_length) / 1024 / 1024, 2) AS size_mb
                    FROM information_schema.tables 
                    WHERE table_schema = ? AND table_type = 'BASE TABLE'
                ", array($db_name));
                
                if ($size_query && $size_query->num_rows() > 0) {
                    $size_row = $size_query->row();
                    $size_mb = $size_row->size_mb;
                    
                    if ($size_mb !== null && is_numeric($size_mb)) {
                        if ($size_mb > 1024) {
                            $info['size'] = round($size_mb / 1024, 2) . ' GB';
                        } else {
                            $info['size'] = $size_mb . ' MB';
                        }
                    }
                }
            } catch (Exception $e) {
                log_message('error', 'Error getting database size: ' . $e->getMessage());
            }
            
            // Get table count with error handling
            try {
                $table_query = $this->db->query("
                    SELECT COUNT(*) as table_count 
                    FROM information_schema.tables 
                    WHERE table_schema = ? AND table_type = 'BASE TABLE'
                ", array($db_name));
                
                if ($table_query && $table_query->num_rows() > 0) {
                    $table_row = $table_query->row();
                    $info['table_count'] = (int)$table_row->table_count;
                }
            } catch (Exception $e) {
                log_message('error', 'Error getting table count: ' . $e->getMessage());
            }
            
            // Get approximate record count with error handling
            try {
                $record_query = $this->db->query("
                    SELECT SUM(table_rows) as total_records 
                    FROM information_schema.tables 
                    WHERE table_schema = ? AND table_type = 'BASE TABLE'
                ", array($db_name));
                
                if ($record_query && $record_query->num_rows() > 0) {
                    $record_row = $record_query->row();
                    $info['record_count'] = (int)($record_row->total_records ?: 0);
                }
            } catch (Exception $e) {
                log_message('error', 'Error getting record count: ' . $e->getMessage());
            }
            
            return $info;
            
        } catch (Exception $e) {
            log_message('error', 'Error in get_database_info: ' . $e->getMessage());
            return array(
                'size' => 'Error',
                'table_count' => 0,
                'record_count' => 0
            );
        }
    }

    public function get_backup_list() {
        try {
            $backup_dir = $this->_get_backup_directory();
            
            if (!$backup_dir || !is_dir($backup_dir)) {
                log_message('error', 'Backup directory not accessible: ' . $backup_dir);
                return array();
            }
            
            $backups = array();
            $files = @scandir($backup_dir);
            
            if ($files === false) {
                log_message('error', 'Cannot read backup directory: ' . $backup_dir);
                return array();
            }
            
            foreach ($files as $file) {
                if ($file === '.' || $file === '..') continue;
                
                $file_path = $backup_dir . $file;
                
                // Skip if not a file
                if (!is_file($file_path)) continue;
                
                try {
                    $file_info = pathinfo($file);
                    $file_size = filesize($file_path);
                    $file_time = filemtime($file_path);
                    
                    // Only include valid backup files
                    $allowed_extensions = array('sql', 'zip');
                    if (!in_array(strtolower($file_info['extension'] ?? ''), $allowed_extensions)) {
                        continue;
                    }
                    
                    $backups[] = array(
                        'filename' => $file,
                        'size' => $this->_format_file_size($file_size),
                        'created_at' => date('Y-m-d H:i:s', $file_time),
                        'type' => strtoupper($file_info['extension'] ?? ''),
                        'is_valid' => $this->_validate_backup_file($file_path)
                    );
                    
                } catch (Exception $e) {
                    log_message('error', 'Error processing backup file ' . $file . ': ' . $e->getMessage());
                    continue;
                }
            }
            
            // Sort by creation time (newest first)
            usort($backups, function($a, $b) {
                return strtotime($b['created_at']) - strtotime($a['created_at']);
            });
            
            return $backups;
            
        } catch (Exception $e) {
            log_message('error', 'Error getting backup list: ' . $e->getMessage());
            return array();
        }
    }

    public function create_database_backup($options) {
        try {
            // Validate options
            if (empty($options['name'])) {
                throw new Exception('Backup name is required');
            }
            
            $backup_dir = $this->_get_backup_directory();
            
            // Create directory if it doesn't exist
            if (!is_dir($backup_dir)) {
                if (!@mkdir($backup_dir, 0755, true)) {
                    throw new Exception('Cannot create backup directory: ' . $backup_dir);
                }
            }
            
            // Check if directory is writable
            if (!is_writable($backup_dir)) {
                throw new Exception('Backup directory is not writable: ' . $backup_dir);
            }
            
            // Sanitize filename
            $safe_name = preg_replace('/[^a-zA-Z0-9_-]/', '', $options['name']);
            if (empty($safe_name)) {
                $safe_name = 'backup_' . date('Y-m-d_H-i-s');
            }
            
            $filename = $safe_name . '.sql';
            $file_path = $backup_dir . $filename;
            
            // Check available disk space (require at least 100MB free)
            $free_space = disk_free_space($backup_dir);
            if ($free_space !== false && $free_space < (100 * 1024 * 1024)) {
                throw new Exception('Insufficient disk space for backup');
            }
            
            // Generate SQL backup
            $sql_content = $this->_generate_sql_backup($options);
            
            if (empty($sql_content)) {
                throw new Exception('Generated backup content is empty');
            }
            
            // Write backup file
            $bytes_written = @file_put_contents($file_path, $sql_content);
            if ($bytes_written === false) {
                throw new Exception('Failed to write backup file: ' . $file_path);
            }
            
            // Verify file was created successfully
            if (!file_exists($file_path) || filesize($file_path) === 0) {
                throw new Exception('Backup file was not created properly');
            }
            
            $final_filename = $filename;
            $file_size = filesize($file_path);
            
            // Compress if requested
            if (!empty($options['compress'])) {
                try {
                    $zip_filename = $safe_name . '.zip';
                    $zip_path = $backup_dir . $zip_filename;
                    
                    if (class_exists('ZipArchive')) {
                        $zip = new ZipArchive();
                        $zip_result = $zip->open($zip_path, ZipArchive::CREATE);
                        
                        if ($zip_result === TRUE) {
                            $zip->addFile($file_path, $filename);
                            $zip->close();
                            
                            // Verify ZIP was created
                            if (file_exists($zip_path) && filesize($zip_path) > 0) {
                                @unlink($file_path); // Remove original SQL file
                                $final_filename = $zip_filename;
                                $file_size = filesize($zip_path);
                            }
                        } else {
                            log_message('warning', 'ZIP creation failed with code: ' . $zip_result);
                        }
                    } else {
                        log_message('warning', 'ZipArchive class not available for compression');
                    }
                } catch (Exception $e) {
                    log_message('warning', 'Compression failed: ' . $e->getMessage());
                    // Continue without compression
                }
            }
            
            return array(
                'success' => true,
                'filename' => $final_filename,
                'file_size' => $this->_format_file_size($file_size),
                'message' => 'Backup berhasil dibuat'
            );
            
        } catch (Exception $e) {
            log_message('error', 'Error creating database backup: ' . $e->getMessage());
            return array(
                'success' => false,
                'message' => 'Gagal membuat backup: ' . $e->getMessage()
            );
        }
    }

    public function restore_database_from_file($file) {
        try {
            // Validate file upload
            if (empty($file) || !is_array($file)) {
                throw new Exception('Invalid file data');
            }
            
            if (!isset($file['tmp_name']) || !file_exists($file['tmp_name'])) {
                throw new Exception('Uploaded file not found');
            }
            
            $file_ext = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
            $temp_path = $file['tmp_name'];
            
            // Validate file extension
            $allowed_extensions = array('sql', 'zip');
            if (!in_array($file_ext, $allowed_extensions)) {
                throw new Exception('File format tidak didukung. Gunakan .sql atau .zip');
            }
            
            // Check file size (max 500MB)
            $max_size = 500 * 1024 * 1024;
            if (filesize($temp_path) > $max_size) {
                throw new Exception('File terlalu besar. Maksimal 500MB');
            }
            
            $sql_content = '';
            
            if ($file_ext === 'zip') {
                $sql_content = $this->_extract_sql_from_zip($temp_path);
            } else {
                $sql_content = @file_get_contents($temp_path);
                if ($sql_content === false) {
                    throw new Exception('Cannot read SQL file');
                }
            }
            
            if (empty($sql_content)) {
                throw new Exception('File backup kosong atau tidak valid');
            }
            
            // Basic validation of SQL content
            if (!$this->_validate_sql_content($sql_content)) {
                throw new Exception('File backup tidak mengandung SQL yang valid');
            }
            
            return $this->_execute_sql_restore($sql_content);
            
        } catch (Exception $e) {
            log_message('error', 'Error restoring database from file: ' . $e->getMessage());
            return array(
                'success' => false,
                'message' => 'Gagal memulihkan database: ' . $e->getMessage()
            );
        }
    }

    public function restore_database_from_backup($filename) {
        try {
            // Validate filename
            if (empty($filename)) {
                throw new Exception('Nama file backup tidak valid');
            }
            
            // Sanitize filename
            $safe_filename = basename($filename);
            
            $backup_dir = $this->_get_backup_directory();
            $file_path = $backup_dir . $safe_filename;
            
            if (!file_exists($file_path)) {
                throw new Exception('File backup tidak ditemukan: ' . $safe_filename);
            }
            
            if (!is_readable($file_path)) {
                throw new Exception('File backup tidak dapat dibaca');
            }
            
            $file_ext = strtolower(pathinfo($safe_filename, PATHINFO_EXTENSION));
            $sql_content = '';
            
            if ($file_ext === 'zip') {
                $sql_content = $this->_extract_sql_from_zip($file_path);
            } else {
                $sql_content = @file_get_contents($file_path);
                if ($sql_content === false) {
                    throw new Exception('Cannot read backup file');
                }
            }
            
            if (empty($sql_content)) {
                throw new Exception('File backup kosong atau tidak valid');
            }
            
            // Basic validation
            if (!$this->_validate_sql_content($sql_content)) {
                throw new Exception('File backup tidak mengandung SQL yang valid');
            }
            
            return $this->_execute_sql_restore($sql_content);
            
        } catch (Exception $e) {
            log_message('error', 'Error restoring database from backup: ' . $e->getMessage());
            return array(
                'success' => false,
                'message' => 'Gagal memulihkan database: ' . $e->getMessage()
            );
        }
    }

    public function delete_backup_file($filename) {
        try {
            if (empty($filename)) {
                return false;
            }
            
            // Sanitize filename and prevent directory traversal
            $safe_filename = basename($filename);
            
            $backup_dir = $this->_get_backup_directory();
            $file_path = $backup_dir . $safe_filename;
            
            // Additional security check
            if (!file_exists($file_path) || !is_file($file_path)) {
                return false;
            }
            
            // Check if file is in the correct directory
            if (dirname(realpath($file_path)) !== realpath($backup_dir)) {
                log_message('error', 'Attempted to delete file outside backup directory: ' . $filename);
                return false;
            }
            
            return @unlink($file_path);
            
        } catch (Exception $e) {
            log_message('error', 'Error deleting backup file: ' . $e->getMessage());
            return false;
        }
    }

    public function clean_old_backups($days = 30) {
        try {
            $backup_dir = $this->_get_backup_directory();
            
            if (!is_dir($backup_dir)) {
                return array(
                    'success' => false,
                    'message' => 'Direktori backup tidak ditemukan'
                );
            }
            
            $cutoff_time = time() - ($days * 24 * 60 * 60);
            $deleted_count = 0;
            
            $files = @scandir($backup_dir);
            if ($files === false) {
                throw new Exception('Cannot read backup directory');
            }
            
            foreach ($files as $file) {
                if ($file === '.' || $file === '..') continue;
                
                $file_path = $backup_dir . $file;
                
                try {
                    if (is_file($file_path)) {
                        $file_time = filemtime($file_path);
                        if ($file_time !== false && $file_time < $cutoff_time) {
                            // Additional check for backup file extensions
                            $file_ext = strtolower(pathinfo($file, PATHINFO_EXTENSION));
                            if (in_array($file_ext, array('sql', 'zip'))) {
                                if (@unlink($file_path)) {
                                    $deleted_count++;
                                }
                            }
                        }
                    }
                } catch (Exception $e) {
                    log_message('error', 'Error processing file during cleanup: ' . $file . ' - ' . $e->getMessage());
                    continue;
                }
            }
            
            return array(
                'success' => true,
                'deleted_count' => $deleted_count,
                'message' => "Berhasil menghapus {$deleted_count} file backup lama"
            );
            
        } catch (Exception $e) {
            log_message('error', 'Error cleaning old backups: ' . $e->getMessage());
            return array(
                'success' => false,
                'message' => 'Gagal membersihkan backup lama: ' . $e->getMessage()
            );
        }
    }

    public function get_backup_file_path($filename) {
        try {
            if (empty($filename)) {
                return false;
            }
            
            $safe_filename = basename($filename);
            $backup_dir = $this->_get_backup_directory();
            $file_path = $backup_dir . $safe_filename;
            
            if (file_exists($file_path) && is_file($file_path)) {
                // Security check
                if (dirname(realpath($file_path)) === realpath($backup_dir)) {
                    return $file_path;
                }
            }
            
            return false;
            
        } catch (Exception $e) {
            log_message('error', 'Error getting backup file path: ' . $e->getMessage());
            return false;
        }
    }

    // ==========================================
    // ACTIVITY LOGGING
    // ==========================================

    public function log_activity($user_id, $activity, $table_affected = null, $record_id = null) {
        $activity_data = array(
            'user_id' => $user_id,
            'activity' => $activity,
            'table_affected' => $table_affected,
            'record_id' => $record_id,
            'ip_address' => $this->input->ip_address(),
            'created_at' => date('Y-m-d H:i:s')
        );
        
        return $this->db->insert('activity_log', $activity_data);
    }

    // ==========================================
    // HELPER METHODS
    // ==========================================

    private function _build_examination_where_conditions($filters = array()) {
        $conditions = array();
        
        if (!empty($filters['start_date'])) {
            $conditions[] = array(
                'field' => 'DATE(pl.tanggal_pemeriksaan) >=',
                'value' => $filters['start_date'],
                'escape' => TRUE
            );
        }
        
        if (!empty($filters['end_date'])) {
            $conditions[] = array(
                'field' => 'DATE(pl.tanggal_pemeriksaan) <=',
                'value' => $filters['end_date'],
                'escape' => TRUE
            );
        }
        
        if (!empty($filters['status'])) {
            $conditions[] = array(
                'field' => 'pl.status_pemeriksaan',
                'value' => $filters['status'],
                'escape' => TRUE
            );
        }
        
        if (!empty($filters['jenis_pemeriksaan'])) {
            $conditions[] = array(
                'field' => 'pl.jenis_pemeriksaan',
                'value' => $filters['jenis_pemeriksaan'],
                'escape' => TRUE
            );
        }
        
        if (!empty($filters['petugas_id'])) {
            $conditions[] = array(
                'field' => 'pl.petugas_id',
                'value' => $filters['petugas_id'],
                'escape' => TRUE
            );
        }
        
        if (!empty($filters['lab_id'])) {
            $conditions[] = array(
                'field' => 'pl.lab_id',
                'value' => $filters['lab_id'],
                'escape' => TRUE
            );
        }
        
        return $conditions;
    }

    private function _build_financial_where_conditions($filters = array()) {
        $conditions = array();
        
        if (!empty($filters['start_date'])) {
            $conditions[] = array(
                'field' => 'DATE(i.tanggal_invoice) >=',
                'value' => $filters['start_date'],
                'escape' => TRUE
            );
        }
        
        if (!empty($filters['end_date'])) {
            $conditions[] = array(
                'field' => 'DATE(i.tanggal_invoice) <=',
                'value' => $filters['end_date'],
                'escape' => TRUE
            );
        }
        
        if (!empty($filters['status'])) {
            $conditions[] = array(
                'field' => 'i.status_pembayaran',
                'value' => $filters['status'],
                'escape' => TRUE
            );
        }
        
        if (!empty($filters['jenis_pembayaran'])) {
            $conditions[] = array(
                'field' => 'i.jenis_pembayaran',
                'value' => $filters['jenis_pembayaran'],
                'escape' => TRUE
            );
        }
        
        if (!empty($filters['metode_pembayaran'])) {
            $conditions[] = array(
                'field' => 'i.metode_pembayaran',
                'value' => $filters['metode_pembayaran'],
                'escape' => TRUE
            );
        }
        
        return $conditions;
    }

    private function _get_today_revenue() {
        $this->db->select('SUM(total_biaya) as total');
        $this->db->where('DATE(tanggal_pembayaran)', date('Y-m-d'));
        $this->db->where('status_pembayaran', 'lunas');
        $query = $this->db->get('invoice');
        
        $result = $query->row_array();
        return $result['total'] ? (float)$result['total'] : 0;
    }

    private function _get_month_revenue($month) {
        $this->db->select('SUM(total_biaya) as total');
        $this->db->where("DATE_FORMAT(tanggal_pembayaran, '%Y-%m') = ", $month, FALSE);
        $this->db->where('status_pembayaran', 'lunas');
        $query = $this->db->get('invoice');
        
        $result = $query->row_array();
        return $result['total'] ? (float)$result['total'] : 0;
    }

    private function get_invoice_data_by_examination($examination_id) {
        try {
            $this->db->select('nomor_pemeriksaan');
            $this->db->where('pemeriksaan_id', $examination_id);
            $query = $this->db->get('pemeriksaan_lab');
            
            if ($query->num_rows() == 0) {
                return null;
            }
            
            $nomor_pemeriksaan = $query->row()->nomor_pemeriksaan;
            
            $this->db->select('*');
            $this->db->where('nomor_pemeriksaan', $nomor_pemeriksaan);
            $query = $this->db->get('v_invoice_cetak');
            
            return $query->num_rows() > 0 ? $query->row_array() : null;
            
        } catch (Exception $e) {
            log_message('error', 'Error getting invoice data: ' . $e->getMessage());
            return null;
        }
    }

    private function create_invoice_for_examination($examination_id) {
        try {
            $this->db->select('*');
            $this->db->where('pemeriksaan_id', $examination_id);
            $examination = $this->db->get('pemeriksaan_lab')->row_array();
            
            if (!$examination) {
                return false;
            }
            
            $existing_invoice = $this->get_invoice_data_by_examination($examination_id);
            if ($existing_invoice) {
                return $existing_invoice;
            }
            
            $this->db->select_max('invoice_id');
            $query = $this->db->get('invoice');
            $max_id = $query->row()->invoice_id ?: 0;
            $invoice_number = 'INV-' . date('Y') . '-' . str_pad($max_id + 1, 4, '0', STR_PAD_LEFT);
            
            $invoice_data = array(
                'pemeriksaan_id' => $examination_id,
                'nomor_invoice' => $invoice_number,
                'tanggal_invoice' => $examination['tanggal_pemeriksaan'],
                'jenis_pembayaran' => 'umum',
                'total_biaya' => $examination['biaya'] ?: 0,
                'status_pembayaran' => 'belum_bayar',
                'created_at' => date('Y-m-d H:i:s')
            );
            
            $this->db->insert('invoice', $invoice_data);
            
            if ($this->db->affected_rows() > 0) {
                return $this->get_invoice_data_by_examination($examination_id);
            }
            
            return false;
            
        } catch (Exception $e) {
            log_message('error', 'Error creating invoice: ' . $e->getMessage());
            return false;
        }
    }

    // ==========================================
    // PRIVATE HELPER METHODS WITH ENHANCED ERROR HANDLING
    // ==========================================

    private function _get_backup_directory() {
        $backup_dir = APPPATH . '../backups/database/';
        
        // Normalize path
        $backup_dir = realpath($backup_dir);
        if ($backup_dir === false) {
            // If realpath fails, use the original path and try to create it
            $backup_dir = APPPATH . '../backups/database/';
        }
        
        // Ensure trailing slash
        return rtrim($backup_dir, '/\\') . DIRECTORY_SEPARATOR;
    }

    private function _format_file_size($size) {
        if (!is_numeric($size) || $size < 0) {
            return 'Unknown';
        }
        
        $units = array('B', 'KB', 'MB', 'GB', 'TB');
        $unit = 0;
        
        while ($size >= 1024 && $unit < count($units) - 1) {
            $size /= 1024;
            $unit++;
        }
        
        return round($size, 2) . ' ' . $units[$unit];
    }

    private function _validate_backup_file($file_path) {
        try {
            if (!file_exists($file_path) || !is_readable($file_path)) {
                return false;
            }
            
            $file_ext = strtolower(pathinfo($file_path, PATHINFO_EXTENSION));
            
            if ($file_ext === 'sql') {
                // Check if file contains SQL keywords
                $content = @file_get_contents($file_path, false, null, 0, 2000);
                return $content !== false && (
                    strpos($content, 'CREATE TABLE') !== false ||
                    strpos($content, 'INSERT INTO') !== false ||
                    strpos($content, 'DROP TABLE') !== false
                );
            }
            
            if ($file_ext === 'zip') {
                return class_exists('ZipArchive') && filesize($file_path) > 0;
            }
            
            return false;
            
        } catch (Exception $e) {
            log_message('error', 'Error validating backup file: ' . $e->getMessage());
            return false;
        }
    }

    private function _generate_sql_backup($options) {
        try {
            $sql = "-- Database backup generated on " . date('Y-m-d H:i:s') . "\n";
            $sql .= "-- Generator: LabSy Database Backup System\n";
            $sql .= "-- Database: " . $this->db->database . "\n\n";
            
            $sql .= "SET SQL_MODE = \"NO_AUTO_VALUE_ON_ZERO\";\n";
            $sql .= "START TRANSACTION;\n";
            $sql .= "SET time_zone = \"+00:00\";\n";
            $sql .= "SET FOREIGN_KEY_CHECKS = 0;\n\n";
            
            // Get all tables
            $tables_query = $this->db->query("SHOW TABLES");
            if (!$tables_query) {
                throw new Exception('Cannot retrieve table list');
            }
            
            $tables = $tables_query->result_array();
            
            if (empty($tables)) {
                throw new Exception('No tables found in database');
            }
            
            foreach ($tables as $table) {
                $table_name = reset($table);
                
                try {
                    if ($options['include_structure']) {
                        // Add DROP TABLE statement
                        $sql .= "-- Table structure for table `{$table_name}`\n";
                        $sql .= "DROP TABLE IF EXISTS `{$table_name}`;\n";
                        
                        // Add CREATE TABLE statement
                        $create_query = $this->db->query("SHOW CREATE TABLE `{$table_name}`");
                        if ($create_query && $create_query->num_rows() > 0) {
                            $create_row = $create_query->row_array();
                            $sql .= $create_row['Create Table'] . ";\n\n";
                        }
                    }
                    
                    if ($options['include_data']) {
                        // Add INSERT statements
                        $sql .= "-- Dumping data for table `{$table_name}`\n";
                        
                        $data_query = $this->db->get($table_name);
                        if ($data_query && $data_query->num_rows() > 0) {
                            $rows = $data_query->result_array();
                            
                            $columns = array_keys($rows[0]);
                            $column_list = '`' . implode('`, `', $columns) . '`';
                            
                            $sql .= "LOCK TABLES `{$table_name}` WRITE;\n";
                            $sql .= "INSERT INTO `{$table_name}` ({$column_list}) VALUES\n";
                            
                            $values = array();
                            foreach ($rows as $row) {
                                $escaped_values = array();
                                foreach ($row as $value) {
                                    if ($value === null) {
                                        $escaped_values[] = 'NULL';
                                    } else {
                                        $escaped_values[] = "'" . $this->db->escape_str($value) . "'";
                                    }
                                }
                                $values[] = '(' . implode(', ', $escaped_values) . ')';
                            }
                            
                            $sql .= implode(",\n", $values) . ";\n";
                            $sql .= "UNLOCK TABLES;\n\n";
                        } else {
                            $sql .= "-- No data found for table `{$table_name}`\n\n";
                        }
                    }
                    
                } catch (Exception $e) {
                    log_message('error', 'Error backing up table ' . $table_name . ': ' . $e->getMessage());
                    $sql .= "-- Error backing up table `{$table_name}`: " . $e->getMessage() . "\n\n";
                    continue;
                }
            }
            
            $sql .= "SET FOREIGN_KEY_CHECKS = 1;\n";
            $sql .= "COMMIT;\n";
            
            return $sql;
            
        } catch (Exception $e) {
            log_message('error', 'Error generating SQL backup: ' . $e->getMessage());
            throw new Exception('Failed to generate backup: ' . $e->getMessage());
        }
    }

    private function _extract_sql_from_zip($zip_path) {
        if (!class_exists('ZipArchive')) {
            throw new Exception('ZipArchive class tidak tersedia di server ini');
        }
        
        $zip = new ZipArchive();
        $open_result = $zip->open($zip_path);
        
        if ($open_result !== TRUE) {
            throw new Exception('Cannot open ZIP file. Error code: ' . $open_result);
        }
        
        try {
            for ($i = 0; $i < $zip->numFiles; $i++) {
                $filename = $zip->getNameIndex($i);
                if (strtolower(pathinfo($filename, PATHINFO_EXTENSION)) === 'sql') {
                    $content = $zip->getFromIndex($i);
                    $zip->close();
                    
                    if ($content === false) {
                        throw new Exception('Cannot extract SQL file from ZIP');
                    }
                    
                    return $content;
                }
            }
            
            $zip->close();
            throw new Exception('No SQL file found in ZIP archive');
            
        } catch (Exception $e) {
            $zip->close();
            throw $e;
        }
    }

    private function _validate_sql_content($sql_content) {
        if (empty($sql_content)) {
            return false;
        }
        
        // Basic SQL validation
        $sql_keywords = array('CREATE', 'INSERT', 'UPDATE', 'DELETE', 'SELECT', 'DROP');
        $has_sql = false;
        
        foreach ($sql_keywords as $keyword) {
            if (stripos($sql_content, $keyword) !== false) {
                $has_sql = true;
                break;
            }
        }
        
        return $has_sql;
    }

    private function _execute_sql_restore($sql_content) {
        try {
            // Set longer execution time for restore
            @set_time_limit(300);
            @ini_set('memory_limit', '512M');
            
            $this->db->trans_start();
            
            // Split SQL content into individual queries
            $queries = $this->_split_sql_queries($sql_content);
            
            if (empty($queries)) {
                throw new Exception('No valid SQL queries found in backup');
            }
            
            $executed_count = 0;
            $error_count = 0;
            
            foreach ($queries as $query) {
                $query = trim($query);
                
                // Skip empty queries and comments
                if (empty($query) || 
                    preg_match('/^(\/\*|--|\#)/', $query) ||
                    preg_match('/^(SET|START|COMMIT|LOCK|UNLOCK)/i', $query)) {
                    continue;
                }
                
                try {
                    $this->db->query($query);
                    $executed_count++;
                } catch (Exception $e) {
                    $error_count++;
                    log_message('error', 'SQL restore query failed: ' . $e->getMessage() . ' Query: ' . substr($query, 0, 100));
                    
                    // Don't fail the entire restore for non-critical errors
                    if (stripos($query, 'DROP TABLE') === false && stripos($query, 'CREATE TABLE') === false) {
                        continue;
                    } else {
                        throw $e;
                    }
                }
            }
            
            $this->db->trans_complete();
            
            if ($this->db->trans_status() === FALSE) {
                throw new Exception('Database restore transaction failed');
            }
            
            $message = "Database berhasil dipulihkan. {$executed_count} query dieksekusi";
            if ($error_count > 0) {
                $message .= ", {$error_count} query diabaikan.";
            }
            
            return array(
                'success' => true,
                'message' => $message
            );
            
        } catch (Exception $e) {
            $this->db->trans_rollback();
            log_message('error', 'SQL restore failed: ' . $e->getMessage());
            throw new Exception('Restore gagal: ' . $e->getMessage());
        }
    }

    private function _split_sql_queries($sql_content) {
        // Simple SQL query splitter
        $queries = array();
        $current_query = '';
        $in_string = false;
        $string_char = '';
        
        $lines = explode("\n", $sql_content);
        
        foreach ($lines as $line) {
            $line = rtrim($line);
            
            if (empty($line) || preg_match('/^(--|\#)/', $line)) {
                continue;
            }
            
            $current_query .= $line . "\n";
            
            // Simple check for query end (semicolon not in string)
            if (substr($line, -1) === ';' && !$in_string) {
                $queries[] = trim($current_query);
                $current_query = '';
            }
        }
        
        if (!empty(trim($current_query))) {
            $queries[] = trim($current_query);
        }
        
        return $queries;
    }
    public function get_dashboard_kpi() {
    try {
        $kpi = array();
        
        // Total pemeriksaan 30 hari terakhir
        $this->db->where('tanggal_pemeriksaan >=', date('Y-m-d', strtotime('-30 days')));
        $kpi['total_examinations'] = $this->db->count_all_results('pemeriksaan_lab');
        
        // Pemeriksaan selesai hari ini
        $this->db->where('DATE(completed_at)', date('Y-m-d'));
        $this->db->where('status_pemeriksaan', 'selesai');
        $kpi['completed_today'] = $this->db->count_all_results('pemeriksaan_lab');
        
        // Pemeriksaan pending
        $this->db->where('status_pemeriksaan', 'pending');
        $kpi['pending_today'] = $this->db->count_all_results('pemeriksaan_lab');
        
        // Pendapatan bulan ini
        $this->db->select('SUM(total_biaya) as total');
        $this->db->where('YEAR(tanggal_invoice)', date('Y'));
        $this->db->where('MONTH(tanggal_invoice)', date('m'));
        $this->db->where('status_pembayaran', 'lunas');
        $query = $this->db->get('invoice');
        $result = $query->row_array();
        $kpi['monthly_revenue'] = $result['total'] ? (float)$result['total'] : 0;
        
        // Users aktif
        $this->db->where('is_active', 1);
        $kpi['active_users'] = $this->db->count_all_results('users');
        
        // Alert items dari inventory
        $this->db->where("alert_level IN ('Urgent', 'Warning', 'Low Stock')", NULL, FALSE);
        $kpi['alert_items'] = $this->db->count_all_results('v_inventory_status');
        
        return $kpi;
        
    } catch (Exception $e) {
        log_message('error', 'Error getting dashboard KPI: ' . $e->getMessage());
        return array(
            'total_examinations' => 0,
            'completed_today' => 0,
            'pending_today' => 0,
            'monthly_revenue' => 0,
            'active_users' => 0,
            'alert_items' => 0
        );
    }
}

public function get_examination_trend($days = 30) {
    try {
        $sql = "
            SELECT 
                DATE(tanggal_pemeriksaan) as exam_date,
                COUNT(*) as total,
                SUM(CASE WHEN status_pemeriksaan = 'selesai' THEN 1 ELSE 0 END) as completed,
                SUM(CASE WHEN status_pemeriksaan = 'pending' THEN 1 ELSE 0 END) as pending,
                SUM(CASE WHEN status_pemeriksaan = 'progress' THEN 1 ELSE 0 END) as progress
            FROM pemeriksaan_lab 
            WHERE tanggal_pemeriksaan >= DATE_SUB(CURDATE(), INTERVAL ? DAY)
            GROUP BY DATE(tanggal_pemeriksaan)
            ORDER BY DATE(tanggal_pemeriksaan) ASC
        ";
        
        $query = $this->db->query($sql, array($days));
        return $query->result_array();
        
    } catch (Exception $e) {
        log_message('error', 'Error getting examination trend: ' . $e->getMessage());
        return array();
    }
}

public function get_user_distribution() {
    try {
        $this->db->select('role, COUNT(*) as count');
        $this->db->where('is_active', 1);
        $this->db->where('role !=', 'dokter');
        $this->db->group_by('role');
        $this->db->order_by('count', 'DESC');
        
        $query = $this->db->get('users');
        return $query->result_array();
        
    } catch (Exception $e) {
        log_message('error', 'Error getting user distribution: ' . $e->getMessage());
        return array();
    }
}

public function get_pending_examinations($limit = 10) {
    try {
        $this->db->select('
            pl.nomor_pemeriksaan, 
            pl.jenis_pemeriksaan, 
            pl.tanggal_pemeriksaan,
            p.nama as nama_pasien
        ');
        $this->db->from('pemeriksaan_lab pl');
        $this->db->join('pasien p', 'pl.pasien_id = p.pasien_id');
        $this->db->where('pl.status_pemeriksaan', 'pending');
        $this->db->order_by('pl.tanggal_pemeriksaan', 'ASC');
        $this->db->limit($limit);
        
        $query = $this->db->get();
        return $query->result_array();
        
    } catch (Exception $e) {
        log_message('error', 'Error getting pending examinations: ' . $e->getMessage());
        return array();
    }
}

public function get_recent_patients($limit = 5) {
    try {
        $this->db->select('nama, nik, created_at');
        $this->db->order_by('created_at', 'DESC');
        $this->db->limit($limit);
        
        $query = $this->db->get('pasien');
        $patients = $query->result_array();
        
        // Add time ago calculation
        foreach ($patients as &$patient) {
            $created_time = strtotime($patient['created_at']);
            $now = time();
            $diff = $now - $created_time;
            
            if ($diff < 3600) {
                $patient['time_ago'] = floor($diff / 60) . ' menit lalu';
            } elseif ($diff < 86400) {
                $patient['time_ago'] = floor($diff / 3600) . ' jam lalu';
            } else {
                $patient['time_ago'] = floor($diff / 86400) . ' hari lalu';
            }
            
            // Add status (simplified)
            $patient['status'] = 'Baru registrasi';
        }
        
        return $patients;
        
    } catch (Exception $e) {
        log_message('error', 'Error getting recent patients: ' . $e->getMessage());
        return array();
    }
}

public function get_inventory_alerts($limit = 10) {
    try {
        $this->db->select('
            tipe_inventory,
            nama_item,
            kode_unik,
            alert_level,
            CASE 
                WHEN tipe_inventory = "reagen" AND expired_date IS NOT NULL AND expired_date <= CURDATE() 
                THEN CONCAT("Kadaluarsa pada ", DATE_FORMAT(expired_date, "%d/%m/%Y"))
                WHEN tipe_inventory = "reagen" AND expired_date IS NOT NULL AND expired_date <= DATE_ADD(CURDATE(), INTERVAL 30 DAY)
                THEN CONCAT("Akan kadaluarsa dalam ", DATEDIFF(expired_date, CURDATE()), " hari")
                WHEN tipe_inventory = "reagen" AND jumlah_stok <= stok_minimal
                THEN CONCAT("Stok rendah (", jumlah_stok, " tersisa)")
                WHEN tipe_inventory = "alat" AND expired_date <= CURDATE()
                THEN CONCAT("Kalibrasi terlambat ", DATEDIFF(CURDATE(), expired_date), " hari")
                WHEN tipe_inventory = "alat" AND expired_date <= DATE_ADD(CURDATE(), INTERVAL 7 DAY)
                THEN CONCAT("Perlu kalibrasi dalam ", DATEDIFF(expired_date, CURDATE()), " hari")
                ELSE "Perlu perhatian"
            END as message
        ');
        $this->db->where("alert_level IN ('Urgent', 'Warning', 'Low Stock')", NULL, FALSE);
        $this->db->order_by('FIELD(alert_level, "Urgent", "Warning", "Low Stock")', '', FALSE);
        $this->db->limit($limit);
        
        $query = $this->db->get('v_inventory_status');
        return $query->result_array();
        
    } catch (Exception $e) {
        log_message('error', 'Error getting inventory alerts: ' . $e->getMessage());
        return array();
    }
}


public function get_system_status() {
    try {
        $status = array();
        
        // Database status
        try {
            $this->db->query('SELECT 1');
            $status['database'] = array(
                'status' => 'online',
                'message' => 'Connected'
            );
        } catch (Exception $e) {
            $status['database'] = array(
                'status' => 'offline',
                'message' => 'Connection failed'
            );
        }
        
        // Storage status (simplified)
        $status['storage'] = array(
            'status' => 'warning',
            'message' => '78% used'
        );
        
        // Backup status (check last backup from activity log)
        $this->db->select('created_at');
        $this->db->where('activity LIKE', '%backup%');
        $this->db->order_by('created_at', 'DESC');
        $this->db->limit(1);
        $query = $this->db->get('activity_log');
        
        if ($query->num_rows() > 0) {
            $last_backup = $query->row()->created_at;
            $backup_time = strtotime($last_backup);
            $hours_ago = (time() - $backup_time) / 3600;
            
            if ($hours_ago < 24) {
                $status['backup'] = array(
                    'status' => 'online',
                    'message' => 'Recent backup'
                );
            } else {
                $status['backup'] = array(
                    'status' => 'warning',
                    'message' => 'Backup needed'
                );
            }
        } else {
            $status['backup'] = array(
                'status' => 'warning',
                'message' => 'No recent backup'
            );
        }
        
        return $status;
        
    } catch (Exception $e) {
        log_message('error', 'Error getting system status: ' . $e->getMessage());
        return array(
            'database' => array('status' => 'unknown', 'message' => 'Check failed'),
            'storage' => array('status' => 'unknown', 'message' => 'Check failed'),
            'backup' => array('status' => 'unknown', 'message' => 'Check failed')
        );
    }
}

// Function untuk mendapatkan semua data dashboard dalam satu call
public function get_dashboard_data() {
    try {
        return array(
            'kpi' => $this->get_dashboard_kpi(),
            'examination_trend' => $this->get_examination_trend(),
            'user_distribution' => $this->get_user_distribution(),
            'pending_examinations' => $this->get_pending_examinations(),
            'recent_patients' => $this->get_recent_patients(),
            'inventory_alerts' => $this->get_inventory_alerts(),
            'recent_activities' => $this->get_recent_activities(),
            'system_status' => $this->get_system_status()
        );
    } catch (Exception $e) {
        log_message('error', 'Error getting dashboard data: ' . $e->getMessage());
        return false;
    }
}
public function get_recent_activities($limit = 10) {
    try {
        // Gunakan query builder yang benar untuk CASE statement
        $sql = "
            SELECT 
                al.activity,
                al.created_at,
                al.ip_address,
                CASE 
                    WHEN u.role = 'admin' THEN a.nama_admin
                    WHEN u.role = 'administrasi' THEN ad.nama_admin
                    WHEN u.role = 'petugas_lab' THEN p.nama_petugas
                    ELSE u.username
                END as nama_user,
                u.role
            FROM activity_log al
            LEFT JOIN users u ON al.user_id = u.user_id
            LEFT JOIN administrator a ON u.user_id = a.user_id AND u.role = 'admin'
            LEFT JOIN administrasi ad ON u.user_id = ad.user_id AND u.role = 'administrasi'
            LEFT JOIN petugas_lab p ON u.user_id = p.user_id AND u.role = 'petugas_lab'
            ORDER BY al.created_at DESC
            LIMIT ?
        ";
        
        $query = $this->db->query($sql, array($limit));
        $activities = $query->result_array();
        
        // Add time ago and activity type
        foreach ($activities as &$activity) {
            $created_time = strtotime($activity['created_at']);
            $now = time();
            $diff = $now - $created_time;
            
            if ($diff < 3600) {
                $activity['time_ago'] = floor($diff / 60) . ' menit lalu';
            } elseif ($diff < 86400) {
                $activity['time_ago'] = floor($diff / 3600) . ' jam lalu';
            } else {
                $activity['time_ago'] = floor($diff / 86400) . ' hari lalu';
            }
            
            // Determine activity type for styling
            if (strpos($activity['activity'], 'login') !== false || 
                strpos($activity['activity'], 'logout') !== false) {
                $activity['type'] = 'info';
            } elseif (strpos($activity['activity'], 'selesai') !== false || 
                     strpos($activity['activity'], 'berhasil') !== false) {
                $activity['type'] = 'success';
            } elseif (strpos($activity['activity'], 'hapus') !== false || 
                     strpos($activity['activity'], 'nonaktif') !== false) {
                $activity['type'] = 'error';
            } else {
                $activity['type'] = 'warning';
            }
        }
        
        return $activities;
        
    } catch (Exception $e) {
        log_message('error', 'Error getting recent activities: ' . $e->getMessage());
        return array();
    }
}

// Dan juga fix function yang lain yang menggunakan CASE statement serupa:

public function get_activity_logs($limit = 50, $offset = 0, $filters = array()) {
    try {
        // Gunakan raw query untuk menghindari masalah escaping
        $where_conditions = array('1=1');
        $params = array();
        
        // Apply filters
        if (!empty($filters['start_date'])) {
            $where_conditions[] = 'DATE(al.created_at) >= ?';
            $params[] = $filters['start_date'];
        }
        
        if (!empty($filters['end_date'])) {
            $where_conditions[] = 'DATE(al.created_at) <= ?';
            $params[] = $filters['end_date'];
        }
        
        if (!empty($filters['user_id'])) {
            $where_conditions[] = 'al.user_id = ?';
            $params[] = $filters['user_id'];
        }
        
        if (!empty($filters['activity_type'])) {
            $where_conditions[] = 'al.activity LIKE ?';
            $params[] = '%' . $filters['activity_type'] . '%';
        }
        
        if (!empty($filters['table_affected'])) {
            $where_conditions[] = 'al.table_affected = ?';
            $params[] = $filters['table_affected'];
        }
        
        if (!empty($filters['search'])) {
            $where_conditions[] = '(al.activity LIKE ? OR u.username LIKE ? OR al.table_affected LIKE ?)';
            $search_term = '%' . $filters['search'] . '%';
            $params[] = $search_term;
            $params[] = $search_term;
            $params[] = $search_term;
        }
        
        $where_clause = implode(' AND ', $where_conditions);
        
        $sql = "
            SELECT 
                al.log_id,
                al.user_id,
                al.activity,
                al.table_affected,
                al.record_id,
                al.ip_address,
                al.created_at,
                CASE 
                    WHEN u.role = 'admin' THEN a.nama_admin
                    WHEN u.role = 'administrasi' THEN ad.nama_admin  
                    WHEN u.role = 'petugas_lab' THEN p.nama_petugas
                    ELSE u.username
                END as nama_lengkap,
                u.username,
                u.role
            FROM activity_log al
            LEFT JOIN users u ON al.user_id = u.user_id
            LEFT JOIN administrator a ON u.user_id = a.user_id AND u.role = 'admin'
            LEFT JOIN administrasi ad ON u.user_id = ad.user_id AND u.role = 'administrasi'
            LEFT JOIN petugas_lab p ON u.user_id = p.user_id AND u.role = 'petugas_lab'
            WHERE {$where_clause}
            ORDER BY al.created_at DESC
            LIMIT ? OFFSET ?
        ";
        
        $params[] = $limit;
        $params[] = $offset;
        
        $query = $this->db->query($sql, $params);
        return $query->result_array();
        
    } catch (Exception $e) {
        log_message('error', 'Error getting activity logs: ' . $e->getMessage());
        return array();
    }
}

public function get_activity_statistics($date_range = 7) {
    try {
        $stats = array();
        
        $this->db->where('created_at >=', date('Y-m-d H:i:s', strtotime("-{$date_range} days")));
        $stats['total_activities'] = $this->db->count_all_results('activity_log');
        
        $sql = "
            SELECT 
                CASE 
                    WHEN activity LIKE '%login%' THEN 'Login/Logout'
                    WHEN activity LIKE '%created%' OR activity LIKE '%added%' THEN 'Create'
                    WHEN activity LIKE '%updated%' OR activity LIKE '%modified%' THEN 'Update'
                    WHEN activity LIKE '%deleted%' OR activity LIKE '%removed%' THEN 'Delete'
                    WHEN activity LIKE '%accessed%' THEN 'Access'
                    ELSE 'Other'
                END as activity_type,
                COUNT(*) as count
            FROM activity_log 
            WHERE created_at >= ?
            GROUP BY activity_type
            ORDER BY count DESC
        ";
        
        $activity_types = $this->db->query($sql, array(date('Y-m-d H:i:s', strtotime("-{$date_range} days"))))->result_array();
        
        $stats['by_type'] = array();
        foreach ($activity_types as $type) {
            $stats['by_type'][$type['activity_type']] = $type['count'];
        }
        
        $this->db->select('u.role, COUNT(*) as count');
        $this->db->from('activity_log al');
        $this->db->join('users u', 'al.user_id = u.user_id');
        $this->db->where('al.created_at >=', date('Y-m-d H:i:s', strtotime("-{$date_range} days")));
        $this->db->group_by('u.role');
        $this->db->order_by('count', 'DESC');
        $by_role = $this->db->get()->result_array();
        
        $stats['by_role'] = array();
        foreach ($by_role as $role) {
            $stats['by_role'][$role['role']] = $role['count'];
        }
        
        $this->db->select('DATE(created_at) as date, COUNT(*) as count');
        $this->db->where('created_at >=', date('Y-m-d H:i:s', strtotime("-{$date_range} days")));
        $this->db->group_by('DATE(created_at)');
        $this->db->order_by('date', 'ASC');
        $daily_trend = $this->db->get('activity_log')->result_array();
        
        $stats['daily_trend'] = $daily_trend;
        
        $sql = "
            SELECT 
                al.user_id,
                u.username,
                CASE 
                    WHEN u.role = 'admin' THEN a.nama_admin
                    WHEN u.role = 'administrasi' THEN ad.nama_admin  
                    WHEN u.role = 'petugas_lab' THEN p.nama_petugas
                    ELSE u.username
                END as nama_lengkap,
                u.role,
                COUNT(*) as activity_count
            FROM activity_log al
            LEFT JOIN users u ON al.user_id = u.user_id
            LEFT JOIN administrator a ON u.user_id = a.user_id AND u.role = 'admin'
            LEFT JOIN administrasi ad ON u.user_id = ad.user_id AND u.role = 'administrasi'
            LEFT JOIN petugas_lab p ON u.user_id = p.user_id AND u.role = 'petugas_lab'
            WHERE al.created_at >= ?
            GROUP BY al.user_id
            ORDER BY activity_count DESC
            LIMIT 10
        ";
        
        $most_active = $this->db->query($sql, array(date('Y-m-d H:i:s', strtotime("-{$date_range} days"))))->result_array();
        
        $stats['most_active_users'] = $most_active;
        
        return $stats;
        
    } catch (Exception $e) {
        log_message('error', 'Error getting activity statistics: ' . $e->getMessage());
        return array(
            'total_activities' => 0,
            'by_type' => array(),
            'by_role' => array(),
            'daily_trend' => array(),
            'most_active_users' => array()
        );
    }
}

public function get_all_users_for_filter() {
    try {
        $sql = "
            SELECT 
                u.user_id,
                u.username,
                u.role,
                CASE 
                    WHEN u.role = 'admin' THEN a.nama_admin
                    WHEN u.role = 'administrasi' THEN ad.nama_admin  
                    WHEN u.role = 'petugas_lab' THEN p.nama_petugas
                    ELSE u.username
                END as nama_lengkap
            FROM users u
            LEFT JOIN administrator a ON u.user_id = a.user_id AND u.role = 'admin'
            LEFT JOIN administrasi ad ON u.user_id = ad.user_id AND u.role = 'administrasi'
            LEFT JOIN petugas_lab p ON u.user_id = p.user_id AND u.role = 'petugas_lab'
            WHERE u.is_active = 1
            ORDER BY nama_lengkap ASC
        ";
        
        return $this->db->query($sql)->result_array();
        
    } catch (Exception $e) {
        log_message('error', 'Error getting users for filter: ' . $e->getMessage());
        return array();
    }
}

public function export_activity_logs($filters = array()) {
    try {
        $where_conditions = array('1=1');
        $params = array();
        
        if (!empty($filters['start_date'])) {
            $where_conditions[] = 'DATE(al.created_at) >= ?';
            $params[] = $filters['start_date'];
        }
        
        if (!empty($filters['end_date'])) {
            $where_conditions[] = 'DATE(al.created_at) <= ?';
            $params[] = $filters['end_date'];
        }
        
        if (!empty($filters['user_id'])) {
            $where_conditions[] = 'al.user_id = ?';
            $params[] = $filters['user_id'];
        }
        
        if (!empty($filters['activity_type'])) {
            $where_conditions[] = 'al.activity LIKE ?';
            $params[] = '%' . $filters['activity_type'] . '%';
        }
        
        if (!empty($filters['table_affected'])) {
            $where_conditions[] = 'al.table_affected = ?';
            $params[] = $filters['table_affected'];
        }
        
        if (!empty($filters['search'])) {
            $where_conditions[] = '(al.activity LIKE ? OR u.username LIKE ? OR al.table_affected LIKE ?)';
            $search_term = '%' . $filters['search'] . '%';
            $params[] = $search_term;
            $params[] = $search_term;
            $params[] = $search_term;
        }
        
        $where_clause = implode(' AND ', $where_conditions);
        
        $sql = "
            SELECT 
                al.log_id,
                al.activity,
                al.table_affected,
                al.record_id,
                al.ip_address,
                al.created_at,
                CASE 
                    WHEN u.role = 'admin' THEN a.nama_admin
                    WHEN u.role = 'administrasi' THEN ad.nama_admin  
                    WHEN u.role = 'petugas_lab' THEN p.nama_petugas
                    ELSE u.username
                END as nama_lengkap,
                u.username,
                u.role
            FROM activity_log al
            LEFT JOIN users u ON al.user_id = u.user_id
            LEFT JOIN administrator a ON u.user_id = a.user_id AND u.role = 'admin'
            LEFT JOIN administrasi ad ON u.user_id = ad.user_id AND u.role = 'administrasi'
            LEFT JOIN petugas_lab p ON u.user_id = p.user_id AND u.role = 'petugas_lab'
            WHERE {$where_clause}
            ORDER BY al.created_at DESC
        ";
        
        return $this->db->query($sql, $params)->result_array();
        
    } catch (Exception $e) {
        log_message('error', 'Error exporting activity logs: ' . $e->getMessage());
        return array();
    }
}
}