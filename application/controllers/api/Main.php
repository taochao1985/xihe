<?php
defined('BASEPATH') OR exit('No direct script access allowed');
 
class Main extends CI_Controller{
    function __construct(){
        parent::__construct(); 
    }
	
    function index(){
        $uid = $this->input->get('uid');
        $slider_images = $this->cache->memcached->get('slider_images');
        if ( !$slider_images ){
            $slider_images = $this->photo->select('slider_images');
            $this->cache->memcached->save('slider_images',$slider_images, 60*60*24*29);
        }
        $lession_type  = $this->photo->select('lession_type', '', '', '', '', array('sort' => 'desc'));
        
        $publishes_count = $this->photo->select_count_where('publishes'); 
        $publishes = $this->_get_publishes(0, 3, $uid);
        $collects = get_collect_stack_list($this->photo, $uid);
        $collect_count = $this->photo->select_collect_count();

        $follows  = $this->photo->select_follows();
        $follows = reoragnize_follows($follows, $uid, $this->photo);
        $follow_count = $this->photo->select_follow_count();

        $agent_apply_news = $this->photo->select('news', 'description, title', array('id' => 3));
        $description = $agent_apply_news[0]->description;
        $apply_title = $agent_apply_news[0]->title;
        $pay_status = 0 ;
        if( $uid ){
            $userinfo = get_common_userinfo($this->photo, $uid);
            $pay_status = $userinfo->pay_status;
        }
        echo json_encode(array(
            'slider_images' => $slider_images,
            'slider_images_url' => rtrim($collects['urls'],';'),
            'lession_type'  => $lession_type,
            'lastest_pub_time' => $publishes[0]['created'],
            'publishes'     => $publishes,
            'collects'      => $collects['collections'],
            'collect_pages' => ceil($collect_count/8),
            'follows'       => $follows,
            'follow_pages'  => ceil($follow_count/8),
            'description'   => $description,
            'title'         => $apply_title,
            'total_pages'   => ceil($publishes_count/3),
            'pay_status'    => $pay_status
        ));
        exit;
    }

    function get_new_publishes(){
        $limit_time = $this->input->get('limit_time');
        $uid        = $this->input->get('uid');
        $publishes = $this->_get_publishes(0, 0, $uid, $limit_time);
        if ( $publishes ){
            echo json_encode(array(
                'code' => 0,
                'lastest_pub_time' => $publishes[0]['created'],
                'publishes'        => $publishes
            ));
            exit;
        }else{
            echo json_encode(array(
                'code' => 10000
            ));
            exit;
        }
        
    }

    function get_more_collects(){
        $page = $this->input->get('page');
        $uid  = $this->input->get('uid');
        $collect_count = $this->photo->select_collect_count();
        if ( $page > ceil($collect_count/8) ){
            echo json_encode(array('code' => 10000 , 'msg' => '操作成功', 'data' => ''));exit;
        }else{
            $collects = get_collect_stack_list($this->photo, $uid, $page*8);
            $collects['urls'] = rtrim($collects['urls'], ';');
            echo json_encode(array('code' => 0 , 'msg' => '操作成功', 'data' => $collects, 'page' => $page ));exit;
        }
    }

    function get_more_follows(){
        $page = $this->input->get('page');
        $uid  = $this->input->get('uid');
        $follow_count = $this->photo->select_follow_count();
        if ( $page > ceil($follow_count/8) ){
            echo json_encode(array('code' => 10000 , 'msg' => '操作成功', 'data' => ''));exit;
        }else{
            $follows  = $this->photo->select_follows($page*8);
            $follows = reoragnize_follows($follows, $uid, $this->photo);
            echo json_encode(array('code' => 0 , 'msg' => '操作成功', 'data' => $follows, 'page' => $page ));exit;
        }
    }
    

    function get_more_publish(){
        $uid = $this->input->get('uid');
        $page = $this->input->get('page');
        $publishes = $this->_get_publishes($page*3, 3, $uid);
        echo json_encode(array('code' => 0 , 'msg' => '操作成功', 'data' => $publishes));exit;
    }


    private function _get_publishes($num, $offset, $uid, $limit_time = 0){
        $publishes = $this->photo->select_publishes_join_attachments('' ,$num, $offset, $limit_time);
        if( count($publishes) > 0 ){
            $publishes = reorganize_index_publishes($this->photo, $publishes, $uid);
        }
        return $publishes;
    }
}	