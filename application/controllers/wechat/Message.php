<?php
defined('BASEPATH') OR exit('No direct script access allowed');
header('Content-type:text');
define("TOKEN", "9RmR2c1H33");
class Message extends CI_Controller {
    
    public function index(){     //校驗服務器地址URL
        if (isset($_GET['echostr'])) {
            $this->valid();
        }else{
            $this->responseMsg();
        }
    }
    public function valid()
    {
        $echoStr = $_GET["echostr"];
        if($this->checkSignature()){
            header('content-type:text');
            echo $echoStr;
            exit;
        }else{
            echo $echoStr.'+++'.TOKEN;
            exit;
        }
    }

    function _get_userinfo($openid)
    {
        $access_token = get_access_token();
        $request_url = 'https://api.weixin.qq.com/cgi-bin/user/info?access_token='.$access_token.'&openid='.$openid.'&lang=zh_CN';

        return  $this->_get_request($request_url);
    }

    public function _get_request($url)
    {
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

     function checkSignature()
    {
        $signature = $_GET["signature"];
        $timestamp = $_GET["timestamp"];
        $nonce = $_GET["nonce"];
    
        $token = TOKEN;
        $tmpArr = array($token, $timestamp, $nonce);
        sort($tmpArr, SORT_STRING);
        $tmpStr = implode( $tmpArr );
        $tmpStr = sha1( $tmpStr );
    
        if( $tmpStr == $signature ){
            return true;
        }else{
            return false;
        }
    }

    function xml2array ( $xmlObject, $out = array () )
{
        foreach ( (array) $xmlObject as $index => $node )
            $out[$index] = ( is_object ( $node ) ||  is_array ( $node ) ) ? xml2array ( $node ) : $node;

        return $out;
}


    public function responseMsg(){ 
      $encryptMsg = file_get_contents("php://input");
      $postObj = simplexml_load_string($encryptMsg, 'SimpleXMLElement', LIBXML_NOCDATA);
      $postObj = $this->xml2array($postObj);
      $msg_data['msg_id'] = $postObj['MsgId'];
      $msg_data['msg_type'] = $postObj['MsgType'];
      $msg_data['created'] = $postObj['CreateTime'];
      if ($postObj['MsgType'] == 'image'){
        $msg_data['msg_content'] = $postObj['PicUrl'];
      }else if ($postObj['MsgType'] == 'text'){
        $msg_data['msg_content'] = $postObj['Content'];
      }
      $temp_user = $this->photo->select('users','uid,nickname', array('openid' => $postObj['FromUserName']));
      if ($temp_user){
        $msg_data['uid'] = $temp_user[0]->uid;
        $msg_data['openid'] = $postObj['FromUserName'];
        $msg_data['nickname'] = $temp_user[0]->nickname;
        $this->photo->insert('user_messages', $msg_data);
      }
      
    }
}
