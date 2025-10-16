<?php

if (!defined('BASEPATH')) exit('No direct script access allowed');

/**
 * Format examination status for display
 */
if (!function_exists('format_examination_status')) {
    function format_examination_status($status) {
        $status_map = array(
            'pending' => array(
                'label' => 'Pending',
                'class' => 'bg-yellow-100 text-yellow-800',
                'icon' => 'clock'
            ),
            'progress' => array(
                'label' => 'Dalam Proses',
                'class' => 'bg-blue-100 text-blue-800',
                'icon' => 'activity'
            ),
            'selesai' => array(
                'label' => 'Selesai',
                'class' => 'bg-green-100 text-green-800',
                'icon' => 'check-circle'
            ),
            'cancelled' => array(
                'label' => 'Dibatalkan',
                'class' => 'bg-red-100 text-red-800',
                'icon' => 'x-circle'
            )
        );
        
        return isset($status_map[$status]) ? $status_map[$status] : array(
            'label' => ucfirst($status),
            'class' => 'bg-gray-100 text-gray-800',
            'icon' => 'help-circle'
        );
    }
}

/**
 * Get examination type badge
 */
if (!function_exists('get_examination_type_badge')) {
    function get_examination_type_badge($type) {
        $type_colors = array(
            'Kimia Darah' => 'bg-blue-100 text-blue-800',
            'Hematologi' => 'bg-red-100 text-red-800',
            'Urinologi' => 'bg-yellow-100 text-yellow-800',
            'Serologi' => 'bg-purple-100 text-purple-800',
            'TBC' => 'bg-orange-100 text-orange-800',
            'IMS' => 'bg-pink-100 text-pink-800',
            'MLS' => 'bg-indigo-100 text-indigo-800'
        );
        
        $class = isset($type_colors[$type]) ? $type_colors[$type] : 'bg-gray-100 text-gray-800';
        
        return array(
            'class' => $class,
            'label' => $type
        );
    }
}

/**
 * Calculate examination completion rate
 */
if (!function_exists('calculate_completion_rate')) {
    function calculate_completion_rate($completed, $total) {
        if ($total == 0) return 0;
        return round(($completed / $total) * 100, 2);
    }
}

/**
 * Format processing time
 */
if (!function_exists('format_processing_time')) {
    function format_processing_time($hours) {
        if ($hours < 1) {
            return round($hours * 60) . ' menit';
        } elseif ($hours < 24) {
            return round($hours, 1) . ' jam';
        } else {
            $days = floor($hours / 24);
            $remaining_hours = $hours % 24;
            return $days . ' hari ' . round($remaining_hours, 1) . ' jam';
        }
    }
}

/**
 * Get examination priority level
 */
if (!function_exists('get_examination_priority')) {
    function get_examination_priority($examination_type, $age, $created_at) {
        $priority = 'normal';
        $hours_since_created = (time() - strtotime($created_at)) / 3600;
        
        // High priority for certain types
        $high_priority_types = array('TBC', 'IMS', 'Serologi');
        if (in_array($examination_type, $high_priority_types)) {
            $priority = 'high';
        }
        
        // Urgent for elderly patients
        if ($age >= 65) {
            $priority = 'urgent';
        }
        
        // Escalate based on waiting time
        if ($hours_since_created > 48) {
            $priority = 'urgent';
        } elseif ($hours_since_created > 24) {
            $priority = 'high';
        }
        
        return array(
            'level' => $priority,
            'class' => $priority == 'urgent' ? 'bg-red-100 text-red-800' : 
                      ($priority == 'high' ? 'bg-orange-100 text-orange-800' : 'bg-green-100 text-green-800'),
            'label' => $priority == 'urgent' ? 'Mendesak' : 
                      ($priority == 'high' ? 'Tinggi' : 'Normal')
        );
    }
}

/**
 * Generate examination report summary
 */
