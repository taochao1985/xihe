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
       
        $follows  = $this->photo->select_follows();
        $follows = reoragnize_follows($follows, $uid, $this->photo);
        echo json_encode(array(
            'slider_images' => $slider_images,
            'slider_images_url' => rtrim($collects['urls'],';'),
            'lession_type'  => $lession_type,
            'publishes'     => $publishes,
            'collects'      => $collects['collections'],
            'follows'       => $follows,
            'total_pages'   => ceil($publishes_count/3)
        ));
        exit;
    }

    function get_more_publish(){
        $uid = $this->input->get('uid');
        $page = $this->input->get('page');
        $publishes = $this->_get_publishes($page*3, 3, $uid);
        echo json_encode(array('code' => 0 , 'msg' => '操作成功', 'data' => $publishes));exit;
    }


    private function _get_publishes($num, $offset, $uid){
        $publishes = $this->photo->select_publishes_join_attachments('' ,$num, $offset);

        if( count($publishes) > 0 ){
            $publishes = reorganize_index_publishes($this->photo, $publishes, $uid);
        }
        return $publishes;
    }
}	