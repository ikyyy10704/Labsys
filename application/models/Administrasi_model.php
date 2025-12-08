<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Administrasi_model extends CI_Model {

    public function __construct() {
        parent::__construct();
        $this->load->database();
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
public function create_patient($data) {
    try {
        $this->db->insert('pasien', $data);
        return $this->db->insert_id();
    } catch (Exception $e) {
        log_message('error', 'Error creating patient: ' . $e->getMessage());
        log_message('error', 'Patient data: ' . json_encode($data));
        return false;
    }
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
    
    return (int)$this->db->count_all_results('pasien');
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
public function generate_invoice_auto($pemeriksaan_id) {
    try {
        $this->db->trans_start();
        
        // Load Invoice_model jika belum
        if (!isset($this->invoice_model)) {
            $this->load->model('Invoice_model', 'invoice_model');
        }
        
        // Cek apakah pemeriksaan sudah punya hasil
        $has_results = $this->invoice_model->has_examination_results($pemeriksaan_id);
        
        if (!$has_results) {
            return array(
                'success' => false,
                'message' => 'Pemeriksaan belum memiliki hasil. Silakan input hasil terlebih dahulu.'
            );
        }
        
        // Buat/update invoice dengan perhitungan otomatis
        $invoice_id = $this->invoice_model->create_or_update_invoice($pemeriksaan_id);
        
        $this->db->trans_complete();
        
        if ($this->db->trans_status() === FALSE || !$invoice_id) {
            return array(
                'success' => false,
                'message' => 'Gagal membuat invoice'
            );
        }
        
        // Ambil detail invoice
        $invoice = $this->invoice_model->get_invoice_with_details($invoice_id);
        
        return array(
            'success' => true,
            'message' => 'Invoice berhasil dibuat dengan total biaya Rp ' . number_format($invoice['total_biaya'], 0, ',', '.'),
            'invoice_id' => $invoice_id,
            'invoice' => $invoice
        );
        
    } catch (Exception $e) {
        $this->db->trans_rollback();
        log_message('error', 'Error generating auto invoice: ' . $e->getMessage());
        return array(
            'success' => false,
            'message' => 'Terjadi kesalahan sistem'
        );
    }
}

/**
 * Get invoice detail dengan breakdown biaya
 */
public function get_invoice_detail_with_breakdown($invoice_id) {
    try {
        if (!isset($this->invoice_model)) {
            $this->load->model('Invoice_model', 'invoice_model');
        }
        
        return $this->invoice_model->get_invoice_with_details($invoice_id);
        
    } catch (Exception $e) {
        log_message('error', 'Error getting invoice detail: ' . $e->getMessage());
        return null;
    }
}

/**
 * Recalculate invoice (jika ada perubahan hasil pemeriksaan)
 */
public function recalculate_invoice($invoice_id) {
    try {
        // Ambil pemeriksaan_id dari invoice
        $this->db->select('pemeriksaan_id');
        $this->db->where('invoice_id', $invoice_id);
        $invoice = $this->db->get('invoice')->row_array();
        
        if (!$invoice) {
            return array(
                'success' => false,
                'message' => 'Invoice tidak ditemukan'
            );
        }
        
        if (!isset($this->invoice_model)) {
            $this->load->model('Invoice_model', 'invoice_model');
        }
        
        // Hitung ulang dan update
        $result = $this->invoice_model->create_or_update_invoice($invoice['pemeriksaan_id']);
        
        if ($result) {
            return array(
                'success' => true,
                'message' => 'Invoice berhasil dihitung ulang'
            );
        } else {
            return array(
                'success' => false,
                'message' => 'Gagal menghitung ulang invoice'
            );
        }
        
    } catch (Exception $e) {
        log_message('error', 'Error recalculating invoice: ' . $e->getMessage());
        return array(
            'success' => false,
            'message' => 'Terjadi kesalahan sistem'
        );
    }
}

/**
 * Get preview biaya sebelum generate invoice
 */
public function preview_examination_cost($pemeriksaan_id) {
    try {
        if (!isset($this->invoice_model)) {
            $this->load->model('Invoice_model', 'invoice_model');
        }
        
        $total_cost = $this->invoice_model->calculate_examination_cost($pemeriksaan_id);
        $breakdown = $this->invoice_model->get_cost_breakdown($pemeriksaan_id);
        
        // Ambil data pemeriksaan
        $this->db->select('
            pl.nomor_pemeriksaan,
            pl.jenis_pemeriksaan,
            pl.tanggal_pemeriksaan,
            p.nama as nama_pasien,
            p.nik
        ');
        $this->db->from('pemeriksaan_lab pl');
        $this->db->join('pasien p', 'pl.pasien_id = p.pasien_id');
        $this->db->where('pl.pemeriksaan_id', $pemeriksaan_id);
        $examination = $this->db->get()->row_array();
        
        return array(
            'success' => true,
            'examination' => $examination,
            'total_cost' => $total_cost,
            'breakdown' => $breakdown
        );
        
    } catch (Exception $e) {
        log_message('error', 'Error previewing cost: ' . $e->getMessage());
        return array(
            'success' => false,
            'message' => 'Gagal menghitung preview biaya'
        );
    }
}

/**
 * Get pemeriksaan yang sudah selesai tapi belum punya invoice
 */
public function get_examinations_without_invoice() {
    try {
        $this->db->select('
            pl.pemeriksaan_id,
            pl.nomor_pemeriksaan,
            pl.jenis_pemeriksaan,
            pl.tanggal_pemeriksaan,
            pl.status_pemeriksaan,
            p.nama as nama_pasien,
            p.nik,
            p.nomor_registrasi
        ');
        $this->db->from('pemeriksaan_lab pl');
        $this->db->join('pasien p', 'pl.pasien_id = p.pasien_id');
        $this->db->where('pl.status_pemeriksaan', 'selesai');
        $this->db->where('pl.pemeriksaan_id NOT IN (SELECT pemeriksaan_id FROM invoice)', null, false);
        $this->db->order_by('pl.tanggal_pemeriksaan', 'DESC');
        
        return $this->db->get()->result_array();
        
    } catch (Exception $e) {
        log_message('error', 'Error getting examinations without invoice: ' . $e->getMessage());
        return array();
    }
}

/**
 * Update method create_invoice yang lama untuk compatibility
 * Sekarang redirect ke generate_invoice_auto
 */
public function create_invoice($data) {
    // Jika masih ada code yang memanggil method lama
    if (isset($data['pemeriksaan_id'])) {
        return $this->generate_invoice_auto($data['pemeriksaan_id']);
    }
    
    return array(
        'success' => false,
        'message' => 'Data pemeriksaan tidak valid'
    );
}

/**
 * Get all invoices with pagination and filters
 */
public function get_invoices_paginated($filters = array(), $limit = 20, $offset = 0) {
    try {
        $this->db->select('
            i.*,
            p.nama as nama_pasien,
            p.nik,
            pl.nomor_pemeriksaan,
            pl.jenis_pemeriksaan
        ');
        $this->db->from('invoice i');
        $this->db->join('pemeriksaan_lab pl', 'i.pemeriksaan_id = pl.pemeriksaan_id');
        $this->db->join('pasien p', 'pl.pasien_id = p.pasien_id');
        
        // Apply filters
        if (!empty($filters['status_pembayaran'])) {
            $this->db->where('i.status_pembayaran', $filters['status_pembayaran']);
        }
        
        if (!empty($filters['jenis_pembayaran'])) {
            $this->db->where('i.jenis_pembayaran', $filters['jenis_pembayaran']);
        }
        
        if (!empty($filters['start_date'])) {
            $this->db->where('i.tanggal_invoice >=', $filters['start_date']);
        }
        
        if (!empty($filters['end_date'])) {
            $this->db->where('i.tanggal_invoice <=', $filters['end_date']);
        }
        
        if (!empty($filters['search'])) {
            $this->db->group_start();
            $this->db->like('i.nomor_invoice', $filters['search']);
            $this->db->or_like('p.nama', $filters['search']);
            $this->db->or_like('p.nik', $filters['search']);
            $this->db->or_like('pl.nomor_pemeriksaan', $filters['search']);
            $this->db->group_end();
        }
        
        $this->db->order_by('i.created_at', 'DESC');
        $this->db->limit($limit, $offset);
        
        return $this->db->get()->result_array();
        
    } catch (Exception $e) {
        log_message('error', 'Error getting invoices: ' . $e->getMessage());
        return array();
    }
}

/**
 * Count invoices with filters
 */
public function count_invoices($filters = array()) {
    try {
        $this->db->select('COUNT(*) as total');
        $this->db->from('invoice i');
        $this->db->join('pemeriksaan_lab pl', 'i.pemeriksaan_id = pl.pemeriksaan_id');
        $this->db->join('pasien p', 'pl.pasien_id = p.pasien_id');
        
        // Apply same filters as get_invoices_paginated
        if (!empty($filters['status_pembayaran'])) {
            $this->db->where('i.status_pembayaran', $filters['status_pembayaran']);
        }
        
        if (!empty($filters['jenis_pembayaran'])) {
            $this->db->where('i.jenis_pembayaran', $filters['jenis_pembayaran']);
        }
        
        if (!empty($filters['start_date'])) {
            $this->db->where('i.tanggal_invoice >=', $filters['start_date']);
        }
        
        if (!empty($filters['end_date'])) {
            $this->db->where('i.tanggal_invoice <=', $filters['end_date']);
        }
        
        if (!empty($filters['search'])) {
            $this->db->group_start();
            $this->db->like('i.nomor_invoice', $filters['search']);
            $this->db->or_like('p.nama', $filters['search']);
            $this->db->or_like('p.nik', $filters['search']);
            $this->db->or_like('pl.nomor_pemeriksaan', $filters['search']);
            $this->db->group_end();
        }
        
        $result = $this->db->get()->row_array();
        return (int)$result['total'];
        
    } catch (Exception $e) {
        log_message('error', 'Error counting invoices: ' . $e->getMessage());
        return 0;
    }
}

/**
 * Log activity untuk audit trail
 */
public function log_activity($user_id, $activity, $table_affected = null, $record_id = null) {
    try {
        $data = array(
            'user_id' => $user_id,
            'activity' => $activity,
            'table_affected' => $table_affected,
            'record_id' => $record_id,
            'ip_address' => $this->input->ip_address(),
            'created_at' => date('Y-m-d H:i:s')
        );
        
        $this->db->insert('activity_log', $data);
        return $this->db->insert_id();
        
    } catch (Exception $e) {
        log_message('error', 'Error logging activity: ' . $e->getMessage());
        return false;
    }
}

public function generate_examination_number() {
    $prefix = 'LAB' . date('Y');
    
    $this->db->where('nomor_pemeriksaan LIKE', $prefix . '%');
    $this->db->order_by('nomor_pemeriksaan', 'DESC');
    $this->db->limit(1);
    $last_exam = $this->db->get('pemeriksaan_lab')->row_array();
    if ($last_exam && !empty($last_exam['nomor_pemeriksaan'])) {
        $last_number = intval(substr($last_exam['nomor_pemeriksaan'], -4));
        $new_number = $last_number + 1;
    } else {
        $new_number = 1;
    }
    
    return $prefix . str_pad($new_number, 4, '0', STR_PAD_LEFT);
}


public function get_full_examination_data($pemeriksaan_id) {
    // Get main data
    $this->db->select('pl.*, p.nama, p.nik, p.nomor_registrasi');
    $this->db->from('pemeriksaan_lab pl');
    $this->db->join('pasien p', 'pl.pasien_id = p.pasien_id');
    $this->db->where('pl.pemeriksaan_id', $pemeriksaan_id);
    $main = $this->db->get()->row_array();
    
    if ($main) {
        // Get detail pemeriksaan
        $main['details'] = $this->get_pemeriksaan_details($pemeriksaan_id);
        
        // Get sampel
        $main['sampel'] = $this->get_pemeriksaan_sampel($pemeriksaan_id);
    }
    
    return $main;
}

public function get_pemeriksaan_details($pemeriksaan_id) {
    return $this->db->where('pemeriksaan_id', $pemeriksaan_id)
                    ->order_by('urutan', 'ASC')
                    ->get('pemeriksaan_detail')
                    ->result_array();
}

/**
 * Get sampel pemeriksaan
 */
public function get_pemeriksaan_sampel($pemeriksaan_id) {
    return $this->db->where('pemeriksaan_id', $pemeriksaan_id)
                    ->get('pemeriksaan_sampel')
                    ->result_array();
}

/**
 * Create examination dengan validasi enhanced
 */
public function create_examination($data) {
    try {
        // Pastikan data tidak mengandung pemeriksaan_id (biarkan auto increment)
        if (isset($data['pemeriksaan_id'])) {
            unset($data['pemeriksaan_id']);
        }
        
        // Pastikan field required ada
        $required_fields = ['pasien_id', 'nomor_pemeriksaan', 'tanggal_pemeriksaan', 'status_pasien'];
        foreach ($required_fields as $field) {
            if (!isset($data[$field]) || empty($data[$field])) {
                throw new Exception("Field $field is required");
            }
        }
        
        // Validasi status pasien
        $valid_status = ['puasa', 'belum_puasa', 'minum_obat'];
        if (!in_array($data['status_pasien'], $valid_status)) {
            throw new Exception("Invalid status_pasien value");
        }
        
        // Jika status minum obat, keterangan_obat harus ada
        if ($data['status_pasien'] === 'minum_obat' && empty($data['keterangan_obat'])) {
            throw new Exception("Keterangan obat required when status is minum_obat");
        }
         
        // Set default values jika tidak disediakan
        $default_data = [
            'status_pemeriksaan' => 'pending',
            'created_at' => date('Y-m-d H:i:s'),
            'biaya' => NULL,
            'jenis_pemeriksaan' => '' // Akan diisi oleh trigger
        ];
        
        $data = array_merge($default_data, $data);
        
        $this->db->insert('pemeriksaan_lab', $data);
        
        if ($this->db->affected_rows() > 0) {
            $insert_id = $this->db->insert_id();
            
            if ($insert_id > 0) {
                log_message('info', 'Examination created successfully with ID: ' . $insert_id);
                return $insert_id;
            } else {
                throw new Exception('Invalid insert ID returned: ' . $insert_id);
            }
        } else {
            $error = $this->db->error();
            throw new Exception('Database error: ' . $error['message']);
        }
        
    } catch (Exception $e) {
        log_message('error', 'Error creating examination: ' . $e->getMessage());
        log_message('error', 'Examination data: ' . json_encode($data));
        return false;
    }
}

/**
 * Get examination requests dengan pagination dan filter
 */
public function get_examination_requests($filters = [], $limit = 10, $offset = 0) {
    $this->db->select('pl.*, p.nama as nama_pasien, p.nik, p.nomor_registrasi');
    $this->db->from('pemeriksaan_lab pl');
    $this->db->join('pasien p', 'pl.pasien_id = p.pasien_id');
    
    // Apply filters
    if (!empty($filters['status'])) {
        $this->db->where('pl.status_pemeriksaan', $filters['status']);
    }
    
    if (!empty($filters['status_pasien'])) {
        $this->db->where('pl.status_pasien', $filters['status_pasien']);
    }
    
    if (!empty($filters['start_date'])) {
        $this->db->where('pl.tanggal_pemeriksaan >=', $filters['start_date']);
    }
    
    if (!empty($filters['end_date'])) {
        $this->db->where('pl.tanggal_pemeriksaan <=', $filters['end_date']);
    }
    
    if (!empty($filters['search'])) {
        $this->db->group_start();
        $this->db->like('pl.nomor_pemeriksaan', $filters['search']);
        $this->db->or_like('p.nama', $filters['search']);
        $this->db->or_like('p.nik', $filters['search']);
        $this->db->group_end();
    }
    
    $this->db->order_by('pl.created_at', 'DESC');
    $this->db->limit($limit, $offset);
    
    $results = $this->db->get()->result_array();
    
    // Tambahkan detail untuk setiap pemeriksaan
    foreach ($results as &$result) {
        $result['details'] = $this->get_pemeriksaan_details($result['pemeriksaan_id']);
        $result['sampel'] = $this->get_pemeriksaan_sampel($result['pemeriksaan_id']);
    }
    
    return $results;
}

/**
 * Count examination requests dengan filter
 */
public function count_examination_requests($filters = []) {
    $this->db->select('COUNT(*) as total');
    $this->db->from('pemeriksaan_lab pl');
    $this->db->join('pasien p', 'pl.pasien_id = p.pasien_id');
    
    // Apply same filters
    if (!empty($filters['status'])) {
        $this->db->where('pl.status_pemeriksaan', $filters['status']);
    }
    
    if (!empty($filters['status_pasien'])) {
        $this->db->where('pl.status_pasien', $filters['status_pasien']);
    }
    
    if (!empty($filters['start_date'])) {
        $this->db->where('pl.tanggal_pemeriksaan >=', $filters['start_date']);
    }
    
    if (!empty($filters['end_date'])) {
        $this->db->where('pl.tanggal_pemeriksaan <=', $filters['end_date']);
    }
    
    if (!empty($filters['search'])) {
        $this->db->group_start();
        $this->db->like('pl.nomor_pemeriksaan', $filters['search']);
        $this->db->or_like('p.nama', $filters['search']);
        $this->db->or_like('p.nik', $filters['search']);
        $this->db->group_end();
    }
    
    $result = $this->db->get()->row_array();
    return (int)$result['total'];
}

/**
 * Get examination statistics dengan status pasien
 */
public function get_examination_stats_by_status_pasien($date_range = null) {
    $this->db->select('status_pasien, COUNT(*) as count');
    $this->db->from('pemeriksaan_lab');
    
    if ($date_range) {
        $this->db->where('tanggal_pemeriksaan >=', $date_range['start']);
        $this->db->where('tanggal_pemeriksaan <=', $date_range['end']);
    }
    
    $this->db->group_by('status_pasien');
    
    return $this->db->get()->result_array();
}

/**
 * Get sampel statistics
 */
public function get_sampel_statistics($date_range = null) {
    $this->db->select('ps.jenis_sampel, COUNT(*) as count');
    $this->db->from('pemeriksaan_sampel ps');
    $this->db->join('pemeriksaan_lab pl', 'ps.pemeriksaan_id = pl.pemeriksaan_id');
    
    if ($date_range) {
        $this->db->where('pl.tanggal_pemeriksaan >=', $date_range['start']);
        $this->db->where('pl.tanggal_pemeriksaan <=', $date_range['end']);
    }
    
    $this->db->group_by('ps.jenis_sampel');
    $this->db->order_by('count', 'DESC');
    
    return $this->db->get()->result_array();
}

/**
 * Update pemeriksaan sampel (untuk petugas lab)
 */
public function update_sampel_diambil($sampel_id, $user_id) {
    $data = [
        'tanggal_diambil' => date('Y-m-d H:i:s'),
        'diambil_oleh' => $user_id
    ];
    
    $this->db->where('sampel_id', $sampel_id);
    return $this->db->update('pemeriksaan_sampel', $data);
}

/**
 * Check if all sampel sudah diambil
 */
public function check_all_sampel_diambil($pemeriksaan_id) {
    $this->db->select('COUNT(*) as total, SUM(CASE WHEN tanggal_diambil IS NOT NULL THEN 1 ELSE 0 END) as diambil');
    $this->db->from('pemeriksaan_sampel');
    $this->db->where('pemeriksaan_id', $pemeriksaan_id);
    
    $result = $this->db->get()->row_array();
    
    return $result['total'] > 0 && $result['total'] == $result['diambil'];
}
}