<?php
if (!defined('BASEPATH')) exit('No direct script access allowed');

/**
 * DateTime Helper for Indonesia Timezone
 * 
 * Helper ini memastikan waktu yang digunakan SELALU menggunakan
 * timezone Indonesia (WIB/WITA/WIT) tanpa peduli setting server
 * 
 * @author  LabSy System
 * @version 1.0
 */

/**
 * Get current WIB (Waktu Indonesia Barat) datetime
 * Timezone: Asia/Jakarta (UTC +7)
 * 
 * @return string Format: Y-m-d H:i:s
 */
if (!function_exists('wib_now')) {
    function wib_now() {
        try {
            $date = new DateTime('now', new DateTimeZone('Asia/Jakarta'));
            return $date->format('Y-m-d H:i:s');
        } catch (Exception $e) {
            // Fallback jika timezone tidak tersedia
            // Asumsi server UTC, tambah 7 jam
            return date('Y-m-d H:i:s', time() + (7 * 3600));
        }
    }
}

/**
 * Get current WITA (Waktu Indonesia Tengah) datetime
 * Timezone: Asia/Makassar (UTC +8)
 * 
 * @return string Format: Y-m-d H:i:s
 */
if (!function_exists('wita_now')) {
    function wita_now() {
        try {
            $date = new DateTime('now', new DateTimeZone('Asia/Makassar'));
            return $date->format('Y-m-d H:i:s');
        } catch (Exception $e) {
            return date('Y-m-d H:i:s', time() + (8 * 3600));
        }
    }
}

/**
 * Get current WIT (Waktu Indonesia Timur) datetime
 * Timezone: Asia/Jayapura (UTC +9)
 * 
 * @return string Format: Y-m-d H:i:s
 */
if (!function_exists('wit_now')) {
    function wit_now() {
        try {
            $date = new DateTime('now', new DateTimeZone('Asia/Jayapura'));
            return $date->format('Y-m-d H:i:s');
        } catch (Exception $e) {
            return date('Y-m-d H:i:s', time() + (9 * 3600));
        }
    }
}

/**
 * Get current Indonesia datetime (default WIB)
 * Alias untuk wib_now()
 * 
 * @return string Format: Y-m-d H:i:s
 */
if (!function_exists('indonesia_now')) {
    function indonesia_now() {
        return wib_now();
    }
}

/**
 * Convert UTC datetime to WIB
 * 
 * @param string $utc_datetime UTC datetime string
 * @return string WIB datetime
 */
if (!function_exists('utc_to_wib')) {
    function utc_to_wib($utc_datetime) {
        try {
            $date = new DateTime($utc_datetime, new DateTimeZone('UTC'));
            $date->setTimezone(new DateTimeZone('Asia/Jakarta'));
            return $date->format('Y-m-d H:i:s');
        } catch (Exception $e) {
            return $utc_datetime; // Return original jika error
        }
    }
}

/**
 * Convert WIB datetime to UTC
 * 
 * @param string $wib_datetime WIB datetime string
 * @return string UTC datetime
 */
if (!function_exists('wib_to_utc')) {
    function wib_to_utc($wib_datetime) {
        try {
            $date = new DateTime($wib_datetime, new DateTimeZone('Asia/Jakarta'));
            $date->setTimezone(new DateTimeZone('UTC'));
            return $date->format('Y-m-d H:i:s');
        } catch (Exception $e) {
            return $wib_datetime;
        }
    }
}

/**
 * Format datetime to Indonesian format
 * 
 * @param string $datetime Datetime string
 * @param string $format Format output (default: 'd F Y, H:i WIB')
 * @return string Formatted datetime
 */
if (!function_exists('format_datetime_indonesia')) {
    function format_datetime_indonesia($datetime, $format = 'd F Y, H:i') {
        $bulan = array(
            1 => 'Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni',
            'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'
        );
        
        $hari = array(
            'Sunday' => 'Minggu', 'Monday' => 'Senin', 'Tuesday' => 'Selasa',
            'Wednesday' => 'Rabu', 'Thursday' => 'Kamis', 'Friday' => 'Jumat',
            'Saturday' => 'Sabtu'
        );
        
        try {
            $date = new DateTime($datetime, new DateTimeZone('Asia/Jakarta'));
            $output = $date->format($format);
            
            // Replace English month with Indonesian
            foreach ($bulan as $num => $name) {
                $output = str_replace($date->format('F'), $name, $output);
            }
            
            // Replace English day with Indonesian
            $output = str_replace(
                $date->format('l'),
                $hari[$date->format('l')],
                $output
            );
            
            return $output . ' WIB';
        } catch (Exception $e) {
            return $datetime;
        }
    }
}

