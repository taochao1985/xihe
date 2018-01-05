<?php
defined('BASEPATH') OR exit('No direct script access allowed');
 
class News extends MY_Controller{
    function __construct(){
        parent::__construct(); 
    }
	
    function index(){
        $news = $this->photo->select('news');
        $data['news'] = $news;
        $this->load->view('admin/news/index', $data);
    }

    function create(){
        $data['news'] = array();
    	$this->load->view('admin/news/create', $data);
    }

    function edit($id){
        $id = $this->uri->segment(4);
        $data['news'] = array();
        $news = $this->photo->select('news','*',array('id' => $id)); 
        if( $news ){
            $data['news'] = $news[0];
        }
        $this->load->view('admin/news/create', $data);   
    }

    function show($id){

    }
    
    function store(){

        $data = array(
            'description'=> trim($_POST['description']), 
            'title'      => trim($_POST['title']),
            'created'    => time(),
            'updated'    => time() 
        );

        $result = $this->photo->insert('news', $data);
        if( $result ){
            echo json_encode(array('code'=>0, 'msg'=>'操作成功'));exit;
        }else{
            echo json_encode(array('code'=>10001, 'msg'=>'操作失败'));exit;
        }
    }

    function update(){
        $id = $_POST['id']; 
        $data = array(
            'description'=> trim($_POST['description']), 
            'title'      => trim($_POST['title']),
            'updated'    => time() 
        );

        $result = $this->photo->update('news', $data, array('id'=> $id));
        if( $result ){
            echo json_encode(array('code'=>0, 'msg'=>'操作成功'));exit;
        }else{
            echo json_encode(array('code'=>10001, 'msg'=>'操作失败'));exit;
        }
    }

    function delete(){
        $id = $_POST['id'];
        $result = $this->photo->delete('news', array('id'=>$id));
        if( $result ){
            echo json_encode(array('code'=>0, 'msg'=>'操作成功'));exit;
        }else{
            echo json_encode(array('code'=>10001, 'msg'=>'操作失败'));exit;
        }
    }
}	