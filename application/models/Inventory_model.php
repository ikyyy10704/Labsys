<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Inventory_model extends CI_Model {

    public function __construct()
    {
        parent::__construct();
        $this->load->database();
    }

public function get_filtered_inventory($filters = array())
{
    try {
        // Base query menggunakan view yang sudah ada
        $this->db->select('*');
        $this->db->from('v_inventory_status');
        
        // Apply filters
        if (!empty($filters['type']) && in_array($filters['type'], ['alat', 'reagen'])) {
            $this->db->where('tipe_inventory', $filters['type']);
        }
        
        if (!empty($filters['status'])) {
            $this->db->where('status', $filters['status']);
        }
        
        if (!empty($filters['alert']) && $filters['alert'] !== 'Semua Level') {
            $this->db->where('alert_level', $filters['alert']);
        }
        
        if (!empty($filters['search'])) {
            $search = $this->db->escape_like_str($filters['search']);
            $this->db->group_start();
            $this->db->like('nama_item', $search, 'both');
            $this->db->or_like('kode_unik', $search, 'both');
            if ($filters['type'] === 'alat' || empty($filters['type'])) {
                $this->db->or_like('lokasi', $search, 'both');
            }
            if ($filters['type'] === 'reagen' || empty($filters['type'])) {
                $this->db->or_like('lokasi_penyimpanan', $search, 'both');
            }
            $this->db->group_end();
        }
        
        $this->db->order_by('nama_item', 'ASC');
        
        $query = $this->db->get();
        $result = $query->result_array();
        
        // Debug: log query and result count
        log_message('debug', 'Filter query: ' . $this->db->last_query());
        log_message('debug', 'Results found: ' . count($result));
        
        return $result;
        
    } catch (Exception $e) {
        log_message('error', 'Database error in get_filtered_inventory: ' . $e->getMessage());
        return array();
    }
}

    public function get_inventory_statistics()
    {
        $stats = array(
            'total_alat' => 0,
            'total_reagen' => 0,
            'total_alerts' => 0,
            'total_critical' => 0
        );
        
        // Count alat laboratorium
        $this->db->from('alat_laboratorium');
        $stats['total_alat'] = $this->db->count_all_results();
        
        // Count reagen
        $this->db->from('reagen');
        $stats['total_reagen'] = $this->db->count_all_results();
        
        // Count alerts (Warning and above)
        $this->db->from('v_inventory_status');
        $this->db->where_in('alert_level', array('Warning', 'Urgent', 'Low Stock', 'Calibration Due'));
        $stats['total_alerts'] = $this->db->count_all_results();
        
        // Count critical (Urgent level)
        $this->db->from('v_inventory_status');
        $this->db->where('alert_level', 'Urgent');
        $stats['total_critical'] = $this->db->count_all_results();
        
        return $stats;
    }

    public function get_detailed_inventory_statistics()
    {
        $stats = $this->get_inventory_statistics();
        
        // Add more detailed statistics
        // Alat by status
        $this->db->select('status_alat, COUNT(*) as count');
        $this->db->from('alat_laboratorium');
        $this->db->group_by('status_alat');
        $query = $this->db->get();
        $stats['alat_by_status'] = array();
        foreach ($query->result_array() as $row) {
            $stats['alat_by_status'][$row['status_alat']] = $row['count'];
        }
        
        // Reagen by status
        $this->db->select('status, COUNT(*) as count');
        $this->db->from('reagen');
        $this->db->group_by('status');
        $query = $this->db->get();
        $stats['reagen_by_status'] = array();
        foreach ($query->result_array() as $row) {
            $stats['reagen_by_status'][$row['status']] = $row['count'];
        }
        
        // Items expiring soon (within 30 days)
        $this->db->from('reagen');
        $this->db->where('expired_date <=', date('Y-m-d', strtotime('+30 days')));
        $this->db->where('expired_date >=', date('Y-m-d'));
        $stats['expiring_soon'] = $this->db->count_all_results();
        
        // Items need calibration
        $this->db->from('alat_laboratorium');
        $this->db->where('jadwal_kalibrasi <=', date('Y-m-d'));
        $stats['need_calibration'] = $this->db->count_all_results();
        
        return $stats;
    }

    public function get_inventory_alerts()
    {
        $this->db->select('*');
        $this->db->from('v_inventory_status');
        $this->db->where_in('alert_level', array('Warning', 'Urgent', 'Low Stock', 'Calibration Due'));
        $this->db->order_by('alert_level', 'DESC');
        $this->db->order_by('nama_item', 'ASC');
        
        $query = $this->db->get();
        return $query->result_array();
    }

    public function get_inventory_chart_data()
    {
        $data = array();
        
        // Inventory by type
        $data['by_type'] = array(
            'alat' => $this->db->count_all('alat_laboratorium'),
            'reagen' => $this->db->count_all('reagen')
        );
        
        // Alert levels distribution
        $this->db->select('alert_level, COUNT(*) as count');
        $this->db->from('v_inventory_status');
        $this->db->group_by('alert_level');
        $query = $this->db->get();
        $data['by_alert_level'] = array();
        foreach ($query->result_array() as $row) {
            $data['by_alert_level'][$row['alert_level']] = $row['count'];
        }
        
        return $data;
    }

    // ==========================================
    // ALAT LABORATORIUM METHODS
    // ==========================================

    public function get_all_alat()
    {
        $this->db->select('*');
        $this->db->from('alat_laboratorium');
        $this->db->order_by('nama_alat', 'ASC');
        
        $query = $this->db->get();
        return $query->result_array();
    }

    public function get_alat_by_id($alat_id)
    {
        $this->db->where('alat_id', $alat_id);
        $query = $this->db->get('alat_laboratorium');
        return $query->row_array();
    }

    public function create_alat($data)
    {
        if ($this->db->insert('alat_laboratorium', $data)) {
            return $this->db->insert_id();
        }
        return false;
    }

    public function update_alat($alat_id, $data)
    {
        $this->db->where('alat_id', $alat_id);
        return $this->db->update('alat_laboratorium', $data);
    }

    public function delete_alat($alat_id)
    {
        $this->db->where('alat_id', $alat_id);
        return $this->db->delete('alat_laboratorium');
    }

    // ==========================================
    // REAGEN METHODS
    // ==========================================

    public function get_all_reagen()
    {
        $this->db->select('*');
        $this->db->from('reagen');
        $this->db->order_by('nama_reagen', 'ASC');
        
        $query = $this->db->get();
        return $query->result_array();
    }

    public function get_reagen_by_id($reagen_id)
    {
        $this->db->where('reagen_id', $reagen_id);
        $query = $this->db->get('reagen');
        return $query->row_array();
    }

    public function create_reagen($data)
    {
        // Auto-update status based on stock and expiry
        $data = $this->_update_reagen_status($data);
        
        if ($this->db->insert('reagen', $data)) {
            return $this->db->insert_id();
        }
        return false;
    }

    public function update_reagen($reagen_id, $data)
    {
        // Auto-update status based on stock and expiry
        $data = $this->_update_reagen_status($data);
        
        $this->db->where('reagen_id', $reagen_id);
        return $this->db->update('reagen', $data);
    }

    public function delete_reagen($reagen_id)
    {
        $this->db->where('reagen_id', $reagen_id);
        return $this->db->delete('reagen');
    }

    // ==========================================
    // UTILITY METHODS
    // ==========================================

    public function generate_kode($type)
    {
        $prefix = strtoupper(substr($type, 0, 3)); // ALT or REA
        
        // Get the highest existing number for this type
        if ($type === 'alat') {
            $this->db->select_max('alat_id');
            $query = $this->db->get('alat_laboratorium');
            $max_id = $query->row()->alat_id ?: 0;
        } else {
            $this->db->select_max('reagen_id');
            $query = $this->db->get('reagen');
            $max_id = $query->row()->reagen_id ?: 0;
        }
        
        $next_number = str_pad($max_id + 1, 3, '0', STR_PAD_LEFT);
        return $prefix . $next_number;
    }

    public function check_kode_exists($kode, $type, $exclude_id = null)
    {
        if ($type === 'alat') {
            $this->db->where('kode_unik', $kode);
            if ($exclude_id) {
                $this->db->where('alat_id !=', $exclude_id);
            }
            $query = $this->db->get('alat_laboratorium');
        } else {
            $this->db->where('kode_unik', $kode);
            if ($exclude_id) {
                $this->db->where('reagen_id !=', $exclude_id);
            }
            $query = $this->db->get('reagen');
        }
        
        return $query->num_rows() > 0;
    }

    public function get_inventory_for_export($filters = array())
    {
        $data = array();
        
        // Get alat data
        if (empty($filters['type']) || $filters['type'] === 'alat') {
            $this->db->select('
                alat_id as item_id,
                "alat" as tipe_inventory,
                kode_unik,
                nama_alat as nama_item,
                merek_model,
                lokasi,
                status_alat as status,
                jadwal_kalibrasi as exp_date,
                tanggal_kalibrasi_terakhir,
                riwayat_perbaikan as catatan,
                created_at,
                updated_at
            ');
            $this->db->from('alat_laboratorium');
            
            if (!empty($filters['search'])) {
                $this->db->group_start();
                $this->db->like('nama_alat', $filters['search']);
                $this->db->or_like('kode_unik', $filters['search']);
                $this->db->or_like('merek_model', $filters['search']);
                $this->db->or_like('lokasi', $filters['search']);
                $this->db->group_end();
            }
            
            $this->db->order_by('nama_alat', 'ASC');
            $query = $this->db->get();
            $alat_data = $query->result_array();
            
            $data = array_merge($data, $alat_data);
        }
        
        // Get reagen data
        if (empty($filters['type']) || $filters['type'] === 'reagen') {
            $this->db->select('
                reagen_id as item_id,
                "reagen" as tipe_inventory,
                kode_unik,
                nama_reagen as nama_item,
                CONCAT(jumlah_stok, " ", IFNULL(satuan, "pcs")) as stok_info,
                lokasi_penyimpanan as lokasi,
                status,
                expired_date as exp_date,
                tanggal_dipakai,
                stok_minimal,
                catatan,
                created_at,
                updated_at
            ');
            $this->db->from('reagen');
            
            if (!empty($filters['search'])) {
                $this->db->group_start();
                $this->db->like('nama_reagen', $filters['search']);
                $this->db->or_like('kode_unik', $filters['search']);
                $this->db->or_like('lokasi_penyimpanan', $filters['search']);
                $this->db->group_end();
            }
            
            $this->db->order_by('nama_reagen', 'ASC');
            $query = $this->db->get();
            $reagen_data = $query->result_array();
            
            $data = array_merge($data, $reagen_data);
        }
        
        // Sort by name
        usort($data, function($a, $b) {
            return strcmp($a['nama_item'], $b['nama_item']);
        });
        
        return $data;
    }

    public function get_inventory_alerts_for_export()
    {
        $this->db->select('
            item_id,
            tipe_inventory,
            kode_unik,
            nama_item,
            status,
            alert_level,
            CASE 
                WHEN tipe_inventory = "reagen" THEN CONCAT(jumlah_stok, " ", IFNULL(v_inventory_status.satuan, "pcs"))
                ELSE "-"
            END as stok_info,
            expired_date,
            CASE 
                WHEN expired_date IS NOT NULL THEN DATEDIFF(expired_date, CURDATE())
                ELSE NULL
            END as days_to_expire
        ');
        $this->db->from('v_inventory_status');
        $this->db->where_in('alert_level', array('Warning', 'Urgent', 'Low Stock', 'Calibration Due'));
        $this->db->order_by('alert_level', 'DESC');
        $this->db->order_by('nama_item', 'ASC');
        
        $query = $this->db->get();
        return $query->result_array();
    }

    public function count_inventory_for_export($filters = array())
    {
        $count = 0;
        
        // Count alat
        if (empty($filters['type']) || $filters['type'] === 'alat') {
            $this->db->from('alat_laboratorium');
            if (!empty($filters['search'])) {
                $this->db->group_start();
                $this->db->like('nama_alat', $filters['search']);
                $this->db->or_like('kode_unik', $filters['search']);
                $this->db->or_like('merek_model', $filters['search']);
                $this->db->or_like('lokasi', $filters['search']);
                $this->db->group_end();
            }
            $count += $this->db->count_all_results();
        }
        
        // Count reagen
        if (empty($filters['type']) || $filters['type'] === 'reagen') {
            $this->db->from('reagen');
            if (!empty($filters['search'])) {
                $this->db->group_start();
                $this->db->like('nama_reagen', $filters['search']);
                $this->db->or_like('kode_unik', $filters['search']);
                $this->db->or_like('lokasi_penyimpanan', $filters['search']);
                $this->db->group_end();
            }
            $count += $this->db->count_all_results();
        }
        
        return $count;
    }

    // ==========================================
    // MAINTENANCE METHODS
    // ==========================================

    public function update_reagen_statuses()
    {
        // Update status for all reagen based on stock and expiry
        $this->db->select('*');
        $query = $this->db->get('reagen');
        
        foreach ($query->result_array() as $reagen) {
            $updated_data = $this->_update_reagen_status($reagen);
            
            if ($updated_data['status'] !== $reagen['status']) {
                $this->db->where('reagen_id', $reagen['reagen_id']);
                $this->db->update('reagen', array('status' => $updated_data['status']));
            }
        }
    }

    // ==========================================
    // PRIVATE HELPER METHODS
    // ==========================================

    private function _update_reagen_status($data)
    {
        $jumlah_stok = isset($data['jumlah_stok']) ? (int)$data['jumlah_stok'] : 0;
        $stok_minimal = isset($data['stok_minimal']) ? (int)$data['stok_minimal'] : 10;
        $expired_date = isset($data['expired_date']) ? $data['expired_date'] : null;
        
        // Check if expired
        if ($expired_date && strtotime($expired_date) < time()) {
            $data['status'] = 'Kadaluarsa';
        }
        // Check if expiring soon (within 30 days)
        elseif ($expired_date && strtotime($expired_date) <= strtotime('+30 days')) {
            $data['status'] = 'Hampir Habis'; // or could be 'Akan Expired'
        }
        // Check stock level
        elseif ($jumlah_stok <= $stok_minimal) {
            $data['status'] = 'Hampir Habis';
        }
        // Default to available
        else {
            $data['status'] = isset($data['status']) ? $data['status'] : 'Tersedia';
        }
        
        return $data;
    }
    // ==========================================
    // ADDITIONAL REPORT METHODS
    // ==========================================

    public function get_high_priority_alerts()
    {
        $this->db->select('*');
        $this->db->from('v_inventory_status');
        $this->db->where_in('alert_level', array('Urgent', 'Warning'));
        $this->db->order_by('alert_level', 'DESC');
        $this->db->order_by('nama_item', 'ASC');
        $this->db->limit(10);
        
        $query = $this->db->get();
        return $query->result_array();
    }

    public function get_expiring_items($days = 30)
    {
        $this->db->select('
            reagen_id as item_id,
            "reagen" as tipe_inventory,
            kode_unik,
            nama_reagen as nama_item,
            expired_date,
            DATEDIFF(expired_date, CURDATE()) as days_to_expire
        ');
        $this->db->from('reagen');
        $this->db->where('expired_date IS NOT NULL');
        $this->db->where('expired_date >=', date('Y-m-d'));
        $this->db->where('expired_date <=', date('Y-m-d', strtotime("+{$days} days")));
        $this->db->order_by('expired_date', 'ASC');
        
        $query = $this->db->get();
        return $query->result_array();
    }

    public function get_low_stock_items()
    {
        $this->db->select('*');
        $this->db->from('reagen');
        $this->db->where('jumlah_stok <= stok_minimal');
        $this->db->order_by('jumlah_stok', 'ASC');
        
        $query = $this->db->get();
        return $query->result_array();
    }

    public function get_maintenance_schedule($days_ahead = 90)
    {
        $this->db->select('*');
        $this->db->from('alat_laboratorium');
        $this->db->where('jadwal_kalibrasi IS NOT NULL');
        $this->db->where('jadwal_kalibrasi <=', date('Y-m-d', strtotime("+{$days_ahead} days")));
        $this->db->order_by('jadwal_kalibrasi', 'ASC');
        
        $query = $this->db->get();
        return $query->result_array();
    }

    public function get_inventory_usage_statistics($start_date = null, $end_date = null)
    {
        if (!$start_date) {
            $start_date = date('Y-m-01'); // First day of current month
        }
        if (!$end_date) {
            $end_date = date('Y-m-t'); // Last day of current month
        }
        
        $stats = array();
        
        // Get stock movements (this would require a stock_movements table)
        // For now, we'll return placeholder data
        $stats['stock_movements'] = array();
        $stats['calibrations_completed'] = 0;
        $stats['maintenance_performed'] = 0;
        
        return $stats;
    }

    // ==========================================
    // ADVANCED SEARCH AND FILTERING
    // ==========================================

    public function advanced_inventory_search($filters = array())
    {
        $this->db->select('*');
        $this->db->from('v_inventory_status');
        
        // Apply multiple filters
        if (!empty($filters['type'])) {
            $this->db->where('tipe_inventory', $filters['type']);
        }
        
        if (!empty($filters['status'])) {
            if (is_array($filters['status'])) {
                $this->db->where_in('status', $filters['status']);
            } else {
                $this->db->where('status', $filters['status']);
            }
        }
        
        if (!empty($filters['alert_level'])) {
            if (is_array($filters['alert_level'])) {
                $this->db->where_in('alert_level', $filters['alert_level']);
            } else {
                $this->db->where('alert_level', $filters['alert_level']);
            }
        }
        
        if (!empty($filters['location'])) {
            $this->db->group_start();
            $this->db->like('lokasi', $filters['location']);
            $this->db->or_like('lokasi_penyimpanan', $filters['location']);
            $this->db->group_end();
        }
        
        if (!empty($filters['expire_range'])) {
            switch ($filters['expire_range']) {
                case 'expired':
                    $this->db->where('expired_date <', date('Y-m-d'));
                    break;
                case '7_days':
                    $this->db->where('expired_date >=', date('Y-m-d'));
                    $this->db->where('expired_date <=', date('Y-m-d', strtotime('+7 days')));
                    break;
                case '30_days':
                    $this->db->where('expired_date >=', date('Y-m-d'));
                    $this->db->where('expired_date <=', date('Y-m-d', strtotime('+30 days')));
                    break;
                case '90_days':
                    $this->db->where('expired_date >=', date('Y-m-d'));
                    $this->db->where('expired_date <=', date('Y-m-d', strtotime('+90 days')));
                    break;
            }
        }
        
        if (!empty($filters['stock_level'])) {
            switch ($filters['stock_level']) {
                case 'empty':
                    $this->db->where('jumlah_stok', 0);
                    break;
                case 'low':
                    $this->db->where('jumlah_stok > 0');
                    $this->db->where('jumlah_stok <= stok_minimal');
                    break;
                case 'adequate':
                    $this->db->where('jumlah_stok > stok_minimal');
                    $this->db->where('jumlah_stok <= (stok_minimal * 2)');
                    break;
                case 'high':
                    $this->db->where('jumlah_stok > (stok_minimal * 2)');
                    break;
            }
        }
        
        if (!empty($filters['search'])) {
            $search = $filters['search'];
            $this->db->group_start();
            $this->db->like('nama_item', $search);
            $this->db->or_like('kode_unik', $search);
            $this->db->or_like('status', $search);
            $this->db->group_end();
        }
        
        // Sorting
        $order_by = !empty($filters['sort_by']) ? $filters['sort_by'] : 'nama_item';
        $order_direction = !empty($filters['sort_direction']) ? $filters['sort_direction'] : 'ASC';
        $this->db->order_by($order_by, $order_direction);
        
        // Pagination
        if (!empty($filters['limit'])) {
            $this->db->limit($filters['limit']);
            if (!empty($filters['offset'])) {
                $this->db->offset($filters['offset']);
            }
        }
        
        $query = $this->db->get();
        return $query->result_array();
    }

    public function count_advanced_inventory_search($filters = array())
    {
        // Same filtering logic as advanced_inventory_search but just count
        $this->db->from('v_inventory_status');
        
        if (!empty($filters['type'])) {
            $this->db->where('tipe_inventory', $filters['type']);
        }
        
        if (!empty($filters['status'])) {
            if (is_array($filters['status'])) {
                $this->db->where_in('status', $filters['status']);
            } else {
                $this->db->where('status', $filters['status']);
            }
        }
        
        if (!empty($filters['alert_level'])) {
            if (is_array($filters['alert_level'])) {
                $this->db->where_in('alert_level', $filters['alert_level']);
            } else {
                $this->db->where('alert_level', $filters['alert_level']);
            }
        }
        
        if (!empty($filters['location'])) {
            $this->db->group_start();
            $this->db->like('lokasi', $filters['location']);
            $this->db->or_like('lokasi_penyimpanan', $filters['location']);
            $this->db->group_end();
        }
        
        if (!empty($filters['search'])) {
            $search = $filters['search'];
            $this->db->group_start();
            $this->db->like('nama_item', $search);
            $this->db->or_like('kode_unik', $search);
            $this->db->or_like('status', $search);
            $this->db->group_end();
        }
        
        return $this->db->count_all_results();
    }

    // ==========================================
    // STOCK MOVEMENT TRACKING
    // ==========================================

    public function log_stock_movement($reagen_id, $movement_type, $quantity, $previous_stock, $new_stock, $notes = null, $user_id = null)
    {
        // This would log stock movements to a separate table
        // You might want to create a stock_movements table for this
        
        $movement_data = array(
            'reagen_id' => $reagen_id,
            'movement_type' => $movement_type, // 'add', 'subtract', 'adjust', 'use'
            'quantity_changed' => $quantity,
            'stock_before' => $previous_stock,
            'stock_after' => $new_stock,
            'notes' => $notes,
            'user_id' => $user_id,
            'movement_date' => date('Y-m-d H:i:s')
        );
        
        // If stock_movements table exists
        // return $this->db->insert('stock_movements', $movement_data);
        
        return true; // Placeholder return
    }

    public function get_stock_movements($reagen_id = null, $limit = 50)
    {
        // This would get stock movement history
        // Placeholder implementation
        return array();
    }

    // ==========================================
    // INVENTORY REPORTS DATA
    // ==========================================

    public function get_inventory_summary_by_category()
    {
        $summary = array();
        
        // Alat by category (you might want to add category field)
        $this->db->select('status_alat as category, COUNT(*) as count, "alat" as type');
        $this->db->from('alat_laboratorium');
        $this->db->group_by('status_alat');
        $query = $this->db->get();
        
        foreach ($query->result_array() as $row) {
            $summary[] = $row;
        }
        
        // Reagen by status
        $this->db->select('status as category, COUNT(*) as count, "reagen" as type');
        $this->db->from('reagen');
        $this->db->group_by('status');
        $query = $this->db->get();
        
        foreach ($query->result_array() as $row) {
            $summary[] = $row;
        }
        
        return $summary;
    }

    public function get_monthly_inventory_trends($months = 12)
    {
        $trends = array();
        
        // Get monthly data for the past X months
        for ($i = $months - 1; $i >= 0; $i--) {
            $month_start = date('Y-m-01', strtotime("-{$i} months"));
            $month_end = date('Y-m-t', strtotime("-{$i} months"));
            $month_label = date('M Y', strtotime("-{$i} months"));
            
            // Count items added in this month
            $this->db->from('alat_laboratorium');
            $this->db->where('created_at >=', $month_start);
            $this->db->where('created_at <=', $month_end . ' 23:59:59');
            $alat_added = $this->db->count_all_results();
            
            $this->db->from('reagen');
            $this->db->where('created_at >=', $month_start);
            $this->db->where('created_at <=', $month_end . ' 23:59:59');
            $reagen_added = $this->db->count_all_results();
            
            $trends[] = array(
                'month' => $month_label,
                'alat_added' => $alat_added,
                'reagen_added' => $reagen_added,
                'total_added' => $alat_added + $reagen_added
            );
        }
        
        return $trends;
    }

    // ==========================================
    // CALIBRATION & MAINTENANCE TRACKING
    // ==========================================

    public function get_calibration_history($alat_id = null, $limit = 20)
    {
        // This would get calibration history from a dedicated table
        // For now, we'll extract from riwayat_perbaikan field
        
        $this->db->select('alat_id, nama_alat, kode_unik, tanggal_kalibrasi_terakhir, jadwal_kalibrasi, riwayat_perbaikan');
        $this->db->from('alat_laboratorium');
        
        if ($alat_id) {
            $this->db->where('alat_id', $alat_id);
        }
        
        $this->db->where('tanggal_kalibrasi_terakhir IS NOT NULL');
        $this->db->order_by('tanggal_kalibrasi_terakhir', 'DESC');
        $this->db->limit($limit);
        
        $query = $this->db->get();
        return $query->result_array();
    }

    public function get_overdue_calibrations()
    {
        $this->db->select('*');
        $this->db->from('alat_laboratorium');
        $this->db->where('jadwal_kalibrasi <', date('Y-m-d'));
        $this->db->where('status_alat !=', 'Rusak');
        $this->db->order_by('jadwal_kalibrasi', 'ASC');
        
        $query = $this->db->get();
        return $query->result_array();
    }

    // ==========================================
    // UTILITY AND HELPER METHODS
    // ==========================================

    public function get_locations_list()
    {
        $locations = array();
        
        // Get unique locations from alat
        $this->db->select('DISTINCT lokasi as location');
        $this->db->from('alat_laboratorium');
        $this->db->where('lokasi IS NOT NULL');
        $this->db->where('lokasi !=', '');
        $query = $this->db->get();
        
        foreach ($query->result_array() as $row) {
            $locations[] = $row['location'];
        }
        
        // Get unique locations from reagen
        $this->db->select('DISTINCT lokasi_penyimpanan as location');
        $this->db->from('reagen');
        $this->db->where('lokasi_penyimpanan IS NOT NULL');
        $this->db->where('lokasi_penyimpanan !=', '');
        $query = $this->db->get();
        
        foreach ($query->result_array() as $row) {
            if (!in_array($row['location'], $locations)) {
                $locations[] = $row['location'];
            }
        }
        
        sort($locations);
        return $locations;
    }

    public function get_inventory_by_location($location)
    {
        $items = array();
        
        // Get alat at this location
        $this->db->select('alat_id as item_id, "alat" as type, kode_unik, nama_alat as nama_item, status_alat as status, lokasi as location');
        $this->db->from('alat_laboratorium');
        $this->db->where('lokasi', $location);
        $query = $this->db->get();
        $items = array_merge($items, $query->result_array());
        
        // Get reagen at this location
        $this->db->select('reagen_id as item_id, "reagen" as type, kode_unik, nama_reagen as nama_item, status, lokasi_penyimpanan as location');
        $this->db->from('reagen');
        $this->db->where('lokasi_penyimpanan', $location);
        $query = $this->db->get();
        $items = array_merge($items, $query->result_array());
        
        return $items;
    }

    public function cleanup_expired_items()
    {
        // Mark expired reagen
        $this->db->set('status', 'Kadaluarsa');
        $this->db->where('expired_date <', date('Y-m-d'));
        $this->db->where('status !=', 'Kadaluarsa');
        $expired_count = $this->db->update('reagen');
        
        return $expired_count;
    }

    public function get_dashboard_widgets_data()
    {
        $widgets = array();
        
        // Critical alerts widget
        $this->db->from('v_inventory_status');
        $this->db->where('alert_level', 'Urgent');
        $widgets['critical_alerts'] = $this->db->count_all_results();
        
        // Items expiring this week
        $this->db->from('reagen');
        $this->db->where('expired_date >=', date('Y-m-d'));
        $this->db->where('expired_date <=', date('Y-m-d', strtotime('+7 days')));
        $widgets['expiring_this_week'] = $this->db->count_all_results();
        
        // Calibrations due this month
        $this->db->from('alat_laboratorium');
        $this->db->where('jadwal_kalibrasi >=', date('Y-m-01'));
        $this->db->where('jadwal_kalibrasi <=', date('Y-m-t'));
        $widgets['calibrations_this_month'] = $this->db->count_all_results();
        
        // Recent additions (last 30 days)
        $this->db->from('alat_laboratorium');
        $this->db->where('created_at >=', date('Y-m-d', strtotime('-30 days')));
        $alat_recent = $this->db->count_all_results();
        
        $this->db->from('reagen');
        $this->db->where('created_at >=', date('Y-m-d', strtotime('-30 days')));
        $reagen_recent = $this->db->count_all_results();
        
        $widgets['recent_additions'] = $alat_recent + $reagen_recent;
        
        return $widgets;
    }
        public function search_inventory($search_term, $type = null)
    {
        if (empty($search_term)) {
            return $this->get_all_inventory();
        }
        
        $this->db->select('*');
        $this->db->from('v_inventory_status');
        
        if (!empty($type) && in_array($type, ['alat', 'reagen'])) {
            $this->db->where('tipe_inventory', $type);
        }
        
        $search = $this->db->escape_like_str($search_term);
        $this->db->group_start();
        $this->db->like('nama_item', $search, 'both');
        $this->db->or_like('kode_unik', $search, 'both');
        $this->db->or_like('status', $search, 'both');
        $this->db->group_end();
        
        $this->db->order_by('nama_item', 'ASC');
        
        $query = $this->db->get();
        return $query->result_array();
    }

    public function get_inventory_by_alert_level($alert_level)
    {
        $this->db->select('*');
        $this->db->from('v_inventory_status');
        
        if (!empty($alert_level) && $alert_level !== 'OK') {
            if ($alert_level === 'All Alerts') {
                $this->db->where_in('alert_level', array('Warning', 'Urgent', 'Low Stock', 'Calibration Due'));
            } else {
                $this->db->where('alert_level', $alert_level);
            }
        } else {
            $this->db->where('alert_level', 'OK');
        }
        
        $this->db->order_by('alert_level', 'DESC');
        $this->db->order_by('nama_item', 'ASC');
        
        $query = $this->db->get();
        return $query->result_array();
    }

    public function debug_inventory_data()
    {
        $this->output->set_content_type('application/json');
        
        // Check if view exists
        $view_exists = $this->db->query("SHOW TABLES LIKE 'v_inventory_status'")->num_rows() > 0;
        
        // Get sample data
        $this->db->limit(5);
        $sample_data = $this->db->get('v_inventory_status')->result_array();
        
        // Get total counts
        $total_alat = $this->db->where('tipe_inventory', 'alat')->count_all_results('v_inventory_status');
        $total_reagen = $this->db->where('tipe_inventory', 'reagen')->count_all_results('v_inventory_status');
        
        $debug_info = array(
            'view_exists' => $view_exists,
            'sample_data' => $sample_data,
            'total_alat' => $total_alat,
            'total_reagen' => $total_reagen,
            'last_query' => $this->db->last_query()
        );
        
        $this->output->set_output(json_encode($debug_info));
    }

    public function clear_inventory_cache()
    {
        // Clear any cached inventory data
        $this->db->cache_delete_all();
        
        // Force refresh view
        $this->db->query('FLUSH TABLES v_inventory_status');
        
        return true;
    }
    public function get_all_inventory()
{
    try {
        $this->db->select('*');
        $this->db->from('v_inventory_status');
        $this->db->order_by('nama_item', 'ASC');
        
        $query = $this->db->get();
        $result = $query->result_array();
        
        // Add additional computed fields if needed
        foreach ($result as &$item) {
            // Add any missing fields for compatibility
            if (!isset($item['merek_model'])) {
                $item['merek_model'] = null;
            }
            if (!isset($item['satuan'])) {
                $item['satuan'] = null;
            }
        }
        
        return $result;
        
    } catch (Exception $e) {
        log_message('error', 'Error in get_all_inventory: ' . $e->getMessage());
        return array();
    }
}
    
}