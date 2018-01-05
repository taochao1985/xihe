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
        $data['current_time'] = date('Y-m-d H:i:s');
    	$this->load->view('admin/lessions/create', $data);
    }

    function edit($id){
        $id = $this->uri->segment(4);
        $data['lession'] = array();
        $lession = $this->photo->select_lessions_join_attachments(array('lessions.id'=>$id)); 
        if( $lession ){
            $data['lession'] = $lession[0];
        }
        $data['lession_types'] = $this->photo->select('lession_type');
        $data['current_time'] = date('Y-m-d H:i:s');
        $this->load->view('admin/lessions/create', $data);   
    }

    function show($id){

    }
    
    function store(){

        $data = array(
            'video_path' => trim($_POST['video_path']),
            'audio_path' => trim($_POST['audio_path']),
            'video_name' => trim($_POST['video_name']),
            'audio_name' => trim($_POST['audio_name']),
            'description'=> trim($_POST['description']),
            'lt_id'      => trim($_POST['lt_id']),
            'title'      => trim($_POST['title']),
            'start_time' => strtotime(trim($_POST['start_time'])),
            'created'    => time(),
            'updated'    => time() 
        );

        $result = $this->photo->insert('lessions', $data);
        if( $result ){
            save_image($this->photo,array('type' => 'lession', 'image_path'=>trim($_POST['image_path']), 'item_id'=>$result)); 
            echo json_encode(array('code'=>0, 'msg'=>'操作成功'));exit;
        }else{
            echo json_encode(array('code'=>10001, 'msg'=>'操作失败'));exit;
        }
    }

    function update(){
        $id = $_POST['id'];
        delete_image($this->photo,array('type'=>'lession', 'item_id'=>$id));
        $image_id = save_image($this->photo,array('type' => 'lession', 'image_path'=>trim($_POST['image_path']), 'item_id'=>$id));
        $data = array(
            'video_path' => trim($_POST['video_path']),
            'audio_path' => trim($_POST['audio_path']),
            'video_name' => trim($_POST['video_name']),
            'audio_name' => trim($_POST['audio_name']),
            'description'=> trim($_POST['description']),
            'start_time' => strtotime(trim($_POST['start_time'])),
            'lt_id'      => trim($_POST['lt_id']),
            'title'      => trim($_POST['title']),
            'updated'    => time() 
        );

        $result = $this->photo->update('lessions', $data, array('id'=> $id));
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