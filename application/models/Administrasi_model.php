<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Administrasi_model extends CI_Model {

    public function __construct() {
        parent::__construct();
        $this->load->database();
    }

    public function create_patient($data) {
        $this->db->insert('pasien', $data);
        return $this->db->insert_id();
    }

    public function get_all_patients() {
        $this->db->order_by('created_at', 'DESC');
        return $this->db->get('pasien')->result_array();
    }

    public function search_patients($search) {
        $this->db->group_start();
        $this->db->like('nama', $search);
        $this->db->or_like('nik', $search);
        $this->db->or_like('nomor_registrasi', $search);
        $this->db->group_end();
        $this->db->order_by('created_at', 'DESC');
        
        return $this->db->get('pasien')->result_array();
    }

    

    public function get_patient_by_nik($nik) {
        $this->db->where('nik', $nik);
        $query = $this->db->get('pasien');
        
        if ($query->num_rows() == 1) {
            return $query->row_array();
        }
        
        return false;
    }


    public function get_last_registration_number($prefix) {
        $this->db->where('nomor_registrasi LIKE', $prefix . '%');
        $this->db->order_by('nomor_registrasi', 'DESC');
        $this->db->limit(1);
        $query = $this->db->get('pasien');
        
        if ($query->num_rows() == 1) {
            return $query->row_array();
        }
        
        return false;
    }

    public function create_examination($data) {
        $this->db->insert('pemeriksaan_lab', $data);
        return $this->db->insert_id();
    }

    public function get_patients_with_pending_exams() {
        $this->db->select('pl.pemeriksaan_id, pl.nomor_pemeriksaan, pl.jenis_pemeriksaan, 
                          pl.tanggal_pemeriksaan, pl.status_pemeriksaan,
                          p.nama as nama_pasien, p.nik, p.nomor_registrasi');
        $this->db->from('pemeriksaan_lab pl');
        $this->db->join('pasien p', 'pl.pasien_id = p.pasien_id');
        $this->db->where_in('pl.status_pemeriksaan', array('pending', 'progress', 'selesai'));
        $this->db->where('pl.pemeriksaan_id NOT IN (SELECT pemeriksaan_id FROM invoice)');
        $this->db->order_by('pl.created_at', 'DESC');
        
        return $this->db->get()->result_array();
    }

    public function create_invoice($data) {
        $this->db->insert('invoice', $data);
        return $this->db->insert_id();
    }

    public function get_invoice_by_id($invoice_id) {
        $this->db->select('i.*, p.nama as nama_pasien, p.nik, 
                          pl.nomor_pemeriksaan, pl.jenis_pemeriksaan');
        $this->db->from('invoice i');
        $this->db->join('pemeriksaan_lab pl', 'i.pemeriksaan_id = pl.pemeriksaan_id');
        $this->db->join('pasien p', 'pl.pasien_id = p.pasien_id');
        $this->db->where('i.invoice_id', $invoice_id);
        
        $query = $this->db->get();
        if ($query->num_rows() == 1) {
            return $query->row_array();
        }
        
        return false;
    }

    public function get_pending_invoices() {
        $this->db->select('i.*, p.nama as nama_pasien, p.nik, 
                          pl.nomor_pemeriksaan, pl.jenis_pemeriksaan');
        $this->db->from('invoice i');
        $this->db->join('pemeriksaan_lab pl', 'i.pemeriksaan_id = pl.pemeriksaan_id');
        $this->db->join('pasien p', 'pl.pasien_id = p.pasien_id');
        $this->db->where('i.status_pembayaran', 'belum_bayar');
        $this->db->order_by('i.tanggal_invoice', 'DESC');
        
        return $this->db->get()->result_array();
    }

    public function get_recent_payments() {
        $this->db->select('i.*, p.nama as nama_pasien, p.nik, 
                          pl.nomor_pemeriksaan, pl.jenis_pemeriksaan');
        $this->db->from('invoice i');
        $this->db->join('pemeriksaan_lab pl', 'i.pemeriksaan_id = pl.pemeriksaan_id');
        $this->db->join('pasien p', 'pl.pasien_id = p.pasien_id');
        $this->db->where('i.status_pembayaran', 'lunas');
        $this->db->order_by('i.tanggal_pembayaran', 'DESC');
        $this->db->limit(10);
        
        return $this->db->get()->result_array();
    }

    public function update_payment_status($invoice_id, $data) {
        $this->db->where('invoice_id', $invoice_id);
        return $this->db->update('invoice', $data);
    }



    public function get_last_invoice_number($prefix) {
        $this->db->where('nomor_invoice LIKE', $prefix . '%');
        $this->db->order_by('nomor_invoice', 'DESC');
        $this->db->limit(1);
        $query = $this->db->get('invoice');
        
        if ($query->num_rows() == 1) {
            return $query->row_array();
        }
        
        return false;
    }

  

    public function get_patients_for_export($date_range = null) {
        if ($date_range) {
            $dates = explode(' - ', $date_range);
            if (count($dates) == 2) {
                $this->db->where('created_at >=', $dates[0]);
                $this->db->where('created_at <=', $dates[1] . ' 23:59:59');
            }
        }
        
        $this->db->order_by('created_at', 'DESC');
        $query = $this->db->get('pasien');
        
        return $query;
    }

    public function get_invoices_for_export($date_range = null) {
        $this->db->select('i.*, p.nama as nama_pasien, p.nik, 
                          pl.nomor_pemeriksaan, pl.jenis_pemeriksaan');
        $this->db->from('invoice i');
        $this->db->join('pemeriksaan_lab pl', 'i.pemeriksaan_id = pl.pemeriksaan_id');
        $this->db->join('pasien p', 'pl.pasien_id = p.pasien_id');
        
        if ($date_range) {
            $dates = explode(' - ', $date_range);
            if (count($dates) == 2) {
                $this->db->where('i.tanggal_invoice >=', $dates[0]);
                $this->db->where('i.tanggal_invoice <=', $dates[1]);
            }
        }
        
        $this->db->order_by('i.tanggal_invoice', 'DESC');
        $query = $this->db->get();
        
        return $query;
    }

    public function get_examinations_for_export($date_range = null) {
        $this->db->select('pl.*, p.nama as nama_pasien, p.nik, 
                          p.dokter_perujuk, p.asal_rujukan, pt.nama_petugas');
        $this->db->from('pemeriksaan_lab pl');
        $this->db->join('pasien p', 'pl.pasien_id = p.pasien_id');
        $this->db->join('petugas_lab pt', 'pl.petugas_id = pt.petugas_id', 'left');
        
        if ($date_range) {
            $dates = explode(' - ', $date_range);
            if (count($dates) == 2) {
                $this->db->where('pl.tanggal_pemeriksaan >=', $dates[0]);
                $this->db->where('pl.tanggal_pemeriksaan <=', $dates[1]);
            }
        }
        
        $this->db->order_by('pl.tanggal_pemeriksaan', 'DESC');
        $query = $this->db->get();
        
        return $query;
    }

    public function get_total_patients() {
        return $this->db->count_all('pasien');
    }

    public function get_monthly_revenue($month = null) {
        if (!$month) {
            $month = date('Y-m');
        }
        
        $this->db->select('SUM(total_biaya) as total');
        // FIXED: Proper DATE_FORMAT syntax
        $this->db->where("DATE_FORMAT(tanggal_pembayaran, '%Y-%m') = ", $month, FALSE);
        $this->db->where('status_pembayaran', 'lunas');
        $query = $this->db->get('invoice');
        
        $result = $query->row_array();
        return $result['total'] ? (float)$result['total'] : 0;
    }

    public function check_nik_exists($nik, $exclude_id = null) {
        $this->db->where('nik', $nik);
        if ($exclude_id) {
            $this->db->where('pasien_id !=', $exclude_id);
        }
        
        return $this->db->count_all_results('pasien') > 0;
    }

    public function get_registration_statistics()
    {
        $this->db->select('
            COUNT(*) as total_pasien,
            COUNT(CASE WHEN DATE(created_at) = CURDATE() THEN 1 END) as registrasi_hari_ini,
            COUNT(CASE WHEN WEEK(created_at) = WEEK(NOW()) AND YEAR(created_at) = YEAR(NOW()) THEN 1 END) as registrasi_minggu_ini,
            COUNT(CASE WHEN MONTH(created_at) = MONTH(NOW()) AND YEAR(created_at) = YEAR(NOW()) THEN 1 END) as registrasi_bulan_ini
        ');
        return $this->db->get('pasien')->row_array();
    }

    public function get_payment_statistics()
    {
        $this->db->select('
            AVG(total_biaya) as rata_rata_biaya,
            MAX(total_biaya) as biaya_tertinggi,
            MIN(total_biaya) as biaya_terendah,
            COUNT(CASE WHEN jenis_pembayaran = "umum" THEN 1 END) as pembayaran_umum,
            COUNT(CASE WHEN jenis_pembayaran = "bpjs" THEN 1 END) as pembayaran_bpjs
        ');
        return $this->db->get('invoice')->row_array();
    }

    /**
     * Get Recent Registrations
     */
    public function get_recent_registrations($limit = 5)
    {
        $this->db->select('pasien_id, nama, nik, jenis_kelamin, telepon, nomor_registrasi, created_at');
        $this->db->order_by('created_at', 'DESC');
        $this->db->limit($limit);
        return $this->db->get('pasien')->result_array();
    }


public function get_dashboard_statistics() {
    $stats = array();
    
    // Total patients
    $stats['total_patients'] = $this->db->count_all('pasien');
    
    // Today's registrations
    $stats['today_registrations'] = $this->db->where('DATE(created_at)', date('Y-m-d'))->count_all_results('pasien');
    
    // Pending examinations
    $stats['pending_examinations'] = $this->db->where('status_pemeriksaan', 'pending')->count_all_results('pemeriksaan_lab');
    
    // Completed examinations (this month)
    $stats['completed_examinations'] = $this->db->where('status_pemeriksaan', 'selesai')
                                               ->where('MONTH(tanggal_pemeriksaan)', date('m'))
                                               ->where('YEAR(tanggal_pemeriksaan)', date('Y'))
                                               ->count_all_results('pemeriksaan_lab');
    
    // Pending payments
    $stats['pending_payments'] = $this->db->where('status_pembayaran', 'belum_bayar')->count_all_results('invoice');
    
    // Total revenue (this month)
    $this->db->select('SUM(total_biaya) as total_revenue');
    $this->db->where('status_pembayaran', 'lunas');
    $this->db->where('MONTH(tanggal_pembayaran)', date('m'));
    $this->db->where('YEAR(tanggal_pembayaran)', date('Y'));
    $revenue_query = $this->db->get('invoice');
    $stats['monthly_revenue'] = $revenue_query->row()->total_revenue ?: 0;
    
    return $stats;
}


public function get_recent_activities($limit = 10) {
    $this->db->select('al.*, u.username, ud.nama_lengkap');
    $this->db->from('activity_log al');
    $this->db->join('users u', 'al.user_id = u.user_id');
    $this->db->join('v_user_details ud', 'u.user_id = ud.user_id', 'left');
    $this->db->order_by('al.created_at', 'DESC');
    $this->db->limit($limit);
    
    return $this->db->get()->result_array();
}


public function get_monthly_registration_trend($months = 6) {
    $trend = array();
    
    for ($i = $months - 1; $i >= 0; $i--) {
        $month = date('Y-m', strtotime("-$i months"));
        $month_name = date('M Y', strtotime($month . '-01'));
        
        $this->db->where("DATE_FORMAT(created_at, '%Y-%m') = ", $month);
        $count = $this->db->count_all_results('pasien');
        
        $trend[] = array(
            'month' => $month_name,
            'count' => $count
        );
    }
    
    return $trend;
}


public function get_examination_status_distribution() {
    $this->db->select('status_pemeriksaan, COUNT(*) as count');
    $this->db->group_by('status_pemeriksaan');
    $query = $this->db->get('pemeriksaan_lab');
    
    $distribution = array();
    foreach ($query->result_array() as $row) {
        $distribution[$row['status_pemeriksaan']] = $row['count'];
    }
    
    return $distribution;
}


public function get_top_referring_doctors($limit = 5) {
    $this->db->select('dokter_perujuk, COUNT(*) as referral_count');
    $this->db->where('dokter_perujuk IS NOT NULL');
    $this->db->where('dokter_perujuk !=', '');
    $this->db->group_by('dokter_perujuk');
    $this->db->order_by('referral_count', 'DESC');
    $this->db->limit($limit);
    
    return $this->db->get('pasien')->result_array();
}


public function get_patient_age_distribution() {
    $distribution = array(
        '0-17' => 0,
        '18-30' => 0,
        '31-45' => 0,
        '46-60' => 0,
        '61+' => 0
    );
    
    $patients = $this->db->get('pasien')->result_array();
    
    foreach ($patients as $patient) {
        $age = $patient['umur'];
        
        if ($age <= 17) {
            $distribution['0-17']++;
        } elseif ($age <= 30) {
            $distribution['18-30']++;
        } elseif ($age <= 45) {
            $distribution['31-45']++;
        } elseif ($age <= 60) {
            $distribution['46-60']++;
        } else {
            $distribution['61+']++;
        }
    }
    
    return $distribution;
}


public function get_upcoming_appointments($days = 7) {
    $this->db->select('pl.*, p.nama as nama_pasien, p.telepon, p.dokter_perujuk');
    $this->db->from('pemeriksaan_lab pl');
    $this->db->join('pasien p', 'pl.pasien_id = p.pasien_id');
    $this->db->where('pl.tanggal_pemeriksaan >=', date('Y-m-d'));
    $this->db->where('pl.tanggal_pemeriksaan <=', date('Y-m-d', strtotime("+$days days")));
    $this->db->order_by('pl.tanggal_pemeriksaan', 'ASC');
    $this->db->order_by('pl.created_at', 'ASC');
    
    return $this->db->get()->result_array();
}


public function get_low_stock_alerts() {
    $this->db->where('status', 'Hampir Habis');
    $this->db->or_where('jumlah_stok <= stok_minimal');
    $this->db->order_by('jumlah_stok', 'ASC');
    
    return $this->db->get('reagen')->result_array();
}


public function get_equipment_maintenance_schedule() {
    $this->db->where('jadwal_kalibrasi >=', date('Y-m-d'));
    $this->db->where('jadwal_kalibrasi <=', date('Y-m-d', strtotime('+30 days')));
    $this->db->order_by('jadwal_kalibrasi', 'ASC');
    
    return $this->db->get('alat_laboratorium')->result_array();
}


public function get_patient_examination_history($patient_id)
{
    $this->db->select('pl.*, pt.nama_petugas');
    $this->db->from('pemeriksaan_lab pl');
    $this->db->join('petugas_lab pt', 'pl.petugas_id = pt.petugas_id', 'left');
    $this->db->where('pl.pasien_id', $patient_id);
    $this->db->order_by('pl.tanggal_pemeriksaan', 'DESC');
    
    return $this->db->get()->result_array();
}

public function get_patient_invoice_history($patient_id)
{
    $this->db->select('i.*, pl.nomor_pemeriksaan, pl.jenis_pemeriksaan');
    $this->db->from('invoice i');
    $this->db->join('pemeriksaan_lab pl', 'i.pemeriksaan_id = pl.pemeriksaan_id');
    $this->db->where('pl.pasien_id', $patient_id);
    $this->db->order_by('i.tanggal_invoice', 'DESC');
    $this->db->order_by('i.created_at', 'DESC');
    
    return $this->db->get()->result_array();
}

public function check_patient_has_examinations($patient_id)
{
    $this->db->where('pasien_id', $patient_id);
    return $this->db->count_all_results('pemeriksaan_lab') > 0;
}

public function check_patient_has_invoices($patient_id)
{
    $this->db->select('i.invoice_id');
    $this->db->from('invoice i');
    $this->db->join('pemeriksaan_lab pl', 'i.pemeriksaan_id = pl.pemeriksaan_id');
    $this->db->where('pl.pasien_id', $patient_id);
    
    return $this->db->count_all_results() > 0;
}

public function delete_patient($patient_id)
{
    $this->db->where('pasien_id', $patient_id);
    return $this->db->delete('pasien');
}
public function get_patients_paginated($search = '', $limit = 10, $offset = 0)
{
    if (!empty($search)) {
        $this->db->group_start();
        $this->db->like('nama', $search);
        $this->db->or_like('nik', $search);
        $this->db->or_like('nomor_registrasi', $search);
        $this->db->or_like('telepon', $search);
        $this->db->group_end();
    }
    
    $this->db->order_by('created_at', 'DESC');
    $this->db->limit($limit, $offset);
    
    return $this->db->get('pasien')->result_array();
}

public function count_patients($search = '')
{
    if (!empty($search)) {
        $this->db->group_start();
        $this->db->like('nama', $search);
        $this->db->or_like('nik', $search);
        $this->db->or_like('nomor_registrasi', $search);
        $this->db->or_like('telepon', $search);
        $this->db->group_end();
    }
    
    return $this->db->count_all_results('pasien');
}

public function get_financial_summary()
{
    $this->db->select('
        COUNT(*) as total_invoices,
        SUM(CASE WHEN status_pembayaran = "lunas" THEN total_biaya ELSE 0 END) as total_revenue,
        SUM(CASE WHEN status_pembayaran = "belum_bayar" THEN total_biaya ELSE 0 END) as pending_revenue,
        COUNT(CASE WHEN status_pembayaran = "lunas" THEN 1 END) as paid_invoices,
        COUNT(CASE WHEN status_pembayaran = "belum_bayar" THEN 1 END) as unpaid_invoices,
        COUNT(CASE WHEN status_pembayaran = "cicilan" THEN 1 END) as installment_invoices
    ');
    return $this->db->get('invoice')->row_array();
}


public function get_dashboard_financial_summary()
{
    $current_month = date('Y-m');
    
    $this->db->select('
        COUNT(*) as total_invoices,
        SUM(CASE WHEN status_pembayaran = "lunas" THEN total_biaya ELSE 0 END) as total_revenue,
        SUM(CASE WHEN status_pembayaran = "belum_bayar" THEN total_biaya ELSE 0 END) as pending_revenue,
        COUNT(CASE WHEN status_pembayaran = "lunas" THEN 1 END) as paid_invoices,
        COUNT(CASE WHEN status_pembayaran = "belum_bayar" THEN 1 END) as unpaid_invoices,
        AVG(total_biaya) as average_invoice_amount,
        MAX(total_biaya) as highest_invoice_amount
    ');
    $this->db->where("DATE_FORMAT(tanggal_invoice, '%Y-%m') =", $current_month);
    
    return $this->db->get('invoice')->row_array();
}

public function get_revenue_by_payment_type()
{
    $this->db->select('
        jenis_pembayaran,
        SUM(total_biaya) as total_revenue,
        COUNT(*) as invoice_count,
        AVG(total_biaya) as average_amount
    ');
    $this->db->where('status_pembayaran', 'lunas');
    $this->db->group_by('jenis_pembayaran');
    
    return $this->db->get('invoice')->result_array();
}

/**
 * Get today's revenue
 */
public function get_today_revenue()
{
    $today = date('Y-m-d');
    
    $this->db->select('SUM(total_biaya) as revenue');
    $this->db->where('status_pembayaran', 'lunas');
    $this->db->where('tanggal_pembayaran', $today);
    
    $result = $this->db->get('invoice')->row();
    return $result ? $result->revenue : 0;
}

/**
 * Get weekly revenue
 */
public function get_weekly_revenue()
{
    $start_of_week = date('Y-m-d', strtotime('monday this week'));
    $end_of_week = date('Y-m-d', strtotime('sunday this week'));
    
    $this->db->select('SUM(total_biaya) as revenue');
    $this->db->where('status_pembayaran', 'lunas');
    $this->db->where('tanggal_pembayaran >=', $start_of_week);
    $this->db->where('tanggal_pembayaran <=', $end_of_week);
    
    $result = $this->db->get('invoice')->row();
    return $result ? $result->revenue : 0;
}

/**
 * Get monthly revenue summary
 */
public function get_monthly_revenue_summary()
{
    $current_month = date('Y-m');
    
    $this->db->select('
        SUM(total_biaya) as revenue,
        COUNT(*) as invoice_count,
        AVG(total_biaya) as average_amount
    ');
    $this->db->where('status_pembayaran', 'lunas');
    $this->db->where("DATE_FORMAT(tanggal_pembayaran, '%Y-%m') =", $current_month);
    
    return $this->db->get('invoice')->row_array();
}
/**
 * Get last examination number
 */
public function get_last_examination_number($prefix)
{
    $this->db->where('nomor_pemeriksaan LIKE', $prefix . '%');
    $this->db->order_by('nomor_pemeriksaan', 'DESC');
    $this->db->limit(1);
    $query = $this->db->get('pemeriksaan_lab');
    
    if ($query->num_rows() == 1) {
        return $query->row_array();
    }
    
    return false;
}

/**
 * Get examination by ID
 */
public function get_examination_by_id($exam_id)
{
    $this->db->select('pl.*, p.nama as nama_pasien, p.nik, p.nomor_registrasi, 
                      p.telepon, p.alamat_domisili, p.dokter_perujuk, p.asal_rujukan');
    $this->db->from('pemeriksaan_lab pl');
    $this->db->join('pasien p', 'pl.pasien_id = p.pasien_id');
    $this->db->where('pl.pemeriksaan_id', $exam_id);
    
    $query = $this->db->get();
    if ($query->num_rows() == 1) {
        return $query->row_array();
    }
    
    return false;
}

/**
 * Get pending examinations
 */
public function get_pending_examinations()
{
    $this->db->select('pl.*, p.nama as nama_pasien, p.nik, p.nomor_registrasi');
    $this->db->from('pemeriksaan_lab pl');
    $this->db->join('pasien p', 'pl.pasien_id = p.pasien_id');
    $this->db->where_in('pl.status_pemeriksaan', array('pending', 'progress'));
    $this->db->order_by('pl.created_at', 'DESC');
    
    return $this->db->get()->result_array();
}

/**
 * Update examination status
 */
public function update_examination_status($exam_id, $status)
{
    $this->db->where('pemeriksaan_id', $exam_id);
    return $this->db->update('pemeriksaan_lab', array(
        'status_pemeriksaan' => $status,
        'updated_at' => date('Y-m-d H:i:s')
    ));
}
// Method untuk mendapatkan jadwal
public function get_appointments()
{
    $this->db->select('pl.*, p.nama as nama_pasien, p.telepon, 
                      p.dokter_perujuk, p.asal_rujukan, pt.nama_petugas');
    $this->db->from('pemeriksaan_lab pl');
    $this->db->join('pasien p', 'pl.pasien_id = p.pasien_id');
    $this->db->join('petugas_lab pt', 'pl.petugas_id = pt.petugas_id', 'left');
    $this->db->where('pl.tanggal_pemeriksaan >=', date('Y-m-d'));
    $this->db->order_by('pl.tanggal_pemeriksaan', 'ASC');
    
    return $this->db->get()->result_array();
}

// Method untuk membuat jadwal
public function create_appointment($data)
{
    $this->db->insert('pemeriksaan_lab', $data);
    return $this->db->insert_id();
}

// Method untuk update jadwal
public function update_appointment($pemeriksaan_id, $data)
{
    $this->db->where('pemeriksaan_id', $pemeriksaan_id);
    return $this->db->update('pemeriksaan_lab', $data);
}

// Method untuk menghapus jadwal
public function delete_appointment($pemeriksaan_id)
{
    $this->db->where('pemeriksaan_id', $pemeriksaan_id);
    return $this->db->delete('pemeriksaan_lab');
}
// File: application/models/Patient_model.php
public function getPatientById($id) {
    $this->db->where('id', $id);
    return $this->db->get('pasien')->row_array();
}
// Get patient by ID
public function get_patient_by_id($patient_id)
{
    $this->db->where('pasien_id', $patient_id);
    $query = $this->db->get('pasien');
    
    if ($query->num_rows() == 1) {
        return $query->row_array();
    }
    
    return false;
}

// Update patient data
public function update_patient($patient_id, $data)
{
    $this->db->where('pasien_id', $patient_id);
    return $this->db->update('pasien', $data);
}
// Add these methods at the end of the class, before closing brace

/**
 * Get patient requests with filters
 */
public function get_patient_requests($status = null, $limit = null, $offset = 0)
{
    $this->db->select('pr.*, p.nama as nama_pasien, p.nik, p.telepon');
    $this->db->from('patient_requests pr');
    $this->db->join('pasien p', 'pr.pasien_id = p.pasien_id', 'left');
    
    if ($status) {
        $this->db->where('pr.status', $status);
    }
    
    $this->db->order_by('pr.created_at', 'DESC');
    
    if ($limit) {
        $this->db->limit($limit, $offset);
    }
    
    return $this->db->get()->result_array();
}

/**
 * Count patient requests by status
 */
public function count_patient_requests($status = null)
{
    if ($status) {
        $this->db->where('status', $status);
    }
    return $this->db->count_all_results('patient_requests');
}

/**
 * Get patient requests counts by status
 */
public function get_requests_count_by_status()
{
    $counts = [
        'pending' => 0,
        'approved' => 0,
        'rejected' => 0,
        'completed' => 0
    ];
    
    $this->db->select('status, COUNT(*) as count');
    $this->db->from('patient_requests');
    $this->db->group_by('status');
    $query = $this->db->get();
    
    foreach ($query->result_array() as $row) {
        $counts[$row['status']] = $row['count'];
    }
    
    return $counts;
}

/**
 * Get patient request by ID
 */
public function get_request_by_id($request_id)
{
    $this->db->select('pr.*, p.nama as nama_pasien, p.nik, p.telepon, p.alamat_domisili');
    $this->db->from('patient_requests pr');
    $this->db->join('pasien p', 'pr.pasien_id = p.pasien_id', 'left');
    $this->db->where('pr.permintaan_id', $request_id);
    
    $query = $this->db->get();
    if ($query->num_rows() == 1) {
        return $query->row_array();
    }
    
    return false;
}

/**
 * Update patient request status
 */
public function update_request_status($request_id, $data)
{
    $this->db->where('permintaan_id', $request_id);
    return $this->db->update('patient_requests', $data);
}

}