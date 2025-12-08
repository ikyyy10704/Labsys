<?php
defined('BASEPATH') OR exit('No direct script access allowed');
class Inventory_lab_model extends CI_Model {
    public function __construct()
    {
        parent::__construct();
        $this->load->database();
    }
    // ==========================================
    // INVENTORY RETRIEVAL METHODS
    // ==========================================
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
                // Add lokasi and lokasi_penyimpanan for compatibility
                if ($item['tipe_inventory'] === 'alat') {
                    $alat_detail = $this->get_alat_by_id($item['item_id']);
                    $item['lokasi'] = $alat_detail['lokasi'] ?? null;
                    $item['merek_model'] = $alat_detail['merek_model'] ?? null;
                } else {
                    $reagen_detail = $this->get_reagen_by_id($item['item_id']);
                    $item['lokasi_penyimpanan'] = $reagen_detail['lokasi_penyimpanan'] ?? null;
                    $item['satuan'] = $reagen_detail['satuan'] ?? null;
                }
            }
            return $result;
        } catch (Exception $e) {
            log_message('error', 'Error in get_all_inventory: ' . $e->getMessage());
            return array();
        }
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
                $this->db->group_end();
            }
            $this->db->order_by('nama_item', 'ASC');
            $query = $this->db->get();
            $result = $query->result_array();
            // Add location details for each item
            foreach ($result as &$item) {
                if ($item['tipe_inventory'] === 'alat') {
                    $alat_detail = $this->get_alat_by_id($item['item_id']);
                    $item['lokasi'] = $alat_detail['lokasi'] ?? null;
                    $item['merek_model'] = $alat_detail['merek_model'] ?? null;
                } else {
                    $reagen_detail = $this->get_reagen_by_id($item['item_id']);
                    $item['lokasi_penyimpanan'] = $reagen_detail['lokasi_penyimpanan'] ?? null;
                    $item['satuan'] = $reagen_detail['satuan'] ?? null;
                }
            }
            log_message('debug', 'Filter query: ' . $this->db->last_query());
            log_message('debug', 'Results found: ' . count($result));
            return $result;
        } catch (Exception $e) {
            log_message('error', 'Database error in get_filtered_inventory: ' . $e->getMessage());
            return array();
        }
    }
    public function get_item_details($item_id, $type)
    {
        if ($type === 'alat') {
            return $this->get_alat_by_id($item_id);
        } else {
            return $this->get_reagen_by_id($item_id);
        }
    }
    public function get_reagen_by_id($reagen_id)
    {
        $this->db->where('reagen_id', $reagen_id);
        $query = $this->db->get('reagen');
        return $query->row_array();
    }
