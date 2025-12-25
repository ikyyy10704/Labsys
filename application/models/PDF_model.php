<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class PDF_model extends CI_Model {

    public function __construct() {
        parent::__construct();
        $this->load->database();
    }

    // ==========================================
    // EXAMINATION FUNCTIONS
    // ==========================================

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
public function get_examination_results_cached($examination_id, $examination_type) {
    $cache_key = "exam_results_{$examination_id}_{$examination_type}";
    
    // Try to get from cache
    $cached = $this->cache->get($cache_key);
    if ($cached !== FALSE) {
        return $cached;
    }
    
    // Get from database
    $results = $this->get_examination_results($examination_id, $examination_type);
    
    // Save to cache for 1 hour
    if ($results) {
        $this->cache->save($cache_key, $results, 3600);
    }
    
    return $results;
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

    public function get_invoice_data_safe($examination_id) {
        try {
            return $this->get_invoice_data_by_examination($examination_id);
        } catch (Exception $e) {
            log_message('error', 'Error in get_invoice_data_safe: ' . $e->getMessage());
            return null;
        }
    }

    public function create_invoice_for_examination_safe($examination_id) {
        try {
            return $this->create_invoice_for_examination($examination_id);
        } catch (Exception $e) {
            log_message('error', 'Error in create_invoice_for_examination_safe: ' . $e->getMessage());
            return null;
        }
    }

    private function get_invoice_data_by_examination($examination_id) {
        try {
            $this->db->select('nomor_pemeriksaan');
            $this->db->where('pemeriksaan_id', $examination_id);
            $query = $this->db->get('pemeriksaan_lab');
            
            if ($query->num_rows() == 0) {
                return null;
            }
            
            $nomor_pemeriksaan = $query->row()->nomor_pemeriksaan;
            
            $this->db->select('*');
            $this->db->where('nomor_pemeriksaan', $nomor_pemeriksaan);
            $query = $this->db->get('v_invoice_cetak');
            
            return $query->num_rows() > 0 ? $query->row_array() : null;
            
        } catch (Exception $e) {
            log_message('error', 'Error getting invoice data: ' . $e->getMessage());
            return null;
        }
    }

    private function create_invoice_for_examination($examination_id) {
        try {
            $this->db->select('*');
            $this->db->where('pemeriksaan_id', $examination_id);
            $examination = $this->db->get('pemeriksaan_lab')->row_array();
            
            if (!$examination) {
                return false;
            }
            
            $existing_invoice = $this->get_invoice_data_by_examination($examination_id);
            if ($existing_invoice) {
                return $existing_invoice;
            }
            
            $this->db->select_max('invoice_id');
            $query = $this->db->get('invoice');
            $max_id = $query->row()->invoice_id ?: 0;
            $invoice_number = 'INV-' . date('Y') . '-' . str_pad($max_id + 1, 4, '0', STR_PAD_LEFT);
            
            $invoice_data = array(
                'pemeriksaan_id' => $examination_id,
                'nomor_invoice' => $invoice_number,
                'tanggal_invoice' => $examination['tanggal_pemeriksaan'],
                'jenis_pembayaran' => 'umum',
                'total_biaya' => $examination['biaya'] ?: 0,
                'status_pembayaran' => 'belum_bayar',
                'created_at' => date('Y-m-d H:i:s')
            );
            
            $this->db->insert('invoice', $invoice_data);
            
            if ($this->db->affected_rows() > 0) {
                return $this->get_invoice_data_by_examination($examination_id);
            }
            
            return false;
            
        } catch (Exception $e) {
            log_message('error', 'Error creating invoice: ' . $e->getMessage());
            return false;
        }
    }

    // ==========================================
    // LAB INFO & LOGO FUNCTIONS
    // ==========================================

    public function get_logo_info() {
        $logo_info = array(
            'logo_exists' => false,
            'logo_path' => '',
            'logo_url' => '',
            'logo_base64' => ''
        );
        
        $logo_extensions = array('png', 'jpg', 'jpeg', 'gif', 'svg');
        $logo_names = array('logo', 'lab_logo', 'labsys_logo');
        
        foreach ($logo_names as $name) {
            foreach ($logo_extensions as $ext) {
                $logo_path = FCPATH . 'assets/logo/' . $name . '.' . $ext;
                
                if (file_exists($logo_path)) {
                    $logo_info['logo_exists'] = true;
                    $logo_info['logo_path'] = $logo_path;
                    $logo_info['logo_url'] = base_url('assets/logo/' . $name . '.' . $ext);
                    
                    if (in_array($ext, array('png', 'jpg', 'jpeg', 'gif'))) {
                        $image_data = file_get_contents($logo_path);
                        $mime_type = 'image/' . ($ext == 'jpg' ? 'jpeg' : $ext);
                        $logo_info['logo_base64'] = 'data:' . $mime_type . ';base64,' . base64_encode($image_data);
                    }
                    
                    break 2;
                }
            }
        }
        
        return $logo_info;
    }

    public function get_lab_info() {
        try {
            $this->db->select('*');
            $this->db->where('lab_id', 1);
            $query = $this->db->get('lab');
            
            if ($query->num_rows() > 0) {
                return $query->row_array();
            }
            
            return array(
                'nama' => 'SISTEM INFORMASI LABORATORIUM',
                'alamat' => 'Jl. Tata Bumi No.3, Area Sawah, Banyuraden, Kec. Gamping, Kabupaten Sleman, DIY 55293',
                'telephone' => '(021) 123-4567',
                'email' => 'info@labsy.com'
            );
            
        } catch (Exception $e) {
            log_message('error', 'Error getting lab info: ' . $e->getMessage());
            return array(
                'nama' => 'SISTEM INFORMASI LABORATORIUM',
                'alamat' => 'Jl. Tata Bumi No.3, Area Sawah, Banyuraden, Kec. Gamping, Kabupaten Sleman, DIY 55293',
                'telephone' => '(021) 123-4567',
                'email' => 'info@labsy.com'
            );
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
    // Di PDF_model.php
public function get_examination_results($examination_id, $examination_type) {
    try {
        // Validasi input
        if (empty($examination_id) || !is_numeric($examination_id)) {
            return null;
        }
        
        $table_map = array(
            'kimia darah' => 'kimia_darah',
            'hematologi' => 'hematologi',
            'urinologi' => 'urinologi',
            'serologi' => 'serologi_imunologi',
            'serologi imunologi' => 'serologi_imunologi',
            'tbc' => 'tbc',
            'ims' => 'ims',
            'mls' => 'mls'
        );

        // Split types by comma if multiple
        $types = array_map('trim', explode(',', strtolower($examination_type)));
        $combined_results = array();
        $found_any = false;

        foreach ($types as $type) {
            if (!isset($table_map[$type])) {
                log_message('error', "Unknown examination type: {$type}"); // Changed from warning to error
                continue;
            }

            $table = $table_map[$type];
            $this->db->where('pemeriksaan_id', $examination_id);
            $query = $this->db->get($table);

            if ($query->num_rows() > 0) {
                $row = $query->row_array();
                // Merge results. Note: overlapping keys (like id, created_at) will be overwritten, 
                // but parameter keys should be unique across tables.
                if (empty($combined_results)) {
                    $combined_results = $row;
                } else {
                    $combined_results = array_merge($combined_results, $row);
                }
                $found_any = true;
            }
        }
        
        return $found_any ? $combined_results : null;
        
    } catch (Exception $e) {
        log_message('error', 'Error getting examination results: ' . $e->getMessage());
        return null;
    }
}
// Di PDF_model.php
public function check_results_completeness($examination_id, $examination_type) {
    $results = $this->get_examination_results($examination_id, $examination_type);
    
    if (!$results) {
        return array(
            'complete' => false,
            'missing_fields' => array(),
            'total_fields' => 0,
            'filled_fields' => 0
        );
    }
    
    $total = 0;
    $filled = 0;
    $missing = array();
    
    foreach ($results as $key => $value) {
        if ($key !== 'pemeriksaan_id' && $key !== 'created_at') {
            $total++;
            if ($value === null || $value === '') {
                $missing[] = $key;
            } else {
                $filled++;
            }
        }
    }
    
    return array(
        'complete' => (count($missing) === 0),
        'missing_fields' => $missing,
        'total_fields' => $total,
        'filled_fields' => $filled,
        'percentage' => $total > 0 ? round(($filled / $total) * 100, 2) : 0
    );
}
}