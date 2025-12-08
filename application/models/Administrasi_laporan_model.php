<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Administrasi_laporan_model extends CI_Model {

    public function __construct() {
        parent::__construct();
        $this->load->database();
    }

    // ==========================================
    // EXAMINATION REPORTS FUNCTIONS
    // ==========================================
    

    public function get_examination_statistics($filters = array()) {
        try {
            $stats = array();
            
            $where_conditions = $this->_build_examination_where_conditions($filters);
            
            $this->db->from('pemeriksaan_lab pl');
            $this->db->join('pasien p', 'pl.pasien_id = p.pasien_id', 'left');
            $this->db->join('petugas_lab pt', 'pl.petugas_id = pt.petugas_id', 'left');
            
            foreach ($where_conditions as $condition) {
                $this->db->where($condition['field'], $condition['value'], $condition['escape']);
            }
            
            $stats['total'] = $this->db->count_all_results();
            
            $status_list = ['pending', 'progress', 'selesai', 'cancelled'];
            foreach ($status_list as $status) {
                $this->db->from('pemeriksaan_lab pl');
                $this->db->join('pasien p', 'pl.pasien_id = p.pasien_id', 'left');
                $this->db->join('petugas_lab pt', 'pl.petugas_id = pt.petugas_id', 'left');
                $this->db->where('pl.status_pemeriksaan', $status);
                
                foreach ($where_conditions as $condition) {
                    $this->db->where($condition['field'], $condition['value'], $condition['escape']);
                }
                
                $stats[$status] = $this->db->count_all_results();
            }
            
            $stats['completion_rate'] = $stats['total'] > 0 ? round(($stats['selesai'] / $stats['total']) * 100, 2) : 0;
            $stats['pending_rate'] = $stats['total'] > 0 ? round(($stats['pending'] / $stats['total']) * 100, 2) : 0;
            
            return $stats;
            
        } catch (Exception $e) {
            log_message('error', 'Error getting examination statistics: ' . $e->getMessage());
            return array(
                'total' => 0,
                'pending' => 0,
                'progress' => 0,
                'selesai' => 0,
                'cancelled' => 0,
                'completion_rate' => 0,
                'pending_rate' => 0
            );
        }
    }

    public function get_examination_chart_data($filters = array()) {
        try {
            $chart_data = array();
            
            $where_conditions = $this->_build_examination_where_conditions($filters);
            
            $this->db->select('DATE(pl.tanggal_pemeriksaan) as date, COUNT(*) as count');
            $this->db->from('pemeriksaan_lab pl');
            $this->db->join('pasien p', 'pl.pasien_id = p.pasien_id', 'left');
            
            if (empty($filters['start_date']) && empty($filters['end_date'])) {
                $this->db->where('pl.tanggal_pemeriksaan >=', date('Y-m-d', strtotime('-7 days')));
            }
            
            foreach ($where_conditions as $condition) {
                $this->db->where($condition['field'], $condition['value'], $condition['escape']);
            }
            
            $this->db->group_by('DATE(pl.tanggal_pemeriksaan)');
            $this->db->order_by('DATE(pl.tanggal_pemeriksaan)', 'ASC');
            
            $trend_result = $this->db->get()->result_array();
            $chart_data['trend'] = $trend_result;
            
            $this->db->select('pl.status_pemeriksaan, COUNT(*) as count');
            $this->db->from('pemeriksaan_lab pl');
            $this->db->join('pasien p', 'pl.pasien_id = p.pasien_id', 'left');
            
            foreach ($where_conditions as $condition) {
                $this->db->where($condition['field'], $condition['value'], $condition['escape']);
            }
            
            $this->db->group_by('pl.status_pemeriksaan');
            $status_result = $this->db->get()->result_array();
            
            $chart_data['status'] = array();
            foreach ($status_result as $row) {
                $chart_data['status'][$row['status_pemeriksaan']] = $row['count'];
            }
            
            return $chart_data;
            
        } catch (Exception $e) {
            log_message('error', 'Error getting examination chart data: ' . $e->getMessage());
            return array();
        }
    }

    public function get_examination_reports($limit = 20, $offset = 0, $filters = array()) {
        try {
            $this->db->select('
                pl.*,
                p.nama as nama_pasien,
                p.nik,
                p.umur,
                p.jenis_kelamin,
                p.dokter_perujuk,
                p.asal_rujukan,
                pt.nama_petugas
            ');
            $this->db->from('pemeriksaan_lab pl');
            $this->db->join('pasien p', 'pl.pasien_id = p.pasien_id', 'left');
            $this->db->join('petugas_lab pt', 'pl.petugas_id = pt.petugas_id', 'left');
            
            $where_conditions = $this->_build_examination_where_conditions($filters);
            foreach ($where_conditions as $condition) {
                $this->db->where($condition['field'], $condition['value'], $condition['escape']);
            }
            
            if (!empty($filters['search'])) {
                $search_term = $filters['search'];
                $this->db->group_start();
                $this->db->like('pl.nomor_pemeriksaan', $search_term);
                $this->db->or_like('p.nama', $search_term);
                $this->db->or_like('p.nik', $search_term);
                $this->db->or_like('pl.jenis_pemeriksaan', $search_term);
                $this->db->or_like('pt.nama_petugas', $search_term);
                $this->db->group_end();
            }
            
            $this->db->order_by('pl.tanggal_pemeriksaan', 'DESC');
            $this->db->order_by('pl.created_at', 'DESC');
            $this->db->limit($limit, $offset);
            
            return $this->db->get()->result_array();
            
        } catch (Exception $e) {
            log_message('error', 'Error getting examination reports: ' . $e->getMessage());
            return array();
        }
    }

    public function count_examination_reports($filters = array()) {
        try {
            $this->db->from('pemeriksaan_lab pl');
            $this->db->join('pasien p', 'pl.pasien_id = p.pasien_id', 'left');
            $this->db->join('petugas_lab pt', 'pl.petugas_id = pt.petugas_id', 'left');
            
            $where_conditions = $this->_build_examination_where_conditions($filters);
            foreach ($where_conditions as $condition) {
                $this->db->where($condition['field'], $condition['value'], $condition['escape']);
            }
            
            if (!empty($filters['search'])) {
                $search_term = $filters['search'];
                $this->db->group_start();
                $this->db->like('pl.nomor_pemeriksaan', $search_term);
                $this->db->or_like('p.nama', $search_term);
                $this->db->or_like('p.nik', $search_term);
                $this->db->or_like('pl.jenis_pemeriksaan', $search_term);
                $this->db->or_like('pt.nama_petugas', $search_term);
                $this->db->group_end();
            }
            
            return $this->db->count_all_results();
            
        } catch (Exception $e) {
            log_message('error', 'Error counting examination reports: ' . $e->getMessage());
            return 0;
        }
    }

    public function get_examination_detail($examination_id) {
        try {
            $this->db->select('
                pl.*,
                p.nama as nama_pasien,
                p.nik,
                p.jenis_kelamin,
                p.tempat_lahir,
                p.tanggal_lahir,
                p.umur,
                p.alamat_domisili,
                p.telepon,
                p.riwayat_pasien,
                p.dokter_perujuk,
                p.asal_rujukan,
                p.nomor_rujukan,
                p.tanggal_rujukan,
                p.diagnosis_awal,
                p.rekomendasi_pemeriksaan,
                pt.nama_petugas,
                pt.jenis_keahlian,
                l.nama as nama_lab
            ');
            $this->db->from('pemeriksaan_lab pl');
            $this->db->join('pasien p', 'pl.pasien_id = p.pasien_id', 'left');
            $this->db->join('petugas_lab pt', 'pl.petugas_id = pt.petugas_id', 'left');
            $this->db->join('lab l', 'pl.lab_id = l.lab_id', 'left');
            $this->db->where('pl.pemeriksaan_id', $examination_id);
            
            return $this->db->get()->row_array();
            
        } catch (Exception $e) {
            log_message('error', 'Error getting examination detail: ' . $e->getMessage());
            return null;
        }
    }

   public function get_examination_results($examination_id, $examination_type) {
    try {
        $results = null;
        
        // Convert to lowercase dan trim untuk matching
        $type_lower = strtolower(trim($examination_type));
        
        switch($type_lower) {
            case 'kimia darah':
                $this->db->where('pemeriksaan_id', $examination_id);
                $query = $this->db->get('kimia_darah');
                if ($query->num_rows() > 0) {
                    $results = $query->row_array();
                }
                break;
                
            case 'hematologi':
                $this->db->where('pemeriksaan_id', $examination_id);
                $query = $this->db->get('hematologi');
                if ($query->num_rows() > 0) {
                    $results = $query->row_array();
                }
                break;
                
            case 'urinologi':
            case 'urine':
                $this->db->where('pemeriksaan_id', $examination_id);
                $query = $this->db->get('urinologi');
                if ($query->num_rows() > 0) {
                    $results = $query->row_array();
                }
                break;
                
            case 'serologi':
            case 'serologi imunologi':
            case 'imunologi':
                $this->db->where('pemeriksaan_id', $examination_id);
                $query = $this->db->get('serologi_imunologi');
                if ($query->num_rows() > 0) {
                    $results = $query->row_array();
                }
                break;
                
            case 'tbc':
            case 'tb':
            case 'tuberculosis':
                $this->db->where('pemeriksaan_id', $examination_id);
                $query = $this->db->get('tbc');
                if ($query->num_rows() > 0) {
                    $results = $query->row_array();
                }
                break;
                
            case 'ims':
                $this->db->where('pemeriksaan_id', $examination_id);
                $query = $this->db->get('ims');
                if ($query->num_rows() > 0) {
                    $results = $query->row_array();
                }
                break;
                
            case 'mls':
            case 'malaria':
                $this->db->where('pemeriksaan_id', $examination_id);
                $query = $this->db->get('mls');
                if ($query->num_rows() > 0) {
                    $results = $query->row_array();
                }
                break;
                
            default:
                // Log untuk debugging
                log_message('debug', 'Unhandled examination type: ' . $examination_type);
                break;
        }
        
        return $results;
        
    } catch (Exception $e) {
        log_message('error', 'Error getting examination results: ' . $e->getMessage());
        return null;
    }
}

    public function get_examination_timeline($examination_id) {
        try {
            $this->db->select('
                tp.*,
                pt.nama_petugas
            ');
            $this->db->from('timeline_progres tp');
            $this->db->join('petugas_lab pt', 'tp.petugas_id = pt.petugas_id', 'left');
            $this->db->where('tp.pemeriksaan_id', $examination_id);
            $this->db->order_by('tp.tanggal_update', 'DESC');
            
            return $this->db->get()->result_array();
            
        } catch (Exception $e) {
            log_message('error', 'Error getting examination timeline: ' . $e->getMessage());
            return array();
        }
    }

    // ==========================================
    // FINANCIAL REPORTS FUNCTIONS
    // ==========================================

    public function get_financial_statistics($filters = array()) {
        try {
            $stats = array();
            
            $where_conditions = $this->_build_financial_where_conditions($filters);
            
            // Total revenue
            $this->db->select('SUM(total_biaya) as total_revenue');
            $this->db->from('invoice i');
            $this->db->join('pemeriksaan_lab pl', 'i.pemeriksaan_id = pl.pemeriksaan_id', 'left');
            $this->db->join('pasien p', 'pl.pasien_id = p.pasien_id', 'left');
            
            foreach ($where_conditions as $condition) {
                $this->db->where($condition['field'], $condition['value'], $condition['escape']);
            }
            
            $result = $this->db->get()->row_array();
            $stats['total_revenue'] = (float)($result['total_revenue'] ?? 0);
            
            // Revenue by payment status
            $payment_statuses = ['lunas', 'belum_bayar', 'cicilan'];
            foreach ($payment_statuses as $status) {
                $this->db->select('SUM(total_biaya) as revenue');
                $this->db->from('invoice i');
                $this->db->join('pemeriksaan_lab pl', 'i.pemeriksaan_id = pl.pemeriksaan_id', 'left');
                $this->db->join('pasien p', 'pl.pasien_id = p.pasien_id', 'left');
                $this->db->where('i.status_pembayaran', $status);
                
                foreach ($where_conditions as $condition) {
                    $this->db->where($condition['field'], $condition['value'], $condition['escape']);
                }
                
                $result = $this->db->get()->row_array();
                $stats[$status . '_revenue'] = (float)($result['revenue'] ?? 0);
            }
            
            // Total invoices count
            $this->db->from('invoice i');
            $this->db->join('pemeriksaan_lab pl', 'i.pemeriksaan_id = pl.pemeriksaan_id', 'left');
            $this->db->join('pasien p', 'pl.pasien_id = p.pasien_id', 'left');
            
            foreach ($where_conditions as $condition) {
                $this->db->where($condition['field'], $condition['value'], $condition['escape']);
            }
            
            $stats['total_invoices'] = $this->db->count_all_results();
            
            // Payment rate calculation
            if ($stats['total_revenue'] > 0) {
                $stats['payment_rate'] = round(($stats['lunas_revenue'] / $stats['total_revenue']) * 100, 2);
            } else {
                $stats['payment_rate'] = 0;
            }
            
            // Additional stats
            $stats['paid_revenue'] = $stats['lunas_revenue'];
            $stats['unpaid_revenue'] = $stats['belum_bayar_revenue'];
            $stats['installment_revenue'] = $stats['cicilan_revenue'];
            
            return $stats;
            
        } catch (Exception $e) {
            log_message('error', 'Error getting financial statistics: ' . $e->getMessage());
            return array(
                'total_revenue' => 0,
                'paid_revenue' => 0,
                'unpaid_revenue' => 0,
                'installment_revenue' => 0,
                'total_invoices' => 0,
                'payment_rate' => 0
            );
        }
    }

    public function get_financial_chart_data($filters = array()) {
        try {
            $chart_data = array();
            
            $where_conditions = $this->_build_financial_where_conditions($filters);
            
            // Revenue trend data (daily)
            $this->db->select('DATE(i.tanggal_invoice) as date, SUM(i.total_biaya) as total');
            $this->db->from('invoice i');
            $this->db->join('pemeriksaan_lab pl', 'i.pemeriksaan_id = pl.pemeriksaan_id', 'left');
            $this->db->join('pasien p', 'pl.pasien_id = p.pasien_id', 'left');
            
            // If no date filter specified, default to last 7 days
            if (empty($filters['start_date']) && empty($filters['end_date'])) {
                $this->db->where('i.tanggal_invoice >=', date('Y-m-d', strtotime('-7 days')));
            }
            
            foreach ($where_conditions as $condition) {
                $this->db->where($condition['field'], $condition['value'], $condition['escape']);
            }
            
            $this->db->group_by('DATE(i.tanggal_invoice)');
            $this->db->order_by('DATE(i.tanggal_invoice)', 'ASC');
            
            $revenue_trend = $this->db->get()->result_array();
            $chart_data['revenue'] = $revenue_trend;
            
            // Payment status distribution
            $this->db->select('i.status_pembayaran, COUNT(*) as count');
            $this->db->from('invoice i');
            $this->db->join('pemeriksaan_lab pl', 'i.pemeriksaan_id = pl.pemeriksaan_id', 'left');
            $this->db->join('pasien p', 'pl.pasien_id = p.pasien_id', 'left');
            
            foreach ($where_conditions as $condition) {
                $this->db->where($condition['field'], $condition['value'], $condition['escape']);
            }
            
            $this->db->group_by('i.status_pembayaran');
            $payment_status_result = $this->db->get()->result_array();
            
            $chart_data['payment_status'] = array();
            foreach ($payment_status_result as $row) {
                $chart_data['payment_status'][$row['status_pembayaran']] = $row['count'];
            }
            
            return $chart_data;
            
        } catch (Exception $e) {
            log_message('error', 'Error getting financial chart data: ' . $e->getMessage());
            return array();
        }
    }

    public function get_financial_reports($limit = 20, $offset = 0, $filters = array()) {
        try {
            $this->db->select('
                i.*,
                p.nama as nama_pasien,
                p.nik,
                pl.nomor_pemeriksaan,
                pl.jenis_pemeriksaan
            ');
            $this->db->from('invoice i');
            $this->db->join('pemeriksaan_lab pl', 'i.pemeriksaan_id = pl.pemeriksaan_id', 'left');
            $this->db->join('pasien p', 'pl.pasien_id = p.pasien_id', 'left');
            
            $where_conditions = $this->_build_financial_where_conditions($filters);
            foreach ($where_conditions as $condition) {
                $this->db->where($condition['field'], $condition['value'], $condition['escape']);
            }
            
            if (!empty($filters['search'])) {
                $search_term = $filters['search'];
                $this->db->group_start();
                $this->db->like('i.nomor_invoice', $search_term);
                $this->db->or_like('p.nama', $search_term);
                $this->db->or_like('p.nik', $search_term);
                $this->db->or_like('pl.nomor_pemeriksaan', $search_term);
                $this->db->or_like('pl.jenis_pemeriksaan', $search_term);
                $this->db->group_end();
            }
            
            $this->db->order_by('i.tanggal_invoice', 'DESC');
            $this->db->order_by('i.created_at', 'DESC');
            $this->db->limit($limit, $offset);
            
            return $this->db->get()->result_array();
            
        } catch (Exception $e) {
            log_message('error', 'Error getting financial reports: ' . $e->getMessage());
            return array();
        }
    }

    public function count_financial_reports($filters = array()) {
        try {
            $this->db->from('invoice i');
            $this->db->join('pemeriksaan_lab pl', 'i.pemeriksaan_id = pl.pemeriksaan_id', 'left');
            $this->db->join('pasien p', 'pl.pasien_id = p.pasien_id', 'left');
            
            $where_conditions = $this->_build_financial_where_conditions($filters);
            foreach ($where_conditions as $condition) {
                $this->db->where($condition['field'], $condition['value'], $condition['escape']);
            }
            
            if (!empty($filters['search'])) {
                $search_term = $filters['search'];
                $this->db->group_start();
                $this->db->like('i.nomor_invoice', $search_term);
                $this->db->or_like('p.nama', $search_term);
                $this->db->or_like('p.nik', $search_term);
                $this->db->or_like('pl.nomor_pemeriksaan', $search_term);
                $this->db->or_like('pl.jenis_pemeriksaan', $search_term);
                $this->db->group_end();
            }
            
            return $this->db->count_all_results();
            
        } catch (Exception $e) {
            log_message('error', 'Error counting financial reports: ' . $e->getMessage());
            return 0;
        }
    }

    public function get_invoice_detail($invoice_id) {
        try {
            $this->db->select('
                i.*,
                p.nama as nama_pasien,
                p.nik,
                p.jenis_kelamin,
                p.umur,
                p.alamat_domisili,
                p.telepon,
                pl.nomor_pemeriksaan,
                pl.jenis_pemeriksaan,
                pl.tanggal_pemeriksaan,
                pl.status_pemeriksaan
            ');
            $this->db->from('invoice i');
            $this->db->join('pemeriksaan_lab pl', 'i.pemeriksaan_id = pl.pemeriksaan_id', 'left');
            $this->db->join('pasien p', 'pl.pasien_id = p.pasien_id', 'left');
            $this->db->where('i.invoice_id', $invoice_id);
            
            return $this->db->get()->row_array();
            
        } catch (Exception $e) {
            log_message('error', 'Error getting invoice detail: ' . $e->getMessage());
            return null;
        }
    }

    public function get_examination_by_invoice($invoice_id) {
        try {
            $this->db->select('pl.*');
            $this->db->from('invoice i');
            $this->db->join('pemeriksaan_lab pl', 'i.pemeriksaan_id = pl.pemeriksaan_id', 'left');
            $this->db->where('i.invoice_id', $invoice_id);
            
            return $this->db->get()->row_array();
            
        } catch (Exception $e) {
            log_message('error', 'Error getting examination by invoice: ' . $e->getMessage());
            return null;
        }
    }

    public function update_invoice_payment($invoice_id, $update_data) {
        try {
            $this->db->where('invoice_id', $invoice_id);
            return $this->db->update('invoice', $update_data);
            
        } catch (Exception $e) {
            log_message('error', 'Error updating invoice payment: ' . $e->getMessage());
            return false;
        }
    }

    public function get_monthly_revenue_summary($year = null) {
        try {
            if (!$year) {
                $year = date('Y');
            }
            
            $this->db->select('
                MONTH(tanggal_invoice) as month,
                SUM(CASE WHEN status_pembayaran = "lunas" THEN total_biaya ELSE 0 END) as paid_revenue,
                SUM(CASE WHEN status_pembayaran = "belum_bayar" THEN total_biaya ELSE 0 END) as unpaid_revenue,
                SUM(CASE WHEN status_pembayaran = "cicilan" THEN total_biaya ELSE 0 END) as installment_revenue,
                SUM(total_biaya) as total_revenue,
                COUNT(*) as total_invoices
            ');
            $this->db->from('invoice');
            $this->db->where('YEAR(tanggal_invoice)', $year);
            $this->db->group_by('MONTH(tanggal_invoice)');
            $this->db->order_by('MONTH(tanggal_invoice)', 'ASC');
            
            return $this->db->get()->result_array();
            
        } catch (Exception $e) {
            log_message('error', 'Error getting monthly revenue summary: ' . $e->getMessage());
            return array();
        }
    }

    public function get_top_paying_patients($limit = 10, $filters = array()) {
        try {
            $this->db->select('
                p.nama as nama_pasien,
                p.nik,
                SUM(i.total_biaya) as total_spent,
                COUNT(i.invoice_id) as total_invoices,
                AVG(i.total_biaya) as avg_invoice_amount
            ');
            $this->db->from('invoice i');
            $this->db->join('pemeriksaan_lab pl', 'i.pemeriksaan_id = pl.pemeriksaan_id');
            $this->db->join('pasien p', 'pl.pasien_id = p.pasien_id');
            
            $where_conditions = $this->_build_financial_where_conditions($filters);
            foreach ($where_conditions as $condition) {
                $this->db->where($condition['field'], $condition['value'], $condition['escape']);
            }
            
            $this->db->group_by('p.pasien_id');
            $this->db->order_by('total_spent', 'DESC');
            $this->db->limit($limit);
            
            return $this->db->get()->result_array();
            
        } catch (Exception $e) {
            log_message('error', 'Error getting top paying patients: ' . $e->getMessage());
            return array();
        }
    }

    public function get_overdue_payments($days_overdue = 30) {
        try {
            $overdue_date = date('Y-m-d', strtotime("-{$days_overdue} days"));
            
            $this->db->select('
                i.*,
                p.nama as nama_pasien,
                p.nik,
                p.telepon,
                pl.nomor_pemeriksaan,
                pl.jenis_pemeriksaan,
                DATEDIFF(CURDATE(), i.tanggal_invoice) as days_overdue
            ');
            $this->db->from('invoice i');
            $this->db->join('pemeriksaan_lab pl', 'i.pemeriksaan_id = pl.pemeriksaan_id');
            $this->db->join('pasien p', 'pl.pasien_id = p.pasien_id');
            $this->db->where('i.status_pembayaran', 'belum_bayar');
            $this->db->where('i.tanggal_invoice <=', $overdue_date);
            $this->db->order_by('i.tanggal_invoice', 'ASC');
            
            return $this->db->get()->result_array();
            
        } catch (Exception $e) {
            log_message('error', 'Error getting overdue payments: ' . $e->getMessage());
            return array();
        }
    }

    public function get_payment_method_statistics($filters = array()) {
        try {
            $this->db->select('
                metode_pembayaran,
                COUNT(*) as count,
                SUM(total_biaya) as total_amount
            ');
            $this->db->from('invoice i');
            $this->db->join('pemeriksaan_lab pl', 'i.pemeriksaan_id = pl.pemeriksaan_id', 'left');
            $this->db->join('pasien p', 'pl.pasien_id = p.pasien_id', 'left');
            $this->db->where('i.status_pembayaran', 'lunas');
            $this->db->where('i.metode_pembayaran IS NOT NULL');
            
            $where_conditions = $this->_build_financial_where_conditions($filters);
            foreach ($where_conditions as $condition) {
                $this->db->where($condition['field'], $condition['value'], $condition['escape']);
            }
            
            $this->db->group_by('metode_pembayaran');
            $this->db->order_by('total_amount', 'DESC');
            
            return $this->db->get()->result_array();
            
        } catch (Exception $e) {
            log_message('error', 'Error getting payment method statistics: ' . $e->getMessage());
            return array();
        }
    }

    // ==========================================
    // ACTIVITY LOGGING
    // ==========================================

    public function log_activity($user_id, $activity, $table_affected = null, $record_id = null) {
        $activity_data = array(
            'user_id' => $user_id,
            'activity' => $activity,
            'table_affected' => $table_affected,
            'record_id' => $record_id,
            'ip_address' => $this->input->ip_address(),
            'created_at' => date('Y-m-d H:i:s')
        );
        
        return $this->db->insert('activity_log', $activity_data);
    }

    // ==========================================
    // HELPER METHODS
    // ==========================================

    private function _build_examination_where_conditions($filters = array()) {
        $conditions = array();
        
        if (!empty($filters['start_date'])) {
            $conditions[] = array(
                'field' => 'DATE(pl.tanggal_pemeriksaan) >=',
                'value' => $filters['start_date'],
                'escape' => TRUE
            );
        }
        
        if (!empty($filters['end_date'])) {
            $conditions[] = array(
                'field' => 'DATE(pl.tanggal_pemeriksaan) <=',
                'value' => $filters['end_date'],
                'escape' => TRUE
            );
        }
        
        if (!empty($filters['status'])) {
            $conditions[] = array(
                'field' => 'pl.status_pemeriksaan',
                'value' => $filters['status'],
                'escape' => TRUE
            );
        }
        
        if (!empty($filters['jenis_pemeriksaan'])) {
            $conditions[] = array(
                'field' => 'pl.jenis_pemeriksaan',
                'value' => $filters['jenis_pemeriksaan'],
                'escape' => TRUE
            );
        }
        
        if (!empty($filters['petugas_id'])) {
            $conditions[] = array(
                'field' => 'pl.petugas_id',
                'value' => $filters['petugas_id'],
                'escape' => TRUE
            );
        }
        
        if (!empty($filters['lab_id'])) {
            $conditions[] = array(
                'field' => 'pl.lab_id',
                'value' => $filters['lab_id'],
                'escape' => TRUE
            );
        }
        
        return $conditions;
    }

    private function _build_financial_where_conditions($filters = array()) {
        $conditions = array();
        
        if (!empty($filters['start_date'])) {
            $conditions[] = array(
                'field' => 'DATE(i.tanggal_invoice) >=',
                'value' => $filters['start_date'],
                'escape' => TRUE
            );
        }
        
        if (!empty($filters['end_date'])) {
            $conditions[] = array(
                'field' => 'DATE(i.tanggal_invoice) <=',
                'value' => $filters['end_date'],
                'escape' => TRUE
            );
        }
        
        if (!empty($filters['status'])) {
            $conditions[] = array(
                'field' => 'i.status_pembayaran',
                'value' => $filters['status'],
                'escape' => TRUE
            );
        }
        
        if (!empty($filters['jenis_pembayaran'])) {
            $conditions[] = array(
                'field' => 'i.jenis_pembayaran',
                'value' => $filters['jenis_pembayaran'],
                'escape' => TRUE
            );
        }
        
        if (!empty($filters['metode_pembayaran'])) {
            $conditions[] = array(
                'field' => 'i.metode_pembayaran',
                'value' => $filters['metode_pembayaran'],
                'escape' => TRUE
            );
        }
        
        return $conditions;
    }
}

?>