<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Inventory_lab extends CI_Controller {

    public function __construct()
    {
        parent::__construct();
        
        if (!$this->session->userdata('logged_in')) {
            $this->session->set_flashdata('error', 'Silakan login terlebih dahulu.');
            redirect('auth/login');
        }
        
        // Check if user is petugas_lab
        if ($this->session->userdata('role') !== 'petugas_lab') {
            $this->session->set_flashdata('error', 'Akses ditolak. Halaman ini khusus untuk petugas laboratorium.');
            redirect('dashboard');
        }
        
        $this->load->model(['Inventory_lab_model', 'Admin_model']);
        $this->load->library(['form_validation', 'upload']);
        $this->load->helper(['form', 'url', 'date']);
    }

    // ==========================================
    // MAIN PAGES
    // ==========================================

    public function index()
    {
        redirect('inventory_lab/kelola');
    }

    public function kelola()
    {
        // Set fullwidth layout
        $data['fullwidth'] = true;
        $data['title'] = 'Kelola Inventory - Petugas Lab';
        
        try {
            $data['inventory'] = $this->Inventory_lab_model->get_all_inventory();
            $data['stats'] = $this->Inventory_lab_model->get_inventory_statistics();
        } catch (Exception $e) {
            log_message('error', 'Error getting inventory data: ' . $e->getMessage());
            $data['inventory'] = array();
            $data['stats'] = $this->_get_default_stats();
        }
        
        // Log activity
        $this->Admin_model->log_activity(
            $this->session->userdata('user_id'),
            'Mengakses halaman kelola inventory petugas',
            'system',
            null
        );
        
        // Load view dengan fullwidth
        $this->load->view('template/header', $data);
        $this->load->view('template/sidebar', $data);
        $this->_load_fullwidth_view('laboratorium/inventory_management', $data);
        $this->load->view('template/footer');
    }
    public function get_statistics()
{
    $this->output->set_content_type('application/json');
    
    try {
        // Call updated model method
        $stats = $this->Inventory_lab_model->get_inventory_statistics();
        
        $response = array(
            'success' => true,
            'stats' => $stats
        );
    } catch (Exception $e) {
        log_message('error', 'Error getting statistics: ' . $e->getMessage());
        $response = array(
            'success' => false,
            'message' => 'Gagal mengambil statistik'
        );
    }
    
    $this->output->set_output(json_encode($response));
}

    // ==========================================
    // AJAX METHODS
    // ==========================================

    public function get_inventory_data()
    {
        $this->output->set_content_type('application/json');
        
        try {
            $inventory = $this->Inventory_lab_model->get_all_inventory();
            
            $response = array(
                'success' => true,
                'inventory' => $inventory
            );
        } catch (Exception $e) {
            log_message('error', 'Error getting inventory data: ' . $e->getMessage());
            $response = array(
                'success' => false,
                'message' => 'Gagal mengambil data inventory'
            );
        }
        
        $this->output->set_output(json_encode($response));
    }


    public function ajax_create_item()
    {
        $this->output->set_content_type('application/json');
        
        if ($this->input->method() !== 'post') {
            $this->output->set_output(json_encode(array(
                'success' => false,
                'message' => 'Method not allowed'
            )));
            return;
        }
        
        $item_type = $this->input->post('item_type');
        
        if (!in_array($item_type, ['alat', 'reagen'])) {
            $this->output->set_output(json_encode(array(
                'success' => false,
                'message' => 'Tipe item tidak valid'
            )));
            return;
        }
        
        // Set validation rules based on item type
        $this->_set_validation_rules($item_type);
        
        if ($this->form_validation->run() === FALSE) {
            $this->output->set_output(json_encode(array(
                'success' => false,
                'message' => 'Validasi gagal',
                'errors' => $this->form_validation->error_array()
            )));
            return;
        }
        
        try {
            $item_data = $this->_prepare_item_data($item_type);
            
            if ($item_type === 'alat') {
                $item_id = $this->Inventory_lab_model->create_alat($item_data);
                $table = 'alat_laboratorium';
            } else {
                $item_id = $this->Inventory_lab_model->create_reagen($item_data);
                $table = 'reagen';
            }
            
            if ($item_id) {
                // Log activity
                $this->Admin_model->log_activity(
                    $this->session->userdata('user_id'),
                    'Item inventory baru ditambahkan: ' . $this->input->post('nama_item'),
                    $table,
                    $item_id
                );
                
                $this->output->set_output(json_encode(array(
                    'success' => true,
                    'message' => 'Item berhasil ditambahkan',
                    'item_id' => $item_id
                )));
            } else {
                $this->output->set_output(json_encode(array(
                    'success' => false,
                    'message' => 'Gagal menambahkan item'
                )));
            }
            
        } catch (Exception $e) {
            log_message('error', 'Error creating inventory item: ' . $e->getMessage());
            $this->output->set_output(json_encode(array(
                'success' => false,
                'message' => 'Terjadi kesalahan sistem: ' . $e->getMessage()
            )));
        }
    }

    public function ajax_get_item_details($item_id, $type)
    {
        $this->output->set_content_type('application/json');
        
        try {
            if (empty($item_id) || !is_numeric($item_id) || !in_array($type, ['alat', 'reagen'])) {
                $this->output->set_output(json_encode(array(
                    'success' => false,
                    'message' => 'Parameter tidak valid'
                )));
                return;
            }
            
            $item = $this->Inventory_lab_model->get_item_details($item_id, $type);
            
            if (!$item) {
                $this->output->set_output(json_encode(array(
                    'success' => false,
                    'message' => 'Item tidak ditemukan'
                )));
                return;
            }
            
            $this->output->set_output(json_encode(array(
                'success' => true,
                'item' => $item
            )));
            
        } catch (Exception $e) {
            log_message('error', 'Error getting item details: ' . $e->getMessage());
            $this->output->set_output(json_encode(array(
                'success' => false,
                'message' => 'Gagal mengambil detail item'
            )));
        }
    }

    public function ajax_update_item($item_id)
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
            if (empty($item_id) || !is_numeric($item_id)) {
                $this->output->set_output(json_encode(array(
                    'success' => false,
                    'message' => 'ID item tidak valid'
                )));
                return;
            }
            
            $item_type = $this->input->post('item_type');
            
            if (!in_array($item_type, ['alat', 'reagen'])) {
                $this->output->set_output(json_encode(array(
                    'success' => false,
                    'message' => 'Tipe item tidak valid'
                )));
                return;
            }
            
            // Check if item exists
            $item = $this->Inventory_lab_model->get_item_details($item_id, $item_type);
            
            if (!$item) {
                $this->output->set_output(json_encode(array(
                    'success' => false,
                    'message' => 'Item tidak ditemukan'
                )));
                return;
            }
            
            // Set validation rules for update
            $this->_set_validation_rules($item_type, true, $item_id);
            
            if ($this->form_validation->run() === FALSE) {
                $this->output->set_output(json_encode(array(
                    'success' => false,
                    'message' => 'Validasi gagal',
                    'errors' => $this->form_validation->error_array()
                )));
                return;
            }
            
            $item_data = $this->_prepare_item_data($item_type, true);
            
            $success = $this->Inventory_lab_model->update_item($item_id, $item_type, $item_data);
            
            if ($success) {
                // Log activity
                $item_name = $item_type === 'alat' ? $item['nama_alat'] : $item['nama_reagen'];
                $this->Admin_model->log_activity(
                    $this->session->userdata('user_id'),
                    'Item inventory diperbarui: ' . $item_name,
                    $item_type === 'alat' ? 'alat_laboratorium' : 'reagen',
                    $item_id
                );
                
                $this->output->set_output(json_encode(array(
                    'success' => true,
                    'message' => 'Item berhasil diperbarui'
                )));
            } else {
                $this->output->set_output(json_encode(array(
                    'success' => false,
                    'message' => 'Gagal memperbarui item'
                )));
            }
            
        } catch (Exception $e) {
            log_message('error', 'Error updating inventory item: ' . $e->getMessage());
            $this->output->set_output(json_encode(array(
                'success' => false,
                'message' => 'Terjadi kesalahan sistem: ' . $e->getMessage()
            )));
        }
    }

    // ==========================================
    // KALIBRASI METHODS
    // ==========================================

    public function ajax_schedule_calibration()
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
            $alat_id = $this->input->post('alat_id');
            $jadwal_kalibrasi = $this->input->post('jadwal_kalibrasi');
            $catatan = $this->input->post('catatan');
            
            if (empty($alat_id) || !is_numeric($alat_id) || empty($jadwal_kalibrasi)) {
                $this->output->set_output(json_encode(array(
                    'success' => false,
                    'message' => 'Parameter tidak lengkap'
                )));
                return;
            }
            
            $update_data = array(
                'jadwal_kalibrasi' => $jadwal_kalibrasi,
                'status_alat' => 'Perlu Kalibrasi',
                'updated_at' => date('Y-m-d H:i:s')
            );
            
            if (!empty($catatan)) {
                $update_data['riwayat_perbaikan'] = $catatan;
            }
            
            $success = $this->Inventory_lab_model->update_item($alat_id, 'alat', $update_data);
            
            if ($success) {
                $alat = $this->Inventory_lab_model->get_item_details($alat_id, 'alat');
                
                $this->Admin_model->log_activity(
                    $this->session->userdata('user_id'),
                    "Kalibrasi dijadwalkan untuk {$alat['nama_alat']} pada " . date('d/m/Y', strtotime($jadwal_kalibrasi)),
                    'alat_laboratorium',
                    $alat_id
                );
                
                $this->output->set_output(json_encode(array(
                    'success' => true,
                    'message' => 'Jadwal kalibrasi berhasil disimpan'
                )));
            } else {
                $this->output->set_output(json_encode(array(
                    'success' => false,
                    'message' => 'Gagal menjadwalkan kalibrasi'
                )));
            }
            
        } catch (Exception $e) {
            log_message('error', 'Error scheduling calibration: ' . $e->getMessage());
            $this->output->set_output(json_encode(array(
                'success' => false,
                'message' => 'Terjadi kesalahan saat menjadwalkan kalibrasi'
            )));
        }
    }

    public function ajax_complete_calibration()
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
            $alat_id = $this->input->post('alat_id');
            $tanggal_kalibrasi = $this->input->post('tanggal_kalibrasi');
            $hasil_kalibrasi = $this->input->post('hasil_kalibrasi');
            $teknisi = $this->input->post('teknisi');
            
            if (empty($alat_id) || !is_numeric($alat_id) || empty($tanggal_kalibrasi)) {
                $this->output->set_output(json_encode(array(
                    'success' => false,
                    'message' => 'Parameter tidak lengkap'
                )));
                return;
            }
            
            // Calculate next calibration date (usually 1 year later)
            $next_calibration = date('Y-m-d', strtotime($tanggal_kalibrasi . ' +1 year'));
            
            $update_data = array(
                'tanggal_kalibrasi_terakhir' => $tanggal_kalibrasi,
                'jadwal_kalibrasi' => $next_calibration,
                'status_alat' => 'Normal',
                'updated_at' => date('Y-m-d H:i:s')
            );
            
            // Update calibration history
            $calibration_record = "Kalibrasi selesai pada " . date('d/m/Y', strtotime($tanggal_kalibrasi));
            if (!empty($teknisi)) {
                $calibration_record .= " oleh {$teknisi}";
            }
            if (!empty($hasil_kalibrasi)) {
                $calibration_record .= ". Hasil: {$hasil_kalibrasi}";
            }
            
            $alat = $this->Inventory_lab_model->get_item_details($alat_id, 'alat');
            $existing_history = $alat['riwayat_perbaikan'];
            $new_history = empty($existing_history) ? $calibration_record : $existing_history . "\n" . $calibration_record;
            $update_data['riwayat_perbaikan'] = $new_history;
            
            $success = $this->Inventory_lab_model->update_item($alat_id, 'alat', $update_data);
            
            if ($success) {
                $this->Admin_model->log_activity(
                    $this->session->userdata('user_id'),
                    "Kalibrasi {$alat['nama_alat']} selesai. Jadwal berikutnya: " . date('d/m/Y', strtotime($next_calibration)),
                    'alat_laboratorium',
                    $alat_id
                );
                
                $this->output->set_output(json_encode(array(
                    'success' => true,
                    'message' => 'Kalibrasi berhasil diselesaikan',
                    'next_calibration' => $next_calibration
                )));
            } else {
                $this->output->set_output(json_encode(array(
                    'success' => false,
                    'message' => 'Gagal menyelesaikan kalibrasi'
                )));
            }
            
        } catch (Exception $e) {
            log_message('error', 'Error completing calibration: ' . $e->getMessage());
            $this->output->set_output(json_encode(array(
                'success' => false,
                'message' => 'Terjadi kesalahan saat menyelesaikan kalibrasi'
            )));
        }
    }

    // ==========================================
    // FILTER METHODS
    // ==========================================

    public function get_filtered_inventory()
    {
        $this->output->set_content_type('application/json');
        
        if ($this->input->method() !== 'get') {
            $this->output->set_output(json_encode(array(
                'success' => false,
                'message' => 'Method not allowed'
            )));
            return;
        }
        
        try {
            $filters = array(
                'type' => $this->input->get('type'),
                'status' => $this->input->get('status'), 
                'alert' => $this->input->get('alert'),
                'search' => $this->input->get('search')
            );
            
            $filters = array_filter($filters, function($value) {
                return !empty($value) && $value !== '';
            });
            
            $inventory = $this->Inventory_lab_model->get_filtered_inventory($filters);
            
            $response = array(
                'success' => true,
                'inventory' => $inventory,
                'count' => count($inventory),
                'filters_applied' => $filters
            );
            
        } catch (Exception $e) {
            log_message('error', 'Error filtering inventory: ' . $e->getMessage());
            $response = array(
                'success' => false,
                'message' => 'Gagal memfilter data inventory: ' . $e->getMessage(),
                'inventory' => array()
            );
        }
        
        $this->output->set_output(json_encode($response));
    }

    // ==========================================
    // VALIDATION METHODS
    // ==========================================

    public function check_kode_unique($kode, $params = null)
    {
        if (empty($kode)) {
            return TRUE; // Kode is optional (will be auto-generated)
        }
        
        $param_array = explode('.', $params);
        $type = $param_array[0];
        $item_id = isset($param_array[1]) ? $param_array[1] : null;
        
        if ($this->Inventory_lab_model->check_kode_exists($kode, $type, $item_id)) {
            $this->form_validation->set_message('check_kode_unique', 'Kode sudah digunakan');
            return FALSE;
        }
        return TRUE;
    }

    public function valid_date($date)
    {
        if (empty($date)) {
            return TRUE; // Date is optional
        }
        
        $d = DateTime::createFromFormat('Y-m-d', $date);
        if ($d && $d->format('Y-m-d') === $date) {
            return TRUE;
        }
        
        $this->form_validation->set_message('valid_date', 'Format tanggal tidak valid (YYYY-MM-DD)');
        return FALSE;
    }

    // ==========================================
    // PRIVATE HELPER METHODS
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

    private function _set_validation_rules($type, $is_update = false, $item_id = null)
    {
        $this->form_validation->set_rules('nama_item', 'Nama Item', 'required|min_length[2]|max_length[100]');
        
        // Kode unique validation
        if ($this->input->post('kode_unik') && !empty($this->input->post('kode_unik'))) {
            $callback_param = $type . ($is_update && $item_id ? '.' . $item_id : '');
            $this->form_validation->set_rules('kode_unik', 'Kode Unik', 'max_length[50]|callback_check_kode_unique[' . $callback_param . ']');
        }
        
        if ($type === 'alat') {
            $this->form_validation->set_rules('merek_model', 'Merek/Model', 'max_length[100]');
            $this->form_validation->set_rules('lokasi', 'Lokasi', 'max_length[100]');
            $this->form_validation->set_rules('status_alat', 'Status Alat', 'in_list[Normal,Perlu Kalibrasi,Rusak,Sedang Kalibrasi]');
            $this->form_validation->set_rules('jadwal_kalibrasi', 'Jadwal Kalibrasi', 'callback_valid_date');
            $this->form_validation->set_rules('tanggal_kalibrasi_terakhir', 'Tanggal Kalibrasi Terakhir', 'callback_valid_date');
            $this->form_validation->set_rules('riwayat_perbaikan', 'Riwayat Perbaikan', '');
        } else {
            $this->form_validation->set_rules('jumlah_stok', 'Jumlah Stok', 'required|integer|greater_than_equal_to[0]');
            $this->form_validation->set_rules('satuan', 'Satuan', 'max_length[20]');
            $this->form_validation->set_rules('stok_minimal', 'Stok Minimal', 'integer|greater_than_equal_to[0]');
            $this->form_validation->set_rules('lokasi_penyimpanan', 'Lokasi Penyimpanan', 'max_length[100]');
            $this->form_validation->set_rules('expired_date', 'Tanggal Kedaluwarsa', 'callback_valid_date');
            $this->form_validation->set_rules('tanggal_dipakai', 'Tanggal Mulai Dipakai', 'callback_valid_date');
            $this->form_validation->set_rules('status', 'Status', 'in_list[Tersedia,Hampir Habis,Dipesan,Kadaluarsa]');
            $this->form_validation->set_rules('catatan', 'Catatan', '');
        }
    }

    private function _prepare_item_data($type, $is_update = false)
    {
        $data = array(
            'nama_' . ($type === 'alat' ? 'alat' : 'reagen') => $this->input->post('nama_item'),
            'kode_unik' => !empty($this->input->post('kode_unik')) ? 
                          $this->input->post('kode_unik') : 
                          $this->Inventory_lab_model->generate_kode($type)
        );
        
        if ($type === 'alat') {
            $data['merek_model'] = !empty($this->input->post('merek_model')) ? $this->input->post('merek_model') : null;
            $data['lokasi'] = !empty($this->input->post('lokasi')) ? $this->input->post('lokasi') : null;
            $data['status_alat'] = $this->input->post('status_alat') ?: 'Normal';
            $data['jadwal_kalibrasi'] = !empty($this->input->post('jadwal_kalibrasi')) ? $this->input->post('jadwal_kalibrasi') : null;
            $data['tanggal_kalibrasi_terakhir'] = !empty($this->input->post('tanggal_kalibrasi_terakhir')) ? $this->input->post('tanggal_kalibrasi_terakhir') : null;
            $data['riwayat_perbaikan'] = !empty($this->input->post('riwayat_perbaikan')) ? $this->input->post('riwayat_perbaikan') : null;
        } else {
            $data['jumlah_stok'] = (int)$this->input->post('jumlah_stok');
            $data['satuan'] = !empty($this->input->post('satuan')) ? $this->input->post('satuan') : null;
            $data['stok_minimal'] = (int)($this->input->post('stok_minimal') ?: 10);
            $data['lokasi_penyimpanan'] = !empty($this->input->post('lokasi_penyimpanan')) ? $this->input->post('lokasi_penyimpanan') : null;
            $data['expired_date'] = !empty($this->input->post('expired_date')) ? $this->input->post('expired_date') : null;
            $data['tanggal_dipakai'] = !empty($this->input->post('tanggal_dipakai')) ? $this->input->post('tanggal_dipakai') : null;
            $data['status'] = $this->input->post('status') ?: 'Tersedia';
            $data['catatan'] = !empty($this->input->post('catatan')) ? $this->input->post('catatan') : null;
        }
        
        if (!$is_update) {
            $data['created_at'] = date('Y-m-d H:i:s');
        } else {
            $data['updated_at'] = date('Y-m-d H:i:s');
        }
        
        return $data;
    }

    private function _get_default_stats()
    {
        return array(
            'total_alat' => 0,
            'total_reagen' => 0,
            'total_alerts' => 0,
            'total_critical' => 0
        );
    }

