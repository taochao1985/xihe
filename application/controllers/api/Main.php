<?php
defined('BASEPATH') OR exit('No direct script access allowed');
 
class Main extends CI_Controller{
    function __construct(){
        parent::__construct(); 
    }
	
    function index(){
        $this->load->driver('cache');
        $result = $this->cache->memcached->save('foo', 'bar', 10); 

        $cached = $this->cache->memcached->get('foo');

        echo '<pre>';print_r($cached);
    }
}	