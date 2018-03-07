<?php
defined('BASEPATH') OR exit('No direct script access allowed');
 
class Baseconfig extends MY_Controller{
    function __construct(){
        parent::__construct(); 
    }
	
    function update_slider_images_cache(){
        $this->cache->memcached->delete('slider_images'); 
        $images = $this->photo->select('slider_images');
        $result = $this->cache->memcached->save('slider_images',$images, 60*60*24*29);
        return $result?true:false;
    }

    function slider_images(){
        $data['images'] = $this->photo->select('slider_images');
        $this->load->view('admin/baseconfig/slider_images', $data);
    }

/*
*   insert or update lession type
*   according to id
*/
    function slider_images_store(){
        $id   = trim($_POST['id']);
        $relate_url = trim($_POST['relate_url']);
        $image_path = trim($_POST['image_path']);
        if( $id != 0 ){
            $result = $this->photo->update('slider_images', array('image_path'=>$image_path,'relate_url'=>$relate_url), array('id'=>$id));
        }else{
            $result = $this->photo->insert('slider_images', array('image_path'=>$image_path,'relate_url'=>$relate_url, 'created'=>time()));
        }

        if( $result ){
            $result = $this->update_slider_images_cache();
            echo json_encode(array('code'=>0, 'msg'=>'操作成功'));exit;
        }else{
            echo json_encode(array('code'=>10001, 'msg'=>'操作失败'));exit;
        }
    }

    function slider_images_delete(){
        $id = trim($_POST['id']); 
         
        $result = $this->photo->delete('slider_images', array('id' => $id));
        if( $result ){
            $this->update_slider_images_cache();
            echo json_encode(array('code'=>0, 'msg'=>'操作成功'));exit;
        }else{
            echo json_encode(array('code'=>10001, 'msg'=>'操作失败'));exit;
        }
    }

/*
*  get all lession types
*/
    function lession_type(){
        
        $data['types'] = $this->photo->select('lession_type', '', '', '', '', array('sort' => 'desc'));
        $this->load->view('admin/baseconfig/lession_type', $data);
    }

/*
*   insert or update lession type
*   according to id
*/
    function lession_type_store(){
        $id   = trim($_POST['id']);
        $name = trim($_POST['name']);
        $sort = $_POST['sort'];
        $order = $_POST['order'];
        $desc = $_POST['desc'];
        if( $id != 0 ){
            $result = $this->photo->update('lession_type', array('name'=>$name, 'desc'=>$desc, 'sort' => $sort, 'lession_order' => $order ), array('id'=>$id));
        }else{
            $result = $this->photo->insert('lession_type', array('name'=>$name, 'desc'=>$desc, 'sort' => $sort, 'lession_order' => $order ));
        }

        if( $result ){
            echo json_encode(array('code'=>0, 'msg'=>'操作成功'));exit;
        }else{
            echo json_encode(array('code'=>10001, 'msg'=>'操作失败'));exit;
        }
    }

/*
*   delete lession type
*   according to id
*/
    function lession_type_delete(){
        $id = trim($_POST['id']); 
        
        $this->photo->translate_begin();

        $result1 = $this->photo->delete('lessions', array('lt_id' => $id));
        $result2 = $this->photo->delete('lession_type',array('id' => $id));
        
        if( $result1 && $result2 ){
            $this->photo->translate_commit();
        }else{
            $this->photo->translate_rollback();
        }    

        echo json_encode(array('code'=>0, 'msg'=>'操作成功'));exit; 
    }

    function update_restrict_words_cache(){
        $restrict_words = $this->photo->select('restrict_words');
        $words = array();
        //创建一个空的trie tree
        $resTrie = trie_filter_new();
        if ( count($restrict_words) > 0 ){
            foreach($restrict_words as $k => $v){
                trie_filter_store($resTrie, $v->words);
            }
        }
        //生成敏感词文件
        trie_filter_save($resTrie,'./dirty_words.dic');
        return true;
    }

/*
*  get all lession types
*/
    function restrict_words(){
        
        $data['types'] = $this->photo->select('restrict_words', '', '', '', '', array('id' => 'desc'));
        $this->load->view('admin/baseconfig/restrict_words', $data);
    }

/*
*   insert or update lession type
*   according to id
*/
    function restrict_words_store(){
        $id    = trim($_POST['id']);
        $words = $this->input->post('words');
        if( $id != 0 ){
            $result = $this->photo->update('restrict_words', array('words'=>$words), array('id'=>$id));
        }else{
            $result = $this->photo->insert('restrict_words', array('words'=>$words));
        }
        
        if( $result ){
            $this->update_restrict_words_cache();
            echo json_encode(array('code'=>0, 'msg'=>'操作成功'));exit;
        }else{
            echo json_encode(array('code'=>10001, 'msg'=>'操作失败'));exit;
        }
    }

/*
*   delete lession type
*   according to id
*/
    function restrict_words_delete(){
        $id = trim($_POST['id']); 
    
        $result2 = $this->photo->delete('restrict_words',array('id' => $id));      

        echo json_encode(array('code'=>0, 'msg'=>'操作成功'));exit; 
    }


/*
*   checkout lessions count 
*   before delete lession type
*/
    function check_lessions(){
        $id = trim($_GET['id']);
        $lessions_count = $this->photo->select_count_where('lessions',array('lt_id'=>$id));
        echo json_encode(array('code'=>0, 'count'=>'操作成功', 'count'=>$lessions_count));exit;
    }

}	