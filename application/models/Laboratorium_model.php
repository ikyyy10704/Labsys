<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Laboratorium_model extends CI_Model {

    public function __construct() {
        parent::__construct();
        $this->load->database();
    }
    public function get_timeline_stats($examination_id) {
        $stats = array();
        
        // Total entries
        $this->db->where('pemeriksaan_id', $examination_id);
        $stats['total_entries'] = $this->db->count_all_results('timeline_progres');
        
        // First and last entry
        $this->db->select('MIN(tanggal_update) as first_entry, MAX(tanggal_update) as last_entry');
        $this->db->where('pemeriksaan_id', $examination_id);
        $result = $this->db->get('timeline_progres')->row_array();
        
        $stats['first_entry'] = $result['first_entry'];
        $stats['last_entry'] = $result['last_entry'];
        
        // Calculate time span
        if ($result['first_entry'] && $result['last_entry']) {
            $first = new DateTime($result['first_entry']);
            $last = new DateTime($result['last_entry']);
            $diff = $first->diff($last);
            $stats['time_span_hours'] = ($diff->days * 24) + $diff->h + ($diff->i / 60);
        } else {
            $stats['time_span_hours'] = 0;
        }
        
        // Average time between entries
        if ($stats['total_entries'] > 1) {
            $stats['avg_time_between_entries'] = $stats['time_span_hours'] / ($stats['total_entries'] - 1);
        } else {
            $stats['avg_time_between_entries'] = 0;
        }
        
        return $stats;
    }

    public function get_recent_timeline_activities($limit = 10, $petugas_id = null) {
        $this->db->select('tp.*, pt.nama_petugas, pl.nomor_pemeriksaan, p.nama as nama_pasien');
        $this->db->from('timeline_progres tp');
        $this->db->join('petugas_lab pt', 'tp.petugas_id = pt.petugas_id', 'left');
        $this->db->join('pemeriksaan_lab pl', 'tp.pemeriksaan_id = pl.pemeriksaan_id');
        $this->db->join('pasien p', 'pl.pasien_id = p.pasien_id');
        
        if ($petugas_id) {
            $this->db->where('tp.petugas_id', $petugas_id);
        }
        
        $this->db->order_by('tp.tanggal_update', 'DESC');
        $this->db->limit($limit);
        
        return $this->db->get()->result_array();
    }

public function get_validation_statistics($period_days = 30)
{
    $stats = array();
    
    // Daily validation trend
    $this->db->select('DATE(completed_at) as date, COUNT(*) as count');
    $this->db->where('status_pemeriksaan', 'selesai');
    $this->db->where('completed_at >=', date('Y-m-d', strtotime("-{$period_days} days")));
    $this->db->group_by('DATE(completed_at)');
    $this->db->order_by('date', 'ASC');
    $stats['daily_trend'] = $this->db->get('pemeriksaan_lab')->result_array();
    
    // Validation by examination type
    $this->db->select('jenis_pemeriksaan, COUNT(*) as count');
    $this->db->where('status_pemeriksaan', 'selesai');
    $this->db->where('completed_at >=', date('Y-m-d', strtotime("-{$period_days} days")));
    $this->db->group_by('jenis_pemeriksaan');
    $this->db->order_by('count', 'DESC');
    $stats['by_type'] = $this->db->get('pemeriksaan_lab')->result_array();
    
    // Average time by examination type
    $this->db->select('jenis_pemeriksaan, AVG(TIMESTAMPDIFF(HOUR, started_at, completed_at)) as avg_hours');
    $this->db->where('status_pemeriksaan', 'selesai');
    $this->db->where('started_at IS NOT NULL');
    $this->db->where('completed_at IS NOT NULL');
    $this->db->where('completed_at >=', date('Y-m-d', strtotime("-{$period_days} days")));
    $this->db->group_by('jenis_pemeriksaan');
    $stats['avg_time_by_type'] = $this->db->get('pemeriksaan_lab')->result_array();
    
    return $stats;
}


public function get_petugas_info_safe($petugas_id)
{
    if (!$petugas_id) {
        return array('nama_petugas' => 'N/A');
    }
    
    $this->db->select('nama_petugas');
    $this->db->where('petugas_id', $petugas_id);
    $result = $this->db->get('petugas_lab')->row_array();
    
    return $result ?: array('nama_petugas' => 'N/A');
}

  public function get_reagent_inventory($filters = array()) {
        $this->db->select('*');
        $this->db->from('reagen');
        
        if (isset($filters['status'])) {
            if ($filters['status'] == 'alert') {
                $this->db->where('(status = "Hampir Habis" OR status = "Kadaluarsa" OR jumlah_stok <= stok_minimal)');
            } elseif ($filters['status'] == 'normal') {
                $this->db->where('status = "Tersedia" AND jumlah_stok > stok_minimal');
            } elseif ($filters['status'] == 'low_stock') {
                $this->db->where('jumlah_stok <=', 'stok_minimal', FALSE);
            } elseif ($filters['status'] == 'expired') {
                $this->db->where('status', 'Kadaluarsa');
            } else {
                $this->db->where('status', $filters['status']);
            }
        }
        
        if (isset($filters['location']) && $filters['location']) {
            $this->db->where('lokasi_penyimpanan', $filters['location']);
        }
        
        if (isset($filters['expired_soon']) && $filters['expired_soon']) {
            $this->db->where('expired_date <=', date('Y-m-d', strtotime("+{$filters['expired_soon']} days")));
        }
        
        if (isset($filters['search']) && $filters['search']) {
            $this->db->group_start();
            $this->db->like('nama_reagen', $filters['search']);
            $this->db->or_like('kode_unik', $filters['search']);
            $this->db->or_like('lokasi_penyimpanan', $filters['search']);
            $this->db->group_end();
        }
        
        $this->db->order_by('nama_reagen', 'ASC');
        return $this->db->get()->result_array();
    }

    /**
     * Get equipment inventory with filters
     */
    public function get_equipment_inventory($filters = array()) {
        $this->db->select('*');
        $this->db->from('alat_laboratorium');
        
        if (isset($filters['status'])) {
            if ($filters['status'] == 'alert') {
                $this->db->where('(status_alat = "Perlu Kalibrasi" OR status_alat = "Rusak" OR jadwal_kalibrasi <= CURDATE())');
            } elseif ($filters['status'] == 'normal') {
                $this->db->where('status_alat = "Normal" AND (jadwal_kalibrasi IS NULL OR jadwal_kalibrasi > CURDATE())');
            } elseif ($filters['status'] == 'critical') {
                $this->db->where('status_alat', 'Rusak');
            } else {
                $this->db->where('status_alat', $filters['status']);
            }
        }
        
        if (isset($filters['location']) && $filters['location']) {
            $this->db->where('lokasi', $filters['location']);
        }
        
        if (isset($filters['maintenance_due']) && $filters['maintenance_due']) {
            $this->db->where('jadwal_kalibrasi <=', date('Y-m-d'));
        }
        
        if (isset($filters['search']) && $filters['search']) {
            $this->db->group_start();
            $this->db->like('nama_alat', $filters['search']);
            $this->db->or_like('kode_unik', $filters['search']);
            $this->db->or_like('merek_model', $filters['search']);
            $this->db->or_like('lokasi', $filters['search']);
            $this->db->group_end();
        }
        
        $this->db->order_by('nama_alat', 'ASC');
        return $this->db->get()->result_array();
    }

    /**
     * Get single reagent by ID
     */
    public function get_reagent_by_id($reagent_id) {
        $this->db->where('reagen_id', $reagent_id);
        return $this->db->get('reagen')->row_array();
    }

    /**
     * Get single equipment by ID
     */
    public function get_equipment_by_id($equipment_id) {
        $this->db->where('alat_id', $equipment_id);
        return $this->db->get('alat_laboratorium')->row_array();
    }

    /**
     * Create new reagent
     */
    public function create_reagent($data) {
        // Auto-determine status based on stock and expiry
        if (isset($data['jumlah_stok']) && isset($data['stok_minimal'])) {
            if ($data['jumlah_stok'] <= 0) {
                $data['status'] = 'Habis';
            } elseif ($data['jumlah_stok'] <= $data['stok_minimal']) {
                $data['status'] = 'Hampir Habis';
            } else {
                $data['status'] = 'Tersedia';
            }
        }
        
        // Check expiry date
        if (isset($data['expired_date']) && $data['expired_date']) {
            if (strtotime($data['expired_date']) <= time()) {
                $data['status'] = 'Kadaluarsa';
            }
        }
        
        return $this->db->insert('reagen', $data);
    }

    /**
     * Update reagent
     */
    public function update_reagent($reagent_id, $data) {
        // Auto-determine status based on stock and expiry
        if (isset($data['jumlah_stok']) && isset($data['stok_minimal'])) {
            if ($data['jumlah_stok'] <= 0) {
                $data['status'] = 'Habis';
            } elseif ($data['jumlah_stok'] <= $data['stok_minimal']) {
                $data['status'] = 'Hampir Habis';
            } else {
                $data['status'] = 'Tersedia';
            }
        }
        
        // Check expiry date
        if (isset($data['expired_date']) && $data['expired_date']) {
            if (strtotime($data['expired_date']) <= time()) {
                $data['status'] = 'Kadaluarsa';
            }
        }
        
        $this->db->where('reagen_id', $reagent_id);
        return $this->db->update('reagen', $data);
    }

    /**
     * Create new equipment
     */
    public function create_equipment($data) {
        return $this->db->insert('alat_laboratorium', $data);
    }

    /**
     * Update equipment
     */
    public function update_equipment($equipment_id, $data) {
        $this->db->where('alat_id', $equipment_id);
        return $this->db->update('alat_laboratorium', $data);
    }

    /**
     * Update reagent stock (existing method)
     */
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
        
        $data['updated_at'] = wib_now();
        
        $this->db->where('reagen_id', $reagent_id);
        return $this->db->update('reagen', $data);
    }

    /**
     * Update equipment status (existing method)
     */
    public function update_equipment_status($equipment_id, $data) {
        $data['updated_at'] = wib_now();
        
        $this->db->where('alat_id', $equipment_id);
        return $this->db->update('alat_laboratorium', $data);
    }

    /**
     * Get inventory alerts
     */
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
        $this->db->where('expired_date <=', date('Y-m-d'));
        $expired = $this->db->get('reagen')->result_array();
        
        foreach ($expired as $reagent) {
            $alerts[] = array(
                'type' => 'expired',
                'severity' => 'urgent',
                'item' => $reagent['nama_reagen'],
                'expired_date' => $reagent['expired_date'],
                'message' => "Reagen {$reagent['nama_reagen']} telah kadaluarsa"
            );
        }
        
        // Soon to expire reagents (within 30 days)
        $this->db->select('nama_reagen, expired_date');
        $this->db->where('expired_date <=', date('Y-m-d', strtotime('+30 days')));
        $this->db->where('expired_date >', date('Y-m-d'));
        $soon_expired = $this->db->get('reagen')->result_array();
        
        foreach ($soon_expired as $reagent) {
            $days_left = ceil((strtotime($reagent['expired_date']) - time()) / (60 * 60 * 24));
            $alerts[] = array(
                'type' => 'expiring_soon',
                'severity' => 'warning',
                'item' => $reagent['nama_reagen'],
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
        
        // Equipment broken
        $this->db->select('nama_alat');
        $this->db->where('status_alat', 'Rusak');
        $broken_equipment = $this->db->get('alat_laboratorium')->result_array();
        
        foreach ($broken_equipment as $equipment) {
            $alerts[] = array(
                'type' => 'broken_equipment',
                'severity' => 'urgent',
                'item' => $equipment['nama_alat'],
                'message' => "Peralatan {$equipment['nama_alat']} dalam kondisi rusak"
            );
        }
        
        return $alerts;
    }

    // ==========================================
    // EXISTING METHODS (kept as is)
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
public function validate_examination_result_enhanced($examination_id, $validator_id = null, $notes = null) {
    $this->db->trans_start();
    
    try {
        // Check examination dengan lock menggunakan query manual
        $examination_query = $this->db->query(
            "SELECT * FROM pemeriksaan_lab WHERE pemeriksaan_id = ? FOR UPDATE", 
            [$examination_id]
        );
        $examination = $examination_query->row_array();
        
        if (!$examination) {
            $this->db->trans_rollback();
            throw new Exception("Examination not found: {$examination_id}");
        }
        
        // Check if already validated
        if ($examination['status_pemeriksaan'] === 'selesai') {
            $this->db->trans_complete();
            return true;
        }

        // Update examination status
        $update_data = array(
            'status_pemeriksaan' => 'selesai',
            'completed_at' => wib_now(),
            'updated_at' => wib_now()
        );
        
        $this->db->where('pemeriksaan_id', $examination_id);
        $update_result = $this->db->update('pemeriksaan_lab', $update_data);
        
        if (!$update_result) {
            $this->db->trans_rollback();
            throw new Exception("Failed to update examination status");
        }
        
        // Create invoice - dengan pengecekan yang lebih ketat
        $invoice_id = null;
        
        // Check existing invoice dengan lock
        $invoice_query = $this->db->query(
            "SELECT * FROM invoice WHERE pemeriksaan_id = ? FOR UPDATE", 
            [$examination_id]
        );
        $existing_invoice = $invoice_query->row_array();
        
        if (!$existing_invoice) {
            $invoice_id = $this->create_invoice_after_validation($examination_id, $validator_id);
            
            if (!$invoice_id) {
                log_message('error', "Failed to create invoice for examination: {$examination_id}");
                // Jangan rollback hanya karena gagal buat invoice
            }
        } else {
            $invoice_id = $existing_invoice['invoice_id'];
            log_message('info', "Invoice already exists for examination: {$examination_id}");
        }
        
        // Add timeline entry
        if ($validator_id) {
            $timeline_notes = $notes ?: 'Hasil pemeriksaan telah divalidasi dan siap diserahkan';
            if ($invoice_id) {
                $timeline_notes .= ' - Invoice telah dibuat';
            }
            
            $this->add_sample_timeline(
                $examination_id, 
                'Hasil Divalidasi', 
                $timeline_notes, 
                $validator_id
            );
        }
        
        $this->db->trans_complete();
        
        if ($this->db->trans_status() === FALSE) {
            throw new Exception("Transaction failed");
        }
        
        return true;
        
    } catch (Exception $e) {
        $this->db->trans_rollback();
        log_message('error', 'Exception in validate_examination_result_enhanced: ' . $e->getMessage());
        return false;
    }
}

public function create_invoice_after_validation($examination_id, $user_id = null)
{
    try {
        // Get examination data
        $examination = $this->db->get_where('pemeriksaan_lab', ['pemeriksaan_id' => $examination_id])->row_array();
        
        if (!$examination) {
            log_message('error', "Examination not found for invoice creation: {$examination_id}");
            return false;
        }
        
        // Check if invoice already exists (lagi untuk safety)
        $existing_invoice = $this->db->get_where('invoice', ['pemeriksaan_id' => $examination_id])->row_array();
        
        if ($existing_invoice) {
            log_message('info', "Invoice already exists for examination: {$examination_id}");
            return $existing_invoice['invoice_id'];
        }
        
        // Generate invoice number
        $invoice_number = $this->generate_invoice_number();
        
        // Determine payment type
        $jenis_pembayaran = 'umum';
        if (isset($examination['jenis_pembayaran'])) {
            $jenis_pembayaran = $examination['jenis_pembayaran'];
        }
        
        // Calculate biaya
        $biaya = $examination['biaya'] ? floatval($examination['biaya']) : 0;
        
        $invoice_data = array(
            'pemeriksaan_id' => $examination_id,
            'nomor_invoice' => $invoice_number,
            'tanggal_invoice' => date('Y-m-d'),
            'jenis_pembayaran' => $jenis_pembayaran,
            'total_biaya' => $biaya,
            'status_pembayaran' => 'belum_bayar',
            'metode_pembayaran' => NULL,
            'nomor_kartu_bpjs' => NULL,
            'nomor_sep' => NULL,
            'tanggal_pembayaran' => NULL,
            'keterangan' => 'Invoice generated automatically after QC validation',
            'created_by' => $user_id,
            'created_at' => wib_now()
        );
        
        // Insert invoice
        $insert_result = $this->db->insert('invoice', $invoice_data);
        
        if (!$insert_result) {
            $error = $this->db->error();
            log_message('error', "Database error creating invoice: " . print_r($error, true));
            return false;
        }
        
        $invoice_id = $this->db->insert_id();
        
        log_message('info', "Invoice created successfully: {$invoice_id} for examination: {$examination_id}");
        return $invoice_id;
        
    } catch (Exception $e) {
        log_message('error', "Exception in create_invoice_after_validation: " . $e->getMessage());
        return false;
    }
}

public function get_invoice_number_by_id($invoice_id)
{
    $this->db->select('nomor_invoice');
    $this->db->where('invoice_id', $invoice_id);
    $result = $this->db->get('invoice')->row_array();
    return $result ? $result['nomor_invoice'] : 'N/A';
}
    public function validate_examination_result($examination_id, $validator_id = null) {
        $data = array(
            'status_pemeriksaan' => 'selesai',
            'updated_at' =>wib_now(),
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

    public function get_lab_dashboard_stats() {
        $stats = array();
        
        // Pending requests
        $stats['pending_requests'] = $this->db->where('status_pemeriksaan', 'pending')->count_all_results('pemeriksaan_lab');
        
        // Samples in progress
        $stats['samples_in_progress'] = $this->db->where('status_pemeriksaan', 'progress')->count_all_results('pemeriksaan_lab');
        
        // Completed tests today
        $this->db->select('COUNT(*) as count');
        $this->db->where('status_pemeriksaan', 'selesai');
        $this->db->where('DATE(completed_at)', date('Y-m-d'));
        $result = $this->db->get('pemeriksaan_lab')->row_array();
        $stats['completed_today'] = $result['count'];
        
        // This month completed
        $this->db->select('COUNT(*) as count');
        $this->db->where('status_pemeriksaan', 'selesai');
        $this->db->where("DATE_FORMAT(completed_at, '%Y-%m') = ", date('Y-m'), FALSE);
        $result = $this->db->get('pemeriksaan_lab')->row_array();
        $stats['completed_this_month'] = $result['count'];
        
        // Low stock items
        $stats['low_stock_items'] = $this->db->where('jumlah_stok <=', 'stok_minimal', FALSE)->count_all_results('reagen');
        
        // Equipment needing maintenance
        $stats['equipment_maintenance_due'] = $this->db->where('jadwal_kalibrasi <=', date('Y-m-d'))->count_all_results('alat_laboratorium');
        
        return $stats;
    }

    // ==========================================
    // INCOMING REQUESTS - ENHANCED
    // ==========================================

    public function get_incoming_requests_paginated($filters = array(), $limit = 20, $offset = 0) {
        $this->db->select('pl.*, p.nama as nama_pasien, p.nik, p.jenis_kelamin, p.umur, 
                          p.telepon, p.alamat_domisili, p.pekerjaan, p.dokter_perujuk, p.asal_rujukan,
                          p.diagnosis_awal, p.rekomendasi_pemeriksaan,
                          TIMESTAMPDIFF(HOUR, pl.created_at, NOW()) as hours_waiting');
        $this->db->from('pemeriksaan_lab pl');
        $this->db->join('pasien p', 'pl.pasien_id = p.pasien_id');
        $this->db->where('pl.status_pemeriksaan', 'pending');
        
        // Apply filters
        if (isset($filters['date_from']) && $filters['date_from']) {
            $this->db->where('DATE(pl.tanggal_pemeriksaan) >=', $filters['date_from']);
        }
        
        if (isset($filters['date_to']) && $filters['date_to']) {
            $this->db->where('DATE(pl.tanggal_pemeriksaan) <=', $filters['date_to']);
        }
        
        if (isset($filters['jenis_pemeriksaan']) && $filters['jenis_pemeriksaan']) {
            $this->db->where('pl.jenis_pemeriksaan', $filters['jenis_pemeriksaan']);
        }
        
        if (isset($filters['priority']) && $filters['priority']) {
            switch($filters['priority']) {
                case 'urgent':
                    $this->db->where('TIMESTAMPDIFF(HOUR, pl.created_at, NOW()) >', 24);
                    break;
                case 'high':
                    $this->db->where('TIMESTAMPDIFF(HOUR, pl.created_at, NOW()) BETWEEN 12 AND 24');
                    break;
                case 'normal':
                    $this->db->where('TIMESTAMPDIFF(HOUR, pl.created_at, NOW()) <', 12);
                    break;
            }
        }
        
        if (isset($filters['search']) && $filters['search']) {
            $this->db->group_start();
            $this->db->like('p.nama', $filters['search']);
            $this->db->or_like('p.nik', $filters['search']);
            $this->db->or_like('pl.nomor_pemeriksaan', $filters['search']);
            $this->db->group_end();
        }
        
        $this->db->order_by('pl.created_at', 'ASC');
        $this->db->limit($limit, $offset);
        
        return $this->db->get()->result_array();
    }

    public function count_incoming_requests($filters = array()) {
        $this->db->from('pemeriksaan_lab pl');
        $this->db->join('pasien p', 'pl.pasien_id = p.pasien_id');
        $this->db->where('pl.status_pemeriksaan', 'pending');
        
        // Apply same filters
        if (isset($filters['date_from']) && $filters['date_from']) {
            $this->db->where('DATE(pl.tanggal_pemeriksaan) >=', $filters['date_from']);
        }
        
        if (isset($filters['date_to']) && $filters['date_to']) {
            $this->db->where('DATE(pl.tanggal_pemeriksaan) <=', $filters['date_to']);
        }
        
        if (isset($filters['jenis_pemeriksaan']) && $filters['jenis_pemeriksaan']) {
            $this->db->where('pl.jenis_pemeriksaan', $filters['jenis_pemeriksaan']);
        }
        
        if (isset($filters['priority']) && $filters['priority']) {
            switch($filters['priority']) {
                case 'urgent':
                    $this->db->where('TIMESTAMPDIFF(HOUR, pl.created_at, NOW()) >', 24);
                    break;
                case 'high':
                    $this->db->where('TIMESTAMPDIFF(HOUR, pl.created_at, NOW()) BETWEEN 12 AND 24');
                    break;
                case 'normal':
                    $this->db->where('TIMESTAMPDIFF(HOUR, pl.created_at, NOW()) <', 12);
                    break;
            }
        }
        
        if (isset($filters['search']) && $filters['search']) {
            $this->db->group_start();
            $this->db->like('p.nama', $filters['search']);
            $this->db->or_like('p.nik', $filters['search']);
            $this->db->or_like('pl.nomor_pemeriksaan', $filters['search']);
            $this->db->group_end();
        }
        
        return $this->db->count_all_results();
    }


    public function count_samples_data($filters = array()) {
        $this->db->from('pemeriksaan_lab pl');
        $this->db->join('pasien p', 'pl.pasien_id = p.pasien_id');
        
        $status = isset($filters['status']) ? $filters['status'] : 'progress';
        $this->db->where('pl.status_pemeriksaan', $status);
        
        // Apply same filters as above
        if (isset($filters['date_from']) && $filters['date_from']) {
            $this->db->where('DATE(pl.tanggal_pemeriksaan) >=', $filters['date_from']);
        }
        
        if (isset($filters['date_to']) && $filters['date_to']) {
            $this->db->where('DATE(pl.tanggal_pemeriksaan) <=', $filters['date_to']);
        }
        
        if (isset($filters['jenis_pemeriksaan']) && $filters['jenis_pemeriksaan']) {
            $this->db->where('pl.jenis_pemeriksaan', $filters['jenis_pemeriksaan']);
        }
        
        if (isset($filters['petugas_id']) && $filters['petugas_id']) {
            $this->db->where('pl.petugas_id', $filters['petugas_id']);
        }
        
        if (isset($filters['search']) && $filters['search']) {
            $this->db->group_start();
            $this->db->like('p.nama', $filters['search']);
            $this->db->or_like('p.nik', $filters['search']);
            $this->db->or_like('pl.nomor_pemeriksaan', $filters['search']);
            $this->db->group_end();
        }
        
        return $this->db->count_all_results();
    }


    public function accept_examination_request($examination_id, $petugas_id) {
        $data = array(
            'status_pemeriksaan' => 'progress',
            'petugas_id' => $petugas_id,
            'started_at' =>wib_now(),
            'updated_at' => wib_now()
        );
        
        $this->db->where('pemeriksaan_id', $examination_id);
        return $this->db->update('pemeriksaan_lab', $data);
    }

    public function update_sample_status($examination_id, $status, $notes = null) {
        $data = array(
            'status_pemeriksaan' => $status,
            'updated_at' => wib_now()
        );
        
        if ($status == 'selesai') {
            $data['completed_at'] = wib_now();
        }
        
        if ($notes) {
            $data['keterangan'] = $notes;
        }
        
        $this->db->where('pemeriksaan_id', $examination_id);
        return $this->db->update('pemeriksaan_lab', $data);
    }

    // ==========================================
    // TIMELINE MANAGEMENT - ENHANCED
    // ==========================================

    public function get_sample_timeline($examination_id) {
        $this->db->select('tp.*, pt.nama_petugas');
        $this->db->from('timeline_progres tp');
        $this->db->join('petugas_lab pt', 'tp.petugas_id = pt.petugas_id', 'left');
        $this->db->where('tp.pemeriksaan_id', $examination_id);
        $this->db->order_by('tp.tanggal_update', 'DESC');
        return $this->db->get()->result_array();
    }

    public function add_sample_timeline($examination_id, $status, $keterangan, $petugas_id) {
        $data = array(
            'pemeriksaan_id' => $examination_id,
            'status' => $status,
            'keterangan' => $keterangan,
            'petugas_id' => $petugas_id,
            'tanggal_update' => wib_now()
        );
        
        return $this->db->insert('timeline_progres', $data);
    }

    public function get_latest_timeline_status($examination_id) {
        $this->db->select('status, keterangan, tanggal_update');
        $this->db->where('pemeriksaan_id', $examination_id);
        $this->db->order_by('tanggal_update', 'DESC');
        $this->db->limit(1);
        $result = $this->db->get('timeline_progres')->row_array();
        
        return $result ? $result : array('status' => 'Belum ada update', 'keterangan' => '', 'tanggal_update' => '');
    }

    public function get_timeline_by_id($timeline_id) {
        $this->db->select('tp.*, pt.nama_petugas, pl.nomor_pemeriksaan');
        $this->db->from('timeline_progres tp');
        $this->db->join('petugas_lab pt', 'tp.petugas_id = pt.petugas_id', 'left');
        $this->db->join('pemeriksaan_lab pl', 'tp.pemeriksaan_id = pl.pemeriksaan_id');
        $this->db->where('tp.timeline_id', $timeline_id);
        return $this->db->get()->row_array();
    }

    public function update_timeline_entry($timeline_id, $data) {
        $this->db->where('timeline_id', $timeline_id);
        return $this->db->update('timeline_progres', $data);
    }

    public function delete_timeline_entry($timeline_id) {
        $this->db->where('timeline_id', $timeline_id);
        return $this->db->delete('timeline_progres');
    }

    // ==========================================
    // HELPER METHODS
    // ==========================================

    public function get_priority_level($hours_waiting) {
        if ($hours_waiting > 24) {
            return array('level' => 'urgent', 'label' => 'MENDESAK', 'color' => 'red');
        } elseif ($hours_waiting > 12) {
            return array('level' => 'high', 'label' => 'TINGGI', 'color' => 'orange');
        } elseif ($hours_waiting > 6) {
            return array('level' => 'medium', 'label' => 'SEDANG', 'color' => 'yellow');
        } else {
            return array('level' => 'normal', 'label' => 'NORMAL', 'color' => 'blue');
        }
    }

    public function get_examination_type_options() {
        return array(
            'Kimia Darah' => 'Kimia Darah',
            'Hematologi' => 'Hematologi', 
            'Urinologi' => 'Urinologi',
            'Serologi' => 'Serologi Imunologi',
            'TBC' => 'TBC',
            'IMS' => 'IMS',
            'MLS' => 'MLS (Lainnya)'
        );
    }

    public function get_all_petugas_lab() {
        $this->db->select('petugas_id, nama_petugas');
        $this->db->order_by('nama_petugas', 'ASC');
        return $this->db->get('petugas_lab')->result_array();
    }

    public function get_petugas_id_by_user_id($user_id) {
        $this->db->select('petugas_id');
        $this->db->where('user_id', $user_id);
        $result = $this->db->get('petugas_lab')->row_array();
        
        return $result ? $result['petugas_id'] : null;
    }

    public function get_lab_performance_stats($period = '30') {
        $stats = array();
        
        // Daily completion trend
        $this->db->select('DATE(completed_at) as date, COUNT(*) as completed');
        $this->db->where('status_pemeriksaan', 'selesai');
        $this->db->where('completed_at >=', date('Y-m-d', strtotime("-{$period} days")));
        $this->db->group_by('DATE(completed_at)');
        $this->db->order_by('date', 'ASC');
        $stats['daily_completions'] = $this->db->get('pemeriksaan_lab')->result_array();
        
        // Test type distribution
        $this->db->select('jenis_pemeriksaan, COUNT(*) as count');
        $this->db->where('tanggal_pemeriksaan >=', date('Y-m-d', strtotime("-{$period} days")));
        $this->db->group_by('jenis_pemeriksaan');
        $this->db->order_by('count', 'DESC');
        $stats['test_distribution'] = $this->db->get('pemeriksaan_lab')->result_array();
        
        // Average processing time
        $this->db->select('AVG(TIMESTAMPDIFF(HOUR, started_at, completed_at)) as avg_hours');
        $this->db->where('status_pemeriksaan', 'selesai');
        $this->db->where('started_at IS NOT NULL');
        $this->db->where('completed_at IS NOT NULL');
        $this->db->where('created_at >=', date('Y-m-d', strtotime("-{$period} days")));
        $result = $this->db->get('pemeriksaan_lab')->row_array();
        $stats['avg_processing_hours'] = round($result['avg_hours'], 1);
        
        return $stats;
    }

    public function get_existing_results($examination_id, $jenis_pemeriksaan) {
        $results = null;
        
        switch (strtolower($jenis_pemeriksaan)) {
            case 'kimia darah':
                $results = $this->get_kimia_darah_results($examination_id);
                break;
            case 'hematologi':
                $results = $this->get_hematologi_results($examination_id);
                break;
            case 'urinologi':
                $results = $this->get_urinologi_results($examination_id);
                break;
            case 'serologi':
            case 'serologi imunologi':
                $results = $this->get_serologi_results($examination_id);
                break;
            case 'tbc':
                $results = $this->get_tbc_results($examination_id);
                break;
            case 'ims':
                $results = $this->get_ims_results($examination_id);
                break;
            case 'mls':
                $results = $this->get_mls_results($examination_id);
                break;
        }
        
        return $results;
    }

    // Save or update methods
    public function save_or_update_kimia_darah_results($examination_id, $data) {
        $existing = $this->get_kimia_darah_results($examination_id);
        
        if ($existing) {
            $this->db->where('pemeriksaan_id', $examination_id);
            return $this->db->update('kimia_darah', $data);
        } else {
            return $this->db->insert('kimia_darah', $data);
        }
    }

    public function save_or_update_hematologi_results($examination_id, $data) {
        $existing = $this->get_hematologi_results($examination_id);
        
        if ($existing) {
            $this->db->where('pemeriksaan_id', $examination_id);
            return $this->db->update('hematologi', $data);
        } else {
            return $this->db->insert('hematologi', $data);
        }
    }

    public function save_or_update_urinologi_results($examination_id, $data) {
        $existing = $this->get_urinologi_results($examination_id);
        
        if ($existing) {
            $this->db->where('pemeriksaan_id', $examination_id);
            return $this->db->update('urinologi', $data);
        } else {
            return $this->db->insert('urinologi', $data);
        }
    }

    public function save_or_update_serologi_results($examination_id, $data) {
        $existing = $this->get_serologi_results($examination_id);
        
        if ($existing) {
            $this->db->where('pemeriksaan_id', $examination_id);
            return $this->db->update('serologi_imunologi', $data);
        } else {
            return $this->db->insert('serologi_imunologi', $data);
        }
    }

    public function save_or_update_tbc_results($examination_id, $data) {
        $existing = $this->get_tbc_results($examination_id);
        
        if ($existing) {
            $this->db->where('pemeriksaan_id', $examination_id);
            return $this->db->update('tbc', $data);
        } else {
            return $this->db->insert('tbc', $data);
        }
    }

    public function save_or_update_ims_results($examination_id, $data) {
        $existing = $this->get_ims_results($examination_id);
        
        if ($existing) {
            $this->db->where('pemeriksaan_id', $examination_id);
            return $this->db->update('ims', $data);
        } else {
            return $this->db->insert('ims', $data);
        }
    }

    public function save_or_update_mls_results($examination_id, $data) {
        $existing = $this->get_mls_results($examination_id);
        
        if ($existing) {
            $this->db->where('pemeriksaan_id', $examination_id);
            return $this->db->update('mls', $data);
        } else {
            return $this->db->insert('mls', $data);
        }
    }

    public function get_formatted_results_by_examination($examination_id) {
        $examination = $this->get_examination_by_id($examination_id);
        if (!$examination) {
            return null;
        }
        
        $jenis_pemeriksaan = strtolower($examination['jenis_pemeriksaan']);
        $formatted_results = array();
        
        switch ($jenis_pemeriksaan) {
            case 'kimia darah':
                $results = $this->get_kimia_darah_results($examination_id);
                if ($results) {
                    $formatted_results = array(
                        'Gula Darah Sewaktu' => $results['gula_darah_sewaktu'] ? $results['gula_darah_sewaktu'] . ' mg/dL' : '-',
                        'Gula Darah Puasa' => $results['gula_darah_puasa'] ? $results['gula_darah_puasa'] . ' mg/dL' : '-',
                        'Gula Darah 2J PP' => $results['gula_darah_2jam_pp'] ? $results['gula_darah_2jam_pp'] . ' mg/dL' : '-',
                        'Kolesterol Total' => $results['cholesterol_total'] ? $results['cholesterol_total'] . ' mg/dL' : '-',
                        'Kolesterol HDL' => $results['cholesterol_hdl'] ? $results['cholesterol_hdl'] . ' mg/dL' : '-',
                        'Kolesterol LDL' => $results['cholesterol_ldl'] ? $results['cholesterol_ldl'] . ' mg/dL' : '-',
                        'Trigliserida' => $results['trigliserida'] ? $results['trigliserida'] . ' mg/dL' : '-',
                        'Asam Urat' => $results['asam_urat'] ? $results['asam_urat'] . ' mg/dL' : '-',
                        'Ureum' => $results['ureum'] ? $results['ureum'] . ' mg/dL' : '-',
                        'Kreatinin' => $results['creatinin'] ? $results['creatinin'] . ' mg/dL' : '-',
                        'SGPT' => $results['sgpt'] ? $results['sgpt'] . ' U/L' : '-',
                        'SGOT' => $results['sgot'] ? $results['sgot'] . ' U/L' : '-'
                    );
                }
                break;
                
                   
            case 'hematologi':
                $results = $this->get_hematologi_results($examination_id);
                if ($results) {
                    $formatted_results = array(
                        'Hemoglobin' => $results['hemoglobin'] ? $results['hemoglobin'] . ' g/dL' : '-',
                        'Hematokrit' => $results['hematokrit'] ? $results['hematokrit'] . '%' : '-',
                        'Leukosit' => $results['leukosit'] ? $results['leukosit'] . ' /uL' : '-',
                        'Trombosit' => $results['trombosit'] ? $results['trombosit'] . ' /uL' : '-',
                        'Eritrosit' => $results['eritrosit'] ? $results['eritrosit'] . ' juta/uL' : '-',
                        'MCV' => $results['mcv'] ? $results['mcv'] . ' fL' : '-',
                        'MCH' => $results['mch'] ? $results['mch'] . ' pg' : '-',
                        'MCHC' => $results['mchc'] ? $results['mchc'] . ' g/dL' : '-',
                        'Laju Endap Darah' => $results['laju_endap_darah'] ? $results['laju_endap_darah'] . ' mm/jam' : '-',
                        'Clotting Time' => $results['clotting_time'] ? $results['clotting_time'] . ' detik' : '-',
                        'Bleeding Time' => $results['bleeding_time'] ? $results['bleeding_time'] . ' detik' : '-',
                        'Eosinofil' => $results['eosinofil'] ? $results['eosinofil'] . '%' : '-',
                        'Basofil' => $results['basofil'] ? $results['basofil'] . '%' : '-',
                        'Neutrofil' => $results['neutrofil'] ? $results['neutrofil'] . '%' : '-',
                        'Limfosit' => $results['limfosit'] ? $results['limfosit'] . '%' : '-',
                        'Monosit' => $results['monosit'] ? $results['monosit'] . '%' : '-',
                        'Golongan Darah' => $results['golongan_darah'] ?: '-',
                        'Rhesus' => $results['rhesus'] ? ($results['rhesus'] == '+' ? 'Positif' : 'Negatif') : '-',
                        'Malaria' => $results['malaria'] ?: '-'
                    );
                }
                break;
                
            case 'urinologi':
                $results = $this->get_urinologi_results($examination_id);
                if ($results) {
                    $formatted_results = array(
                        'Makroskopis' => $results['makroskopis'] ?: '-',
                        'Mikroskopis' => $results['mikroskopis'] ?: '-',
                        'Berat Jenis' => $results['berat_jenis'] ?: '-',
                        'pH Kimia' => $results['kimia_ph'] ?: '-',
                        'Protein' => $results['protein'] ?: '-',
                        'Glukosa' => $results['glukosa'] ?: '-',
                        'Keton' => $results['keton'] ?: '-',
                        'Bilirubin' => $results['bilirubin'] ?: '-',
                        'Urobilinogen' => $results['urobilinogen'] ?: '-',
                        'Tes Kehamilan' => $results['tes_kehamilan'] ?: '-'
                    );
                }
                break;
                
            case 'serologi':
            case 'serologi imunologi':
                $results = $this->get_serologi_results($examination_id);
                if ($results) {
                    $formatted_results = array(
                        'RDT Antigen' => $results['rdt_antigen'] ?: '-',
                        'Widal' => $results['widal'] ?: '-',
                        'HbsAg' => $results['hbsag'] ?: '-',
                        'NS1' => $results['ns1'] ?: '-',
                        'HIV' => $results['hiv'] ?: '-'
                    );
                }
                break;
                
            case 'tbc':
                $results = $this->get_tbc_results($examination_id);
                if ($results) {
                    $formatted_results = array(
                        'Dahak' => $results['dahak'] ?: '-',
                        'TCM' => $results['tcm'] ?: '-'
                    );
                }
                break;
                
            case 'ims':
                $results = $this->get_ims_results($examination_id);
                if ($results) {
                    $formatted_results = array(
                        'Sifilis' => $results['sifilis'] ?: '-',
                        'Duh Tubuh' => $results['duh_tubuh'] ?: '-'
                    );
                }
                break;
                
            case 'mls':
                $results = $this->get_mls_results($examination_id);
                if ($results && !empty($results)) {
                    foreach ($results as $result) {
                        $formatted_results[$result['jenis_tes']] = $result['hasil'] . 
                            ($result['satuan'] ? ' ' . $result['satuan'] : '') . 
                            ($result['nilai_rujukan'] ? ' (Normal: ' . $result['nilai_rujukan'] . ')' : '');
                    }
                }
                break;
        }
        
        return $formatted_results;
    }

    public function get_results_pending_validation_enhanced() {
        $this->db->select('pl.*, p.nama as nama_pasien, p.nik, 
                          COALESCE(pt.nama_petugas, "N/A") as nama_petugas,
                          TIMESTAMPDIFF(HOUR, pl.updated_at, NOW()) as hours_waiting');
        $this->db->from('pemeriksaan_lab pl');
        $this->db->join('pasien p', 'pl.pasien_id = p.pasien_id', 'left');
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

    public function get_recent_validations_enhanced($limit = 10) {
        $this->db->select('pl.*, p.nama as nama_pasien, p.nik,
                          COALESCE(pt.nama_petugas, "System") as nama_petugas');
        $this->db->from('pemeriksaan_lab pl');
        $this->db->join('pasien p', 'pl.pasien_id = p.pasien_id', 'left');
        $this->db->join('petugas_lab pt', 'pl.petugas_id = pt.petugas_id', 'left');
        $this->db->where('pl.status_pemeriksaan', 'selesai');
        $this->db->where('DATE(pl.completed_at)', date('Y-m-d'));
        $this->db->order_by('pl.completed_at', 'DESC');
        $this->db->limit($limit);
        
        return $this->db->get()->result_array();
    }


    public function get_examinations_ready_for_results_enhanced($petugas_id = null) {
        $this->db->select('pl.*, p.nama as nama_pasien, p.nik, p.jenis_kelamin, p.umur,
                          CASE 
                            WHEN EXISTS (SELECT 1 FROM kimia_darah WHERE pemeriksaan_id = pl.pemeriksaan_id) THEN 1
                            WHEN EXISTS (SELECT 1 FROM hematologi WHERE pemeriksaan_id = pl.pemeriksaan_id) THEN 1
                            WHEN EXISTS (SELECT 1 FROM urinologi WHERE pemeriksaan_id = pl.pemeriksaan_id) THEN 1
                            WHEN EXISTS (SELECT 1 FROM serologi_imunologi WHERE pemeriksaan_id = pl.pemeriksaan_id) THEN 1
                            WHEN EXISTS (SELECT 1 FROM tbc WHERE pemeriksaan_id = pl.pemeriksaan_id) THEN 1
                            WHEN EXISTS (SELECT 1 FROM ims WHERE pemeriksaan_id = pl.pemeriksaan_id) THEN 1
                            WHEN EXISTS (SELECT 1 FROM mls WHERE pemeriksaan_id = pl.pemeriksaan_id) THEN 1
                            ELSE 0
                          END as has_results');
        $this->db->from('pemeriksaan_lab pl');
        $this->db->join('pasien p', 'pl.pasien_id = p.pasien_id');
        $this->db->where('pl.status_pemeriksaan', 'progress');
        
        if ($petugas_id) {
            $this->db->where('pl.petugas_id', $petugas_id);
        }
        
        $this->db->order_by('pl.tanggal_pemeriksaan', 'ASC');
        return $this->db->get()->result_array();
    }


public function get_examination_status_distribution() 
{
    $this->db->select('status_pemeriksaan, COUNT(*) as count');
    $this->db->group_by('status_pemeriksaan');
    $query = $this->db->get('pemeriksaan_lab');
    
    $distribution = array(
        'pending' => 0,
        'progress' => 0,
        'selesai' => 0,
        'cancelled' => 0
    );
    
    foreach ($query->result_array() as $row) {
        $distribution[$row['status_pemeriksaan']] = (int)$row['count'];
    }
    
    return $distribution;
}

/**
 * Get QC dashboard stats
 */
public function get_qc_dashboard_stats() 
{
    $stats = array();
    
    // Pending validation
    $stats['pending_validation'] = $this->count_pending_validation();
    
    // Validated today
    $stats['validated_today'] = $this->count_validated_today();
    
    // Validated this month
    $stats['validated_this_month'] = $this->count_validated_this_month();
    
    // Average validation time (hours)
    $stats['avg_validation_time'] = $this->get_avg_validation_time();
    
    return $stats;
}

private function count_pending_validation() 
{
    $this->db->from('pemeriksaan_lab pl');
    $this->db->where('pl.status_pemeriksaan', 'progress');
    $this->db->where('(
        EXISTS (SELECT 1 FROM kimia_darah WHERE pemeriksaan_id = pl.pemeriksaan_id) OR
        EXISTS (SELECT 1 FROM hematologi WHERE pemeriksaan_id = pl.pemeriksaan_id) OR
        EXISTS (SELECT 1 FROM urinologi WHERE pemeriksaan_id = pl.pemeriksaan_id) OR
        EXISTS (SELECT 1 FROM serologi_imunologi WHERE pemeriksaan_id = pl.pemeriksaan_id) OR
        EXISTS (SELECT 1 FROM tbc WHERE pemeriksaan_id = pl.pemeriksaan_id) OR
        EXISTS (SELECT 1 FROM ims WHERE pemeriksaan_id = pl.pemeriksaan_id) OR
        EXISTS (SELECT 1 FROM mls WHERE pemeriksaan_id = pl.pemeriksaan_id)
    )');
    
    return $this->db->count_all_results();
}

private function count_validated_today() 
{
    $this->db->where('status_pemeriksaan', 'selesai');
    $this->db->where('DATE(completed_at)', date('Y-m-d'));
    return $this->db->count_all_results('pemeriksaan_lab');
}

private function count_validated_this_month() 
{
    $this->db->where('status_pemeriksaan', 'selesai');
    $this->db->where("DATE_FORMAT(completed_at, '%Y-%m') = ", date('Y-m'), FALSE);
    return $this->db->count_all_results('pemeriksaan_lab');
}

private function get_avg_validation_time() 
{
    $this->db->select('AVG(TIMESTAMPDIFF(HOUR, started_at, completed_at)) as avg_hours');
    $this->db->where('status_pemeriksaan', 'selesai');
    $this->db->where('started_at IS NOT NULL');
    $this->db->where('completed_at IS NOT NULL');
    $this->db->where('completed_at >=', date('Y-m-d', strtotime('-30 days')));
    
    $result = $this->db->get('pemeriksaan_lab')->row_array();
    return round($result['avg_hours'] ?: 0, 1);
}

private function generate_invoice_number()
{
    $prefix = 'INV-' . date('Y') . '-';
    
    $this->db->select('nomor_invoice');
    $this->db->like('nomor_invoice', $prefix, 'after');
    $this->db->order_by('nomor_invoice', 'DESC');
    $this->db->limit(1);
    $result = $this->db->get('invoice')->row_array();
    
    if ($result) {
        $last_number = intval(str_replace($prefix, '', $result['nomor_invoice']));
        $new_number = $last_number + 1;
    } else {
        $new_number = 1;
    }
    
    return $prefix . str_pad($new_number, 4, '0', STR_PAD_LEFT);
}

/**
 * Get invoice by examination ID
 */
public function get_invoice_by_examination($examination_id)
{
    $this->db->where('pemeriksaan_id', $examination_id);
    return $this->db->get('invoice')->row_array();
}
public function validate_examination_result_simple($examination_id, $validator_id = null) {
    // Cek status terlebih dahulu
    $examination = $this->db->get_where('pemeriksaan_lab', ['pemeriksaan_id' => $examination_id])->row_array();
    
    if (!$examination) {
        return false;
    }
    
    if ($examination['status_pemeriksaan'] === 'selesai') {
        return true; // Sudah divalidasi
    }
    
    // Update status
    $update_data = array(
        'status_pemeriksaan' => 'selesai',
        'completed_at' => wib_now(),
        'updated_at' => wib_now()
    );
    
    $this->db->where('pemeriksaan_id', $examination_id);
    $update_result = $this->db->update('pemeriksaan_lab', $update_data);
    
    if (!$update_result) {
        return false;
    }
    
    // Buat invoice hanya jika belum ada
    $existing_invoice = $this->db->get_where('invoice', ['pemeriksaan_id' => $examination_id])->row_array();
    if (!$existing_invoice) {
        $this->create_invoice_after_validation($examination_id, $validator_id);
    }
    
    // Add timeline
    if ($validator_id) {
        $this->add_sample_timeline(
            $examination_id, 
            'Hasil Divalidasi', 
            'Hasil pemeriksaan telah divalidasi dan siap diserahkan', 
            $validator_id
        );
    }
    
    return true;
}

/**
 * Get sub pemeriksaan options by jenis pemeriksaan
 * @param string $jenis_pemeriksaan
 * @return array
 */
public function get_sub_pemeriksaan_options($jenis_pemeriksaan) {
    $options = array();
    
    switch (strtolower($jenis_pemeriksaan)) {
        case 'kimia darah':
            $options = array(
                'gula_darah_sewaktu' => array(
                    'label' => 'Gula Darah Sewaktu',
                    'unit' => 'mg/dL',
                    'normal_range' => '70-200',
                    'type' => 'number',
                    'step' => '0.01'
                ),
                'gula_darah_puasa' => array(
                    'label' => 'Gula Darah Puasa',
                    'unit' => 'mg/dL',
                    'normal_range' => '70-110',
                    'type' => 'number',
                    'step' => '0.01'
                ),
                'gula_darah_2jam_pp' => array(
                    'label' => 'Gula Darah 2 Jam PP',
                    'unit' => 'mg/dL',
                    'normal_range' => '< 140',
                    'type' => 'number',
                    'step' => '0.01'
                ),
                'cholesterol_total' => array(
                    'label' => 'Kolesterol Total',
                    'unit' => 'mg/dL',
                    'normal_range' => '< 200',
                    'type' => 'number',
                    'step' => '0.01'
                ),
                'cholesterol_hdl' => array(
                    'label' => 'Kolesterol HDL',
                    'unit' => 'mg/dL',
                    'normal_range' => '> 40',
                    'type' => 'number',
                    'step' => '0.01'
                ),
                'cholesterol_ldl' => array(
                    'label' => 'Kolesterol LDL',
                    'unit' => 'mg/dL',
                    'normal_range' => '< 130',
                    'type' => 'number',
                    'step' => '0.01'
                ),
                'trigliserida' => array(
                    'label' => 'Trigliserida',
                    'unit' => 'mg/dL',
                    'normal_range' => '< 150',
                    'type' => 'number',
                    'step' => '0.01'
                ),
                'asam_urat' => array(
                    'label' => 'Asam Urat',
                    'unit' => 'mg/dL',
                    'normal_range' => 'L: 3.5-7.0, P: 2.5-6.0',
                    'type' => 'number',
                    'step' => '0.01'
                ),
                'ureum' => array(
                    'label' => 'Ureum',
                    'unit' => 'mg/dL',
                    'normal_range' => '10-50',
                    'type' => 'number',
                    'step' => '0.01'
                ),
                'creatinin' => array(
                    'label' => 'Kreatinin',
                    'unit' => 'mg/dL',
                    'normal_range' => 'L: 0.7-1.3, P: 0.6-1.1',
                    'type' => 'number',
                    'step' => '0.01'
                ),
                'sgpt' => array(
                    'label' => 'SGPT',
                    'unit' => 'U/L',
                    'normal_range' => '< 41',
                    'type' => 'number',
                    'step' => '0.01'
                ),
                'sgot' => array(
                    'label' => 'SGOT',
                    'unit' => 'U/L',
                    'normal_range' => '< 37',
                    'type' => 'number',
                    'step' => '0.01'
                )
            );
            break;
            
        case 'hematologi':
            $options = array(
                'paket_darah_rutin' => array(
                    'label' => 'Paket Darah Rutin',
                    'description' => 'Hemoglobin, Hematokrit, Eritrosit, Leukosit, Trombosit',
                    'is_package' => true,
                    'includes' => ['hemoglobin', 'hematokrit', 'eritrosit', 'leukosit', 'trombosit']
                ),
                'hitung_jenis_leukosit' => array(
                    'label' => 'Hitung Jenis Leukosit',
                    'description' => 'Neutrofil, Limfosit, Monosit, Eosinofil, Basofil',
                    'is_package' => true,
                    'includes' => ['neutrofil', 'limfosit', 'monosit', 'eosinofil', 'basofil']
                ),
                'laju_endap_darah' => array(
                    'label' => 'Laju Endap Darah (LED)',
                    'unit' => 'mm/jam',
                    'normal_range' => 'L: < 15, P: < 20',
                    'type' => 'number',
                    'step' => '0.1'
                ),
                'golongan_darah' => array(
                    'label' => 'Golongan Darah & Rhesus',
                    'is_package' => true,
                    'includes' => ['golongan_darah', 'rhesus']
                ),
                'hemostasis' => array(
                    'label' => 'Hemostasis (CT/BT)',
                    'description' => 'Clotting Time & Bleeding Time',
                    'is_package' => true,
                    'includes' => ['clotting_time', 'bleeding_time']
                ),
                'malaria' => array(
                    'label' => 'Malaria',
                    'type' => 'textarea'
                )
            );
            break;
            
        case 'urinologi':
             case 'urinologi':
            $options = array(
                'urin_rutin' => array(
                    'label' => 'Urin Rutin',
                    'description' => 'Pemeriksaan Fisik, Kimia, dan Mikroskopis',
                    'is_package' => true,
                    'includes' => ['makroskopis', 'mikroskopis', 'berat_jenis', 'kimia_ph', 'protein_regular', 'glukosa', 'keton', 'bilirubin', 'urobilinogen']
                ),
                'protein' => array(  // TAMBAH INI - sub pemeriksaan terpisah
                    'label' => 'Protein Urin (Kuantitatif)',
                    'type' => 'text',
                    'description' => 'Pemeriksaan protein urin secara kuantitatif'
                ),
                'tes_kehamilan' => array(
                    'label' => 'Tes Kehamilan (HCG)',
                    'type' => 'select',
                    'options' => ['', 'Positif', 'Negatif']
                )
            );
            break;
            
        case 'serologi':
        case 'serologi imunologi':
            $options = array(
                'rdt_antigen' => array(
                    'label' => 'RDT Antigen',
                    'type' => 'select',
                    'options' => ['', 'Positif', 'Negatif']
                ),
                'widal' => array(
                    'label' => 'Widal',
                    'type' => 'textarea'
                ),
                'hbsag' => array(
                    'label' => 'HBsAg',
                    'type' => 'select',
                    'options' => ['', 'Reaktif', 'Non-Reaktif']
                ),
                'ns1' => array(
                    'label' => 'NS1 (Dengue)',
                    'type' => 'select',
                    'options' => ['', 'Positif', 'Negatif']
                ),
                'hiv' => array(
                    'label' => 'HIV',
                    'type' => 'select',
                    'options' => ['', 'Reaktif', 'Non-Reaktif']
                )
            );
            break;
            
        case 'tbc':
            $options = array(
                'dahak' => array(
                    'label' => 'Dahak (BTA)',
                    'type' => 'select',
                    'options' => ['', 'Negatif', 'Scanty', '+1', '+2', '+3']
                ),
                'tcm' => array(
                    'label' => 'TCM (GeneXpert)',
                    'type' => 'select',
                    'options' => ['', 'Detected', 'Not Detected']
                )
            );
            break;
            
        case 'ims':
            $options = array(
                'sifilis' => array(
                    'label' => 'Sifilis',
                    'type' => 'select',
                    'options' => ['', 'Reaktif', 'Non-Reaktif']
                ),
                'duh_tubuh' => array(
                    'label' => 'Duh Tubuh',
                    'type' => 'textarea'
                )
            );
            break;
    }
    
    return $options;
}

/**
 * Get detailed field configuration for a specific sub pemeriksaan
 */
public function get_field_details($jenis_pemeriksaan) {
    $all_fields = array();
    
    switch (strtolower($jenis_pemeriksaan)) {
        case 'kimia darah':
            $all_fields = array(
                'gula_darah_sewaktu' => array('label' => 'Gula Darah Sewaktu', 'unit' => 'mg/dL', 'normal' => '70-200'),
                'gula_darah_puasa' => array('label' => 'Gula Darah Puasa', 'unit' => 'mg/dL', 'normal' => '70-110'),
                'gula_darah_2jam_pp' => array('label' => 'Gula Darah 2 Jam PP', 'unit' => 'mg/dL', 'normal' => '< 140'),
                'cholesterol_total' => array('label' => 'Kolesterol Total', 'unit' => 'mg/dL', 'normal' => '< 200'),
                'cholesterol_hdl' => array('label' => 'Kolesterol HDL', 'unit' => 'mg/dL', 'normal' => '> 40'),
                'cholesterol_ldl' => array('label' => 'Kolesterol LDL', 'unit' => 'mg/dL', 'normal' => '< 130'),
                'trigliserida' => array('label' => 'Trigliserida', 'unit' => 'mg/dL', 'normal' => '< 150'),
                'asam_urat' => array('label' => 'Asam Urat', 'unit' => 'mg/dL', 'normal' => 'L: 3.5-7.0, P: 2.5-6.0'),
                'ureum' => array('label' => 'Ureum', 'unit' => 'mg/dL', 'normal' => '10-50'),
                'creatinin' => array('label' => 'Kreatinin', 'unit' => 'mg/dL', 'normal' => 'L: 0.7-1.3, P: 0.6-1.1'),
                'sgpt' => array('label' => 'SGPT', 'unit' => 'U/L', 'normal' => '< 41'),
                'sgot' => array('label' => 'SGOT', 'unit' => 'U/L', 'normal' => '< 37')
            );
            break;
            
        case 'hematologi':
            $all_fields = array(
                'hemoglobin' => array('label' => 'Hemoglobin', 'unit' => 'g/dL', 'normal' => 'L: 13-17, P: 12-15', 'package' => 'paket_darah_rutin'),
                'hematokrit' => array('label' => 'Hematokrit', 'unit' => '%', 'normal' => 'L: 40-50, P: 35-45', 'package' => 'paket_darah_rutin'),
                'eritrosit' => array('label' => 'Eritrosit', 'unit' => 'juta/µL', 'normal' => 'L: 4.5-5.5, P: 4.0-5.0', 'package' => 'paket_darah_rutin'),
                'leukosit' => array('label' => 'Leukosit', 'unit' => 'ribu/µL', 'normal' => '4.0-11.0', 'package' => 'paket_darah_rutin'),
                'trombosit' => array('label' => 'Trombosit', 'unit' => 'ribu/µL', 'normal' => '150-400', 'package' => 'paket_darah_rutin'),
                'mcv' => array('label' => 'MCV', 'unit' => 'fL', 'normal' => '80-100'),
                'mch' => array('label' => 'MCH', 'unit' => 'pg', 'normal' => '27-31'),
                'mchc' => array('label' => 'MCHC', 'unit' => 'g/dL', 'normal' => '32-36'),
                'neutrofil' => array('label' => 'Neutrofil', 'unit' => '%', 'normal' => '50-70', 'package' => 'hitung_jenis_leukosit'),
                'limfosit' => array('label' => 'Limfosit', 'unit' => '%', 'normal' => '20-40', 'package' => 'hitung_jenis_leukosit'),
                'monosit' => array('label' => 'Monosit', 'unit' => '%', 'normal' => '2-8', 'package' => 'hitung_jenis_leukosit'),
                'eosinofil' => array('label' => 'Eosinofil', 'unit' => '%', 'normal' => '1-3', 'package' => 'hitung_jenis_leukosit'),
                'basofil' => array('label' => 'Basofil', 'unit' => '%', 'normal' => '0-1', 'package' => 'hitung_jenis_leukosit'),
                'laju_endap_darah' => array('label' => 'Laju Endap Darah', 'unit' => 'mm/jam', 'normal' => 'L: < 15, P: < 20'),
                'golongan_darah' => array('label' => 'Golongan Darah', 'type' => 'select', 'options' => ['', 'A', 'B', 'AB', 'O'], 'package' => 'golongan_darah'),
                'rhesus' => array('label' => 'Rhesus', 'type' => 'select', 'options' => ['', '+', '-'], 'package' => 'golongan_darah'),
                'clotting_time' => array('label' => 'Clotting Time', 'unit' => 'detik', 'normal' => '5-15 menit', 'package' => 'hemostasis'),
                'bleeding_time' => array('label' => 'Bleeding Time', 'unit' => 'detik', 'normal' => '1-6 menit', 'package' => 'hemostasis'),
                'malaria' => array('label' => 'Malaria', 'type' => 'textarea')
            );
            break;
            
        case 'urinologi':
            $all_fields = array(
                'makroskopis' => array('label' => 'Makroskopis', 'type' => 'textarea', 'package' => 'urin_rutin'),
                'mikroskopis' => array('label' => 'Mikroskopis', 'type' => 'textarea', 'package' => 'urin_rutin'),
                'berat_jenis' => array('label' => 'Berat Jenis', 'normal' => '1.003-1.030', 'package' => 'urin_rutin'),
                'kimia_ph' => array('label' => 'pH', 'normal' => '4.5-8.0', 'package' => 'urin_rutin'),
                'protein' => array('label' => 'Protein', 'type' => 'select', 'options' => ['', 'Negatif', '+1', '+2', '+3', '+4'], 'package' => 'urin_rutin'),
                'glukosa' => array('label' => 'Glukosa', 'type' => 'select', 'options' => ['', 'Negatif', '+1', '+2', '+3', '+4'], 'package' => 'urin_rutin'),
                'keton' => array('label' => 'Keton', 'type' => 'select', 'options' => ['', 'Negatif', '+1', '+2', '+3', '+4'], 'package' => 'urin_rutin'),
                'bilirubin' => array('label' => 'Bilirubin', 'type' => 'select', 'options' => ['', 'Negatif', '+1', '+2', '+3', '+4'], 'package' => 'urin_rutin'),
                'urobilinogen' => array('label' => 'Urobilinogen', 'type' => 'select', 'options' => ['', 'Negatif', '+1', '+2', '+3', '+4'], 'package' => 'urin_rutin'),
                'tes_kehamilan' => array('label' => 'Tes Kehamilan', 'type' => 'select', 'options' => ['', 'Positif', 'Negatif'])
            );
            break;
    }
    
    return $all_fields;
}

public function get_sub_pemeriksaan_labels($sub_pemeriksaan_json, $jenis_pemeriksaan) {
    if (empty($sub_pemeriksaan_json)) {
        return 'Semua pemeriksaan';
    }
    
    $subs = json_decode($sub_pemeriksaan_json, true);
    if (!is_array($subs) || empty($subs)) {
        return 'Semua pemeriksaan';
    }
    
    $options = $this->get_sub_pemeriksaan_options($jenis_pemeriksaan);
    $labels = array();
    
    foreach ($subs as $sub) {
        if (isset($options[$sub])) {
            $labels[] = $options[$sub]['label'];
        }
    }
    
    return !empty($labels) ? implode(', ', $labels) : 'Semua pemeriksaan';
}


public function get_examination_details($examination_id) {
    $this->db->select('pd.*, pl.jenis_pemeriksaan as main_jenis');
    $this->db->from('pemeriksaan_detail pd');
    $this->db->join('pemeriksaan_lab pl', 'pd.pemeriksaan_id = pl.pemeriksaan_id');
    $this->db->where('pd.pemeriksaan_id', $examination_id);
    $this->db->order_by('pd.urutan', 'ASC');
    
    $details = $this->db->get()->result_array();
    
    // Parse sub_pemeriksaan JSON dan tambahkan display label
    foreach ($details as &$detail) {
        if (!empty($detail['sub_pemeriksaan'])) {
            $subs = json_decode($detail['sub_pemeriksaan'], true);
            if (is_array($subs)) {
                $detail['sub_pemeriksaan_array'] = $subs;
                $detail['sub_pemeriksaan_display'] = $this->get_sub_pemeriksaan_labels(
                    $detail['sub_pemeriksaan'], 
                    $detail['jenis_pemeriksaan']
                );
            }
        }
    }
    
    return $details;
}

/**
 * Get samples data dengan examination details (MODIFIED)
 */
public function get_samples_data_enhanced($filters = array(), $limit = 20, $offset = 0) {
    $this->db->select('pl.*, p.nama as nama_pasien, p.nik, p.jenis_kelamin, p.umur,
                      p.telepon, p.alamat_domisili, p.pekerjaan, p.dokter_perujuk,
                      pt.nama_petugas,
                      TIMESTAMPDIFF(HOUR, pl.created_at, NOW()) as processing_hours,
                      (SELECT COUNT(*) FROM timeline_progres tp WHERE tp.pemeriksaan_id = pl.pemeriksaan_id) as timeline_count');
    $this->db->from('pemeriksaan_lab pl');
    $this->db->join('pasien p', 'pl.pasien_id = p.pasien_id');
    $this->db->join('petugas_lab pt', 'pl.petugas_id = pt.petugas_id', 'left');
    
    // Filter by status
    $status = isset($filters['status']) ? $filters['status'] : 'progress';
    $this->db->where('pl.status_pemeriksaan', $status);
    
    // Apply other filters
    if (isset($filters['date_from']) && $filters['date_from']) {
        $this->db->where('DATE(pl.tanggal_pemeriksaan) >=', $filters['date_from']);
    }
    
    if (isset($filters['date_to']) && $filters['date_to']) {
        $this->db->where('DATE(pl.tanggal_pemeriksaan) <=', $filters['date_to']);
    }
    
    if (isset($filters['jenis_pemeriksaan']) && $filters['jenis_pemeriksaan']) {
        // Search dalam jenis_pemeriksaan (comma-separated) atau dalam detail
        $this->db->group_start();
        $this->db->like('pl.jenis_pemeriksaan', $filters['jenis_pemeriksaan']);
        $this->db->or_where("EXISTS (
            SELECT 1 FROM pemeriksaan_detail pd 
            WHERE pd.pemeriksaan_id = pl.pemeriksaan_id 
            AND pd.jenis_pemeriksaan LIKE '%{$filters['jenis_pemeriksaan']}%'
        )", NULL, FALSE);
        $this->db->group_end();
    }
    
    if (isset($filters['petugas_id']) && $filters['petugas_id']) {
        $this->db->where('pl.petugas_id', $filters['petugas_id']);
    }
    
    if (isset($filters['search']) && $filters['search']) {
        $this->db->group_start();
        $this->db->like('p.nama', $filters['search']);
        $this->db->or_like('p.nik', $filters['search']);
        $this->db->or_like('pl.nomor_pemeriksaan', $filters['search']);
        $this->db->group_end();
    }
    
    $this->db->order_by('pl.created_at', 'DESC');
    $this->db->limit($limit, $offset);
    
    $samples = $this->db->get()->result_array();
    
    // Attach examination details untuk setiap sample
    foreach ($samples as &$sample) {
        $sample['examination_details'] = $this->get_examination_details($sample['pemeriksaan_id']);
    }
    
    return $samples;
}

/**
 * Get examination by ID dengan details (MODIFIED)
 */
public function get_examination_by_id($examination_id) {
    $this->db->select('pl.*, p.nama as nama_pasien, p.nik, p.jenis_kelamin, p.umur, 
                      p.tempat_lahir, p.tanggal_lahir, p.alamat_domisili, p.telepon, 
                      p.pekerjaan, p.riwayat_pasien, p.dokter_perujuk, p.asal_rujukan,
                      p.diagnosis_awal, p.rekomendasi_pemeriksaan,
                      pt.nama_petugas');
    $this->db->from('pemeriksaan_lab pl');
    $this->db->join('pasien p', 'pl.pasien_id = p.pasien_id');
    $this->db->join('petugas_lab pt', 'pl.petugas_id = pt.petugas_id', 'left');
    $this->db->where('pl.pemeriksaan_id', $examination_id);
    
    $examination = $this->db->get()->row_array();
    
    if ($examination) {
        // Attach examination details
        $examination['examination_details'] = $this->get_examination_details($examination_id);
    }
    
    return $examination;
}

/**
 * Get existing results untuk multiple examination types
 */
public function get_existing_results_multiple($examination_id, $examination_details) {
    $results = array();
    
    foreach ($examination_details as $detail) {
        $jenis = $detail['jenis_pemeriksaan'];
        $existing = $this->get_existing_results($examination_id, $jenis);
        
        if ($existing) {
            $results[$jenis] = $existing;
        }
    }
    
    return $results;
}

/**
 * Check if all examinations have results
 */
public function check_all_examinations_have_results($examination_id) {
    $details = $this->get_examination_details($examination_id);
    
    if (empty($details)) {
        return false;
    }
    
    foreach ($details as $detail) {
        $has_results = $this->check_examination_type_has_results(
            $examination_id, 
            $detail['jenis_pemeriksaan']
        );
        
        if (!$has_results) {
            return false;
        }
    }
    
    return true;
}

/**
 * Check if specific examination type has results
 */
private function check_examination_type_has_results($examination_id, $jenis_pemeriksaan) {
    $table_map = array(
        'Kimia Darah' => 'kimia_darah',
        'Hematologi' => 'hematologi',
        'Urinologi' => 'urinologi',
        'Serologi' => 'serologi_imunologi',
        'Serologi Imunologi' => 'serologi_imunologi',
        'TBC' => 'tbc',
        'IMS' => 'ims'
    );
    
    if (!isset($table_map[$jenis_pemeriksaan])) {
        return false;
    }
    
    $table = $table_map[$jenis_pemeriksaan];
    $this->db->where('pemeriksaan_id', $examination_id);
    $count = $this->db->count_all_results($table);
    
    return $count > 0;
}

/**
 * Get formatted results untuk multiple examinations
 */
public function get_formatted_results_multiple($examination_id) {
    $details = $this->get_examination_details($examination_id);
    $all_results = array();
    
    foreach ($details as $detail) {
        $jenis = $detail['jenis_pemeriksaan'];
        $results = $this->get_existing_results($examination_id, $jenis);
        
        if ($results) {
            $all_results[$jenis] = array(
                'jenis_pemeriksaan' => $jenis,
                'sub_pemeriksaan_display' => $detail['sub_pemeriksaan_display'] ?? '',
                'results' => $this->format_results_by_type($results, $jenis)
            );
        }
    }
    
    return $all_results;
}

/**
 * Format results berdasarkan tipe pemeriksaan
 */
private function format_results_by_type($results, $jenis_pemeriksaan) {
    $formatted = array();
    
    switch (strtolower($jenis_pemeriksaan)) {
        case 'kimia darah':
            $fields = array(
                'gula_darah_sewaktu' => 'Gula Darah Sewaktu (mg/dL)',
                'gula_darah_puasa' => 'Gula Darah Puasa (mg/dL)',
                'gula_darah_2jam_pp' => 'Gula Darah 2J PP (mg/dL)',
                'cholesterol_total' => 'Kolesterol Total (mg/dL)',
                'cholesterol_hdl' => 'Kolesterol HDL (mg/dL)',
                'cholesterol_ldl' => 'Kolesterol LDL (mg/dL)',
                'trigliserida' => 'Trigliserida (mg/dL)',
                'asam_urat' => 'Asam Urat (mg/dL)',
                'ureum' => 'Ureum (mg/dL)',
                'creatinin' => 'Kreatinin (mg/dL)',
                'sgpt' => 'SGPT (U/L)',
                'sgot' => 'SGOT (U/L)'
            );
            break;
            
        case 'hematologi':
            $fields = array(
                'hemoglobin' => 'Hemoglobin (g/dL)',
                'hematokrit' => 'Hematokrit (%)',
                'leukosit' => 'Leukosit (/uL)',
                'trombosit' => 'Trombosit (/uL)',
                'eritrosit' => 'Eritrosit (juta/uL)',
                'golongan_darah' => 'Golongan Darah',
                'rhesus' => 'Rhesus'
            );
            break;
            
        case 'urinologi':
            $fields = array(
                'makroskopis' => 'Makroskopis',
                'mikroskopis' => 'Mikroskopis',
                'protein' => 'Protein',
                'tes_kehamilan' => 'Tes Kehamilan'
            );
            break;
            
        case 'serologi':
        case 'serologi imunologi':
            $fields = array(
                'rdt_antigen' => 'RDT Antigen',
                'widal' => 'Widal',
                'hbsag' => 'HBsAg',
                'ns1' => 'NS1',
                'hiv' => 'HIV'
            );
            break;
            
        case 'tbc':
            $fields = array(
                'dahak' => 'Dahak (BTA)',
                'tcm' => 'TCM (GeneXpert)'
            );
            break;
            
        case 'ims':
            $fields = array(
                'sifilis' => 'Sifilis',
                'duh_tubuh' => 'Duh Tubuh'
            );
            break;
            
        default:
            $fields = array();
    }
    
    foreach ($fields as $key => $label) {
        if (isset($results[$key]) && !empty($results[$key])) {
            $formatted[$label] = $results[$key];
        }
    }
    
    return $formatted;
}
}