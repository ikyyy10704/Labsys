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
        $data['salary_stats'] = $this->Dashboard_model->get_salary_by_department();
        $data['top_performers'] = $this->Dashboard_model->get_top_performers();
        $data['performance_distribution'] = $this->Dashboard_model->get_performance_distribution();
        
        $this->load->view('template/header', $data);
        $this->load->view('template/sidebar');
        $this->load->view('beranda/index', $data);
        $this->load->view('template/footer');
    }
}