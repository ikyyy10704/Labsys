<?php
defined('BASEPATH') OR exit('No direct script access allowed');


class beranda extends CI_Controller {
    
    public function __construct() {
        parent::__construct();
        $this->load->model('Dashboard_model');
    }
    
    public function index() {
        $data['title'] = 'Dashboard';
        $data['total_karyawan'] = $this->Dashboard_model->count_karyawan();
        $data['total_manajer'] = $this->Dashboard_model->count_manajer();
        $data['total_absensi'] = $this->Dashboard_model->count_absensi();
        $data['total_kinerja'] = $this->Dashboard_model->count_kinerja();
        $data['total_gaji'] = $this->Dashboard_model->count_gaji();
        $data['total_departemen'] = $this->Dashboard_model->count_departemen();
        $data['kinerja_stats'] = $this->Dashboard_model->get_kinerja_statistics();
        
        $this->load->view('template/header', $data);
        $this->load->view('template/sidebar');
        $this->load->view('beranda/index', $data);
        $this->load->view('template/footer');
    }
}