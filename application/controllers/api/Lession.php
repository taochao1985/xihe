<?php
defined('BASEPATH') OR exit('No direct script access allowed');
 
class Lession extends CI_Controller{
    function __construct(){
        parent::__construct(); 
    }

/*
*   record user lession shared history
*/
    function shared(){
        $uid     = $this->input->post('uid');
        $post_id = $this->input->post('post_id');

        $share_data = array(
            'uid'     => $uid,
            'post_id' => $post_id,
            'created' => time()
        );
        $this->photo->insert('lession_share_log', $share_data);
    }


/*
*   get lession list according to lession category id
*/
    function list(){
        $type_id = $this->input->get('lt_id');
        $types = $this->type();
        if( $type_id == 0 ){
            $type_id = $types[0]->id;
        }
        $lessiones = $this->get_lession(array('lt_id' => $type_id ));
        echo json_encode(array('code' => 0, 'msg' => '操作成功', 'data' => array('lessions' => $lessiones, 'types'=>$types, 'type_id' => $type_id)));exit;
    }


/*
*   redeal lession struct
*/
    function deal_lessions($lessions){ 
        if( count($lessions) > 0 ){
            foreach ( $lessions as $key => $val ){
                $lessions[$key]->image_path = base_url().$val->image_path;
                $lessions[$key]->created    = date('Y-m-d', $val->created);
            }
        }
        return $lessions;
    }

// get lession list according to some conditions
    function get_lession($cond){
        $cond['start_time >='] = time(); 
        $lessions = $this->photo->select_lessions_join_attachments($cond); 
        return $this->deal_lessions($lessions);
    }

//get all lession types
    function type(){
        $type = $this->photo->select('lession_type', '', '', '', '', array('sort' => 'desc'));
        return $type;
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
        if ( $order_check ){
            $order_check = $order_check[0];
            $trade_no = $order_check->pay_id;
            $amount = $order_check->amount;
        }else{
            $trade_no = '1495605242'.time();
            $amount = 1;
        }
        
        $input->SetBody("test");
        $input->SetAttach("test");
        $input->SetOut_trade_no($trade_no);
        $input->SetTotal_fee($amount);
        $input->SetTime_start(date("YmdHis"));
        $input->SetTime_expire(date("YmdHis", time() + 6000));
        $input->SetGoods_tag("test");
        $input->SetNotify_url(base_url()."api/lession/pay_notify");
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
        
      //  $openid = "oFpQk0aY-zlPQ8YCPT1O4v0WVKAU";
        $userinfo = get_common_userinfo($this->photo, $uid);
        return $this->_generate_pay_params($userinfo->openid, $uid);
        
    }

/*
*   check user read lession count and user's pay status
*   if user's read count equals 6 and user have not payed return false
*   or return true
*/
    function _lession_user_check($uid, $id){
        if ( $uid == 0 ){
            return true;
        }
        $sql = "select distinct l_id from lession_views where uid = '". $uid ."' and l_id != '". $id ."'";
        $lession_views = $this->photo->personal_select($sql); 

        $userinfo = get_common_userinfo($this->photo, $uid);
        // if (( count($lession_views)== 6 ) && ( !$userinfo->pay_status)){
        if( !$userinfo->pay_status ){
            return false; 
        }else{
            $lession_data = array(
                'uid'     => $uid,
                'l_id'    => $id,
                'created' => time()
            );
            $this->photo->insert('lession_views',$lession_data);
            return true;
        }
    }

/*
*   get lession detail according to lession id
*/	  
    function show(){
        $id  = $this->uri->segment(4); 
        $uid = $this->uri->segment(5);

        $lession_user_check = $this->_lession_user_check($uid, $id);
        if( !$lession_user_check ){
            $params = $this->_create_pay($uid);
            echo json_encode(array('code' => 10000, 'msg' => '用户未支付', 'data' => $params));exit;
        }else{
            $lession = $this->get_lession(array('lessions.id'=>$id)); 
            if( $lession ){
                echo json_encode(array('code' => 0, 'msg' => '操作成功', 'data' => $lession[0]));exit;
            }else{
                echo json_encode(array('code' => 10001, 'msg' => '课程不存在', 'data' => []));exit;
            }
        }
    }
}	