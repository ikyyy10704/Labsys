<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Admin extends CI_Controller {

    public function __construct()
    {
        parent::__construct();
        
        if (!$this->session->userdata('logged_in') || $this->session->userdata('role') !== 'admin') {
            $this->session->set_flashdata('error', 'Akses ditolak. Anda tidak memiliki izin untuk mengakses halaman ini.');
            redirect('auth/login');
        }
        
        $this->load->model(['User_model', 'Admin_model', 'Administrasi_model']);
        $this->load->library(['form_validation', 'upload']);
        $this->load->helper(['form', 'url', 'date']);
    }

    // ==========================================
    // DASHBOARD
    // ==========================================

    
    public function user_management()
    {
        // Set fullwidth layout untuk halaman ini
        $data['fullwidth'] = true;
        $data['title'] = 'Manajemen Pengguna';
        
        try {
            $data['users'] = $this->Admin_model->get_all_users();
            $data['user_stats'] = $this->Admin_model->get_user_statistics();
        } catch (Exception $e) {
            log_message('error', 'Error getting users data: ' . $e->getMessage());
            $data['users'] = array();
            $data['user_stats'] = $this->_get_default_user_stats();
        }
        
        // Load view dengan fullwidth
        $this->load->view('template/header', $data);
        $this->load->view('template/sidebar', $data);
        $this->_load_fullwidth_view('admin/user_manajemen', $data);
        $this->load->view('template/footer', $data);
    }

    public function get_users_data()
    {
        $this->output->set_content_type('application/json');
        
        try {
            $users = $this->Admin_model->get_all_users();
            $stats = $this->Admin_model->get_user_statistics();
            
            $response = array(
                'success' => true,
                'users' => $users,
                'stats' => $stats
            );
        } catch (Exception $e) {
            $response = array(
                'success' => false,
                'message' => 'Gagal mengambil data pengguna'
            );
        }
        
        $this->output->set_output(json_encode($response));
    }

    public function ajax_create_user()
    {
        $this->output->set_content_type('application/json');
        
        if ($this->input->method() !== 'post') {
            $this->output->set_output(json_encode(array(
                'success' => false,
                'message' => 'Method not allowed'
            )));
            return;
        }
        
        $this->form_validation->set_rules('username', 'Username', 'required|is_unique[users.username]|alpha_dash|min_length[3]');
        $this->form_validation->set_rules('password', 'Password', 'required|min_length[6]');
        $this->form_validation->set_rules('role', 'Role', 'required|in_list[admin,administrasi,petugas_lab,supervisor]');
        $this->form_validation->set_rules('nama_lengkap', 'Nama Lengkap', 'required|min_length[2]');
        
        if ($this->form_validation->run() === FALSE) {
            $this->output->set_output(json_encode(array(
                'success' => false,
                'message' => 'Validasi gagal',
                'errors' => $this->form_validation->error_array()
            )));
            return;
        }
        
        $role = $this->input->post('role');
        
        $user_data = array(
            'username' => $this->input->post('username'),
            'password' => md5($this->input->post('password')),
            'role' => $role,
            'is_active' => 1,
            'created_at' => date('Y-m-d H:i:s')
        );
        
        $role_data = array();
        
        switch($role) {
            case 'admin':
                $role_data['nama_admin'] = $this->input->post('nama_lengkap');
                break;
            case 'administrasi':
                $role_data['nama_admin'] = $this->input->post('nama_lengkap');
                $role_data['telepon'] = $this->input->post('telepon');
                break;
            case 'petugas_lab':
                $role_data['nama_petugas'] = $this->input->post('nama_lengkap');
                $role_data['jenis_keahlian'] = $this->input->post('jenis_keahlian');
                $role_data['telepon'] = $this->input->post('telepon_lab');
                $role_data['alamat'] = $this->input->post('alamat');
                break;
            case 'supervisor':
                $role_data['nama_supervisor'] = $this->input->post('nama_lengkap');
                $role_data['jenis_keahlian'] = $this->input->post('jenis_keahlian_supervisor');
                $role_data['telepon'] = $this->input->post('telepon_supervisor');
                $role_data['alamat'] = $this->input->post('alamat_supervisor');
                break;
        }
        
        $user_id = $this->Admin_model->create_user($user_data, $role_data);
        
        if ($user_id) {
            $this->Admin_model->log_activity(
                $this->session->userdata('user_id'), 
                'Pengguna baru ditambahkan: ' . $this->input->post('username'), 
                'users', 
                $user_id
            );
            
            $this->output->set_output(json_encode(array(
                'success' => true,
                'message' => 'Pengguna berhasil ditambahkan',
                'user_id' => $user_id
            )));
        } else {
            $this->output->set_output(json_encode(array(
                'success' => false,
                'message' => 'Gagal menambahkan pengguna'
            )));
        }
    }

    public function ajax_update_user($user_id)
    {
        $this->output->set_content_type('application/json');
        
        if ($this->input->method() !== 'post') {
            $this->output->set_output(json_encode(array(
                'success' => false,
                'message' => 'Method not allowed'
            )));
            return;
        }
        
        $user = $this->Admin_model->get_user_by_id($user_id);
        
        if (!$user) {
            $this->output->set_output(json_encode(array(
                'success' => false,
                'message' => 'Pengguna tidak ditemukan'
            )));
            return;
        }
        
        $this->form_validation->set_rules('username', 'Username', 'required|alpha_dash|min_length[3]|callback_check_username_unique[' . $user_id . ']');
        $this->form_validation->set_rules('nama_lengkap', 'Nama Lengkap', 'required|min_length[2]');
        
        if ($this->input->post('password')) {
            $this->form_validation->set_rules('password', 'Password', 'min_length[6]');
        }
        
        if ($this->form_validation->run() === FALSE) {
            $this->output->set_output(json_encode(array(
                'success' => false,
                'message' => 'Validasi gagal',
                'errors' => $this->form_validation->error_array()
            )));
            return;
        }
        
        $user_data = array(
            'username' => $this->input->post('username'),
            'updated_at' => date('Y-m-d H:i:s')
        );
        
        if ($this->input->post('password')) {
            $user_data['password'] = md5($this->input->post('password'));
        }
        
        $role_data = array();
        
        switch($user['role']) {
            case 'admin':
                $role_data['nama_admin'] = $this->input->post('nama_lengkap');
                break;
            case 'administrasi':
                $role_data['nama_admin'] = $this->input->post('nama_lengkap');
                $role_data['telepon'] = $this->input->post('telepon');
                break;
            case 'petugas_lab':
                $role_data['nama_petugas'] = $this->input->post('nama_lengkap');
                $role_data['jenis_keahlian'] = $this->input->post('jenis_keahlian');
                $role_data['telepon'] = $this->input->post('telepon');
                $role_data['alamat'] = $this->input->post('alamat');
                break;
            case 'supervisor':
                $role_data['nama_supervisor'] = $this->input->post('nama_lengkap');
                $role_data['jenis_keahlian'] = $this->input->post('jenis_keahlian_supervisor');
                $role_data['telepon'] = $this->input->post('telepon_supervisor');
                $role_data['alamat'] = $this->input->post('alamat_supervisor');
                break;
        }
        
        if ($this->Admin_model->update_user($user_id, $user_data, $role_data)) {
            $this->Admin_model->log_activity(
                $this->session->userdata('user_id'), 
                'Data pengguna diperbarui: ' . $user['username'], 
                'users', 
                $user_id
            );
            
            $this->output->set_output(json_encode(array(
                'success' => true,
                'message' => 'Pengguna berhasil diperbarui'
            )));
        } else {
            $this->output->set_output(json_encode(array(
                'success' => false,
                'message' => 'Gagal memperbarui pengguna'
            )));
        }
    }

    public function ajax_toggle_user_status($user_id)
    {
        $this->output->set_content_type('application/json');
        
        $user = $this->Admin_model->get_user_by_id($user_id);
        
        if (!$user) {
            $this->output->set_output(json_encode(array(
                'success' => false,
                'message' => 'Pengguna tidak ditemukan'
            )));
            return;
        }
        
        if ($user_id == $this->session->userdata('user_id')) {
            $this->output->set_output(json_encode(array(
                'success' => false,
                'message' => 'Anda tidak dapat menonaktifkan akun Anda sendiri'
            )));
            return;
        }
        
        $new_status = $user['is_active'] ? 0 : 1;
        $status_text = $new_status ? 'diaktifkan' : 'dinonaktifkan';
        
        if ($this->Admin_model->update_user_status($user_id, $new_status)) {
            $activity = $new_status ? 'Pengguna diaktifkan: ' : 'Pengguna dinonaktifkan: ';
            $activity .= $user['username'];
            
            $this->Admin_model->log_activity(
                $this->session->userdata('user_id'), 
                $activity, 
                'users', 
                $user_id
            );
            
            $this->output->set_output(json_encode(array(
                'success' => true,
                'message' => "Pengguna berhasil {$status_text}",
                'new_status' => $new_status
            )));
        } else {
            $this->output->set_output(json_encode(array(
                'success' => false,
                'message' => "Gagal {$status_text} pengguna"
            )));
        }
    }

    public function ajax_get_user_details($user_id)
    {
        $this->output->set_content_type('application/json');
        
        $user = $this->Admin_model->get_user_by_id($user_id);
        
        if (!$user) {
            $this->output->set_output(json_encode(array(
                'success' => false,
                'message' => 'Pengguna tidak ditemukan'
            )));
            return;
        }
        
        $user_details = $this->Admin_model->get_user_details($user);
        
        $this->output->set_output(json_encode(array(
            'success' => true,
            'user' => $user,
            'details' => $user_details
        )));
    }

    public function ajax_delete_user($user_id = null)
    {
        $this->output->set_content_type('application/json');
        
        if (!$user_id) {
            $this->output->set_output(json_encode(array(
                'success' => false,
                'message' => 'ID pengguna tidak valid'
            )));
            return;
        }
        
        $method = $this->input->method();
        if (!in_array($method, ['post', 'delete'])) {
            $this->output->set_output(json_encode(array(
                'success' => false,
                'message' => 'Method tidak diizinkan'
            )));
            return;
        }
        
        try {
            $user = $this->Admin_model->get_user_by_id($user_id);
            
            if (!$user) {
                $this->output->set_output(json_encode(array(
                    'success' => false,
                    'message' => 'Pengguna tidak ditemukan'
                )));
                return;
            }
            
            if ($user_id == $this->session->userdata('user_id')) {
                $this->output->set_output(json_encode(array(
                    'success' => false,
                    'message' => 'Anda tidak dapat menghapus akun Anda sendiri'
                )));
                return;
            }
            
            $delete_result = $this->Admin_model->delete_user($user_id);
            
            if ($delete_result) {
                $this->Admin_model->log_activity(
                    $this->session->userdata('user_id'), 
                    'Pengguna dihapus: ' . $user['username'], 
                    'users', 
                    $user_id
                );
                
                $this->output->set_output(json_encode(array(
                    'success' => true,
                    'message' => 'Pengguna berhasil dihapus'
                )));
            } else {
                $this->output->set_output(json_encode(array(
                    'success' => false,
                    'message' => 'Gagal menghapus pengguna'
                )));
            }
            
        } catch (Exception $e) {
            log_message('error', 'Error deleting user: ' . $e->getMessage());
            $this->output->set_output(json_encode(array(
                'success' => false,
                'message' => 'Terjadi kesalahan sistem: ' . $e->getMessage()
            )));
        }
    }

    public function check_username_unique($username, $user_id)
    {
        if ($this->Admin_model->check_username_exists($username, $user_id)) {
            $this->form_validation->set_message('check_username_unique', 'Username sudah digunakan');
            return FALSE;
        }
        return TRUE;
    }

    // ==========================================
    // ACTIVITY REPORTS - FULLWIDTH VERSION
    // ==========================================

    public function activity_reports()
    {
        // Set fullwidth layout untuk halaman ini
        $data['fullwidth'] = true;
        $data['title'] = 'Laporan Aktivitas';
        
        try {
            $filters = array(
                'start_date' => $this->input->get('start_date'),
                'end_date' => $this->input->get('end_date'),
                'user_id' => $this->input->get('user_id'),
                'activity_type' => $this->input->get('activity_type'),
                'table_affected' => $this->input->get('table_affected'),
                'search' => $this->input->get('search')
            );
            
            $per_page = 50;
            $page = $this->input->get('page') ? (int)$this->input->get('page') : 1;
            $offset = ($page - 1) * $per_page;
            
            $data['activity_logs'] = $this->Admin_model->get_activity_logs($per_page, $offset, $filters);
            $data['total_records'] = $this->Admin_model->count_activity_logs($filters);
            $data['statistics'] = $this->Admin_model->get_activity_statistics(7);
            $data['users'] = $this->Admin_model->get_all_users_for_filter();
            $data['tables'] = $this->Admin_model->get_activity_tables();
            $data['today_login_logout'] = $this->Admin_model->get_today_login_logout_count();
            
            $data['pagination'] = array(
                'current_page' => $page,
                'per_page' => $per_page,
                'total_records' => $data['total_records'],
                'total_pages' => ceil($data['total_records'] / $per_page),
                'filters' => $filters
            );
            
            $this->Admin_model->log_activity(
                $this->session->userdata('user_id'),
                'Mengakses laporan aktivitas',
                'system',
                null
            );
            
        } catch (Exception $e) {
            log_message('error', 'Error loading activity reports: ' . $e->getMessage());
            $data['activity_logs'] = array();
            $data['total_records'] = 0;
            $data['statistics'] = array();
            $data['users'] = array();
            $data['tables'] = array();
            $data['pagination'] = array(
                'current_page' => 1,
                'per_page' => $per_page,
                'total_records' => 0,
                'total_pages' => 0,
                'filters' => array()
            );
        }
        
        // Load view dengan fullwidth
        $this->load->view('template/header', $data);
        $this->load->view('template/sidebar', $data);
        $this->_load_fullwidth_view('admin/activity_reports', $data);
        $this->load->view('template/footer', $data);
    }

    public function ajax_get_activity_logs()
    {
        $this->output->set_content_type('application/json');
        
        try {
            $filters = array(
                'start_date' => $this->input->get('start_date'),
                'end_date' => $this->input->get('end_date'),
                'user_id' => $this->input->get('user_id'),
                'activity_type' => $this->input->get('activity_type'),
                'table_affected' => $this->input->get('table_affected'),
                'search' => $this->input->get('search')
            );
            
            $per_page = $this->input->get('per_page') ? (int)$this->input->get('per_page') : 50;
            $page = $this->input->get('page') ? (int)$this->input->get('page') : 1;
            $offset = ($page - 1) * $per_page;
            
            $activity_logs = $this->Admin_model->get_activity_logs($per_page, $offset, $filters);
            $total_records = $this->Admin_model->count_activity_logs($filters);
            
            $response = array(
                'success' => true,
                'data' => $activity_logs,
                'total_records' => $total_records,
                'current_page' => $page,
                'per_page' => $per_page,
                'total_pages' => ceil($total_records / $per_page)
            );
            
        } catch (Exception $e) {
            $response = array(
                'success' => false,
                'message' => 'Gagal mengambil data aktivitas'
            );
        }
        
        $this->output->set_output(json_encode($response));
    }

    public function ajax_get_activity_statistics()
    {
        $this->output->set_content_type('application/json');
        
        try {
            $date_range = $this->input->get('date_range') ? (int)$this->input->get('date_range') : 7;
            $statistics = $this->Admin_model->get_activity_statistics($date_range);
            
            $response = array(
                'success' => true,
                'data' => $statistics
            );
            
        } catch (Exception $e) {
            $response = array(
                'success' => false,
                'message' => 'Gagal mengambil statistik aktivitas'
            );
        }
        
        $this->output->set_output(json_encode($response));
    }

    public function export_activity_logs()
    {
        try {
            $filters = array(
                'start_date' => $this->input->get('start_date'),
                'end_date' => $this->input->get('end_date'),
                'user_id' => $this->input->get('user_id'),
                'activity_type' => $this->input->get('activity_type'),
                'table_affected' => $this->input->get('table_affected'),
                'search' => $this->input->get('search')
            );
            
            $data = $this->Admin_model->export_activity_logs($filters);
            
            if (empty($data)) {
                $this->session->set_flashdata('error', 'Tidak ada data untuk diekspor');
                redirect('admin/activity_reports');
                return;
            }
            
            $filename = 'activity_logs_' . date('Y-m-d_H-i-s') . '.csv';
            header('Content-Type: text/csv; charset=UTF-8');
            header('Content-Disposition: attachment; filename="' . $filename . '"');
            header('Cache-Control: no-cache, must-revalidate');
            header('Pragma: no-cache');
            
            $output = fopen('php://output', 'w');
            
            fprintf($output, chr(0xEF).chr(0xBB).chr(0xBF));
            
            fputcsv($output, array(
                'Log ID',
                'Tanggal & Waktu',
                'Pengguna',
                'Username',
                'Role',
                'Aktivitas',
                'Tabel Terkait',
                'Record ID',
                'IP Address'
            ));
            
            foreach ($data as $row) {
                fputcsv($output, array(
                    $row['log_id'],
                    date('d/m/Y H:i:s', strtotime($row['created_at'])),
                    $row['nama_lengkap'] ?: $row['username'],
                    $row['username'],
                    ucfirst($row['role']),
                    $row['activity'],
                    $row['table_affected'] ?: '-',
                    $row['record_id'] ?: '-',
                    $row['ip_address']
                ));
            }
            
            fclose($output);
            
            $this->Admin_model->log_activity(
                $this->session->userdata('user_id'),
                'Log aktivitas diekspor ke CSV',
                'activity_log',
                null
            );
            
        } catch (Exception $e) {
            log_message('error', 'Error exporting activity logs: ' . $e->getMessage());
            $this->session->set_flashdata('error', 'Gagal mengekspor data aktivitas');
            redirect('admin/activity_reports');
        }
    }

    public function ajax_delete_activity_log($log_id)
    {
        $this->output->set_content_type('application/json');
        
        if ($this->input->method() !== 'delete') {
            $this->output->set_output(json_encode(array(
                'success' => false,
                'message' => 'Method not allowed'
            )));
            return;
        }
        
        try {
            $this->db->where('log_id', $log_id);
            $result = $this->db->delete('activity_log');
            
            if ($result) {
                $this->Admin_model->log_activity(
                    $this->session->userdata('user_id'),
                    'Log aktivitas dihapus',
                    'activity_log',
                    $log_id
                );
                
                $this->output->set_output(json_encode(array(
                    'success' => true,
                    'message' => 'Log aktivitas berhasil dihapus'
                )));
            } else {
                $this->output->set_output(json_encode(array(
                    'success' => false,
                    'message' => 'Gagal menghapus log aktivitas'
                )));
            }
            
        } catch (Exception $e) {
            log_message('error', 'Error deleting activity log: ' . $e->getMessage());
            $this->output->set_output(json_encode(array(
                'success' => false,
                'message' => 'Terjadi kesalahan sistem'
            )));
        }
    }

    public function ajax_clear_old_activity_logs()
    {
        $this->output->set_content_type('application/json');
        
        if ($this->input->method() !== 'post') {
            $this->output->set_output(json_encode(array(
                'success' => false,
                'message' => 'Method not allowed'
            )));
            return;
        }
        
        try {
            $days = $this->input->post('days') ? (int)$this->input->post('days') : 30;
            
            $this->db->where('created_at <', date('Y-m-d H:i:s', strtotime("-{$days} days")));
            $this->db->delete('activity_log');
            
            $affected_rows = $this->db->affected_rows();
            
            $this->Admin_model->log_activity(
                $this->session->userdata('user_id'),
                "Log aktivitas lama dibersihkan (lebih dari {$days} hari)",
                'activity_log',
                null
            );
            
            $this->output->set_output(json_encode(array(
                'success' => true,
                'message' => "Berhasil menghapus {$affected_rows} log aktivitas lama",
                'affected_rows' => $affected_rows
            )));
            
        } catch (Exception $e) {
            log_message('error', 'Error clearing old activity logs: ' . $e->getMessage());
            $this->output->set_output(json_encode(array(
                'success' => false,
                'message' => 'Gagal menghapus log aktivitas lama'
            )));
        }
    }

    // ==========================================
    // EXAMINATION REPORTS - FULLWIDTH VERSION
    // ==========================================

    public function examination_reports()
    {
        // Set fullwidth layout untuk halaman ini
        $data['fullwidth'] = true;
        $data['title'] = 'Laporan Pemeriksaan';
        
        try {
            $data['stats'] = $this->Admin_model->get_examination_statistics();
            $data['chart_data'] = $this->Admin_model->get_examination_chart_data();
            
            $this->Admin_model->log_activity(
                $this->session->userdata('user_id'),
                'Mengakses laporan pemeriksaan',
                'system',
                null
            );
            
        } catch (Exception $e) {
            log_message('error', 'Error loading examination reports: ' . $e->getMessage());
            $data['stats'] = array(
                'total' => 0,
                'pending' => 0,
                'progress' => 0,
                'selesai' => 0,
                'cancelled' => 0
            );
            $data['chart_data'] = array();
        }
        
        // Load view dengan fullwidth
        $this->load->view('template/header', $data);
        $this->load->view('template/sidebar', $data);
        $this->_load_fullwidth_view('admin/examination_reports', $data);
        $this->load->view('template/footer', $data);
    }

    public function ajax_get_examination_reports()
    {
        $this->output->set_content_type('application/json');
        
        try {
            $filters = array(
                'start_date' => $this->input->get('start_date'),
                'end_date' => $this->input->get('end_date'),
                'status' => $this->input->get('status'),
                'jenis_pemeriksaan' => $this->input->get('jenis_pemeriksaan'),
                'search' => $this->input->get('search')
            );
            
            $per_page = $this->input->get('per_page') ? (int)$this->input->get('per_page') : 20;
            $page = $this->input->get('page') ? (int)$this->input->get('page') : 1;
            $offset = ($page - 1) * $per_page;
            
            $examinations = $this->Admin_model->get_examination_reports($per_page, $offset, $filters);
            $total_records = $this->Admin_model->count_examination_reports($filters);
            $stats = $this->Admin_model->get_examination_statistics($filters);
            $chart_data = $this->Admin_model->get_examination_chart_data($filters);
            
            $response = array(
                'success' => true,
                'examinations' => $examinations,
                'total_records' => $total_records,
                'stats' => $stats,
                'chart_data' => $chart_data,
                'pagination' => array(
                    'current_page' => $page,
                    'per_page' => $per_page,
                    'total_pages' => ceil($total_records / $per_page),
                    'total_records' => $total_records
                )
            );
            
        } catch (Exception $e) {
            $response = array(
                'success' => false,
                'message' => 'Gagal mengambil data laporan pemeriksaan'
            );
        }
        
        $this->output->set_output(json_encode($response));
    }

    public function ajax_get_examination_detail($examination_id)
    {
        $this->output->set_content_type('application/json');
        
        try {
            $examination = $this->Admin_model->get_examination_detail($examination_id);
            
            if (!$examination) {
                $this->output->set_output(json_encode(array(
                    'success' => false,
                    'message' => 'Pemeriksaan tidak ditemukan'
                )));
                return;
            }
            
            $results = $this->Admin_model->get_examination_results($examination_id, $examination['jenis_pemeriksaan']);
            $timeline = $this->Admin_model->get_examination_timeline($examination_id);
            
            $response = array(
                'success' => true,
                'examination' => $examination,
                'results' => $results,
                'timeline' => $timeline
            );
            
        } catch (Exception $e) {
            log_message('error', 'Error getting examination detail: ' . $e->getMessage());
            $response = array(
                'success' => false,
                'message' => 'Gagal mengambil detail pemeriksaan'
            );
        }
        
        $this->output->set_output(json_encode($response));
    }

    // ==========================================
    // FINANCIAL REPORTS - FULLWIDTH VERSION
    // ==========================================

    public function financial_reports()
    {
        // Set fullwidth layout untuk halaman ini
        $data['fullwidth'] = true;
        $data['title'] = 'Laporan Keuangan';
        
        try {
            $data['stats'] = $this->Admin_model->get_financial_statistics();
            $data['chart_data'] = $this->Admin_model->get_financial_chart_data();
            
            $this->Admin_model->log_activity(
                $this->session->userdata('user_id'),
                'Mengakses laporan keuangan',
                'system',
                null
            );
            
        } catch (Exception $e) {
            log_message('error', 'Error loading financial reports: ' . $e->getMessage());
            $data['stats'] = array(
                'total_revenue' => 0,
                'paid_revenue' => 0,
                'unpaid_revenue' => 0,
                'installment_revenue' => 0,
                'total_invoices' => 0,
                'payment_rate' => 0
            );
            $data['chart_data'] = array();
        }
        
        // Load view tanpa template untuk fullwidth
        $this->load->view('template/header', $data);
        $this->load->view('template/sidebar', $data);
        $this->_load_fullwidth_view('admin/financial_reports', $data);
        $this->load->view('template/footer');
    }

    public function ajax_get_financial_reports()
    {
        $this->output->set_content_type('application/json');
        
        try {
            $filters = array(
                'start_date' => $this->input->get('start_date'),
                'end_date' => $this->input->get('end_date'),
                'status' => $this->input->get('status'),
                'jenis_pembayaran' => $this->input->get('jenis_pembayaran'),
                'metode_pembayaran' => $this->input->get('metode_pembayaran'),
                'search' => $this->input->get('search')
            );
            
            $per_page = $this->input->get('per_page') ? (int)$this->input->get('per_page') : 20;
            $page = $this->input->get('page') ? (int)$this->input->get('page') : 1;
            $offset = ($page - 1) * $per_page;
            
            // Validasi parameter
            if ($per_page > 100) $per_page = 100; // Batas maksimal per halaman
            if ($page < 1) $page = 1;
            
            $invoices = $this->Admin_model->get_financial_reports($per_page, $offset, $filters);
            $total_records = $this->Admin_model->count_financial_reports($filters);
            $stats = $this->Admin_model->get_financial_statistics($filters);
            $chart_data = $this->Admin_model->get_financial_chart_data($filters);
            
            $response = array(
                'success' => true,
                'invoices' => $invoices,
                'total_records' => $total_records,
                'stats' => $stats,
                'chart_data' => $chart_data,
                'pagination' => array(
                    'current_page' => $page,
                    'per_page' => $per_page,
                    'total_pages' => ceil($total_records / $per_page),
                    'total_records' => $total_records
                )
            );
            
        } catch (Exception $e) {
            log_message('error', 'Error getting financial reports: ' . $e->getMessage());
            $response = array(
                'success' => false,
                'message' => 'Gagal mengambil data laporan keuangan',
                'debug_info' => ENVIRONMENT === 'development' ? $e->getMessage() : null
            );
        }
        
        $this->output->set_output(json_encode($response));
    }

    public function ajax_get_invoice_detail($invoice_id)
    {
        $this->output->set_content_type('application/json');
        
        try {
            // Validasi input
            if (empty($invoice_id) || !is_numeric($invoice_id)) {
                $this->output->set_output(json_encode(array(
                    'success' => false,
                    'message' => 'ID invoice tidak valid'
                )));
                return;
            }
            
            $invoice_id = (int)$invoice_id;
            
            $invoice = $this->Admin_model->get_invoice_detail($invoice_id);
            
            if (!$invoice) {
                $this->output->set_output(json_encode(array(
                    'success' => false,
                    'message' => 'Invoice tidak ditemukan'
                )));
                return;
            }
            
            $examination = $this->Admin_model->get_examination_by_invoice($invoice_id);
            
            $response = array(
                'success' => true,
                'invoice' => $invoice,
                'examination' => $examination
            );
            
        } catch (Exception $e) {
            log_message('error', 'Error getting invoice detail: ' . $e->getMessage());
            $response = array(
                'success' => false,
                'message' => 'Gagal mengambil detail invoice'
            );
        }
        
        $this->output->set_output(json_encode($response));
    }

    // ==========================================
    // PAYMENT MANAGEMENT FUNCTIONS
    // ==========================================

    public function ajax_update_payment_status()
    {
        $this->output->set_content_type('application/json');
        
        if ($this->input->method() !== 'post') {
            $this->output->set_output(json_encode(array(
                'success' => false,
                'message' => 'Method not allowed'
            )));
            return;
        }
        
        try {
            $invoice_id = $this->input->post('invoice_id');
            $status = $this->input->post('status');
            $metode_pembayaran = $this->input->post('metode_pembayaran');
            $tanggal_pembayaran = $this->input->post('tanggal_pembayaran');
            $keterangan = $this->input->post('keterangan');
            
            // Validasi input
            if (!$invoice_id || !$status) {
                $this->output->set_output(json_encode(array(
                    'success' => false,
                    'message' => 'Data tidak lengkap'
                )));
                return;
            }
            
            if (!is_numeric($invoice_id)) {
                $this->output->set_output(json_encode(array(
                    'success' => false,
                    'message' => 'ID invoice tidak valid'
                )));
                return;
            }
            
            if (!in_array($status, ['lunas', 'belum_bayar', 'cicilan'])) {
                $this->output->set_output(json_encode(array(
                    'success' => false,
                    'message' => 'Status pembayaran tidak valid'
                )));
                return;
            }
            
            $update_data = array(
                'status_pembayaran' => $status,
                'metode_pembayaran' => $metode_pembayaran,
                'keterangan' => $keterangan
            );
            
            if ($status === 'lunas' && $tanggal_pembayaran) {
                $update_data['tanggal_pembayaran'] = $tanggal_pembayaran;
            }
            
            if ($this->Admin_model->update_invoice_payment($invoice_id, $update_data)) {
                $this->Admin_model->log_activity(
                    $this->session->userdata('user_id'),
                    'Status pembayaran invoice diperbarui: ' . $status,
                    'invoice',
                    $invoice_id
                );
                
                $this->output->set_output(json_encode(array(
                    'success' => true,
                    'message' => 'Status pembayaran berhasil diperbarui'
                )));
            } else {
                $this->output->set_output(json_encode(array(
                    'success' => false,
                    'message' => 'Gagal memperbarui status pembayaran'
                )));
            }
            
        } catch (Exception $e) {
            log_message('error', 'Error updating payment status: ' . $e->getMessage());
            $this->output->set_output(json_encode(array(
                'success' => false,
                'message' => 'Terjadi kesalahan sistem'
            )));
        }
    }

    // ==========================================
    // ADDITIONAL FINANCIAL FUNCTIONS
    // ==========================================

    public function ajax_get_financial_dashboard_stats()
    {
        $this->output->set_content_type('application/json');
        
        try {
            $stats = array();
            
            // Daily stats
            $today = date('Y-m-d');
            $stats['today'] = $this->Admin_model->get_financial_statistics(array('start_date' => $today, 'end_date' => $today));
            
            // Monthly stats
            $this_month_start = date('Y-m-01');
            $this_month_end = date('Y-m-t');
            $stats['this_month'] = $this->Admin_model->get_financial_statistics(array('start_date' => $this_month_start, 'end_date' => $this_month_end));
            
            // Year stats
            $this_year_start = date('Y-01-01');
            $this_year_end = date('Y-12-31');
            $stats['this_year'] = $this->Admin_model->get_financial_statistics(array('start_date' => $this_year_start, 'end_date' => $this_year_end));
            
            // Top paying patients
            $stats['top_patients'] = $this->Admin_model->get_top_paying_patients(5);
            
            // Overdue payments
            $stats['overdue_payments'] = $this->Admin_model->get_overdue_payments(30);
            
            // Payment method statistics
            $stats['payment_methods'] = $this->Admin_model->get_payment_method_statistics();
            
            $response = array(
                'success' => true,
                'data' => $stats
            );
            
        } catch (Exception $e) {
            log_message('error', 'Error getting financial dashboard stats: ' . $e->getMessage());
            $response = array(
                'success' => false,
                'message' => 'Gagal mengambil statistik keuangan'
            );
        }
        
        $this->output->set_output(json_encode($response));
    }

    public function ajax_get_monthly_revenue()
    {
        $this->output->set_content_type('application/json');
        
        try {
            $year = $this->input->get('year') ? (int)$this->input->get('year') : date('Y');
            
            if ($year < 2000 || $year > 2100) {
                $this->output->set_output(json_encode(array(
                    'success' => false,
                    'message' => 'Tahun tidak valid'
                )));
                return;
            }
            
            $monthly_data = $this->Admin_model->get_monthly_revenue_summary($year);
            
            $response = array(
                'success' => true,
                'data' => $monthly_data,
                'year' => $year
            );
            
        } catch (Exception $e) {
            log_message('error', 'Error getting monthly revenue: ' . $e->getMessage());
            $response = array(
                'success' => false,
                'message' => 'Gagal mengambil data pendapatan bulanan'
            );
        }
        
        $this->output->set_output(json_encode($response));
    }

    public function generate_invoice($examination_id)
    {
        $this->output->set_content_type('application/json');
        
        if ($this->input->method() !== 'post') {
            $this->output->set_output(json_encode(array(
                'success' => false,
                'message' => 'Method not allowed'
            )));
            return;
        }
        
        try {
            if (empty($examination_id) || !is_numeric($examination_id)) {
                $this->output->set_output(json_encode(array(
                    'success' => false,
                    'message' => 'ID pemeriksaan tidak valid'
                )));
                return;
            }
            
            $examination_id = (int)$examination_id;
            
            // Check if examination exists
            $examination = $this->Admin_model->get_examination_detail($examination_id);
            if (!$examination) {
                $this->output->set_output(json_encode(array(
                    'success' => false,
                    'message' => 'Pemeriksaan tidak ditemukan'
                )));
                return;
            }
            
            // Check if invoice already exists
            $existing_invoice = $this->Admin_model->get_invoice_data_safe($examination_id);
            if ($existing_invoice) {
                $this->output->set_output(json_encode(array(
                    'success' => false,
                    'message' => 'Invoice untuk pemeriksaan ini sudah ada',
                    'invoice_id' => $existing_invoice['invoice_id']
                )));
                return;
            }
            
            // Create new invoice
            $invoice_data = $this->Admin_model->create_invoice_for_examination_safe($examination_id);
            
            if ($invoice_data) {
                $this->Admin_model->log_activity(
                    $this->session->userdata('user_id'),
                    'Invoice baru dibuat: ' . $invoice_data['nomor_invoice'],
                    'invoice',
                    $invoice_data['invoice_id']
                );
                
                $this->output->set_output(json_encode(array(
                    'success' => true,
                    'message' => 'Invoice berhasil dibuat',
                    'invoice_id' => $invoice_data['invoice_id'],
                    'invoice_number' => $invoice_data['nomor_invoice']
                )));
            } else {
                $this->output->set_output(json_encode(array(
                    'success' => false,
                    'message' => 'Gagal membuat invoice'
                )));
            }
            
        } catch (Exception $e) {
            log_message('error', 'Error generating invoice: ' . $e->getMessage());
            $this->output->set_output(json_encode(array(
                'success' => false,
                'message' => 'Terjadi kesalahan sistem'
            )));
        }
    }

    // ==========================================
    // DATABASE BACKUP FUNCTIONS
    // ==========================================

    public function backup()
    {
        $data['fullwidth'] = true;
        $data['title'] = 'Cadangkan & Pulihkan Database';
        
        try {
            $data['database_info'] = $this->Admin_model->get_database_info();
            $data['backup_list'] = $this->Admin_model->get_backup_list();
            
            $this->Admin_model->log_activity(
                $this->session->userdata('user_id'),
                'Mengakses halaman backup database',
                'system',
                null
            );
            
        } catch (Exception $e) {
            log_message('error', 'Error loading database backup page: ' . $e->getMessage());
            $data['database_info'] = array();
            $data['backup_list'] = array();
        }
        
        $this->load->view('template/header', $data);
        $this->load->view('template/sidebar', $data);
        $this->_load_fullwidth_view('admin/database_backup', $data);
        $this->load->view('template/footer');
    }

    public function ajax_create_backup()
    {
        $this->output->set_content_type('application/json');
        
        if ($this->input->method() !== 'post') {
            $this->output->set_output(json_encode(array(
                'success' => false,
                'message' => 'Method not allowed'
            )));
            return;
        }
        
        try {
            $raw_input = $this->input->raw_input_stream;
            
            if (empty($raw_input)) {
                $this->output->set_output(json_encode(array(
                    'success' => false,
                    'message' => 'No input data received'
                )));
                return;
            }
            
            $input = json_decode($raw_input, true);
            
            if (json_last_error() !== JSON_ERROR_NONE) {
                $this->output->set_output(json_encode(array(
                    'success' => false,
                    'message' => 'Invalid JSON input: ' . json_last_error_msg()
                )));
                return;
            }
            
            $backup_options = array(
                'name' => !empty($input['name']) ? preg_replace('/[^a-zA-Z0-9_-]/', '', $input['name']) : 'backup_' . date('Y-m-d_H-i-s'),
                'include_structure' => isset($input['include_structure']) ? (bool)$input['include_structure'] : true,
                'include_data' => isset($input['include_data']) ? (bool)$input['include_data'] : true,
                'compress' => isset($input['compress']) ? (bool)$input['compress'] : false
            );
            
            if (!$backup_options['include_structure'] && !$backup_options['include_data']) {
                $this->output->set_output(json_encode(array(
                    'success' => false,
                    'message' => 'Pilih minimal struktur atau data untuk di-backup'
                )));
                return;
            }
            
            $result = $this->Admin_model->create_database_backup($backup_options);
            
            if ($result['success']) {
                $this->Admin_model->log_activity(
                    $this->session->userdata('user_id'),
                    'Database backup dibuat: ' . $backup_options['name'],
                    'system',
                    null
                );
                
                $this->output->set_output(json_encode(array(
                    'success' => true,
                    'message' => 'Backup berhasil dibuat',
                    'filename' => $result['filename'],
                    'file_size' => isset($result['file_size']) ? $result['file_size'] : 'Unknown'
                )));
            } else {
                $this->output->set_output(json_encode(array(
                    'success' => false,
                    'message' => $result['message']
                )));
            }
            
        } catch (Exception $e) {
            log_message('error', 'Error creating backup: ' . $e->getMessage());
            $this->output->set_output(json_encode(array(
                'success' => false,
                'message' => 'Terjadi kesalahan saat membuat backup: ' . $e->getMessage()
            )));
        }
    }

    public function ajax_restore_from_backup()
    {
        $this->output->set_content_type('application/json');
        
        if ($this->input->method() !== 'post') {
            $this->output->set_output(json_encode(array(
                'success' => false,
                'message' => 'Method not allowed'
            )));
            return;
        }
        
        try {
            $input = json_decode($this->input->raw_input_stream, true);
            $filename = $input['filename'];
            
            if (empty($filename)) {
                $this->output->set_output(json_encode(array(
                    'success' => false,
                    'message' => 'Nama file backup tidak valid'
                )));
                return;
            }
            
            $result = $this->Admin_model->restore_database_from_backup($filename);
            
            if ($result['success']) {
                $this->Admin_model->log_activity(
                    $this->session->userdata('user_id'),
                    'Database dipulihkan dari backup: ' . $filename,
                    'system',
                    null
                );
                
                $this->output->set_output(json_encode(array(
                    'success' => true,
                    'message' => 'Database berhasil dipulihkan'
                )));
            } else {
                $this->output->set_output(json_encode(array(
                    'success' => false,
                    'message' => $result['message']
                )));
            }
            
        } catch (Exception $e) {
            log_message('error', 'Error restoring from backup: ' . $e->getMessage());
            $this->output->set_output(json_encode(array(
                'success' => false,
                'message' => 'Terjadi kesalahan saat memulihkan database'
            )));
        }
    }

    public function ajax_clean_old_backups()
    {
        $this->output->set_content_type('application/json');
        
        if ($this->input->method() !== 'post') {
            $this->output->set_output(json_encode(array(
                'success' => false,
                'message' => 'Method not allowed'
            )));
            return;
        }
        
        try {
            $days = $this->input->post('days') ? (int)$this->input->post('days') : 30;
            
            $result = $this->Admin_model->clean_old_backups($days);
            
            if ($result['success']) {
                $this->Admin_model->log_activity(
                    $this->session->userdata('user_id'),
                    'Backup lama dibersihkan (> ' . $days . ' hari): ' . $result['deleted_count'] . ' file',
                    'system',
                    null
                );
                
                $this->output->set_output(json_encode(array(
                    'success' => true,
                    'message' => $result['deleted_count'] . ' file backup lama berhasil dihapus',
                    'deleted_count' => $result['deleted_count']
                )));
            } else {
                $this->output->set_output(json_encode(array(
                    'success' => false,
                    'message' => $result['message']
                )));
            }
            
        } catch (Exception $e) {
            log_message('error', 'Error cleaning old backups: ' . $e->getMessage());
            $this->output->set_output(json_encode(array(
                'success' => false,
                'message' => 'Terjadi kesalahan saat membersihkan backup lama'
            )));
        }
    }

    public function download_backup($filename)
    {
        try {
            if (empty($filename)) {
                show_404();
                return;
            }
            
            $file_path = $this->Admin_model->get_backup_file_path($filename);
            
            if (!$file_path || !file_exists($file_path)) {
                show_404();
                return;
            }
            
            $this->load->helper('download');
            
            $this->Admin_model->log_activity(
                $this->session->userdata('user_id'),
                'File backup didownload: ' . $filename,
                'system',
                null
            );
            
            force_download($filename, file_get_contents($file_path));
            
        } catch (Exception $e) {
            log_message('error', 'Error downloading backup: ' . $e->getMessage());
            show_error('Gagal mendownload file backup');
        }
    }

    public function ajax_get_database_info()
    {
        $this->output->set_content_type('application/json');
        
        try {
            if (!$this->session->userdata('logged_in') || $this->session->userdata('role') !== 'admin') {
                $this->output->set_output(json_encode(array(
                    'success' => false,
                    'message' => 'Unauthorized access'
                )));
                return;
            }
            
            $info = $this->Admin_model->get_database_info();
            
            $response = array(
                'success' => true,
                'info' => $info
            );
            
            $this->output->set_output(json_encode($response));
            
        } catch (Exception $e) {
            log_message('error', 'Error getting database info: ' . $e->getMessage());
            $this->output->set_output(json_encode(array(
                'success' => false,
                'message' => 'Gagal mengambil informasi database'
            )));
        }
    }

    public function ajax_get_backup_list()
    {
        $this->output->set_content_type('application/json');
        
        try {
            if (!$this->session->userdata('logged_in') || $this->session->userdata('role') !== 'admin') {
                $this->output->set_output(json_encode(array(
                    'success' => false,
                    'message' => 'Unauthorized access'
                )));
                return;
            }
            
            $backups = $this->Admin_model->get_backup_list();
            
            $response = array(
                'success' => true,
                'backups' => $backups
            );
            
            $this->output->set_output(json_encode($response));
            
        } catch (Exception $e) {
            log_message('error', 'Error getting backup list: ' . $e->getMessage());
            $this->output->set_output(json_encode(array(
                'success' => false,
                'message' => 'Gagal mengambil daftar backup'
            )));
        }
    }

    public function ajax_restore_backup()
    {
        $this->output->set_content_type('application/json');
        
        if ($this->input->method() !== 'post') {
            $this->output->set_output(json_encode(array(
                'success' => false,
                'message' => 'Method not allowed'
            )));
            return;
        }
        
        try {
            if (!$this->session->userdata('logged_in') || $this->session->userdata('role') !== 'admin') {
                $this->output->set_output(json_encode(array(
                    'success' => false,
                    'message' => 'Unauthorized access'
                )));
                return;
            }
            
            if (!isset($_FILES['backup_file']) || $_FILES['backup_file']['error'] !== UPLOAD_ERR_OK) {
                $error_messages = array(
                    UPLOAD_ERR_INI_SIZE => 'File terlalu besar (melebihi upload_max_filesize)',
                    UPLOAD_ERR_FORM_SIZE => 'File terlalu besar (melebihi MAX_FILE_SIZE)',
                    UPLOAD_ERR_PARTIAL => 'File hanya terupload sebagian',
                    UPLOAD_ERR_NO_FILE => 'Tidak ada file yang diupload',
                    UPLOAD_ERR_NO_TMP_DIR => 'Folder temporary tidak ditemukan',
                    UPLOAD_ERR_CANT_WRITE => 'Gagal menulis file ke disk',
                    UPLOAD_ERR_EXTENSION => 'Upload dihentikan oleh ekstensi PHP'
                );
                
                $error_code = $_FILES['backup_file']['error'] ?? UPLOAD_ERR_NO_FILE;
                $error_msg = $error_messages[$error_code] ?? 'Error upload tidak dikenal';
                
                $this->output->set_output(json_encode(array(
                    'success' => false,
                    'message' => 'File backup tidak berhasil diupload: ' . $error_msg
                )));
                return;
            }
            
            $allowed_extensions = array('sql', 'zip');
            $file_ext = strtolower(pathinfo($_FILES['backup_file']['name'], PATHINFO_EXTENSION));
            
            if (!in_array($file_ext, $allowed_extensions)) {
                $this->output->set_output(json_encode(array(
                    'success' => false,
                    'message' => 'Format file tidak didukung. Gunakan file .sql atau .zip'
                )));
                return;
            }
            
            $max_size = 100 * 1024 * 1024; // 100MB in bytes
            if ($_FILES['backup_file']['size'] > $max_size) {
                $this->output->set_output(json_encode(array(
                    'success' => false,
                    'message' => 'File terlalu besar. Maksimal 100MB'
                )));
                return;
            }
            
            $result = $this->Admin_model->restore_database_from_file($_FILES['backup_file']);
            
            if ($result['success']) {
                $this->Admin_model->log_activity(
                    $this->session->userdata('user_id'),
                    'Database dipulihkan dari file: ' . $_FILES['backup_file']['name'],
                    'system',
                    null
                );
                
                $this->output->set_output(json_encode(array(
                    'success' => true,
                    'message' => 'Database berhasil dipulihkan'
                )));
            } else {
                $this->output->set_output(json_encode(array(
                    'success' => false,
                    'message' => $result['message']
                )));
            }
            
        } catch (Exception $e) {
            log_message('error', 'Error restoring backup: ' . $e->getMessage());
            $this->output->set_output(json_encode(array(
                'success' => false,
                'message' => 'Terjadi kesalahan saat memulihkan database: ' . $e->getMessage()
            )));
        }
    }

    public function ajax_delete_backup()
    {
        $this->output->set_content_type('application/json');
        
        if ($this->input->method() !== 'post') {
            $this->output->set_output(json_encode(array(
                'success' => false,
                'message' => 'Method not allowed'
            )));
            return;
        }
        
        try {
            if (!$this->session->userdata('logged_in') || $this->session->userdata('role') !== 'admin') {
                $this->output->set_output(json_encode(array(
                    'success' => false,
                    'message' => 'Unauthorized access'
                )));
                return;
            }
            
            $raw_input = $this->input->raw_input_stream;
            $input = json_decode($raw_input, true);
            
            if (json_last_error() !== JSON_ERROR_NONE || empty($input['filename'])) {
                $this->output->set_output(json_encode(array(
                    'success' => false,
                    'message' => 'Nama file backup tidak valid'
                )));
                return;
            }
            
            $filename = basename($input['filename']);
            
            $allowed_extensions = array('sql', 'zip');
            $file_ext = strtolower(pathinfo($filename, PATHINFO_EXTENSION));
            
            if (!in_array($file_ext, $allowed_extensions)) {
                $this->output->set_output(json_encode(array(
                    'success' => false,
                    'message' => 'Format file tidak valid'
                )));
                return;
            }
            
            $result = $this->Admin_model->delete_backup_file($filename);
            
            if ($result) {
                $this->Admin_model->log_activity(
                    $this->session->userdata('user_id'),
                    'File backup dihapus: ' . $filename,
                    'system',
                    null
                );
                
                $this->output->set_output(json_encode(array(
                    'success' => true,
                    'message' => 'File backup berhasil dihapus'
                )));
            } else {
                $this->output->set_output(json_encode(array(
                    'success' => false,
                    'message' => 'Gagal menghapus file backup'
                )));
            }
            
        } catch (Exception $e) {
            log_message('error', 'Error deleting backup: ' . $e->getMessage());
            $this->output->set_output(json_encode(array(
                'success' => false,
                'message' => 'Terjadi kesalahan saat menghapus backup'
            )));
        }
    }

    public function test_backup_connection()
    {
        $this->output->set_content_type('application/json');
        
        try {
            $this->db->query('SELECT 1');
            
            $this->output->set_output(json_encode(array(
                'success' => true,
                'message' => 'Database connection OK',
                'server_info' => array(
                    'php_version' => PHP_VERSION,
                    'ci_version' => CI_VERSION,
                    'memory_limit' => ini_get('memory_limit'),
                    'max_execution_time' => ini_get('max_execution_time'),
                    'upload_max_filesize' => ini_get('upload_max_filesize'),
                    'post_max_size' => ini_get('post_max_size')
                )
            )));
            
        } catch (Exception $e) {
            $this->output->set_output(json_encode(array(
                'success' => false,
                'message' => 'Database connection failed: ' . $e->getMessage()
            )));
        }
    }

    // ==========================================
    // HELPER METHODS
    // ==========================================

    private function _load_fullwidth_view($view, $data = array())
    {
        if (!empty($data) && isset($data['fullwidth']) && $data['fullwidth'] === true) {
            $this->load->view($view, $data);
        } else {
            $this->load->view('template/header', $data);
            $this->load->view('template/sidebar', $data);
            $this->load->view($view, $data);
            $this->load->view('template/footer');
        }
    }

    private function _load_conditional_view($view, $data = array())
    {
        $is_fullwidth = $this->input->get('fullwidth') === '1' || 
                       (isset($data['fullwidth']) && $data['fullwidth'] === true);
        
        if ($is_fullwidth) {
            $this->load->view($view, $data);
        } else {
            $this->load->view('template/header', $data);
            $this->load->view('template/sidebar', $data);
            $this->load->view($view, $data);
            $this->load->view('template/footer');
        }
    }

    public function toggle_fullwidth()
    {
        $current_view = $this->input->post('current_view');
        $is_fullwidth = $this->session->userdata('fullwidth_mode') ? false : true;
        
        $this->session->set_userdata('fullwidth_mode', $is_fullwidth);
        
        redirect($current_view);
    }

    private function _is_fullwidth_mode()
    {
        return $this->session->userdata('fullwidth_mode') === true;
    }

    private function _get_default_admin_stats()
    {
        return array(
            'today' => array(
                'new_patients' => 0,
                'examinations' => 0,
                'completed_tests' => 0,
                'revenue' => 0
            ),
            'this_month' => array(
                'new_patients' => 0,
                'examinations' => 0,
                'total_revenue' => 0
            ),
            'equipment' => array()
        );
    }

    private function _get_default_user_stats()
    {
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
    public function dashboard()
{
    $data['title'] = 'Admin Dashboard';
    
    // Check if fullwidth mode enabled
    if ($this->_is_fullwidth_mode()) {
        $data['fullwidth'] = true;
    }
    
    try {
        // Get dashboard data menggunakan function model yang sudah ada
        $dashboard_data = $this->Admin_model->get_dashboard_data();
        
        if ($dashboard_data !== false) {
            $data['dashboard_data'] = $dashboard_data;
            $data['has_data'] = true;
        } else {
            $data['dashboard_data'] = $this->_get_default_dashboard_data();
            $data['has_data'] = false;
        }
        
        $this->Admin_model->log_activity(
            $this->session->userdata('user_id'),
            'Mengakses dashboard',
            'system',
            null
        );
        
    } catch (Exception $e) {
        log_message('error', 'Error loading admin dashboard: ' . $e->getMessage());
        $data['dashboard_data'] = $this->_get_default_dashboard_data();
        $data['has_data'] = false;
        $data['error_message'] = 'Gagal memuat data dashboard';
    }
    
    // Load view dengan conditional fullwidth
    $this->_load_conditional_view('admin/index', $data);
}

public function ajax_get_dashboard_data()
{
    $this->output->set_content_type('application/json');
    
    try {
        // Validasi request method
        if ($this->input->method() !== 'get') {
            $this->output->set_output(json_encode(array(
                'success' => false,
                'message' => 'Method not allowed'
            )));
            return;
        }
        
        // Validasi session
        if (!$this->session->userdata('logged_in') || $this->session->userdata('role') !== 'admin') {
            $this->output->set_output(json_encode(array(
                'success' => false,
                'message' => 'Unauthorized access'
            )));
            return;
        }
        
        // Get dashboard data
        $dashboard_data = $this->Admin_model->get_dashboard_data();
        
        if ($dashboard_data !== false) {
            $this->output->set_output(json_encode(array(
                'success' => true,
                'data' => $dashboard_data,
                'timestamp' => date('Y-m-d H:i:s')
            )));
        } else {
            $this->output->set_output(json_encode(array(
                'success' => false,
                'message' => 'Gagal mengambil data dashboard',
                'data' => $this->_get_default_dashboard_data()
            )));
        }
        
    } catch (Exception $e) {
        log_message('error', 'Error getting dashboard data via AJAX: ' . $e->getMessage());
        $this->output->set_output(json_encode(array(
            'success' => false,
            'message' => 'Terjadi kesalahan sistem',
            'data' => $this->_get_default_dashboard_data()
        )));
    }
}

public function ajax_get_kpi_data()
{
    $this->output->set_content_type('application/json');
    
    try {
        if (!$this->session->userdata('logged_in') || $this->session->userdata('role') !== 'admin') {
            $this->output->set_output(json_encode(array(
                'success' => false,
                'message' => 'Unauthorized access'
            )));
            return;
        }
        
        $kpi_data = $this->Admin_model->get_dashboard_kpi();
        
        $this->output->set_output(json_encode(array(
            'success' => true,
            'data' => $kpi_data
        )));
        
    } catch (Exception $e) {
        log_message('error', 'Error getting KPI data: ' . $e->getMessage());
        $this->output->set_output(json_encode(array(
            'success' => false,
            'message' => 'Gagal mengambil data KPI'
        )));
    }
}

public function ajax_get_examination_trend()
{
    $this->output->set_content_type('application/json');
    
    try {
        if (!$this->session->userdata('logged_in') || $this->session->userdata('role') !== 'admin') {
            $this->output->set_output(json_encode(array(
                'success' => false,
                'message' => 'Unauthorized access'
            )));
            return;
        }
        
        $days = $this->input->get('days') ? (int)$this->input->get('days') : 30;
        $trend_data = $this->Admin_model->get_examination_trend($days);
        
        $this->output->set_output(json_encode(array(
            'success' => true,
            'data' => $trend_data
        )));
        
    } catch (Exception $e) {
        log_message('error', 'Error getting examination trend: ' . $e->getMessage());
        $this->output->set_output(json_encode(array(
            'success' => false,
            'message' => 'Gagal mengambil data trend pemeriksaan'
        )));
    }
}

public function ajax_refresh_component()
{
    $this->output->set_content_type('application/json');
    
    try {
        if (!$this->session->userdata('logged_in') || $this->session->userdata('role') !== 'admin') {
            $this->output->set_output(json_encode(array(
                'success' => false,
                'message' => 'Unauthorized access'
            )));
            return;
        }
        
        $component = $this->input->post('component');
        $result = array('success' => false, 'data' => null);
        
        switch ($component) {
            case 'kpi':
                $result['data'] = $this->Admin_model->get_dashboard_kpi();
                $result['success'] = true;
                break;
                
            case 'examination_trend':
                $days = $this->input->post('days') ? (int)$this->input->post('days') : 30;
                $result['data'] = $this->Admin_model->get_examination_trend($days);
                $result['success'] = true;
                break;
                
            case 'user_distribution':
                $result['data'] = $this->Admin_model->get_user_distribution();
                $result['success'] = true;
                break;
                
            case 'pending_examinations':
                $limit = $this->input->post('limit') ? (int)$this->input->post('limit') : 10;
                $result['data'] = $this->Admin_model->get_pending_examinations($limit);
                $result['success'] = true;
                break;
                
            case 'recent_patients':
                $limit = $this->input->post('limit') ? (int)$this->input->post('limit') : 5;
                $result['data'] = $this->Admin_model->get_recent_patients($limit);
                $result['success'] = true;
                break;
                
            case 'inventory_alerts':
                $limit = $this->input->post('limit') ? (int)$this->input->post('limit') : 10;
                $result['data'] = $this->Admin_model->get_inventory_alerts($limit);
                $result['success'] = true;
                break;
                
            case 'recent_activities':
                $limit = $this->input->post('limit') ? (int)$this->input->post('limit') : 10;
                $result['data'] = $this->Admin_model->get_recent_activities($limit);
                $result['success'] = true;
                break;
                
            case 'system_status':
                $result['data'] = $this->Admin_model->get_system_status();
                $result['success'] = true;
                break;
                
            default:
                $result['message'] = 'Component tidak dikenal';
                break;
        }
        
        $this->output->set_output(json_encode($result));
        
    } catch (Exception $e) {
        log_message('error', 'Error refreshing component: ' . $e->getMessage());
        $this->output->set_output(json_encode(array(
            'success' => false,
            'message' => 'Gagal memperbarui komponen'
        )));
    }
}

public function ajax_get_system_health()
{
    $this->output->set_content_type('application/json');
    
    try {
        if (!$this->session->userdata('logged_in') || $this->session->userdata('role') !== 'admin') {
            $this->output->set_output(json_encode(array(
                'success' => false,
                'message' => 'Unauthorized access'
            )));
            return;
        }
        
        $system_health = $this->Admin_model->get_system_health();
        $system_status = $this->Admin_model->get_system_status();
        
        $combined_data = array(
            'health' => $system_health,
            'status' => $system_status
        );
        
        $this->output->set_output(json_encode(array(
            'success' => true,
            'data' => $combined_data
        )));
        
    } catch (Exception $e) {
        log_message('error', 'Error getting system health: ' . $e->getMessage());
        $this->output->set_output(json_encode(array(
            'success' => false,
            'message' => 'Gagal mengambil status sistem'
        )));
    }
}

// ==========================================
// HELPER METHODS
// ==========================================

private function _get_default_dashboard_data()
{
    return array(
        'kpi' => array(
            'total_examinations' => 0,
            'completed_today' => 0,
            'pending_today' => 0,
            'monthly_revenue' => 0,
            'active_users' => 0,
            'alert_items' => 0
        ),
        'examination_trend' => array(),
        'user_distribution' => array(),
        'pending_examinations' => array(),
        'recent_patients' => array(),
        'inventory_alerts' => array(),
        'recent_activities' => array(),
        'system_status' => array(
            'database' => array('status' => 'unknown', 'message' => 'Check failed'),
            'storage' => array('status' => 'unknown', 'message' => 'Check failed'),
            'backup' => array('status' => 'unknown', 'message' => 'Check failed')
        )
    );
}

private function _format_dashboard_data($data)
{
    // Format currency values
    if (isset($data['kpi']['monthly_revenue'])) {
        $data['kpi']['monthly_revenue_formatted'] = $this->_format_currency($data['kpi']['monthly_revenue']);
    }
    
    // Format dates in examination trend
    if (isset($data['examination_trend']) && is_array($data['examination_trend'])) {
        foreach ($data['examination_trend'] as &$trend) {
            if (isset($trend['exam_date'])) {
                $trend['exam_date_formatted'] = date('d M', strtotime($trend['exam_date']));
            }
        }
    }
    
    // Add time ago to recent activities
    if (isset($data['recent_activities']) && is_array($data['recent_activities'])) {
        foreach ($data['recent_activities'] as &$activity) {
            if (isset($activity['created_at'])) {
                $activity['time_ago'] = $this->_time_ago($activity['created_at']);
            }
        }
    }
    
    return $data;
}

private function _format_currency($amount)
{
    return 'Rp ' . number_format($amount, 0, ',', '.');
}

private function _time_ago($datetime)
{
    $time = time() - strtotime($datetime);
    
    if ($time < 60) return 'baru saja';
    if ($time < 3600) return floor($time/60) . ' menit lalu';
    if ($time < 86400) return floor($time/3600) . ' jam lalu';
    if ($time < 2629746) return floor($time/86400) . ' hari lalu';
    
    return date('d M Y', strtotime($datetime));
}
}