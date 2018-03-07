<?php
defined('BASEPATH') OR exit('No direct script access allowed');
 
class Users extends MY_Controller{
    function __construct(){
        parent::__construct(); 
    }

    function messages(){
        $page = $this->uri->segment(4);

        $perpage = 10;
        if($page==""){
            $page=1;
        } 
        $start = ($page-1)*$perpage;
        $where = array();
        $like = array(); 
        $total = $this->photo->select_count_where('user_messages', $where);
        $data['total'] = ceil($total/$perpage);
        $data['current_page'] = $page;
        
        $messages = $this->photo->select('user_messages', '*', $where,$perpage, $start, array('created' => 'desc'), $like);
        foreach ($messages as $key => $value) { 
            $messages[$key]->nickname = base64_decode($value->nickname);
            $reply = $this->photo->select('user_message_reply','*', array('mid'=>$value->id));
            $messages[$key]->reply = $reply;
        }
        $data['messages'] = $messages;
        $this->load->view('admin/users/messages', $data);
    }

    function _send_reply($openid, $content){
        $access_token = get_access_token();
        if ( $access_token ){
            $post_data='{"touser":"'.$openid.'","msgtype":"text","text":{"content":"'.$content.'"}}';
            $url="https://api.weixin.qq.com/cgi-bin/message/custom/send?access_token=".$access_token;
            $result=api_notice_increment($url,$post_data);
            return $result;
        }else{
            return false;
        }    
    }

    function message_reply(){
        $id = $this->input->post('id');
        $msg_id = $this->input->post('msg_id');
        $openid = $this->input->post('openid');
        $content = $this->input->post('content');

        $reply_data = array(
            'mid' => $id,
            'msg_id' => $msg_id,
            'content' => $content,
            'created' => time()
        );

        $result = $this->photo->insert('user_message_reply', $reply_data);
        $result1 = $this->_send_reply($openid, $content);

        $this->photo->translate_begin();
        if( $result && $result1 ){
            $this->photo->translate_commit();
            echo json_encode(array('code'=>0, 'msg'=>'操作成功'));exit;
        }else{
            $this->photo->translate_rollback();
            echo json_encode(array('code'=>10002, 'msg'=>'操作失败'));exit;
        }
    }
	
    function agent_records() {
        $uid = $this->uri->segment(4);
        $cond = array('pay_status' => 1);
        $userinfo = array();
        if ( $uid ){
            $userinfo = get_common_userinfo($this->photo, $uid);
            $cond = array('agent_id'=> $uid, 'pay_status' => 1);
        }
        $users = $this->photo->select('users','nickname,agent_id,uid, pay_time, pay_status', $cond, '', '', array('pay_time' => 'desc'));
        if ($users){
            foreach( $users as $key => $val){
                if( $val->nickname ){ 
                    $val->nickname = base64_decode($val->nickname);
                    $users[$key] = $val;
                }
            }
        }
        $data['users'] = $users;
        $data['userinfo'] = $userinfo;
        $this->load->view('admin/users/agent_records', $data);
    }

    function index(){
        $page = $this->uri->segment(4);

        $perpage = 10;
        if($page==""){
            $page=1;
        }

        $start_time = isset($_GET['start_time'])?$_GET['start_time']:'';
        $end_time = isset($_GET['end_time'])?$_GET['end_time']:'';
        $nickname = isset($_GET['nickname'])?$_GET['nickname']:'';
        $agent_user = isset($_GET['agent_user'])?$_GET['agent_user']:'-1';
        $data['start_time'] = $start_time;
        $data['end_time']   = $end_time;
        $data['nickname']   = $nickname;
        $data['agent_user'] = $agent_user;
        $start = ($page-1)*$perpage;
        $where = array();
        $like = array();
        $or_like = array();
        if ( $start_time ){
            $where['created >='] = strtotime($start_time.' 00:00:00');
        }
        if ( $end_time ){
            $where['created <='] = strtotime($end_time.' 23:59:59');
        }

        
        $where_in = array();
        if ( $agent_user >= 0 ){
            $agent_user_array = array();
            $agent_records = $this->photo->select('user_agent_apply','uid', array('status'=>$agent_user), '', array('created' => 'desc'));
            foreach( $agent_records as $k => $v ){
                $agent_user_array[] = $v->uid;
            }
            if ( count($agent_user_array) > 0 ){
                $where_in = array(
                    'uid' => $agent_user_array
                );
            }else{
                $where_in = array(
                    'uid' => array(-1)
                );
            }
        }

        if ( $nickname ){
            $like['origin_name'] = $nickname;
            $or_like['wechat_num'] = $nickname;
            $or_like['mobile'] = $nickname;
        }

        $total = $this->photo->select_count_where('users', $where, $like, $or_like, $where_in);
        $create_time = date('Y-m-d');
        $pay_where = array('pay_status !='=>0);
        $agent_where = array('agent_status '=>1);

        $data['search_count'] = $this->photo->select_count_where('users', $where, $like, $or_like, $where_in);
        $data['search_member_count'] = $this->photo->select_count_where('users', array_merge($where, $pay_where), $like, $or_like, $where_in);
        $data['search_agent_count'] = $this->photo->select_count_where('users', array_merge($where, $agent_where), $like, $or_like, $where_in);

        $data['new_count'] = $this->photo->select_count_where('users', array('created >= ' => strtotime($create_time)));
        $data['new_member_count'] = $this->photo->select_count_where('users', array('pay_status !='=>0,'created >= ' => strtotime($create_time)));
        $data['new_agent_count'] = $this->photo->select_count_where('users', array('agent_status '=>1,'created >= ' => strtotime($create_time)));

        $data['user_count'] = $this->photo->select_count_where('users');
        $data['user_member_count'] = $this->photo->select_count_where('users', array('pay_status !='=>0));
        $data['user_agent_count'] = $this->photo->select_count_where('users', array('agent_status '=>1));

        $data['total'] = ceil($total/$perpage);
        $data['current_page'] = $page;
        
        $users = $this->photo->select('users', '*', $where,$perpage, $start, array('created' => 'desc'), $like, $or_like, $where_in);
        foreach ($users as $key => $value) {
        	$users[$key]->nickname = base64_decode($value->nickname);
            $agent_nickname = "无";
            if( $value->agent_id != 0 ){
                $temp_agent = $this->photo->select('users','nickname',array('uid' => $value->agent_id, 'pay_status' => 1));
                if ( $temp_agent ){
                    $agent_nickname = base64_decode($temp_agent[0]->nickname);
                }
            }
            $users[$key]->agent_nickname = $agent_nickname;

            $agent_apply_check = $this->photo->select('user_agent_apply','*', array('uid' => $value->uid));
            if ( $agent_apply_check ){
                $users[$key]->agent_apply = 1;
                $users[$key]->reason = $agent_apply_check[0]->refuse_reason;
            }else{
                $users[$key]->agent_apply = 0;
                $users[$key]->reason = "";
            }
        }
        $data['users'] = $users;
        $this->load->view('admin/users/index', $data);
    }

