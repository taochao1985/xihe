<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Main extends MY_Controller {

    function websocket(){
      echo date('Y-m-d H:i:s', '1516869853');
    }
    
    function _get_count($table, $cond=array()){
      return $this->photo->select_count_where($table, $cond);
    }

    function _get_cacu_user(){
      $today = date('Y-m-d');
      $today_start = strtotime($today.' 00:00:00');
      $today_end   = strtotime($today.' 23:59:59');
      $date_data = array();
      $publish_data = array();
      $user_data = array();
      $member_data = array();
      $agent_data = array();
      $agent_member = array();
      for ( $i = 6 ; $i > 0 ; $i-- ){
        $date = date('Y-m-d',$today_start - $i * 3600 * 24);
        $count = $this->_get_count('users',
          array(
            'created >=' => $today_start - $i * 3600 * 24,
            'created <=' => $today_end - $i * 3600 * 24, 
          )
        );
        $pub_count = $this->_get_count('publishes',
          array(
            'created >=' => $today_start - $i * 3600 * 24,
            'created <=' => $today_end - $i * 3600 * 24, 
          )
        );

        $member_count = $this->_get_count('users',
          array(
            'pay_time >=' => $today_start - $i * 3600 * 24,
            'pay_time <=' => $today_end - $i * 3600 * 24, 
            'pay_status'  => 1
          )
        );

        $agent_count = $this->_get_count('user_agent_apply',
          array(
            'created >=' => $today_start - $i * 3600 * 24,
            'created <=' => $today_end - $i * 3600 * 24
          )
        );

        $agent_member_count = $this->_get_count('users',
          array(
            'pay_time >=' => $today_start - $i * 3600 * 24,
            'pay_time <=' => $today_end - $i * 3600 * 24, 
            'pay_status'  => 1,
            'agent_id !=' => 0
          )
        );

        $date_data[]= $date;
        $user_data[] = $count;
        $publish_data[] = $pub_count;
        $member_data[] = $member_count;
        $agent_data[] = $agent_count;
        $agent_member[] = $agent_member_count;
      }
      return array(
        'cacu_date' => $date_data,
        'user_data' => $user_data,
        'publish_data' => $publish_data,
        'member_data' => $member_data,
        'agent_data' => $agent_data,
        'agent_member_data' => $agent_member
      );
    }

    function index(){
      $today = date('Y-m-d');
      $yesterday_start = strtotime($today.' 00:00:00') - 3600*24;
      $yesterday_end   = strtotime($today.' 23:59:59') - 3600*24;
      $data['user_count'] = $this->_get_count('users');
      $data['yesterday_user_count'] = $this->_get_count('users', array(
            'created >=' => $yesterday_start,
            'created <=' => $yesterday_end 
          ));

      $data['member_count'] = $this->_get_count('users', array('pay_status' => 1));
      $data['yesterday_member_count'] = $this->_get_count('users', array(
            'pay_time >=' => $yesterday_start,
            'pay_time <=' => $yesterday_end, 
            'pay_status' => 1
          ));
      $data['publish_count'] = $this->_get_count('publishes');
      $data['yesterday_publish_count'] = $this->_get_count('publishes',
          array(
            'created >=' => $yesterday_start,
            'created <=' => $yesterday_end, 
          )
        );
      $data['agent_count'] = $this->_get_count('user_agent_apply');
      $data['yesterday_agent_count'] = $this->_get_count('user_agent_apply',
          array(
            'created >=' => $yesterday_start,
            'created <=' => $yesterday_end
          )
        );

      $data['agent_member_count'] = $this->_get_count('users',
          array(
            'pay_status'  => 1,
            'agent_id !=' => 0
          )
        );
        $data['yesterday_agent_member_count'] = $this->_get_count('users',
          array(
            'pay_time >=' => $yesterday_start,
            'pay_time <=' => $yesterday_end, 
            'pay_status'  => 1,
            'agent_id !=' => 0
          )
        );

        $data['comment_count'] = $this->_get_count('user_comments');
        $data['yesterday_comment_count'] = $this->_get_count('user_comments',
          array(
            'created >=' => $yesterday_start,
            'created <=' => $yesterday_end
          )
        );

      $this->load->view('/admin/main/index',$data);
    }

    function get_data(){
      $user_data = $this->_get_cacu_user();
      $user_data['code'] = 0;
      echo json_encode($user_data);exit;
    }
}