<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Sampel_model extends CI_Model {

    public function __construct() {
        parent::__construct();
        $this->load->database();
    }

    /**
     * Get samples data with filters (enhanced)
     */
    public function get_samples_data_enhanced($filters = array(), $limit = 20, $offset = 0) {
        $this->db->select('pl.*,pl.sub_pemeriksaan, p.nama as nama_pasien, p.nik, p.jenis_kelamin, p.umur,
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
            
            // Untuk single examination, ambil sub_pemeriksaan dari pemeriksaan_detail jika kosong di pl
            if (empty($sample['sub_pemeriksaan']) && count($sample['examination_details']) == 1) {
                $detail = $sample['examination_details'][0];
                if (!empty($detail['sub_pemeriksaan'])) {
                    $sample['sub_pemeriksaan'] = $detail['sub_pemeriksaan'];
                }
            }
        }
        
        return $samples;
    }

    /**
     * Count samples data
     */
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

    public function get_sample_timeline($examination_id) {
        $this->db->select('tp.*, pt.nama_petugas');
        $this->db->from('timeline_progres tp');
        $this->db->join('petugas_lab pt', 'tp.petugas_id = pt.petugas_id', 'left');
        $this->db->where('tp.pemeriksaan_id', $examination_id);
        $this->db->order_by('tp.tanggal_update', 'DESC');
        return $this->db->get()->result_array();
    }

    /**
     * Get latest timeline status
     */
    public function get_latest_timeline_status($examination_id) {
        $this->db->select('status, keterangan, tanggal_update');
        $this->db->where('pemeriksaan_id', $examination_id);
        $this->db->order_by('tanggal_update', 'DESC');
        $this->db->limit(1);
        $result = $this->db->get('timeline_progres')->row_array();
        
        return $result ? $result : array('status' => 'Belum ada update', 'keterangan' => '', 'tanggal_update' => '');
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

    /**
     * Get timeline statistics
     */
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
        
        // Entries by petugas
        $this->db->select('pt.nama_petugas, COUNT(*) as count');
        $this->db->from('timeline_progres tp');
        $this->db->join('petugas_lab pt', 'tp.petugas_id = pt.petugas_id', 'left');
        $this->db->where('tp.pemeriksaan_id', $examination_id);
        $this->db->group_by('tp.petugas_id');
        $stats['entries_by_petugas'] = $this->db->get()->result_array();
        
        return $stats;
    }

    public function get_examination_type_options() {
        return array(
            'Kimia Darah' => 'Kimia Darah',
            'Hematologi' => 'Hematologi', 
            'Urinologi' => 'Urinologi',
            'Serologi' => 'Serologi Imunologi',
            'TBC' => 'TBC',
            'IMS' => 'IMS'
        );
    }

    /**
     * Get all petugas lab
     */
    public function get_all_petugas_lab() {
        $this->db->select('petugas_id, nama_petugas');
        $this->db->order_by('nama_petugas', 'ASC');
        return $this->db->get('petugas_lab')->result_array();
    }

    /**
     * Get existing results for multiple examination types
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
     * Get existing results for single examination type
     */
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

    /**
     * Get sub pemeriksaan labels
     */
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

    /**
     * Get sub pemeriksaan options by jenis pemeriksaan
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
                        'label' => 'Paket Darah Rutin (Numerik)',
                        'description' => 'Hb, Ht, WBC, PLT, RBC, MCV, MCH, MCHC, Eosinofil, Basofil, Neutrofil, Limfosit, Monosit',
                        'is_package' => true,
                        'includes' => ['hemoglobin', 'hematokrit', 'leukosit', 'trombosit', 'eritrosit', 'mcv', 'mch', 'mchc', 'eosinofil', 'basofil', 'neutrofil', 'limfosit', 'monosit']
                    ),
                    'laju_endap_darah' => array(
                        'label' => 'Laju Endap Darah (LED)',
                        'unit' => 'mm/jam',
                        'normal_range' => 'L: < 15, P: < 20',
                        'type' => 'number',
                        'step' => '0.1'
                    ),
                    'clotting_time' => array(
                        'label' => 'Clotting Time',
                        'unit' => 'menit',
                        'normal_range' => '5-15',
                        'type' => 'number',
                        'step' => '0.1'
                    ),
                    'bleeding_time' => array(
                        'label' => 'Bleeding Time',
                        'unit' => 'menit',
                        'normal_range' => '1-6',
                        'type' => 'number',
                        'step' => '0.1'
                    ),
                    'golongan_darah' => array(
                        'label' => 'Golongan Darah + Rhesus',
                        'type' => 'select',
                        'options' => ['A', 'B', 'AB', 'O'],
                        'includes' => ['golongan_darah', 'rhesus']
                    ),
                    'malaria' => array(
                        'label' => 'Malaria',
                        'type' => 'textarea'
                    )
                );
                break;
                
            case 'urinologi':
                $options = array(
                    'urin_rutin' => array(
                        'label' => 'Urin Rutin',
                        'description' => 'Pemeriksaan Fisik, Kimia, dan Mikroskopis',
                        'is_package' => true,
                        'includes' => ['makroskopis', 'mikroskopis', 'berat_jenis', 'kimia_ph', 'protein_regular', 'glukosa', 'keton', 'bilirubin', 'urobilinogen']
                    ),
                    'protein' => array(
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

    // ==========================================
    // RESULT METHODS (from Laboratorium_model)
    // ==========================================

    /**
     * Get Kimia Darah Results
     */
    public function get_kimia_darah_results($examination_id) {
        $this->db->where('pemeriksaan_id', $examination_id);
        return $this->db->get('kimia_darah')->row_array();
    }

    /**
     * Get Hematologi Results
     */
    public function get_hematologi_results($examination_id) {
        $this->db->where('pemeriksaan_id', $examination_id);
        return $this->db->get('hematologi')->row_array();
    }

    /**
     * Get Urinologi Results
     */
    public function get_urinologi_results($examination_id) {
        $this->db->where('pemeriksaan_id', $examination_id);
        return $this->db->get('urinologi')->row_array();
    }

    /**
     * Get Serologi Results
     */
    public function get_serologi_results($examination_id) {
        $this->db->where('pemeriksaan_id', $examination_id);
        return $this->db->get('serologi_imunologi')->row_array();
    }

    /**
     * Get TBC Results
     */
    public function get_tbc_results($examination_id) {
        $this->db->where('pemeriksaan_id', $examination_id);
        return $this->db->get('tbc')->row_array();
    }

    /**
     * Get IMS Results
     */
    public function get_ims_results($examination_id) {
        $this->db->where('pemeriksaan_id', $examination_id);
        return $this->db->get('ims')->row_array();
    }

    /**
     * Get MLS Results
     */
    public function get_mls_results($examination_id) {
        $this->db->where('pemeriksaan_id', $examination_id);
        return $this->db->get('mls')->result_array();
    }

    /**
     * Save or update Kimia Darah results
     */
    public function save_or_update_kimia_darah_results($examination_id, $data) {
        $existing = $this->get_kimia_darah_results($examination_id);
        
        if ($existing) {
            $this->db->where('pemeriksaan_id', $examination_id);
            return $this->db->update('kimia_darah', $data);
        } else {
            return $this->db->insert('kimia_darah', $data);
        }
    }

    /**
     * Save or update Hematologi results
     */
    public function save_or_update_hematologi_results($examination_id, $data) {
        $existing = $this->get_hematologi_results($examination_id);
        
        if ($existing) {
            $this->db->where('pemeriksaan_id', $examination_id);
            return $this->db->update('hematologi', $data);
        } else {
            return $this->db->insert('hematologi', $data);
        }
    }

    /**
     * Save or update Urinologi results
     */
    public function save_or_update_urinologi_results($examination_id, $data) {
        $existing = $this->get_urinologi_results($examination_id);
        
        if ($existing) {
            $this->db->where('pemeriksaan_id', $examination_id);
            return $this->db->update('urinologi', $data);
        } else {
            return $this->db->insert('urinologi', $data);
        }
    }

    /**
     * Save or update Serologi results
     */
    public function save_or_update_serologi_results($examination_id, $data) {
        $existing = $this->get_serologi_results($examination_id);
        
        if ($existing) {
            $this->db->where('pemeriksaan_id', $examination_id);
            return $this->db->update('serologi_imunologi', $data);
        } else {
            return $this->db->insert('serologi_imunologi', $data);
        }
    }

    /**
     * Save or update TBC results
     */
    public function save_or_update_tbc_results($examination_id, $data) {
        $existing = $this->get_tbc_results($examination_id);
        
        if ($existing) {
            $this->db->where('pemeriksaan_id', $examination_id);
            return $this->db->update('tbc', $data);
        } else {
            return $this->db->insert('tbc', $data);
        }
    }

    /**
     * Save or update IMS results
     */
    public function save_or_update_ims_results($examination_id, $data) {
        $existing = $this->get_ims_results($examination_id);
        
        if ($existing) {
            $this->db->where('pemeriksaan_id', $examination_id);
            return $this->db->update('ims', $data);
        } else {
            return $this->db->insert('ims', $data);
        }
    }

    /**
     * Save or update MLS results
     */
    public function save_or_update_mls_results($examination_id, $data) {
        $existing = $this->get_mls_results($examination_id);
        
        if ($existing && !empty($existing)) {
            $this->db->where('pemeriksaan_id', $examination_id);
            return $this->db->update('mls', $data);
        } else {
            return $this->db->insert('mls', $data);
        }
    }

    /**
     * Get priority level
     */
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

    /**
     * Get recent timeline activities
     */
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


public function update_sample_conditions($sampel_id, $kondisi_ids, $catatan_kondisi = null) {
    $data = array(
        'kondisi_sampel' => json_encode($kondisi_ids)
    );
    
    if ($catatan_kondisi !== null) {
        $data['catatan_penolakan'] = $catatan_kondisi;
    }
    
    $this->db->where('sampel_id', $sampel_id);
    return $this->db->update('pemeriksaan_sampel', $data);
}

/**
 * Delete sample
 */
public function delete_sample($sampel_id) {
    $this->db->where('sampel_id', $sampel_id);
    return $this->db->delete('pemeriksaan_sampel');
}

/**
 * Add sample condition (for legacy support if needed)
 */
public function add_sample_condition($sampel_id, $kondisi_id, $catatan = null) {
    // Get existing conditions
    $this->db->select('kondisi_sampel');
    $this->db->where('sampel_id', $sampel_id);
    $sample = $this->db->get('pemeriksaan_sampel')->row_array();
    
    if (!$sample) {
        return false;
    }
    
    $existing_kondisi = json_decode($sample['kondisi_sampel'], true) ?: array();
    
    // Add new condition if not exists
    if (!in_array($kondisi_id, $existing_kondisi)) {
        $existing_kondisi[] = $kondisi_id;
    }
    
    $data = array(
        'kondisi_sampel' => json_encode($existing_kondisi)
    );
    
    if ($catatan) {
        $data['catatan_penolakan'] = $catatan;
    }
    
    $this->db->where('sampel_id', $sampel_id);
    return $this->db->update('pemeriksaan_sampel', $data);
}

/**
 * Remove sample condition (for legacy support if needed)
 */
public function remove_sample_condition($sampel_id, $kondisi_id) {
    // Get existing conditions
    $this->db->select('kondisi_sampel');
    $this->db->where('sampel_id', $sampel_id);
    $sample = $this->db->get('pemeriksaan_sampel')->row_array();
    
    if (!$sample) {
        return false;
    }
    
    $existing_kondisi = json_decode($sample['kondisi_sampel'], true) ?: array();
    
    // Remove condition
    $existing_kondisi = array_diff($existing_kondisi, array($kondisi_id));
    $existing_kondisi = array_values($existing_kondisi); // Re-index array
    
    $data = array(
        'kondisi_sampel' => !empty($existing_kondisi) ? json_encode($existing_kondisi) : null
    );
    
    $this->db->where('sampel_id', $sampel_id);
    return $this->db->update('pemeriksaan_sampel', $data);
}

/**
 * Bulk add sample conditions
 */
public function bulk_add_sample_conditions($sampel_id, $kondisi_ids, $catatan = null) {
    $data = array(
        'kondisi_sampel' => json_encode($kondisi_ids)
    );
    
    if ($catatan) {
        $data['catatan_penolakan'] = $catatan;
    }
    
    $this->db->where('sampel_id', $sampel_id);
    
    if ($this->db->update('pemeriksaan_sampel', $data)) {
        return count($kondisi_ids);
    }
    
    return 0;
}

public function get_existing_results_single($examination_id) {
    // Get examination details first
    $examination = $this->get_examination_by_id($examination_id);
    
    if (!$examination || empty($examination['examination_details'])) {
        return null;
    }
    
    // Get first examination type
    $jenis = $examination['examination_details'][0]['jenis_pemeriksaan'];
    
    return $this->get_existing_results($examination_id, $jenis);
}

    public function get_jenis_sampel_options() {
        return array(
            'whole_blood' => 'Whole Blood',
            'serum' => 'Serum',
            'plasma' => 'Plasma',
            'urin' => 'Urin',
            'feses' => 'Feses',
            'sputum' => 'Sputum',
            'lain' => 'Lain - Lain'
        );
    }

    /**
     * Get examination by ID
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
     * Get examination details
     */
    private function get_examination_details($examination_id) {
        $this->db->select('pd.*, pl.jenis_pemeriksaan as main_jenis');
        $this->db->from('pemeriksaan_detail pd');
        $this->db->join('pemeriksaan_lab pl', 'pd.pemeriksaan_id = pl.pemeriksaan_id');
        $this->db->where('pd.pemeriksaan_id', $examination_id);
        $this->db->order_by('pd.urutan', 'ASC');
        
        $details = $this->db->get()->result_array();
        
        // Parse sub_pemeriksaan JSON and generate display text
        foreach ($details as &$detail) {
            if (!empty($detail['sub_pemeriksaan'])) {
                $subs = json_decode($detail['sub_pemeriksaan'], true);
                if (is_array($subs)) {
                    $detail['sub_pemeriksaan_array'] = $subs;
                    
                    // Generate human-readable display text
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
     * Get examination samples - FIXED VERSION
     */
    public function get_examination_samples($examination_id) {
        $this->db->select('
            ps.*,
            pt_pengambil.nama_petugas as petugas_pengambil_nama,
            pt_evaluasi.nama_petugas as petugas_evaluasi_nama
        ');
        $this->db->from('pemeriksaan_sampel ps');
        $this->db->join('petugas_lab pt_pengambil', 'ps.petugas_pengambil_id = pt_pengambil.petugas_id', 'left');
        $this->db->join('petugas_lab pt_evaluasi', 'ps.petugas_evaluasi_id = pt_evaluasi.petugas_id', 'left');
        $this->db->where('ps.pemeriksaan_id', $examination_id);
        $this->db->order_by('ps.sampel_id', 'ASC');
        
        $samples = $this->db->get()->result_array();
        
        // Decode JSON kondisi_sampel dan attach detail kondisi
        foreach ($samples as &$sample) {
            $sample['kondisi_details'] = $this->decode_sample_conditions(
                $sample['kondisi_sampel'], 
                $sample['jenis_sampel']
            );
            
            // FIX: Safely decode JSON with NULL check
            if (!empty($sample['kondisi_sampel'])) {
                $sample['kondisi_ids'] = json_decode($sample['kondisi_sampel'], true) ?: array();
            } else {
                $sample['kondisi_ids'] = array();
            }
        }
        
        return $samples;
    }

    /**
     * Get single sample by ID - FIXED VERSION
     */
    public function get_sample_by_id($sampel_id) {
        $this->db->select('
            ps.*,
            pl.nomor_pemeriksaan,
            p.nama as nama_pasien,
            pt_pengambil.nama_petugas as petugas_pengambil_nama,
            pt_evaluasi.nama_petugas as petugas_evaluasi_nama
        ');
        $this->db->from('pemeriksaan_sampel ps');
        $this->db->join('pemeriksaan_lab pl', 'ps.pemeriksaan_id = pl.pemeriksaan_id');
        $this->db->join('pasien p', 'pl.pasien_id = p.pasien_id');
        $this->db->join('petugas_lab pt_pengambil', 'ps.petugas_pengambil_id = pt_pengambil.petugas_id', 'left');
        $this->db->join('petugas_lab pt_evaluasi', 'ps.petugas_evaluasi_id = pt_evaluasi.petugas_id', 'left');
        $this->db->where('ps.sampel_id', $sampel_id);
        
        $sample = $this->db->get()->row_array();
        
        if ($sample) {
            $sample['kondisi_details'] = $this->decode_sample_conditions(
                $sample['kondisi_sampel'], 
                $sample['jenis_sampel']
            );
            
            // FIX: Safely decode JSON with NULL check
            if (!empty($sample['kondisi_sampel'])) {
                $sample['kondisi_ids'] = json_decode($sample['kondisi_sampel'], true) ?: array();
            } else {
                $sample['kondisi_ids'] = array();
            }
        }
        
        return $sample;
    }

    /**
     * Decode kondisi_sampel JSON - FIXED VERSION
     */
    private function decode_sample_conditions($kondisi_json, $jenis_sampel) {
        // FIX: Properly handle NULL and empty values
        if ($kondisi_json === null || $kondisi_json === '' || trim($kondisi_json) === '') {
            return array();
        }
        
        $kondisi_ids = json_decode($kondisi_json, true);
        if (!is_array($kondisi_ids) || empty($kondisi_ids)) {
            return array();
        }
        
        // Get details from master_kondisi_sampel
        $this->db->select('*');
        $this->db->from('master_kondisi_sampel');
        $this->db->where_in('kondisi_id', $kondisi_ids);
        $this->db->where('jenis_sampel', $jenis_sampel);
        $this->db->order_by('urutan', 'ASC');
        
        return $this->db->get()->result_array();
    }

    /**
     * Get master kondisi by jenis sampel
     */
    public function get_master_kondisi_by_jenis($jenis_sampel) {
        $this->db->select('*');
        $this->db->from('master_kondisi_sampel');
        $this->db->where('jenis_sampel', $jenis_sampel);
        $this->db->where('is_active', 1);
        $this->db->order_by('urutan', 'ASC');
        
        return $this->db->get()->result_array();
    }

    /**
     * Get samples summary
     */
    public function get_samples_summary($examination_id) {
        $this->db->select('
            COUNT(*) as total_samples,
            SUM(CASE WHEN status_sampel = "belum_diambil" THEN 1 ELSE 0 END) as belum_diambil,
            SUM(CASE WHEN status_sampel = "sudah_diambil" THEN 1 ELSE 0 END) as sudah_diambil,
            SUM(CASE WHEN status_sampel = "diterima" THEN 1 ELSE 0 END) as diterima,
            SUM(CASE WHEN status_sampel = "ditolak" THEN 1 ELSE 0 END) as ditolak
        ');
        $this->db->from('pemeriksaan_sampel');
        $this->db->where('pemeriksaan_id', $examination_id);
        
        return $this->db->get()->row_array();
    }

    /**
     * Create new sample
     */
    public function create_sample($data) {
        $insert_data = array(
            'pemeriksaan_id' => $data['pemeriksaan_id'],
            'jenis_sampel' => $data['jenis_sampel'],
            'keterangan_sampel' => isset($data['keterangan_sampel']) ? $data['keterangan_sampel'] : null,
            'status_sampel' => 'belum_diambil',
            'created_at' => date('Y-m-d H:i:s')
        );
        
        if ($this->db->insert('pemeriksaan_sampel', $insert_data)) {
            return $this->db->insert_id();
        }
        
        return false;
    }

    /**
     * Update sample status - Pengambilan
     */
    public function update_sample_pengambilan($sampel_id, $petugas_id) {
        $data = array(
            'status_sampel' => 'sudah_diambil',
            'tanggal_pengambilan' => date('Y-m-d H:i:s'),
            'petugas_pengambil_id' => $petugas_id
        );
        
        $this->db->where('sampel_id', $sampel_id);
        return $this->db->update('pemeriksaan_sampel', $data);
    }

    /**
     * Update sample status - Diterima dengan kondisi
     */
    public function update_sample_diterima($sampel_id, $petugas_id, $kondisi_ids = array(), $catatan_kondisi = null) {
        $data = array(
            'status_sampel' => 'diterima',
            'tanggal_evaluasi' => date('Y-m-d H:i:s'),
            'petugas_evaluasi_id' => $petugas_id,
            'kondisi_sampel' => !empty($kondisi_ids) ? json_encode($kondisi_ids) : null,
            'catatan_penolakan' => $catatan_kondisi // Using catatan_penolakan field for notes
        );
        
        $this->db->where('sampel_id', $sampel_id);
        return $this->db->update('pemeriksaan_sampel', $data);
    }

    /**
     * Update sample status - Ditolak dengan kondisi
     */
    public function update_sample_ditolak($sampel_id, $petugas_id, $catatan_penolakan, $kondisi_ids = array()) {
        $data = array(
            'status_sampel' => 'ditolak',
            'tanggal_evaluasi' => date('Y-m-d H:i:s'),
            'petugas_evaluasi_id' => $petugas_id,
            'catatan_penolakan' => $catatan_penolakan,
            'kondisi_sampel' => !empty($kondisi_ids) ? json_encode($kondisi_ids) : null
        );
        
        $this->db->where('sampel_id', $sampel_id);
        return $this->db->update('pemeriksaan_sampel', $data);
    }

    /**
     * Get petugas ID by user ID
     */
    public function get_petugas_id_by_user_id($user_id) {
        $this->db->select('petugas_id');
        $this->db->where('user_id', $user_id);
        $result = $this->db->get('petugas_lab')->row_array();
        
        return $result ? $result['petugas_id'] : null;
    }

    /**
     * Add sample timeline entry
     */
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

    /**
     * Check if all samples accepted
     */
    public function check_all_samples_accepted($examination_id) {
        $this->db->where('pemeriksaan_id', $examination_id);
        $this->db->where_in('status_sampel', array('belum_diambil', 'sudah_diambil', 'ditolak'));
        $pending_count = $this->db->count_all_results('pemeriksaan_sampel');
        
        return $pending_count === 0;
    }

    /**
     * Get samples with critical conditions
     */
    public function get_samples_with_critical_conditions($examination_id = null) {
        $this->db->select('
            ps.*,
            pl.nomor_pemeriksaan,
            p.nama as nama_pasien
        ');
        $this->db->from('pemeriksaan_sampel ps');
        $this->db->join('pemeriksaan_lab pl', 'ps.pemeriksaan_id = pl.pemeriksaan_id');
        $this->db->join('pasien p', 'pl.pasien_id = p.pasien_id');
        $this->db->where('ps.kondisi_sampel IS NOT NULL');
        
        if ($examination_id) {
            $this->db->where('ps.pemeriksaan_id', $examination_id);
        }
        
        $this->db->order_by('ps.tanggal_evaluasi', 'DESC');
        
        $samples = $this->db->get()->result_array();
        
        // Filter only samples with critical conditions
        $critical_samples = array();
        foreach ($samples as $sample) {
            $kondisi_details = $this->decode_sample_conditions($sample['kondisi_sampel'], $sample['jenis_sampel']);
            
            $has_critical = false;
            foreach ($kondisi_details as $kondisi) {
                if ($kondisi['kategori'] === 'critical') {
                    $has_critical = true;
                    break;
                }
            }
            
            if ($has_critical) {
                $sample['kondisi_details'] = $kondisi_details;
                $critical_samples[] = $sample;
            }
        }
        
        return $critical_samples;
    }
    /**
 * Get validated results with pagination
 */
public function get_validated_results_paginated($filters = array(), $limit = 20, $offset = 0)
{
    $this->db->select('pl.*, p.nama as nama_pasien, p.nik, pt.nama_petugas');
    $this->db->from('pemeriksaan_lab pl');
    $this->db->join('pasien p', 'pl.pasien_id = p.pasien_id');
    $this->db->join('petugas_lab pt', 'pl.petugas_id = pt.petugas_id', 'left');
    $this->db->where('pl.status_pemeriksaan', 'selesai');
    
    // Apply filters
    if (isset($filters['date_from']) && $filters['date_from']) {
        $this->db->where('DATE(pl.completed_at) >=', $filters['date_from']);
    }
    
    if (isset($filters['date_to']) && $filters['date_to']) {
        $this->db->where('DATE(pl.completed_at) <=', $filters['date_to']);
    }
    
    if (isset($filters['jenis_pemeriksaan']) && $filters['jenis_pemeriksaan']) {
        $this->db->where('pl.jenis_pemeriksaan', $filters['jenis_pemeriksaan']);
    }
    
    if (isset($filters['validator']) && $filters['validator']) {
        $this->db->where('pl.petugas_id', $filters['validator']);
    }
    
    if (isset($filters['search']) && $filters['search']) {
        $this->db->group_start();
        $this->db->like('p.nama', $filters['search']);
        $this->db->or_like('p.nik', $filters['search']);
        $this->db->or_like('pl.nomor_pemeriksaan', $filters['search']);
        $this->db->group_end();
    }
    
    $this->db->order_by('pl.completed_at', 'DESC');
    $this->db->limit($limit, $offset);
    
    return $this->db->get()->result_array();
}

/**
 * Count validated results
 */
public function count_validated_results($filters = array())
{
    $this->db->from('pemeriksaan_lab pl');
    $this->db->join('pasien p', 'pl.pasien_id = p.pasien_id');
    $this->db->where('pl.status_pemeriksaan', 'selesai');
    
    // Apply same filters
    if (isset($filters['date_from']) && $filters['date_from']) {
        $this->db->where('DATE(pl.completed_at) >=', $filters['date_from']);
    }
    
    if (isset($filters['date_to']) && $filters['date_to']) {
        $this->db->where('DATE(pl.completed_at) <=', $filters['date_to']);
    }
    
    if (isset($filters['jenis_pemeriksaan']) && $filters['jenis_pemeriksaan']) {
        $this->db->where('pl.jenis_pemeriksaan', $filters['jenis_pemeriksaan']);
    }
    
    if (isset($filters['validator']) && $filters['validator']) {
        $this->db->where('pl.petugas_id', $filters['validator']);
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

/**
 * Get QC performance metrics
 */
public function get_qc_performance_metrics($period = 'month')
{
    $days = $period === 'week' ? 7 : ($period === 'month' ? 30 : 90);
    
    $this->db->select("
        COUNT(*) as total_validated,
        SUM(CASE WHEN TIMESTAMPDIFF(HOUR, started_at, completed_at) < 24 THEN 1 ELSE 0 END) as within_24h,
        AVG(TIMESTAMPDIFF(HOUR, started_at, completed_at)) as avg_time_hours,
        DATE(completed_at) as validation_date
    ");
    $this->db->where('status_pemeriksaan', 'selesai');
    $this->db->where('completed_at >=', date('Y-m-d', strtotime("-$days days")));
    $this->db->group_by('DATE(completed_at)');
    $this->db->order_by('validation_date', 'ASC');
    
    return $this->db->get('pemeriksaan_lab')->result_array();
}

/**
 * Get validation trends
 */
public function get_validation_trends($period = 'month')
{
    $days = $period === 'week' ? 7 : ($period === 'month' ? 30 : 90);
    
    $this->db->select("
        DATE(completed_at) as date,
        COUNT(*) as count,
        AVG(TIMESTAMPDIFF(HOUR, started_at, completed_at)) as avg_hours
    ");
    $this->db->where('status_pemeriksaan', 'selesai');
    $this->db->where('completed_at >=', date('Y-m-d', strtotime("-$days days")));
    $this->db->group_by('DATE(completed_at)');
    $this->db->order_by('date', 'ASC');
    
    return $this->db->get('pemeriksaan_lab')->result_array();
}

/**
 * Get validator performance
 */
public function get_validator_performance($period = 'month')
{
    $days = $period === 'week' ? 7 : ($period === 'month' ? 30 : 90);
    
    $this->db->select("
        pt.nama_petugas,
        COUNT(*) as total_validated,
        AVG(TIMESTAMPDIFF(HOUR, pl.started_at, pl.completed_at)) as avg_time_hours
    ");
    $this->db->from('pemeriksaan_lab pl');
    $this->db->join('petugas_lab pt', 'pl.petugas_id = pt.petugas_id');
    $this->db->where('pl.status_pemeriksaan', 'selesai');
    $this->db->where('pl.completed_at >=', date('Y-m-d', strtotime("-$days days")));
    $this->db->group_by('pl.petugas_id');
    $this->db->order_by('total_validated', 'DESC');
    
    return $this->db->get()->result_array();
}
}