<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Administrasi_laporan extends CI_Controller {

    public function __construct()
    {
        parent::__construct();
        
       // Hanya izinkan role administrasi dan admin
        $allowed_roles = ['administrasi', 'admin'];
        if (!in_array($this->session->userdata('role'), $allowed_roles)) {
            $this->session->set_flashdata('error', 'Akses ditolak. Anda tidak memiliki izin untuk mengakses halaman ini.');
            redirect('auth/login');
        }
        
        
        $this->load->model(['Administrasi_laporan_model']);
        $this->load->library(['form_validation']);
        $this->load->helper(['form', 'url', 'date']);
    }

    // ==========================================
    // EXAMINATION REPORTS
    // ==========================================

    public function examination_reports()
    {
        $data['fullwidth'] = true;
        $data['title'] = 'Laporan Pemeriksaan';
        
        try {
            $data['stats'] = $this->Administrasi_laporan_model->get_examination_statistics();
            $data['chart_data'] = $this->Administrasi_laporan_model->get_examination_chart_data();
            
            $this->Administrasi_laporan_model->log_activity(
                $this->session->userdata('user_id'),
                'Mengakses laporan pemeriksaan',
                'system',
                null
            );
            
        } catch (Exception $e) {
            log_message('error', 'Error loading examination reports: ' . $e->getMessage());
            $data['stats'] = array(
                'total' => 0,
                'pending' => 0,
                'progress' => 0,
                'selesai' => 0,
                'cancelled' => 0
            );
            $data['chart_data'] = array();
        }
        
        // Load view dengan fullwidth
        $this->load->view('template/header', $data);
        $this->load->view('template/sidebar', $data);
        $this->_load_fullwidth_view('administrasi/examination_reports', $data);
        $this->load->view('template/footer', $data);
    }

    public function ajax_get_examination_reports()
    {
        $this->output->set_content_type('application/json');
        
        try {
            $filters = array(
                'start_date' => $this->input->get('start_date'),
                'end_date' => $this->input->get('end_date'),
                'status' => $this->input->get('status'),
                'jenis_pemeriksaan' => $this->input->get('jenis_pemeriksaan'),
                'search' => $this->input->get('search')
            );
            
            $per_page = $this->input->get('per_page') ? (int)$this->input->get('per_page') : 20;
            $page = $this->input->get('page') ? (int)$this->input->get('page') : 1;
            $offset = ($page - 1) * $per_page;
            
            $examinations = $this->Administrasi_laporan_model->get_examination_reports($per_page, $offset, $filters);
            $total_records = $this->Administrasi_laporan_model->count_examination_reports($filters);
            $stats = $this->Administrasi_laporan_model->get_examination_statistics($filters);
            $chart_data = $this->Administrasi_laporan_model->get_examination_chart_data($filters);
            
            $response = array(
                'success' => true,
                'examinations' => $examinations,
                'total_records' => $total_records,
                'stats' => $stats,
                'chart_data' => $chart_data,
                'pagination' => array(
                    'current_page' => $page,
                    'per_page' => $per_page,
                    'total_pages' => ceil($total_records / $per_page),
                    'total_records' => $total_records
                )
            );
            
        } catch (Exception $e) {
            $response = array(
                'success' => false,
                'message' => 'Gagal mengambil data laporan pemeriksaan'
            );
        }
        
        $this->output->set_output(json_encode($response));
    }

    public function ajax_get_examination_detail($examination_id)
    {
        $this->output->set_content_type('application/json');
        
        try {
            $examination = $this->Administrasi_laporan_model->get_examination_detail($examination_id);
            
            if (!$examination) {
                $this->output->set_output(json_encode(array(
                    'success' => false,
                    'message' => 'Pemeriksaan tidak ditemukan'
                )));
                return;
            }
            
            $results = $this->Administrasi_laporan_model->get_examination_results($examination_id, $examination['jenis_pemeriksaan']);
            $timeline = $this->Administrasi_laporan_model->get_examination_timeline($examination_id);
            
            $response = array(
                'success' => true,
                'examination' => $examination,
                'results' => $results,
                'timeline' => $timeline
            );
            
        } catch (Exception $e) {
            log_message('error', 'Error getting examination detail: ' . $e->getMessage());
            $response = array(
                'success' => false,
                'message' => 'Gagal mengambil detail pemeriksaan'
            );
        }
        
        $this->output->set_output(json_encode($response));
    }

    // ==========================================
    // FINANCIAL REPORTS
    // ==========================================

    public function financial_reports()
    {
        $data['fullwidth'] = true;
        $data['title'] = 'Laporan Keuangan';
        
        try {
            $data['stats'] = $this->Administrasi_laporan_model->get_financial_statistics();
            $data['chart_data'] = $this->Administrasi_laporan_model->get_financial_chart_data();
            
            $this->Administrasi_laporan_model->log_activity(
                $this->session->userdata('user_id'),
                'Mengakses laporan keuangan',
                'system',
                null
            );
            
        } catch (Exception $e) {
            log_message('error', 'Error loading financial reports: ' . $e->getMessage());
            $data['stats'] = array(
                'total_revenue' => 0,
                'paid_revenue' => 0,
                'unpaid_revenue' => 0,
                'installment_revenue' => 0,
                'total_invoices' => 0,
                'payment_rate' => 0
            );
            $data['chart_data'] = array();
        }
        
        // Load view dengan fullwidth
        $this->load->view('template/header', $data);
        $this->load->view('template/sidebar', $data);
        $this->_load_fullwidth_view('administrasi/financial_reports', $data);
        $this->load->view('template/footer', $data);
    }


    public function ajax_get_financial_dashboard_stats()
    {
        $this->output->set_content_type('application/json');
        
        try {
            $stats = array();
            
            // Daily stats
            $today = date('Y-m-d');
            $stats['today'] = $this->Administrasi_laporan_model->get_financial_statistics(array('start_date' => $today, 'end_date' => $today));
            
            // Monthly stats
            $this_month_start = date('Y-m-01');
            $this_month_end = date('Y-m-t');
            $stats['this_month'] = $this->Administrasi_laporan_model->get_financial_statistics(array('start_date' => $this_month_start, 'end_date' => $this_month_end));
            
            // Year stats
            $this_year_start = date('Y-01-01');
            $this_year_end = date('Y-12-31');
            $stats['this_year'] = $this->Administrasi_laporan_model->get_financial_statistics(array('start_date' => $this_year_start, 'end_date' => $this_year_end));
            
            // Top paying patients
            $stats['top_patients'] = $this->Administrasi_laporan_model->get_top_paying_patients(5);
            
            // Overdue payments
            $stats['overdue_payments'] = $this->Administrasi_laporan_model->get_overdue_payments(30);
            
            // Payment method statistics
            $stats['payment_methods'] = $this->Administrasi_laporan_model->get_payment_method_statistics();
            
            $response = array(
                'success' => true,
                'data' => $stats
            );
            
        } catch (Exception $e) {
            log_message('error', 'Error getting financial dashboard stats: ' . $e->getMessage());
            $response = array(
                'success' => false,
                'message' => 'Gagal mengambil statistik keuangan'
            );
        }
        
        $this->output->set_output(json_encode($response));
    }

    public function ajax_get_monthly_revenue()
    {
        $this->output->set_content_type('application/json');
        
        try {
            $year = $this->input->get('year') ? (int)$this->input->get('year') : date('Y');
            
            if ($year < 2000 || $year > 2100) {
                $this->output->set_output(json_encode(array(
                    'success' => false,
                    'message' => 'Tahun tidak valid'
                )));
                return;
            }
            
            $monthly_data = $this->Administrasi_laporan_model->get_monthly_revenue_summary($year);
            
            $response = array(
                'success' => true,
                'data' => $monthly_data,
                'year' => $year
            );
            
        } catch (Exception $e) {
            log_message('error', 'Error getting monthly revenue: ' . $e->getMessage());
            $response = array(
                'success' => false,
                'message' => 'Gagal mengambil data pendapatan bulanan'
            );
        }
        
        $this->output->set_output(json_encode($response));
    }

    // ==========================================
    // EXPORT FUNCTIONS
    // ==========================================

    public function export_examination_reports()
    {
        try {
            $filters = array(
                'start_date' => $this->input->get('start_date'),
                'end_date' => $this->input->get('end_date'),
                'status' => $this->input->get('status'),
                'jenis_pemeriksaan' => $this->input->get('jenis_pemeriksaan'),
                'search' => $this->input->get('search')
            );
            
            $data = $this->Administrasi_laporan_model->get_examination_reports(5000, 0, $filters); // Maksimal 5000 record
            
            if (empty($data)) {
                $this->session->set_flashdata('error', 'Tidak ada data untuk diekspor');
                redirect('administrasi_laporan/examination_reports');
                return;
            }
            
            $filename = 'examination_reports_' . date('Y-m-d_H-i-s') . '.csv';
            header('Content-Type: text/csv; charset=UTF-8');
            header('Content-Disposition: attachment; filename="' . $filename . '"');
            header('Cache-Control: no-cache, must-revalidate');
            header('Pragma: no-cache');
            
            $output = fopen('php://output', 'w');
            
            fprintf($output, chr(0xEF).chr(0xBB).chr(0xBF));
            
            fputcsv($output, array(
                'Nomor Pemeriksaan',
                'Nama Pasien',
                'NIK',
                'Jenis Pemeriksaan',
                'Tanggal Pemeriksaan',
                'Status',
                'Petugas Lab',
                'Biaya',
                'Dokter Perujuk',
                'Asal Rujukan'
            ));
            
            foreach ($data as $row) {
                fputcsv($output, array(
                    $row['nomor_pemeriksaan'],
                    $row['nama_pasien'],
                    $row['nik'] ?: '-',
                    $row['jenis_pemeriksaan'],
                    date('d/m/Y', strtotime($row['tanggal_pemeriksaan'])),
                    ucfirst($row['status_pemeriksaan']),
                    $row['nama_petugas'] ?: 'Belum ditugaskan',
                    number_format($row['biaya'], 0, ',', '.'),
                    $row['dokter_perujuk'] ?: '-',
                    $row['asal_rujukan'] ?: '-'
                ));
            }
            
            fclose($output);
            
            $this->Administrasi_laporan_model->log_activity(
                $this->session->userdata('user_id'),
                'Laporan pemeriksaan diekspor ke CSV',
                'pemeriksaan_lab',
                null
            );
            
        } catch (Exception $e) {
            log_message('error', 'Error exporting examination reports: ' . $e->getMessage());
            $this->session->set_flashdata('error', 'Gagal mengekspor data pemeriksaan');
            redirect('administrasi_laporan/examination_reports');
        }
    }

    public function export_financial_reports()
    {
        try {
            $filters = array(
                'start_date' => $this->input->get('start_date'),
                'end_date' => $this->input->get('end_date'),
                'status' => $this->input->get('status'),
                'jenis_pembayaran' => $this->input->get('jenis_pembayaran'),
                'metode_pembayaran' => $this->input->get('metode_pembayaran'),
                'search' => $this->input->get('search')
            );
            
            $data = $this->Administrasi_laporan_model->get_financial_reports(5000, 0, $filters); // Maksimal 5000 record
            
            if (empty($data)) {
                $this->session->set_flashdata('error', 'Tidak ada data untuk diekspor');
                redirect('administrasi_laporan/financial_reports');
                return;
            }
            
            $filename = 'financial_reports_' . date('Y-m-d_H-i-s') . '.csv';
            header('Content-Type: text/csv; charset=UTF-8');
            header('Content-Disposition: attachment; filename="' . $filename . '"');
            header('Cache-Control: no-cache, must-revalidate');
            header('Pragma: no-cache');
            
            $output = fopen('php://output', 'w');
            
            fprintf($output, chr(0xEF).chr(0xBB).chr(0xBF));
            
            fputcsv($output, array(
                'Nomor Invoice',
                'Nama Pasien',
                'NIK',
                'Nomor Pemeriksaan',
                'Jenis Pemeriksaan',
                'Tanggal Invoice',
                'Tanggal Pembayaran',
                'Total Biaya',
                'Status Pembayaran',
                'Jenis Pembayaran',
                'Metode Pembayaran',
                'Keterangan'
            ));
            
            foreach ($data as $row) {
                fputcsv($output, array(
                    $row['nomor_invoice'],
                    $row['nama_pasien'],
                    $row['nik'] ?: '-',
                    $row['nomor_pemeriksaan'],
                    $row['jenis_pemeriksaan'],
                    date('d/m/Y', strtotime($row['tanggal_invoice'])),
                    $row['tanggal_pembayaran'] ? date('d/m/Y', strtotime($row['tanggal_pembayaran'])) : '-',
                    number_format($row['total_biaya'], 0, ',', '.'),
                    ucfirst(str_replace('_', ' ', $row['status_pembayaran'])),
                    ucfirst($row['jenis_pembayaran']),
                    $row['metode_pembayaran'] ?: '-',
                    $row['keterangan'] ?: '-'
                ));
            }
            
            fclose($output);
            
            $this->Administrasi_laporan_model->log_activity(
                $this->session->userdata('user_id'),
                'Laporan keuangan diekspor ke CSV',
                'invoice',
                null
            );
            
        } catch (Exception $e) {
            log_message('error', 'Error exporting financial reports: ' . $e->getMessage());
            $this->session->set_flashdata('error', 'Gagal mengekspor data keuangan');
            redirect('administrasi_laporan/financial_reports');
        }
    }

    // ==========================================
    // DASHBOARD SUMMARY FOR ADMINISTRASI
    // ==========================================

    public function dashboard_summary()
    {
        $data['title'] = 'Ringkasan Laporan';
        
        try {
            // Get basic statistics for today
            $today = date('Y-m-d');
            $this_month_start = date('Y-m-01');
            $this_month_end = date('Y-m-t');
            
            // Examination stats
            $data['exam_stats'] = array(
                'today' => $this->Administrasi_laporan_model->get_examination_statistics(array(
                    'start_date' => $today, 
                    'end_date' => $today
                )),
                'this_month' => $this->Administrasi_laporan_model->get_examination_statistics(array(
                    'start_date' => $this_month_start,
                    'end_date' => $this_month_end
                ))
            );
            
            // Financial stats
            $data['financial_stats'] = array(
                'today' => $this->Administrasi_laporan_model->get_financial_statistics(array(
                    'start_date' => $today,
                    'end_date' => $today
                )),
                'this_month' => $this->Administrasi_laporan_model->get_financial_statistics(array(
                    'start_date' => $this_month_start,
                    'end_date' => $this_month_end
                ))
            );
            
            // Overdue payments
            $data['overdue_payments'] = $this->Administrasi_laporan_model->get_overdue_payments(30);
            
            $this->Administrasi_laporan_model->log_activity(
                $this->session->userdata('user_id'),
                'Mengakses ringkasan laporan',
                'system',
                null
            );
            
        } catch (Exception $e) {
            log_message('error', 'Error loading dashboard summary: ' . $e->getMessage());
            $data['exam_stats'] = array();
            $data['financial_stats'] = array();
            $data['overdue_payments'] = array();
        }
        
        $this->load->view('template/header', $data);
        $this->load->view('template/sidebar', $data);
        $this->load->view('administrasi/dashboard_summary', $data);
        $this->load->view('template/footer');
    }

    // ==========================================
    // HELPER METHODS
    // ==========================================

    private function _load_fullwidth_view($view, $data = array())
    {
        if (!empty($data) && isset($data['fullwidth']) && $data['fullwidth'] === true) {
            $this->load->view($view, $data);
        } else {
            $this->load->view('template/header', $data);
            $this->load->view('template/sidebar', $data);
            $this->load->view($view, $data);
            $this->load->view('template/footer');
        }
    }

    private function _is_fullwidth_mode()
    {
        return $this->session->userdata('fullwidth_mode') === true;
    }

    // ==========================================
    // ADDITIONAL HELPER FUNCTIONS
    // ==========================================

    public function ajax_search_patients()
    {
        $this->output->set_content_type('application/json');
        
        try {
            $search_term = $this->input->get('q');
            
            if (strlen($search_term) < 2) {
                $this->output->set_output(json_encode(array(
                    'success' => false,
                    'message' => 'Minimal 2 karakter untuk pencarian'
                )));
                return;
            }
            
            $this->db->select('pasien_id, nama, nik');
            $this->db->from('pasien');
            $this->db->group_start();
            $this->db->like('nama', $search_term);
            $this->db->or_like('nik', $search_term);
            $this->db->group_end();
            $this->db->order_by('nama', 'ASC');
            $this->db->limit(10);
            
            $patients = $this->db->get()->result_array();
            
            $this->output->set_output(json_encode(array(
                'success' => true,
                'data' => $patients
            )));
            
        } catch (Exception $e) {
            log_message('error', 'Error searching patients: ' . $e->getMessage());
            $this->output->set_output(json_encode(array(
                'success' => false,
                'message' => 'Gagal mencari data pasien'
            )));
        }
    }

    public function ajax_get_examination_types()
    {
        $this->output->set_content_type('application/json');
        
        try {
            $this->db->select('DISTINCT jenis_pemeriksaan');
            $this->db->from('pemeriksaan_lab');
            $this->db->where('jenis_pemeriksaan IS NOT NULL');
            $this->db->where('jenis_pemeriksaan !=', '');
            $this->db->order_by('jenis_pemeriksaan', 'ASC');
            
            $types = $this->db->get()->result_array();
            
            $this->output->set_output(json_encode(array(
                'success' => true,
                'data' => $types
            )));
            
        } catch (Exception $e) {
            log_message('error', 'Error getting examination types: ' . $e->getMessage());
            $this->output->set_output(json_encode(array(
                'success' => false,
                'message' => 'Gagal mengambil jenis pemeriksaan'
            )));
        }
    }
    
    public function ajax_get_financial_reports()
    {
        $this->output->set_content_type('application/json');
        
        try {
            $filters = array(
                'start_date' => $this->input->get('start_date'),
                'end_date' => $this->input->get('end_date'),
                'status' => $this->input->get('status'),
                'jenis_pembayaran' => $this->input->get('jenis_pembayaran'),
                'metode_pembayaran' => $this->input->get('metode_pembayaran'),
                'search' => $this->input->get('search')
            );

            $per_page = $this->input->get('per_page') ? (int)$this->input->get('per_page') : 20;
            $page = $this->input->get('page') ? (int)$this->input->get('page') : 1;
            $offset = ($page - 1) * $per_page;
            
            // Validasi parameter
            if ($per_page > 100) $per_page = 100;
            if ($page < 1) $page = 1;
            
            $invoices = $this->Administrasi_laporan_model->get_financial_reports($per_page, $offset, $filters);
            $total_records = $this->Administrasi_laporan_model->count_financial_reports($filters);
            $stats = $this->Administrasi_laporan_model->get_financial_statistics($filters);
            $chart_data = $this->Administrasi_laporan_model->get_financial_chart_data($filters);
            
            $response = array(
                'success' => true,
                'invoices' => $invoices,
                'total_records' => $total_records,
                'stats' => $stats,
                'chart_data' => $chart_data,
                'pagination' => array(
                    'current_page' => $page,
                    'per_page' => $per_page,
                    'total_pages' => ceil($total_records / $per_page),
                    'total_records' => $total_records
                )
            );
            
        } catch (Exception $e) {
            log_message('error', 'Error getting financial reports: ' . $e->getMessage());
            $response = array(
                'success' => false,
                'message' => 'Gagal mengambil data laporan keuangan'
            );
        }
        
        $this->output->set_output(json_encode($response));
    }

    public function ajax_get_invoice_detail($invoice_id)
    {
        $this->output->set_content_type('application/json');
        
        try {
            if (empty($invoice_id) || !is_numeric($invoice_id)) {
                $this->output->set_output(json_encode(array(
                    'success' => false,
                    'message' => 'ID invoice tidak valid'
                )));
                return;
            }
            
            $invoice_id = (int)$invoice_id;
            
            $invoice = $this->Administrasi_laporan_model->get_invoice_detail($invoice_id);
            
            if (!$invoice) {
                $this->output->set_output(json_encode(array(
                    'success' => false,
                    'message' => 'Invoice tidak ditemukan'
                )));
                return;
            }
            
            $examination = $this->Administrasi_laporan_model->get_examination_by_invoice($invoice_id);
            
            $response = array(
                'success' => true,
                'invoice' => $invoice,
                'examination' => $examination
            );
            
        } catch (Exception $e) {
            log_message('error', 'Error getting invoice detail: ' . $e->getMessage());
            $response = array(
                'success' => false,
                'message' => 'Gagal mengambil detail invoice'
            );
        }
        
        $this->output->set_output(json_encode($response));
    }

    public function ajax_update_payment_status()
    {
        $this->output->set_content_type('application/json');
        
        if ($this->input->method() !== 'post') {
            $this->output->set_output(json_encode(array(
                'success' => false,
                'message' => 'Method not allowed'
            )));
            return;
        }
        
        try {
            $invoice_id = $this->input->post('invoice_id');
            $status = $this->input->post('status');
            $metode_pembayaran = $this->input->post('metode_pembayaran');
            $tanggal_pembayaran = $this->input->post('tanggal_pembayaran');
            $keterangan = $this->input->post('keterangan');
            
            // Validasi input
            if (!$invoice_id || !$status) {
                $this->output->set_output(json_encode(array(
                    'success' => false,
                    'message' => 'Data tidak lengkap'
                )));
                return;
            }
            
            if (!is_numeric($invoice_id)) {
                $this->output->set_output(json_encode(array(
                    'success' => false,
                    'message' => 'ID invoice tidak valid'
                )));
                return;
            }
            
            if (!in_array($status, ['lunas', 'belum_bayar', 'cicilan'])) {
                $this->output->set_output(json_encode(array(
                    'success' => false,
                    'message' => 'Status pembayaran tidak valid'
                )));
                return;
            }
            
            $update_data = array(
                'status_pembayaran' => $status,
                'metode_pembayaran' => $metode_pembayaran,
                'keterangan' => $keterangan
            );
            
            if ($status === 'lunas' && $tanggal_pembayaran) {
                $update_data['tanggal_pembayaran'] = $tanggal_pembayaran;
            }
            
            if ($this->Administrasi_laporan_model->update_invoice_payment($invoice_id, $update_data)) {
                $this->Administrasi_laporan_model->log_activity(
                    $this->session->userdata('user_id'),
                    'Status pembayaran invoice diperbarui: ' . $status,
                    'invoice',
                    $invoice_id
                );
                
                $this->output->set_output(json_encode(array(
                    'success' => true,
                    'message' => 'Status pembayaran berhasil diperbarui'
                )));
            } else {
                $this->output->set_output(json_encode(array(
                    'success' => false,
                    'message' => 'Gagal memperbarui status pembayaran'
                )));
            }
            
        } catch (Exception $e) {
            log_message('error', 'Error updating payment status: ' . $e->getMessage());
            $this->output->set_output(json_encode(array(
                'success' => false,
                'message' => 'Terjadi kesalahan sistem'
            )));
        }
    }
}
?>