<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Sample_inventory_model extends CI_Model {

    public function __construct() {
        parent::__construct();
        $this->load->database();
    }

    /**
     * Get all samples currently in storage
     */
    public function get_all_samples_in_storage() {
        $this->db->select('
            ss.*,
            ps.jenis_sampel,
            ps.tanggal_pengambilan,
            pl.nomor_pemeriksaan,
            p.nama as nama_pasien,
            p.nik,
            se.masa_berlaku_hari,
            DATE_ADD(ps.tanggal_pengambilan, INTERVAL se.masa_berlaku_hari DAY) as tanggal_kadaluarsa,
            CASE 
                WHEN DATE_ADD(ps.tanggal_pengambilan, INTERVAL se.masa_berlaku_hari DAY) < NOW() THEN "expired"
                WHEN DATE_ADD(ps.tanggal_pengambilan, INTERVAL se.masa_berlaku_hari DAY) < DATE_ADD(NOW(), INTERVAL 2 DAY) THEN "expiring_soon"
                ELSE "valid"
            END as status_berlaku,
            DATEDIFF(DATE_ADD(ps.tanggal_pengambilan, INTERVAL se.masa_berlaku_hari DAY), NOW()) as days_remaining
        ');
        $this->db->from('sampel_storage ss');
        $this->db->join('pemeriksaan_sampel ps', 'ss.sampel_id = ps.sampel_id');
        $this->db->join('sampel_expiry se', 'ps.jenis_sampel = se.jenis_sampel');
        $this->db->join('pemeriksaan_lab pl', 'ps.pemeriksaan_id = pl.pemeriksaan_id');
        $this->db->join('pasien p', 'pl.pasien_id = p.pasien_id');
        $this->db->where('ss.status_penyimpanan', 'tersimpan');
        $this->db->order_by('ss.tanggal_masuk', 'DESC');
        
        return $this->db->get()->result_array();
    }

    /**
     * Get inventory summary statistics
     */
    public function get_inventory_summary() {
        $this->db->select('
            COUNT(DISTINCT ss.storage_id) as total_samples,
            COUNT(DISTINCT ss.lokasi_penyimpanan) as total_locations,
            SUM(CASE WHEN DATE_ADD(ps.tanggal_pengambilan, INTERVAL se.masa_berlaku_hari DAY) < NOW() THEN 1 ELSE 0 END) as expired_count,
            SUM(CASE WHEN DATE_ADD(ps.tanggal_pengambilan, INTERVAL se.masa_berlaku_hari DAY) < DATE_ADD(NOW(), INTERVAL 2 DAY) 
                    AND DATE_ADD(ps.tanggal_pengambilan, INTERVAL se.masa_berlaku_hari DAY) >= NOW() THEN 1 ELSE 0 END) as expiring_soon_count,
            SUM(CASE WHEN DATE_ADD(ps.tanggal_pengambilan, INTERVAL se.masa_berlaku_hari DAY) >= DATE_ADD(NOW(), INTERVAL 2 DAY) THEN 1 ELSE 0 END) as valid_count
        ');
        $this->db->from('sampel_storage ss');
        $this->db->join('pemeriksaan_sampel ps', 'ss.sampel_id = ps.sampel_id');
        $this->db->join('sampel_expiry se', 'ps.jenis_sampel = se.jenis_sampel');
        $this->db->where('ss.status_penyimpanan', 'tersimpan');
        
        return $this->db->get()->row_array();
    }

    /**
     * Get storage locations
     */
    public function get_storage_locations() {
        $this->db->select('lokasi_penyimpanan, COUNT(*) as jumlah_sampel');
        $this->db->from('sampel_storage');
        $this->db->where('status_penyimpanan', 'tersimpan');
        $this->db->group_by('lokasi_penyimpanan');
        $this->db->order_by('lokasi_penyimpanan', 'ASC');
        
        return $this->db->get()->result_array();
    }

    /**
     * Get expiring samples
     */
    public function get_expiring_samples($days = 2) {
        $this->db->select('
            ss.*,
            ps.jenis_sampel,
            ps.tanggal_pengambilan,
            pl.nomor_pemeriksaan,
            p.nama as nama_pasien,
            se.masa_berlaku_hari,
            DATE_ADD(ps.tanggal_pengambilan, INTERVAL se.masa_berlaku_hari DAY) as tanggal_kadaluarsa,
            DATEDIFF(DATE_ADD(ps.tanggal_pengambilan, INTERVAL se.masa_berlaku_hari DAY), NOW()) as days_remaining
        ');
        $this->db->from('sampel_storage ss');
        $this->db->join('pemeriksaan_sampel ps', 'ss.sampel_id = ps.sampel_id');
        $this->db->join('sampel_expiry se', 'ps.jenis_sampel = se.jenis_sampel');
        $this->db->join('pemeriksaan_lab pl', 'ps.pemeriksaan_id = pl.pemeriksaan_id');
        $this->db->join('pasien p', 'pl.pasien_id = p.pasien_id');
        $this->db->where('ss.status_penyimpanan', 'tersimpan');
        $this->db->where('DATE_ADD(ps.tanggal_pengambilan, INTERVAL se.masa_berlaku_hari DAY) <=', 'DATE_ADD(NOW(), INTERVAL '.$days.' DAY)', FALSE);
        $this->db->where('DATE_ADD(ps.tanggal_pengambilan, INTERVAL se.masa_berlaku_hari DAY) >=', 'NOW()', FALSE);
        $this->db->order_by('tanggal_kadaluarsa', 'ASC');
        
        return $this->db->get()->result_array();
    }

    /**
     * Add sample to storage
     */
    public function add_to_storage($data) {
        $insert_data = [
            'sampel_id' => $data['sampel_id'],
            'lokasi_penyimpanan' => $data['lokasi_penyimpanan'],
            'suhu_penyimpanan' => isset($data['suhu_penyimpanan']) ? $data['suhu_penyimpanan'] : '2-8°C',
            'volume_sampel' => isset($data['volume_sampel']) ? $data['volume_sampel'] : 0,
            'satuan_volume' => isset($data['satuan_volume']) ? $data['satuan_volume'] : 'ml',
            'keterangan' => isset($data['keterangan']) ? $data['keterangan'] : null,
            'petugas_id' => isset($data['petugas_id']) ? $data['petugas_id'] : null,
            'status_penyimpanan' => 'tersimpan',
            'tanggal_masuk' => date('Y-m-d H:i:s')
        ];
        
        if ($this->db->insert('sampel_storage', $insert_data)) {
            return $this->db->insert_id();
        } else {
            // Log DB error
            $error = $this->db->error();
            log_message('error', 'DB Error during add_to_storage: ' . print_r($error, true));
            return false;
        }
    }

    /**
     * Update storage status
     */
    public function update_storage_status($storage_id, $status, $keterangan = null) {
        $data = [
            'status_penyimpanan' => $status,
            'keterangan' => $keterangan
        ];
        
        // Set tanggal_keluar jika status bukan tersimpan
        if ($status !== 'tersimpan') {
            $data['tanggal_keluar'] = date('Y-m-d H:i:s');
        }
        
        $this->db->where('storage_id', $storage_id);
        return $this->db->update('sampel_storage', $data);
    }

    /**
     * Get storage detail
     */
    public function get_storage_detail($storage_id) {
        $this->db->select('
            ss.*,
            ps.jenis_sampel,
            ps.tanggal_pengambilan,
            ps.status_sampel,
            pl.nomor_pemeriksaan,
            pl.jenis_pemeriksaan,
            p.nama as nama_pasien,
            p.nik,
            se.masa_berlaku_hari,
            se.suhu_optimal_min,
            se.suhu_optimal_max,
            DATE_ADD(ps.tanggal_pengambilan, INTERVAL se.masa_berlaku_hari DAY) as tanggal_kadaluarsa
        ');
        $this->db->from('sampel_storage ss');
        $this->db->join('pemeriksaan_sampel ps', 'ss.sampel_id = ps.sampel_id');
        $this->db->join('sampel_expiry se', 'ps.jenis_sampel = se.jenis_sampel');
        $this->db->join('pemeriksaan_lab pl', 'ps.pemeriksaan_id = pl.pemeriksaan_id');
        $this->db->join('pasien p', 'pl.pasien_id = p.pasien_id');
        $this->db->where('ss.storage_id', $storage_id);
        
        return $this->db->get()->row_array();
    }

    /**
     * Log temperature
     */
    public function log_temperature($data) {
        $insert_data = [
            'lokasi_storage' => $data['lokasi_storage'],
            'suhu_tercatat' => $data['suhu_tercatat'],
            'kelembaban' => isset($data['kelembaban']) ? $data['kelembaban'] : null,
            'keterangan' => isset($data['keterangan']) ? $data['keterangan'] : null,
            'petugas_id' => isset($data['petugas_id']) ? $data['petugas_id'] : null,
            'tanggal_pencatatan' => date('Y-m-d H:i:s')
        ];
        
        // Determine temperature status
        // You can add logic here to check against optimal temperature ranges
        $insert_data['status_suhu'] = 'normal';
        
        if ($this->db->insert('storage_temperature_log', $insert_data)) {
            return $this->db->insert_id();
        }
        
        return false;
    }

    /**
     * Get temperature logs for a location
     */
    public function get_temperature_logs($lokasi, $limit = 10) {
        $this->db->select('*');
        $this->db->from('storage_temperature_log');
        $this->db->where('lokasi_storage', $lokasi);
        $this->db->order_by('tanggal_pencatatan', 'DESC');
        $this->db->limit($limit);
        
        return $this->db->get()->result_array();
    }
}