<?php
defined('BASEPATH') OR exit('No direct script access allowed');
 
class Wechat extends CI_Controller{
    function __construct(){
        parent::__construct(); 
    }
	

/*
*   get wechat user's openid according 
*   to wechat js_code
*/
    function get_openid_sessionkey(){
        $appid       = trim($_GET['appid']);
        $secret      = trim($_GET['secret']);
        $js_code     = trim($_GET['js_code']);
        $grant_type  = trim($_GET['grant_type']);
        $request_url = "https://api.weixin.qq.com/sns/jscode2session?appid=".$appid."&secret=".$secret."&js_code=".$js_code."&grant_type=".$grant_type;

        $result = $this->_get_request($request_url);
        echo json_encode($result);exit;
    }


    public function _get_request($url){
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        $output = curl_exec($ch);
        curl_close($ch);
        $jsoninfo = json_decode($output, true);

        return $jsoninfo;
    }
}	