<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Excel_model extends CI_Model {

    public function __construct()
    {
        parent::__construct();
        $this->load->database();
    }

    // ==========================================
    // EXAMINATION DATA FOR EXPORT
    // ==========================================

    public function get_examination_data_for_export($filters = array())
    {
        $this->db->select('
            pl.pemeriksaan_id,
            pl.nomor_pemeriksaan,
            pl.tanggal_pemeriksaan,
            pl.jenis_pemeriksaan,
            pl.status_pemeriksaan,
            pl.biaya,
            pl.keterangan,
            pl.created_at,
            pl.completed_at,
            p.nama as nama_pasien,
            p.nik,
            p.jenis_kelamin,
            p.umur,
            p.alamat_domisili,
            p.telepon,
            p.dokter_perujuk,
            p.asal_rujukan,
            p.nomor_rujukan,
            p.tanggal_rujukan,
            p.diagnosis_awal,
            pt.nama_petugas,
            pt.jenis_keahlian,
            l.nama as nama_lab
        ');
        
        $this->db->from('pemeriksaan_lab pl');
        $this->db->join('pasien p', 'pl.pasien_id = p.pasien_id', 'left');
        $this->db->join('petugas_lab pt', 'pl.petugas_id = pt.petugas_id', 'left');
        $this->db->join('lab l', 'pl.lab_id = l.lab_id', 'left');

        // Apply filters
        if (!empty($filters['start_date'])) {
            $this->db->where('pl.tanggal_pemeriksaan >=', $filters['start_date']);
        }

        if (!empty($filters['end_date'])) {
            $this->db->where('pl.tanggal_pemeriksaan <=', $filters['end_date']);
        }

        if (!empty($filters['status'])) {
            $this->db->where('pl.status_pemeriksaan', $filters['status']);
        }

        if (!empty($filters['jenis_pemeriksaan'])) {
            $this->db->where('pl.jenis_pemeriksaan', $filters['jenis_pemeriksaan']);
        }

        if (!empty($filters['search'])) {
            $this->db->group_start();
            $this->db->like('pl.nomor_pemeriksaan', $filters['search']);
            $this->db->or_like('p.nama', $filters['search']);
            $this->db->or_like('p.nik', $filters['search']);
            $this->db->or_like('pl.jenis_pemeriksaan', $filters['search']);
            $this->db->group_end();
        }

        $this->db->order_by('pl.tanggal_pemeriksaan', 'DESC');
        $this->db->order_by('pl.created_at', 'DESC');

        $query = $this->db->get();
        return $query->result_array();
    }

    public function count_examination_data_for_export($filters = array())
    {
        $this->db->from('pemeriksaan_lab pl');
        $this->db->join('pasien p', 'pl.pasien_id = p.pasien_id', 'left');

        // Apply same filters as above
        if (!empty($filters['start_date'])) {
            $this->db->where('pl.tanggal_pemeriksaan >=', $filters['start_date']);
        }

        if (!empty($filters['end_date'])) {
            $this->db->where('pl.tanggal_pemeriksaan <=', $filters['end_date']);
        }

        if (!empty($filters['status'])) {
            $this->db->where('pl.status_pemeriksaan', $filters['status']);
        }

        if (!empty($filters['jenis_pemeriksaan'])) {
            $this->db->where('pl.jenis_pemeriksaan', $filters['jenis_pemeriksaan']);
        }

        if (!empty($filters['search'])) {
            $this->db->group_start();
            $this->db->like('pl.nomor_pemeriksaan', $filters['search']);
            $this->db->or_like('p.nama', $filters['search']);
            $this->db->or_like('p.nik', $filters['search']);
            $this->db->or_like('pl.jenis_pemeriksaan', $filters['search']);
            $this->db->group_end();
        }

        return $this->db->count_all_results();
    }

    public function get_examination_summary_for_export($filters = array())
    {
        $summary = array();

        // Get total examinations by status
        $this->db->select('status_pemeriksaan, COUNT(*) as jumlah');
        $this->db->from('pemeriksaan_lab pl');
        
        if (!empty($filters['start_date'])) {
            $this->db->where('pl.tanggal_pemeriksaan >=', $filters['start_date']);
        }

        if (!empty($filters['end_date'])) {
            $this->db->where('pl.tanggal_pemeriksaan <=', $filters['end_date']);
        }

        if (!empty($filters['jenis_pemeriksaan'])) {
            $this->db->where('pl.jenis_pemeriksaan', $filters['jenis_pemeriksaan']);
        }

        $this->db->group_by('status_pemeriksaan');
        $status_data = $this->db->get()->result_array();

        $summary['total_examinations'] = 0;
        $summary['completed'] = 0;
        $summary['in_progress'] = 0;
        $summary['pending'] = 0;
        $summary['cancelled'] = 0;

        foreach ($status_data as $status) {
            $summary['total_examinations'] += $status['jumlah'];
            
            switch($status['status_pemeriksaan']) {
                case 'selesai':
                    $summary['completed'] = $status['jumlah'];
                    break;
                case 'progress':
                    $summary['in_progress'] = $status['jumlah'];
                    break;
                case 'pending':
                    $summary['pending'] = $status['jumlah'];
                    break;
                case 'cancelled':
                    $summary['cancelled'] = $status['jumlah'];
                    break;
            }
        }

        // Get data by examination type
        $this->db->select('jenis_pemeriksaan, COUNT(*) as jumlah');
        $this->db->from('pemeriksaan_lab pl');
        
        if (!empty($filters['start_date'])) {
            $this->db->where('pl.tanggal_pemeriksaan >=', $filters['start_date']);
        }

        if (!empty($filters['end_date'])) {
            $this->db->where('pl.tanggal_pemeriksaan <=', $filters['end_date']);
        }

        if (!empty($filters['status'])) {
            $this->db->where('pl.status_pemeriksaan', $filters['status']);
        }

        $this->db->group_by('jenis_pemeriksaan');
        $this->db->order_by('jumlah', 'DESC');
        $type_data = $this->db->get()->result_array();

        $summary['by_type'] = array();
        foreach ($type_data as $type) {
            $summary['by_type'][$type['jenis_pemeriksaan']] = $type['jumlah'];
        }

        // Get monthly trend data
        $this->db->select('
            DATE_FORMAT(tanggal_pemeriksaan, "%Y-%m") as bulan,
            COUNT(*) as jumlah
        ');
        $this->db->from('pemeriksaan_lab pl');
        
        if (!empty($filters['start_date'])) {
            $this->db->where('pl.tanggal_pemeriksaan >=', $filters['start_date']);
        }

        if (!empty($filters['end_date'])) {
            $this->db->where('pl.tanggal_pemeriksaan <=', $filters['end_date']);
        }

        $this->db->group_by('bulan');
        $this->db->order_by('bulan', 'ASC');
        $trend_data = $this->db->get()->result_array();

        $summary['monthly_trend'] = array();
        foreach ($trend_data as $trend) {
            $summary['monthly_trend'][$trend['bulan']] = $trend['jumlah'];
        }

        return $summary;
    }

    public function get_detailed_examination_data($filters = array())
    {
        $examinations = $this->get_examination_data_for_export($filters);
        
        foreach ($examinations as &$exam) {
            // Get examination results based on type
            $exam['hasil_pemeriksaan'] = $this->get_examination_results($exam['pemeriksaan_id'], $exam['jenis_pemeriksaan']);
        }

        return $examinations;
    }

    private function get_examination_results($pemeriksaan_id, $jenis_pemeriksaan)
    {
        $results = array();

        switch(strtolower($jenis_pemeriksaan)) {
            case 'kimia darah':
                $this->db->select('*');
                $this->db->from('kimia_darah');
                $this->db->where('pemeriksaan_id', $pemeriksaan_id);
                $query = $this->db->get();
                if ($query->num_rows() > 0) {
                    $results = $query->row_array();
                }
                break;

            case 'hematologi':
                $this->db->select('*');
                $this->db->from('hematologi');
                $this->db->where('pemeriksaan_id', $pemeriksaan_id);
                $query = $this->db->get();
                if ($query->num_rows() > 0) {
                    $results = $query->row_array();
                }
                break;

            case 'urinologi':
                $this->db->select('*');
                $this->db->from('urinologi');
                $this->db->where('pemeriksaan_id', $pemeriksaan_id);
                $query = $this->db->get();
                if ($query->num_rows() > 0) {
                    $results = $query->row_array();
                }
                break;

            case 'serologi':
                $this->db->select('*');
                $this->db->from('serologi_imunologi');
                $this->db->where('pemeriksaan_id', $pemeriksaan_id);
                $query = $this->db->get();
                if ($query->num_rows() > 0) {
                    $results = $query->row_array();
                }
                break;

            case 'tbc':
                $this->db->select('*');
                $this->db->from('tbc');
                $this->db->where('pemeriksaan_id', $pemeriksaan_id);
                $query = $this->db->get();
                if ($query->num_rows() > 0) {
                    $results = $query->row_array();
                }
                break;

            case 'ims':
                $this->db->select('*');
                $this->db->from('ims');
                $this->db->where('pemeriksaan_id', $pemeriksaan_id);
                $query = $this->db->get();
                if ($query->num_rows() > 0) {
                    $results = $query->row_array();
                }
                break;

            case 'mls':
                $this->db->select('*');
                $this->db->from('mls');
                $this->db->where('pemeriksaan_id', $pemeriksaan_id);
                $query = $this->db->get();
                if ($query->num_rows() > 0) {
                    $results = $query->row_array();
                }
                break;
        }

        return $results;
    }

    // ==========================================
    // FINANCIAL DATA FOR EXPORT
    // ==========================================

    public function get_financial_data_for_export($filters = array())
    {
        $this->db->select('
            i.invoice_id,
            i.nomor_invoice,
            i.tanggal_invoice,
            i.jenis_pembayaran,
            i.total_biaya,
            i.status_pembayaran,
            i.metode_pembayaran,
            i.nomor_kartu_bpjs,
            i.nomor_sep,
            i.tanggal_pembayaran,
            i.keterangan as invoice_keterangan,
            p.nama as nama_pasien,
            p.nik,
            p.telepon,
            p.alamat_domisili,
            pe.nomor_pemeriksaan,
            pe.jenis_pemeriksaan,
            pe.tanggal_pemeriksaan,
            pe.keterangan as pemeriksaan_keterangan,
            l.nama as nama_lab
        ');
        
        $this->db->from('invoice i');
        $this->db->join('pemeriksaan_lab pe', 'i.pemeriksaan_id = pe.pemeriksaan_id', 'left');
        $this->db->join('pasien p', 'pe.pasien_id = p.pasien_id', 'left');
        $this->db->join('lab l', 'pe.lab_id = l.lab_id', 'left');

        // Apply filters
        if (!empty($filters['start_date'])) {
            $this->db->where('i.tanggal_invoice >=', $filters['start_date']);
        }

        if (!empty($filters['end_date'])) {
            $this->db->where('i.tanggal_invoice <=', $filters['end_date']);
        }

        if (!empty($filters['status'])) {
            $this->db->where('i.status_pembayaran', $filters['status']);
        }

        if (!empty($filters['jenis_pembayaran'])) {
            $this->db->where('i.jenis_pembayaran', $filters['jenis_pembayaran']);
        }

        if (!empty($filters['metode_pembayaran'])) {
            $this->db->where('i.metode_pembayaran', $filters['metode_pembayaran']);
        }

        if (!empty($filters['search'])) {
            $this->db->group_start();
            $this->db->like('i.nomor_invoice', $filters['search']);
            $this->db->or_like('p.nama', $filters['search']);
            $this->db->or_like('p.nik', $filters['search']);
            $this->db->or_like('pe.nomor_pemeriksaan', $filters['search']);
            $this->db->or_like('pe.jenis_pemeriksaan', $filters['search']);
            $this->db->group_end();
        }

        $this->db->order_by('i.tanggal_invoice', 'DESC');
        $this->db->order_by('i.created_at', 'DESC');

        $query = $this->db->get();
        return $query->result_array();
    }

    public function count_financial_data_for_export($filters = array())
    {
        $this->db->from('invoice i');
        $this->db->join('pemeriksaan_lab pe', 'i.pemeriksaan_id = pe.pemeriksaan_id', 'left');
        $this->db->join('pasien p', 'pe.pasien_id = p.pasien_id', 'left');

        // Apply same filters as above
        if (!empty($filters['start_date'])) {
            $this->db->where('i.tanggal_invoice >=', $filters['start_date']);
        }

        if (!empty($filters['end_date'])) {
            $this->db->where('i.tanggal_invoice <=', $filters['end_date']);
        }

        if (!empty($filters['status'])) {
            $this->db->where('i.status_pembayaran', $filters['status']);
        }

        if (!empty($filters['jenis_pembayaran'])) {
            $this->db->where('i.jenis_pembayaran', $filters['jenis_pembayaran']);
        }

        if (!empty($filters['metode_pembayaran'])) {
            $this->db->where('i.metode_pembayaran', $filters['metode_pembayaran']);
        }

        if (!empty($filters['search'])) {
            $this->db->group_start();
            $this->db->like('i.nomor_invoice', $filters['search']);
            $this->db->or_like('p.nama', $filters['search']);
            $this->db->or_like('p.nik', $filters['search']);
            $this->db->or_like('pe.nomor_pemeriksaan', $filters['search']);
            $this->db->or_like('pe.jenis_pemeriksaan', $filters['search']);
            $this->db->group_end();
        }

        return $this->db->count_all_results();
    }

    public function get_financial_statistics_for_export($filters = array())
    {
        $stats = array();

        // Base query for statistics
        $this->db->select('
            COUNT(*) as total_invoices,
            SUM(total_biaya) as total_revenue,
            SUM(CASE WHEN status_pembayaran = "lunas" THEN total_biaya ELSE 0 END) as paid_revenue,
            SUM(CASE WHEN status_pembayaran = "belum_bayar" THEN total_biaya ELSE 0 END) as unpaid_revenue,
            SUM(CASE WHEN status_pembayaran = "cicilan" THEN total_biaya ELSE 0 END) as installment_revenue,
            COUNT(CASE WHEN status_pembayaran = "lunas" THEN 1 END) as paid_count,
            COUNT(CASE WHEN status_pembayaran = "belum_bayar" THEN 1 END) as unpaid_count,
            COUNT(CASE WHEN status_pembayaran = "cicilan" THEN 1 END) as installment_count
        ');
        
        $this->db->from('invoice i');
        
        if (!empty($filters['start_date'])) {
            $this->db->where('i.tanggal_invoice >=', $filters['start_date']);
        }

        if (!empty($filters['end_date'])) {
            $this->db->where('i.tanggal_invoice <=', $filters['end_date']);
        }

        if (!empty($filters['jenis_pembayaran'])) {
            $this->db->where('i.jenis_pembayaran', $filters['jenis_pembayaran']);
        }

        $query = $this->db->get();
        $result = $query->row_array();

        $stats['total_invoices'] = $result['total_invoices'] ?: 0;
        $stats['total_revenue'] = $result['total_revenue'] ?: 0;
        $stats['paid_revenue'] = $result['paid_revenue'] ?: 0;
        $stats['unpaid_revenue'] = $result['unpaid_revenue'] ?: 0;
        $stats['installment_revenue'] = $result['installment_revenue'] ?: 0;
        $stats['paid_count'] = $result['paid_count'] ?: 0;
        $stats['unpaid_count'] = $result['unpaid_count'] ?: 0;
        $stats['installment_count'] = $result['installment_count'] ?: 0;

        // Calculate payment rate
        if ($stats['total_invoices'] > 0) {
            $stats['payment_rate'] = round(($stats['paid_count'] / $stats['total_invoices']) * 100, 2);
        } else {
            $stats['payment_rate'] = 0;
        }

        return $stats;
    }

    public function get_payment_method_breakdown($filters = array())
    {
        $this->db->select('
            metode_pembayaran,
            COUNT(*) as jumlah,
            SUM(total_biaya) as total_nilai
        ');
        $this->db->from('invoice i');
        
        if (!empty($filters['start_date'])) {
            $this->db->where('i.tanggal_invoice >=', $filters['start_date']);
        }

        if (!empty($filters['end_date'])) {
            $this->db->where('i.tanggal_invoice <=', $filters['end_date']);
        }

        if (!empty($filters['jenis_pembayaran'])) {
            $this->db->where('i.jenis_pembayaran', $filters['jenis_pembayaran']);
        }

        $this->db->where('metode_pembayaran IS NOT NULL');
        $this->db->group_by('metode_pembayaran');
        $this->db->order_by('total_nilai', 'DESC');

        $query = $this->db->get();
        return $query->result_array();
    }

    public function get_monthly_revenue_summary($filters = array())
    {
        $this->db->select('
            DATE_FORMAT(tanggal_invoice, "%Y-%m") as bulan,
            COUNT(*) as jumlah_invoice,
            SUM(total_biaya) as total_pendapatan,
            SUM(CASE WHEN status_pembayaran = "lunas" THEN total_biaya ELSE 0 END) as pendapatan_lunas,
            SUM(CASE WHEN status_pembayaran = "belum_bayar" THEN total_biaya ELSE 0 END) as piutang
        ');
        $this->db->from('invoice i');
        
        if (!empty($filters['start_date'])) {
            $this->db->where('i.tanggal_invoice >=', $filters['start_date']);
        }

        if (!empty($filters['end_date'])) {
            $this->db->where('i.tanggal_invoice <=', $filters['end_date']);
        }

        if (!empty($filters['jenis_pembayaran'])) {
            $this->db->where('i.jenis_pembayaran', $filters['jenis_pembayaran']);
        }

        $this->db->group_by('bulan');
        $this->db->order_by('bulan', 'ASC');

        $query = $this->db->get();
        return $query->result_array();
    }

    public function get_top_patients_by_revenue($filters = array(), $limit = 10)
    {
        $this->db->select('
            p.nama as nama_pasien,
            p.nik,
            COUNT(i.invoice_id) as jumlah_invoice,
            SUM(i.total_biaya) as total_pendapatan
        ');
        $this->db->from('invoice i');
        $this->db->join('pemeriksaan_lab pe', 'i.pemeriksaan_id = pe.pemeriksaan_id', 'left');
        $this->db->join('pasien p', 'pe.pasien_id = p.pasien_id', 'left');
        
        if (!empty($filters['start_date'])) {
            $this->db->where('i.tanggal_invoice >=', $filters['start_date']);
        }

        if (!empty($filters['end_date'])) {
            $this->db->where('i.tanggal_invoice <=', $filters['end_date']);
        }

        if (!empty($filters['jenis_pembayaran'])) {
            $this->db->where('i.jenis_pembayaran', $filters['jenis_pembayaran']);
        }

        $this->db->group_by('p.pasien_id');
        $this->db->order_by('total_pendapatan', 'DESC');
        $this->db->limit($limit);

        $query = $this->db->get();
        return $query->result_array();
    }

    public function get_examination_type_revenue($filters = array())
    {
        $this->db->select('
            pe.jenis_pemeriksaan,
            COUNT(i.invoice_id) as jumlah_invoice,
            SUM(i.total_biaya) as total_pendapatan,
            AVG(i.total_biaya) as rata_rata_biaya
        ');
        $this->db->from('invoice i');
        $this->db->join('pemeriksaan_lab pe', 'i.pemeriksaan_id = pe.pemeriksaan_id', 'left');
        
        if (!empty($filters['start_date'])) {
            $this->db->where('i.tanggal_invoice >=', $filters['start_date']);
        }

        if (!empty($filters['end_date'])) {
            $this->db->where('i.tanggal_invoice <=', $filters['end_date']);
        }

        if (!empty($filters['jenis_pembayaran'])) {
            $this->db->where('i.jenis_pembayaran', $filters['jenis_pembayaran']);
        }

        $this->db->group_by('pe.jenis_pemeriksaan');
        $this->db->order_by('total_pendapatan', 'DESC');

        $query = $this->db->get();
        return $query->result_array();
    }

    public function get_overdue_payments($filters = array(), $days_overdue = 30)
    {
        $this->db->select('
            i.nomor_invoice,
            i.tanggal_invoice,
            i.total_biaya,
            i.status_pembayaran,
            p.nama as nama_pasien,
            p.telepon,
            pe.nomor_pemeriksaan,
            DATEDIFF(CURDATE(), i.tanggal_invoice) as hari_terlambat
        ');
        $this->db->from('invoice i');
        $this->db->join('pemeriksaan_lab pe', 'i.pemeriksaan_id = pe.pemeriksaan_id', 'left');
        $this->db->join('pasien p', 'pe.pasien_id = p.pasien_id', 'left');
        
        $this->db->where('i.status_pembayaran', 'belum_bayar');
        $this->db->where('DATEDIFF(CURDATE(), i.tanggal_invoice) >=', $days_overdue);
        
        if (!empty($filters['start_date'])) {
            $this->db->where('i.tanggal_invoice >=', $filters['start_date']);
        }

        if (!empty($filters['end_date'])) {
            $this->db->where('i.tanggal_invoice <=', $filters['end_date']);
        }

        if (!empty($filters['jenis_pembayaran'])) {
            $this->db->where('i.jenis_pembayaran', $filters['jenis_pembayaran']);
        }

        $this->db->order_by('hari_terlambat', 'DESC');

        $query = $this->db->get();
        return $query->result_array();
    }

    // ==========================================
    // HELPER METHODS
    // ==========================================

    public function get_examination_types()
    {
        $this->db->select('DISTINCT jenis_pemeriksaan');
        $this->db->from('pemeriksaan_lab');
        $this->db->where('jenis_pemeriksaan IS NOT NULL');
        $this->db->order_by('jenis_pemeriksaan', 'ASC');
        
        $query = $this->db->get();
        $types = array();
        
        foreach ($query->result_array() as $row) {
            $types[] = $row['jenis_pemeriksaan'];
        }

        return $types;
    }

    public function get_payment_types()
    {
        $this->db->select('DISTINCT jenis_pembayaran');
        $this->db->from('invoice');
        $this->db->where('jenis_pembayaran IS NOT NULL');
        $this->db->order_by('jenis_pembayaran', 'ASC');
        
        $query = $this->db->get();
        $types = array();
        
        foreach ($query->result_array() as $row) {
            $types[] = $row['jenis_pembayaran'];
        }

        return $types;
    }

    public function get_payment_methods()
    {
        $this->db->select('DISTINCT metode_pembayaran');
        $this->db->from('invoice');
        $this->db->where('metode_pembayaran IS NOT NULL');
        $this->db->order_by('metode_pembayaran', 'ASC');
        
        $query = $this->db->get();
        $methods = array();
        
        foreach ($query->result_array() as $row) {
            $methods[] = $row['metode_pembayaran'];
        }

        return $methods;
    }

    public function get_examination_statistics($date_range = 30)
    {
        $stats = array();

        // Get counts by status for the specified date range
        $this->db->select('
            status_pemeriksaan,
            COUNT(*) as jumlah,
            SUM(biaya) as total_biaya
        ');
        $this->db->from('pemeriksaan_lab');
        $this->db->where('tanggal_pemeriksaan >=', date('Y-m-d', strtotime("-{$date_range} days")));
        $this->db->group_by('status_pemeriksaan');
        
        $query = $this->db->get();
        $status_data = $query->result_array();

        $stats['total'] = 0;
        $stats['pending'] = 0;
        $stats['progress'] = 0;
        $stats['selesai'] = 0;
        $stats['cancelled'] = 0;
        $stats['total_revenue'] = 0;

        foreach ($status_data as $status) {
            $stats['total'] += $status['jumlah'];
            $stats['total_revenue'] += $status['total_biaya'] ?: 0;
            $stats[$status['status_pemeriksaan']] = $status['jumlah'];
        }

        return $stats;
    }

    public function get_date_range_summary($start_date, $end_date)
    {
        $this->db->select('
            DATE(tanggal_pemeriksaan) as tanggal,
            COUNT(*) as total_pemeriksaan,
            SUM(CASE WHEN status_pemeriksaan = "selesai" THEN 1 ELSE 0 END) as selesai,
            SUM(CASE WHEN status_pemeriksaan = "progress" THEN 1 ELSE 0 END) as progress,
            SUM(CASE WHEN status_pemeriksaan = "pending" THEN 1 ELSE 0 END) as pending,
            SUM(CASE WHEN status_pemeriksaan = "cancelled" THEN 1 ELSE 0 END) as cancelled,
            SUM(biaya) as total_biaya
        ');
        $this->db->from('pemeriksaan_lab');
        $this->db->where('tanggal_pemeriksaan >=', $start_date);
        $this->db->where('tanggal_pemeriksaan <=', $end_date);
        $this->db->group_by('DATE(tanggal_pemeriksaan)');
        $this->db->order_by('tanggal', 'ASC');

        return $this->db->get()->result_array();
    }

    public function get_date_range_summary_invoice($start_date, $end_date)
    {
        $this->db->select('
            DATE(tanggal_invoice) as tanggal,
            COUNT(*) as total_invoice,
            SUM(total_biaya) as total_pendapatan,
            SUM(CASE WHEN status_pembayaran = "lunas" THEN total_biaya ELSE 0 END) as pendapatan_lunas,
            SUM(CASE WHEN status_pembayaran = "belum_bayar" THEN total_biaya ELSE 0 END) as piutang,
            SUM(CASE WHEN status_pembayaran = "cicilan" THEN total_biaya ELSE 0 END) as cicilan
        ');
        $this->db->from('invoice');
        $this->db->where('tanggal_invoice >=', $start_date);
        $this->db->where('tanggal_invoice <=', $end_date);
        $this->db->group_by('DATE(tanggal_invoice)');
        $this->db->order_by('tanggal', 'ASC');

        return $this->db->get()->result_array();
    }
    public function get_patients_for_export($filters = array())
    {
        // Get patients data
        $this->db->select('
            pasien_id, nama, nik, jenis_kelamin, tempat_lahir, tanggal_lahir, umur,
            alamat_domisili, pekerjaan, telepon, kontak_darurat, riwayat_pasien,
            permintaan_pemeriksaan, dokter_perujuk, asal_rujukan, nomor_rujukan,
            tanggal_rujukan, diagnosis_awal, rekomendasi_pemeriksaan, nomor_registrasi,
            created_at
        ');
        $this->db->from('pasien');
        
        // Apply filters
        $this->_apply_patient_filters($filters);
        
        $this->db->order_by('created_at', 'DESC');
        
        $patients = $this->db->get()->result_array();
        
        // Get statistics
        $stats = $this->get_patient_statistics($filters);
        
        return array(
            'patients' => $patients,
            'stats' => $stats
        );
    }

    public function get_patient_statistics($filters = array())
    {
        $stats = array();
        
        // Total patients
        $this->db->from('pasien');
        $this->_apply_patient_filters($filters);
        $stats['total'] = $this->db->count_all_results();
        
        // Patients registered today
        $this->db->from('pasien');
        $this->db->where('DATE(created_at)', date('Y-m-d'));
        $this->_apply_patient_filters($filters);
        $stats['today'] = $this->db->count_all_results();
        
        // Gender distribution
        $this->db->select('jenis_kelamin, COUNT(*) as count');
        $this->db->from('pasien');
        $this->_apply_patient_filters($filters);
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

    // ==========================================
    // EXAMINATION DATA FOR EXPORT
    // ==========================================

    public function get_examinations_for_export($filters = array())
    {
        $this->db->select('
            pe.pemeriksaan_id,
            pe.nomor_pemeriksaan,
            pe.tanggal_pemeriksaan,
            pe.jenis_pemeriksaan,
            pe.status_pemeriksaan,
            pe.biaya,
            pe.keterangan,
            pe.created_at,
            pe.updated_at,
            pe.completed_at,
            pe.started_at,
            p.nama as nama_pasien,
            p.nik,
            p.jenis_kelamin,
            p.umur,
            p.telepon,
            p.nomor_registrasi,
            p.dokter_perujuk,
            p.asal_rujukan,
            pt.nama_petugas,
            l.nama as nama_lab
        ');
        
        $this->db->from('pemeriksaan_lab pe');
        $this->db->join('pasien p', 'pe.pasien_id = p.pasien_id', 'left');
        $this->db->join('petugas_lab pt', 'pe.petugas_id = pt.petugas_id', 'left');
        $this->db->join('lab l', 'pe.lab_id = l.lab_id', 'left');
        
        // Apply filters
        $this->_apply_examination_filters($filters);
        
        $this->db->order_by('pe.tanggal_pemeriksaan', 'DESC');
        
        $examinations = $this->db->get()->result_array();
        
        // Get statistics
        $stats = $this->get_examination_statistics($filters);
        
        return array(
            'examinations' => $examinations,
            'stats' => $stats
        );
    }


    // ==========================================
    // FINANCIAL DATA FOR EXPORT
    // ==========================================

    public function get_financial_for_export($filters = array())
    {
        $this->db->select('
            i.invoice_id,
            i.nomor_invoice,
            i.tanggal_invoice,
            i.jenis_pembayaran,
            i.total_biaya,
            i.status_pembayaran,
            i.metode_pembayaran,
            i.nomor_kartu_bpjs,
            i.nomor_sep,
            i.tanggal_pembayaran,
            i.keterangan as invoice_keterangan,
            p.nama as nama_pasien,
            p.nik,
            p.telepon,
            pe.nomor_pemeriksaan,
            pe.jenis_pemeriksaan,
            pe.tanggal_pemeriksaan
        ');
        
        $this->db->from('invoice i');
        $this->db->join('pemeriksaan_lab pe', 'i.pemeriksaan_id = pe.pemeriksaan_id', 'left');
        $this->db->join('pasien p', 'pe.pasien_id = p.pasien_id', 'left');
        
        // Apply filters
        $this->_apply_financial_filters($filters);
        
        $this->db->order_by('i.tanggal_invoice', 'DESC');
        
        $financial_data = $this->db->get()->result_array();
        
        // Get statistics
        $stats = $this->get_financial_statistics($filters);
        
        return array(
            'financial_data' => $financial_data,
            'stats' => $stats
        );
    }

    public function get_financial_statistics($filters = array())
    {
        $stats = array();
        
        // Total invoices
        $this->db->from('invoice i');
        $this->db->join('pemeriksaan_lab pe', 'i.pemeriksaan_id = pe.pemeriksaan_id', 'left');
        $this->db->join('pasien p', 'pe.pasien_id = p.pasien_id', 'left');
        $this->_apply_financial_filters($filters);
        $stats['total_invoices'] = $this->db->count_all_results();
        
        // Payment status distribution
        $this->db->select('i.status_pembayaran, COUNT(*) as count, SUM(i.total_biaya) as total_amount');
        $this->db->from('invoice i');
        $this->db->join('pemeriksaan_lab pe', 'i.pemeriksaan_id = pe.pemeriksaan_id', 'left');
        $this->db->join('pasien p', 'pe.pasien_id = p.pasien_id', 'left');
        $this->_apply_financial_filters($filters);
        $this->db->group_by('i.status_pembayaran');
        $payment_query = $this->db->get();
        
        $stats['belum_bayar'] = ['count' => 0, 'amount' => 0];
        $stats['lunas'] = ['count' => 0, 'amount' => 0];
        $stats['cicilan'] = ['count' => 0, 'amount' => 0];
        
        foreach ($payment_query->result_array() as $row) {
            $stats[$row['status_pembayaran']] = [
                'count' => $row['count'],
                'amount' => $row['total_amount']
            ];
        }
        
        // Total revenue
        $this->db->select('SUM(i.total_biaya) as total_revenue');
        $this->db->from('invoice i');
        $this->db->join('pemeriksaan_lab pe', 'i.pemeriksaan_id = pe.pemeriksaan_id', 'left');
        $this->db->join('pasien p', 'pe.pasien_id = p.pasien_id', 'left');
        $this->_apply_financial_filters($filters);
        $revenue_query = $this->db->get();
        $stats['total_revenue'] = $revenue_query->row_array()['total_revenue'] ?: 0;
        
        return $stats;
    }

    // ==========================================
    // PRIVATE FILTER METHODS
    // ==========================================

    private function _apply_patient_filters($filters)
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
        
        if (!empty($filters['search'])) {
            $this->db->group_start();
            $this->db->like('nama', $filters['search']);
            $this->db->or_like('nik', $filters['search']);
            $this->db->or_like('telepon', $filters['search']);
            $this->db->or_like('nomor_registrasi', $filters['search']);
            $this->db->group_end();
        }
    }

    private function _apply_examination_filters($filters)
    {
        if (!empty($filters['start_date'])) {
            $this->db->where('DATE(pe.tanggal_pemeriksaan) >=', $filters['start_date']);
        }
        
        if (!empty($filters['end_date'])) {
            $this->db->where('DATE(pe.tanggal_pemeriksaan) <=', $filters['end_date']);
        }
        
        if (!empty($filters['status'])) {
            $this->db->where('pe.status_pemeriksaan', $filters['status']);
        }
        
        if (!empty($filters['jenis_pemeriksaan'])) {
            $this->db->where('pe.jenis_pemeriksaan', $filters['jenis_pemeriksaan']);
        }
        
        if (!empty($filters['search'])) {
            $this->db->group_start();
            $this->db->like('pe.nomor_pemeriksaan', $filters['search']);
            $this->db->or_like('p.nama', $filters['search']);
            $this->db->or_like('p.nik', $filters['search']);
            $this->db->group_end();
        }
    }

    private function _apply_financial_filters($filters)
    {
        if (!empty($filters['start_date'])) {
            $this->db->where('DATE(i.tanggal_invoice) >=', $filters['start_date']);
        }
        
        if (!empty($filters['end_date'])) {
            $this->db->where('DATE(i.tanggal_invoice) <=', $filters['end_date']);
        }
        
        if (!empty($filters['status_pembayaran'])) {
            $this->db->where('i.status_pembayaran', $filters['status_pembayaran']);
        }
        
        if (!empty($filters['jenis_pembayaran'])) {
            $this->db->where('i.jenis_pembayaran', $filters['jenis_pembayaran']);
        }
        
        if (!empty($filters['search'])) {
            $this->db->group_start();
            $this->db->like('i.nomor_invoice', $filters['search']);
            $this->db->or_like('p.nama', $filters['search']);
            $this->db->or_like('pe.nomor_pemeriksaan', $filters['search']);
            $this->db->group_end();
        }
    }
}