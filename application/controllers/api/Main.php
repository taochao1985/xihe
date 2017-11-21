<?php
defined('BASEPATH') OR exit('No direct script access allowed');
 
class Main extends CI_Controller{
    function __construct(){
        parent::__construct(); 
    }
	
    function index(){
        $slider_images = $this->cache->memcached->get('slider_images');
        echo json_encode(array(
            'slider_images' => $slider_images
        ));
        exit;
    }
}	