if (!function_exists('generate_examination_summary')) {
    function generate_examination_summary($examinations) {
        $summary = array(
            'total' => count($examinations),
            'by_status' => array(),
            'by_type' => array(),
            'total_revenue' => 0,
            'avg_processing_time' => 0
        );
        
        $processing_times = array();
        
        foreach ($examinations as $exam) {
            // Count by status
            $status = $exam['status_pemeriksaan'];
            $summary['by_status'][$status] = isset($summary['by_status'][$status]) ? 
                                           $summary['by_status'][$status] + 1 : 1;
            
            // Count by type
            $type = $exam['jenis_pemeriksaan'];
            $summary['by_type'][$type] = isset($summary['by_type'][$type]) ? 
                                       $summary['by_type'][$type] + 1 : 1;
            
            // Sum revenue
            $summary['total_revenue'] += $exam['biaya'];
            
            // Calculate processing time for completed exams
            if ($status == 'selesai' && $exam['started_at'] && $exam['completed_at']) {
                $start = strtotime($exam['started_at']);
                $end = strtotime($exam['completed_at']);
                $processing_times[] = ($end - $start) / 3600; // hours
            }
        }
        
        // Calculate average processing time
        if (!empty($processing_times)) {
            $summary['avg_processing_time'] = array_sum($processing_times) / count($processing_times);
        }
        
        return $summary;
    }
}

// ==============================================
// File: application/config/examination_config.php
// Configuration untuk laporan pemeriksaan
// ==============================================

defined('BASEPATH') OR exit('No direct script access allowed');

/*
|--------------------------------------------------------------------------
| Examination Report Configuration
|--------------------------------------------------------------------------
*/

// Export settings
$config['examination_export'] = array(
    'max_records_excel' => 10000,  // Maximum records for Excel export
    'max_records_pdf' => 1000,     // Maximum records for PDF export
    'memory_limit' => '512M',      // Memory limit for export
    'time_limit' => 300,           // Time limit in seconds
    'temp_dir' => APPPATH . 'temp/', // Temporary directory for files
);

// Chart settings
$config['examination_charts'] = array(
    'trend_days' => 30,            // Default days for trend chart
    'max_chart_points' => 31,      // Maximum points in trend chart
    'status_colors' => array(
        'pending' => '#F59E0B',
        'progress' => '#3B82F6',
        'selesai' => '#10B981',
        'cancelled' => '#EF4444'
    )
);

// Pagination settings
$config['examination_pagination'] = array(
    'per_page_default' => 20,
    'per_page_options' => array(10, 20, 50, 100),
    'max_per_page' => 100
);

// Reference values for examination results
$config['examination_reference_values'] = array(
    'Kimia Darah' => array(
        'gula_darah_puasa' => array(
            'normal_min' => 70,
            'normal_max' => 100,
            'unit' => 'mg/dL',
            'description' => 'Gula Darah Puasa'
        ),
        'gula_darah_sewaktu' => array(
            'normal_min' => 70,
            'normal_max' => 140,
            'unit' => 'mg/dL',
            'description' => 'Gula Darah Sewaktu'
        ),
        'cholesterol_total' => array(
            'normal_min' => 0,
            'normal_max' => 200,
            'unit' => 'mg/dL',
            'description' => 'Kolesterol Total'
        ),
        'cholesterol_hdl' => array(
            'normal_min' => 40,
            'normal_max' => 999,
            'unit' => 'mg/dL',
            'description' => 'Kolesterol HDL'
        ),
        'cholesterol_ldl' => array(
            'normal_min' => 0,
            'normal_max' => 130,
            'unit' => 'mg/dL',
            'description' => 'Kolesterol LDL'
        ),
        'trigliserida' => array(
            'normal_min' => 0,
            'normal_max' => 150,
            'unit' => 'mg/dL',
            'description' => 'Trigliserida'
        ),
        'asam_urat' => array(
            'normal_min' => 3.5,
            'normal_max' => 7.0,
            'unit' => 'mg/dL',
            'description' => 'Asam Urat'
        ),
        'ureum' => array(
            'normal_min' => 10,
            'normal_max' => 50,
            'unit' => 'mg/dL',
            'description' => 'Ureum'
        ),
        'creatinin' => array(
            'normal_min' => 0.6,
            'normal_max' => 1.3,
            'unit' => 'mg/dL',
            'description' => 'Kreatinin'
        ),
        'sgpt' => array(
            'normal_min' => 7,
            'normal_max' => 56,
            'unit' => 'U/L',
            'description' => 'SGPT (ALT)'
        ),
        'sgot' => array(
            'normal_min' => 10,
            'normal_max' => 40,
            'unit' => 'U/L',
            'description' => 'SGOT (AST)'
        )
    ),
    'Hematologi' => array(
        'hemoglobin' => array(
            'normal_min_male' => 14.0,
            'normal_max_male' => 18.0,
            'normal_min_female' => 12.0,
            'normal_max_female' => 16.0,
            'unit' => 'g/dL',
            'description' => 'Hemoglobin'
        ),
        'hematokrit' => array(
            'normal_min_male' => 42.0,
            'normal_max_male' => 52.0,
            'normal_min_female' => 36.0,
            'normal_max_female' => 46.0,
            'unit' => '%',
            'description' => 'Hematokrit'
        ),
        'laju_endap_darah' => array(
            'normal_min' => 0,
            'normal_max' => 20,
            'unit' => 'mm/jam',
            'description' => 'Laju Endap Darah'
        )
    )
);

