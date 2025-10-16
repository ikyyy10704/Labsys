<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Inventory extends CI_Controller {

    public function __construct()
    {
        parent::__construct();
        
        if (!$this->session->userdata('logged_in')) {
            $this->session->set_flashdata('error', 'Silakan login terlebih dahulu.');
            redirect('auth/login');
        }
        
        $this->load->model(['Inventory_model', 'Admin_model']);
        $this->load->library(['form_validation', 'upload']);
        $this->load->helper(['form', 'url', 'date']);
    }

    // ==========================================
    // MAIN PAGES
    // ==========================================

    public function index()
    {
        // Redirect to kelola for consistency
        redirect('inventory/kelola');
    }
    public function get_inventory_data()
    {
        $this->output->set_content_type('application/json');
        
        try {
            $inventory = $this->Inventory_model->get_all_inventory();
            
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

    public function get_statistics()
    {
        $this->output->set_content_type('application/json');
        
        try {
            $stats = $this->Inventory_model->get_inventory_statistics();
            
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
                $item_id = $this->Inventory_model->create_alat($item_data);
                $table = 'alat_laboratorium';
            } else {
                $item_id = $this->Inventory_model->create_reagen($item_data);
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
            
            if ($type === 'alat') {
                $item = $this->Inventory_model->get_alat_by_id($item_id);
            } else {
                $item = $this->Inventory_model->get_reagen_by_id($item_id);
            }
            
            if (!$item) {
                $this->output->set_output(json_encode(array(
                    'success' => false,
                    'message' => 'Item tidak ditemukan'
                )));
                return;
            }
            
            // Add item_id for consistency
            $item['item_id'] = $item_id;
            
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
            if ($item_type === 'alat') {
                $item = $this->Inventory_model->get_alat_by_id($item_id);
            } else {
                $item = $this->Inventory_model->get_reagen_by_id($item_id);
            }
            
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
            
            if ($item_type === 'alat') {
                $success = $this->Inventory_model->update_alat($item_id, $item_data);
                $table = 'alat_laboratorium';
            } else {
                $success = $this->Inventory_model->update_reagen($item_id, $item_data);
                $table = 'reagen';
            }
            
            if ($success) {
                // Log activity
                $this->Admin_model->log_activity(
                    $this->session->userdata('user_id'),
                    'Item inventory diperbarui: ' . $item['nama_' . ($item_type === 'alat' ? 'alat' : 'reagen')],
                    $table,
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

    public function ajax_delete_item($item_id, $type)
    {
        $this->output->set_content_type('application/json');
        
        if (!in_array($this->input->method(), ['post', 'delete'])) {
            $this->output->set_output(json_encode(array(
                'success' => false,
                'message' => 'Method tidak diizinkan'
            )));
            return;
        }
        
        try {
            if (empty($item_id) || !is_numeric($item_id) || !in_array($type, ['alat', 'reagen'])) {
                $this->output->set_output(json_encode(array(
                    'success' => false,
                    'message' => 'Parameter tidak valid'
                )));
                return;
            }
            
            // Get item details for logging
            if ($type === 'alat') {
                $item = $this->Inventory_model->get_alat_by_id($item_id);
                $table = 'alat_laboratorium';
            } else {
                $item = $this->Inventory_model->get_reagen_by_id($item_id);
                $table = 'reagen';
            }
            
            if (!$item) {
                $this->output->set_output(json_encode(array(
                    'success' => false,
                    'message' => 'Item tidak ditemukan'
                )));
                return;
            }
            
            // Check if item is being used (you can add more checks here)
            // For now, we'll just delete it
            
            if ($type === 'alat') {
                $success = $this->Inventory_model->delete_alat($item_id);
            } else {
                $success = $this->Inventory_model->delete_reagen($item_id);
            }
            
            if ($success) {
                // Log activity
                $item_name = $item['nama_' . ($type === 'alat' ? 'alat' : 'reagen')];
                $this->Admin_model->log_activity(
                    $this->session->userdata('user_id'),
                    'Item inventory dihapus: ' . $item_name,
                    $table,
                    $item_id
                );
                
                $this->output->set_output(json_encode(array(
                    'success' => true,
                    'message' => 'Item berhasil dihapus'
                )));
            } else {
                $this->output->set_output(json_encode(array(
                    'success' => false,
                    'message' => 'Gagal menghapus item'
                )));
            }
            
        } catch (Exception $e) {
            log_message('error', 'Error deleting inventory item: ' . $e->getMessage());
            $this->output->set_output(json_encode(array(
                'success' => false,
                'message' => 'Terjadi kesalahan sistem: ' . $e->getMessage()
            )));
        }
    }

    // ==========================================
    // INVENTORY REPORTS & ANALYTICS
    // ==========================================

    public function reports()
    {
        // Set fullwidth layout
        $data['fullwidth'] = true;
        $data['title'] = 'Laporan Inventory';
        
        try {
            $data['stats'] = $this->Inventory_model->get_detailed_inventory_statistics();
            $data['alerts'] = $this->Inventory_model->get_inventory_alerts();
            $data['chart_data'] = $this->Inventory_model->get_inventory_chart_data();
            
            // Log activity
            $this->Admin_model->log_activity(
                $this->session->userdata('user_id'),
                'Mengakses laporan inventory',
                'system',
                null
            );
            
        } catch (Exception $e) {
            log_message('error', 'Error loading inventory reports: ' . $e->getMessage());
            $data['stats'] = array();
            $data['alerts'] = array();
            $data['chart_data'] = array();
        }
        
        $this->load->view('template/header', $data);
        $this->load->view('template/sidebar', $data);
        $this->_load_fullwidth_view('inventory/reports', $data);
        $this->load->view('template/footer', $data);
    }

    // ==========================================
    // VALIDATION CALLBACKS
    // ==========================================

    public function check_kode_unique($kode, $params = null)
    {
        if (empty($kode)) {
            return TRUE; // Kode is optional (will be auto-generated)
        }
        
        $param_array = explode('.', $params);
        $type = $param_array[0];
        $item_id = isset($param_array[1]) ? $param_array[1] : null;
        
        if ($this->Inventory_model->check_kode_exists($kode, $type, $item_id)) {
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
                          $this->Inventory_model->generate_kode($type)
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
     public function get_high_priority_alerts()
    {
        $this->output->set_content_type('application/json');
        
        try {
            $alerts = $this->Inventory_model->get_high_priority_alerts();
            
            $response = array(
                'success' => true,
                'alerts' => $alerts
            );
        } catch (Exception $e) {
            log_message('error', 'Error getting high priority alerts: ' . $e->getMessage());
            $response = array(
                'success' => false,
                'message' => 'Gagal mengambil alert prioritas tinggi'
            );
        }
        
        $this->output->set_output(json_encode($response));
    }

    public function get_recent_activities()
    {
        $this->output->set_content_type('application/json');
        
        try {
            // Get recent activities from activity log related to inventory
            $this->db->select('activity, created_at, table_affected, record_id');
            $this->db->from('activity_log');
            $this->db->where_in('table_affected', array('alat_laboratorium', 'reagen', 'inventory'));
            $this->db->order_by('created_at', 'DESC');
            $this->db->limit(10);
            
            $query = $this->db->get();
            $activities = $query->result_array();
            
            $response = array(
                'success' => true,
                'activities' => $activities
            );
        } catch (Exception $e) {
            log_message('error', 'Error getting recent activities: ' . $e->getMessage());
            $response = array(
                'success' => false,
                'message' => 'Gagal mengambil aktivitas terbaru'
            );
        }
        
        $this->output->set_output(json_encode($response));
    }

    public function get_expiring_items()
    {
        $this->output->set_content_type('application/json');
        
        try {
            $expiring_items = $this->Inventory_model->get_expiring_items();
            
            $response = array(
                'success' => true,
                'items' => $expiring_items
            );
        } catch (Exception $e) {
            log_message('error', 'Error getting expiring items: ' . $e->getMessage());
            $response = array(
                'success' => false,
                'message' => 'Gagal mengambil item yang akan expired'
            );
        }
        
        $this->output->set_output(json_encode($response));
    }

    public function get_low_stock_items()
    {
        $this->output->set_content_type('application/json');
        
        try {
            $low_stock_items = $this->Inventory_model->get_low_stock_items();
            
            $response = array(
                'success' => true,
                'items' => $low_stock_items
            );
        } catch (Exception $e) {
            log_message('error', 'Error getting low stock items: ' . $e->getMessage());
            $response = array(
                'success' => false,
                'message' => 'Gagal mengambil item stok rendah'
            );
        }
        
        $this->output->set_output(json_encode($response));
    }

    public function get_maintenance_schedule()
    {
        $this->output->set_content_type('application/json');
        
        try {
            $maintenance_schedule = $this->Inventory_model->get_maintenance_schedule();
            
            $response = array(
                'success' => true,
                'schedule' => $maintenance_schedule
            );
        } catch (Exception $e) {
            log_message('error', 'Error getting maintenance schedule: ' . $e->getMessage());
            $response = array(
                'success' => false,
                'message' => 'Gagal mengambil jadwal maintenance'
            );
        }
        
        $this->output->set_output(json_encode($response));
    }

    // ==========================================
    // BULK OPERATIONS
    // ==========================================

    public function ajax_bulk_update_status()
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
            $items = $this->input->post('items'); // Array of item IDs
            $type = $this->input->post('type'); // 'alat' or 'reagen'
            $new_status = $this->input->post('new_status');
            
            if (empty($items) || !is_array($items) || empty($type) || empty($new_status)) {
                $this->output->set_output(json_encode(array(
                    'success' => false,
                    'message' => 'Parameter tidak lengkap'
                )));
                return;
            }
            
            $updated_count = 0;
            foreach ($items as $item_id) {
                if ($type === 'alat') {
                    $success = $this->Inventory_model->update_alat($item_id, array('status_alat' => $new_status));
                } else {
                    $success = $this->Inventory_model->update_reagen($item_id, array('status' => $new_status));
                }
                
                if ($success) {
                    $updated_count++;
                }
            }
            
            // Log activity
            $this->Admin_model->log_activity(
                $this->session->userdata('user_id'),
                "Bulk update status {$type}: {$updated_count} item(s) diperbarui ke {$new_status}",
                $type === 'alat' ? 'alat_laboratorium' : 'reagen',
                null
            );
            
            $this->output->set_output(json_encode(array(
                'success' => true,
                'message' => "{$updated_count} item berhasil diperbarui",
                'updated_count' => $updated_count
            )));
            
        } catch (Exception $e) {
            log_message('error', 'Error in bulk update: ' . $e->getMessage());
            $this->output->set_output(json_encode(array(
                'success' => false,
                'message' => 'Terjadi kesalahan saat update massal'
            )));
        }
    }

    public function ajax_quick_stock_update()
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
            $reagen_id = $this->input->post('reagen_id');
            $new_stock = $this->input->post('new_stock');
            $adjustment_type = $this->input->post('adjustment_type'); // 'add', 'subtract', 'set'
            $notes = $this->input->post('notes');
            
            if (empty($reagen_id) || !is_numeric($reagen_id) || !is_numeric($new_stock)) {
                $this->output->set_output(json_encode(array(
                    'success' => false,
                    'message' => 'Parameter tidak valid'
                )));
                return;
            }
            
            $reagen = $this->Inventory_model->get_reagen_by_id($reagen_id);
            if (!$reagen) {
                $this->output->set_output(json_encode(array(
                    'success' => false,
                    'message' => 'Reagen tidak ditemukan'
                )));
                return;
            }
            
            $current_stock = $reagen['jumlah_stok'];
            $final_stock = $current_stock;
            
            switch ($adjustment_type) {
                case 'add':
                    $final_stock = $current_stock + $new_stock;
                    break;
                case 'subtract':
                    $final_stock = max(0, $current_stock - $new_stock);
                    break;
                case 'set':
                    $final_stock = $new_stock;
                    break;
            }
            
            $update_data = array(
                'jumlah_stok' => $final_stock,
                'updated_at' => date('Y-m-d H:i:s')
            );
            
            if ($this->Inventory_model->update_reagen($reagen_id, $update_data)) {
                // Log stock adjustment
                $adjustment_log = "Stok {$reagen['nama_reagen']} diubah dari {$current_stock} menjadi {$final_stock}";
                if (!empty($notes)) {
                    $adjustment_log .= ". Catatan: {$notes}";
                }
                
                $this->Admin_model->log_activity(
                    $this->session->userdata('user_id'),
                    $adjustment_log,
                    'reagen',
                    $reagen_id
                );
                
                $this->output->set_output(json_encode(array(
                    'success' => true,
                    'message' => 'Stok berhasil diperbarui',
                    'old_stock' => $current_stock,
                    'new_stock' => $final_stock
                )));
            } else {
                $this->output->set_output(json_encode(array(
                    'success' => false,
                    'message' => 'Gagal memperbarui stok'
                )));
            }
            
        } catch (Exception $e) {
            log_message('error', 'Error in quick stock update: ' . $e->getMessage());
            $this->output->set_output(json_encode(array(
                'success' => false,
                'message' => 'Terjadi kesalahan saat update stok'
            )));
        }
    }

    // ==========================================
    // MAINTENANCE & CALIBRATION
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
            
            if ($this->Inventory_model->update_alat($alat_id, $update_data)) {
                $alat = $this->Inventory_model->get_alat_by_id($alat_id);
                
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
            
            $alat = $this->Inventory_model->get_alat_by_id($alat_id);
            $existing_history = $alat['riwayat_perbaikan'];
            $new_history = empty($existing_history) ? $calibration_record : $existing_history . "\n" . $calibration_record;
            $update_data['riwayat_perbaikan'] = $new_history;
            
            if ($this->Inventory_model->update_alat($alat_id, $update_data)) {
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
    // IMPORT/EXPORT UTILITIES
    // ==========================================

    public function ajax_import_inventory()
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
            // Configure upload
            $config['upload_path'] = './uploads/temp/';
            $config['allowed_types'] = 'xlsx|xls|csv';
            $config['max_size'] = 10240; // 10MB
            $config['file_name'] = 'inventory_import_' . time();
            
            // Create temp directory if not exists
            if (!is_dir($config['upload_path'])) {
                mkdir($config['upload_path'], 0755, true);
            }
            
            $this->upload->initialize($config);
            
            if (!$this->upload->do_upload('import_file')) {
                $this->output->set_output(json_encode(array(
                    'success' => false,
                    'message' => 'Upload gagal: ' . $this->upload->display_errors('', '')
                )));
                return;
            }
            
            $upload_data = $this->upload->data();
            $file_path = $upload_data['full_path'];
            
            // Process the uploaded file
            $import_result = $this->_process_inventory_import($file_path, $upload_data['file_ext']);
            
            // Clean up temp file
            unlink($file_path);
            
            if ($import_result['success']) {
                $this->Admin_model->log_activity(
                    $this->session->userdata('user_id'),
                    "Import inventory: {$import_result['imported']} item berhasil, {$import_result['failed']} gagal",
                    'inventory',
                    null
                );
            }
            
            $this->output->set_output(json_encode($import_result));
            
        } catch (Exception $e) {
            log_message('error', 'Error importing inventory: ' . $e->getMessage());
            $this->output->set_output(json_encode(array(
                'success' => false,
                'message' => 'Terjadi kesalahan saat import: ' . $e->getMessage()
            )));
        }
    }

    private function _process_inventory_import($file_path, $file_ext)
    {
        // This would process Excel/CSV file and import inventory data
        // Implementation depends on your specific file format and requirements
        
        return array(
            'success' => true,
            'message' => 'Import berhasil',
            'imported' => 0,
            'failed' => 0,
            'errors' => array()
        );
    }

public function get_filtered_inventory()
{
    $this->output->set_content_type('application/json');
    
    // Pastikan method hanya menerima GET request
    if ($this->input->method() !== 'get') {
        $this->output->set_output(json_encode(array(
            'success' => false,
            'message' => 'Method not allowed'
        )));
        return;
    }
    
    try {
        // Ambil parameter filter dari GET request
        $filters = array(
            'type' => $this->input->get('type'),
            'status' => $this->input->get('status'), 
            'alert' => $this->input->get('alert'),
            'search' => $this->input->get('search')
        );
        
        // Clean filter values - hapus yang kosong
        $filters = array_filter($filters, function($value) {
            return !empty($value) && $value !== '';
        });
        
        // Debug: log filter parameters
        log_message('debug', 'Filter parameters: ' . json_encode($filters));
        
        // Check if inventory view exists, create if not
        $this->_ensure_inventory_view();
        
        // Get filtered data from model
        $inventory = $this->Inventory_model->get_filtered_inventory($filters);
        
        $response = array(
            'success' => true,
            'inventory' => $inventory,
            'count' => count($inventory),
            'filters_applied' => $filters
        );
        
        // Debug response
        log_message('debug', 'Filter response count: ' . count($inventory));
        
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

// Method untuk memastikan view inventory exists
private function _ensure_inventory_view()
{
    // Check if view exists
    $query = $this->db->query("SHOW TABLES LIKE 'v_inventory_status'");
    
    if ($query->num_rows() == 0) {
        // Create the view
        $this->Inventory_model->create_inventory_view();
        log_message('info', 'Created v_inventory_status view');
    }
}

// Method untuk debugging filter (bisa dihapus setelah filter berfungsi)
public function debug_filter()
{
    $this->output->set_content_type('application/json');
    
    $filters = array(
        'type' => $this->input->get('type'),
        'status' => $this->input->get('status'), 
        'alert' => $this->input->get('alert'),
        'search' => $this->input->get('search')
    );
    
    $debug_data = $this->Inventory_model->debug_filter_data($filters);
    
    $this->output->set_output(json_encode($debug_data));
}

// Method untuk reset dan rebuild inventory view
public function rebuild_inventory_view()
{
    $this->output->set_content_type('application/json');
    
    try {
        // Drop existing view
        $this->db->query("DROP VIEW IF EXISTS v_inventory_status");
        
        // Recreate view
        $result = $this->Inventory_model->create_inventory_view();
        
        if ($result) {
            $response = array(
                'success' => true,
                'message' => 'Inventory view berhasil di-rebuild'
            );
            
            // Log activity
            $this->Admin_model->log_activity(
                $this->session->userdata('user_id'),
                'Inventory view di-rebuild',
                'system',
                null
            );
        } else {
            $response = array(
                'success' => false,
                'message' => 'Gagal rebuild inventory view'
            );
        }
        
    } catch (Exception $e) {
        log_message('error', 'Error rebuilding inventory view: ' . $e->getMessage());
        $response = array(
            'success' => false,
            'message' => 'Error: ' . $e->getMessage()
        );
    }
    
    $this->output->set_output(json_encode($response));
}

// Perbaikan method kelola untuk memastikan fullwidth layout
public function kelola()
{
    // Set fullwidth layout untuk halaman ini
    $data['fullwidth'] = true;
    $data['title'] = 'Kelola Inventory';
    
    try {
        // Ensure view exists
        $this->_ensure_inventory_view();
        
        $data['inventory'] = $this->Inventory_model->get_all_inventory();
        $data['stats'] = $this->Inventory_model->get_inventory_statistics();
    } catch (Exception $e) {
        log_message('error', 'Error getting inventory data: ' . $e->getMessage());
        $data['inventory'] = array();
        $data['stats'] = $this->_get_default_stats();
    }
    
    // Log activity
    $this->Admin_model->log_activity(
        $this->session->userdata('user_id'),
        'Mengakses halaman kelola inventory',
        'system',
        null
    );
    
    // Load view dengan fullwidth
    $this->load->view('template/header', $data);
    $this->load->view('template/sidebar', $data);
    $this->_load_fullwidth_view('admin/inventory_management', $data);
    $this->load->view('template/footer');
}

}