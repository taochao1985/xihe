<?php
defined('BASEPATH') OR exit('No direct script access allowed');
 
class Lession extends CI_Controller{
    function __construct(){
        parent::__construct(); 
    }

/*
*   record user lession shared history
*/
    function shared(){
        $uid     = $this->input->post('uid');
        $post_id = $this->input->post('post_id');

        $share_data = array(
            'uid'     => $uid,
            'post_id' => $post_id,
            'created' => time()
        );
        $this->photo->insert('lession_share_log', $share_data);
    }


/*
*   get lession list according to lession category id
*/
    function list(){
        $type_id = $this->input->get('lt_id');
        $uid = $this->input->get('uid');
        $types = $this->type();
        if( $type_id == 0 ){
            $type_id = $types[0]->id; 
        }
        $cond = array('lt_id' => $type_id );
        $desc = '';
        $lession_type = $this->photo->select('lession_type', 'lession_order, desc', array('id' => $type_id));
        if ( $lession_type ){
            $order = $lession_type[0]->lession_order;
            $desc  = $lession_type[0]->desc;
            $desc = str_replace('/ueditor/php',base_url().'ueditor/php', $desc);
        }
        $lessiones = $this->get_lession($cond, $order, $uid, "lessions.title, lessions.created, lessions.id");
        echo json_encode(array('code' => 0, 'msg' => '操作成功', 'data' => array('lessions' => $lessiones, 'types'=>$types, 'desc'=>$desc, 'type_id' => $type_id)));exit;
    }


/*
*   redeal lession struct
*/
    function deal_lessions($lessions, $uid, $fields){ 
        if( count($lessions) > 0 ){
            foreach ( $lessions as $key => $val ){
                $lessions[$key]->image_path = base_url().get_rename_image($val->image_path);
                $lessions[$key]->created    = date('Y-m-d', $val->created);
                if ($fields == "lessions.*"){
                    $lessions[$key]->description = str_replace('/ueditor/php',base_url().'ueditor/php', $val->description);
                }
                
                $read_flag = 1;
                if ( $uid ){
                    $read_check = $this->photo->photo->select('lession_views','id', array('uid' => $uid, 'l_id' => $val->id));
                    if ( !$read_check ){
                        $read_flag = 0;
                    }
                }
                $lessions[$key]->is_read = $read_flag;
            }
        }
        return $lessions;
    }

// get lession list according to some conditions
    function get_lession($cond, $order="", $uid=0, $fileds="lessions.*"){
        $cond['start_time <='] = time(); 
        $lessions = $this->photo->select_lessions_join_attachments($cond, $order, $fileds); 
        return $this->deal_lessions($lessions, $uid, $fileds);
    }

//get all lession types
    function type(){
        $type = $this->photo->select('lession_type', '', '', '', '', array('sort' => 'desc'));
        return $type;
    }

    function check_member_valid ($userinfo) {
        $member_valid = 0;
        $pay_status = $userinfo->pay_status;
        $uid = $userinfo->uid;
        if ( $pay_status == 1 ){
            $payed_order = $this->photo->select('user_pay_orders', 'updated', array('uid' => $uid, 'status' => 1), '','',array('updated' => 'asc'));
            $invalid_time = $payed_order[0]->updated + 3600 * 24 *365 * count($payed_order);
            if ( time() < $invalid_time ){
                //会员有效
                $member_valid = 1;
            }
        }else if ( $pay_status == 2 ){
            $pay_time = $userinfo->pay_time;
            if ( !$pay_time ){
                $pay_time = time();
                $this->photo->update('users', array('pay_time' => $pay_time), array('uid' => $uid));
            }
            $invalid_time = $pay_time + 3600 * 24 *365 ;
            if ( time() < $invalid_time ){
                //会员有效
                $member_valid = 1;
            }
        }
        return $member_valid;
    }
/*
*   check user read lession count and user's pay status
*   if user's read count equals 6 and user have not payed return false
*   or return true
*/
    function _lession_user_check($uid, $id){
        if ( $uid == 0 ){
            return true;
        }
        $sql = "select distinct l_id from lession_views where uid = '". $uid ."' and l_id != '". $id ."'";
        $lession_views = $this->photo->personal_select($sql); 

        $userinfo = get_common_userinfo($this->photo, $uid);
        // $register_time = $userinfo->created;
        // $registered_time = time() - $register_time - 7 * 3600 * 24;
        $member_check = $this->check_member_valid($userinfo);
        if ((( count($lession_views) >= 2 ) && ( !$userinfo->pay_status )) || !$member_check ){
        //if (( $registered_time > 0 ) && ( !$userinfo->pay_status )){
            return false; 
        }else{
            $lession_data = array(
                'uid'     => $uid,
                'l_id'    => $id,
                'created' => time()
            );

            $this->photo->insert('lession_views',$lession_data);
            return true;
        }
    }

/*
*   get lession detail according to lession id
*/	  
    function show(){
        $id  = $this->uri->segment(4); 
        $uid = $this->uri->segment(5);

        $lession_user_check = $this->_lession_user_check($uid, $id);
        if( !$lession_user_check ){
            // $params = $this->_create_pay($uid);
            $agent_apply_news = $this->photo->select('news', 'description, title', array('id' => 3));
            $description = $agent_apply_news[0]->description;
            $apply_title = $agent_apply_news[0]->title;
            echo json_encode(array('code' => 10000, 'msg' => '用户未支付', 'data' => [], 'description' => $description, 'title' => $apply_title));exit;
        }else{
            $lession = $this->get_lession(array('lessions.id'=>$id)); 
            if( $lession ){
                echo json_encode(array('code' => 0, 'msg' => '操作成功', 'data' => $lession[0]));exit;
            }else{
                echo json_encode(array('code' => 10001, 'msg' => '课程不存在', 'data' => []));exit;
            }
        }
    }
}	