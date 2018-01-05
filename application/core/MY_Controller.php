<?php 
class MY_Controller extends CI_Controller
{
    public function __construct()
    {
        parent::__construct();
        // /*判断是否登录，判断当前URL是否是auth/login*/
         $user = $this->session->userdata('admin');
        if($user==""){
            //直接跳转到登录页面
            redirect('/admin/login');exit;
        }
    }
}