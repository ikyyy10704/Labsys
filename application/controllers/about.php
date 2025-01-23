<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class About extends CI_Controller {
   public function __construct() {
       parent::__construct(); 
       $this->load->helper('url');
   }

   public function index() {
    $data['slides'] = [
        'assets/img/carousel/1.png',
        'assets/img/carousel/2.png',
        'assets/img/carousel/3.png',
        'assets/img/carousel/4.png'
    ];

       $data['title'] = 'About Us';
       $this->load->view('about/index', $data);
   }
}