/**
 * Get WIB timezone object
 * 
 * @return DateTimeZone
 */
if (!function_exists('get_wib_timezone')) {
    function get_wib_timezone() {
        try {
            return new DateTimeZone('Asia/Jakarta');
        } catch (Exception $e) {
            return new DateTimeZone('UTC');
        }
    }
}

/**
 * Check if datetime string is in WIB timezone
 * 
 * @param string $datetime Datetime string
 * @return bool
 */
if (!function_exists('is_wib_time')) {
    function is_wib_time($datetime) {
        try {
            $date = new DateTime($datetime);
            $timezone = $date->getTimezone();
            return $timezone->getName() === 'Asia/Jakarta';
        } catch (Exception $e) {
            return false;
        }
    }
}

/**
 * Get current timestamp in WIB
 * 
 * @return int Unix timestamp
 */
if (!function_exists('wib_timestamp')) {
    function wib_timestamp() {
        try {
            $date = new DateTime('now', new DateTimeZone('Asia/Jakarta'));
            return $date->getTimestamp();
        } catch (Exception $e) {
            return time();
        }
    }
}

/**
 * Get date only (Y-m-d) in WIB
 * 
 * @return string Format: Y-m-d
 */
if (!function_exists('wib_date')) {
    function wib_date() {
        try {
            $date = new DateTime('now', new DateTimeZone('Asia/Jakarta'));
            return $date->format('Y-m-d');
        } catch (Exception $e) {
            return date('Y-m-d', time() + (7 * 3600));
        }
    }
}

/**
 * Get time only (H:i:s) in WIB
 * 
 * @return string Format: H:i:s
 */
if (!function_exists('wib_time')) {
    function wib_time() {
        try {
            $date = new DateTime('now', new DateTimeZone('Asia/Jakarta'));
            return $date->format('H:i:s');
        } catch (Exception $e) {
            return date('H:i:s', time() + (7 * 3600));
        }
    }
}

/**
 * Calculate time difference in hours
 * 
 * @param string $start_datetime Start datetime
 * @param string $end_datetime End datetime (default: now)
 * @return float Hours difference
 */
if (!function_exists('time_diff_hours')) {
    function time_diff_hours($start_datetime, $end_datetime = null) {
        try {
            $start = new DateTime($start_datetime, new DateTimeZone('Asia/Jakarta'));
            $end = $end_datetime 
                ? new DateTime($end_datetime, new DateTimeZone('Asia/Jakarta'))
                : new DateTime('now', new DateTimeZone('Asia/Jakarta'));
            
            $diff = $end->getTimestamp() - $start->getTimestamp();
            return round($diff / 3600, 2);
        } catch (Exception $e) {
            return 0;
        }
    }
}

/**
 * Add hours to datetime
 * 
 * @param string $datetime Base datetime
 * @param int $hours Hours to add
 * @return string New datetime
 */
if (!function_exists('add_hours')) {
    function add_hours($datetime, $hours) {
        try {
            $date = new DateTime($datetime, new DateTimeZone('Asia/Jakarta'));
            $date->modify("+{$hours} hours");
            return $date->format('Y-m-d H:i:s');
        } catch (Exception $e) {
            return $datetime;
        }
    }
}

/**
 * Get yesterday date in WIB
 * 
 * @return string Format: Y-m-d
 */
if (!function_exists('wib_yesterday')) {
    function wib_yesterday() {
        try {
            $date = new DateTime('yesterday', new DateTimeZone('Asia/Jakarta'));
            return $date->format('Y-m-d');
        } catch (Exception $e) {
            return date('Y-m-d', time() - 86400 + (7 * 3600));
        }
    }
}

/**
 * Get tomorrow date in WIB
 * 
 * @return string Format: Y-m-d
 */
if (!function_exists('wib_tomorrow')) {
    function wib_tomorrow() {
        try {
            $date = new DateTime('tomorrow', new DateTimeZone('Asia/Jakarta'));
            return $date->format('Y-m-d');
        } catch (Exception $e) {
            return date('Y-m-d', time() + 86400 + (7 * 3600));
        }
    }
}

/* End of file datetime_helper.php */
/* Location: ./application/helpers/datetime_helper.php */