// Status mapping
$config['examination_status'] = array(
    'pending' => array(
        'label' => 'Pending',
        'description' => 'Menunggu untuk diproses',
        'color' => '#F59E0B',
        'next_actions' => array('progress', 'cancelled')
    ),
    'progress' => array(
        'label' => 'Dalam Proses',
        'description' => 'Sedang dalam pemeriksaan',
        'color' => '#3B82F6',
        'next_actions' => array('selesai', 'cancelled')
    ),
    'selesai' => array(
        'label' => 'Selesai',
        'description' => 'Pemeriksaan telah selesai',
        'color' => '#10B981',
        'next_actions' => array()
    ),
    'cancelled' => array(
        'label' => 'Dibatalkan',
        'description' => 'Pemeriksaan dibatalkan',
        'color' => '#EF4444',
        'next_actions' => array()
    )
);

// Examination types
$config['examination_types'] = array(
    'Kimia Darah' => array(
        'description' => 'Pemeriksaan kimia darah',
        'sample_type' => 'Darah',
        'avg_processing_time' => 2, // hours
        'category' => 'clinical_chemistry'
    ),
    'Hematologi' => array(
        'description' => 'Pemeriksaan darah lengkap',
        'sample_type' => 'Darah',
        'avg_processing_time' => 1,
        'category' => 'hematology'
    ),
    'Urinologi' => array(
        'description' => 'Pemeriksaan urin',
        'sample_type' => 'Urin',
        'avg_processing_time' => 1,
        'category' => 'urinology'
    ),
    'Serologi' => array(
        'description' => 'Pemeriksaan serologi',
        'sample_type' => 'Serum',
        'avg_processing_time' => 4,
        'category' => 'serology'
    ),
    'TBC' => array(
        'description' => 'Pemeriksaan tuberculosis',
        'sample_type' => 'Dahak',
        'avg_processing_time' => 8,
        'category' => 'microbiology'
    ),
    'IMS' => array(
        'description' => 'Pemeriksaan infeksi menular seksual',
        'sample_type' => 'Variasi',
        'avg_processing_time' => 6,
        'category' => 'microbiology'
    ),
    'MLS' => array(
        'description' => 'Pemeriksaan mikrobiologi lainnya',
        'sample_type' => 'Variasi',
        'avg_processing_time' => 4,
        'category' => 'microbiology'
    )
);

// Report templates
$config['examination_report_templates'] = array(
    'summary' => array(
        'title' => 'Ringkasan Pemeriksaan',
        'sections' => array('stats', 'charts', 'top_types')
    ),
    'detailed' => array(
        'title' => 'Laporan Detail Pemeriksaan',
        'sections' => array('stats', 'charts', 'data_table', 'timeline')
    ),
    'financial' => array(
        'title' => 'Laporan Keuangan Pemeriksaan',
        'sections' => array('revenue_stats', 'revenue_charts', 'billing_details')
    )
);

// Performance thresholds
$config['examination_performance'] = array(
    'processing_time_warning' => 24, // hours
    'processing_time_critical' => 48, // hours
    'completion_rate_target' => 95, // percentage
    'daily_capacity' => 100, // examinations per day
    'queue_warning_level' => 50 // pending examinations
);
