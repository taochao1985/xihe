<?php
defined('BASEPATH') OR exit('No direct script access allowed');
 
class Publish extends MY_Controller{
    function __construct(){
        parent::__construct(); 
    }
  
    function store() {
      $id = $this->input->post('id');
      $pub_id = $this->input->post('pub_id');
      $content = $this->input->post('content');
      $comment_data = array('content' => $content, 'post_id' => $pub_id, 'uid' => 3);
      if( $id ){
        $result = $this->photo->update('user_comments', $comment_data, array('id' => $id));
      }else {
        $comment_data['created'] = time();
        $result = $this->photo->insert('user_comments', $comment_data);
      }

      if( $result ){
          echo json_encode(array('code'=>0, 'msg'=>'操作成功'));exit;
      }else{
          echo json_encode(array('code'=>10001, 'msg'=>'操作失败'));exit;
      }
    }

    function delete_comment(){
        $id = $_POST['id'];
        $result = $this->photo->delete('user_comments', array('id'=>$id));
        if( $result ){
            echo json_encode(array('code'=>0, 'msg'=>'操作成功'));exit;
        }else{
            echo json_encode(array('code'=>10001, 'msg'=>'操作失败'));exit;
        }
    }

    function delete(){
        $publish_id = $this->input->post('id');
        $publish = $this->photo->select('publishes','id', array('id'=>$publish_id));
        if( $publish ){
            $this->photo->translate_begin();
            
            $del_image = delete_image($this->photo, array('type' => 'publish', 'item_id' => $publish_id));
            $del_publish = $this->photo->delete('publishes', array('id' => $publish_id));
            $comment_delete = $this->photo->delete('user_comments', array('post_id' => $publish_id));
            if( $del_publish && $del_image && $comment_delete ){
                $this->photo->translate_commit();
                echo json_encode(array('code'=>0, 'msg'=>'操作成功'));exit; 
            }else{
                $this->photo->translate_rollback();
                echo json_encode(array('code' => 1, 'msg' => '操作失败'));exit;
            }    
        }else{
            echo json_encode(array('code' => 10001, 'msg' => '作品不存在'));exit;
        }
    }

    function index(){
      $page = $this->uri->segment(4);

      $perpage = 10;
      if($page==""){
        $page=1;
      }
      $start = ($page-1)*$perpage;
      $total = $this->photo->select_post_publishes_count(array('users.pay_status !=' => 0));
      $data['total'] = ceil($total/$perpage);
      $data['current_page'] = $page;

      $publishes = $this->photo->select_post_publishes(array('users.pay_status !=' => 0), $start, $perpage);
      if ( $publishes ){
        foreach( $publishes as $key => $val){
          $images = $this->photo->select('attachments','*',array('type' => 'publish','item_id' => $val->id));
          if ( $images ){
            foreach( $images as $k => $v){
              $images[$k]->thumb_image = get_rename_image($v->image_path);
            }
          }
          $publishes[$key]->images = $images;
          $comments = $this->photo->select('user_comments','*',array('post_id' => $val->id,'uid' => 3));
          $publishes[$key]->comments = $comments;
        }
      }

      $data['publishes'] = $publishes;
      $this->load->view('admin/publish/index', $data);
    }
} 