/**
 * Get calibration reminders (notifications)
 */
public function ajax_get_calibration_reminders()
{
    $this->output->set_content_type('application/json');
    
    try {
        // Get reminders from model
        $reminders_data = $this->Inventory_lab_model->get_calibration_reminders(30);
        
        $notifications = array();
        foreach ($reminders_data as $item) {
            $days = (int)$item['days_until'];
            
            if ($days < 0) {
                $notifications[] = array(
                    'alat_id' => $item['alat_id'],
                    'nama_alat' => $item['nama_alat'],
                    'kode_unik' => $item['kode_unik'],
                    'jadwal' => $item['jadwal_kalibrasi'],
                    'type' => 'danger',
                    'message' => "Kalibrasi {$item['nama_alat']} sudah melewati jadwal " . abs($days) . " hari",
                    'days' => $days,
                    'priority' => 1
                );
            } elseif ($days <= 7) {
                $notifications[] = array(
                    'alat_id' => $item['alat_id'],
                    'nama_alat' => $item['nama_alat'],
                    'kode_unik' => $item['kode_unik'],
                    'jadwal' => $item['jadwal_kalibrasi'],
                    'type' => 'warning',
                    'message' => "Kalibrasi {$item['nama_alat']} dalam {$days} hari",
                    'days' => $days,
                    'priority' => 2
                );
            } else {
                $notifications[] = array(
                    'alat_id' => $item['alat_id'],
                    'nama_alat' => $item['nama_alat'],
                    'kode_unik' => $item['kode_unik'],
                    'jadwal' => $item['jadwal_kalibrasi'],
                    'type' => 'info',
                    'message' => "Kalibrasi {$item['nama_alat']} dalam {$days} hari",
                    'days' => $days,
                    'priority' => 3
                );
            }
        }
        
        $this->output->set_output(json_encode(array(
            'success' => true,
            'reminders' => $notifications,
            'count' => count($notifications)
        )));
        
    } catch (Exception $e) {
        log_message('error', 'Error getting calibration reminders: ' . $e->getMessage());
        $this->output->set_output(json_encode(array(
            'success' => false,
            'message' => 'Gagal memuat reminder kalibrasi: ' . $e->getMessage()
        )));
    }
}