public function get_calibration_stats($alat_id)
{
    try {
        $history = $this->get_calibration_history($alat_id);
        $stats = array(
            'total_calibrations' => count($history),
            'passed_count' => 0,
            'failed_count' => 0,
            'conditional_count' => 0,
            'avg_interval_days' => 0
        );
        if (empty($history)) {
            return $stats;
        }
        // Count by result
        foreach ($history as $record) {
            if (isset($record['hasil_kalibrasi'])) {
                if ($record['hasil_kalibrasi'] === 'Passed') {
                    $stats['passed_count']++;
                } elseif ($record['hasil_kalibrasi'] === 'Failed') {
                    $stats['failed_count']++;
                } elseif ($record['hasil_kalibrasi'] === 'Conditional') {
                    $stats['conditional_count']++;
                }
            }
        }
        // Calculate average interval
        if (count($history) > 1) {
            $intervals = array();
            for ($i = 0; $i < count($history) - 1; $i++) {
                if (isset($history[$i]['tanggal_kalibrasi']) && isset($history[$i + 1]['tanggal_kalibrasi'])) {
                    $date1 = strtotime($history[$i]['tanggal_kalibrasi']);
                    $date2 = strtotime($history[$i + 1]['tanggal_kalibrasi']);
                    $intervals[] = abs($date1 - $date2) / (60 * 60 * 24);
                }
            }
            if (!empty($intervals)) {
                $stats['avg_interval_days'] = round(array_sum($intervals) / count($intervals));
            }
        }
        return $stats;
    } catch (Exception $e) {
        log_message('error', 'Exception in get_calibration_stats: ' . $e->getMessage());
        return array(
            'total_calibrations' => 0,
            'passed_count' => 0,
            'failed_count' => 0,
            'conditional_count' => 0,
            'avg_interval_days' => 0
        );
    }
}
    public function create_alat($data)
    {
        if ($this->db->insert('alat_laboratorium', $data)) {
            return $this->db->insert_id();
        }
        return false;
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
    // ==========================================
    // UPDATE METHODS
    // ==========================================
    public function update_item($item_id, $type, $data)
    {
        if ($type === 'alat') {
            return $this->update_alat($item_id, $data);
        } else {
            return $this->update_reagen($item_id, $data);
        }
    }
    public function update_alat($alat_id, $data)
    {
        $this->db->where('alat_id', $alat_id);
        return $this->db->update('alat_laboratorium', $data);
    }
    public function update_reagen($reagen_id, $data)
    {
        // Auto-update status based on stock and expiry
        $data = $this->_update_reagen_status($data);
        $this->db->where('reagen_id', $reagen_id);
        return $this->db->update('reagen', $data);
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
    // ==========================================
    // ALERT AND NOTIFICATION METHODS
    // ==========================================
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
    // STOCK MANAGEMENT METHODS
    // ==========================================
    public function adjust_stock($reagen_id, $adjustment_type, $quantity, $notes = null, $user_id = null)
    {
        // Get current stock
        $reagen = $this->get_reagen_by_id($reagen_id);
        if (!$reagen) {
            return false;
        }
        $current_stock = $reagen['jumlah_stok'];
        $new_stock = $current_stock;
        switch ($adjustment_type) {
            case 'add':
                $new_stock = $current_stock + $quantity;
                break;
            case 'subtract':
                $new_stock = max(0, $current_stock - $quantity);
                break;
            case 'set':
                $new_stock = $quantity;
                break;
        }
        // Update stock
        $update_data = array(
            'jumlah_stok' => $new_stock,
            'updated_at' => date('Y-m-d H:i:s')
        );
        if ($this->update_reagen($reagen_id, $update_data)) {
            // Log stock movement if needed
            $this->log_stock_movement($reagen_id, $adjustment_type, $quantity, $current_stock, $new_stock, $notes, $user_id);
            return true;
        }
        return false;
    }
    public function log_stock_movement($reagen_id, $movement_type, $quantity, $previous_stock, $new_stock, $notes = null, $user_id = null)
    {
        // Check if stock_movements table exists
        if ($this->db->table_exists('stock_movements')) {
            $movement_data = array(
                'reagen_id' => $reagen_id,
                'movement_type' => $movement_type,
                'quantity_changed' => $quantity,
                'stock_before' => $previous_stock,
                'stock_after' => $new_stock,
                'notes' => $notes,
                'user_id' => $user_id,
                'movement_date' => date('Y-m-d H:i:s')
            );
            return $this->db->insert('stock_movements', $movement_data);
        }
        return true;
    }
    // ==========================================
    // DASHBOARD METHODS
    // ==========================================
    public function get_dashboard_summary()
    {
        $summary = array();
        // Items needing attention
        $this->db->from('v_inventory_status');
        $this->db->where_in('alert_level', array('Warning', 'Urgent', 'Low Stock', 'Calibration Due'));
        $summary['items_need_attention'] = $this->db->count_all_results();
        // Calibrations due this week
        $week_end = date('Y-m-d', strtotime('+7 days'));
        $this->db->from('alat_laboratorium');
        $this->db->where('jadwal_kalibrasi >=', date('Y-m-d'));
        $this->db->where('jadwal_kalibrasi <=', $week_end);
        $summary['calibrations_due_week'] = $this->db->count_all_results();
        // Items expiring this month
        $month_end = date('Y-m-t');
        $this->db->from('reagen');
        $this->db->where('expired_date >=', date('Y-m-d'));
        $this->db->where('expired_date <=', $month_end);
        $summary['expiring_this_month'] = $this->db->count_all_results();
        return $summary;
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
            $data['status'] = 'Hampir Habis';
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
        public function get_calibration_history($alat_id)
{
    try {
        $this->db->select('ch.*, u.username as user_name'); // Ubah u.nama jadi u.username
        $this->db->from('calibration_history ch');
        $this->db->join('users u', 'ch.user_id = u.user_id', 'left');
        $this->db->where('ch.alat_id', $alat_id);
        $this->db->order_by('ch.tanggal_kalibrasi', 'DESC');
        $query = $this->db->get();
        if ($this->db->error()['code'] != 0) {
            log_message('error', 'Database error in get_calibration_history: ' . $this->db->error()['message']);
            return array();
        }
        return $query->result_array();
    } catch (Exception $e) {
        log_message('error', 'Exception in get_calibration_history: ' . $e->getMessage());
        return array();
    }
}
    /**
     * Get calibration reminders (due soon or overdue)
     */
    public function get_calibration_reminders($days_ahead = 30)
    {
        $this->db->select('alat_id, nama_alat, kode_unik, jadwal_kalibrasi, status_alat,
                          DATEDIFF(jadwal_kalibrasi, CURDATE()) as days_until');
        $this->db->from('alat_laboratorium');
        $this->db->where('jadwal_kalibrasi IS NOT NULL');
        $this->db->where('jadwal_kalibrasi <=', date('Y-m-d', strtotime("+{$days_ahead} days")));
        $this->db->order_by('jadwal_kalibrasi', 'ASC');
        return $this->db->get()->result_array();
    }
    /**
     * Update alat calibration info
     */
    public function update_alat_calibration($alat_id, $data)
    {
        $this->db->where('alat_id', $alat_id);
        return $this->db->update('alat_laboratorium', $data);
    }
    /**
     * Get alat by ID
     */
    public function get_alat_by_id($alat_id)
    {
        $this->db->where('alat_id', $alat_id);
        return $this->db->get('alat_laboratorium')->row_array();
    }
    /**
     * Check if calibration is overdue
     */
    public function is_calibration_overdue($alat_id)
    {
        $this->db->select('jadwal_kalibrasi');
        $this->db->where('alat_id', $alat_id);
        $alat = $this->db->get('alat_laboratorium')->row_array();
        if (!$alat || !$alat['jadwal_kalibrasi']) {
            return false;
        }
        return strtotime($alat['jadwal_kalibrasi']) < time();
    }
    /**
     * Get days until next calibration
     */
    public function get_days_until_calibration($alat_id)
    {
        $this->db->select('DATEDIFF(jadwal_kalibrasi, CURDATE()) as days_until');
        $this->db->where('alat_id', $alat_id);
        $result = $this->db->get('alat_laboratorium')->row_array();
        return $result ? (int)$result['days_until'] : null;
    }
    /**
     * Get latest calibration record
     */
    public function get_latest_calibration($alat_id)
    {
        $this->db->select('ch.*, u.nama as user_name');
        $this->db->from('calibration_history ch');
        $this->db->join('users u', 'ch.user_id = u.user_id', 'left');
        $this->db->where('ch.alat_id', $alat_id);
        $this->db->order_by('ch.tanggal_kalibrasi', 'DESC');
        $this->db->limit(1);
        return $this->db->get()->row_array();
    }
    /**
     * Count calibrations by result
     */
    public function count_calibrations_by_result($alat_id, $result)
    {
        $this->db->where('alat_id', $alat_id);
        $this->db->where('hasil_kalibrasi', $result);
        return $this->db->count_all_results('calibration_history');
    }
    /**
     * Get calibration history by date range
     */
    public function get_calibration_history_by_date($alat_id, $start_date, $end_date)
    {
        $this->db->select('ch.*, u.nama as user_name');
        $this->db->from('calibration_history ch');
        $this->db->join('users u', 'ch.user_id = u.user_id', 'left');
        $this->db->where('ch.alat_id', $alat_id);
        $this->db->where('ch.tanggal_kalibrasi >=', $start_date);
        $this->db->where('ch.tanggal_kalibrasi <=', $end_date);
        $this->db->order_by('ch.tanggal_kalibrasi', 'DESC');
        return $this->db->get()->result_array();
    }
    /**
     * Delete calibration record
     */
    public function delete_calibration($calibration_id)
    {
        $this->db->where('calibration_id', $calibration_id);
        return $this->db->delete('calibration_history');
    }
    /**
     * Get all calibration records (for reports)
     */
    public function get_all_calibrations($limit = null, $offset = 0)
    {
        $this->db->select('ch.*, al.nama_alat, al.kode_unik, u.nama as user_name');
        $this->db->from('calibration_history ch');
        $this->db->join('alat_laboratorium al', 'ch.alat_id = al.alat_id', 'left');
        $this->db->join('users u', 'ch.user_id = u.user_id', 'left');
        $this->db->order_by('ch.tanggal_kalibrasi', 'DESC');
        if ($limit !== null) {
            $this->db->limit($limit, $offset);
        }
        return $this->db->get()->result_array();
    }
    /**
     * Get calibration statistics (global)
     */
    public function get_global_calibration_stats()
    {
        // Total calibrations
        $total = $this->db->count_all('calibration_history');
        // By result
        $this->db->where('hasil_kalibrasi', 'Passed');
        $passed = $this->db->count_all_results('calibration_history');
        $this->db->where('hasil_kalibrasi', 'Failed');
        $failed = $this->db->count_all_results('calibration_history');
        $this->db->where('hasil_kalibrasi', 'Conditional');
        $conditional = $this->db->count_all_results('calibration_history');
        // Overdue calibrations
        $this->db->where('jadwal_kalibrasi <', date('Y-m-d'));
        $this->db->where('jadwal_kalibrasi IS NOT NULL', null, false);
        $overdue = $this->db->count_all_results('alat_laboratorium');
        // Due soon (within 30 days)
        $this->db->where('jadwal_kalibrasi >=', date('Y-m-d'));
        $this->db->where('jadwal_kalibrasi <=', date('Y-m-d', strtotime('+30 days')));
        $due_soon = $this->db->count_all_results('alat_laboratorium');
        return array(
            'total_calibrations' => $total,
            'passed' => $passed,
            'failed' => $failed,
            'conditional' => $conditional,
            'overdue' => $overdue,
            'due_soon' => $due_soon,
            'pass_rate' => $total > 0 ? round(($passed / $total) * 100, 2) : 0
        );
    }
    /**
     * Update inventory statistics to include calibration info
     */
    public function get_inventory_statistics()
    {
        // Existing stats
        $this->db->where('1=1');
        $total_alat = $this->db->count_all_results('alat_laboratorium');
        $this->db->where('1=1');
        $total_reagen = $this->db->count_all_results('reagen');
        // Low stock reagents
        $this->db->where('jumlah_stok <=', 'stok_minimal', FALSE);
        $low_stock = $this->db->count_all_results('reagen');
        // Expired reagents
        $this->db->where('expired_date <', date('Y-m-d'));
        $this->db->where('expired_date IS NOT NULL', null, false);
        $expired = $this->db->count_all_results('reagen');
        // Calibration due (NEW)
        $this->db->where('jadwal_kalibrasi <=', date('Y-m-d', strtotime('+30 days')));
        $this->db->where('jadwal_kalibrasi IS NOT NULL', null, false);
        $calibration_due = $this->db->count_all_results('alat_laboratorium');
        // Broken equipment (NEW)
        $this->db->where('status_alat', 'Rusak');
        $broken_equipment = $this->db->count_all_results('alat_laboratorium');
        // Total critical alerts
        $total_critical = $low_stock + $expired + $calibration_due + $broken_equipment;
        return array(
            'total_alat' => $total_alat,
            'total_reagen' => $total_reagen,
            'low_stock' => $low_stock,
            'expired' => $expired,
            'calibration_due' => $calibration_due,
            'broken_equipment' => $broken_equipment,
            'total_critical' => $total_critical,
            'total_alerts' => $total_critical
        );
    }
public function save_calibration($data)
    {
        try {
            // Insert calibration history
            if ($this->db->insert('calibration_history', $data)) {
                $calibration_id = $this->db->insert_id();
                // Update alat_laboratorium
                $update_data = array(
                    'tanggal_kalibrasi_terakhir' => $data['tanggal_kalibrasi'],
                    'jadwal_kalibrasi' => $data['next_calibration_date'],
                    'status_alat' => 'Normal',
                    'updated_at' => date('Y-m-d H:i:s')
                );
                // Update riwayat_perbaikan
                $alat = $this->get_alat_by_id($data['alat_id']);
                $calibration_record = "Kalibrasi selesai pada " . date('d/m/Y', strtotime($data['tanggal_kalibrasi']));
                if (!empty($data['teknisi'])) {
                    $calibration_record .= " oleh {$data['teknisi']}";
                }
                if (!empty($data['hasil_kalibrasi'])) {
                    $calibration_record .= ". Hasil: {$data['hasil_kalibrasi']}";
                }
                $existing_history = $alat['riwayat_perbaikan'];
                $new_history = empty($existing_history) ? $calibration_record : $existing_history . "
" . $calibration_record;
                $update_data['riwayat_perbaikan'] = $new_history;
                $this->update_alat($data['alat_id'], $update_data);
                return $calibration_id;
            }
            return false;
        } catch (Exception $e) {
            log_message('error', 'Error saving calibration: ' . $e->getMessage());
            return false;
        }
    }
    public function schedule_calibration($alat_id, $jadwal_kalibrasi, $catatan = null)
    {
        try {
            $update_data = array(
                'jadwal_kalibrasi' => $jadwal_kalibrasi,
                'status_alat' => 'Perlu Kalibrasi',
                'updated_at' => date('Y-m-d H:i:s')
            );
            if (!empty($catatan)) {
                $alat = $this->get_alat_by_id($alat_id);
                $existing_history = $alat['riwayat_perbaikan'];
                $schedule_note = "Kalibrasi dijadwalkan pada " . date('d/m/Y', strtotime($jadwal_kalibrasi));
                if (!empty($catatan)) {
                    $schedule_note .= ". Catatan: {$catatan}";
                }
                $new_history = empty($existing_history) ? $schedule_note : $existing_history . "
" . $schedule_note;
                $update_data['riwayat_perbaikan'] = $new_history;
            }
            return $this->update_alat($alat_id, $update_data);
        } catch (Exception $e) {
            log_message('error', 'Error scheduling calibration: ' . $e->getMessage());
            return false;
        }
    }
}