    function form_user(){
        $uid = $this->input->post('id');
        $user = $this->photo->select('users','pay_status',array('uid' => $uid));
        if( !$user ){
            echo json_encode(array('code'=>10001, 'msg'=>'用户不存在'));exit;
        }else{
            $user = $user[0];
            if ( $user->pay_status != 0 ){
                echo json_encode(array('code'=>10000, 'msg'=>'用户已是会员'));exit;
            }else{
                $result = $this->photo->update('users', array('pay_status' => 2, 'pay_time' => time()), array('uid' => $uid ));

                if ( $result ) {
                    echo json_encode(array('code'=>0, 'msg'=>'操作成功'));exit;
                }else{
                    echo json_encode(array('code'=>10002, 'msg'=>'操作失败'));exit;
                }
            }
        }
    }

    function form_user_delete(){
        $uid = $this->input->post('id');
        $user = $this->photo->select('users','pay_status',array('uid' => $uid));
        if( !$user ){
            echo json_encode(array('code'=>10001, 'msg'=>'用户不存在'));exit;
        }else{
            $user = $user[0];
            if ( $user->pay_status == 0 ){
                echo json_encode(array('code'=>10000, 'msg'=>'用户不是会员'));exit;
            }else{
                $result = $this->photo->update('users', array('pay_status' => 0, 'pay_time' => ''), array('uid' => $uid ));

                if ( $result ) {
                    echo json_encode(array('code'=>0, 'msg'=>'操作成功'));exit;
                }else{
                    echo json_encode(array('code'=>10002, 'msg'=>'操作失败'));exit;
                }
            }
        }
    }

    function _generate_qrcode( $uid ){
        $path="pages/index/index?agent_uid=".$uid;
        $width=430;
        $post_data='{"path":"'.$path.'","width":'.$width.'}';
        $access_token = get_access_token();
        if ( $access_token ){
            $url="https://api.weixin.qq.com/wxa/getwxacode?access_token=".$access_token;
            $result=api_notice_increment($url,$post_data);
            $code_url = "assets/qrcodes/qrcode_".$uid.".png";
            file_put_contents(FCPATH.$code_url, $result);
            $this->photo->update('users', array('agent_qrcode' => $code_url ), array('uid' => $uid));
            return true;
        }else {
            return false;
        }
    }

    function _apply_formated( $uid, $int, $reason = "" ){
        $this->photo->translate_begin();

        $result  = $this->photo->update('users', array('agent_status' => $int), array('uid' => $uid ));
        $result1 = $this->photo->update('user_agent_apply', array('status' => $int, 'refuse_reason' => $reason), array('uid' => $uid ));

        if( $result && $result1 ){
            $this->photo->translate_commit();
            echo json_encode(array('code'=>0, 'msg'=>'操作成功'));exit;
        }else{
            $this->photo->translate_rollback();
            echo json_encode(array('code'=>10002, 'msg'=>'操作失败'));exit;
        }
    }

    function apply_formated(){
        $uid  = $this->input->post('uid');
        $type = $this->input->post('type');
        $user = $this->photo->select('users','agent_status, avatarurl, agent_qrcode',array('uid' => $uid));
        if( !$user ){
            echo json_encode(array('code'=>10001, 'msg'=>'用户不存在'));exit;
        }else{
            $user = $user[0];
            if ( $user->agent_status == 1 ){
                echo json_encode(array('code'=>10000, 'msg'=>'用户审核已通过'));exit;
            }else{

                if ( $type == 'pass' ){
                    $resul = $this->_generate_qrcode($uid);
                    if ( $resul ){
                        $this->_apply_formated($uid, 1);
                    }else{
                        echo json_encode(array('code'=>10000, 'msg'=>'操作失败，请稍后再试'));exit;
                    }
                }else {
                    $reason = $this->input->post('reason');
                    $this->_apply_formated($uid, 2, $reason);
                }
            }
        }
    }
}	