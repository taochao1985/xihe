<?php
defined('BASEPATH') OR exit('No direct script access allowed');
 
class News extends CI_Controller{
    function __construct(){
        parent::__construct(); 
    }
 
/*
*   get lession list according to lession category id
*/
    function list(){
        $news = $this->photo->select('news','*', array('id > '=> 2));
        if ($news){
            foreach( $news as $key => $val){
                $news[$key]->created = date('Y-m-d', $val->created);
            }
        } 
        echo json_encode(array('code' => 0, 'msg' => '操作成功', 'data' => array('news' => $news)));exit;
    }

/*
*   get lession detail according to lession id
*/	  
    function show(){
        $id  = $this->uri->segment(4);    
        $news = $this->photo->select('news','*', array('id' => $id));
        if( $news ){
            $news = $news[0];    
            $news->created = date('Y-m-d', $news->created);
            echo json_encode(array('code' => 0, 'msg' => '操作成功', 'data' => $news));exit;
        }else{
            echo json_encode(array('code' => 10001, 'msg' => '课程不存在', 'data' => []));exit;
        }        
    }
}	