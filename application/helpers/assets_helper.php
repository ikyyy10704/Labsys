<?php
if (!function_exists('get_image_url')) {
    function get_image_url($path) {
        return base_url('assets/img/' . $path);
    }
}

if (!function_exists('get_upload_url')) {
    function get_upload_url($path) {
        return base_url('assets/uploads/' . $path);
    }
}