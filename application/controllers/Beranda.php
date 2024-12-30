<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Beranda extends CI_Controller {
    public function __construct() {
        parent::__construct();
        $this->load->helper('url');
    }

    public function index() {
        $data['title'] = 'Beranda';
        $this->load->view('template/header', $data);
        $this->load->view('template/sidebar');
        $this->load->view('beranda/index');
        $this->load->view('template/footer');
    }
}
