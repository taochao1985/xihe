<?php
defined('BASEPATH') OR exit('No direct script access allowed');
 
class Lessions extends MY_Controller{
    function __construct(){
        parent::__construct(); 
    }
	
    function index(){
        $data['lession_type'] = $this->photo->select_count_where('lession_type');
        $lessions = $this->photo->select('lessions');
        $data['lessions'] = $lessions;
        $this->load->view('admin/lessions/index', $data);
    }

    function create(){
        $data['lession'] = array();
        $data['lession_types'] = $this->photo->select('lession_type');
    	$this->load->view('admin/lessions/create', $data);
    }

    function edit($id){
        $id = $this->uri->segment(4);
        $data['lession'] = array();
        $lession = $this->photo->select('lessions','*', array('id' => $id));
        if( $lession ){
            $data['lession'] = $lession[0];
        }
        $data['lession_types'] = $this->photo->select('lession_type');
        $this->load->view('admin/lessions/create', $data);   
    }

    function show($id){

    }
    
    function store(){
        $data = array(
            'video_path' => trim($_POST['video_path']),
            'audio_path' => trim($_POST['audio_path']),
            'image_path' => trim($_POST['image_path']),
            'description'=> trim($_POST['description']),
            'lt_id'      => trim($_POST['lt_id']),
            'title'      => trim($_POST['title']),
            'created'    => time(),
            'updated'    => time() 
        );

        $result = $this->photo->insert('lessions', $data);
        if( $result ){
            echo json_encode(array('code'=>0, 'msg'=>'操作成功'));exit;
        }else{
            echo json_encode(array('code'=>10001, 'msg'=>'操作失败'));exit;
        }
    }

    function update(){
        $data = array(
            'video_path' => trim($_POST['video_path']),
            'audio_path' => trim($_POST['audio_path']),
            'image_path' => trim($_POST['image_path']),
            'description'=> trim($_POST['description']),
            'lt_id'      => trim($_POST['lt_id']),
            'title'      => trim($_POST['title']),
            'updated'    => time() 
        );

        $result = $this->photo->update('lessions', $data, array('id'=> $_POST['id']));
        if( $result ){
            echo json_encode(array('code'=>0, 'msg'=>'操作成功'));exit;
        }else{
            echo json_encode(array('code'=>10001, 'msg'=>'操作失败'));exit;
        }
    }

    function delete(){
        $id = $_POST['id'];
        $result = $this->photo->delete('lessions', array('id'=>$id));
        if( $result ){
            echo json_encode(array('code'=>0, 'msg'=>'操作成功'));exit;
        }else{
            echo json_encode(array('code'=>10001, 'msg'=>'操作失败'));exit;
        }
    }
}	