public function ajax_get_calibration_schedule()
{
    $this->output->set_content_type('application/json');
    
    try {
        $this->db->select('
            alat_id,
            nama_alat,
            kode_unik,
            lokasi,
            status_alat,
            jadwal_kalibrasi,
            tanggal_kalibrasi_terakhir,
            DATEDIFF(jadwal_kalibrasi, CURDATE()) as days_until
        ');
        $this->db->from('alat_laboratorium');
        $this->db->where('jadwal_kalibrasi IS NOT NULL');
        $this->db->order_by('jadwal_kalibrasi', 'ASC');
        
        $query = $this->db->get();
        $all_items = $query->result_array();
        
        // Categorize items
        $overdue = array();
        $due_soon = array();
        $up_to_date = array();
        
        foreach ($all_items as $item) {
            $days = (int)$item['days_until'];
            
            if ($days < 0) {
                $item['priority'] = 'urgent';
                $item['days_label'] = abs($days) . ' hari terlambat';
                $overdue[] = $item;
            } elseif ($days <= 30) {
                $item['priority'] = 'high';
                $item['days_label'] = $days . ' hari lagi';
                $due_soon[] = $item;
            } else {
                $item['priority'] = 'normal';
                $item['days_label'] = $days . ' hari lagi';
                $up_to_date[] = $item;
            }
            
            // Get last calibration
            $this->db->select('tanggal_kalibrasi');
            $this->db->from('calibration_history');
            $this->db->where('alat_id', $item['alat_id']);
            $this->db->order_by('tanggal_kalibrasi', 'DESC');
            $this->db->limit(1);
            $last_cal = $this->db->get()->row_array();
            
            $item['last_calibration'] = $last_cal ? $last_cal['tanggal_kalibrasi'] : null;
        }
        
        $stats = array(
            'overdue' => count($overdue),
            'due_soon' => count($due_soon),
            'up_to_date' => count($up_to_date),
            'total' => count($all_items)
        );
        
        $this->output->set_output(json_encode(array(
            'success' => true,
            'data' => array(
                'overdue' => $overdue,
                'due_soon' => $due_soon,
                'up_to_date' => $up_to_date
            ),
            'stats' => $stats
        )));
        
    } catch (Exception $e) {
        log_message('error', 'Error in ajax_get_calibration_schedule: ' . $e->getMessage());
        $this->output->set_output(json_encode(array(
            'success' => false,
            'message' => 'Gagal memuat jadwal kalibrasi: ' . $e->getMessage()
        )));
    }
}

/**
 * AJAX: Save calibration
 */
public function ajax_save_calibration()
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
        $alat_id = $this->input->post('alat_id');
        $mode = $this->input->post('mode');
        
        if (empty($alat_id) || !is_numeric($alat_id)) {
            $this->output->set_output(json_encode(array(
                'success' => false,
                'message' => 'Parameter tidak lengkap'
            )));
            return;
        }
        
        if ($mode === 'schedule') {
            $tanggal_kalibrasi = $this->input->post('tanggal_kalibrasi');
            $catatan = $this->input->post('catatan');
            
            $update_data = array(
                'jadwal_kalibrasi' => $tanggal_kalibrasi,
                'status_alat' => 'Perlu Kalibrasi',
                'updated_at' => date('Y-m-d H:i:s')
            );
            
            if (!empty($catatan)) {
                $update_data['riwayat_perbaikan'] = $catatan;
            }
            
            $success = $this->Inventory_lab_model->update_alat($alat_id, $update_data);
            
            if ($success) {
                $alat = $this->Inventory_lab_model->get_alat_by_id($alat_id);
                $this->Admin_model->log_activity(
                    $this->session->userdata('user_id'),
                    "Kalibrasi dijadwalkan untuk {$alat['nama_alat']}",
                    'alat_laboratorium',
                    $alat_id
                );
                
                $this->output->set_output(json_encode(array(
                    'success' => true,
                    'message' => 'Jadwal kalibrasi berhasil disimpan'
                )));
            } else {
                $this->output->set_output(json_encode(array(
                    'success' => false,
                    'message' => 'Gagal menjadwalkan kalibrasi'
                )));
            }
            
        } elseif ($mode === 'complete') {
            $tanggal_kalibrasi = $this->input->post('tanggal_kalibrasi');
            $hasil_kalibrasi = $this->input->post('hasil_kalibrasi');
            $teknisi = $this->input->post('teknisi');
            $catatan = $this->input->post('catatan');
            
            // Calculate next calibration (1 year later)
            $next_calibration = date('Y-m-d', strtotime($tanggal_kalibrasi . ' +1 year'));
            
            // Save to calibration_history
            $history_data = array(
                'alat_id' => $alat_id,
                'tanggal_kalibrasi' => $tanggal_kalibrasi,
                'hasil_kalibrasi' => $hasil_kalibrasi,
                'teknisi' => $teknisi,
                'catatan' => $catatan,
                'next_calibration_date' => $next_calibration,
                'user_id' => $this->session->userdata('user_id'),
                'created_at' => date('Y-m-d H:i:s')
            );
            
            $this->Inventory_lab_model->save_calibration($history_data);
            
            // Update alat
            $update_data = array(
                'tanggal_kalibrasi_terakhir' => $tanggal_kalibrasi,
                'jadwal_kalibrasi' => $next_calibration,
                'status_alat' => 'Normal',
                'updated_at' => date('Y-m-d H:i:s')
            );
            
            $success = $this->Inventory_lab_model->update_alat($alat_id, $update_data);
            
            if ($success) {
                $alat = $this->Inventory_lab_model->get_alat_by_id($alat_id);
                $this->Admin_model->log_activity(
                    $this->session->userdata('user_id'),
                    "Kalibrasi {$alat['nama_alat']} selesai",
                    'alat_laboratorium',
                    $alat_id
                );
                
                $this->output->set_output(json_encode(array(
                    'success' => true,
                    'message' => 'Kalibrasi berhasil diselesaikan'
                )));
            } else {
                $this->output->set_output(json_encode(array(
                    'success' => false,
                    'message' => 'Gagal menyelesaikan kalibrasi'
                )));
            }
        } else {
            $this->output->set_output(json_encode(array(
                'success' => false,
                'message' => 'Mode tidak valid'
            )));
        }
        
    } catch (Exception $e) {
        log_message('error', 'Error in ajax_save_calibration: ' . $e->getMessage());
        $this->output->set_output(json_encode(array(
            'success' => false,
            'message' => 'Terjadi kesalahan: ' . $e->getMessage()
        )));
    }
}
public function ajax_get_calibration_history($alat_id)
{
    $this->output->set_content_type('application/json');
    
    try {
        // Validasi input
        if (empty($alat_id) || !is_numeric($alat_id)) {
            $this->output->set_output(json_encode(array(
                'success' => false,
                'message' => 'ID alat tidak valid'
            )));
            return;
        }
        
        // Cek apakah alat exists
        $alat = $this->Inventory_lab_model->get_alat_by_id($alat_id);
        
        if (!$alat) {
            $this->output->set_output(json_encode(array(
                'success' => false,
                'message' => 'Alat tidak ditemukan'
            )));
            return;
        }
        
        // Ambil history - tambahkan try-catch
        try {
            $history = $this->Inventory_lab_model->get_calibration_history($alat_id);
        } catch (Exception $e) {
            log_message('error', 'Error getting calibration history: ' . $e->getMessage());
            $history = array(); // Kosongkan jika error
        }
        
        // Ambil stats - tambahkan try-catch
        try {
            $stats = $this->Inventory_lab_model->get_calibration_stats($alat_id);
        } catch (Exception $e) {
            log_message('error', 'Error getting calibration stats: ' . $e->getMessage());
            // Default stats jika error
            $stats = array(
                'total_calibrations' => 0,
                'passed_count' => 0,
                'failed_count' => 0,
                'conditional_count' => 0,
                'avg_interval_days' => 0
            );
        }
        
        $this->output->set_output(json_encode(array(
            'success' => true,
            'alat' => $alat,
            'history' => $history,
            'stats' => $stats
        )));
        
    } catch (Exception $e) {
        log_message('error', 'Error in ajax_get_calibration_history: ' . $e->getMessage());
        $this->output->set_output(json_encode(array(
            'success' => false,
            'message' => 'Gagal memuat riwayat kalibrasi',
            'error_detail' => $e->getMessage()
        )));
    }
}
}