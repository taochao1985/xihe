<?php
defined('BASEPATH') OR exit('No direct script access allowed');
 
class Publish extends CI_Controller{
    function __construct(){
        parent::__construct(); 
    }

/*
*   save post comments according to post id and user id
*/
    function store_comment(){
        $uid     = $this->input->post('uid');
        $post_id = $this->input->post('post_id');
        $content = $this->input->post('content');
        if ( !$content ){
            echo json_encode(array('code' => 10002, 'msg' => '评论内容不能空'));exit;
        }

        $usercheck = $this->_check_user($uid);
        if( !$usercheck ){
            echo json_encode(array('code'=>1001, 'msg'=>'用户不存在'));exit; 
        }

        $publish = $this->show($post_id);
        if( !$publish ){
            echo json_encode(array('code' => 10002, 'msg' => '作品不存在'));exit;
        }

        $comment_data = array(
            'uid'     => $uid,
            'content' => $content,
            'post_id' => $post_id,
            'created' => time()
        );
        $comment_id = $this->photo->insert('user_comments', $comment_data);
        if( $comment_id ){
            $comments = $this->photo->select_post_comments(array('user_comments.post_id'=>$post_id));
            foreach ($comments as $key => $value) {
                $comments[$key]->nickname = base64_decode($value->nickname);
            }
            echo json_encode(array('code' => 0, 'msg' => '操作成功', 'data' => $comments));exit;
        }else{
            echo json_encode(array('code' => 10000, 'msg' => '操作失败', 'data' => []));exit;
        }
    }

/*
*   check user exist or not according to userid
*/
    private function _check_user($uid){
        return $this->photo->select('users', 'uid', array('uid' => $uid ));
    }

/*
*   get publish list according to user id
*/
    function list(){
        $uid = $this->uri->segment(4);
        $userinfo = $this->photo->select('users', '*', array('uid' => $uid ));
        if( !$userinfo ){
            echo json_encode(array('code'=>1001, 'msg'=>'用户不存在'));exit; 
        }else{
            $publishes_count = $this->photo->select_count_where('publishes', array('uid' => $uid)); 
            $publishes       = $this->_get_publishes(0, 4, $uid);
            echo json_encode(array(
                                    'code' => 0, 
                                    'data' => $publishes, 
                                    'today' => date('Y-m-d'),
                                    'total_pages' => ceil($publishes_count/4)
                                ));exit;
        }    
    }
	 
    function get_more_publish(){
        $uid = $this->input->get('uid');
        $page = $this->input->get('page');
        $publishes = $this->_get_publishes($page*4, 4, $uid);
        echo json_encode(array('code' => 0 , 'msg' => '操作成功', 'data' => $publishes));exit;
    } 

    private function _get_publishes($num, $offset, $uid){
        $publishes = $this->photo->select_publishes_join_attachments($uid, $num, $offset);
        if( count($publishes) > 0 ){
            $publishes = reorganize_publishes($publishes, $this->photo);
        }
        return $publishes;
    }


    function store(){
        $image_path  = $this->input->post('image_path');
        $description = $this->input->post('description');
        $uid         = $this->input->post('user_id'); 

        $publish_data = array(
            'description' => $description,
            'uid'         => $uid,
            'created'     => time()
        );

        $publish_id = $this->photo->insert('publishes', $publish_data);
        if ($publish_id ){
            if ($image_path != ""){
                $image_path = explode(';', $image_path);
                for( $i = 0 ; $i < count($image_path); $i++ ){
                    if( $image_path[$i] ){
                        save_image($this->photo,array('type' => 'publish', 'image_path'=>$image_path[$i], 'item_id'=>$publish_id)); 
                    }
                }
            }

            echo json_encode(array('code' => 0, 'msg' => '发布成功'));exit;
        }  
    }


/*
*   delete publish acccording to publish id
*/
    function destory (){
        $uid = $this->input->post('uid');
        $publish_id = $this->input->post('publish_id');

        $publish = $this->show($publish_id);
        if( $publish ){
            if ( $publish->uid != $uid ){
                echo json_encode(array('code' => 10002, 'msg' => '您不能删除别人的作品'));exit;
            }
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


    function show($id){
        $publish = $this->photo->select('publishes','*', array('id' => $id ));
        if( $publish ){
            return $publish[0];
        }else{
            return false;
        }
    }
}	