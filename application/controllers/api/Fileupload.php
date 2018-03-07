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

    public function dealthumb($path){
        $config['image_library'] = 'gd2';
        $config['source_image'] = $path;
        $config['create_thumb'] = TRUE;
        //生成的缩略图将在保持纵横比例 在宽度和高度上接近所设定的width和height
        $config['new_image'] = $path;
        $config['width'] = 360;
        $this->load->library('image_lib', $config);
        $this->image_lib->resize();
        $this->image_lib->clear();
    }   
    
       
    function index(){    
        $new_name                = $this->rename_file($_FILES['userfile']['name']);
        $file_type               = isset($_POST['file_type'])?trim($_POST['file_type']):'images'; 
        $target_folder           = '/uploads/'.$file_type.'/';
        $config_path             = '.'.$target_folder;
        $config['upload_path']   = $config_path;
        $config['file_name']     = $new_name;
        $config['allowed_types'] = 'mp3|mov|jpg|jpeg|png|mp4';  
        $config['max_width']     = '';
        $config['max_height']    = '';

        $this->load->library('upload', $config);  
        if ( !$this->upload->do_upload('userfile')){
            $error = $this->upload->display_errors();
            echo json_encode(array('errno'=>10001,'msg'=>$error));exit;
        }else{
            $data = $this->upload->data();
            if($data['is_image'] == 1) {
                $this->dealthumb($data['full_path']);
            }
            $data['final_path'] = $target_folder; 
            echo json_encode(array('errno'=>0,'msg'=>'success','data'=>$data));exit;
        }
    }
}   