<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Administrasi_model extends CI_Model {

    public function __construct() {
        parent::__construct();
        $this->load->database();
    }

    // ==========================================
    // PATIENT MANAGEMENT
    // ==========================================

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

    public function get_patient_by_id($patient_id) {
        $this->db->where('pasien_id', $patient_id);
        $query = $this->db->get('pasien');
        
        if ($query->num_rows() == 1) {
            return $query->row_array();
        }
        
        return false;
    }

    public function get_patient_by_nik($nik) {
        $this->db->where('nik', $nik);
        $query = $this->db->get('pasien');
        
        if ($query->num_rows() == 1) {
            return $query->row_array();
        }
        
        return false;
    }

    public function update_patient($patient_id, $data) {
        $this->db->where('pasien_id', $patient_id);
        return $this->db->update('pasien', $data);
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

    // ==========================================
    // EXAMINATION MANAGEMENT
    // ==========================================

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

    // ==========================================
    // FINANCIAL MANAGEMENT
    // ==========================================

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

    public function get_financial_summary() {
        $summary = array();
        
        // Today's revenue
        $this->db->select('SUM(total_biaya) as total, COUNT(*) as count');
        $this->db->where('DATE(tanggal_pembayaran)', date('Y-m-d'));
        $this->db->where('status_pembayaran', 'lunas');
        $query = $this->db->get('invoice');
        $result = $query->row_array();
        $summary['today'] = array(
            'revenue' => $result['total'] ? (float)$result['total'] : 0,
            'transactions' => $result['count']
        );
        
        // This month's revenue - FIXED
        $this->db->select('SUM(total_biaya) as total, COUNT(*) as count');
        $this->db->where("DATE_FORMAT(tanggal_pembayaran, '%Y-%m') = ", date('Y-m'), FALSE);
        $this->db->where('status_pembayaran', 'lunas');
        $query = $this->db->get('invoice');
        $result = $query->row_array();
        $summary['this_month'] = array(
            'revenue' => $result['total'] ? (float)$result['total'] : 0,
            'transactions' => $result['count']
        );
        
        // Pending payments
        $this->db->select('SUM(total_biaya) as total, COUNT(*) as count');
        $this->db->where('status_pembayaran', 'belum_bayar');
        $query = $this->db->get('invoice');
        $result = $query->row_array();
        $summary['pending'] = array(
            'amount' => $result['total'] ? (float)$result['total'] : 0,
            'count' => $result['count']
        );
        
        // Payment type breakdown - FIXED
        $this->db->select('jenis_pembayaran, SUM(total_biaya) as total, COUNT(*) as count');
        $this->db->where('status_pembayaran', 'lunas');
        $this->db->where("DATE_FORMAT(tanggal_pembayaran, '%Y-%m') = ", date('Y-m'), FALSE);
        $this->db->group_by('jenis_pembayaran');
        $query = $this->db->get('invoice');
        $summary['by_payment_type'] = $query->result_array();
        
        return $summary;
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

    // ==========================================
    // SCHEDULING
    // ==========================================

    public function get_appointments() {
        $this->db->select('pl.*, p.nama as nama_pasien, p.telepon, 
                          p.dokter_perujuk, p.asal_rujukan, pt.nama_petugas');
        $this->db->from('pemeriksaan_lab pl');
        $this->db->join('pasien p', 'pl.pasien_id = p.pasien_id');
        $this->db->join('petugas_lab pt', 'pl.petugas_id = pt.petugas_id', 'left');
        $this->db->where('pl.tanggal_pemeriksaan >=', date('Y-m-d'));
        $this->db->order_by('pl.tanggal_pemeriksaan', 'ASC');
        
        return $this->db->get()->result_array();
    }

    public function create_appointment($data) {
        $this->db->insert('pemeriksaan_lab', $data);
        return $this->db->insert_id();
    }

    public function update_appointment($pemeriksaan_id, $data) {
        $this->db->where('pemeriksaan_id', $pemeriksaan_id);
        return $this->db->update('pemeriksaan_lab', $data);
    }

    // ==========================================
    // REPORTING & EXPORT
    // ==========================================

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

    // ==========================================
    // STATISTICS & ANALYTICS
    // ==========================================

    public function get_registration_statistics() {
        $stats = array();
        
        // Today's registrations
        $stats['today'] = $this->db->where('DATE(created_at)', date('Y-m-d'))->count_all_results('pasien');
        
        // This month's registrations - FIXED
        $stats['this_month'] = $this->db->where("DATE_FORMAT(created_at, '%Y-%m') = ", date('Y-m'), FALSE)->count_all_results('pasien');
        
        // Registration trend (last 7 days)
        $stats['daily_trend'] = array();
        for ($i = 6; $i >= 0; $i--) {
            $date = date('Y-m-d', strtotime("-{$i} days"));
            $count = $this->db->where('DATE(created_at)', $date)->count_all_results('pasien');
            $stats['daily_trend'][] = array(
                'date' => date('M j', strtotime($date)),
                'count' => $count
            );
        }
        
        return $stats;
    }

    public function get_payment_statistics() {
        $stats = array();
        
        // Payment status distribution
        $this->db->select('status_pembayaran, COUNT(*) as count, SUM(total_biaya) as total');
        $this->db->group_by('status_pembayaran');
        $query = $this->db->get('invoice');
        foreach ($query->result_array() as $row) {
            $stats['by_status'][$row['status_pembayaran']] = array(
                'count' => $row['count'],
                'total' => $row['total'] ? (float)$row['total'] : 0
            );
        }
        
        // Payment type distribution
        $this->db->select('jenis_pembayaran, COUNT(*) as count, SUM(total_biaya) as total');
        $this->db->where('status_pembayaran', 'lunas');
        $this->db->group_by('jenis_pembayaran');
        $query = $this->db->get('invoice');
        foreach ($query->result_array() as $row) {
            $stats['by_type'][$row['jenis_pembayaran']] = array(
                'count' => $row['count'],
                'total' => $row['total'] ? (float)$row['total'] : 0
            );
        }
        
        return $stats;
    }

    // ==========================================
    // HELPER METHODS
    // ==========================================

    public function get_recent_registrations($limit = 5) {
        $this->db->order_by('created_at', 'DESC');
        $this->db->limit($limit);
        
        return $this->db->get('pasien')->result_array();
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

    public function get_patient_examination_history($patient_id) {
        $this->db->select('pl.*, pt.nama_petugas');
        $this->db->from('pemeriksaan_lab pl');
        $this->db->join('petugas_lab pt', 'pl.petugas_id = pt.petugas_id', 'left');
        $this->db->where('pl.pasien_id', $patient_id);
        $this->db->order_by('pl.tanggal_pemeriksaan', 'DESC');
        
        return $this->db->get()->result_array();
    }
}