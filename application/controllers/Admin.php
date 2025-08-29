<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Admin extends CI_Controller {

    public function __construct()
    {
        parent::__construct();
        
        // Check if user is logged in and has admin role
        if (!$this->session->userdata('logged_in') || $this->session->userdata('role') !== 'admin') {
            $this->session->set_flashdata('error', 'Akses ditolak. Anda tidak memiliki izin untuk mengakses halaman ini.');
            redirect('auth/login');
        }
        
        $this->load->model(['User_model', 'Admin_model', 'Administrasi_model']);
        $this->load->library(['form_validation', 'upload']);
        $this->load->helper(['form', 'url', 'date']);
    }

    /**
     * Admin Dashboard
     */
    public function dashboard()
{
    $data['title'] = 'Admin Dashboard';
    
    try {
        // Get REAL operational statistics
        $data['stats'] = $this->Admin_model->get_operational_stats();
        
        // Get system health
        $data['system_health'] = $this->Admin_model->get_system_health();
        
        // Get master data statistics
        $data['master_stats'] = $this->Admin_model->get_master_data_stats();
        
        // Get recent activities
        $data['recent_activities'] = $this->Admin_model->count_all_activities();
        
        // Get recent examinations
        $data['recent_examinations'] = $this->Admin_model->get_recent_examinations(10);
        
        // ADDED: Get lab-specific stats
        $data['lab_stats'] = $this->_get_lab_dashboard_stats();
        
    } catch (Exception $e) {
        log_message('error', 'Error loading admin dashboard: ' . $e->getMessage());
        $data['stats'] = $this->_get_default_admin_stats();
        $data['system_health'] = array();
        $data['master_stats'] = array();
        $data['recent_activities'] = 0;
        $data['recent_examinations'] = array();
        $data['lab_stats'] = array();
    }
    
    $this->load->view('template/header', $data);
    $this->load->view('template/sidebar', $data);
    $this->load->view('admin/index', $data); // This needs to be updated
    $this->load->view('template/footer');
}
    /**
     * User Management - Add User
     */
    public function users()
    {
        $data['title'] = 'Tambah Pengguna';
        
        if ($this->input->method() === 'post') {
            $this->_handle_add_user();
        }
        
        $this->load->view('template/header', $data);
        $this->load->view('template/sidebar', $data);
        $this->load->view('admin/add_user', $data);
        $this->load->view('template/footer');
    }

    /**
     * User Management - Edit User
     */
    public function edit_user()
    {
        $data['title'] = 'Kelola Pengguna';
        
        // Get all users
        try {
            $data['users'] = $this->User_model->get_all_users();
        } catch (Exception $e) {
            log_message('error', 'Error getting users: ' . $e->getMessage());
            $data['users'] = array();
        }
        
        $this->load->view('template/header', $data);
        $this->load->view('template/sidebar', $data);
        $this->load->view('admin/edit_user', $data);
        $this->load->view('template/footer');
    }

    /**
     * Edit specific user
     */
    public function edit_user_detail($user_id)
    {
        $user = $this->User_model->get_user_by_id($user_id);
        
        if (!$user) {
            $this->session->set_flashdata('error', 'Pengguna tidak ditemukan');
            redirect('admin/edit_user');
        }
        
        $data['title'] = 'Edit Pengguna: ' . $user['username'];
        $data['user'] = $user;
        $data['user_details'] = $this->User_model->get_user_details($user);
        
        if ($this->input->method() === 'post') {
            $this->_handle_edit_user($user_id);
        }
        
        $this->load->view('template/header', $data);
        $this->load->view('template/sidebar', $data);
        $this->load->view('admin/edit_user_form', $data);
        $this->load->view('template/footer');
    }

    /**
     * Delete user
     */
    public function delete_user($user_id)
    {
        // Prevent admin from deleting themselves
        if ($user_id == $this->session->userdata('user_id')) {
            $this->session->set_flashdata('error', 'Anda tidak dapat menghapus akun Anda sendiri');
            redirect('admin/edit_user');
        }
        
        if ($this->User_model->delete_user($user_id)) {
            $this->User_model->log_activity($this->session->userdata('user_id'), 'User deleted', 'users', $user_id);
            $this->session->set_flashdata('success', 'Pengguna berhasil dihapus');
        } else {
            $this->session->set_flashdata('error', 'Gagal menghapus pengguna');
        }
        
        redirect('admin/edit_user');
    }

    /**
     * Toggle user status
     */
    private function _get_lab_dashboard_stats()
{
    $stats = array();
    
    // Pending lab requests
    $stats['pending_requests'] = $this->db->where('status_pemeriksaan', 'pending')->count_all_results('pemeriksaan_lab');
    
    // Samples in progress
    $stats['in_progress'] = $this->db->where('status_pemeriksaan', 'progress')->count_all_results('pemeriksaan_lab');
    
    // Completed today
    $stats['completed_today'] = $this->db->where('status_pemeriksaan', 'selesai')
                                         ->where('DATE(updated_at)', date('Y-m-d'))
                                         ->count_all_results('pemeriksaan_lab');
    
    // Active lab technicians
    $stats['active_technicians'] = $this->db->where('is_active', 1)
                                            ->where('role', 'petugas_lab')
                                            ->count_all_results('users');
    
    // Low stock alerts
    $stats['low_stock_alerts'] = $this->db->where('jumlah_stok <=', 'stok_minimal', FALSE)
                                          ->count_all_results('reagen');
    
    // Equipment needing maintenance
    $stats['maintenance_due'] = $this->db->where('jadwal_kalibrasi <=', date('Y-m-d'))
                                         ->count_all_results('alat_laboratorium');
    
    // Revenue this month
    $this_month = date('Y-m');
    $this->db->select('SUM(total_biaya) as total');
    $this->db->where("DATE_FORMAT(tanggal_pembayaran, '%Y-%m') = ", $this_month, FALSE);
    $this->db->where('status_pembayaran', 'lunas');
    $result = $this->db->get('invoice')->row_array();
    $stats['monthly_revenue'] = $result['total'] ? (float)$result['total'] : 0;
    
    return $stats;
}

    public function toggle_user_status($user_id)
    {
        $user = $this->User_model->get_user_by_id($user_id);
        
        if (!$user) {
            $this->session->set_flashdata('error', 'Pengguna tidak ditemukan');
            redirect('admin/edit_user');
        }
        
        // Prevent admin from deactivating themselves
        if ($user_id == $this->session->userdata('user_id')) {
            $this->session->set_flashdata('error', 'Anda tidak dapat menonaktifkan akun Anda sendiri');
            redirect('admin/edit_user');
        }
        
        $new_status = $user['is_active'] ? 0 : 1;
        $status_text = $new_status ? 'diaktifkan' : 'dinonaktifkan';
        
        if ($this->User_model->update_user($user_id, array('is_active' => $new_status))) {
            $this->User_model->log_activity($this->session->userdata('user_id'), "User {$status_text}", 'users', $user_id);
            $this->session->set_flashdata('success', "Pengguna berhasil {$status_text}");
        } else {
            $this->session->set_flashdata('error', "Gagal {$status_text} pengguna");
        }
        
        redirect('admin/edit_user');
    }

    /**
     * Backup & Restore Database
     */
    public function backup()
    {
        $data['title'] = 'Backup & Restore Database';
        
        // Get existing backup files
        try {
            $data['backup_files'] = $this->Admin_model->get_backup_files();
        } catch (Exception $e) {
            log_message('error', 'Error getting backup files: ' . $e->getMessage());
            $data['backup_files'] = array();
        }
        
        $this->load->view('template/header', $data);
        $this->load->view('template/sidebar', $data);
        $this->load->view('admin/backup', $data);
        $this->load->view('template/footer');
    }

    /**
     * Create database backup
     */
    public function create_backup()
    {
        $this->load->dbutil();
        
        // Backup configuration
        $backup_config = array(
            'format' => 'sql',
            'filename' => 'backup_' . date('Y-m-d_H-i-s') . '.sql'
        );
        
        // Create backup
        $backup = $this->dbutil->backup($backup_config);
        
        // Save to file
        $backup_path = APPPATH . 'backups/';
        if (!is_dir($backup_path)) {
            mkdir($backup_path, 0755, true);
        }
        
        $filename = $backup_config['filename'];
        $filepath = $backup_path . $filename;
        
        if (write_file($filepath, $backup)) {
            $this->User_model->log_activity($this->session->userdata('user_id'), 'Database backup created', 'system');
            $this->session->set_flashdata('success', 'Backup database berhasil dibuat: ' . $filename);
        } else {
            $this->session->set_flashdata('error', 'Gagal membuat backup database');
        }
        
        redirect('admin/backup');
    }

    /**
     * Operational Data Management
     */
    public function operational_data()
    {
        $data['title'] = 'Data Operasional';
        
        // Get operational statistics
        try {
            $data['stats'] = $this->Admin_model->get_operational_stats();
            $data['examination_stats'] = $this->Admin_model->get_examination_stats();
        } catch (Exception $e) {
            log_message('error', 'Error getting operational data: ' . $e->getMessage());
            $data['stats'] = array();
            $data['examination_stats'] = array();
        }
        
        $this->load->view('template/header', $data);
        $this->load->view('template/sidebar', $data);
        $this->load->view('admin/operational_data', $data);
        $this->load->view('template/footer');
    }
    public function get_monthly_examination_trends() 
{
    $trends = array();
    
    for ($i = 5; $i >= 0; $i--) {
        $month = date('Y-m', strtotime("-{$i} months"));
        $month_name = date('M', strtotime($month . '-01'));
        
        // Get examination counts by status for each month
        $this->db->select('status_pemeriksaan, COUNT(*) as count');
        $this->db->where("DATE_FORMAT(tanggal_pemeriksaan, '%Y-%m') = ", $month, FALSE);
        $this->db->group_by('status_pemeriksaan');
        $query = $this->db->get('pemeriksaan_lab');
        
        $month_data = array('month' => $month_name);
        foreach ($query->result_array() as $row) {
            $month_data[$row['status_pemeriksaan']] = $row['count'];
        }
        
        // Set defaults for missing statuses
        $month_data['pending'] = isset($month_data['pending']) ? $month_data['pending'] : 0;
        $month_data['progress'] = isset($month_data['progress']) ? $month_data['progress'] : 0;
        $month_data['selesai'] = isset($month_data['selesai']) ? $month_data['selesai'] : 0;
        
        $trends[] = $month_data;
    }
    
    return $trends;
}
public function get_inventory_dashboard_status()
{
    $status = array();
    
    // Reagent status
    $this->db->select('status, COUNT(*) as count');
    $this->db->group_by('status');
    $reagent_query = $this->db->get('reagen');
    foreach ($reagent_query->result_array() as $row) {
        $status['reagents'][$row['status']] = $row['count'];
    }
    
    // Equipment status
    $this->db->select('status_alat, COUNT(*) as count');
    $this->db->group_by('status_alat');
    $equipment_query = $this->db->get('alat_laboratorium');
    foreach ($equipment_query->result_array() as $row) {
        $status['equipment'][$row['status_alat']] = $row['count'];
    }
    
    return $status;
}
public function get_real_dashboard_data()
{
    return array(
        'cards' => array(
            'pending_requests' => $this->db->where('status_pemeriksaan', 'pending')->count_all_results('pemeriksaan_lab'),
            'in_progress' => $this->db->where('status_pemeriksaan', 'progress')->count_all_results('pemeriksaan_lab'),
            'completed_today' => $this->db->where('status_pemeriksaan', 'selesai')->where('DATE(updated_at)', date('Y-m-d'))->count_all_results('pemeriksaan_lab'),
            'active_technicians' => $this->db->where('role', 'petugas_lab')->where('is_active', 1)->count_all_results('users')
        ),
        'alerts' => array(
            'low_stock' => $this->db->where('jumlah_stok <=', 'stok_minimal', FALSE)->count_all_results('reagen'),
            'maintenance_due' => $this->db->where('jadwal_kalibrasi <=', date('Y-m-d'))->count_all_results('alat_laboratorium')
        ),
        'trends' => $this->get_monthly_examination_trends()
    );
}

    /**
     * Master Data Management
     */
    public function master_data()
    {
        $data['title'] = 'Master Data';
        
        try {
            $data['stats'] = $this->Admin_model->get_master_data_stats();
        } catch (Exception $e) {
            log_message('error', 'Error getting master data: ' . $e->getMessage());
            $data['stats'] = array();
        }
        
        $this->load->view('template/header', $data);
        $this->load->view('template/sidebar', $data);
        $this->load->view('admin/master_data', $data);
        $this->load->view('template/footer');
    }

    /**
     * Activity Reports
     */
    public function activity_reports()
    {
        $data['title'] = 'Laporan Aktivitas';
        
        // Get date range from input
        $date_from = $this->input->get('date_from');
        $date_to = $this->input->get('date_to');
        
        if (!$date_from) $date_from = date('Y-m-01');
        if (!$date_to) $date_to = date('Y-m-d');
        
        $data['date_from'] = $date_from;
        $data['date_to'] = $date_to;
        
        // Get activity logs
        try {
            $this->db->select('al.*, u.username, u.role');
            $this->db->from('activity_log al');
            $this->db->join('users u', 'al.user_id = u.user_id');
            $this->db->where('DATE(al.created_at) >=', $date_from);
            $this->db->where('DATE(al.created_at) <=', $date_to);
            $this->db->order_by('al.created_at', 'DESC');
            $this->db->limit(100);
            
            $data['activities'] = $this->db->get()->result_array();
        } catch (Exception $e) {
            log_message('error', 'Error getting activity reports: ' . $e->getMessage());
            $data['activities'] = array();
        }
        
        $this->load->view('template/header', $data);
        $this->load->view('template/sidebar', $data);
        $this->load->view('admin/activity_reports', $data);
        $this->load->view('template/footer');
    }

    /**
     * Examination Reports
     */
    public function examination_reports()
    {
        $data['title'] = 'Laporan Pemeriksaan';
        
        try {
            $data['exam_stats'] = $this->Admin_model->get_examination_stats();
            $data['recent_exams'] = $this->Admin_model->get_recent_examinations(20);
        } catch (Exception $e) {
            log_message('error', 'Error getting examination reports: ' . $e->getMessage());
            $data['exam_stats'] = array();
            $data['recent_exams'] = array();
        }
        
        $this->load->view('template/header', $data);
        $this->load->view('template/sidebar', $data);
        $this->load->view('admin/examination_reports', $data);
        $this->load->view('template/footer');
    }

    /**
     * Financial Reports
     */
    public function financial_reports()
    {
        $data['title'] = 'Laporan Keuangan';
        
        try {
            $data['financial_stats'] = $this->Admin_model->get_financial_stats();
            $data['revenue_chart'] = $this->Admin_model->get_revenue_chart_data();
        } catch (Exception $e) {
            log_message('error', 'Error getting financial reports: ' . $e->getMessage());
            $data['financial_stats'] = array();
            $data['revenue_chart'] = array();
        }
        
        $this->load->view('template/header', $data);
        $this->load->view('template/sidebar', $data);
        $this->load->view('admin/financial_reports', $data);
        $this->load->view('template/footer');
    }

    // =============================
    // PRIVATE METHODS
    // =============================

    private function _handle_add_user()
    {
        $this->form_validation->set_rules('username', 'Username', 'required|is_unique[users.username]|alpha_dash|min_length[3]');
        $this->form_validation->set_rules('password', 'Password', 'required|min_length[6]');
        $this->form_validation->set_rules('role', 'Role', 'required|in_list[admin,administrasi,petugas_lab]');
        $this->form_validation->set_rules('nama_lengkap', 'Nama Lengkap', 'required|min_length[2]');
        
        if ($this->form_validation->run() === TRUE) {
            $role = $this->input->post('role');
            
            // User data
            $user_data = array(
                'username' => $this->input->post('username'),
                'password' => md5($this->input->post('password')),
                'role' => $role,
                'is_active' => 1,
                'created_at' => date('Y-m-d H:i:s')
            );
            
            // Role-specific data
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
                    $role_data['telepon'] = $this->input->post('telepon');
                    $role_data['alamat'] = $this->input->post('alamat');
                    break;
            }
            
            $user_id = $this->User_model->create_user($user_data, $role_data);
            
            if ($user_id) {
                $this->User_model->log_activity($this->session->userdata('user_id'), 'New user created', 'users', $user_id);
                $this->session->set_flashdata('success', 'Pengguna berhasil ditambahkan');
                redirect('admin/users');
            } else {
                $this->session->set_flashdata('error', 'Gagal menambahkan pengguna');
            }
        }
    }

    private function _handle_edit_user($user_id)
    {
        $this->form_validation->set_rules('username', 'Username', 'required|alpha_dash|min_length[3]|callback_check_username_unique[' . $user_id . ']');
        $this->form_validation->set_rules('nama_lengkap', 'Nama Lengkap', 'required|min_length[2]');
        
        if ($this->input->post('password')) {
            $this->form_validation->set_rules('password', 'Password', 'min_length[6]');
        }
        
        if ($this->form_validation->run() === TRUE) {
            $user = $this->User_model->get_user_by_id($user_id);
            
            // User data
            $user_data = array(
                'username' => $this->input->post('username'),
                'updated_at' => date('Y-m-d H:i:s')
            );
            
            if ($this->input->post('password')) {
                $user_data['password'] = md5($this->input->post('password'));
            }
            
            // Role-specific data
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
            }
            
            if ($this->User_model->update_user($user_id, $user_data, $role_data)) {
                $this->User_model->log_activity($this->session->userdata('user_id'), 'User updated', 'users', $user_id);
                $this->session->set_flashdata('success', 'Pengguna berhasil diperbarui');
                redirect('admin/edit_user');
            } else {
                $this->session->set_flashdata('error', 'Gagal memperbarui pengguna');
            }
        }
    }

    public function check_username_unique($username, $user_id)
    {
        if ($this->User_model->check_username_exists($username, $user_id)) {
            $this->form_validation->set_message('check_username_unique', 'Username sudah digunakan');
            return FALSE;
        }
        return TRUE;
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
}