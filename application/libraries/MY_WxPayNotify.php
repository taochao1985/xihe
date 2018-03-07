<?php
require_once APPPATH."libraries/wechat_pay/lib/WxPay.Api.php";
require_once APPPATH."libraries/wechat_pay/lib/WxPay.Notify.php";

class MY_WxPayNotify extends WxPayNotify {

    private $_CI;

    public function  __construct(){
        // 获得 CI 超级对象 使得自定义类可以使用Controller类的方法
        $this->_CI = & get_instance();
    }

    /**
     * NotifyProcess 微信回调处理
     * @access public
     */
    public function NotifyProcess($data, &$msg){ 

        if ($data['return_code'] == 'SUCCESS' && $data['result_code'] == 'SUCCESS') {
            $this->updateOrderPay($data);
            return true;
        }else{
            error_log(print_r($data,true), 3, '/home/www/xihe/pay_failure_data.txt');
            error_log(print_r($msg,true), 3, '/home/www/xihe/pay_failure.txt');
        }

        return false;
    }
 

    /**
     * updateOrderPay 更新订单支付信息
     * @access private
     * @param string $order_code
     * @return mixed
     * @author Mike Lee
     */
    public function updateOrderPay($wxpay_data){
        $this->_CI->load->model('photo');
        // 根据订单号获取订单信息
        $order_code = $wxpay_data['out_trade_no'];
        $openid = $wxpay_data['openid'];
        $order_info = $this->_CI->photo->select('user_pay_orders', '*', array('trade_no' => $order_code));
        if ( !$order_info ){
            $order_code = str_replace('1495605242', '', $order_code);
            $order_info = $this->_CI->photo->select('user_pay_orders', '*', array('created' => $order_code, 'status' => 0));
        }
        
    /*
    *  如果正常返回没有查询到用户订单
    *  则根据用户openid  去查询用户最近一次未支付成功的订单
    */ 
        if ( !$order_info ){
            $user = $this->_CI->photo->select('users', 'uid', array('openid' => $openid));
            if ( $user ){
                $order_info = $this->_CI->photo->select('user_pay_orders', '*', array('uid' => $user[0]->uid, 'status' => 0), '', '', array('created' => 'desc'));
            }
        }   
        if ( $order_info ){ 
            $order_info = $order_info[0];
            if ($order_info->status == 0 ) {
                $pay_info = array('status' => 1, 'updated' => time(), 'transaction_id' => $wxpay_data['transaction_id']);
                $pay_result = $this->_CI->photo->update('user_pay_orders', $pay_info, array('id' => $order_info->id )); 
                $order_pay_info = array(
                    'pay_status' => 1,
                    'pay_time' => time()
                );
                $this->_CI->photo->update('users', $order_pay_info, array('uid' => $order_info->uid )); 
                //$this->_create_user_agent($order_info->uid, $order_info->pay_id, $order_info->amount);
            } 
        }else {
/*
*   如果没有找到用户订单
*   则插入异常订单队列
*/        
            $uid = 0 ;
            if( $user ){
                $uid = $user[0]->uid;
            }
            $this->_CI->photo->insert('user_pay_errors', array('pay_info' => serialize($wxpay_data), 'uid' => $uid, 'created' => time() ));
        }    
        return true;
    }
}
