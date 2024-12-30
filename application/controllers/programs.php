<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Programs extends CI_Controller {
    public function __construct() {
        parent::__construct();
        $this->load->helper('url');
    }

    public function index() {
        $data['title'] = 'Programs';
        $this->load->view('template/header', $data);
        $this->load->view('template/sidebar');
        $this->load->view('programs/index');
        $this->load->view('template/footer');
    }
}