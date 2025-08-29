<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Dashboard extends CI_Controller {

    public function __construct()
    {
        parent::__construct();
        
        // Check if user is logged in
        if (!$this->session->userdata('user_id') || !$this->session->userdata('logged_in')) {
            redirect('auth/login');
        }
        
        $this->load->model(['User_model', 'Admin_model', 'Administrasi_model']);
        $this->load->helper(['url', 'date']);
    }

    public function index()
    {
        // Redirect to role-specific dashboard
        $role = $this->session->userdata('role');
        
        switch($role) {
            case 'admin':
                redirect('admin/dashboard');
                break;
            case 'administrasi':
                redirect('administrasi/dashboard');
                break;
            case 'petugas_lab':
                redirect('laboratorium/dashboard');
                break;
            default:
                $this->session->set_flashdata('error', 'Role tidak dikenali');
                redirect('auth/logout');
                break;
        }
    }

    /**
     * Get current user info for any dashboard
     */
    public function get_current_user_info()
    {
        return array(
            'user_id' => $this->session->userdata('user_id'),
            'username' => $this->session->userdata('username'),
            'nama_lengkap' => $this->session->userdata('nama_lengkap'),
            'role' => $this->session->userdata('role'),
            'telepon' => $this->session->userdata('telepon'),
            'jenis_keahlian' => $this->session->userdata('jenis_keahlian')
        );
    }

    /**
     * Get common statistics for all roles
     */
    public function get_common_stats()
    {
        $stats = array();
        
        try {
            // Basic counts
            $stats['total_patients'] = $this->db->count_all('pasien');
            $stats['total_examinations'] = $this->db->count_all('pemeriksaan_lab');
            $stats['pending_examinations'] = $this->db->where('status_pemeriksaan', 'pending')->count_all_results('pemeriksaan_lab');
            $stats['completed_examinations'] = $this->db->where('status_pemeriksaan', 'selesai')->count_all_results('pemeriksaan_lab');
            
            // Today's stats
            $today = date('Y-m-d');
            $stats['today_patients'] = $this->db->where('DATE(created_at)', $today)->count_all_results('pasien');
            $stats['today_examinations'] = $this->db->where('DATE(tanggal_pemeriksaan)', $today)->count_all_results('pemeriksaan_lab');
            
            // This month's stats
            $this_month = date('Y-m');
            $stats['month_patients'] = $this->db->where('DATE_FORMAT(created_at, "%Y-%m")', $this_month)->count_all_results('pasien');
            $stats['month_examinations'] = $this->db->where('DATE_FORMAT(tanggal_pemeriksaan, "%Y-%m")', $this_month)->count_all_results('pemeriksaan_lab');
            
        } catch (Exception $e) {
            log_message('error', 'Error getting common stats: ' . $e->getMessage());
            $stats = $this->_get_default_stats();
        }
        
        return $stats;
    }

    /**
     * Get recent activities for all roles
     */
    public function get_recent_activities($limit = 10)
    {
        try {
            $this->db->select('al.*, u.username, u.role');
            $this->db->from('activity_log al');
            $this->db->join('users u', 'al.user_id = u.user_id');
            $this->db->order_by('al.created_at', 'DESC');
            $this->db->limit($limit);
            
            $query = $this->db->get();
            return $query->result_array();
        } catch (Exception $e) {
            log_message('error', 'Error getting recent activities: ' . $e->getMessage());
            return array();
        }
    }

    /**
     * Get system alerts for all roles
     */
    public function get_system_alerts()
    {
        $alerts = array();
        
        try {
            // Low stock alerts (for lab staff)
            $low_stock = $this->db->where('jumlah_stok <=', 'stok_minimal', FALSE)->count_all_results('reagen');
            if ($low_stock > 0) {
                $alerts[] = array(
                    'type' => 'warning',
                    'title' => 'Stok Reagen Rendah',
                    'message' => "{$low_stock} reagen memiliki stok rendah",
                    'icon' => 'package',
                    'url' => base_url('laboratorium/inventory_list')
                );
            }
            
            // Equipment maintenance alerts
            $maintenance_due = $this->db->where('jadwal_kalibrasi <=', date('Y-m-d'))->count_all_results('alat_laboratorium');
            if ($maintenance_due > 0) {
                $alerts[] = array(
                    'type' => 'info',
                    'title' => 'Jadwal Kalibrasi',
                    'message' => "{$maintenance_due} alat perlu dikalibrasi",
                    'icon' => 'settings',
                    'url' => base_url('laboratorium/equipment_list')
                );
            }
            
            // Pending payments (for admin staff)
            $pending_payments = $this->db->where('status_pembayaran', 'belum_bayar')->count_all_results('invoice');
            if ($pending_payments > 0) {
                $alerts[] = array(
                    'type' => 'warning',
                    'title' => 'Pembayaran Tertunda',
                    'message' => "{$pending_payments} pembayaran belum diselesaikan",
                    'icon' => 'credit-card',
                    'url' => base_url('administrasi/pending_payments')
                );
            }
            
        } catch (Exception $e) {
            log_message('error', 'Error getting system alerts: ' . $e->getMessage());
        }
        
        return $alerts;
    }

    /**
     * AJAX endpoint to get notifications
     */
    public function get_notifications()
    {
        $this->output->set_content_type('application/json');
        
        $user_id = $this->session->userdata('user_id');
        $role = $this->session->userdata('role');
        
        $notifications = array();
        
        try {
            // Get role-specific notifications
            switch($role) {
                case 'admin':
                    $notifications = $this->_get_admin_notifications();
                    break;
                case 'administrasi':
                    $notifications = $this->_get_admin_notifications();
                    break;
                case 'petugas_lab':
                    $notifications = $this->_get_lab_notifications();
                    break;
            }
            
        } catch (Exception $e) {
            log_message('error', 'Error getting notifications: ' . $e->getMessage());
        }
        
        $this->output->set_output(json_encode(array(
            'success' => true,
            'notifications' => $notifications,
            'count' => count($notifications)
        )));
    }

    /**
     * Profile management for all users
     */
    public function profile()
    {
        $data['title'] = 'Profil Pengguna';
        $data['user_info'] = $this->get_current_user_info();
        
        // Get user details from database
        $user_id = $this->session->userdata('user_id');
        $user_details = $this->User_model->get_user_details($this->User_model->get_user_by_id($user_id));
        $data['user_details'] = $user_details;
        
        $this->load->view('template/header', $data);
        $this->load->view('template/sidebar', $data);
        $this->load->view('dashboard/profile', $data);
        $this->load->view('template/footer');
    }

    /**
     * Settings page for all users
     */
    public function settings()
    {
        $data['title'] = 'Pengaturan';
        $data['user_info'] = $this->get_current_user_info();
        
        $this->load->view('template/header', $data);
        $this->load->view('template/sidebar', $data);
        $this->load->view('dashboard/settings', $data);
        $this->load->view('template/footer');
    }

    // =============================
    // PRIVATE HELPER METHODS
    // =============================

    private function _get_default_stats()
    {
        return array(
            'total_patients' => 0,
            'total_examinations' => 0,
            'pending_examinations' => 0,
            'completed_examinations' => 0,
            'today_patients' => 0,
            'today_examinations' => 0,
            'month_patients' => 0,
            'month_examinations' => 0
        );
    }

    private function _get_admin_notifications()
    {
        $notifications = array();
        
        // Recent patient registrations
        $this->db->select('nama, created_at');
        $this->db->where('DATE(created_at)', date('Y-m-d'));
        $this->db->order_by('created_at', 'DESC');
        $this->db->limit(5);
        $recent_patients = $this->db->get('pasien')->result_array();
        
        foreach ($recent_patients as $patient) {
            $notifications[] = array(
                'type' => 'info',
                'title' => 'Pasien Baru',
                'message' => 'Pasien ' . $patient['nama'] . ' telah terdaftar',
                'time' => $patient['created_at'],
                'icon' => 'user-plus'
            );
        }
        
        return $notifications;
    }

    private function _get_lab_notifications()
    {
        $notifications = array();
        
        // Pending examinations
        $this->db->select('pl.nomor_pemeriksaan, pl.created_at, p.nama');
        $this->db->from('pemeriksaan_lab pl');
        $this->db->join('pasien p', 'pl.pasien_id = p.pasien_id');
        $this->db->where('pl.status_pemeriksaan', 'pending');
        $this->db->order_by('pl.created_at', 'ASC');
        $this->db->limit(5);
        $pending_exams = $this->db->get()->result_array();
        
        foreach ($pending_exams as $exam) {
            $notifications[] = array(
                'type' => 'warning',
                'title' => 'Pemeriksaan Menunggu',
                'message' => 'Pemeriksaan ' . $exam['nomor_pemeriksaan'] . ' untuk ' . $exam['nama'],
                'time' => $exam['created_at'],
                'icon' => 'clock'
            );
        }
        
        return $notifications;
    }
}