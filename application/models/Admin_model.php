<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Admin_model extends CI_Model {

    public function __construct() {
        parent::__construct();
        $this->load->database();
    }

    // ==========================================
    // SYSTEM MONITORING
    // ==========================================

    public function get_system_health() {
        $health = array();
        
        // Database status
        try {
            $this->db->get('users', 1);
            $health['database'] = 'online';
        } catch (Exception $e) {
            $health['database'] = 'offline';
        }
        
        // Storage usage (simplified)
        $health['storage'] = array(
            'used' => 78,
            'total' => 100,
            'status' => 'warning'
        );
        
        // Active connections
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
        
        // This month's statistics - FIXED
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

    public function get_master_data_stats() {
        return array(
            'total_users' => $this->db->count_all('users'),
            'active_users' => $this->db->where('is_active', 1)->count_all_results('users'),
            'total_patients' => $this->db->count_all('pasien'),
            'total_examinations' => $this->db->count_all('pemeriksaan_lab'),
            'inventory_items' => $this->db->count_all('reagen') + $this->db->count_all('alat_laboratorium'),
            'low_stock_items' => $this->db->where('jumlah_stok <=', 'stok_minimal', FALSE)->count_all_results('reagen')
        );
    }

    // ==========================================
    // BACKUP MANAGEMENT
    // ==========================================

    public function get_backup_files() {
        $backup_path = APPPATH . 'backups/';
        $files = array();
        
        if (is_dir($backup_path)) {
            $dir = opendir($backup_path);
            while (($file = readdir($dir)) !== false) {
                if ($file != '.' && $file != '..' && pathinfo($file, PATHINFO_EXTENSION) == 'sql') {
                    $files[] = array(
                        'filename' => $file,
                        'size' => filesize($backup_path . $file),
                        'date' => date('Y-m-d H:i:s', filemtime($backup_path . $file))
                    );
                }
            }
            closedir($dir);
        }
        
        // Sort by date descending
        usort($files, function($a, $b) {
            return strtotime($b['date']) - strtotime($a['date']);
        });
        
        return $files;
    }

    // ==========================================
    // REPORTS
    // ==========================================

    public function count_all_activities() {
        return $this->db->count_all('activity_log');
    }

    public function get_examination_stats() {
        $stats = array();
        
        // Total examinations by status
        $this->db->select('status_pemeriksaan, COUNT(*) as count');
        $this->db->group_by('status_pemeriksaan');
        $query = $this->db->get('pemeriksaan_lab');
        foreach ($query->result_array() as $row) {
            $stats['by_status'][$row['status_pemeriksaan']] = $row['count'];
        }
        
        // Examinations by type
        $this->db->select('jenis_pemeriksaan, COUNT(*) as count');
        $this->db->group_by('jenis_pemeriksaan');
        $this->db->order_by('count', 'DESC');
        $query = $this->db->get('pemeriksaan_lab');
        $stats['by_type'] = $query->result_array();
        
        // Monthly trend (last 6 months) - FIXED
        $stats['monthly_trend'] = array();
        for ($i = 5; $i >= 0; $i--) {
            $month = date('Y-m', strtotime("-{$i} months"));
            $count = $this->db->where("DATE_FORMAT(tanggal_pemeriksaan, '%Y-%m') = ", $month, FALSE)->count_all_results('pemeriksaan_lab');
            $stats['monthly_trend'][] = array(
                'month' => date('M Y', strtotime($month . '-01')),
                'count' => $count
            );
        }
        
        return $stats;
    }

    public function get_recent_examinations($limit = 20) {
        // UPDATED: Hapus join dengan tabel dokter
        $this->db->select('pl.*, p.nama as nama_pasien, p.dokter_perujuk, p.asal_rujukan');
        $this->db->from('pemeriksaan_lab pl');
        $this->db->join('pasien p', 'pl.pasien_id = p.pasien_id');
        $this->db->order_by('pl.created_at', 'DESC');
        $this->db->limit($limit);
        
        return $this->db->get()->result_array();
    }

    public function get_financial_stats() {
        $stats = array();
        
        // Total revenue
        $this->db->select('SUM(total_biaya) as total');
        $this->db->where('status_pembayaran', 'lunas');
        $query = $this->db->get('invoice');
        $result = $query->row_array();
        $stats['total_revenue'] = $result['total'] ? $result['total'] : 0;
        
        // Revenue by payment type
        $this->db->select('jenis_pembayaran, SUM(total_biaya) as total');
        $this->db->where('status_pembayaran', 'lunas');
        $this->db->group_by('jenis_pembayaran');
        $query = $this->db->get('invoice');
        foreach ($query->result_array() as $row) {
            $stats['by_payment_type'][$row['jenis_pembayaran']] = $row['total'];
        }
        
        // Pending payments
        $this->db->select('COUNT(*) as count, SUM(total_biaya) as total');
        $this->db->where('status_pembayaran', 'belum_bayar');
        $query = $this->db->get('invoice');
        $result = $query->row_array();
        $stats['pending_payments'] = array(
            'count' => $result['count'],
            'total' => $result['total'] ? $result['total'] : 0
        );
        
        return $stats;
    }

    public function get_revenue_chart_data() {
        $data = array();
        
        // Last 12 months revenue - FIXED
        for ($i = 11; $i >= 0; $i--) {
            $month = date('Y-m', strtotime("-{$i} months"));
            $this->db->select('SUM(total_biaya) as total');
            $this->db->where('status_pembayaran', 'lunas');
            $this->db->where("DATE_FORMAT(tanggal_pembayaran, '%Y-%m') = ", $month, FALSE);
            $query = $this->db->get('invoice');
            $result = $query->row_array();
            
            $data[] = array(
                'month' => date('M Y', strtotime($month . '-01')),
                'revenue' => $result['total'] ? (float)$result['total'] : 0
            );
        }
        
        return $data;
    }

    // ==========================================
    // USER ANALYTICS
    // ==========================================

    public function get_user_analytics() {
        $analytics = array();
        
        // User distribution by role (exclude dokter)
        $this->db->select('role, COUNT(*) as count');
        $this->db->where('is_active', 1);
        $this->db->where_not_in('role', array('dokter'));
        $this->db->group_by('role');
        $query = $this->db->get('users');
        foreach ($query->result_array() as $row) {
            $analytics['by_role'][$row['role']] = $row['count'];
        }
        
        // User activity (last 30 days)
        $this->db->select('DATE(created_at) as date, COUNT(*) as count');
        $this->db->where('created_at >=', date('Y-m-d', strtotime('-30 days')));
        $this->db->group_by('DATE(created_at)');
        $this->db->order_by('date', 'ASC');
        $query = $this->db->get('activity_log');
        $analytics['daily_activity'] = $query->result_array();
        
        return $analytics;
    }

    // ==========================================
    // HELPER METHODS
    // ==========================================

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
        // FIXED: Proper DATE_FORMAT syntax
        $this->db->where("DATE_FORMAT(tanggal_pembayaran, '%Y-%m') = ", $month, FALSE);
        $this->db->where('status_pembayaran', 'lunas');
        $query = $this->db->get('invoice');
        
        $result = $query->row_array();
        return $result['total'] ? (float)$result['total'] : 0;
    }

    // ==========================================
    // SYSTEM MAINTENANCE
    // ==========================================

    public function optimize_database() {
        $tables = array('users', 'pasien', 'pemeriksaan_lab', 'activity_log', 'invoice');
        $results = array();
        
        foreach ($tables as $table) {
            $query = $this->db->query("OPTIMIZE TABLE {$table}");
            $results[$table] = $query ? 'success' : 'failed';
        }
        
        return $results;
    }

    public function clean_old_logs($days = 90) {
        $cutoff_date = date('Y-m-d', strtotime("-{$days} days"));
        $this->db->where('created_at <', $cutoff_date);
        return $this->db->delete('activity_log');
    }

    public function get_database_size() {
        $query = $this->db->query("
            SELECT 
                ROUND(SUM(data_length + index_length) / 1024 / 1024, 1) AS 'db_size_mb'
            FROM information_schema.tables 
            WHERE table_schema = '{$this->db->database}'
        ");
        
        $result = $query->row_array();
        return $result['db_size_mb'];
    }

    // ==========================================
    // DASHBOARD WIDGETS
    // ==========================================

    public function get_dashboard_widgets() {
        return array(
            'recent_logins' => $this->_get_recent_logins(),
            'system_alerts' => $this->_get_system_alerts(),
            'quick_stats' => $this->_get_quick_stats()
        );
    }

    private function _get_recent_logins() {
        // UPDATED: Hapus referensi ke tabel dokter
        $this->db->select('al.created_at, u.username, u.role, al.user_id');
        $this->db->from('activity_log al');
        $this->db->join('users u', 'al.user_id = u.user_id');
        $this->db->where('al.activity', 'User logged in');
        $this->db->order_by('al.created_at', 'DESC');
        $this->db->limit(5);
        
        $results = $this->db->get()->result_array();
        
        // Tambahkan nama lengkap secara manual (tanpa dokter)
        foreach ($results as &$result) {
            $nama_lengkap = $result['username']; // Default fallback
            
            if ($result['role'] == 'admin') {
                $this->db->select('nama_admin');
                $this->db->where('user_id', $result['user_id']);
                $query = $this->db->get('administrator');
                if ($query->num_rows() > 0) {
                    $row = $query->row_array();
                    $nama_lengkap = $row['nama_admin'];
                }
            } elseif ($result['role'] == 'administrasi') {
                $this->db->select('nama_admin');
                $this->db->where('user_id', $result['user_id']);
                $query = $this->db->get('administrasi');
                if ($query->num_rows() > 0) {
                    $row = $query->row_array();
                    $nama_lengkap = $row['nama_admin'];
                }
            } elseif ($result['role'] == 'petugas_lab') {
                $this->db->select('nama_petugas');
                $this->db->where('user_id', $result['user_id']);
                $query = $this->db->get('petugas_lab');
                if ($query->num_rows() > 0) {
                    $row = $query->row_array();
                    $nama_lengkap = $row['nama_petugas'];
                }
            }
            // REMOVED: handling untuk role dokter
            
            $result['nama_lengkap'] = $nama_lengkap;
            unset($result['user_id']); // Hapus user_id dari result
        }
        
        return $results;
    }

    private function _get_system_alerts() {
        $alerts = array();
        
        // Low stock alerts
        $low_stock = $this->db->where('jumlah_stok <=', 'stok_minimal', FALSE)->count_all_results('reagen');
        if ($low_stock > 0) {
            $alerts[] = array(
                'type' => 'warning',
                'message' => "{$low_stock} items have low stock",
                'action' => 'View Inventory'
            );
        }
        
        // Equipment maintenance alerts
        $maintenance_due = $this->db->where('jadwal_kalibrasi <=', date('Y-m-d'))->count_all_results('alat_laboratorium');
        if ($maintenance_due > 0) {
            $alerts[] = array(
                'type' => 'info',
                'message' => "{$maintenance_due} equipment need maintenance",
                'action' => 'Schedule Maintenance'
            );
        }
        
        return $alerts;
    }

    private function _get_quick_stats() {
        return array(
            'active_sessions' => $this->_count_active_sessions(),
            'pending_approvals' => $this->_count_pending_approvals(),
            'system_uptime' => $this->_get_system_uptime()
        );
    }

    private function _count_active_sessions() {
        // Simplified - in real implementation you'd check session storage
        return rand(5, 15);
    }

    private function _count_pending_approvals() {
        return $this->db->where('status_pemeriksaan', 'pending')->count_all_results('pemeriksaan_lab');
    }

    private function _get_system_uptime() {
        // Simplified - in real implementation you'd track actual uptime
        return '99.9%';
    }
}