<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Fileupload extends CI_Controller {
    function __construct(){
        parent::__construct(); 
    }

    function generate_code($len = 10){
        $chars = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
        for ($i = 0, $count = strlen($chars); $i < $count; $i++){
            $arr[$i] = $chars[$i];
        }

        mt_srand((double) microtime() * 1000000);
        shuffle($arr);
        $code = substr(implode('', $arr), 5, $len);
        return $code;
    }

    function get_ext($name){
        $name = explode('.', $name);
        return $name[count($name)-1];
    }

    function rename_file($origin_name) {
        $ext    = $this->get_ext($origin_name); 
        $random =rand(1000,10000);
        $random.=$this->generate_code();
        $filename = date("Ymd", time()).$random.'.'.$ext; 
        return $filename;
    } 
       
    function index(){    
        $new_name                = $this->rename_file($_FILES['userfile']['name']);
        $file_type               = isset($_POST['file_type'])?trim($_POST['file_type']):'images'; 
        $target_folder           = '/uploads/'.$file_type.'/';
        $config_path             = '.'.$target_folder;
        $config['upload_path']   = $config_path;
        $config['file_name']     = $new_name;
        $config['allowed_types'] = '*';  
        $config['max_width']     = '';
        $config['max_height']    = '';

        $this->load->library('upload', $config);  
        if ( !$this->upload->do_upload('userfile')){
            $error = $this->upload->display_errors();
            echo json_encode(array('errno'=>10001,'msg'=>$error));exit;
        }else{
            $data = $this->upload->data();
            $data['final_path'] = $target_folder; 
            echo json_encode(array('errno'=>0,'msg'=>'success','data'=>$data));exit;
        }
    }

    function set_upload_options($config_path){ 
    //upload an image options
         $config = array();
         $config['upload_path'] = $config_path;
         $config['allowed_types'] = '*'; 
         return $config;
    }

    function mluti_upload(){
        $file_type               = isset($_POST['file_type'])?trim($_POST['file_type']):'images'; 
        $target_folder           = '/uploads/'.$file_type.'/';
        $config_path             = '.'.$target_folder;
        $this->load->library('upload');
        
        // $this->load->library('upload', $config); 
        $files = $_FILES;
        $cpt = count($_FILES['userfile']['name']); 
        // if( $cpt > 1){
            $final_path = [];
            for($i=0; $i<$cpt; $i++){
                $_FILES['userfile']['name']= $files['userfile']['name'][$i];
                $_FILES['userfile']['type']= $files['userfile']['type'][$i];
                $_FILES['userfile']['tmp_name']= $files['userfile']['tmp_name'][$i];
                $_FILES['userfile']['error']= $files['userfile']['error'][$i];
                $_FILES['userfile']['size']= $files['userfile']['size'][$i]; 
                $this->upload->initialize($this->set_upload_options($config_path));
                $this->upload->do_upload();
                $data = $this->upload->data();
                $final_path[] = $data;
            }
            
            $data_final['final_data'] = $final_path;
            $data_final['final_path'] = $target_folder; 
            echo json_encode(array('errno'=>0,'msg'=>'success','data'=>$data_final));exit;
    }
}   