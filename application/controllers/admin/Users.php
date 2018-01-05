<?php
defined('BASEPATH') OR exit('No direct script access allowed');
 
class Users extends MY_Controller{
    function __construct(){
        parent::__construct(); 
    }
	
    function index(){
        $users = $this->photo->select('users', '*', '', '', '', array('created' => 'desc'));
        foreach ($users as $key => $value) {
        	$users[$key]->nickname = base64_decode($value->nickname);
            $agent_nickname = "无";
            if( $value->agent_id != 0 ){
                $temp_agent = $this->photo->select('users','nickname',array('uid' => $value->agent_id));
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
                $result = $this->photo->update('users', array('pay_status' => 2), array('uid' => $uid ));

                if ( $result ) {
                    echo json_encode(array('code'=>0, 'msg'=>'操作成功'));exit;
                }else{
                    echo json_encode(array('code'=>10002, 'msg'=>'操作失败'));exit;
                }
            }
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
        $user = $this->photo->select('users','agent_status',array('uid' => $uid));
        if( !$user ){
            echo json_encode(array('code'=>10001, 'msg'=>'用户不存在'));exit;
        }else{
            $user = $user[0];
            if ( $user->agent_status == 2 ){
                echo json_encode(array('code'=>10000, 'msg'=>'用户审核已被拒绝'));exit;
            }else if ( $user->agent_status == 1 ){
                echo json_encode(array('code'=>10000, 'msg'=>'用户审核已通过'));exit;
            }else{

                if ( $type == 'pass' ){
                    $this->_apply_formated($uid, 1);
                }else {
                    $reason = $this->input->post('reason');
                    $this->_apply_formated($uid, 2, $reason);
                }
            }
        }
    }
 
}	