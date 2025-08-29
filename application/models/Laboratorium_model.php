<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Laboratorium_model extends CI_Model {

    public function __construct() {
        parent::__construct();
        $this->load->database();
    }

    // ==========================================
    // DASHBOARD STATISTICS
    // ==========================================

    public function get_lab_dashboard_stats() {
        $stats = array();
        
        // Pending requests
        $stats['pending_requests'] = $this->db->where('status_pemeriksaan', 'pending')->count_all_results('pemeriksaan_lab');
        
        // Samples in progress
        $stats['samples_in_progress'] = $this->db->where('status_pemeriksaan', 'progress')->count_all_results('pemeriksaan_lab');
        
        // Completed tests today
        $this->db->select('COUNT(*) as count');
        $this->db->where('status_pemeriksaan', 'selesai');
        $this->db->where('DATE(updated_at)', date('Y-m-d'));
        $result = $this->db->get('pemeriksaan_lab')->row_array();
        $stats['completed_today'] = $result['count'];
        
        // This month completed - FIXED
        $this->db->select('COUNT(*) as count');
        $this->db->where('status_pemeriksaan', 'selesai');
        $this->db->where("DATE_FORMAT(updated_at, '%Y-%m') = ", date('Y-m'), FALSE);
        $result = $this->db->get('pemeriksaan_lab')->row_array();
        $stats['completed_this_month'] = $result['count'];
        
        // Low stock items
        $stats['low_stock_items'] = $this->db->where('jumlah_stok <=', 'stok_minimal', FALSE)->count_all_results('reagen');
        
        // Equipment needing maintenance
        $stats['equipment_maintenance_due'] = $this->db->where('jadwal_kalibrasi <=', date('Y-m-d'))->count_all_results('alat_laboratorium');
        
        return $stats;
    }

    public function get_lab_performance_stats($period = '30') {
        $stats = array();
        
        // Daily completion trend
        $this->db->select('DATE(updated_at) as date, COUNT(*) as completed');
        $this->db->where('status_pemeriksaan', 'selesai');
        $this->db->where('updated_at >=', date('Y-m-d', strtotime("-{$period} days")));
        $this->db->group_by('DATE(updated_at)');
        $this->db->order_by('date', 'ASC');
        $stats['daily_completions'] = $this->db->get('pemeriksaan_lab')->result_array();
        
        // Test type distribution
        $this->db->select('jenis_pemeriksaan, COUNT(*) as count');
        $this->db->where('tanggal_pemeriksaan >=', date('Y-m-d', strtotime("-{$period} days")));
        $this->db->group_by('jenis_pemeriksaan');
        $this->db->order_by('count', 'DESC');
        $stats['test_distribution'] = $this->db->get('pemeriksaan_lab')->result_array();
        
        // Average processing time (simplified)
        $this->db->select('AVG(TIMESTAMPDIFF(HOUR, created_at, updated_at)) as avg_hours');
        $this->db->where('status_pemeriksaan', 'selesai');
        $this->db->where('created_at >=', date('Y-m-d', strtotime("-{$period} days")));
        $result = $this->db->get('pemeriksaan_lab')->row_array();
        $stats['avg_processing_hours'] = round($result['avg_hours'], 1);
        
        return $stats;
    }

    // ==========================================
    // EXAMINATION REQUESTS MANAGEMENT
    // ==========================================

    public function get_pending_requests($limit = null) {
        $this->db->select('pl.*, p.nama as nama_pasien, p.nik, p.jenis_kelamin, p.umur, p.dokter_perujuk, p.asal_rujukan');
        $this->db->from('pemeriksaan_lab pl');
        $this->db->join('pasien p', 'pl.pasien_id = p.pasien_id');
        $this->db->where('pl.status_pemeriksaan', 'pending');
        $this->db->order_by('pl.created_at', 'ASC');
        
        if ($limit) {
            $this->db->limit($limit);
        }
        
        return $this->db->get()->result_array();
    }

    public function get_lab_requests_by_filter($filters = array()) {
        $this->db->select('pl.*, p.nama as nama_pasien, p.nik, p.jenis_kelamin, p.umur, p.dokter_perujuk, p.asal_rujukan, pt.nama_petugas');
        $this->db->from('pemeriksaan_lab pl');
        $this->db->join('pasien p', 'pl.pasien_id = p.pasien_id');
        $this->db->join('petugas_lab pt', 'pl.petugas_id = pt.petugas_id', 'left');
        
        // Apply filters
        if (isset($filters['status'])) {
            $this->db->where('pl.status_pemeriksaan', $filters['status']);
        }
        
        if (isset($filters['date'])) {
            $this->db->where('DATE(pl.tanggal_pemeriksaan)', $filters['date']);
        }
        
        if (isset($filters['date_from']) && isset($filters['date_to'])) {
            $this->db->where('DATE(pl.tanggal_pemeriksaan) >=', $filters['date_from']);
            $this->db->where('DATE(pl.tanggal_pemeriksaan) <=', $filters['date_to']);
        }
        
        if (isset($filters['jenis_pemeriksaan'])) {
            $this->db->where('pl.jenis_pemeriksaan', $filters['jenis_pemeriksaan']);
        }
        
        if (isset($filters['search'])) {
            $this->db->group_start();
            $this->db->like('p.nama', $filters['search']);
            $this->db->or_like('p.nik', $filters['search']);
            $this->db->or_like('pl.nomor_pemeriksaan', $filters['search']);
            $this->db->group_end();
        }
        
        $this->db->order_by('pl.created_at', 'DESC');
        
        if (isset($filters['limit'])) {
            $this->db->limit($filters['limit']);
        }
        
        return $this->db->get()->result_array();
    }

    public function accept_examination_request($examination_id, $petugas_id) {
        $data = array(
            'status_pemeriksaan' => 'progress',
            'petugas_id' => $petugas_id,
            'updated_at' => date('Y-m-d H:i:s')
        );
        
        $this->db->where('pemeriksaan_id', $examination_id);
        return $this->db->update('pemeriksaan_lab', $data);
    }

    public function get_examination_by_id($examination_id) {
        $this->db->select('pl.*, p.nama as nama_pasien, p.nik, p.jenis_kelamin, p.umur, p.tempat_lahir, p.tanggal_lahir, 
                          p.alamat_domisili, p.telepon, p.dokter_perujuk, p.asal_rujukan, p.diagnosis_awal, 
                          p.rekomendasi_pemeriksaan, pt.nama_petugas');
        $this->db->from('pemeriksaan_lab pl');
        $this->db->join('pasien p', 'pl.pasien_id = p.pasien_id');
        $this->db->join('petugas_lab pt', 'pl.petugas_id = pt.petugas_id', 'left');
        $this->db->where('pl.pemeriksaan_id', $examination_id);
        
        return $this->db->get()->row_array();
    }

    // ==========================================
    // SAMPLE & SPECIMEN TRACKING
    // ==========================================

    public function get_samples_by_status($status = 'progress', $search = null) {
        $this->db->select('pl.*, p.nama as nama_pasien, p.nik, p.jenis_kelamin, p.umur, pt.nama_petugas');
        $this->db->from('pemeriksaan_lab pl');
        $this->db->join('pasien p', 'pl.pasien_id = p.pasien_id');
        $this->db->join('petugas_lab pt', 'pl.petugas_id = pt.petugas_id', 'left');
        $this->db->where('pl.status_pemeriksaan', $status);
        
        if ($search) {
            $this->db->group_start();
            $this->db->like('p.nama', $search);
            $this->db->or_like('p.nik', $search);
            $this->db->or_like('pl.nomor_pemeriksaan', $search);
            $this->db->group_end();
        }
        
        $this->db->order_by('pl.tanggal_pemeriksaan', 'DESC');
        return $this->db->get()->result_array();
    }

    public function update_sample_status($examination_id, $status, $notes = null) {
        $data = array(
            'status_pemeriksaan' => $status,
            'updated_at' => date('Y-m-d H:i:s')
        );
        
        if ($notes) {
            $data['keterangan'] = $notes;
        }
        
        $this->db->where('pemeriksaan_id', $examination_id);
        return $this->db->update('pemeriksaan_lab', $data);
    }

    public function get_sample_timeline($examination_id) {
        // Get timeline progress for a sample
        $this->db->select('*');
        $this->db->where('pemeriksaan_id', $examination_id);
        $this->db->order_by('tanggal_update', 'ASC');
        return $this->db->get('timeline_progres')->result_array();
    }

    public function add_sample_timeline($examination_id, $status, $keterangan, $petugas_id) {
        $data = array(
            'pemeriksaan_id' => $examination_id,
            'status' => $status,
            'keterangan' => $keterangan,
            'petugas_id' => $petugas_id,
            'tanggal_update' => date('Y-m-d H:i:s')
        );
        
        return $this->db->insert('timeline_progres', $data);
    }

    // ==========================================
    // LABORATORY RESULTS MANAGEMENT
    // ==========================================

    public function get_examinations_ready_for_results($petugas_id = null) {
        $this->db->select('pl.*, p.nama as nama_pasien, p.nik, p.jenis_kelamin, p.umur');
        $this->db->from('pemeriksaan_lab pl');
        $this->db->join('pasien p', 'pl.pasien_id = p.pasien_id');
        $this->db->where('pl.status_pemeriksaan', 'progress');
        
        if ($petugas_id) {
            $this->db->where('pl.petugas_id', $petugas_id);
        }
        
        $this->db->order_by('pl.tanggal_pemeriksaan', 'ASC');
        return $this->db->get()->result_array();
    }

    // Kimia Darah Results
    public function save_kimia_darah_results($data) {
        return $this->db->insert('kimia_darah', $data);
    }

    public function get_kimia_darah_results($examination_id) {
        $this->db->where('pemeriksaan_id', $examination_id);
        return $this->db->get('kimia_darah')->row_array();
    }

    public function update_kimia_darah_results($examination_id, $data) {
        $this->db->where('pemeriksaan_id', $examination_id);
        return $this->db->update('kimia_darah', $data);
    }

    // Hematologi Results
    public function save_hematologi_results($data) {
        return $this->db->insert('hematologi', $data);
    }

    public function get_hematologi_results($examination_id) {
        $this->db->where('pemeriksaan_id', $examination_id);
        return $this->db->get('hematologi')->row_array();
    }

    public function update_hematologi_results($examination_id, $data) {
        $this->db->where('pemeriksaan_id', $examination_id);
        return $this->db->update('hematologi', $data);
    }

    // Urinologi Results
    public function save_urinologi_results($data) {
        return $this->db->insert('urinologi', $data);
    }

    public function get_urinologi_results($examination_id) {
        $this->db->where('pemeriksaan_id', $examination_id);
        return $this->db->get('urinologi')->row_array();
    }

    public function update_urinologi_results($examination_id, $data) {
        $this->db->where('pemeriksaan_id', $examination_id);
        return $this->db->update('urinologi', $data);
    }

    // Serologi Imunologi Results
    public function save_serologi_results($data) {
        return $this->db->insert('serologi_imunologi', $data);
    }

    public function get_serologi_results($examination_id) {
        $this->db->where('pemeriksaan_id', $examination_id);
        return $this->db->get('serologi_imunologi')->row_array();
    }

    public function update_serologi_results($examination_id, $data) {
        $this->db->where('pemeriksaan_id', $examination_id);
        return $this->db->update('serologi_imunologi', $data);
    }

    // TBC Results
    public function save_tbc_results($data) {
        return $this->db->insert('tbc', $data);
    }

    public function get_tbc_results($examination_id) {
        $this->db->where('pemeriksaan_id', $examination_id);
        return $this->db->get('tbc')->row_array();
    }

    public function update_tbc_results($examination_id, $data) {
        $this->db->where('pemeriksaan_id', $examination_id);
        return $this->db->update('tbc', $data);
    }

    // IMS Results
    public function save_ims_results($data) {
        return $this->db->insert('ims', $data);
    }

    public function get_ims_results($examination_id) {
        $this->db->where('pemeriksaan_id', $examination_id);
        return $this->db->get('ims')->row_array();
    }

    public function update_ims_results($examination_id, $data) {
        $this->db->where('pemeriksaan_id', $examination_id);
        return $this->db->update('ims', $data);
    }

    // MLS (General Lab) Results
    public function save_mls_results($data) {
        return $this->db->insert('mls', $data);
    }

    public function get_mls_results($examination_id) {
        $this->db->where('pemeriksaan_id', $examination_id);
        return $this->db->get('mls')->result_array();
    }

    public function update_mls_results($examination_id, $data) {
        $this->db->where('pemeriksaan_id', $examination_id);
        return $this->db->update('mls', $data);
    }

    // Get all results for an examination
    public function get_all_results_by_examination($examination_id) {
        $results = array(
            'kimia_darah' => $this->get_kimia_darah_results($examination_id),
            'hematologi' => $this->get_hematologi_results($examination_id),
            'urinologi' => $this->get_urinologi_results($examination_id),
            'serologi' => $this->get_serologi_results($examination_id),
            'tbc' => $this->get_tbc_results($examination_id),
            'ims' => $this->get_ims_results($examination_id),
            'mls' => $this->get_mls_results($examination_id)
        );
        
        return $results;
    }

    // Check if examination has results
    public function has_examination_results($examination_id) {
        $tables = array('kimia_darah', 'hematologi', 'urinologi', 'serologi_imunologi', 'tbc', 'ims', 'mls');
        
        foreach ($tables as $table) {
            $this->db->where('pemeriksaan_id', $examination_id);
            if ($this->db->count_all_results($table) > 0) {
                return true;
            }
        }
        
        return false;
    }

    // ==========================================
    // QUALITY CONTROL & VALIDATION
    // ==========================================

    public function get_results_pending_validation() {
        $this->db->select('pl.*, p.nama as nama_pasien, p.nik, pt.nama_petugas');
        $this->db->from('pemeriksaan_lab pl');
        $this->db->join('pasien p', 'pl.pasien_id = p.pasien_id');
        $this->db->join('petugas_lab pt', 'pl.petugas_id = pt.petugas_id', 'left');
        $this->db->where('pl.status_pemeriksaan', 'progress');
        
        // Check if results exist
        $this->db->where('(
            EXISTS (SELECT 1 FROM kimia_darah WHERE pemeriksaan_id = pl.pemeriksaan_id) OR
            EXISTS (SELECT 1 FROM hematologi WHERE pemeriksaan_id = pl.pemeriksaan_id) OR
            EXISTS (SELECT 1 FROM urinologi WHERE pemeriksaan_id = pl.pemeriksaan_id) OR
            EXISTS (SELECT 1 FROM serologi_imunologi WHERE pemeriksaan_id = pl.pemeriksaan_id) OR
            EXISTS (SELECT 1 FROM tbc WHERE pemeriksaan_id = pl.pemeriksaan_id) OR
            EXISTS (SELECT 1 FROM ims WHERE pemeriksaan_id = pl.pemeriksaan_id) OR
            EXISTS (SELECT 1 FROM mls WHERE pemeriksaan_id = pl.pemeriksaan_id)
        )');
        
        $this->db->order_by('pl.created_at', 'ASC');
        return $this->db->get()->result_array();
    }

    public function validate_examination_result($examination_id, $validator_id = null) {
        $data = array(
            'status_pemeriksaan' => 'selesai',
            'updated_at' => date('Y-m-d H:i:s')
        );
        
        $this->db->where('pemeriksaan_id', $examination_id);
        $result = $this->db->update('pemeriksaan_lab', $data);
        
        // Add timeline entry
        if ($result && $validator_id) {
            $this->add_sample_timeline($examination_id, 'Hasil Divalidasi', 'Hasil pemeriksaan telah divalidasi dan siap diserahkan', $validator_id);
        }
        
        return $result;
    }

    public function get_recent_validations($limit = 10) {
        $this->db->select('pl.*, p.nama as nama_pasien, p.nik');
        $this->db->from('pemeriksaan_lab pl');
        $this->db->join('pasien p', 'pl.pasien_id = p.pasien_id');
        $this->db->where('pl.status_pemeriksaan', 'selesai');
        $this->db->where('DATE(pl.updated_at)', date('Y-m-d'));
        $this->db->order_by('pl.updated_at', 'DESC');
        $this->db->limit($limit);
        
        return $this->db->get()->result_array();
    }

    // ==========================================
    // INVENTORY MANAGEMENT
    // ==========================================

    public function get_reagent_inventory($filters = array()) {
        $this->db->select('*');
        $this->db->from('reagen');
        
        if (isset($filters['status'])) {
            $this->db->where('status', $filters['status']);
        }
        
        if (isset($filters['low_stock']) && $filters['low_stock']) {
            $this->db->where('jumlah_stok <=', 'stok_minimal', FALSE);
        }
        
        if (isset($filters['expired_soon'])) {
            $this->db->where('expired_date <=', date('Y-m-d', strtotime("+{$filters['expired_soon']} days")));
        }
        
        $this->db->order_by('nama_reagen', 'ASC');
        return $this->db->get()->result_array();
    }

    public function get_equipment_inventory($filters = array()) {
        $this->db->select('*');
        $this->db->from('alat_laboratorium');
        
        if (isset($filters['status'])) {
            $this->db->where('status_alat', $filters['status']);
        }
        
        if (isset($filters['maintenance_due']) && $filters['maintenance_due']) {
            $this->db->where('jadwal_kalibrasi <=', date('Y-m-d'));
        }
        
        $this->db->order_by('nama_alat', 'ASC');
        return $this->db->get()->result_array();
    }

    public function get_inventory_alerts() {
        $alerts = array();
        
        // Low stock reagents
        $this->db->select('nama_reagen, jumlah_stok, stok_minimal');
        $this->db->where('jumlah_stok <=', 'stok_minimal', FALSE);
        $low_stock = $this->db->get('reagen')->result_array();
        
        foreach ($low_stock as $reagent) {
            $alerts[] = array(
                'type' => 'low_stock',
                'severity' => 'warning',
                'item' => $reagent['nama_reagen'],
                'current_stock' => $reagent['jumlah_stok'],
                'minimum_stock' => $reagent['stok_minimal'],
                'message' => "Stok {$reagent['nama_reagen']} rendah ({$reagent['jumlah_stok']} tersisa)"
            );
        }
        
        // Expired reagents
        $this->db->select('nama_reagen, expired_date');
        $this->db->where('expired_date <=', date('Y-m-d', strtotime('+30 days')));
        $expired_soon = $this->db->get('reagen')->result_array();
        
        foreach ($expired_soon as $reagent) {
            $days_left = ceil((strtotime($reagent['expired_date']) - time()) / (60 * 60 * 24));
            $alerts[] = array(
                'type' => 'expiry',
                'severity' => $days_left <= 7 ? 'urgent' : 'warning',
                'item' => $reagent['nama_reagen'],
                'expired_date' => $reagent['expired_date'],
                'days_left' => $days_left,
                'message' => "Reagen {$reagent['nama_reagen']} akan kadaluarsa dalam {$days_left} hari"
            );
        }
        
        // Equipment maintenance due
        $this->db->select('nama_alat, jadwal_kalibrasi');
        $this->db->where('jadwal_kalibrasi <=', date('Y-m-d'));
        $maintenance_due = $this->db->get('alat_laboratorium')->result_array();
        
        foreach ($maintenance_due as $equipment) {
            $days_overdue = ceil((time() - strtotime($equipment['jadwal_kalibrasi'])) / (60 * 60 * 24));
            $alerts[] = array(
                'type' => 'maintenance',
                'severity' => $days_overdue > 7 ? 'urgent' : 'info',
                'item' => $equipment['nama_alat'],
                'due_date' => $equipment['jadwal_kalibrasi'],
                'days_overdue' => $days_overdue,
                'message' => "Kalibrasi {$equipment['nama_alat']} terlambat {$days_overdue} hari"
            );
        }
        
        return $alerts;
    }

    public function update_reagent_stock($reagent_id, $data) {
        // Auto-update status based on stock level
        if (isset($data['jumlah_stok'])) {
            $this->db->select('stok_minimal');
            $this->db->where('reagen_id', $reagent_id);
            $reagent = $this->db->get('reagen')->row_array();
            
            if ($data['jumlah_stok'] <= 0) {
                $data['status'] = 'Habis';
            } elseif ($data['jumlah_stok'] <= $reagent['stok_minimal']) {
                $data['status'] = 'Hampir Habis';
            } else {
                $data['status'] = 'Tersedia';
            }
        }
        
        // Check expiry date
        if (isset($data['expired_date'])) {
            if (strtotime($data['expired_date']) <= time()) {
                $data['status'] = 'Kadaluarsa';
            }
        }
        
        $data['updated_at'] = date('Y-m-d H:i:s');
        
        $this->db->where('reagen_id', $reagent_id);
        return $this->db->update('reagen', $data);
    }

    public function update_equipment_status($equipment_id, $data) {
        $data['updated_at'] = date('Y-m-d H:i:s');
        
        $this->db->where('alat_id', $equipment_id);
        return $this->db->update('alat_laboratorium', $data);
    }

    public function get_reagent_usage_history($reagent_id, $limit = 10) {
        // This would require a reagent usage log table
        // For now, return empty array
        return array();
    }

    public function get_equipment_maintenance_history($equipment_id, $limit = 10) {
        // This would require an equipment maintenance log table
        // For now, return basic info from riwayat_perbaikan
        $this->db->select('riwayat_perbaikan, tanggal_kalibrasi_terakhir, updated_at');
        $this->db->where('alat_id', $equipment_id);
        $equipment = $this->db->get('alat_laboratorium')->row_array();
        
        return $equipment ? array($equipment) : array();
    }

    // ==========================================
    // REPORTS & ANALYTICS
    // ==========================================

    public function get_examination_report($date_from, $date_to) {
        $this->db->select('pl.*, p.nama as nama_pasien, p.nik, pt.nama_petugas');
        $this->db->from('pemeriksaan_lab pl');
        $this->db->join('pasien p', 'pl.pasien_id = p.pasien_id');
        $this->db->join('petugas_lab pt', 'pl.petugas_id = pt.petugas_id', 'left');
        $this->db->where('DATE(pl.tanggal_pemeriksaan) >=', $date_from);
        $this->db->where('DATE(pl.tanggal_pemeriksaan) <=', $date_to);
        $this->db->order_by('pl.tanggal_pemeriksaan', 'DESC');
        
        return $this->db->get()->result_array();
    }

    public function get_workload_report($petugas_id = null, $period = '30') {
        $this->db->select('pl.status_pemeriksaan, COUNT(*) as count, pt.nama_petugas');
        $this->db->from('pemeriksaan_lab pl');
        $this->db->join('petugas_lab pt', 'pl.petugas_id = pt.petugas_id', 'left');
        $this->db->where('pl.created_at >=', date('Y-m-d', strtotime("-{$period} days")));
        
        if ($petugas_id) {
            $this->db->where('pl.petugas_id', $petugas_id);
        }
        
        $this->db->group_by('pl.petugas_id, pl.status_pemeriksaan');
        return $this->db->get()->result_array();
    }

    public function get_inventory_report() {
        $report = array();
        
        // Reagent inventory summary
        $this->db->select('status, COUNT(*) as count, SUM(jumlah_stok) as total_stock');
        $this->db->group_by('status');
        $report['reagents'] = $this->db->get('reagen')->result_array();
        
        // Equipment status summary
        $this->db->select('status_alat, COUNT(*) as count');
        $this->db->group_by('status_alat');
        $report['equipment'] = $this->db->get('alat_laboratorium')->result_array();
        
        return $report;
    }

    // ==========================================
    // HELPER METHODS
    // ==========================================

    public function get_petugas_id_by_user_id($user_id) {
        $this->db->select('petugas_id');
        $this->db->where('user_id', $user_id);
        $result = $this->db->get('petugas_lab')->row_array();
        
        return $result ? $result['petugas_id'] : null;
    }

    public function get_examination_types() {
        $this->db->select('DISTINCT jenis_pemeriksaan');
        $this->db->order_by('jenis_pemeriksaan', 'ASC');
        $query = $this->db->get('pemeriksaan_lab');
        
        $types = array();
        foreach ($query->result_array() as $row) {
            $types[] = $row['jenis_pemeriksaan'];
        }
        
        return $types;
    }

    public function get_last_examination_number($prefix = null) {
        if (!$prefix) {
            $prefix = 'EX' . date('Ymd');
        }
        
        $this->db->select('nomor_pemeriksaan');
        $this->db->like('nomor_pemeriksaan', $prefix, 'after');
        $this->db->order_by('nomor_pemeriksaan', 'DESC');
        $this->db->limit(1);
        $result = $this->db->get('pemeriksaan_lab')->row_array();
        
        return $result ? $result['nomor_pemeriksaan'] : null;
    }

    public function check_examination_exists($examination_id) {
        $this->db->where('pemeriksaan_id', $examination_id);
        return $this->db->count_all_results('pemeriksaan_lab') > 0;
    }

    public function get_examination_status_history($examination_id) {
        return $this->get_sample_timeline($examination_id);
    }
}