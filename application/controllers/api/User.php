<?php
defined('BASEPATH') OR exit('No direct script access allowed');
 
class User extends CI_Controller{
    function __construct(){
        parent::__construct(); 
    }

    public function update_free_trial(){
        $uid = $this->input->post('uid');
        $result = $this->photo->update('users', array('free_trial' => 1), array('uid' => $uid));

        if ( $result ){
            echo json_encode(array('code' => 0, 'msg' => '操作成功'));exit;
        }else{
            echo json_encode(array('code' => 10000, 'msg' => '请求失败，请稍后再试' ));exit;
        }    
    }

    public function pay_notify(){
        $this->load->library('MY_WxPayNotify');
        $result = $this->my_wxpaynotify->Handle(true);
    }

    function _generate_pay_params($openId, $uid) {
        require_once APPPATH."libraries/wechat_pay/lib/WxPay.Api.php";
        require_once APPPATH."libraries/wechat_pay/WxPay.JsApiPay.php";
        $tools = new JsApiPay();
        //②、统一下单
        $input = new WxPayUnifiedOrder();
        $order_check = $this->photo->select('user_pay_orders', '*', array('uid' => $uid, 'status' => 0 ));
        $trade_no = '1495605242'.time();
        if ( $order_check ){
            $order_check = $order_check[0];
            $amount = $order_check->amount;
        }else{            
            $amount = 1;
        }
        
        $input->SetBody("test");
        $input->SetAttach("test");
        $input->SetOut_trade_no($trade_no);
        $input->SetTotal_fee($amount);
        $input->SetTime_start(date("YmdHis"));
        $input->SetTime_expire(date("YmdHis", time() + 6000));
        $input->SetGoods_tag("test");
        $input->SetNotify_url(base_url()."api/user/pay_notify");
        $input->SetTrade_type("JSAPI");
        $input->SetOpenid($openId); 
        $order = WxPayApi::unifiedOrder($input); 
        if ( $order['result_code'] == 'SUCCESS' ){
            $jsApiParameters = $tools->GetJsApiParameters($order); 
            if ( !$order_check ){
                $order_data = array(
                    'uid'      => $uid,
                    'trade_no' => $trade_no,
                    'created'  => time(),
                    'amount'   => $amount,
                    'pay_id'   => $order['prepay_id']
                );
                $order_id = $this->photo->insert('user_pay_orders', $order_data);
            }else{
                $order_id = $order_check->id;
                $this->photo->update('user_pay_orders', array('trade_no' => $trade_no), array('id' => $order_id));
            }
            if( $order_id ){
                return $jsApiParameters;
            }else{
                return false;
            }
        }else{
            return false;
        }
        
    }

    function _create_pay($uid){
         
        $userinfo = get_common_userinfo($this->photo, $uid);
        return $this->_generate_pay_params($userinfo->openid, $uid);
        
    }

/*
*   create wechat pay and local order record 
*   according to uid
*/
    function create_wechat_pay(){
        $uid    = $this->input->post('uid');
        $params = $this->_create_pay($uid);
        if ( $params ){
            echo json_encode(array('code' => 0, 'msg' => '用户未支付', 'data' => $params ));exit;
        }else{
            echo json_encode(array('code' => 10000, 'msg' => '请求失败，请稍后再试' ));exit;
        }
       
    }


    function apply_agent(){
        $uid = $this->input->post('uid');
        $agent_check = $this->photo->select('user_agent_apply','*',array('uid' => $uid));
        if( $agent_check ){
            echo json_encode(array('code' => 1, 'msg' => '您已经有申请记录'));exit;
        }else{
            $agent_data = array(
                'uid'     => $uid,
                'created' => time()
            );
            $result = $this->photo->insert('user_agent_apply', $agent_data);
            if ( $result ){
                echo json_encode(array('code' => 0, 'msg' => '申请成功'));exit;
            }else{
                echo json_encode(array('code' => 1, 'msg' => '申请失败'));exit;
            }
        }

    }

    function folders(){
        $uid = $this->input->get('uid');
        $folders = $this->_get_folders($uid);
        echo json_encode(array('code' => 0, 'msg' => '操作成功', 'data' => $folders ));exit;
    }

    function folders_index(){
        $uid = $this->input->get('uid');
        $folders = $this->_get_folders($uid);
        if ( count($folders) > 0 ){
            $return_array = array();
            foreach ($folders as $key => $value) {
                $return_array[] = $value->folder_name;
            }
            $folders = $return_array;
        }
        echo json_encode(array('code' => 0, 'msg' => '操作成功', 'data' => $folders ));exit;
    }

    function _get_folders($uid){
        return $this->photo->select('user_folders','*',array('uid' => $uid));
    }

