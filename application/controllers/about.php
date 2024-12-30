<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class About extends CI_Controller {
    public function __construct() {
        parent::__construct();
    }

    public function index() {
        $data['title'] = 'About Us';
        $this->load->view('template/header', $data);
        $this->load->view('template/sidebar');
        $this->load->view('about/index');
        $this->load->view('template/footer');
    }
}