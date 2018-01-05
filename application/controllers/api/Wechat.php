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
        $js_code     = $this->input->post('js_code');
        $grant_type  = $this->input->post('grant_type');
        $agent_id    = $this->input->post('agent_uid');
        $request_url = "https://api.weixin.qq.com/sns/jscode2session?appid=".MINIPROGRAM_APPID."&secret=".MINIPROGRAM_SECRET."&js_code=".$js_code."&grant_type=".$grant_type;
        $result = $this->_get_request($request_url);

        if (!isset($result['unionid'])){
            $result['unionid'] = "";
        }
        $result['agent_id'] = $agent_id; 
        $uid = $this->create_user($result);
        $info = get_common_userinfo($this->photo, $uid);
        $result['uid'] = $uid;
        $result['userinfo'] = $info->userinfo;
        echo json_encode($result);exit;
    }

/*
*   create user according to openid and unionid
*/
    function create_user($info){
        $user_check = $this->photo->select('users','uid',array('openid' => $info['openid']));
        if( !$user_check ){
            $user_data = array('openid' => $info['openid'], 'agent_id' => $info['agent_id'], 'unionid' => $info['unionid'],'created' => time() );
            $uid = $this->photo->insert('users', $user_data);
        }else{
            $this->photo->update('users', array('openid' => $info['openid'],'unionid' => $info['unionid'],'updated' => time()), array('openid' => $info['openid']));
            $uid = $user_check[0]->uid;
        }
        return $uid;
    }    

/*
*   save userinfo
*   check wether user exist, if exist then update
*   or create a new user
*/    
    function update_userinfo(){
        $userinfo       = $this->input->post('userinfo');
        $unionid        = $this->input->post('unionid');
        $openid         = $this->input->post('openid');
        $encrypteddata  = $this->input->post('encrypteddata');
        $decode_user    = json_decode($userinfo);
        $user_data = array(
            'userinfo'      => base64_encode($userinfo),
            'unionid'       => $unionid,
            'encrypteddata' => $encrypteddata,
            'openid'        => $openid,
            'nickname'      => base64_encode($decode_user->nickName),
            'avatarurl'     => $decode_user->avatarUrl 
        );
        
        $user_check     = $this->photo->select('users','uid',array('openid' => $openid));
        if ( count($user_check) > 0 ){
            $user_data['updated'] = time();
            $result = $this->photo->update('users', $user_data, array('openid' => $openid));
            $uid = $user_check[0]->uid;
        }else{
            $user_data['created'] = time();
            $uid = $this->photo->insert('users', $user_data);
        }
        if( $uid ){
            $userinfo = get_common_userinfo($this->photo, $uid);
            echo json_encode(array('code'=>0, 'msg'=>'操作成功', 'data'=>$userinfo));exit;
        }else{
            echo json_encode(array('code'=>10001, 'msg'=>'操作失败'));exit;
        }
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