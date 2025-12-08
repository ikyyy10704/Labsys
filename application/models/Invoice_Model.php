<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Invoice_model extends CI_Model {

    public function __construct() {
        parent::__construct();
        $this->load->database();
    }

    /**
     * Hitung total biaya pemeriksaan berdasarkan hasil yang diinput
     * @param int $pemeriksaan_id
     * @return float Total biaya
     */
    public function calculate_examination_cost($pemeriksaan_id) {
        try {
            // Ambil data pemeriksaan
            $this->db->select('jenis_pemeriksaan');
            $this->db->where('pemeriksaan_id', $pemeriksaan_id);
            $examination = $this->db->get('pemeriksaan_lab')->row_array();
            
            if (!$examination) {
                return 0;
            }
            
            $total_cost = 0;
            $jenis = strtolower(trim($examination['jenis_pemeriksaan']));
            
            // Hitung biaya berdasarkan jenis pemeriksaan
            switch($jenis) {
                case 'tbc':
                    $total_cost = $this->_calculate_tbc_cost($pemeriksaan_id);
                    break;
                    
                case 'kimia darah':
                    $total_cost = $this->_calculate_kimia_darah_cost($pemeriksaan_id);
                    break;
                    
                case 'hematologi':
                    $total_cost = $this->_calculate_hematologi_cost($pemeriksaan_id);
                    break;
                    
                case 'urinologi':
                case 'urine':
                    $total_cost = $this->_calculate_urinologi_cost($pemeriksaan_id);
                    break;
                    
                case 'serologi':
                case 'serologi imunologi':
                    $total_cost = $this->_calculate_serologi_cost($pemeriksaan_id);
                    break;
                    
                case 'ims':
                    $total_cost = $this->_calculate_ims_cost($pemeriksaan_id);
                    break;
                    
                default:
                    $total_cost = 0;
                    break;
            }
            
            return $total_cost;
            
        } catch (Exception $e) {
            log_message('error', 'Error calculating examination cost: ' . $e->getMessage());
            return 0;
        }
    }

    /**
     * Hitung biaya TBC
     */
    private function _calculate_tbc_cost($pemeriksaan_id) {
        $this->db->where('pemeriksaan_id', $pemeriksaan_id);
        $tbc = $this->db->get('tbc')->row_array();
        
        if (!$tbc) return 0;
        
        $total = 0;
        
        // Hitung hanya kolom yang terisi
        if (!empty($tbc['dahak']) && $tbc['dahak'] !== null) {
            $total += (float)($tbc['harga_dahak'] ?? 30000);
        }
        
        if (!empty($tbc['tcm']) && $tbc['tcm'] !== null) {
            $total += (float)($tbc['harga_tcm'] ?? 450000);
        }
        
        return $total;
    }

    /**
     * Hitung biaya Kimia Darah
     */
    private function _calculate_kimia_darah_cost($pemeriksaan_id) {
        $this->db->where('pemeriksaan_id', $pemeriksaan_id);
        $kimia = $this->db->get('kimia_darah')->row_array();
        
        if (!$kimia) return 0;
        
        $total = 0;
        
        // Array mapping kolom hasil dengan harga
        $items = array(
            'gula_darah_sewaktu' => array('harga_gds', 15000),
            'gula_darah_puasa' => array('harga_gdp', 15000),
            'gula_darah_2jam_pp' => array('harga_gd2pp', 15000),
            'cholesterol_total' => array('harga_chol_total', 25000),
            'cholesterol_hdl' => array('harga_chol_hdl', 30000),
            'cholesterol_ldl' => array('harga_chol_ldl', 30000),
            'trigliserida' => array('harga_trigliserida', 25000),
            'asam_urat' => array('harga_asam_urat', 15000),
            'ureum' => array('harga_ureum', 30000),
            'creatinin' => array('harga_creatinin', 30000),
            'sgpt' => array('harga_sgpt', 30000),
            'sgot' => array('harga_sgot', 30000)
        );
        
        // Hitung hanya untuk kolom yang terisi
        foreach ($items as $field => $price_info) {
            if (!empty($kimia[$field]) && $kimia[$field] !== null) {
                $harga_field = $price_info[0];
                $default_price = $price_info[1];
                $total += (float)($kimia[$harga_field] ?? $default_price);
            }
        }
        
        return $total;
    }

    private function _calculate_serologi_cost($pemeriksaan_id) {
        $this->db->where('pemeriksaan_id', $pemeriksaan_id);
        $serologi = $this->db->get('serologi_imunologi')->row_array();
        
        if (!$serologi) return 0;
        
        $total = 0;
        
        // RDT Antigen
        if (!empty($serologi['rdt_antigen']) && $serologi['rdt_antigen'] !== null) {
            $total += (float)($serologi['harga_rdt_antigen'] ?? 75000);
        }
        
        // Widal
        if (!empty($serologi['widal']) && $serologi['widal'] !== null) {
            $total += (float)($serologi['harga_widal'] ?? 33000);
        }
        
        // HBsAg
        if (!empty($serologi['hbsag']) && $serologi['hbsag'] !== null) {
            $total += (float)($serologi['harga_hbsag'] ?? 35000);
        }
        
        // NS1
        if (!empty($serologi['ns1']) && $serologi['ns1'] !== null) {
            $total += (float)($serologi['harga_ns1'] ?? 100000);
        }
        
        // HIV
        if (!empty($serologi['hiv']) && $serologi['hiv'] !== null) {
            $total += (float)($serologi['harga_hiv'] ?? 125000);
        }
        
        return $total;
    }

    /**
     * Hitung biaya IMS
     */
    private function _calculate_ims_cost($pemeriksaan_id) {
        $this->db->where('pemeriksaan_id', $pemeriksaan_id);
        $ims = $this->db->get('ims')->row_array();
        
        if (!$ims) return 0;
        
        $total = 0;
        
        // Tes Sifilis
        if (!empty($ims['sifilis']) && $ims['sifilis'] !== null) {
            $total += (float)($ims['harga_sifilis'] ?? 100000);
        }
        
        // Tes Duh Tubuh
        if (!empty($ims['duh_tubuh']) && $ims['duh_tubuh'] !== null) {
            $total += (float)($ims['harga_duh_tubuh'] ?? 50000);
        }
        
        return $total;
    }

    /**
     * Buat atau update invoice dengan perhitungan otomatis
     */
    public function create_or_update_invoice($pemeriksaan_id) {
        try {
            $this->db->trans_start();
            
            // Hitung total biaya
            $total_biaya = $this->calculate_examination_cost($pemeriksaan_id);
            
            if ($total_biaya <= 0) {
                log_message('info', 'Total biaya 0 untuk pemeriksaan_id: ' . $pemeriksaan_id . ' - Mungkin hasil belum diinput');
                $this->db->trans_rollback();
                return false;
            }
            
            // Cek apakah invoice sudah ada
            $this->db->where('pemeriksaan_id', $pemeriksaan_id);
            $existing_invoice = $this->db->get('invoice')->row_array();
            
            if ($existing_invoice) {
                // Update invoice yang sudah ada
                $update_data = array(
                    'total_biaya' => $total_biaya
                );
                
                $this->db->where('invoice_id', $existing_invoice['invoice_id']);
                $this->db->update('invoice', $update_data);
                
                $invoice_id = $existing_invoice['invoice_id'];
                
                log_message('info', 'Invoice updated: ' . $existing_invoice['nomor_invoice'] . ' with new total: ' . $total_biaya);
                
            } else {
                // Buat invoice baru
                // Generate nomor invoice
                $this->db->select_max('invoice_id');
                $query = $this->db->get('invoice');
                $max_id = $query->row()->invoice_id ?: 0;
                $nomor_invoice = 'INV-' . date('Y') . '-' . str_pad($max_id + 1, 4, '0', STR_PAD_LEFT);
                
                // Ambil data pemeriksaan
                $this->db->where('pemeriksaan_id', $pemeriksaan_id);
                $pemeriksaan = $this->db->get('pemeriksaan_lab')->row_array();
                
                if (!$pemeriksaan) {
                    log_message('error', 'Pemeriksaan not found: ' . $pemeriksaan_id);
                    $this->db->trans_rollback();
                    return false;
                }
                
                $invoice_data = array(
                    'pemeriksaan_id' => $pemeriksaan_id,
                    'nomor_invoice' => $nomor_invoice,
                    'tanggal_invoice' => $pemeriksaan['tanggal_pemeriksaan'],
                    'jenis_pembayaran' => 'umum',
                    'total_biaya' => $total_biaya,
                    'status_pembayaran' => 'belum_bayar',
                    'created_at' => date('Y-m-d H:i:s')
                );
                
                $this->db->insert('invoice', $invoice_data);
                $invoice_id = $this->db->insert_id();
                
                log_message('info', 'New invoice created: ' . $nomor_invoice . ' with total: ' . $total_biaya);
            }
            
            // Update biaya di tabel pemeriksaan_lab
            $this->db->where('pemeriksaan_id', $pemeriksaan_id);
            $this->db->update('pemeriksaan_lab', array('biaya' => $total_biaya));
            
            $this->db->trans_complete();
            
            if ($this->db->trans_status() === FALSE) {
                log_message('error', 'Transaction failed for pemeriksaan_id: ' . $pemeriksaan_id);
                return false;
            }
            
            return $invoice_id;
            
        } catch (Exception $e) {
            $this->db->trans_rollback();
            log_message('error', 'Error creating/updating invoice: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Get invoice detail with calculated cost
     */
    public function get_invoice_with_details($invoice_id) {
        try {
            $this->db->select('
                i.*,
                p.nama as nama_pasien,
                p.nik,
                p.umur,
                p.alamat_domisili,
                p.telepon,
                pl.nomor_pemeriksaan,
                pl.jenis_pemeriksaan,
                pl.tanggal_pemeriksaan
            ');
            $this->db->from('invoice i');
            $this->db->join('pemeriksaan_lab pl', 'i.pemeriksaan_id = pl.pemeriksaan_id');
            $this->db->join('pasien p', 'pl.pasien_id = p.pasien_id');
            $this->db->where('i.invoice_id', $invoice_id);
            
            $invoice = $this->db->get()->row_array();
            
            if (!$invoice) return null;
            
            // Ambil detail biaya per item
            $invoice['cost_breakdown'] = $this->get_cost_breakdown($invoice['pemeriksaan_id']);
            
            return $invoice;
            
        } catch (Exception $e) {
            log_message('error', 'Error getting invoice details: ' . $e->getMessage());
            return null;
        }
    }

    /**
     * Get breakdown biaya per pemeriksaan (hanya item yang terisi)
     */
    public function get_cost_breakdown($pemeriksaan_id) {
        try {
            $breakdown = array();
            
            // Ambil jenis pemeriksaan
            $this->db->select('jenis_pemeriksaan');
            $this->db->where('pemeriksaan_id', $pemeriksaan_id);
            $examination = $this->db->get('pemeriksaan_lab')->row_array();
            
            if (!$examination) return $breakdown;
            
            $jenis = strtolower(trim($examination['jenis_pemeriksaan']));
            
            switch($jenis) {
                case 'tbc':
                    $breakdown = $this->_get_tbc_breakdown($pemeriksaan_id);
                    break;
                    
                case 'kimia darah':
                    $breakdown = $this->_get_kimia_darah_breakdown($pemeriksaan_id);
                    break;
                    
                case 'hematologi':
                    $breakdown = $this->_get_hematologi_breakdown($pemeriksaan_id);
                    break;
                    
                case 'urinologi':
                case 'urine':
                    $breakdown = $this->_get_urinologi_breakdown($pemeriksaan_id);
                    break;
                    
                case 'serologi':
                case 'serologi imunologi':
                    $breakdown = $this->_get_serologi_breakdown($pemeriksaan_id);
                    break;
                    
                case 'ims':
                    $breakdown = $this->_get_ims_breakdown($pemeriksaan_id);
                    break;
            }
            
            return $breakdown;
            
        } catch (Exception $e) {
            log_message('error', 'Error getting cost breakdown: ' . $e->getMessage());
            return array();
        }
    }

    // Private methods untuk breakdown - hanya tampilkan item yang terisi
    private function _get_tbc_breakdown($pemeriksaan_id) {
        $breakdown = array();
        $this->db->where('pemeriksaan_id', $pemeriksaan_id);
        $tbc = $this->db->get('tbc')->row_array();
        
        if (!$tbc) return $breakdown;
        
        if (!empty($tbc['dahak']) && $tbc['dahak'] !== null) {
            $breakdown[] = array(
                'item' => 'Tes Dahak BTA',
                'hasil' => $tbc['dahak'],
                'harga' => (float)($tbc['harga_dahak'] ?? 30000)
            );
        }
        
        if (!empty($tbc['tcm']) && $tbc['tcm'] !== null) {
            $breakdown[] = array(
                'item' => 'TCM (TB)',
                'hasil' => $tbc['tcm'],
                'harga' => (float)($tbc['harga_tcm'] ?? 450000)
            );
        }
        
        return $breakdown;
    }

    private function _get_kimia_darah_breakdown($pemeriksaan_id) {
        $breakdown = array();
        $this->db->where('pemeriksaan_id', $pemeriksaan_id);
        $kimia = $this->db->get('kimia_darah')->row_array();
        
        if (!$kimia) return $breakdown;
        
        $items = array(
            'gula_darah_sewaktu' => array('Gula Darah Sewaktu', 'harga_gds', 15000, 'mg/dL'),
            'gula_darah_puasa' => array('Gula Darah Puasa', 'harga_gdp', 15000, 'mg/dL'),
            'gula_darah_2jam_pp' => array('Gula Darah 2 Jam PP', 'harga_gd2pp', 15000, 'mg/dL'),
            'cholesterol_total' => array('Cholesterol Total', 'harga_chol_total', 25000, 'mg/dL'),
            'cholesterol_hdl' => array('Cholesterol HDL', 'harga_chol_hdl', 30000, 'mg/dL'),
            'cholesterol_ldl' => array('Cholesterol LDL', 'harga_chol_ldl', 30000, 'mg/dL'),
            'trigliserida' => array('Trigliserida', 'harga_trigliserida', 25000, 'mg/dL'),
            'asam_urat' => array('Asam Urat', 'harga_asam_urat', 15000, 'mg/dL'),
            'ureum' => array('Ureum', 'harga_ureum', 30000, 'mg/dL'),
            'creatinin' => array('Creatinin', 'harga_creatinin', 30000, 'mg/dL'),
            'sgpt' => array('SGPT', 'harga_sgpt', 30000, 'U/L'),
            'sgot' => array('SGOT', 'harga_sgot', 30000, 'U/L')
        );
        
        foreach ($items as $field => $info) {
            if (!empty($kimia[$field]) && $kimia[$field] !== null) {
                $breakdown[] = array(
                    'item' => $info[0],
                    'hasil' => $kimia[$field] . ' ' . $info[3],
                    'harga' => (float)($kimia[$info[1]] ?? $info[2])
                );
            }
        }
        
        return $breakdown;
    }


    private function _get_serologi_breakdown($pemeriksaan_id) {
        $breakdown = array();
        $this->db->where('pemeriksaan_id', $pemeriksaan_id);
        $serologi = $this->db->get('serologi_imunologi')->row_array();
        
        if (!$serologi) return $breakdown;
        
        $tests = array(
            'rdt_antigen' => array('RDT Antigen', 'harga_rdt_antigen', 75000),
            'widal' => array('Widal Test', 'harga_widal', 33000),
            'hbsag' => array('HBsAg', 'harga_hbsag', 35000),
            'ns1' => array('NS1 (Dengue)', 'harga_ns1', 100000),
            'hiv' => array('HIV Test', 'harga_hiv', 125000)
        );
        
        foreach ($tests as $field => $info) {
            if (!empty($serologi[$field]) && $serologi[$field] !== null) {
                $breakdown[] = array(
                    'item' => $info[0],
                    'hasil' => $serologi[$field],
                    'harga' => (float)($serologi[$info[1]] ?? $info[2])
                );
            }
        }
        
        return $breakdown;
    }

    private function _get_ims_breakdown($pemeriksaan_id) {
        $breakdown = array();
        $this->db->where('pemeriksaan_id', $pemeriksaan_id);
        $ims = $this->db->get('ims')->row_array();
        
        if (!$ims) return $breakdown;
        
        if (!empty($ims['sifilis']) && $ims['sifilis'] !== null) {
            $breakdown[] = array(
                'item' => 'Tes Sifilis',
                'hasil' => $ims['sifilis'],
                'harga' => (float)($ims['harga_sifilis'] ?? 100000)
            );
        }
        
        if (!empty($ims['duh_tubuh']) && $ims['duh_tubuh'] !== null) {
            $breakdown[] = array(
                'item' => 'Tes Duh Tubuh',
                'hasil' => $ims['duh_tubuh'],
                'harga' => (float)($ims['harga_duh_tubuh'] ?? 50000)
            );
        }
        
        return $breakdown;
    }

    /**
     * Cek apakah pemeriksaan sudah punya hasil
     */
    public function has_examination_results($pemeriksaan_id) {
        $this->db->select('jenis_pemeriksaan');
        $this->db->where('pemeriksaan_id', $pemeriksaan_id);
        $examination = $this->db->get('pemeriksaan_lab')->row_array();
        
        if (!$examination) return false;
        
        $jenis = strtolower(trim($examination['jenis_pemeriksaan']));
        $table_map = array(
            'tbc' => 'tbc',
            'kimia darah' => 'kimia_darah',
            'hematologi' => 'hematologi',
            'urinologi' => 'urinologi',
            'urine' => 'urinologi',
            'serologi' => 'serologi_imunologi',
            'serologi imunologi' => 'serologi_imunologi',
            'ims' => 'ims'
        );
        
        if (!isset($table_map[$jenis])) return false;
        
        $this->db->where('pemeriksaan_id', $pemeriksaan_id);
        return $this->db->count_all_results($table_map[$jenis]) > 0;
    }
        private function _calculate_hematologi_cost($pemeriksaan_id) {
        $this->db->where('pemeriksaan_id', $pemeriksaan_id);
        $hematologi = $this->db->get('hematologi')->row_array();
        
        if (!$hematologi) return 0;
        
        $total = 0;
        
        // PAKET DARAH RUTIN - Cek apakah ada salah satu parameter yang terisi
        $paket_darah_fields = [
            'hemoglobin', 'hematokrit', 'leukosit', 'trombosit', 'eritrosit',
            'mcv', 'mch', 'mchc', 'eosinofil', 'basofil', 'neutrofil',
            'limfosit', 'monosit'
        ];
        
        $has_paket_darah = false;
        foreach ($paket_darah_fields as $field) {
            if (!empty($hematologi[$field]) && $hematologi[$field] !== null) {
                $has_paket_darah = true;
                break; // Cukup 1 yang terisi, langsung break
            }
        }
        
        // Jika ada minimal 1 parameter paket terisi, charge harga paket
        if ($has_paket_darah) {
            $total += (float)($hematologi['harga_paket_darah_rutin'] ?? 40000);
        }
        
        // Item di luar paket - dihitung terpisah
        if (!empty($hematologi['laju_endap_darah']) && $hematologi['laju_endap_darah'] !== null) {
            $total += (float)($hematologi['harga_led'] ?? 15000);
        }
        
        if (!empty($hematologi['clotting_time']) && $hematologi['clotting_time'] !== null) {
            $total += (float)($hematologi['harga_clotting'] ?? 15000);
        }
        
        if (!empty($hematologi['bleeding_time']) && $hematologi['bleeding_time'] !== null) {
            $total += (float)($hematologi['harga_bleeding'] ?? 15000);
        }
        
        // Golongan Darah + Rhesus
        if ((!empty($hematologi['golongan_darah']) && $hematologi['golongan_darah'] !== null) || 
            (!empty($hematologi['rhesus']) && $hematologi['rhesus'] !== null)) {
            $total += (float)($hematologi['harga_goldar'] ?? 20000);
        }
        
        // Malaria
        if (!empty($hematologi['malaria']) && $hematologi['malaria'] !== null) {
            $total += (float)($hematologi['harga_malaria'] ?? 15000);
        }
        
        return $total;
    }

    /**
     * UPDATED: Hitung biaya Urinologi dengan sistem PAKET
     * Urin Rutin = 1 harga flat jika ada salah satu parameter terisi
     */
    private function _calculate_urinologi_cost($pemeriksaan_id) {
        $this->db->where('pemeriksaan_id', $pemeriksaan_id);
        $urinologi = $this->db->get('urinologi')->row_array();
        
        if (!$urinologi) return 0;
        
        $total = 0;
        
        // URIN RUTIN - Cek apakah ada salah satu parameter yang terisi
        $urin_rutin_fields = [
            'makroskopis', 'mikroskopis', 'kimia_ph', 'berat_jenis',
            'glukosa', 'keton', 'bilirubin', 'urobilinogen', 'protein_regular',
        ];
        
        $has_urin_rutin = false;
        foreach ($urin_rutin_fields as $field) {
            if (!empty($urinologi[$field]) && $urinologi[$field] !== null) {
                $has_urin_rutin = true;
                break; // Cukup 1 yang terisi, langsung break
            }
        }
        
        // Jika ada minimal 1 parameter paket terisi, charge harga paket
        if ($has_urin_rutin) {
            $total += (float)($urinologi['harga_urin_rutin'] ?? 25000);
        }
        
        // Item di luar paket - dihitung terpisah
        if (!empty($urinologi['protein']) && $urinologi['protein'] !== null) {
            $total += (float)($urinologi['harga_protein'] ?? 10000);
        }
        
        if (!empty($urinologi['tes_kehamilan']) && $urinologi['tes_kehamilan'] !== null) {
            $total += (float)($urinologi['harga_tes_kehamilan'] ?? 15000);
        }
        
        return $total;
    }

    /**
     * UPDATED: Get breakdown dengan sistem PAKET
     */
    private function _get_hematologi_breakdown($pemeriksaan_id) {
        $breakdown = array();
        $this->db->where('pemeriksaan_id', $pemeriksaan_id);
        $hematologi = $this->db->get('hematologi')->row_array();
        
        if (!$hematologi) return $breakdown;
        
        // PAKET DARAH RUTIN - Kumpulkan semua parameter yang terisi
        $paket_fields = [
            'hemoglobin' => ['Hemoglobin', 'g/dL'],
            'hematokrit' => ['Hematokrit', '%'],
            'leukosit' => ['Leukosit', '/µL'],
            'trombosit' => ['Trombosit', '/µL'],
            'eritrosit' => ['Eritrosit', '/µL'],
            'mcv' => ['MCV', 'fL'],
            'mch' => ['MCH', 'pg'],
            'mchc' => ['MCHC', 'g/dL'],
            'eosinofil' => ['Eosinofil', '%'],
            'basofil' => ['Basofil', '%'],
            'neutrofil' => ['Neutrofil', '%'],
            'limfosit' => ['Limfosit', '%'],
            'monosit' => ['Monosit', '%']
        ];
        
        $hasil_paket = [];
        $has_paket = false;
        
        foreach ($paket_fields as $field => $info) {
            if (!empty($hematologi[$field]) && $hematologi[$field] !== null) {
                $has_paket = true;
                $hasil_paket[] = $info[0] . ': ' . $hematologi[$field] . ' ' . $info[1];
            }
        }
        
        // Tampilkan sebagai 1 item paket dengan detail
        if ($has_paket) {
            $breakdown[] = array(
                'item' => 'Paket Darah Rutin Lengkap',
                'hasil' => implode(', ', $hasil_paket),
                'harga' => (float)($hematologi['harga_paket_darah_rutin'] ?? 40000)
            );
        }
        
        // Item terpisah
        if (!empty($hematologi['laju_endap_darah']) && $hematologi['laju_endap_darah'] !== null) {
            $breakdown[] = array(
                'item' => 'Laju Endap Darah (LED)',
                'hasil' => $hematologi['laju_endap_darah'] . ' mm/jam',
                'harga' => (float)($hematologi['harga_led'] ?? 15000)
            );
        }
        
        if (!empty($hematologi['clotting_time']) && $hematologi['clotting_time'] !== null) {
            $breakdown[] = array(
                'item' => 'Clotting Time',
                'hasil' => $hematologi['clotting_time'] . ' detik',
                'harga' => (float)($hematologi['harga_clotting'] ?? 15000)
            );
        }
        
        if (!empty($hematologi['bleeding_time']) && $hematologi['bleeding_time'] !== null) {
            $breakdown[] = array(
                'item' => 'Bleeding Time',
                'hasil' => $hematologi['bleeding_time'] . ' detik',
                'harga' => (float)($hematologi['harga_bleeding'] ?? 15000)
            );
        }
        
        if ((!empty($hematologi['golongan_darah']) && $hematologi['golongan_darah'] !== null) || 
            (!empty($hematologi['rhesus']) && $hematologi['rhesus'] !== null)) {
            $hasil_goldar = [];
            if (!empty($hematologi['golongan_darah'])) {
                $hasil_goldar[] = 'Golongan: ' . $hematologi['golongan_darah'];
            }
            if (!empty($hematologi['rhesus'])) {
                $hasil_goldar[] = 'Rhesus: ' . $hematologi['rhesus'];
            }
            
            $breakdown[] = array(
                'item' => 'Golongan Darah & Rhesus',
                'hasil' => implode(', ', $hasil_goldar),
                'harga' => (float)($hematologi['harga_goldar'] ?? 20000)
            );
        }
        
        if (!empty($hematologi['malaria']) && $hematologi['malaria'] !== null) {
            $breakdown[] = array(
                'item' => 'Tes Malaria',
                'hasil' => $hematologi['malaria'],
                'harga' => (float)($hematologi['harga_malaria'] ?? 15000)
            );
        }
        
        return $breakdown;
    }

    private function _get_urinologi_breakdown($pemeriksaan_id) {
        $breakdown = array();
        $this->db->where('pemeriksaan_id', $pemeriksaan_id);
        $urinologi = $this->db->get('urinologi')->row_array();
        
        if (!$urinologi) return $breakdown;
        
        // URIN RUTIN - Kumpulkan semua parameter yang terisi
        $urin_fields = [
            'makroskopis' => 'Makroskopis',
            'mikroskopis' => 'Mikroskopis',
            'kimia_ph' => 'pH',
            'berat_jenis' => 'Berat Jenis',
            'glukosa' => 'Glukosa',
            'keton' => 'Keton',
            'bilirubin' => 'Bilirubin',
            'urobilinogen' => 'Urobilinogen'
        ];
        
        $hasil_urin = [];
        $has_urin = false;
        
        foreach ($urin_fields as $field => $label) {
            if (!empty($urinologi[$field]) && $urinologi[$field] !== null) {
                $has_urin = true;
                $hasil_urin[] = $label . ': ' . $urinologi[$field];
            }
        }
        
        // Tampilkan sebagai 1 item paket dengan detail
        if ($has_urin) {
            $breakdown[] = array(
                'item' => 'Urin Rutin Lengkap',
                'hasil' => implode(', ', $hasil_urin),
                'harga' => (float)($urinologi['harga_urin_rutin'] ?? 25000)
            );
        }
        
        // Item terpisah
        if (!empty($urinologi['protein']) && $urinologi['protein'] !== null) {
            $breakdown[] = array(
                'item' => 'Tes Protein Urin',
                'hasil' => $urinologi['protein'],
                'harga' => (float)($urinologi['harga_protein'] ?? 10000)
            );
        }
        
        if (!empty($urinologi['tes_kehamilan']) && $urinologi['tes_kehamilan'] !== null) {
            $breakdown[] = array(
                'item' => 'Tes Kehamilan',
                'hasil' => $urinologi['tes_kehamilan'],
                'harga' => (float)($urinologi['harga_tes_kehamilan'] ?? 15000)
            );
        }
        
        return $breakdown;
    }
}