    function delete_folder(){
        $uid      = $this->input->post('uid');
        $folder_id= $this->input->post('folder_id');
        $folder_data = array('id' => $folder_id, 'uid' => $uid);

        $folder_check = $this->photo->select_count_where('user_folders', $folder_data);
        if ($folder_check > 0 ){
            $result = $this->photo->delete('user_folders', $folder_data);
        }else{
            $result = true;
        }
        if( $result ){
            
            echo json_encode(array('code' => 0, 'msg' => '操作成功'));exit;
        }else{
            echo json_encode(array('code'=>1001, 'msg'=>'操作失败'));exit; 
        }
    }

    function create_folder(){
        $uid      = $this->input->post('uid');
        $folder_name= $this->input->post('folder_name');
        $folder_data = array('folder_name' => $folder_name, 'uid' => $uid);

        $folder_check = $this->photo->select_count_where('user_folders', $folder_data);
        if ($folder_check > 0 ){
            $result = true;
        }else{
            $folder_data['created'] = time();
            $result = $this->photo->insert('user_folders', $folder_data);
        }
        if( $result ){
            
            echo json_encode(array('code' => 0, 'msg' => '操作成功'));exit;
        }else{
            echo json_encode(array('code'=>1001, 'msg'=>'操作失败'));exit; 
        }
    }

    function update_avatar(){
        $uid      = $this->input->post('uid');
        $image_url= $this->input->post('image_path');
        $this->photo->update('users',array('cover_image' => $image_url ), array('uid' => $uid));

        $userinfo = $this->_get_userinfo($uid);
        if( !$userinfo ){
            echo json_encode(array('code'=>1001, 'msg'=>'用户不存在'));exit; 
        }else{
            save_image($this->photo,array('type' => 'avatar', 'image_path'=>$image_url, 'item_id'=>$uid)); 
            echo json_encode(array('code' => 0, 'msg' => '修改成功', 'cover' => base_url().$image_url ));exit;
        }
    }

    function get_follow_count(){
        $uid = $this->input->get('uid');
        $count = $this->photo->select_count_where('user_follows',array('target_uid' => $uid));
        $agent_flag = $this->photo->select('users','agent_status',array('uid' => $uid));
        $agent_status = $agent_flag[0]->agent_status;
        $reason = "";
        if ( $agent_status != 1 ){
            $agent_apply = $this->photo->select('user_agent_apply', '*', array('uid' => $uid ));
            if ( $agent_apply ){
                $agent_apply = $agent_apply[0];
                $agentstatus = $agent_apply->status;
                if( $agentstatus == 0 ){
                    $agent_status = 2;
                }else if ($agentstatus == 2){
                    $agent_status = 3;
                }
                $reason = $agent_apply->refuse_reason;
            }
        }

        $agent_apply_news = $this->photo->select('news', 'description, title', array('id' => 2));
        $description = $agent_apply_news[0]->description;
        $apply_title = $agent_apply_news[0]->title;
        echo json_encode(array('code' => 0, 'msg' => '操作成功', 'count' => $count, 'agent_status' => $agent_status, 'reason' => $reason, 'apply_note' => $description, 'apply_title' => $apply_title ));exit; 
    }

    function follows(){
        $uid = $this->input->get('uid');
        $follows = $this->photo->select_user_follows(array('user_follows.uid' => $uid));
        if( count($follows) > 0 ){
            foreach ($follows as $key => $value) { 
                if( $value->nickname ){ 
                    $value->nickname = base64_decode($value->nickname);
                    $follows[$key] = $value;
                }
            } 
        }   
        echo json_encode(array('code' => 0, 'msg' => '操作成功', 'users' => $follows ));exit; 
    }

    function _get_collections($uid, $folder_id){
        $cond = array('image_collects.user_id' => $uid);
        if ($folder_id != 0 ){
            $cond['image_collects.folder_id'] = $folder_id;
        }
        $collections = $this->photo->select_user_collections($cond);
        foreach($collections as $key => $val){
            $val->image_path = ltrim($val->image_path,'/');
            $collections[$key]->image_path = base_url().$val->image_path;
            $collections[$key]->created = date('Y-m-d', $val->created);
            $collections[$key]->nickname = base64_decode($val->nickname);
        }
        $folders = $this->_get_folders($uid);
        $folders_org = $folders;
        if ( count($folders_org) > 0 ){
            $return_array = array();
            foreach ($folders_org as $key => $value) {
                $return_array[] = $value->folder_name;
            }
            $folders_org = $return_array;
        }
        echo json_encode(array('code' => 0, 'msg' => '操作成功', 'collections' => $collections, 'folders' => $folders, 'folders_org' => $folders_org ));exit; 
    }

