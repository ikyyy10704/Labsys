<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Sample_data extends CI_Controller {

    public function __construct()
    {
        parent::__construct();
        
        // Check if user is logged in and has petugas_lab role
        if (!$this->session->userdata('logged_in') || $this->session->userdata('role') !== 'petugas_lab') {
            $this->session->set_flashdata('error', 'Akses ditolak. Anda tidak memiliki izin untuk mengakses halaman ini.');
            redirect('auth/login');
        }
        
        $this->load->model(['User_model', 'Sampel_model', 'Sampel_model']);
        $this->load->library(['form_validation']);
        $this->load->helper(['form', 'url', 'date']);
    }

    /**
     * Sample Data / Specimen Tracking - Enhanced View
     */
    public function index()
    {
        $data['title'] = 'Data Sampel / Pelacakan Spesimen';
        
        // Pagination setup
        $limit = 10;
        $page = $this->input->get('page') ?: 1;
        $offset = ($page - 1) * $limit;
        
        // Get filters from URL
        $filters = array(
            'status' => $this->input->get('status') ?: 'progress',
            'date_from' => $this->input->get('date_from'),
            'date_to' => $this->input->get('date_to'),
            'jenis_pemeriksaan' => $this->input->get('jenis_pemeriksaan'),
            'petugas_id' => $this->input->get('petugas_id'),
            'search' => $this->input->get('search')
        );
        
        try {
            // Get paginated data
            $data['samples'] = $this->Sampel_model->get_samples_data_enhanced($filters, $limit, $offset);
            $data['total_samples'] = $this->Sampel_model->count_samples_data($filters);
            
            // Add latest timeline status to each sample
            foreach ($data['samples'] as &$sample) {
                $sample['latest_status'] = $this->Sampel_model->get_latest_timeline_status($sample['pemeriksaan_id']);
                
                // Process sub_pemeriksaan labels if exists
                if (!empty($sample['sub_pemeriksaan'])) {
                    $sub_array = json_decode($sample['sub_pemeriksaan'], true);
                    if (is_array($sub_array)) {
                        $sub_labels = array();
                        foreach ($sub_array as $sub_key) {
                            $sub_labels[] = $this->getSubPemeriksaanLabel($sub_key, $sample['jenis_pemeriksaan']);
                        }
                        $sample['sub_pemeriksaan_labels'] = $sub_labels;
                    }
                }
            }
            
            // Pagination info
            $data['current_page'] = $page;
            $data['total_pages'] = ceil($data['total_samples'] / $limit);
            $data['has_prev'] = $page > 1;
            $data['has_next'] = $page < $data['total_pages'];
            
            // Get options for filters
            $data['examination_types'] = $this->Sampel_model->get_examination_type_options();
            $data['petugas_list'] = $this->Sampel_model->get_all_petugas_lab();
            $data['status_options'] = array(
                'progress' => 'Sedang Diproses',
                'selesai' => 'Selesai',
                'cancelled' => 'Dibatalkan'
            );
            
        } catch (Exception $e) {
            log_message('error', 'Error getting sample data: ' . $e->getMessage());
            $data['samples'] = array();
            $data['total_samples'] = 0;
            $data['current_page'] = 1;
            $data['total_pages'] = 0;
            $data['has_prev'] = false;
            $data['has_next'] = false;
            $data['examination_types'] = array();
            $data['petugas_list'] = array();
            $data['status_options'] = array();
        }
        
        $data['filters'] = $filters;
        
        $this->load->view('template/header', $data);
        $this->load->view('template/sidebar', $data);
        $this->load->view('laboratorium/sample_data', $data);
        $this->load->view('template/footer');
    }

    /**
     * Update sample status
     */
    public function update_sample_status($examination_id)
    {
        if ($this->input->method() === 'post') {
            $this->form_validation->set_rules('status', 'Status', 'required|in_list[progress,selesai,cancelled]');
            $this->form_validation->set_rules('keterangan', 'Keterangan', 'required');
            
            if ($this->form_validation->run() === TRUE) {
                $status = $this->input->post('status');
                $keterangan = $this->input->post('keterangan');
                
                try {
                    $petugas_id = $this->Sampel_model->get_petugas_id_by_user_id($this->session->userdata('user_id'));
                    
                    // Update status
                    if ($this->Sampel_model->update_sample_status($examination_id, $status, $keterangan)) {
                        // Add timeline entry
                        $status_label = array(
                            'progress' => 'Status Diperbarui',
                            'selesai' => 'Pemeriksaan Selesai',
                            'cancelled' => 'Pemeriksaan Dibatalkan'
                        );
                        
                        $this->Sampel_model->add_sample_timeline(
                            $examination_id,
                            $status_label[$status],
                            $keterangan,
                            $petugas_id
                        );
                        
                        $this->User_model->log_activity($this->session->userdata('user_id'), "Sample status updated to {$status}", 'pemeriksaan_lab', $examination_id);
                        echo json_encode(['success' => true, 'message' => 'Status sampel berhasil diperbarui']);
                    } else {
                        echo json_encode(['success' => false, 'message' => 'Gagal memperbarui status sampel']);
                    }
                } catch (Exception $e) {
                    log_message('error', 'Error updating sample status: ' . $e->getMessage());
                    echo json_encode(['success' => false, 'message' => 'Terjadi kesalahan saat memperbarui status']);
                }
            } else {
                echo json_encode(['success' => false, 'message' => validation_errors()]);
            }
        }
    }

    public function view_sample_timeline($examination_id)
    {
        // Validasi examination ID
        if (!is_numeric($examination_id)) {
            $this->session->set_flashdata('error', 'ID pemeriksaan tidak valid');
            redirect('sample_data');
        }

        try {
            $examination = $this->Sampel_model->get_examination_by_id($examination_id);
            
            if (!$examination) {
                $this->session->set_flashdata('error', 'Pemeriksaan tidak ditemukan');
                redirect('sample_data');
            }
            
            $data['title'] = 'Timeline Sampel: ' . $examination['nomor_pemeriksaan'];
            $data['examination'] = $examination;
            $data['timeline'] = $this->Sampel_model->get_sample_timeline($examination_id);
            
            // Get timeline statistics
            $data['timeline_stats'] = $this->_get_timeline_stats($examination_id);
            
            $this->load->view('template/header', $data);
            $this->load->view('template/sidebar', $data);
            $this->load->view('laboratorium/sample_timeline', $data);
            $this->load->view('template/footer');
            
        } catch (Exception $e) {
            log_message('error', 'Error viewing sample timeline: ' . $e->getMessage());
            $this->session->set_flashdata('error', 'Terjadi kesalahan saat memuat timeline');
            redirect('sample_data');
        }
    }

    /**
     * Get sample timeline data (AJAX)
     */
    public function get_sample_timeline_data($examination_id)
    {
        try {
            $examination = $this->Sampel_model->get_examination_by_id($examination_id);
            
            if (!$examination) {
                echo json_encode(['success' => false, 'message' => 'Pemeriksaan tidak ditemukan']);
                return;
            }
            
            $timeline = $this->Sampel_model->get_sample_timeline($examination_id);
            
            echo json_encode([
                'success' => true,
                'examination' => $examination,
                'timeline' => $timeline
            ]);
            
        } catch (Exception $e) {
            log_message('error', 'Error getting timeline data: ' . $e->getMessage());
            echo json_encode(['success' => false, 'message' => 'Terjadi kesalahan saat memuat timeline']);
        }
    }

    /**
     * Get Timeline Data (AJAX)
     */
    public function get_timeline_data($examination_id)
    {
        try {
            $timeline = $this->Sampel_model->get_sample_timeline($examination_id);
            $stats = $this->_get_timeline_stats($examination_id);
            
            echo json_encode([
                'success' => true,
                'timeline' => $timeline,
                'stats' => $stats
            ]);
            
        } catch (Exception $e) {
            log_message('error', 'Error getting timeline data: ' . $e->getMessage());
            echo json_encode(['success' => false, 'message' => 'Gagal memuat data timeline']);
        }
    }

    /**
     * Add Timeline Entry
     */
    public function add_timeline_entry($examination_id)
    {
        // Hanya terima POST request
        if ($this->input->method() !== 'post') {
            show_404();
            return;
        }
        
        // Validasi input
        $this->form_validation->set_rules('status', 'Status', 'required|max_length[100]');
        $this->form_validation->set_rules('keterangan', 'Keterangan', 'required|max_length[500]');
        $this->form_validation->set_rules('tanggal_update', 'Tanggal Update', 'valid_date');
        
        if ($this->form_validation->run() === FALSE) {
            echo json_encode([
                'success' => false, 
                'message' => strip_tags(validation_errors())
            ]);
            return;
        }
        
        try {
            // Validasi examination exists
            $examination = $this->Sampel_model->get_examination_by_id($examination_id);
            if (!$examination) {
                echo json_encode(['success' => false, 'message' => 'Pemeriksaan tidak ditemukan']);
                return;
            }
            
            // Get petugas ID
            $petugas_id = $this->Sampel_model->get_petugas_id_by_user_id($this->session->userdata('user_id'));
            if (!$petugas_id) {
                echo json_encode(['success' => false, 'message' => 'User tidak terdaftar sebagai petugas lab']);
                return;
            }
            
            // Prepare data
            $status = $this->input->post('status');
            $keterangan = $this->input->post('keterangan');
            $tanggal_update = $this->input->post('tanggal_update');
            
            // Jika tanggal tidak diisi, gunakan waktu sekarang
            if (empty($tanggal_update)) {
                $tanggal_update = date('Y-m-d H:i:s');
            } else {
                // Validasi format tanggal
                $datetime = DateTime::createFromFormat('Y-m-d\TH:i', $tanggal_update);
                if ($datetime) {
                    $tanggal_update = $datetime->format('Y-m-d H:i:s');
                } else {
                    $tanggal_update = date('Y-m-d H:i:s');
                }
            }
            
            // Insert timeline entry
            $timeline_data = array(
                'pemeriksaan_id' => $examination_id,
                'status' => $status,
                'keterangan' => $keterangan,
                'petugas_id' => $petugas_id,
                'tanggal_update' => $tanggal_update
            );
            
            if ($this->db->insert('timeline_progres', $timeline_data)) {
                // Log activity
                $this->User_model->log_activity(
                    $this->session->userdata('user_id'), 
                    'Timeline entry added: ' . $status, 
                    'timeline_progres', 
                    $examination_id
                );
                
                echo json_encode([
                    'success' => true, 
                    'message' => 'Timeline berhasil ditambahkan',
                    'timeline_id' => $this->db->insert_id()
                ]);
            } else {
                echo json_encode(['success' => false, 'message' => 'Gagal menambahkan timeline ke database']);
            }
            
        } catch (Exception $e) {
            log_message('error', 'Error adding timeline entry: ' . $e->getMessage());
            echo json_encode(['success' => false, 'message' => 'Terjadi kesalahan sistem saat menambah timeline']);
        }
    }

    /**
     * Update Timeline Entry
     */
    public function update_timeline_entry($timeline_id)
    {
        if ($this->input->method() !== 'post') {
            show_404();
            return;
        }
        
        $this->form_validation->set_rules('status', 'Status', 'required|max_length[100]');
        $this->form_validation->set_rules('keterangan', 'Keterangan', 'required|max_length[500]');
        
        if ($this->form_validation->run() === FALSE) {
            echo json_encode(['success' => false, 'message' => strip_tags(validation_errors())]);
            return;
        }
        
        try {
            // Cek apakah timeline entry exists
            $this->db->where('timeline_id', $timeline_id);
            $timeline = $this->db->get('timeline_progres')->row_array();
            
            if (!$timeline) {
                echo json_encode(['success' => false, 'message' => 'Entry timeline tidak ditemukan']);
                return;
            }
            
            // Cek permission - hanya petugas yang membuat yang bisa edit
            $current_petugas_id = $this->Sampel_model->get_petugas_id_by_user_id($this->session->userdata('user_id'));
            if ($timeline['petugas_id'] != $current_petugas_id) {
                echo json_encode(['success' => false, 'message' => 'Anda tidak memiliki izin untuk mengedit entry ini']);
                return;
            }
            
            // Update data
            $update_data = array(
                'status' => $this->input->post('status'),
                'keterangan' => $this->input->post('keterangan')
            );
            
            $this->db->where('timeline_id', $timeline_id);
            if ($this->db->update('timeline_progres', $update_data)) {
                $this->User_model->log_activity(
                    $this->session->userdata('user_id'), 
                    'Timeline entry updated', 
                    'timeline_progres', 
                    $timeline_id
                );
                
                echo json_encode(['success' => true, 'message' => 'Timeline berhasil diperbarui']);
            } else {
                echo json_encode(['success' => false, 'message' => 'Gagal memperbarui timeline']);
            }
            
        } catch (Exception $e) {
            log_message('error', 'Error updating timeline entry: ' . $e->getMessage());
            echo json_encode(['success' => false, 'message' => 'Terjadi kesalahan saat memperbarui timeline']);
        }
    }

    /**
     * Delete Timeline Entry
     */
    public function delete_timeline_entry($timeline_id)
    {
        if ($this->input->method() !== 'post') {
            show_404();
            return;
        }
        
        try {
            // Cek apakah timeline entry exists
            $this->db->where('timeline_id', $timeline_id);
            $timeline = $this->db->get('timeline_progres')->row_array();
            
            if (!$timeline) {
                echo json_encode(['success' => false, 'message' => 'Entry timeline tidak ditemukan']);
                return;
            }
            
            // Cek permission - hanya petugas yang membuat yang bisa hapus
            $current_petugas_id = $this->Sampel_model->get_petugas_id_by_user_id($this->session->userdata('user_id'));
            if ($timeline['petugas_id'] != $current_petugas_id) {
                echo json_encode(['success' => false, 'message' => 'Anda tidak memiliki izin untuk menghapus entry ini']);
                return;
            }
            
            // Jangan hapus jika ini adalah satu-satunya entry
            $this->db->where('pemeriksaan_id', $timeline['pemeriksaan_id']);
            $total_entries = $this->db->count_all_results('timeline_progres');
            
            if ($total_entries <= 1) {
                echo json_encode(['success' => false, 'message' => 'Tidak dapat menghapus entry terakhir dalam timeline']);
                return;
            }
            
            // Delete entry
            $this->db->where('timeline_id', $timeline_id);
            if ($this->db->delete('timeline_progres')) {
                $this->User_model->log_activity(
                    $this->session->userdata('user_id'), 
                    'Timeline entry deleted', 
                    'timeline_progres', 
                    $timeline_id
                );
                
                echo json_encode(['success' => true, 'message' => 'Entry timeline berhasil dihapus']);
            } else {
                echo json_encode(['success' => false, 'message' => 'Gagal menghapus entry timeline']);
            }
            
        } catch (Exception $e) {
            log_message('error', 'Error deleting timeline entry: ' . $e->getMessage());
            echo json_encode(['success' => false, 'message' => 'Terjadi kesalahan saat menghapus timeline']);
        }
    }

    public function bulk_add_timeline()
    {
        if ($this->input->method() !== 'post') {
            show_404();
            return;
        }
        
        $examination_ids = $this->input->post('examination_ids');
        $status = $this->input->post('status');
        $keterangan = $this->input->post('keterangan');
        
        if (empty($examination_ids) || empty($status) || empty($keterangan)) {
            echo json_encode(['success' => false, 'message' => 'Data tidak lengkap']);
            return;
        }
        
        try {
            $petugas_id = $this->Sampel_model->get_petugas_id_by_user_id($this->session->userdata('user_id'));
            $success_count = 0;
            $error_count = 0;
            
            foreach ($examination_ids as $examination_id) {
                $timeline_data = array(
                    'pemeriksaan_id' => $examination_id,
                    'status' => $status,
                    'keterangan' => $keterangan,
                    'petugas_id' => $petugas_id,
                    'tanggal_update' => date('Y-m-d H:i:s')
                );
                
                if ($this->db->insert('timeline_progres', $timeline_data)) {
                    $success_count++;
                } else {
                    $error_count++;
                }
            }
            
            if ($success_count > 0) {
                $this->User_model->log_activity(
                    $this->session->userdata('user_id'), 
                    "Bulk timeline added: {$success_count} entries", 
                    'timeline_progres', 
                    null
                );
            }
            
            echo json_encode([
                'success' => true,
                'message' => "Berhasil menambahkan {$success_count} timeline" . ($error_count > 0 ? ", {$error_count} gagal" : ""),
                'success_count' => $success_count,
                'error_count' => $error_count
            ]);
            
        } catch (Exception $e) {
            log_message('error', 'Error bulk adding timeline: ' . $e->getMessage());
            echo json_encode(['success' => false, 'message' => 'Terjadi kesalahan saat menambah timeline']);
        }
    }

    /**
     * Get Timeline Templates (preset status)
     */
    public function get_timeline_templates()
    {
        $templates = array(
            'incoming' => array(
                array('status' => 'Sampel Diterima', 'keterangan' => 'Sampel diterima dalam kondisi baik dan siap untuk diproses'),
                array('status' => 'Verifikasi Identitas', 'keterangan' => 'Identitas pasien dan sampel telah diverifikasi sesuai'),
                array('status' => 'Registrasi Sampel', 'keterangan' => 'Sampel telah diregistrasi ke dalam sistem laboratorium')
            ),
            'processing' => array(
                array('status' => 'Preparasi Sampel', 'keterangan' => 'Sampel sedang dipreparasi untuk analisis'),
                array('status' => 'Quality Control', 'keterangan' => 'Menjalankan quality control instrumen dan reagen'),
                array('status' => 'Analisis Dimulai', 'keterangan' => 'Proses analisis laboratorium telah dimulai'),
                array('status' => 'Analisis Berlangsung', 'keterangan' => 'Analisis sedang berlangsung menggunakan metode standar'),
                array('status' => 'Review Hasil', 'keterangan' => 'Hasil analisis sedang direview oleh petugas senior')
            ),
            'completion' => array(
                array('status' => 'Analisis Selesai', 'keterangan' => 'Analisis laboratorium telah selesai dilakukan'),
                array('status' => 'Validasi Hasil', 'keterangan' => 'Hasil telah divalidasi dan memenuhi standar kualitas'),
                array('status' => 'Hasil Siap', 'keterangan' => 'Hasil pemeriksaan siap untuk diserahkan atau dikirim'),
                array('status' => 'Hasil Diserahkan', 'keterangan' => 'Hasil pemeriksaan telah diserahkan kepada pasien/dokter')
            ),
            'issues' => array(
                array('status' => 'Sampel Hemolisis', 'keterangan' => 'Sampel mengalami hemolisis, perlu pengambilan ulang'),
                array('status' => 'Sampel Lipemik', 'keterangan' => 'Sampel lipemik, mungkin mempengaruhi hasil'),
                array('status' => 'Volume Tidak Cukup', 'keterangan' => 'Volume sampel tidak mencukupi untuk analisis'),
                array('status' => 'Instrumen Error', 'keterangan' => 'Terjadi error pada instrumen, sedang diperbaiki'),
                array('status' => 'Perlu Pengulangan', 'keterangan' => 'Hasil perlu diulang karena hasil tidak konsisten')
            )
        );
        
        echo json_encode(['success' => true, 'templates' => $templates]);
    }

    /**
     * Get examination detail for modal (AJAX)
     */
    public function get_examination_detail($examination_id)
    {
        try {
            $examination = $this->Sampel_model->get_examination_by_id($examination_id);
            
            if (!$examination) {
                echo json_encode(['success' => false, 'message' => 'Pemeriksaan tidak ditemukan']);
                return;
            }
            
            // Add priority level calculation
            $hours_waiting = 0;
            if ($examination['tanggal_pemeriksaan']) {
                $hours_waiting = round((time() - strtotime($examination['tanggal_pemeriksaan'])) / 3600, 1);
            }
            $examination['hours_waiting'] = $hours_waiting;
            $examination['priority_level'] = $this->_calculate_priority_level($hours_waiting);
            
            echo json_encode([
                'success' => true,
                'examination' => $examination
            ]);
            
        } catch (Exception $e) {
            log_message('error', 'Error getting examination detail: ' . $e->getMessage());
            echo json_encode(['success' => false, 'message' => 'Terjadi kesalahan saat memuat data']);
        }
    }

    private function _calculate_priority_level($hours_waiting)
    {
        if ($hours_waiting >= 48) return 'urgent';
        if ($hours_waiting >= 24) return 'high';
        return 'normal';
    }

    private function _get_timeline_stats($examination_id)
    {
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
        
        // Time span
        if ($result['first_entry'] && $result['last_entry']) {
            $first = new DateTime($result['first_entry']);
            $last = new DateTime($result['last_entry']);
            $diff = $first->diff($last);
            $stats['time_span_hours'] = ($diff->days * 24) + $diff->h + ($diff->i / 60);
        } else {
            $stats['time_span_hours'] = 0;
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
public function get_examination_data_multiple($examination_id)
{
    if ($this->input->method() !== 'post') {
        echo json_encode(['success' => false, 'message' => 'Invalid request method']);
        return;
    }
    
    try {
        // Get examination dengan details
        $examination = $this->Sampel_model->get_examination_by_id($examination_id);
        
        if (!$examination) {
            echo json_encode(['success' => false, 'message' => 'Pemeriksaan tidak ditemukan']);
            return;
        }
        
        // Get existing results untuk semua jenis pemeriksaan
        $existing_results = $this->Sampel_model->get_existing_results_multiple(
            $examination_id, 
            $examination['examination_details']
        );
        
        echo json_encode([
            'success' => true,
            'examination' => $examination,
            'examination_details' => $examination['examination_details'],
            'existing_results' => $existing_results
        ]);
        
    } catch (Exception $e) {
        log_message('error', 'Error getting examination data: ' . $e->getMessage());
        echo json_encode(['success' => false, 'message' => 'Terjadi kesalahan saat memuat data']);
    }
}
// Di Sample_data.php controller
public function get_examination_data($examination_id)
{
    // ... existing code ...
    
    $examination = $this->Sampel_model->get_examination_by_id($examination_id);
    
    // AUTO-DETECT: apakah multi atau single
    if (!empty($examination['examination_details']) && count($examination['examination_details']) > 1) {
        // Return multi examination data
        $existing_results = $this->Sampel_model->get_existing_results_multiple(
            $examination_id, 
            $examination['examination_details']
        );
        
        echo json_encode([
            'success' => true,
            'is_multiple' => true,
            'examination' => $examination,
            'examination_details' => $examination['examination_details'],
            'existing_results' => $existing_results
        ]);
    } else {
        // Return single examination data
        $existing_results = $this->Sampel_model->get_existing_results_single($examination_id);
        
        echo json_encode([
            'success' => true,
            'is_multiple' => false,
            'examination' => $examination,
            'existing_results' => $existing_results
        ]);
    }
}

public function save_examination_results()
{
    // PENTING: Set header JSON di awal untuk mencegah HTML error
    header('Content-Type: application/json');
    
    // Log semua POST data untuk debugging
    log_message('debug', 'Save results POST data: ' . print_r($_POST, true));
    
    if ($this->input->method() !== 'post') {
        echo json_encode(['success' => false, 'message' => 'Invalid request method']);
        exit;
    }
    
    $examination_id = $this->input->post('examination_id');
    $result_type = $this->input->post('result_type');
    
    if (!$examination_id || !$result_type) {
        echo json_encode(['success' => false, 'message' => 'Data tidak lengkap: examination_id atau result_type kosong']);
        exit;
    }
    
    try {
        // Verifikasi examination exists
        $examination = $this->Sampel_model->get_examination_by_id($examination_id);
        if (!$examination) {
            echo json_encode(['success' => false, 'message' => 'Pemeriksaan tidak ditemukan']);
            exit;
        }
        
        // Check user access
        $petugas_id = $this->Sampel_model->get_petugas_id_by_user_id($this->session->userdata('user_id'));
        if (!$petugas_id) {
            echo json_encode(['success' => false, 'message' => 'User tidak terdaftar sebagai petugas lab']);
            exit;
        }
        
        // Hanya check petugas_id jika sudah ada assignment
        if ($examination['petugas_id'] && $examination['petugas_id'] != $petugas_id) {
            echo json_encode(['success' => false, 'message' => 'Akses ditolak - Anda bukan petugas yang ditugaskan']);
            exit;
        }
        
        // Prepare base data
        $data = array(
            'pemeriksaan_id' => $examination_id,
            'created_at' => wib_now()
        );
        
        $success = false;
        $error_detail = '';
        
        // Process based on result type
        switch (strtolower($result_type)) {
                case 'kimia_darah':
                $data = array_merge($data, $this->_get_kimia_darah_data());
                $success = $this->Sampel_model->save_or_update_kimia_darah_results($examination_id, $data);
                break;
                
            case 'hematologi':
                $data = array_merge($data, $this->_get_hematologi_data()); // Tambah parameter
                $success = $this->Sampel_model->save_or_update_hematologi_results($examination_id, $data);
                break;
                
            case 'urinologi':
                $data = array_merge($data, $this->_get_urinologi_data()); // Tambah parameter
                $success = $this->Sampel_model->save_or_update_urinologi_results($examination_id, $data);
        break;
                
            case 'serologi':
                $data = array_merge($data, $this->_get_serologi_data());
                $success = $this->Sampel_model->save_or_update_serologi_results($examination_id, $data);
                break;
                
            case 'tbc':
                $data = array_merge($data, $this->_get_tbc_data());
                $success = $this->Sampel_model->save_or_update_tbc_results($examination_id, $data);
                break;
                
            case 'ims':
                $data = array_merge($data, $this->_get_ims_data());
                $success = $this->Sampel_model->save_or_update_ims_results($examination_id, $data);
                break;
                
                
            default:
                echo json_encode(['success' => false, 'message' => 'Jenis pemeriksaan tidak valid: ' . $result_type]);
                exit;
        }
        
        // Check database error
        if ($this->db->error()['code'] != 0) {
            $db_error = $this->db->error();
            log_message('error', 'Database error saving results: ' . print_r($db_error, true));
            $error_detail = $db_error['message'];
        }
        
        if ($success) {
            // Add timeline entry
            try {
                $this->Sampel_model->add_sample_timeline(
                    $examination_id,
                    'Hasil Diinput',
                    'Hasil pemeriksaan ' . $result_type . ' telah diinput dan siap untuk divalidasi',
                    $petugas_id
                );
            } catch (Exception $timeline_error) {
                log_message('error', 'Error adding timeline: ' . $timeline_error->getMessage());
                // Timeline error tidak akan menggagalkan penyimpanan hasil
            }
            
            $this->User_model->log_activity(
                $this->session->userdata('user_id'), 
                "Lab results saved: {$result_type}", 
                'pemeriksaan_lab', 
                $examination_id
            );
            
            echo json_encode([
                'success' => true, 
                'message' => 'Hasil berhasil disimpan',
                'examination_id' => $examination_id
            ]);
        } else {
            echo json_encode([
                'success' => false, 
                'message' => 'Gagal menyimpan hasil ke database' . ($error_detail ? ': ' . $error_detail : '')
            ]);
        }
        
    } catch (Exception $e) {
        log_message('error', 'Exception in save_examination_results: ' . $e->getMessage());
        log_message('error', 'Stack trace: ' . $e->getTraceAsString());
        
        echo json_encode([
            'success' => false, 
            'message' => 'Terjadi kesalahan sistem: ' . $e->getMessage(),
            'error_type' => get_class($e)
        ]);
    }
    
    exit; 
}
private function _get_kimia_darah_data($prefix = '')
{
    $data = array(
        'gula_darah_sewaktu' => $this->input->post($prefix . 'gula_darah_sewaktu') ?: null,
        'gula_darah_puasa' => $this->input->post($prefix . 'gula_darah_puasa') ?: null,
        'gula_darah_2jam_pp' => $this->input->post($prefix . 'gula_darah_2jam_pp') ?: null,
        'cholesterol_total' => $this->input->post($prefix . 'cholesterol_total') ?: null,
        'cholesterol_hdl' => $this->input->post($prefix . 'cholesterol_hdl') ?: null,
        'cholesterol_ldl' => $this->input->post($prefix . 'cholesterol_ldl') ?: null,
        'trigliserida' => $this->input->post($prefix . 'trigliserida') ?: null,
        'asam_urat' => $this->input->post($prefix . 'asam_urat') ?: null,
        'ureum' => $this->input->post($prefix . 'ureum') ?: null,
        'creatinin' => $this->input->post($prefix . 'creatinin') ?: null,
        'sgpt' => $this->input->post($prefix . 'sgpt') ?: null,
        'sgot' => $this->input->post($prefix . 'sgot') ?: null
    );
    
    log_message('debug', "Kimia Darah data (prefix: '{$prefix}'): " . json_encode($data));
    return $data;
}

private function _get_hematologi_data($prefix = '')
{
    $data = array(
        'hemoglobin' => $this->input->post($prefix . 'hemoglobin') ?: null,
        'hematokrit' => $this->input->post($prefix . 'hematokrit') ?: null,
        'laju_endap_darah' => $this->input->post($prefix . 'laju_endap_darah') ?: null,
        'clotting_time' => $this->input->post($prefix . 'clotting_time') ?: null,
        'bleeding_time' => $this->input->post($prefix . 'bleeding_time') ?: null,
        'golongan_darah' => $this->input->post($prefix . 'golongan_darah') ?: null,
        'rhesus' => $this->input->post($prefix . 'rhesus') ?: null,
        'malaria' => $this->input->post($prefix . 'malaria') ?: null,
        'leukosit' => $this->input->post($prefix . 'leukosit') ?: null,
        'trombosit' => $this->input->post($prefix . 'trombosit') ?: null,
        'eritrosit' => $this->input->post($prefix . 'eritrosit') ?: null,
        'mcv' => $this->input->post($prefix . 'mcv') ?: null,
        'mch' => $this->input->post($prefix . 'mch') ?: null,
        'mchc' => $this->input->post($prefix . 'mchc') ?: null,
        'eosinofil' => $this->input->post($prefix . 'eosinofil') ?: null,
        'basofil' => $this->input->post($prefix . 'basofil') ?: null,
        'neutrofil' => $this->input->post($prefix . 'neutrofil') ?: null,
        'limfosit' => $this->input->post($prefix . 'limfosit') ?: null,
        'monosit' => $this->input->post($prefix . 'monosit') ?: null
    );
    
    log_message('debug', "Hematologi data (prefix: '{$prefix}'): " . json_encode($data));
    return $data;
}

private function _get_urinologi_data($prefix = '')
{
    $data = array(
        'makroskopis' => $this->input->post($prefix . 'makroskopis') ?: null,
        'mikroskopis' => $this->input->post($prefix . 'mikroskopis') ?: null,
        'kimia_ph' => $this->input->post($prefix . 'kimia_ph') ?: null,
        'protein_regular' => $this->input->post($prefix . 'protein_regular') ?: null,
        'protein' => $this->input->post($prefix . 'protein') ?: null,
        'tes_kehamilan' => $this->input->post($prefix . 'tes_kehamilan') ?: null,
        'berat_jenis' => $this->input->post($prefix . 'berat_jenis') ?: null,
        'glukosa' => $this->input->post($prefix . 'glukosa') ?: null,
        'keton' => $this->input->post($prefix . 'keton') ?: null,
        'bilirubin' => $this->input->post($prefix . 'bilirubin') ?: null,
        'urobilinogen' => $this->input->post($prefix . 'urobilinogen') ?: null
    );
    
    log_message('debug', "Urinologi data (prefix: '{$prefix}'): " . json_encode($data));
    return $data;
}

private function _get_serologi_data($prefix = '')
{
    $data = array(
        'rdt_antigen' => $this->input->post($prefix . 'rdt_antigen') ?: null,
        'widal' => $this->input->post($prefix . 'widal') ?: null,
        'hbsag' => $this->input->post($prefix . 'hbsag') ?: null,
        'ns1' => $this->input->post($prefix . 'ns1') ?: null,
        'hiv' => $this->input->post($prefix . 'hiv') ?: null
    );
    
    log_message('debug', "Serologi data (prefix: '{$prefix}'): " . json_encode($data));
    return $data;
}

private function _get_tbc_data($prefix = '')
{
    $data = array(
        'dahak' => $this->input->post($prefix . 'dahak') ?: null,
        'tcm' => $this->input->post($prefix . 'tcm') ?: null
    );
    
    log_message('debug', "TBC data (prefix: '{$prefix}'): " . json_encode($data));
    return $data;
}

private function _get_ims_data($prefix = '')
{
    $data = array(
        'sifilis' => $this->input->post($prefix . 'sifilis') ?: null,
        'duh_tubuh' => $this->input->post($prefix . 'duh_tubuh') ?: null
    );
    
    log_message('debug', "IMS data (prefix: '{$prefix}'): " . json_encode($data));
    return $data;
}

public function save_examination_results_multiple()
{
    // Start output buffering immediately to catch any stray output
    ob_start();
    
    // Set error handler to catch all PHP errors
    set_error_handler(function($errno, $errstr, $errfile, $errline) {
        throw new ErrorException($errstr, 0, $errno, $errfile, $errline);
    });
    
    // Register shutdown function to catch fatal errors
    register_shutdown_function(function() {
        $error = error_get_last();
        if ($error !== null && in_array($error['type'], [E_ERROR, E_PARSE, E_CORE_ERROR, E_COMPILE_ERROR])) {
            // Clean any buffered output
            while (ob_get_level() > 0) {
                ob_end_clean();
            }
            header('Content-Type: application/json');
            echo json_encode([
                'success' => false,
                'message' => 'Fatal Error: ' . $error['message'],
                'file' => basename($error['file']),
                'line' => $error['line']
            ]);
        }
    });
    
    log_message('debug', 'save_examination_results_multiple called');
    log_message('debug', 'POST data: ' . print_r($_POST, true));
    
    // Clean any buffered output and set JSON header
    ob_end_clean();
    header('Content-Type: application/json');
    
    // Disable any error output that might corrupt JSON
    @ini_set('display_errors', 0);
    @ini_set('log_errors', 1);
    
    try {
        // Basic validation
        if ($this->input->method() !== 'post') {
            throw new Exception('Invalid request method');
        }
        
        $examination_id = $this->input->post('examination_id');
        $result_types_json = $this->input->post('result_types');
        
        if (!$examination_id) {
            throw new Exception('examination_id is required');
        }
        
        if (!$result_types_json) {
            throw new Exception('result_types is required');
        }
        
        // Decode result_types
        $result_types = json_decode($result_types_json, true);
        if (json_last_error() !== JSON_ERROR_NONE) {
            throw new Exception('Invalid JSON in result_types: ' . json_last_error_msg());
        }
        
        if (!is_array($result_types) || empty($result_types)) {
            throw new Exception('result_types must be non-empty array');
        }
        
        // Get petugas
        $petugas_id = $this->Sampel_model->get_petugas_id_by_user_id($this->session->userdata('user_id'));
        if (!$petugas_id) {
            throw new Exception('User not registered as petugas lab');
        }
        
        // DEBUG: Log the result_types being processed
        log_message('debug', 'Processing result_types: ' . print_r($result_types, true));
        log_message('debug', 'Examination ID: ' . $examination_id);
        log_message('debug', 'Petugas ID: ' . $petugas_id);
        
        $saved_count = 0;
        $errors = array();
        
        // Process each type
        foreach ($result_types as $result_type) {
            try {
                $prefix = $result_type . '_';
                $type_data = array();
                
                // Get data based on type
                log_message('debug', "Processing type: '{$result_type}' with prefix: '{$prefix}'");
                
                switch (strtolower($result_type)) {
                    case 'kimia_darah':
                        $type_data = $this->_get_kimia_darah_data($prefix);
                        break;
                    case 'hematologi':
                        $type_data = $this->_get_hematologi_data($prefix);
                        break;
                    case 'urinologi':
                        $type_data = $this->_get_urinologi_data($prefix);
                        break;
                    case 'serologi':
                    case 'serologi_imunologi':
                        $type_data = $this->_get_serologi_data($prefix);
                        break;
                    case 'tbc':
                        $type_data = $this->_get_tbc_data($prefix);
                        break;
                    case 'ims':
                        $type_data = $this->_get_ims_data($prefix);
                        break;
                    default:
                        // Skip unknown result types
                        log_message('debug', "Unknown result type skipped: '{$result_type}'");
                        continue 2;
                }
                
                // Check if has data
                $has_data = false;
                foreach ($type_data as $value) {
                    if ($value !== null && $value !== '') {
                        $has_data = true;
                        break;
                    }
                }
                
                if (!$has_data) {
                    continue;
                }
                
                // Prepare full data
                $full_data = array_merge(
                    array(
                        'pemeriksaan_id' => $examination_id,
                        'created_at' => date('Y-m-d H:i:s')
                    ),
                    $type_data
                );
                
                // Save based on type
                $success = false;
                log_message('debug', "Saving type: '{$result_type}' with data: " . json_encode($full_data));
                
                switch (strtolower($result_type)) {
                    case 'kimia_darah':
                        $success = $this->Sampel_model->save_or_update_kimia_darah_results($examination_id, $full_data);
                        break;
                    case 'hematologi':
                        $success = $this->Sampel_model->save_or_update_hematologi_results($examination_id, $full_data);
                        break;
                    case 'urinologi':
                        $success = $this->Sampel_model->save_or_update_urinologi_results($examination_id, $full_data);
                        break;
                    case 'serologi':
                    case 'serologi_imunologi':
                        $success = $this->Sampel_model->save_or_update_serologi_results($examination_id, $full_data);
                        break;
                    case 'tbc':
                        $success = $this->Sampel_model->save_or_update_tbc_results($examination_id, $full_data);
                        break;
                    case 'ims':
                        $success = $this->Sampel_model->save_or_update_ims_results($examination_id, $full_data);
                        break;
                }
                
                if ($success) {
                    $saved_count++;
                } else {
                    $db_error = $this->db->error();
                    $errors[] = $result_type . ': ' . ($db_error['message'] ?: 'Unknown error');
                }
                
            } catch (Exception $e) {
                $errors[] = $result_type . ': ' . $e->getMessage();
            }
        }
        
        // Timeline
        if ($saved_count > 0) {
            try {
                $this->Sampel_model->add_sample_timeline(
                    $examination_id,
                    'Hasil Diinput',
                    "Hasil pemeriksaan telah diinput ({$saved_count} jenis)",
                    $petugas_id
                );
            } catch (Exception $e) {
                // Ignore timeline errors
            }
        }
        
        // Response
        if ($saved_count > 0) {
            $response = array(
                'success' => true,
                'message' => "Berhasil menyimpan {$saved_count} jenis pemeriksaan",
                'saved_count' => $saved_count
            );
            if (!empty($errors)) {
                $response['errors'] = $errors;
            }
        } else {
            $response = array(
                'success' => false,
                'message' => 'Tidak ada data yang berhasil disimpan',
                'errors' => $errors
            );
        }
        
        echo json_encode($response);
        
    } catch (ErrorException $e) {
        // Catch PHP errors converted to exceptions
        log_message('error', 'ErrorException in save_examination_results_multiple: ' . $e->getMessage());
        log_message('error', 'Stack trace: ' . $e->getTraceAsString());
        
        echo json_encode(array(
            'success' => false,
            'message' => 'PHP Error: ' . $e->getMessage(),
            'file' => basename($e->getFile()),
            'line' => $e->getLine(),
            'type' => 'ErrorException'
        ));
    } catch (Exception $e) {
        // Always return JSON even for errors
        log_message('error', 'Exception in save_examination_results_multiple: ' . $e->getMessage());
        log_message('error', 'Stack trace: ' . $e->getTraceAsString());
        
        echo json_encode(array(
            'success' => false,
            'message' => $e->getMessage(),
            'file' => basename($e->getFile()),
            'line' => $e->getLine(),
            'type' => get_class($e)
        ));
    } finally {
        // Restore error handler
        restore_error_handler();
    }
    
    // Exit to prevent any further output
    exit;
}
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
        $sample['kondisi_details'] = $this->decode_sample_conditions($sample['kondisi_sampel'], $sample['jenis_sampel']);
        $sample['kondisi_ids'] = json_decode($sample['kondisi_sampel'], true) ?: array();
    }
    
    return $samples;
}

/**
 * Get single sample with decoded conditions
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
        $sample['kondisi_details'] = $this->decode_sample_conditions($sample['kondisi_sampel'], $sample['jenis_sampel']);
        $sample['kondisi_ids'] = json_decode($sample['kondisi_sampel'], true) ?: array();
    }
    
    return $sample;
}

/**
 * Decode kondisi_sampel JSON dan ambil detail dari master
 */
private function decode_sample_conditions($kondisi_json, $jenis_sampel) {
    if (empty($kondisi_json)) {
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

public function get_master_kondisi_by_jenis($jenis_sampel) {
    $this->db->select('*');
    $this->db->from('master_kondisi_sampel');
    $this->db->where('jenis_sampel', $jenis_sampel);
    $this->db->where('is_active', 1);
    $this->db->order_by('urutan', 'ASC');
    
    return $this->db->get()->result_array();
}

/**
 * Get jenis sampel options
 */
public function get_jenis_sampel_options() {
    return array(
        'whole_blood' => 'Whole Blood',
        'serum' => 'Serum',
        'plasma' => 'Plasma',
        'urin' => 'Urin',
        'feses' => 'Feses',
        'sputum' => 'Sputum'
    );
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



public function update_sample_conditions()
{
    if ($this->input->method() !== 'post') {
        show_404();
        return;
    }
    
    $this->form_validation->set_rules('sampel_id', 'Sampel ID', 'required|numeric');
    $this->form_validation->set_rules('kondisi_ids[]', 'Kondisi', 'required');
    
    if ($this->form_validation->run() === FALSE) {
        echo json_encode(['success' => false, 'message' => strip_tags(validation_errors())]);
        return;
    }
    
    try {
        $sampel_id = $this->input->post('sampel_id');
        $kondisi_ids = $this->input->post('kondisi_ids');
        $catatan_kondisi = $this->input->post('catatan_kondisi');
        
        if (!is_array($kondisi_ids)) {
            $kondisi_ids = array($kondisi_ids);
        }
        
        if ($this->Sampel_model->update_sample_conditions($sampel_id, $kondisi_ids, $catatan_kondisi)) {
            $this->User_model->log_activity(
                $this->session->userdata('user_id'),
                'Sample conditions updated',
                'pemeriksaan_sampel',
                $sampel_id
            );
            
            echo json_encode(['success' => true, 'message' => 'Kondisi sampel berhasil diperbarui']);
        } else {
            echo json_encode(['success' => false, 'message' => 'Gagal memperbarui kondisi sampel']);
        }
        
    } catch (Exception $e) {
        log_message('error', 'Error updating sample conditions: ' . $e->getMessage());
        echo json_encode(['success' => false, 'message' => 'Terjadi kesalahan']);
    }
}
public function update_sample_diterima($sampel_id)
{
    if ($this->input->method() !== 'post') {
        show_404();
        return;
    }
    
    try {
        $sample = $this->Sampel_model->get_sample_by_id($sampel_id);
        
        if (!$sample) {
            echo json_encode(['success' => false, 'message' => 'Sampel tidak ditemukan']);
            return;
        }
        
        if ($sample['status_sampel'] !== 'sudah_diambil') {
            echo json_encode(['success' => false, 'message' => 'Sampel belum diambil']);
            return;
        }
        
        $petugas_id = $this->Sampel_model->get_petugas_id_by_user_id($this->session->userdata('user_id'));
        
        if ($this->Sampel_model->update_sample_diterima($sampel_id, $petugas_id)) {
            // Add timeline
            $this->Sampel_model->add_sample_timeline(
                $sample['pemeriksaan_id'],
                'Sampel Diterima',
                'Sampel ' . $this->Sampel_model->get_jenis_sampel_options()[$sample['jenis_sampel']] . ' telah diterima dan lolos evaluasi',
                $petugas_id
            );
            
            $this->User_model->log_activity(
                $this->session->userdata('user_id'),
                'Sample accepted',
                'pemeriksaan_sampel',
                $sampel_id
            );
            
            echo json_encode(['success' => true, 'message' => 'Sampel berhasil diterima']);
        } else {
            echo json_encode(['success' => false, 'message' => 'Gagal menerima sampel']);
        }
        
    } catch (Exception $e) {
        log_message('error', 'Error accepting sample: ' . $e->getMessage());
        echo json_encode(['success' => false, 'message' => 'Terjadi kesalahan']);
    }
}

/**
 * Update sample status - Evaluasi (Ditolak)
 */
public function update_sample_ditolak($sampel_id)
{
    if ($this->input->method() !== 'post') {
        show_404();
        return;
    }
    
    $this->form_validation->set_rules('catatan_penolakan', 'Alasan Penolakan', 'required|max_length[500]');
    
    if ($this->form_validation->run() === FALSE) {
        echo json_encode(['success' => false, 'message' => strip_tags(validation_errors())]);
        return;
    }
    
    try {
        $sample = $this->Sampel_model->get_sample_by_id($sampel_id);
        
        if (!$sample) {
            echo json_encode(['success' => false, 'message' => 'Sampel tidak ditemukan']);
            return;
        }
        
        if ($sample['status_sampel'] !== 'sudah_diambil') {
            echo json_encode(['success' => false, 'message' => 'Sampel belum diambil']);
            return;
        }
        
        $petugas_id = $this->Sampel_model->get_petugas_id_by_user_id($this->session->userdata('user_id'));
        $catatan_penolakan = $this->input->post('catatan_penolakan');
        
        if ($this->Sampel_model->update_sample_ditolak($sampel_id, $petugas_id, $catatan_penolakan)) {
            // Add timeline
            $this->Sampel_model->add_sample_timeline(
                $sample['pemeriksaan_id'],
                'Sampel Ditolak',
                'Sampel ' . $this->Sampel_model->get_jenis_sampel_options()[$sample['jenis_sampel']] . ' ditolak: ' . $catatan_penolakan,
                $petugas_id
            );
            
            $this->User_model->log_activity(
                $this->session->userdata('user_id'),
                'Sample rejected: ' . $catatan_penolakan,
                'pemeriksaan_sampel',
                $sampel_id
            );
            
            echo json_encode(['success' => true, 'message' => 'Sampel ditolak']);
        } else {
            echo json_encode(['success' => false, 'message' => 'Gagal menolak sampel']);
        }
        
    } catch (Exception $e) {
        log_message('error', 'Error rejecting sample: ' . $e->getMessage());
        echo json_encode(['success' => false, 'message' => 'Terjadi kesalahan']);
    }
}

/**
 * Add sample condition
 */
public function add_sample_condition()
{
    if ($this->input->method() !== 'post') {
        show_404();
        return;
    }
    
    $this->form_validation->set_rules('sampel_id', 'Sampel ID', 'required|numeric');
    $this->form_validation->set_rules('kondisi_id', 'Kondisi ID', 'required|numeric');
    $this->form_validation->set_rules('catatan', 'Catatan', 'max_length[500]');
    
    if ($this->form_validation->run() === FALSE) {
        echo json_encode(['success' => false, 'message' => strip_tags(validation_errors())]);
        return;
    }
    
    try {
        $sampel_id = $this->input->post('sampel_id');
        $kondisi_id = $this->input->post('kondisi_id');
        $catatan = $this->input->post('catatan');
        
        if ($this->Sampel_model->add_sample_condition($sampel_id, $kondisi_id, $catatan)) {
            $this->User_model->log_activity(
                $this->session->userdata('user_id'),
                'Sample condition added',
                'kondisi_sampel',
                $sampel_id
            );
            
            echo json_encode(['success' => true, 'message' => 'Kondisi sampel berhasil ditambahkan']);
        } else {
            echo json_encode(['success' => false, 'message' => 'Gagal menambahkan kondisi sampel']);
        }
        
    } catch (Exception $e) {
        log_message('error', 'Error adding sample condition: ' . $e->getMessage());
        echo json_encode(['success' => false, 'message' => 'Terjadi kesalahan']);
    }
}

/**
 * Remove sample condition
 */
public function remove_sample_condition($kondisi_sampel_id)
{
    if ($this->input->method() !== 'post') {
        show_404();
        return;
    }
    
    try {
        if ($this->Sampel_model->remove_sample_condition($kondisi_sampel_id)) {
            $this->User_model->log_activity(
                $this->session->userdata('user_id'),
                'Sample condition removed',
                'kondisi_sampel',
                $kondisi_sampel_id
            );
            
            echo json_encode(['success' => true, 'message' => 'Kondisi sampel berhasil dihapus']);
        } else {
            echo json_encode(['success' => false, 'message' => 'Gagal menghapus kondisi sampel']);
        }
        
    } catch (Exception $e) {
        log_message('error', 'Error removing sample condition: ' . $e->getMessage());
        echo json_encode(['success' => false, 'message' => 'Terjadi kesalahan']);
    }
}

public function delete_sample($sampel_id)
{
    if ($this->input->method() !== 'post') {
        show_404();
        return;
    }
    
    try {
        $sample = $this->Sampel_model->get_sample_by_id($sampel_id);
        
        if (!$sample) {
            echo json_encode(['success' => false, 'message' => 'Sampel tidak ditemukan']);
            return;
        }
        
        // Only allow deletion if status is 'belum_diambil'
        if ($sample['status_sampel'] !== 'belum_diambil') {
            echo json_encode(['success' => false, 'message' => 'Hanya sampel dengan status "Belum Diambil" yang dapat dihapus']);
            return;
        }
        
        if ($this->Sampel_model->delete_sample($sampel_id)) {
            $this->User_model->log_activity(
                $this->session->userdata('user_id'),
                'Sample deleted',
                'pemeriksaan_sampel',
                $sampel_id
            );
            
            echo json_encode(['success' => true, 'message' => 'Sampel berhasil dihapus']);
        } else {
            echo json_encode(['success' => false, 'message' => 'Gagal menghapus sampel']);
        }
        
    } catch (Exception $e) {
        log_message('error', 'Error deleting sample: ' . $e->getMessage());
        echo json_encode(['success' => false, 'message' => 'Terjadi kesalahan']);
    }
}
// Hapus function global di baris ~1304 dan ganti dengan:
private function getSubPemeriksaanLabel($subKey, $jenisType) {
    $labelMaps = array(
        'kimia darah' => array(
            'gula_darah_sewaktu' => 'Gula Darah Sewaktu',
            'gula_darah_puasa' => 'Gula Darah Puasa',
            'gula_darah_2jam_pp' => 'Gula Darah 2 Jam PP',
            'cholesterol_total' => 'Kolesterol Total',
            'cholesterol_hdl' => 'Kolesterol HDL',
            'cholesterol_ldl' => 'Kolesterol LDL',
            'trigliserida' => 'Trigliserida',
            'asam_urat' => 'Asam Urat',
            'ureum' => 'Ureum',
            'creatinin' => 'Kreatinin',
            'sgpt' => 'SGPT',
            'sgot' => 'SGOT'
        ),
        'hematologi' => array(
            'paket_darah_rutin' => 'Paket Darah Rutin',
            'hitung_jenis_leukosit' => 'Hitung Jenis Leukosit',
            'laju_endap_darah' => 'Laju Endap Darah',
            'golongan_darah' => 'Golongan Darah & Rhesus',
            'hemostasis' => 'Hemostasis (CT/BT)',
            'malaria' => 'Malaria'
        ),
        'urinologi' => array(
            'urin_rutin' => 'Urin Rutin',
            'protein' => 'Protein Urin (Kuantitatif)',
            'tes_kehamilan' => 'Tes Kehamilan'
        ),
        'serologi' => array(
            'rdt_antigen' => 'RDT Antigen',
            'widal' => 'Widal',
            'hbsag' => 'HBsAg',
            'ns1' => 'NS1 (Dengue)',
            'hiv' => 'HIV'
        ),
        'serologi imunologi' => array(
            'rdt_antigen' => 'RDT Antigen',
            'widal' => 'Widal',
            'hbsag' => 'HBsAg',
            'ns1' => 'NS1 (Dengue)',
            'hiv' => 'HIV'
        ),
        'tbc' => array(
            'dahak' => 'Dahak (BTA)',
            'tcm' => 'TCM (GeneXpert)'
        ),
        'ims' => array(
            'sifilis' => 'Sifilis',
            'duh_tubuh' => 'Duh Tubuh'
        )
    );
    
    $jenisLower = strtolower($jenisType);
    if (isset($labelMaps[$jenisLower]) && isset($labelMaps[$jenisLower][$subKey])) {
        return $labelMaps[$jenisLower][$subKey];
    }
    
    return ucwords(str_replace('_', ' ', $subKey));
}
public function bulk_add_conditions()
{
    if ($this->input->method() !== 'post') {
        show_404();
        return;
    }
    
    $sampel_id = $this->input->post('sampel_id');
    $kondisi_ids = $this->input->post('kondisi_ids');
    $catatan = $this->input->post('catatan');
    
    if (empty($sampel_id) || empty($kondisi_ids)) {
        echo json_encode(['success' => false, 'message' => 'Data tidak lengkap']);
        return;
    }
    
    try {
        $success_count = $this->Sampel_model->bulk_add_sample_conditions($sampel_id, $kondisi_ids, $catatan);
        
        if ($success_count > 0) {
            $this->User_model->log_activity(
                $this->session->userdata('user_id'),
                "Bulk conditions added: {$success_count} conditions",
                'kondisi_sampel',
                $sampel_id
            );
            
            echo json_encode([
                'success' => true,
                'message' => "Berhasil menambahkan {$success_count} kondisi",
                'added_count' => $success_count
            ]);
        } else {
            echo json_encode(['success' => false, 'message' => 'Gagal menambahkan kondisi']);
        }
        
    } catch (Exception $e) {
        log_message('error', 'Error bulk adding conditions: ' . $e->getMessage());
        echo json_encode(['success' => false, 'message' => 'Terjadi kesalahan']);
    }
}
 public function manage_samples($examination_id)
    {
        if (!is_numeric($examination_id)) {
            $this->session->set_flashdata('error', 'ID pemeriksaan tidak valid');
            redirect('sample_data');
        }
        
        try {
            $examination = $this->Sampel_model->get_examination_by_id($examination_id);
            
            if (!$examination) {
                $this->session->set_flashdata('error', 'Pemeriksaan tidak ditemukan');
                redirect('sample_data');
            }
            
            $data['title'] = 'Manajemen Sampel: ' . $examination['nomor_pemeriksaan'];
            $data['examination'] = $examination;
            $data['samples'] = $this->Sampel_model->get_examination_samples($examination_id);
            $data['samples_summary'] = $this->Sampel_model->get_samples_summary($examination_id);
            $data['jenis_sampel_options'] = $this->Sampel_model->get_jenis_sampel_options();
            
            // Load views
            $this->load->view('template/header', $data);
            $this->load->view('template/sidebar', $data);
            $this->load->view('laboratorium/manage_samples', $data);
            $this->load->view('template/footer');
            
        } catch (Exception $e) {
            log_message('error', 'Error managing samples: ' . $e->getMessage());
            $this->session->set_flashdata('error', 'Terjadi kesalahan saat memuat data sampel');
            redirect('sample_data');
        }
    }

    /**
     * Get sample data for modal (AJAX)
     */
    public function get_sample_data($sampel_id)
    {
        try {
            $sample = $this->Sampel_model->get_sample_by_id($sampel_id);
            
            if (!$sample) {
                echo json_encode(['success' => false, 'message' => 'Sampel tidak ditemukan']);
                return;
            }
            
            // Get available conditions for this jenis_sampel
            $available_conditions = $this->Sampel_model->get_master_kondisi_by_jenis($sample['jenis_sampel']);
            
            echo json_encode([
                'success' => true,
                'sample' => $sample,
                'available_conditions' => $available_conditions
            ]);
            
        } catch (Exception $e) {
            log_message('error', 'Error getting sample data: ' . $e->getMessage());
            echo json_encode(['success' => false, 'message' => 'Terjadi kesalahan saat memuat data']);
        }
    }

    /**
     * Create new sample
     */
    public function create_sample()
    {
        if ($this->input->method() !== 'post') {
            show_404();
            return;
        }
        
        $this->form_validation->set_rules('pemeriksaan_id', 'Pemeriksaan ID', 'required|numeric');
        $this->form_validation->set_rules('jenis_sampel', 'Jenis Sampel', 'required|in_list[whole_blood,serum,plasma,urin,feses,sputum,lain]');
        $this->form_validation->set_rules('keterangan_sampel', 'Keterangan', 'max_length[500]');
        
        if ($this->form_validation->run() === FALSE) {
            echo json_encode(['success' => false, 'message' => strip_tags(validation_errors())]);
            return;
        }
        
        try {
            // Verify examination exists
            $examination = $this->Sampel_model->get_examination_by_id($this->input->post('pemeriksaan_id'));
            if (!$examination) {
                echo json_encode(['success' => false, 'message' => 'Pemeriksaan tidak ditemukan']);
                return;
            }
            
            $jenis_sampel = $this->input->post('jenis_sampel');
            $keterangan = $this->input->post('keterangan_sampel');
            $custom_name = $this->input->post('jenis_sampel_custom');
            
            // If type is 'lain', prepend custom name to description
            if ($jenis_sampel === 'lain' && !empty($custom_name)) {
                $keterangan = "Jenis: " . $custom_name . ($keterangan ? " | " . $keterangan : "");
            }
            
            $data = array(
                'pemeriksaan_id' => $this->input->post('pemeriksaan_id'),
                'jenis_sampel' => $jenis_sampel,
                'keterangan_sampel' => $keterangan
            );
            
            $sampel_id = $this->Sampel_model->create_sample($data);
            
            if ($sampel_id) {
                // Add timeline
                $petugas_id = $this->Sampel_model->get_petugas_id_by_user_id($this->session->userdata('user_id'));
                $this->Sampel_model->add_sample_timeline(
                    $examination['pemeriksaan_id'],
                    'Sampel Ditambahkan',
                    'Sampel ' . $this->Sampel_model->get_jenis_sampel_options()[$data['jenis_sampel']] . ' telah ditambahkan',
                    $petugas_id
                );
                
                $this->User_model->log_activity(
                    $this->session->userdata('user_id'),
                    'Sample created',
                    'pemeriksaan_sampel',
                    $sampel_id
                );
                
                echo json_encode([
                    'success' => true,
                    'message' => 'Sampel berhasil ditambahkan',
                    'sampel_id' => $sampel_id
                ]);
            } else {
                echo json_encode(['success' => false, 'message' => 'Gagal menambahkan sampel']);
            }
            
        } catch (Exception $e) {
            log_message('error', 'Error creating sample: ' . $e->getMessage());
            echo json_encode(['success' => false, 'message' => 'Terjadi kesalahan saat menambahkan sampel']);
        }
    }

    /**
     * Update sample status - Pengambilan
     */
    public function update_sample_pengambilan($sampel_id)
    {
        if ($this->input->method() !== 'post') {
            show_404();
            return;
        }
        
        try {
            $sample = $this->Sampel_model->get_sample_by_id($sampel_id);
            
            if (!$sample) {
                echo json_encode(['success' => false, 'message' => 'Sampel tidak ditemukan']);
                return;
            }
            
            if ($sample['status_sampel'] !== 'belum_diambil') {
                echo json_encode(['success' => false, 'message' => 'Sampel sudah diambil sebelumnya']);
                return;
            }
            
            $petugas_id = $this->Sampel_model->get_petugas_id_by_user_id($this->session->userdata('user_id'));
            
            if ($this->Sampel_model->update_sample_pengambilan($sampel_id, $petugas_id)) {
                // Add timeline
                $this->Sampel_model->add_sample_timeline(
                    $sample['pemeriksaan_id'],
                    'Sampel Diambil',
                    'Sampel ' . $this->Sampel_model->get_jenis_sampel_options()[$sample['jenis_sampel']] . ' telah diambil',
                    $petugas_id
                );
                
                $this->User_model->log_activity(
                    $this->session->userdata('user_id'),
                    'Sample collected',
                    'pemeriksaan_sampel',
                    $sampel_id
                );
                
                echo json_encode(['success' => true, 'message' => 'Status sampel berhasil diperbarui']);
            } else {
                echo json_encode(['success' => false, 'message' => 'Gagal memperbarui status sampel']);
            }
            
        } catch (Exception $e) {
            log_message('error', 'Error updating sample pengambilan: ' . $e->getMessage());
            echo json_encode(['success' => false, 'message' => 'Terjadi kesalahan']);
        }
    }

    /**
     * Update sample - Diterima dengan kondisi
     */
    public function update_sample_diterima_with_conditions($sampel_id)
    {
        if ($this->input->method() !== 'post') {
            show_404();
            return;
        }
        
        try {
            $sample = $this->Sampel_model->get_sample_by_id($sampel_id);
            
            if (!$sample) {
                echo json_encode(['success' => false, 'message' => 'Sampel tidak ditemukan']);
                return;
            }
            
            if ($sample['status_sampel'] !== 'sudah_diambil') {
                echo json_encode(['success' => false, 'message' => 'Sampel belum diambil']);
                return;
            }
            
            $petugas_id = $this->Sampel_model->get_petugas_id_by_user_id($this->session->userdata('user_id'));
            $kondisi_ids = $this->input->post('kondisi_ids') ?: array();
            $catatan_kondisi = $this->input->post('catatan_kondisi');
            
            // Ensure kondisi_ids is an array
            if (!is_array($kondisi_ids)) {
                $kondisi_ids = array($kondisi_ids);
            }
            
            if ($this->Sampel_model->update_sample_diterima($sampel_id, $petugas_id, $kondisi_ids, $catatan_kondisi)) {
                // Add timeline
                $kondisi_text = count($kondisi_ids) > 0 ? ' dengan ' . count($kondisi_ids) . ' kondisi tercatat' : '';
                $this->Sampel_model->add_sample_timeline(
                    $sample['pemeriksaan_id'],
                    'Sampel Diterima',
                    'Sampel ' . $this->Sampel_model->get_jenis_sampel_options()[$sample['jenis_sampel']] . ' telah diterima' . $kondisi_text,
                    $petugas_id
                );
                
                $this->User_model->log_activity(
                    $this->session->userdata('user_id'),
                    'Sample accepted with conditions',
                    'pemeriksaan_sampel',
                    $sampel_id
                );
                
                echo json_encode(['success' => true, 'message' => 'Sampel berhasil diterima']);
            } else {
                echo json_encode(['success' => false, 'message' => 'Gagal menerima sampel']);
            }
            
        } catch (Exception $e) {
            log_message('error', 'Error accepting sample: ' . $e->getMessage());
            echo json_encode(['success' => false, 'message' => 'Terjadi kesalahan']);
        }
    }

    /**
     * Update sample - Ditolak dengan kondisi
     */
    public function update_sample_ditolak_with_conditions($sampel_id)
    {
        if ($this->input->method() !== 'post') {
            show_404();
            return;
        }
        
        $this->form_validation->set_rules('catatan_penolakan', 'Alasan Penolakan', 'required|max_length[500]');
        
        if ($this->form_validation->run() === FALSE) {
            echo json_encode(['success' => false, 'message' => strip_tags(validation_errors())]);
            return;
        }
        
        try {
            $sample = $this->Sampel_model->get_sample_by_id($sampel_id);
            
            if (!$sample) {
                echo json_encode(['success' => false, 'message' => 'Sampel tidak ditemukan']);
                return;
            }
            
            if ($sample['status_sampel'] !== 'sudah_diambil') {
                echo json_encode(['success' => false, 'message' => 'Sampel belum diambil']);
                return;
            }
            
            $petugas_id = $this->Sampel_model->get_petugas_id_by_user_id($this->session->userdata('user_id'));
            $catatan_penolakan = $this->input->post('catatan_penolakan');
            $kondisi_ids = $this->input->post('kondisi_ids') ?: array();
            
            // Ensure kondisi_ids is an array
            if (!is_array($kondisi_ids)) {
                $kondisi_ids = array($kondisi_ids);
            }
            
            if ($this->Sampel_model->update_sample_ditolak($sampel_id, $petugas_id, $catatan_penolakan, $kondisi_ids)) {
                // Add timeline
                $this->Sampel_model->add_sample_timeline(
                    $sample['pemeriksaan_id'],
                    'Sampel Ditolak',
                    'Sampel ' . $this->Sampel_model->get_jenis_sampel_options()[$sample['jenis_sampel']] . ' ditolak: ' . $catatan_penolakan,
                    $petugas_id
                );
                
                $this->User_model->log_activity(
                    $this->session->userdata('user_id'),
                    'Sample rejected: ' . $catatan_penolakan,
                    'pemeriksaan_sampel',
                    $sampel_id
                );
                
                echo json_encode(['success' => true, 'message' => 'Sampel ditolak']);
            } else {
                echo json_encode(['success' => false, 'message' => 'Gagal menolak sampel']);
            }
            
        } catch (Exception $e) {
            log_message('error', 'Error rejecting sample: ' . $e->getMessage());
            echo json_encode(['success' => false, 'message' => 'Terjadi kesalahan']);
        }
    }

    /**
     * Get master kondisi by jenis sampel (AJAX)
     */
    public function get_master_kondisi($jenis_sampel)
    {
        try {
            $conditions = $this->Sampel_model->get_master_kondisi_by_jenis($jenis_sampel);
            
            echo json_encode([
                'success' => true,
                'conditions' => $conditions
            ]);
            
        } catch (Exception $e) {
            log_message('error', 'Error getting master kondisi: ' . $e->getMessage());
            echo json_encode(['success' => false, 'message' => 'Terjadi kesalahan']);
        }
    }
    public function get_result_details($examination_id)
{
    // CRITICAL: Set proper headers BEFORE any output
    header('Content-Type: application/json; charset=utf-8');
    
    // Disable any error output that might corrupt JSON
    ini_set('display_errors', 0);
    
    try {
        // Validate examination_id
        if (!is_numeric($examination_id)) {
            echo json_encode([
                'success' => false, 
                'message' => 'ID pemeriksaan tidak valid'
            ]);
            exit;
        }
        
        // Get examination with details
        $examination = $this->Laboratorium_model->get_examination_by_id($examination_id);
        
        if (!$examination) {
            echo json_encode([
                'success' => false, 
                'message' => 'Pemeriksaan tidak ditemukan'
            ]);
            exit;
        }
        
        // Check if multiple examinations
        $details = isset($examination['examination_details']) ? $examination['examination_details'] : array();
        $is_multiple = !empty($details) && count($details) > 1;
        
        if ($is_multiple) {
            // Get results untuk setiap jenis pemeriksaan
            $results = array();
            
            foreach ($details as $detail) {
                $jenis = $detail['jenis_pemeriksaan'];
                $jenis_results = $this->Laboratorium_model->get_existing_results($examination_id, $jenis);
                
                if ($jenis_results && !empty($jenis_results)) {
                    // Format hasil untuk ditampilkan
                    $formatted = $this->_format_results_for_display($jenis_results, $jenis);
                    if (!empty($formatted)) {
                        $results[$jenis] = $formatted;
                    }
                }
            }
            
            echo json_encode([
                'success' => true,
                'is_multiple' => true,
                'examination' => $examination,
                'results' => $results
            ], JSON_UNESCAPED_UNICODE);
            
        } else {
            // Single examination
            $jenis = $examination['jenis_pemeriksaan'];
            
            // Handle comma-separated jenis (legacy)
            if (strpos($jenis, ',') !== false) {
                $jenis = trim(explode(',', $jenis)[0]);
            }
            
            $results = $this->Laboratorium_model->get_existing_results($examination_id, $jenis);
            
            if (!$results || empty($results)) {
                echo json_encode([
                    'success' => false,
                    'message' => 'Hasil pemeriksaan belum tersedia'
                ]);
                exit;
            }
            
            // Format results
            $formatted_results = $this->_format_results_for_display($results, $jenis);
            
            echo json_encode([
                'success' => true,
                'is_multiple' => false,
                'examination' => $examination,
                'results' => $formatted_results
            ], JSON_UNESCAPED_UNICODE);
        }
        
    } catch (Exception $e) {
        log_message('error', 'Error getting result details: ' . $e->getMessage());
        log_message('error', 'Stack trace: ' . $e->getTraceAsString());
        
        echo json_encode([
            'success' => false, 
            'message' => 'Gagal memuat detail hasil: ' . $e->getMessage()
        ]);
    }
    
    exit; // CRITICAL: Prevent any further output
}

/**
 * Helper: Format results untuk display
 * PRIVATE METHOD
 */
private function _format_results_for_display($results, $jenis_pemeriksaan)
{
    $formatted = array();
    
    switch (strtolower($jenis_pemeriksaan)) {
        case 'kimia darah':
            $fields = array(
                'gula_darah_sewaktu' => 'Gula Darah Sewaktu',
                'gula_darah_puasa' => 'Gula Darah Puasa',
                'gula_darah_2jam_pp' => 'Gula Darah 2 Jam PP',
                'cholesterol_total' => 'Kolesterol Total',
                'cholesterol_hdl' => 'Kolesterol HDL',
                'cholesterol_ldl' => 'Kolesterol LDL',
                'trigliserida' => 'Trigliserida',
                'asam_urat' => 'Asam Urat',
                'ureum' => 'Ureum',
                'creatinin' => 'Kreatinin',
                'sgpt' => 'SGPT',
                'sgot' => 'SGOT'
            );
            break;
            
        case 'hematologi':
            $fields = array(
                'hemoglobin' => 'Hemoglobin',
                'hematokrit' => 'Hematokrit',
                'leukosit' => 'Leukosit',
                'trombosit' => 'Trombosit',
                'eritrosit' => 'Eritrosit',
                'mcv' => 'MCV',
                'mch' => 'MCH',
                'mchc' => 'MCHC',
                'neutrofil' => 'Neutrofil',
                'limfosit' => 'Limfosit',
                'monosit' => 'Monosit',
                'eosinofil' => 'Eosinofil',
                'basofil' => 'Basofil',
                'laju_endap_darah' => 'Laju Endap Darah',
                'golongan_darah' => 'Golongan Darah',
                'rhesus' => 'Rhesus',
                'clotting_time' => 'Clotting Time',
                'bleeding_time' => 'Bleeding Time',
                'malaria' => 'Malaria'
            );
            break;
            
        case 'urinologi':
            $fields = array(
                'makroskopis' => 'Makroskopis',
                'mikroskopis' => 'Mikroskopis',
                'berat_jenis' => 'Berat Jenis',
                'kimia_ph' => 'pH',
                'protein' => 'Protein',
                'glukosa' => 'Glukosa',
                'keton' => 'Keton',
                'bilirubin' => 'Bilirubin',
                'urobilinogen' => 'Urobilinogen',
                'tes_kehamilan' => 'Tes Kehamilan'
            );
            break;
            
        case 'serologi':
        case 'serologi imunologi':
            $fields = array(
                'rdt_antigen' => 'RDT Antigen',
                'widal' => 'Widal',
                'hbsag' => 'HBsAg',
                'ns1' => 'NS1 (Dengue)',
                'hiv' => 'HIV'
            );
            break;
            
        case 'tbc':
            $fields = array(
                'dahak' => 'Dahak (BTA)',
                'tcm' => 'TCM (GeneXpert)'
            );
            break;
            
        case 'ims':
            $fields = array(
                'sifilis' => 'Sifilis',
                'duh_tubuh' => 'Duh Tubuh'
            );
            break;
            
        default:
            $fields = array();
    }
    
    // Filter only filled values
    foreach ($fields as $key => $label) {
        if (isset($results[$key]) && $results[$key] !== null && $results[$key] !== '') {
            $formatted[$label] = $results[$key];
        }
    }
    
    return $formatted;
}
public function test_save_multiple()
{
    // Force JSON response
    header('Content-Type: application/json');
    ob_clean();
    
    echo json_encode([
        'success' => true,
        'message' => 'Test endpoint works',
        'post_data' => $_POST,
        'model_loaded' => isset($this->Sampel_model),
        'methods_exist' => [
            'save_kimia_darah' => method_exists($this->Sampel_model, 'save_or_update_kimia_darah_results'),
            'save_hematologi' => method_exists($this->Sampel_model, 'save_or_update_hematologi_results'),
            'save_urinologi' => method_exists($this->Sampel_model, 'save_or_update_urinologi_results'),
        ]
    ]);
    exit;
}
}