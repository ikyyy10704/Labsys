<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Quality_control_model extends CI_Model {
    
    public function __construct()
    {
        parent::__construct();
        $this->load->database();
    }

    // ==========================================
    // CREATE METHODS
    // ==========================================
    
    /**
     * Create new QC record
     */
    public function create_qc($data)
    {
        try {
            if ($this->db->insert('quality_control', $data)) {
                return $this->db->insert_id();
            }
            return false;
        } catch (Exception $e) {
            log_message('error', 'Error creating QC: ' . $e->getMessage());
            return false;
        }
    }

    // ==========================================
    // READ METHODS
    // ==========================================
    
    /**
     * Get all QC records with details
     */
    public function get_all_qc($limit = null, $offset = 0)
    {
        try {
            $this->db->select('qc.*, al.nama_alat, al.kode_unik, al.merek_model, al.lokasi');
            $this->db->from('quality_control qc');
            $this->db->join('alat_laboratorium al', 'qc.alat_id = al.alat_id', 'left');
            $this->db->order_by('qc.tanggal_qc', 'DESC');
            $this->db->order_by('qc.waktu_qc', 'DESC');
            
            if ($limit !== null) {
                $this->db->limit($limit, $offset);
            }
            
            $query = $this->db->get();
            $results = $query->result_array();
            
            // Decode JSON fields
            foreach ($results as &$qc) {
                $qc['parameter_qc'] = json_decode($qc['parameter_qc'], true) ?: [];
                $qc['nilai_hasil'] = json_decode($qc['nilai_hasil'], true) ?: [];
                $qc['nilai_standar'] = json_decode($qc['nilai_standar'], true) ?: [];
            }
            
            return $results;
        } catch (Exception $e) {
            log_message('error', 'Error getting all QC: ' . $e->getMessage());
            return array();
        }
    }

    /**
     * Get QC by ID
     */
    public function get_qc_by_id($qc_id)
    {
        try {
            $this->db->select('qc.*, al.nama_alat, al.kode_unik, al.merek_model, al.lokasi');
            $this->db->from('quality_control qc');
            $this->db->join('alat_laboratorium al', 'qc.alat_id = al.alat_id', 'left');
            $this->db->where('qc.qc_id', $qc_id);
            
            $query = $this->db->get();
            $qc = $query->row_array();
            
            if ($qc) {
                $qc['parameter_qc'] = json_decode($qc['parameter_qc'], true) ?: [];
                $qc['nilai_hasil'] = json_decode($qc['nilai_hasil'], true) ?: [];
                $qc['nilai_standar'] = json_decode($qc['nilai_standar'], true) ?: [];
            }
            
            return $qc;
        } catch (Exception $e) {
            log_message('error', 'Error getting QC by ID: ' . $e->getMessage());
            return null;
        }
    }

    /**
     * Get QC history by alat
     */
    public function get_qc_by_alat($alat_id, $limit = null)
    {
        try {
            $this->db->select('*');
            $this->db->from('v_qc_detail');
            $this->db->where('alat_id', $alat_id);
            $this->db->where('status', 'Active');
            $this->db->order_by('tanggal_qc', 'DESC');
            
            if ($limit !== null) {
                $this->db->limit($limit);
            }
            
            $query = $this->db->get();
            return $query->result_array();
        } catch (Exception $e) {
            log_message('error', 'Error getting QC by alat: ' . $e->getMessage());
            return array();
        }
    }

    /**
     * Get QC by date range
     */
    public function get_qc_by_date_range($start_date, $end_date)
    {
        try {
            $this->db->select('*');
            $this->db->from('v_qc_detail');
            $this->db->where('tanggal_qc >=', $start_date);
            $this->db->where('tanggal_qc <=', $end_date);
            $this->db->where('status', 'Active');
            $this->db->order_by('tanggal_qc', 'DESC');
            
            $query = $this->db->get();
            return $query->result_array();
        } catch (Exception $e) {
            log_message('error', 'Error getting QC by date range: ' . $e->getMessage());
            return array();
        }
    }

    /**
     * Get latest QC for alat
     */
    public function get_latest_qc($alat_id)
    {
        try {
            $this->db->select('*');
            $this->db->from('v_qc_detail');
            $this->db->where('alat_id', $alat_id);
            $this->db->where('status', 'Active');
            $this->db->order_by('tanggal_qc', 'DESC');
            $this->db->limit(1);
            
            $query = $this->db->get();
            return $query->row_array();
        } catch (Exception $e) {
            log_message('error', 'Error getting latest QC: ' . $e->getMessage());
            return null;
        }
    }

    // ==========================================
    // UPDATE METHODS
    // ==========================================
    
    /**
     * Update QC record
     */
    public function update_qc($qc_id, $data)
    {
        try {
            $this->db->where('qc_id', $qc_id);
            return $this->db->update('quality_control', $data);
        } catch (Exception $e) {
            log_message('error', 'Error updating QC: ' . $e->getMessage());
            return false;
        }
    }

    // ==========================================
    // DELETE METHODS
    // ==========================================
    
    /**
     * Soft delete QC (set status to Cancelled)
     */
    public function delete_qc($qc_id)
    {
        try {
            $data = array(
                'status' => 'Cancelled',
                'updated_at' => date('Y-m-d H:i:s')
            );
            
            $this->db->where('qc_id', $qc_id);
            return $this->db->update('quality_control', $data);
        } catch (Exception $e) {
            log_message('error', 'Error deleting QC: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Hard delete QC (permanent)
     */
    public function permanent_delete_qc($qc_id)
    {
        try {
            $this->db->where('qc_id', $qc_id);
            return $this->db->delete('quality_control');
        } catch (Exception $e) {
            log_message('error', 'Error permanent deleting QC: ' . $e->getMessage());
            return false;
        }
    }

    // ==========================================
    // STATISTICS METHODS
    // ==========================================
    
    /**
     * Get QC statistics
     */
    public function get_qc_statistics($start_date = null, $end_date = null)
    {
        try {
            if ($start_date === null) {
                $start_date = date('Y-m-01'); // First day of current month
            }
            if ($end_date === null) {
                $end_date = date('Y-m-d'); // Today
            }

            // Total QC performed
            $this->db->where('tanggal_qc >=', $start_date);
            $this->db->where('tanggal_qc <=', $end_date);
            $this->db->where('status', 'Active');
            $total_qc = $this->db->count_all_results('quality_control');

            // Passed count
            $this->db->where('tanggal_qc >=', $start_date);
            $this->db->where('tanggal_qc <=', $end_date);
            $this->db->where('status', 'Active');
            $this->db->where('hasil_qc', 'Passed');
            $passed_count = $this->db->count_all_results('quality_control');

            // Failed count
            $this->db->where('tanggal_qc >=', $start_date);
            $this->db->where('tanggal_qc <=', $end_date);
            $this->db->where('status', 'Active');
            $this->db->where('hasil_qc', 'Failed');
            $failed_count = $this->db->count_all_results('quality_control');

            // Conditional count
            $this->db->where('tanggal_qc >=', $start_date);
            $this->db->where('tanggal_qc <=', $end_date);
            $this->db->where('status', 'Active');
            $this->db->where('hasil_qc', 'Conditional');
            $conditional_count = $this->db->count_all_results('quality_control');

            // Under Review count
            $this->db->where('tanggal_qc >=', $start_date);
            $this->db->where('tanggal_qc <=', $end_date);
            $this->db->where('status', 'Active');
            $this->db->where('hasil_qc', 'Under Review');
            $under_review_count = $this->db->count_all_results('quality_control');

            // Total alat tested
            $this->db->select('COUNT(DISTINCT alat_id) as count');
            $this->db->where('tanggal_qc >=', $start_date);
            $this->db->where('tanggal_qc <=', $end_date);
            $this->db->where('status', 'Active');
            $query = $this->db->get('quality_control');
            $alat_tested = $query->row()->count;

            // Calculate pass rate
            $pass_rate = $total_qc > 0 ? round(($passed_count / $total_qc) * 100, 2) : 0;

            return array(
                'total_qc' => $total_qc,
                'passed_count' => $passed_count,
                'failed_count' => $failed_count,
                'conditional_count' => $conditional_count,
                'under_review_count' => $under_review_count,
                'alat_tested' => $alat_tested,
                'pass_rate' => $pass_rate,
                'period_start' => $start_date,
                'period_end' => $end_date
            );
        } catch (Exception $e) {
            log_message('error', 'Error getting QC statistics: ' . $e->getMessage());
            return array(
                'total_qc' => 0,
                'passed_count' => 0,
                'failed_count' => 0,
                'conditional_count' => 0,
                'under_review_count' => 0,
                'alat_tested' => 0,
                'pass_rate' => 0
            );
        }
    }

    /**
     * Get QC trend data (for charts)
     */
    public function get_qc_trend($months = 6)
    {
        try {
            $this->db->select("
                DATE_FORMAT(tanggal_qc, '%Y-%m') as period,
                DATE_FORMAT(tanggal_qc, '%b %Y') as period_label,
                COUNT(*) as total,
                SUM(CASE WHEN hasil_qc = 'Passed' THEN 1 ELSE 0 END) as passed,
                SUM(CASE WHEN hasil_qc = 'Failed' THEN 1 ELSE 0 END) as failed,
                SUM(CASE WHEN hasil_qc = 'Conditional' THEN 1 ELSE 0 END) as conditional
            ");
            $this->db->from('quality_control');
            $this->db->where('status', 'Active');
            $this->db->where('tanggal_qc >=', date('Y-m-d', strtotime("-$months months")));
            $this->db->group_by("DATE_FORMAT(tanggal_qc, '%Y-%m')");
            $this->db->order_by('period', 'ASC');
            
            $query = $this->db->get();
            return $query->result_array();
        } catch (Exception $e) {
            log_message('error', 'Error getting QC trend: ' . $e->getMessage());
            return array();
        }
    }

    /**
     * Get alat performance summary
     */
    public function get_alat_performance($alat_id = null)
    {
        try {
            $this->db->select("
                qc.alat_id,
                al.nama_alat,
                al.kode_unik,
                COUNT(*) as total_qc,
                SUM(CASE WHEN qc.hasil_qc = 'Passed' THEN 1 ELSE 0 END) as passed_count,
                SUM(CASE WHEN qc.hasil_qc = 'Failed' THEN 1 ELSE 0 END) as failed_count,
                ROUND((SUM(CASE WHEN qc.hasil_qc = 'Passed' THEN 1 ELSE 0 END) / COUNT(*)) * 100, 2) as pass_rate,
                MAX(qc.tanggal_qc) as last_qc_date
            ");
            $this->db->from('quality_control qc');
            $this->db->join('alat_laboratorium al', 'qc.alat_id = al.alat_id', 'left');
            $this->db->where('qc.status', 'Active');
            
            if ($alat_id !== null) {
                $this->db->where('qc.alat_id', $alat_id);
            }
            
            $this->db->group_by('qc.alat_id');
            $this->db->order_by('pass_rate', 'ASC');
            
            $query = $this->db->get();
            return $alat_id !== null ? $query->row_array() : $query->result_array();
        } catch (Exception $e) {
            log_message('error', 'Error getting alat performance: ' . $e->getMessage());
            return $alat_id !== null ? null : array();
        }
    }

    // ==========================================
    // QC PARAMETERS METHODS
    // ==========================================
    
    /**
     * Get QC parameters for alat
     */
    public function get_qc_parameters($alat_id = null)
    {
        try {
            $this->db->select('*');
            $this->db->from('qc_parameters');
            $this->db->where('is_active', 1);
            
            if ($alat_id !== null) {
                $this->db->group_start();
                $this->db->where('alat_id', $alat_id);
                $this->db->or_where('alat_id IS NULL');
                $this->db->group_end();
            } else {
                $this->db->where('alat_id IS NULL');
            }
            
            $this->db->order_by('parameter_name', 'ASC');
            
            $query = $this->db->get();
            return $query->result_array();
        } catch (Exception $e) {
            log_message('error', 'Error getting QC parameters: ' . $e->getMessage());
            return array();
        }
    }

    /**
     * Create QC parameter
     */
    public function create_qc_parameter($data)
    {
        try {
            if ($this->db->insert('qc_parameters', $data)) {
                return $this->db->insert_id();
            }
            return false;
        } catch (Exception $e) {
            log_message('error', 'Error creating QC parameter: ' . $e->getMessage());
            return false;
        }
    }

    // ==========================================
    // FILTER & SEARCH METHODS
    // ==========================================
    
    /**
     * Get filtered QC records
     */
    public function get_filtered_qc($filters = array())
    {
        try {
            $this->db->select('*');
            $this->db->from('v_qc_detail');
            $this->db->where('status', 'Active');
            
            // Apply filters
            if (!empty($filters['alat_id'])) {
                $this->db->where('alat_id', $filters['alat_id']);
            }
            
            if (!empty($filters['hasil_qc'])) {
                $this->db->where('hasil_qc', $filters['hasil_qc']);
            }
            
            if (!empty($filters['qc_type'])) {
                $this->db->where('qc_type', $filters['qc_type']);
            }
            
            if (!empty($filters['start_date'])) {
                $this->db->where('tanggal_qc >=', $filters['start_date']);
            }
            
            if (!empty($filters['end_date'])) {
                $this->db->where('tanggal_qc <=', $filters['end_date']);
            }
            
            if (!empty($filters['search'])) {
                $search = $this->db->escape_like_str($filters['search']);
                $this->db->group_start();
                $this->db->like('nama_alat', $search, 'both');
                $this->db->or_like('kode_alat', $search, 'both');
                $this->db->or_like('parameter_qc', $search, 'both');
                $this->db->or_like('teknisi', $search, 'both');
                $this->db->group_end();
            }
            
            $this->db->order_by('tanggal_qc', 'DESC');
            
            $query = $this->db->get();
            return $query->result_array();
        } catch (Exception $e) {
            log_message('error', 'Error getting filtered QC: ' . $e->getMessage());
            return array();
        }
    }

    // ==========================================
    // ALERT METHODS
    // ==========================================
    
    /**
     * Get alat that need QC (no QC in last 30 days)
     */
    public function get_alat_need_qc()
    {
        try {
            $query = $this->db->query("
                SELECT 
                    al.*,
                    MAX(qc.tanggal_qc) as last_qc_date,
                    DATEDIFF(CURDATE(), MAX(qc.tanggal_qc)) as days_since_qc
                FROM alat_laboratorium al
                LEFT JOIN quality_control qc ON al.alat_id = qc.alat_id 
                    AND qc.status = 'Active'
                WHERE al.status_alat != 'Rusak'
                GROUP BY al.alat_id
                HAVING last_qc_date IS NULL 
                    OR DATEDIFF(CURDATE(), MAX(qc.tanggal_qc)) > 30
                ORDER BY days_since_qc DESC
            ");
            
            return $query->result_array();
        } catch (Exception $e) {
            log_message('error', 'Error getting alat need QC: ' . $e->getMessage());
            return array();
        }
    }

    /**
     * Check if alat has recent failed QC
     */
    public function has_recent_failed_qc($alat_id, $days = 7)
    {
        try {
            $this->db->where('alat_id', $alat_id);
            $this->db->where('hasil_qc', 'Failed');
            $this->db->where('status', 'Active');
            $this->db->where('tanggal_qc >=', date('Y-m-d', strtotime("-$days days")));
            
            return $this->db->count_all_results('quality_control') > 0;
        } catch (Exception $e) {
            log_message('error', 'Error checking failed QC: ' . $e->getMessage());
            return false;
        }
    }
    
    // ==========================================
    // EQUIPMENT HELPER METHODS
    // ==========================================
    
    /**
     * Get all active equipment
     */
    public function get_active_equipment()
    {
        try {
            $this->db->select('*');
            $this->db->from('alat_laboratorium');
            $this->db->order_by('nama_alat', 'ASC');
            
            return $this->db->get()->result_array();
        } catch (Exception $e) {
            log_message('error', 'Error getting active equipment: ' . $e->getMessage());
            return array();
        }
    }
    
    /**
     * Get equipment by ID
     */
    public function get_equipment_by_id($alat_id)
    {
        try {
            $this->db->select('*');
            $this->db->where('alat_id', $alat_id);
            
            return $this->db->get('alat_laboratorium')->row_array();
        } catch (Exception $e) {
            log_message('error', 'Error getting equipment by ID: ' . $e->getMessage());
            return null;
        }
    }
    
    /**
     * Get calibration status of equipment
     */
    public function get_calibration_status($equipment)
    {
        if (empty($equipment['tanggal_kalibrasi_terakhir'])) {
            return [
                'status' => 'EXPIRED',
                'days_remaining' => null,
                'class' => 'bg-red-100 text-red-700',
                'message' => 'Belum pernah kalibrasi'
            ];
        }
        
        $last_cal = strtotime($equipment['tanggal_kalibrasi_terakhir']);
        $next_cal = strtotime($equipment['jadwal_kalibrasi']);
        $today = time();
        
        $days_remaining = ceil(($next_cal - $today) / 86400);
        
        if ($days_remaining < 0) {
            return [
                'status' => 'EXPIRED',
                'days_remaining' => $days_remaining,
                'class' => 'bg-red-100 text-red-700',
                'message' => 'Kalibrasi expired ' . abs($days_remaining) . ' hari yang lalu'
            ];
        } elseif ($days_remaining < 30) {
            return [
                'status' => 'WARNING',
                'days_remaining' => $days_remaining,
                'class' => 'bg-yellow-100 text-yellow-700',
                'message' => 'Kalibrasi akan expired dalam ' . $days_remaining . ' hari'
            ];
        } else {
            return [
                'status' => 'VALID',
                'days_remaining' => $days_remaining,
                'class' => 'bg-green-100 text-green-700',
                'message' => 'Kalibrasi valid (' . $days_remaining . ' hari lagi)'
            ];
        }
    }
    
    /**
     * Save QC data (alias for create_qc for compatibility)
     */
    public function save_qc($data)
    {
        return $this->create_qc($data);
    }
    

    
    // ==========================================
    // SUPERVISOR VALIDATION METHODS
    // ==========================================
    
    /**
     * Get QC pending validation (supervisor IS NULL)
     */
    public function get_pending_validation($limit = 50, $offset = 0)
    {
        try {
            $this->db->select('qc.*, al.nama_alat, al.kode_unik, al.merek_model, al.lokasi');
            $this->db->from('quality_control qc');
            $this->db->join('alat_laboratorium al', 'qc.alat_id = al.alat_id', 'left');
            $this->db->where('qc.supervisor IS NULL');
            $this->db->or_where('qc.supervisor', '');
            $this->db->order_by('qc.tanggal_qc', 'DESC');
            $this->db->order_by('qc.waktu_qc', 'DESC');
            
            if ($limit !== null) {
                $this->db->limit($limit, $offset);
            }
            
            $query = $this->db->get();
            $results = $query->result_array();
            
            // Decode JSON fields
            foreach ($results as &$qc) {
                $qc['parameter_qc'] = json_decode($qc['parameter_qc'], true) ?: [];
                $qc['nilai_hasil'] = json_decode($qc['nilai_hasil'], true) ?: [];
                $qc['nilai_standar'] = json_decode($qc['nilai_standar'], true) ?: [];
            }
            
            return $results;
        } catch (Exception $e) {
            log_message('error', 'Error getting pending validation: ' . $e->getMessage());
            return array();
        }
    }
    
    /**
     * Validate QC (approve/reject by supervisor)
     */
    public function validate_qc($qc_id, $supervisor_name, $validation_note = null)
    {
        try {
            $data = [
                'supervisor' => $supervisor_name
            ];
            
            // If validation note provided, append to existing catatan
            if ($validation_note) {
                $existing = $this->get_qc_by_id($qc_id);
                if ($existing) {
                    $old_note = $existing['catatan'] ?: '';
                    $data['catatan'] = $old_note . ($old_note ? '\n\n' : '') . 
                                      '--- Catatan Supervisor ---\n' . $validation_note;
                }
            }
            
            $this->db->where('qc_id', $qc_id);
            return $this->db->update('quality_control', $data);
        } catch (Exception $e) {
            log_message('error', 'Error validating QC: ' . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Get validated QC history (has supervisor)
     */
    public function get_validated_qc($limit = 50, $offset = 0)
    {
        try {
            $this->db->select('qc.*, al.nama_alat, al.kode_unik, al.merek_model, al.lokasi');
            $this->db->from('quality_control qc');
            $this->db->join('alat_laboratorium al', 'qc.alat_id = al.alat_id', 'left');
            $this->db->where('qc.supervisor IS NOT NULL');
            $this->db->where('qc.supervisor !=', '');
            $this->db->order_by('qc.tanggal_qc', 'DESC');
            $this->db->order_by('qc.waktu_qc', 'DESC');
            
            if ($limit !== null) {
                $this->db->limit($limit, $offset);
            }
            
            $query = $this->db->get();
            $results = $query->result_array();
            
            // Decode JSON fields
            foreach ($results as &$qc) {
                $qc['parameter_qc'] = json_decode($qc['parameter_qc'], true) ?: [];
                $qc['nilai_hasil'] = json_decode($qc['nilai_hasil'], true) ?: [];
                $qc['nilai_standar'] = json_decode($qc['nilai_standar'], true) ?: [];
            }
            
            return $results;
        } catch (Exception $e) {
            log_message('error', 'Error getting validated QC: ' . $e->getMessage());
            return array();
        }
    }
    /**
     * Get QC history with filters
     */
    public function get_qc_history($limit = 50, $offset = 0, $filters = array())
    {
        try {
            $this->db->select('qc.*, al.nama_alat, al.kode_unik, al.merek_model, al.lokasi');
            $this->db->from('quality_control qc');
            $this->db->join('alat_laboratorium al', 'qc.alat_id = al.alat_id', 'left');
            $this->db->where('qc.supervisor IS NOT NULL');
            $this->db->where('qc.supervisor !=', '');
            
            // Apply filters
            if (!empty($filters['start_date'])) {
                $this->db->where('qc.tanggal_qc >=', $filters['start_date']);
            }
            if (!empty($filters['end_date'])) {
                $this->db->where('qc.tanggal_qc <=', $filters['end_date']);
            }
            if (!empty($filters['search'])) {
                $this->db->group_start();
                $this->db->like('al.nama_alat', $filters['search']);
                $this->db->or_like('al.kode_unik', $filters['search']);
                $this->db->or_like('qc.teknisi', $filters['search']);
                $this->db->or_like('qc.supervisor', $filters['search']);
                $this->db->group_end();
            }

            $this->db->order_by('qc.tanggal_qc', 'DESC');
            $this->db->order_by('qc.waktu_qc', 'DESC');
            
            if ($limit !== null) {
                $this->db->limit($limit, $offset);
            }
            
            $query = $this->db->get();
            $results = $query->result_array();
            
            // Decode JSON fields
            foreach ($results as &$qc) {
                $qc['parameter_qc'] = json_decode($qc['parameter_qc'], true) ?: [];
                $qc['nilai_hasil'] = json_decode($qc['nilai_hasil'], true) ?: [];
                $qc['nilai_standar'] = json_decode($qc['nilai_standar'], true) ?: [];
            }
            
            return $results;
        } catch (Exception $e) {
            log_message('error', 'Error getting QC history: ' . $e->getMessage());
            return array();
        }
    }

    /**
     * Count QC history for pagination
     */
    public function count_qc_history($filters = array())
    {
        try {
            $this->db->from('quality_control qc');
            $this->db->join('alat_laboratorium al', 'qc.alat_id = al.alat_id', 'left');
            $this->db->where('qc.supervisor IS NOT NULL');
            $this->db->where('qc.supervisor !=', '');
            
            // Apply filters (same as above)
            if (!empty($filters['start_date'])) {
                $this->db->where('qc.tanggal_qc >=', $filters['start_date']);
            }
            if (!empty($filters['end_date'])) {
                $this->db->where('qc.tanggal_qc <=', $filters['end_date']);
            }
            if (!empty($filters['search'])) {
                $this->db->group_start();
                $this->db->like('al.nama_alat', $filters['search']);
                $this->db->or_like('al.kode_unik', $filters['search']);
                $this->db->or_like('qc.teknisi', $filters['search']);
                $this->db->or_like('qc.supervisor', $filters['search']);
                $this->db->group_end();
            }
            
            return $this->db->count_all_results();
        } catch (Exception $e) {
            return 0;
        }
    }
}
