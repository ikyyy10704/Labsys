<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class About extends CI_Controller {
    
    public function __construct() {
        parent::__construct();
        $this->load->helper('url');
    }

    public function index() {
        $data['title'] = 'About Us';
        $data['slides'] = [
            'assets/img/carousel/1.png',
            'assets/img/carousel/2.png',
            'assets/img/carousel/3.png',
            'assets/img/carousel/4.png'
        ];
        
        $this->load->view('template/header', $data);
        $this->load->view('about/index', $data);
        $this->load->view('template/footer');
    }
}