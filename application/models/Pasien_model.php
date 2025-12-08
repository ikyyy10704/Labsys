<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Pasien_model extends CI_Model {

    public function __construct()
    {
        parent::__construct();
        $this->load->database();
    }

    // ==========================================
    // BASIC CRUD OPERATIONS
    // ==========================================

    /**
     * Get all patients with basic information
     */
    public function get_all_patients($limit = null, $offset = null)
    {
        $this->db->select('
            pasien_id, nama, nik, jenis_kelamin, tempat_lahir, tanggal_lahir, umur,
            alamat_domisili, pekerjaan, telepon, kontak_darurat, riwayat_pasien,
            permintaan_pemeriksaan, dokter_perujuk, asal_rujukan, nomor_rujukan,
            tanggal_rujukan, diagnosis_awal, rekomendasi_pemeriksaan, nomor_registrasi,
            created_at
        ');
        $this->db->from('pasien');
        $this->db->order_by('created_at', 'DESC');
        
        if ($limit !== null) {
            $this->db->limit($limit, $offset);
        }
        
        $query = $this->db->get();
        return $query->result_array();
    }

    /**
     * Get patient by ID
     */
    public function get_patient_by_id($patient_id)
    {
        $this->db->where('pasien_id', $patient_id);
        $query = $this->db->get('pasien');
        return $query->row_array();
    }

    /**
     * Get patient by NIK
     */
    public function get_patient_by_nik($nik)
    {
        $this->db->where('nik', $nik);
        $query = $this->db->get('pasien');
        return $query->row_array();
    }

    /**
     * Get patient by registration number
     */
    public function get_patient_by_registration($nomor_registrasi)
    {
        $this->db->where('nomor_registrasi', $nomor_registrasi);
        $query = $this->db->get('pasien');
        return $query->row_array();
    }


    public function delete_patient($patient_id)
    {
        $this->db->trans_start();
        
        try {
            // Check if patient has examination records
            $this->db->where('pasien_id', $patient_id);
            $examination_count = $this->db->count_all_results('pemeriksaan_lab');
            
            if ($examination_count > 0) {
                throw new Exception('Pasien memiliki riwayat pemeriksaan, tidak dapat dihapus');
            }
            
            $this->db->where('pasien_id', $patient_id);
            $this->db->delete('pasien');
            
            $this->db->trans_complete();
            
            return $this->db->trans_status() !== FALSE;
            
        } catch (Exception $e) {
            $this->db->trans_rollback();
            log_message('error', 'Error deleting patient: ' . $e->getMessage());
            return false;
        }
    }

    // ==========================================
    // SEARCH AND FILTERING
    // ==========================================

    /**
     * Search patients by various fields
     */
    public function search_patients($search_term, $limit = 10)
    {
        $this->db->select('pasien_id, nama, nik, jenis_kelamin, telepon, nomor_registrasi, created_at');
        $this->db->from('pasien');
        $this->db->group_start();
        $this->db->like('nama', $search_term);
        $this->db->or_like('nik', $search_term);
        $this->db->or_like('telepon', $search_term);
        $this->db->or_like('nomor_registrasi', $search_term);
        $this->db->group_end();
        $this->db->order_by('nama', 'ASC');
        $this->db->limit($limit);
        
        $query = $this->db->get();
        return $query->result_array();
    }

    /**
     * Get patients with filters for reports
     */
    public function get_patient_reports($limit = 20, $offset = 0, $filters = array())
    {
        $this->db->select('
            p.pasien_id, p.nama, p.nik, p.jenis_kelamin, p.tempat_lahir, p.tanggal_lahir, p.umur,
            p.alamat_domisili, p.pekerjaan, p.telepon, p.kontak_darurat, p.riwayat_pasien,
            p.permintaan_pemeriksaan, p.dokter_perujuk, p.asal_rujukan, p.nomor_rujukan,
            p.tanggal_rujukan, p.diagnosis_awal, p.rekomendasi_pemeriksaan, p.nomor_registrasi,
            p.created_at,
            COUNT(pl.pemeriksaan_id) as total_examinations
        ');
        $this->db->from('pasien p');
        $this->db->join('pemeriksaan_lab pl', 'p.pasien_id = pl.pasien_id', 'left');
        
        // Apply filters
        if (!empty($filters['start_date'])) {
            $this->db->where('DATE(p.created_at) >=', $filters['start_date']);
        }
        
        if (!empty($filters['end_date'])) {
            $this->db->where('DATE(p.created_at) <=', $filters['end_date']);
        }
        
        if (!empty($filters['gender'])) {
            $this->db->where('p.jenis_kelamin', $filters['gender']);
        }
        
        if (!empty($filters['age_range'])) {
            switch ($filters['age_range']) {
                case 'child':
                    $this->db->where('p.umur <', 18);
                    break;
                case 'adult':
                    $this->db->where('p.umur >=', 18);
                    $this->db->where('p.umur <', 60);
                    break;
                case 'elderly':
                    $this->db->where('p.umur >=', 60);
                    break;
            }
        }
        
        if (!empty($filters['search'])) {
            $this->db->group_start();
            $this->db->like('p.nama', $filters['search']);
            $this->db->or_like('p.nik', $filters['search']);
            $this->db->or_like('p.telepon', $filters['search']);
            $this->db->or_like('p.nomor_registrasi', $filters['search']);
            $this->db->group_end();
        }
        
        $this->db->group_by('p.pasien_id');
        $this->db->order_by('p.created_at', 'DESC');
        $this->db->limit($limit, $offset);
        
        $query = $this->db->get();
        return $query->result_array();
    }

    /**
     * Count patients for reports with filters
     */
    public function count_patient_reports($filters = array())
    {
        $this->db->from('pasien p');
        
        // Apply same filters as get_patient_reports
        if (!empty($filters['start_date'])) {
            $this->db->where('DATE(p.created_at) >=', $filters['start_date']);
        }
        
        if (!empty($filters['end_date'])) {
            $this->db->where('DATE(p.created_at) <=', $filters['end_date']);
        }
        
        if (!empty($filters['gender'])) {
            $this->db->where('p.jenis_kelamin', $filters['gender']);
        }
        
        if (!empty($filters['age_range'])) {
            switch ($filters['age_range']) {
                case 'child':
                    $this->db->where('p.umur <', 18);
                    break;
                case 'adult':
                    $this->db->where('p.umur >=', 18);
                    $this->db->where('p.umur <', 60);
                    break;
                case 'elderly':
                    $this->db->where('p.umur >=', 60);
                    break;
            }
        }
        
        if (!empty($filters['search'])) {
            $this->db->group_start();
            $this->db->like('p.nama', $filters['search']);
            $this->db->or_like('p.nik', $filters['search']);
            $this->db->or_like('p.telepon', $filters['search']);
            $this->db->or_like('p.nomor_registrasi', $filters['search']);
            $this->db->group_end();
        }
        
        return $this->db->count_all_results();
    }

    // ==========================================
    // STATISTICS AND ANALYTICS
    // ==========================================

    /**
     * Get basic patient statistics
     */
    public function get_patient_statistics($filters = array())
    {
        $stats = array();
        
        // Total patients
        $this->db->from('pasien');
        if (!empty($filters)) {
            $this->_apply_filters($filters);
        }
        $stats['total'] = $this->db->count_all_results();
        
        // Patients registered today
        $this->db->from('pasien');
        $this->db->where('DATE(created_at)', date('Y-m-d'));
        if (!empty($filters)) {
            $this->_apply_filters($filters);
        }
        $stats['today'] = $this->db->count_all_results();
        
        // Gender distribution
        $this->db->select('jenis_kelamin, COUNT(*) as count');
        $this->db->from('pasien');
        if (!empty($filters)) {
            $this->_apply_filters($filters);
        }
        $this->db->group_by('jenis_kelamin');
        $gender_query = $this->db->get();
        
        $stats['male'] = 0;
        $stats['female'] = 0;
        
        foreach ($gender_query->result_array() as $row) {
            if ($row['jenis_kelamin'] === 'L') {
                $stats['male'] = $row['count'];
            } elseif ($row['jenis_kelamin'] === 'P') {
                $stats['female'] = $row['count'];
            }
        }
        
        return $stats;
    }

    /**
     * Get detailed patient statistics for reports
     */
    public function get_detailed_patient_statistics()
    {
        $stats = array();
        
        // Basic counts
        $stats['total'] = $this->db->count_all('pasien');
        
        // Today's registrations
        $this->db->where('DATE(created_at)', date('Y-m-d'));
        $stats['today'] = $this->db->count_all_results('pasien');
        
        // Gender distribution
        $this->db->select('jenis_kelamin, COUNT(*) as count');
        $this->db->group_by('jenis_kelamin');
        $gender_query = $this->db->get('pasien');
        
        $stats['by_gender'] = array();
        foreach ($gender_query->result_array() as $row) {
            $stats['by_gender'][$row['jenis_kelamin']] = $row['count'];
        }
        
        // Age group distribution
        $this->db->select('
            SUM(CASE WHEN umur < 18 THEN 1 ELSE 0 END) as child,
            SUM(CASE WHEN umur >= 18 AND umur < 60 THEN 1 ELSE 0 END) as adult,
            SUM(CASE WHEN umur >= 60 THEN 1 ELSE 0 END) as elderly,
            SUM(CASE WHEN umur IS NULL THEN 1 ELSE 0 END) as unknown
        ');
        $age_query = $this->db->get('pasien');
        $age_data = $age_query->row_array();
        
        $stats['by_age_group'] = array(
            'child' => (int)$age_data['child'],
            'adult' => (int)$age_data['adult'],
            'elderly' => (int)$age_data['elderly'],
            'unknown' => (int)$age_data['unknown']
        );
        
        // Monthly registration trend (last 12 months)
        $this->db->select('
            YEAR(created_at) as year,
            MONTH(created_at) as month,
            COUNT(*) as count
        ');
        $this->db->where('created_at >=', date('Y-m-d', strtotime('-12 months')));
        $this->db->group_by('YEAR(created_at), MONTH(created_at)');
        $this->db->order_by('year ASC, month ASC');
        $monthly_query = $this->db->get('pasien');
        
        $stats['monthly_trend'] = array();
        foreach ($monthly_query->result_array() as $row) {
            $month_key = sprintf('%04d-%02d', $row['year'], $row['month']);
            $stats['monthly_trend'][$month_key] = $row['count'];
        }
        
        // Patients with examinations
        $this->db->select('COUNT(DISTINCT p.pasien_id) as count');
        $this->db->from('pasien p');
        $this->db->join('pemeriksaan_lab pl', 'p.pasien_id = pl.pasien_id', 'inner');
        $exam_query = $this->db->get();
        $stats['with_examinations'] = $exam_query->row_array()['count'];
        
        // Patients by referral source
        $this->db->select('
            SUM(CASE WHEN asal_rujukan IS NOT NULL AND asal_rujukan != "" THEN 1 ELSE 0 END) as with_referral,
            SUM(CASE WHEN asal_rujukan IS NULL OR asal_rujukan = "" THEN 1 ELSE 0 END) as without_referral
        ');
        $referral_query = $this->db->get('pasien');
        $referral_data = $referral_query->row_array();
        
        $stats['by_referral'] = array(
            'with_referral' => (int)$referral_data['with_referral'],
            'without_referral' => (int)$referral_data['without_referral']
        );
        
        return $stats;
    }

    /**
     * Get chart data for patient analytics
     */
    public function get_patient_chart_data()
    {
        $chart_data = array();
        
        // Gender distribution pie chart
        $this->db->select('jenis_kelamin, COUNT(*) as count');
        $this->db->group_by('jenis_kelamin');
        $gender_query = $this->db->get('pasien');
        
        $chart_data['gender_distribution'] = array();
        foreach ($gender_query->result_array() as $row) {
            $chart_data['gender_distribution'][] = array(
                'label' => $row['jenis_kelamin'] === 'L' ? 'Laki-laki' : 'Perempuan',
                'value' => (int)$row['count']
            );
        }
        
        // Age group distribution
        $this->db->select('
            SUM(CASE WHEN umur < 18 THEN 1 ELSE 0 END) as child,
            SUM(CASE WHEN umur >= 18 AND umur < 30 THEN 1 ELSE 0 END) as young_adult,
            SUM(CASE WHEN umur >= 30 AND umur < 50 THEN 1 ELSE 0 END) as middle_age,
            SUM(CASE WHEN umur >= 50 AND umur < 65 THEN 1 ELSE 0 END) as senior,
            SUM(CASE WHEN umur >= 65 THEN 1 ELSE 0 END) as elderly
        ');
        $age_query = $this->db->get('pasien');
        $age_data = $age_query->row_array();
        
        $chart_data['age_distribution'] = array(
            array('label' => 'Anak (<18)', 'value' => (int)$age_data['child']),
            array('label' => 'Dewasa Muda (18-29)', 'value' => (int)$age_data['young_adult']),
            array('label' => 'Dewasa (30-49)', 'value' => (int)$age_data['middle_age']),
            array('label' => 'Lansia Awal (50-64)', 'value' => (int)$age_data['senior']),
            array('label' => 'Lansia (65+)', 'value' => (int)$age_data['elderly'])
        );
        
        // Monthly registration trend (last 6 months)
        $chart_data['monthly_registrations'] = array();
        for ($i = 5; $i >= 0; $i--) {
            $month = date('Y-m', strtotime("-{$i} months"));
            $month_start = $month . '-01';
            $month_end = date('Y-m-t', strtotime($month_start));
            
            $this->db->where('DATE(created_at) >=', $month_start);
            $this->db->where('DATE(created_at) <=', $month_end);
            $count = $this->db->count_all_results('pasien');
            
            $chart_data['monthly_registrations'][] = array(
                'month' => date('M Y', strtotime($month_start)),
                'count' => $count
            );
        }
        
        return $chart_data;
    }

    // ==========================================
    // UTILITY FUNCTIONS
    // ==========================================

    /**
     * Generate unique registration number
     */
    public function generate_registration_number()
    {
        $prefix = 'REG';
        $year = date('Y');
        
        // Get the last registration number for this year
        $this->db->like('nomor_registrasi', $prefix . $year, 'after');
        $this->db->order_by('nomor_registrasi', 'DESC');
        $this->db->limit(1);
        $query = $this->db->get('pasien');
        
        if ($query->num_rows() > 0) {
            $last_number = $query->row_array()['nomor_registrasi'];
            $last_sequence = (int)substr($last_number, -5);
            $new_sequence = $last_sequence + 1;
        } else {
            $new_sequence = 1;
        }
        
        return $prefix . $year . str_pad($new_sequence, 5, '0', STR_PAD_LEFT);
    }

    /**
     * Check if NIK already exists (for validation)
     */
    public function check_nik_exists($nik, $exclude_patient_id = null)
    {
        $this->db->where('nik', $nik);
        
        if ($exclude_patient_id !== null) {
            $this->db->where('pasien_id !=', $exclude_patient_id);
        }
        
        $query = $this->db->get('pasien');
        return $query->num_rows() > 0;
    }

    /**
     * Check if patient has examination records
     */
    public function check_patient_examinations($patient_id)
    {
        $this->db->where('pasien_id', $patient_id);
        $count = $this->db->count_all_results('pemeriksaan_lab');
        return $count > 0;
    }

    /**
     * Get patients for dropdown/select options
     */
    public function get_patients_for_select($search = null, $limit = 50)
    {
        $this->db->select('pasien_id, nama, nik, nomor_registrasi, telepon');
        $this->db->from('pasien');
        
        if ($search) {
            $this->db->group_start();
            $this->db->like('nama', $search);
            $this->db->or_like('nik', $search);
            $this->db->or_like('nomor_registrasi', $search);
            $this->db->group_end();
        }
        
        $this->db->order_by('nama', 'ASC');
        $this->db->limit($limit);
        
        $query = $this->db->get();
        return $query->result_array();
    }

    /**
     * Get patient with examination history
     */
    public function get_patient_with_examinations($patient_id)
    {
        // Get patient data
        $patient = $this->get_patient_by_id($patient_id);
        
        if (!$patient) {
            return null;
        }
        
        // Get examination history
        $this->db->select('
            pl.pemeriksaan_id, pl.nomor_pemeriksaan, pl.tanggal_pemeriksaan,
            pl.jenis_pemeriksaan, pl.status_pemeriksaan, pl.biaya,
            pt.nama_petugas, l.nama as nama_lab
        ');
        $this->db->from('pemeriksaan_lab pl');
        $this->db->join('petugas_lab pt', 'pl.petugas_id = pt.petugas_id', 'left');
        $this->db->join('lab l', 'pl.lab_id = l.lab_id', 'left');
        $this->db->where('pl.pasien_id', $patient_id);
        $this->db->order_by('pl.tanggal_pemeriksaan', 'DESC');
        
        $examinations = $this->db->get()->result_array();
        
        $patient['examinations'] = $examinations;
        $patient['total_examinations'] = count($examinations);
        
        return $patient;
    }

    /**
     * Get recent patients (for dashboard widgets)
     */
    public function get_recent_patients($limit = 5)
    {
        $this->db->select('pasien_id, nama, jenis_kelamin, umur, nomor_registrasi, created_at');
        $this->db->from('pasien');
        $this->db->order_by('created_at', 'DESC');
        $this->db->limit($limit);
        
        $query = $this->db->get();
        return $query->result_array();
    }

    /**
     * Get patients by age range
     */
    public function get_patients_by_age_range($min_age = null, $max_age = null, $limit = null)
    {
        $this->db->select('pasien_id, nama, umur, jenis_kelamin, telepon, nomor_registrasi');
        $this->db->from('pasien');
        
        if ($min_age !== null) {
            $this->db->where('umur >=', $min_age);
        }
        
        if ($max_age !== null) {
            $this->db->where('umur <=', $max_age);
        }
        
        $this->db->order_by('umur', 'ASC');
        
        if ($limit !== null) {
            $this->db->limit($limit);
        }
        
        $query = $this->db->get();
        return $query->result_array();
    }

    /**
     * Get patients by referral source
     */
    public function get_patients_by_referral($asal_rujukan = null)
    {
        $this->db->select('
            pasien_id, nama, nik, jenis_kelamin, telepon, nomor_registrasi,
            dokter_perujuk, asal_rujukan, nomor_rujukan, tanggal_rujukan,
            diagnosis_awal, created_at
        ');
        $this->db->from('pasien');
        
        if ($asal_rujukan !== null) {
            $this->db->where('asal_rujukan', $asal_rujukan);
        } else {
            $this->db->where('asal_rujukan IS NOT NULL');
            $this->db->where('asal_rujukan !=', '');
        }
        
        $this->db->order_by('created_at', 'DESC');
        
        $query = $this->db->get();
        return $query->result_array();
    }

    // ==========================================
    // PRIVATE HELPER METHODS
    // ==========================================

    /**
     * Apply filters to current query
     */
    private function _apply_filters($filters)
    {
        if (!empty($filters['start_date'])) {
            $this->db->where('DATE(created_at) >=', $filters['start_date']);
        }
        
        if (!empty($filters['end_date'])) {
            $this->db->where('DATE(created_at) <=', $filters['end_date']);
        }
        
        if (!empty($filters['gender'])) {
            $this->db->where('jenis_kelamin', $filters['gender']);
        }
        
        if (!empty($filters['age_range'])) {
            switch ($filters['age_range']) {
                case 'child':
                    $this->db->where('umur <', 18);
                    break;
                case 'adult':
                    $this->db->where('umur >=', 18);
                    $this->db->where('umur <', 60);
                    break;
                case 'elderly':
                    $this->db->where('umur >=', 60);
                    break;
            }
        }
        
        if (!empty($filters['search'])) {
            $this->db->group_start();
            $this->db->like('nama', $filters['search']);
            $this->db->or_like('nik', $filters['search']);
            $this->db->or_like('telepon', $filters['search']);
            $this->db->or_like('nomor_registrasi', $filters['search']);
            $this->db->group_end();
        }
    }
    public function get_patients_paginated($filters = [], $limit = 20, $offset = 0)
{
    try {
        $this->db->select('
            pasien_id,
            nomor_registrasi,
            nama,
            nik,
            jenis_kelamin,
            umur,
            telepon,
            asal_rujukan,
            dokter_perujuk,
            created_at
        ');
        $this->db->from('pasien');
        
        // Apply filters
        if (!empty($filters['search'])) {
            $search = $filters['search'];
            $this->db->group_start();
            $this->db->like('nama', $search);
            $this->db->or_like('nik', $search);
            $this->db->or_like('nomor_registrasi', $search);
            $this->db->or_like('telepon', $search);
            $this->db->group_end();
        }
        
        if (!empty($filters['gender'])) {
            $this->db->where('jenis_kelamin', $filters['gender']);
        }
        
        if (!empty($filters['start_date'])) {
            $this->db->where('DATE(created_at) >=', $filters['start_date']);
        }
        
        if (!empty($filters['end_date'])) {
            $this->db->where('DATE(created_at) <=', $filters['end_date']);
        }
        
        if (!empty($filters['age_min'])) {
            $this->db->where('umur >=', $filters['age_min']);
        }
        
        if (!empty($filters['age_max'])) {
            $this->db->where('umur <=', $filters['age_max']);
        }
        
        $this->db->order_by('created_at', 'DESC');
        $this->db->limit($limit, $offset);
        
        return $this->db->get()->result_array();
        
    } catch (Exception $e) {
        log_message('error', 'Error getting patients paginated: ' . $e->getMessage());
        return [];
    }
}

/**
 * Count patients with filters (for pagination)
 */
public function count_patients($filters = [])
{
    try {
        $this->db->from('pasien');
        
        if (!empty($filters['search'])) {
            $search = $filters['search'];
            $this->db->group_start();
            $this->db->like('nama', $search);
            $this->db->or_like('nik', $search);
            $this->db->or_like('nomor_registrasi', $search);
            $this->db->or_like('telepon', $search);
            $this->db->group_end();
        }
        
        if (!empty($filters['gender'])) {
            $this->db->where('jenis_kelamin', $filters['gender']);
        }
        
        if (!empty($filters['start_date'])) {
            $this->db->where('DATE(created_at) >=', $filters['start_date']);
        }
        
        if (!empty($filters['end_date'])) {
            $this->db->where('DATE(created_at) <=', $filters['end_date']);
        }
        
        if (!empty($filters['age_min'])) {
            $this->db->where('umur >=', $filters['age_min']);
        }
        
        if (!empty($filters['age_max'])) {
            $this->db->where('umur <=', $filters['age_max']);
        }
        
        return $this->db->count_all_results();
        
    } catch (Exception $e) {
        log_message('error', 'Error counting patients: ' . $e->getMessage());
        return 0;
    }
}

/**
 * Get last registration number with prefix
 */
public function get_last_registration_number($prefix)
{
    try {
        $this->db->where('nomor_registrasi LIKE', $prefix . '%');
        $this->db->order_by('nomor_registrasi', 'DESC');
        $this->db->limit(1);
        $query = $this->db->get('pasien');
        
        return $query->num_rows() > 0 ? $query->row_array() : FALSE;
    } catch (Exception $e) {
        log_message('error', 'Error getting last registration number: ' . $e->getMessage());
        return FALSE;
    }
}

/**
 * Check if patient has invoices (through examinations)
 */
public function check_patient_has_invoices($patient_id)
{
    try {
        $this->db->select('i.invoice_id');
        $this->db->from('invoice i');
        $this->db->join('pemeriksaan_lab pl', 'i.pemeriksaan_id = pl.pemeriksaan_id');
        $this->db->where('pl.pasien_id', $patient_id);
        
        return $this->db->count_all_results() > 0;
    } catch (Exception $e) {
        log_message('error', 'Error checking patient invoices: ' . $e->getMessage());
        return FALSE;
    }
}

/**
 * Search patients with advanced filters (for autocomplete)
 */
public function search_patients_advanced($search_term, $limit = 10)
{
    try {
        $this->db->select('pasien_id, nama, nik, nomor_registrasi, telepon, jenis_kelamin');
        $this->db->from('pasien');
        
        if (!empty($search_term)) {
            $this->db->group_start();
            $this->db->like('nama', $search_term);
            $this->db->or_like('nik', $search_term);
            $this->db->or_like('nomor_registrasi', $search_term);
            $this->db->or_like('telepon', $search_term);
            $this->db->group_end();
        }
        
        $this->db->order_by('created_at', 'DESC');
        $this->db->limit($limit);
        
        return $this->db->get()->result_array();
        
    } catch (Exception $e) {
        log_message('error', 'Error searching patients: ' . $e->getMessage());
        return [];
    }
}

/**
 * Get patient statistics with week and month counts
 */
public function get_patient_statistics_extended()
{
    try {
        $stats = [];
        
        // Total patients
        $stats['total'] = $this->db->count_all('pasien');
        
        // Today's registrations
        $this->db->where('DATE(created_at)', date('Y-m-d'));
        $stats['today'] = $this->db->count_all_results('pasien');
        
        // This week
        $this->db->where('WEEK(created_at)', date('W'));
        $this->db->where('YEAR(created_at)', date('Y'));
        $stats['this_week'] = $this->db->count_all_results('pasien');
        
        // This month
        $this->db->where('MONTH(created_at)', date('m'));
        $this->db->where('YEAR(created_at)', date('Y'));
        $stats['this_month'] = $this->db->count_all_results('pasien');
        
        // Gender distribution
        $this->db->where('jenis_kelamin', 'L');
        $stats['male'] = $this->db->count_all_results('pasien');
        
        $this->db->where('jenis_kelamin', 'P');
        $stats['female'] = $this->db->count_all_results('pasien');
        
        // Age groups
        $this->db->where('umur <', 18);
        $stats['children'] = $this->db->count_all_results('pasien');
        
        $this->db->where('umur >=', 18);
        $this->db->where('umur <', 60);
        $stats['adults'] = $this->db->count_all_results('pasien');
        
        $this->db->where('umur >=', 60);
        $stats['elderly'] = $this->db->count_all_results('pasien');
        
        return $stats;
        
    } catch (Exception $e) {
        log_message('error', 'Error getting patient statistics: ' . $e->getMessage());
        return [
            'total' => 0,
            'today' => 0,
            'this_week' => 0,
            'this_month' => 0,
            'male' => 0,
            'female' => 0,
            'children' => 0,
            'adults' => 0,
            'elderly' => 0
        ];
    }
}

/**
 * Validate NIK format (Indonesian ID number)
 */
public function validate_nik($nik)
{
    // NIK must be exactly 16 digits
    if (!preg_match('/^[0-9]{16}$/', $nik)) {
        return false;
    }
    
    // Additional validation could be added here:
    // - Check province code (2 first digits)
    // - Check city/regency code (2 next digits)
    // - Check district code (2 next digits)
    // - Check birth date (6 next digits: DDMMYY)
    // - Check sequence number (4 last digits)
    
    return true;
}

/**
 * Calculate age from birth date
 */
public function calculate_age($birth_date)
{
    if (empty($birth_date)) {
        return null;
    }
    
    try {
        $birth = new DateTime($birth_date);
        $today = new DateTime();
        $age = $birth->diff($today)->y;
        
        return $age;
    } catch (Exception $e) {
        log_message('error', 'Error calculating age: ' . $e->getMessage());
        return null;
    }
}

/**
 * Get patients with complete examination history
 */
public function get_patients_with_exam_history($limit = 20, $offset = 0, $filters = [])
{
    try {
        $this->db->select('
            p.*,
            COUNT(DISTINCT pl.pemeriksaan_id) as total_examinations,
            MAX(pl.tanggal_pemeriksaan) as last_examination_date
        ');
        $this->db->from('pasien p');
        $this->db->join('pemeriksaan_lab pl', 'p.pasien_id = pl.pasien_id', 'left');
        
        if (!empty($filters['search'])) {
            $search = $filters['search'];
            $this->db->group_start();
            $this->db->like('p.nama', $search);
            $this->db->or_like('p.nik', $search);
            $this->db->or_like('p.nomor_registrasi', $search);
            $this->db->group_end();
        }
        
        if (!empty($filters['gender'])) {
            $this->db->where('p.jenis_kelamin', $filters['gender']);
        }
        
        if (!empty($filters['has_examinations'])) {
            $this->db->having('total_examinations >', 0);
        }
        
        $this->db->group_by('p.pasien_id');
        $this->db->order_by('p.created_at', 'DESC');
        $this->db->limit($limit, $offset);
        
        return $this->db->get()->result_array();
        
    } catch (Exception $e) {
        log_message('error', 'Error getting patients with exam history: ' . $e->getMessage());
        return [];
    }
}

/**
 * Export patients data for Excel/CSV
 */
public function get_patients_for_export($filters = [])
{
    try {
        $this->db->select('
            nomor_registrasi,
            nama,
            nik,
            jenis_kelamin,
            tempat_lahir,
            tanggal_lahir,
            umur,
            alamat_domisili,
            pekerjaan,
            telepon,
            kontak_darurat,
            dokter_perujuk,
            asal_rujukan,
            created_at
        ');
        $this->db->from('pasien');
        
        if (!empty($filters['search'])) {
            $search = $filters['search'];
            $this->db->group_start();
            $this->db->like('nama', $search);
            $this->db->or_like('nik', $search);
            $this->db->or_like('nomor_registrasi', $search);
            $this->db->group_end();
        }
        
        if (!empty($filters['gender'])) {
            $this->db->where('jenis_kelamin', $filters['gender']);
        }
        
        if (!empty($filters['start_date'])) {
            $this->db->where('DATE(created_at) >=', $filters['start_date']);
        }
        
        if (!empty($filters['end_date'])) {
            $this->db->where('DATE(created_at) <=', $filters['end_date']);
        }
        
        $this->db->order_by('created_at', 'DESC');
        
        return $this->db->get();
        
    } catch (Exception $e) {
        log_message('error', 'Error getting patients for export: ' . $e->getMessage());
        return false;
    }
}

/**
 * Get top referring doctors
 */
public function get_top_referring_doctors($limit = 10)
{
    try {
        $this->db->select('
            dokter_perujuk,
            asal_rujukan,
            COUNT(*) as total_referrals
        ');
        $this->db->from('pasien');
        $this->db->where('dokter_perujuk IS NOT NULL');
        $this->db->where('dokter_perujuk !=', '');
        $this->db->group_by('dokter_perujuk, asal_rujukan');
        $this->db->order_by('total_referrals', 'DESC');
        $this->db->limit($limit);
        
        return $this->db->get()->result_array();
        
    } catch (Exception $e) {
        log_message('error', 'Error getting top referring doctors: ' . $e->getMessage());
        return [];
    }
}

/**
 * Get patients birthday this month (for reminder)
 */
public function get_birthday_patients_this_month()
{
    try {
        $current_month = date('m');
        
        $this->db->select('pasien_id, nama, tanggal_lahir, umur, telepon');
        $this->db->from('pasien');
        $this->db->where('MONTH(tanggal_lahir)', $current_month);
        $this->db->where('tanggal_lahir IS NOT NULL');
        $this->db->order_by('DAY(tanggal_lahir)', 'ASC');
        
        return $this->db->get()->result_array();
        
    } catch (Exception $e) {
        log_message('error', 'Error getting birthday patients: ' . $e->getMessage());
        return [];
    }
}

/**
 * Batch update patients ages (maintenance function)
 */
public function batch_update_ages()
{
    try {
        $this->db->trans_start();
        
        $this->db->select('pasien_id, tanggal_lahir');
        $this->db->where('tanggal_lahir IS NOT NULL');
        $patients = $this->db->get('pasien')->result_array();
        
        $updated_count = 0;
        
        foreach ($patients as $patient) {
            $new_age = $this->calculate_age($patient['tanggal_lahir']);
            
            if ($new_age !== null) {
                $this->db->where('pasien_id', $patient['pasien_id']);
                $this->db->update('pasien', ['umur' => $new_age]);
                $updated_count++;
            }
        }
        
        $this->db->trans_complete();
        
        return [
            'success' => $this->db->trans_status() !== FALSE,
            'updated_count' => $updated_count
        ];
        
    } catch (Exception $e) {
        $this->db->trans_rollback();
        log_message('error', 'Error batch updating ages: ' . $e->getMessage());
        return [
            'success' => false,
            'updated_count' => 0
        ];
    }
}
/**
 * Create new patient - FIXED for AUTO_INCREMENT compatibility
 */
public function create_patient($patient_data)
{
    $this->db->trans_start();
    
    try {
        // PERBAIKAN: Pastikan pasien_id tidak ada dalam data yang akan diinsert
        if (isset($patient_data['pasien_id'])) {
            unset($patient_data['pasien_id']);
        }
        
        // Clean and validate data sebelum insert
        $clean_data = array();
        $allowed_fields = array(
            'nama', 'nik', 'jenis_kelamin', 'tempat_lahir', 'tanggal_lahir', 'umur',
            'alamat_domisili', 'pekerjaan', 'telepon', 'kontak_darurat', 'riwayat_pasien',
            'permintaan_pemeriksaan', 'dokter_perujuk', 'asal_rujukan', 'nomor_rujukan',
            'tanggal_rujukan', 'diagnosis_awal', 'rekomendasi_pemeriksaan', 
            'nomor_registrasi', 'created_at'
        );
        
        foreach ($allowed_fields as $field) {
            if (isset($patient_data[$field])) {
                $clean_data[$field] = $patient_data[$field];
            }
        }
        
        // Set default created_at jika tidak ada
        if (!isset($clean_data['created_at'])) {
            $clean_data['created_at'] = date('Y-m-d H:i:s');
        }
        
        // Insert data
        $this->db->insert('pasien', $clean_data);
        $patient_id = $this->db->insert_id();
        
        $this->db->trans_complete();
        
        if ($this->db->trans_status() === FALSE || !$patient_id || $patient_id == 0) {
            $error = $this->db->error();
            log_message('error', 'Failed to create patient. Error: ' . json_encode($error));
            log_message('error', 'Data attempted: ' . json_encode($clean_data));
            return false;
        }
        
        return $patient_id;
        
    } catch (Exception $e) {
        $this->db->trans_rollback();
        log_message('error', 'Exception creating patient: ' . $e->getMessage());
        return false;
    }
}

/**
 * Update patient data - FIXED to prevent pasien_id modification
 */
public function update_patient($patient_id, $patient_data)
{
    $this->db->trans_start();
    
    try {
        // PERBAIKAN: Pastikan pasien_id tidak di-update
        if (isset($patient_data['pasien_id'])) {
            unset($patient_data['pasien_id']);
        }
        
        // Pastikan nomor_registrasi tidak berubah saat update
        if (isset($patient_data['nomor_registrasi'])) {
            unset($patient_data['nomor_registrasi']);
        }
        
        // Clean data
        $clean_data = array();
        $allowed_fields = array(
            'nama', 'nik', 'jenis_kelamin', 'tempat_lahir', 'tanggal_lahir', 'umur',
            'alamat_domisili', 'pekerjaan', 'telepon', 'kontak_darurat', 'riwayat_pasien',
            'permintaan_pemeriksaan', 'dokter_perujuk', 'asal_rujukan', 'nomor_rujukan',
            'tanggal_rujukan', 'diagnosis_awal', 'rekomendasi_pemeriksaan'
        );
        
        foreach ($allowed_fields as $field) {
            if (isset($patient_data[$field])) {
                $clean_data[$field] = $patient_data[$field];
            }
        }
        
        if (empty($clean_data)) {
            log_message('error', 'No valid data to update for patient_id: ' . $patient_id);
            return false;
        }
        
        $this->db->where('pasien_id', $patient_id);
        $this->db->update('pasien', $clean_data);
        
        $this->db->trans_complete();
        
        if ($this->db->trans_status() === FALSE) {
            $error = $this->db->error();
            log_message('error', 'Failed to update patient. Error: ' . json_encode($error));
            return false;
        }
        
        return true;
        
    } catch (Exception $e) {
        $this->db->trans_rollback();
        log_message('error', 'Exception updating patient: ' . $e->getMessage());
        return false;
    }
}
}