    function collections(){
        $uid = $this->input->get('uid');
        $folder_id = $this->input->get('folder_id');

        $this->_get_collections($uid, $folder_id);
    }

	
    function _get_userinfo($uid){
        return get_common_userinfo($this->photo, $uid);
    }

    function show(){
        $uid = $this->uri->segment(4);
        $userinfo = $this->_get_userinfo($uid);
        if( !$userinfo ){
            echo json_encode(array('code'=>1001, 'msg'=>'用户不存在'));exit; 
        }else{
            $coverimage      = $userinfo->cover_image; 
            if( !$coverimage ){
                $info  = $userinfo->userinfo;
                $info = json_decode($info); 
                $cover = $info->avatarUrl;
            }else{
                $cover = base_url().$coverimage;
            }
            $follow_count = $this->photo->select_count_where('user_follows', array('target_uid' => $uid));
            echo json_encode(array('code' => 0, 'msg' => '操作成功', 'data' => $userinfo, 'cover' => $cover, 'follow_count' => $follow_count ));exit; 
        }
    }

    function index(){
        $uid = $this->uri->segment(4);
        $userinfo = $this->_get_userinfo($uid);
        if( !$userinfo ){
            echo json_encode(array('code'=>1001, 'msg'=>'用户不存在'));exit; 
        }else{
            $publishes = $this->photo->select();
        }
    }

/*
*   follow persion according to user id and target uid
*/
    function follow(){
        $uid        = $this->input->post('user_id');
        $target_uid = $this->input->post('target_uid');
        $follow     = $this->input->post('follow');

        $follow_data = array('uid' => $uid, 'target_uid' => $target_uid );
        if( $follow == 1){
            $follow_data['created'] = time();
            $result = $this->photo->insert('user_follows', $follow_data);
        }else{
            $result = $this->photo->delete('user_follows', $follow_data);
        } 
        echo json_encode(array('code'=>0, 'msg'=>'操作成功', 'is_follow' => $follow, 'target_uid' => $target_uid));exit;    
    }

    function update_collect_image(){
        $collect_id  = $this->input->post('collect_id');
        $folder_name = $this->input->post('folder_name');
        $uid         = $this->input->post('uid');
        $folder_check = $this->photo->select('user_folders','id',array('uid' => $uid, 'folder_name' => $folder_name));
        if( $folder_check ){
            $folder_id = $folder_check[0]->id;
        }else{
            $folder_id = 0;
        }
      
        $result = $this->photo->update('image_collects', array('folder_id' => $folder_id ), array('id' => $collect_id));
        if ( $result ){
            $this->_get_collections($uid, 0);
        } 
    }

    function delete_collect(){
        $collect_id = $this->input->post('collect_id');
        $uid        = $this->input->post('uid');

        $collect_check = $this->photo->select('image_collects', '*', array('id' => $collect_id));
        if ( !$collect_check ){
            echo json_encode(array('code'=>10000, 'msg'=>'记录不存在'));exit;
        }else{
            $collect = $collect_check[0];
            if( $collect->user_id != $uid ){
                echo json_encode(array('code'=>10000, 'msg'=>'没有权限'));exit;
            }else{
                $result = $this->photo->delete('image_collects', array('id' => $collect->id));
                if ( $result ){
                    echo json_encode(array('code'=>0, 'msg'=>'操作成功'));exit;    
                }else{
                    echo json_encode(array('code'=>10000, 'msg'=>'操作失败'));exit;   
                }
            }
        }
    }

/*
*   collect image according to user id and image_id
*/
    function collect_image(){
        $uid        = $this->input->post('user_id');
        $target_aid = $this->input->post('target_aid'); 
        $target_uid = $this->input->post('target_uid'); 
        $collect    = $this->input->post('collect');
        $act_type   = $this->input->post('act_type');
        $folder_name = $this->input->post('folder_name');
        $folder_check = $this->photo->select('user_folders','id',array('uid' => $uid, 'folder_name' => $folder_name));
        if( $folder_check ){
            $folder_id = $folder_check[0]->id;
        }else{
            $folder_id = 0;
        }
        $collect_data = array('user_id' => $uid, 'image_id' => $target_aid );
        if( $collect == 1){
            $collect_data['created'] = time();
            $collect_data['image_uid'] = $target_uid;
            $collect_data['folder_id'] = $folder_id;
            $result = $this->photo->insert('image_collects', $collect_data);
        }else{
            $result = $this->photo->delete('image_collects', $collect_data);
        } 
        $data = array();
        if( $act_type == 'collect'){
            $data = get_collect_stack_list($this->photo, $uid);
        }
        echo json_encode(array('code'=>0, 'msg'=>'操作成功', 'is_collect' => $collect, 'data' => $data ));exit;
    }
 
}	