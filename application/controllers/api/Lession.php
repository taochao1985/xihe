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
        $types = $this->type();
        if( $type_id == 0 ){
            $type_id = $types[0]->id; 
        }
        $cond = array('lt_id' => $type_id );
        $lession_type = $this->photo->select('lession_type', 'lession_order', array('id' => $type_id));
        if ( $lession_type ){
            $order = $lession_type[0]->lession_order;
        }
        $lessiones = $this->get_lession($cond, $order);
        echo json_encode(array('code' => 0, 'msg' => '操作成功', 'data' => array('lessions' => $lessiones, 'types'=>$types, 'type_id' => $type_id)));exit;
    }


/*
*   redeal lession struct
*/
    function deal_lessions($lessions){ 
        if( count($lessions) > 0 ){
            foreach ( $lessions as $key => $val ){
                $lessions[$key]->image_path = base_url().$val->image_path;
                $lessions[$key]->created    = date('Y-m-d', $val->created);
            }
        }
        return $lessions;
    }

// get lession list according to some conditions
    function get_lession($cond, $order=""){
        $cond['start_time <='] = time(); 
        $lessions = $this->photo->select_lessions_join_attachments($cond, $order); 
        return $this->deal_lessions($lessions);
    }

//get all lession types
    function type(){
        $type = $this->photo->select('lession_type', '', '', '', '', array('sort' => 'desc'));
        return $type;
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
        // if (( count($lession_views)== 6 ) && ( !$userinfo->pay_status)){
        if( !$userinfo->pay_status ){
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
            //$params = $this->_create_pay($uid);
            echo json_encode(array('code' => 10000, 'msg' => '用户未支付', 'data' => []));exit;
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