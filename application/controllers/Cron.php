<?php
defined('BASEPATH') OR exit('No direct script access allowed'); 
class Cron extends CI_Controller {

    function index(){
        $time = time() - 60 * 60 * 2;
        $orders = $this->photo->select('user_pay_orders', '*' , array('created >= ' => $time, 'status' => 0));
        if( $orders ){
            foreach( $orders as $key => $val ){
                $this->_get_order_info($val);
            } 
        }
    }

    private function _get_order_info($order){
        require_once APPPATH."libraries/wechat_pay/lib/WxPay.Api.php";
        $input = new WxPayUnifiedOrder();
        $input->SetOut_trade_no($order->trade_no);
        $orderinfo = WxPayApi::orderQuery($input);

        if( $orderinfo['trade_state'] == "SUCCESS" ){
            $this->_change_order_status($orderinfo);
        }
    }

    function text(){
        echo date('Y-m-d H:i:s', '1518413358');exit;
        require_once APPPATH."libraries/wechat_pay/lib/WxPay.Api.php";
        $input = new WxPayUnifiedOrder();
        $input->SetOut_trade_no('14956052421518411140');
        $orderinfo = WxPayApi::orderQuery($input);
        echo '<pre>';print_r($orderinfo);exit;
    }

    private function _change_order_status($orderinfo){
        $order_code = $orderinfo['out_trade_no'];
        $order_info = $this->photo->select('user_pay_orders', '*', array('trade_no' => $order_code));
        $order_info = $order_info[0];
        if ($order_info->status == 0 ) {
            $pay_info = array('status' => 1, 'updated' => time(), 'transaction_id' => $orderinfo['transaction_id']);
            $pay_result = $this->photo->update('user_pay_orders', $pay_info, array('id' => $order_info->id )); 
            $order_pay_info = array(
                'pay_status' => 1,
                'pay_time' => time()
            );
            $this->photo->update('users', $order_pay_info, array('uid' => $order_info->uid )); 
        }
    }
}
