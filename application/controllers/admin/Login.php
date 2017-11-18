<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Login extends CI_Controller {
    function __construct(){
        parent::__construct(); 
    }
	   
    function index(){
        if ($_POST){
            $username = trim($_POST['username']);
            $password = trim($_POST['password']);
            $re = $this->photo->select('admin','*',array('username'=>$username,'password'=>md5($password)));
            if ($re){ 
                $this->session->set_userdata('admin',$re);
                $r = array('success'=>'yes');

            }else{
                $r = array('success'=>'no');
            }
            $user = $this->session->userdata('admin');
            echo json_encode($r);exit;

        }else{
            $this->load->view('admin/login/index');
        }
    }

    function logout(){
        $user = $this->session->userdata('admin');
        if($user==""){
            //直接跳转到登录页面
            redirect('admin/login');exit;
        }else{
            $this->session->sess_destroy();
            redirect('admin/login');exit;
        }
    }

}	