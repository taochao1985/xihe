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
        $order_info = $this->_CI->photo->select('user_pay_orders', '*', array('trade_no' => $order_code));
        $order_info = $order_info[0];
        if ($order_info->status == 0 ) {
            $pay_info = array('status' => 1, 'updated' => time());
            $pay_result = $this->_CI->photo->update('user_pay_orders', $pay_info, array('id' => $order_info->id )); 
            $order_pay_info = array(
                'pay_status' => 1
            );
            $this->_CI->photo->update('users', $order_pay_info, array('uid' => $order_info->uid )); 
        }
        return true;
    }
}
