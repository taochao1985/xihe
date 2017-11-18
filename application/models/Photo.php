<?php
class Photo extends CI_Model {

    function _generate_code($len = 20){ 
        $chars = '0123456789';

        for ($i = 0, $count = strlen($chars); $i < $count; $i++){
        	$arr[$i] = $chars[$i];
        }

    	mt_srand((double) microtime() * 1000000);
    	shuffle($arr);
    	$code = substr(implode('', $arr),0 , $len);
        return $code;
    }

    function get_ip(){
        return $_SERVER["REMOTE_ADDR"];
    }

    function _send_sms($mobile,$content){
        require_once APPPATH.'libraries/sms/ChuanglanSmsApi.php';
        $clapi  = new ChuanglanSmsApi();
        $result = $clapi->sendSMS($mobile,$content,'true');
        $result = $clapi->execResult($result);
        if(!$result[0]){
            return "发送失败";
        }else{

            if($result[1]==0){
                    return 'success';
            }else{
                return "发送失败{$result[1]}";
            }
        }
    }

     function _generate_num($table,$label){
        $result = $this->music->select($table,'numbe','',0,1,array('id'=>'desc'));
        if($result){
            $num = $result[0]->numbe;
        }else{
            return $label.'000001';
        }

        $l = strlen(intval(ltrim($num,$label)));
        $next_num = intval(ltrim($num,$label));
        $j = $label;

        for($i = 0; $i< 6-$l; $i++){
            $j.='0';
        }

        $next_num++;
        $t = strlen($next_num);
        $tt = substr($next_num, $t-1, $t);
        if($tt == 4){
            $next_num++;
        }
        return $j.$next_num;
    }


    function check_user($cond){
        $temp_user = $this->jifen->select('users','*',$cond);
        if(!$temp_user){
            echo json_encode(array('success'=>'no','msg'=>'用户不存在，请确认'));exit;
        }else{
            return $temp_user[0];
        }
  }

  function add_score($user,$socre,$type){
      if($type == 'add_score'){
          $final_score = $user->score+$socre;
      }else if($type == 'minus_score'){
          $final_score = $user->score-$socre;
          if($final_score < 0){
             echo json_encode(array('success'=>'no','msg'=>'积分不能小于0'));exit;
          }
      }
      $result = $this->jifen->update('users',array('score'=>$final_score),array('id'=>$user->id));
      if($result){
        return true;
      }else{
         echo json_encode(array('success'=>'no','msg'=>'操作失败'));exit;
      }
  }

    function generate_cart_no(){
        return $this->_generate_code(12);
    }

    function xml_to_array($xml){
        $reg = "/<(\w+)[^>]*>([\\x00-\\xFF]*)<\\/\\1>/";
        if(preg_match_all($reg, $xml, $matches)){
            $count = count($matches[0]);
            for($i = 0; $i < $count; $i++){
                $subxml= $matches[2][$i];
                $key = $matches[1][$i];
                if(preg_match( $reg, $subxml )){
                    $arr[$key] = xml_to_array( $subxml );
                }else{
                    $arr[$key] = $subxml;
                }
            }
        }
        return $arr;
    }


    function select_count_where($table,$cond='',$like='',$or_like=''){
        if ($cond){
           $this->db->where($cond);
        }
        if (!empty($like)){
            $this->db->like($like);
        }
        if (!empty($or_like)){
            $this->db->or_like($or_like);
        }

        $query = $this->db->from($table);
        $result= $query->count_all_results();

        return $result;
    }

    function personal_select($sql){
        $query = $this->db->query($sql);
        $result= $query->result();
        return $result;
    }

    function personal_query($sql){
        $query = $this->db->query($sql);
        return $query;
    }


    function translate_begin(){
        $this->db->trans_begin();
    }

    function translate_commit(){
        $this->db->trans_commit();
    }

    function translate_rollback(){
        $this->db->trans_rollback();
    }

    function select_sum_avg($table,$cond,$field,$type='avg'){
        if ($type == "sum"){
            $this->db->select_sum($field);
        }else{
           $this->db->select_avg($field);
        }
        $this->db->where($cond);
        $query = $this->db->get($table);
        $result= $query->result();
        return $result;
    }


    function insert($table,$data){
       $result = $this->db->insert($table,$data);
       $id = $this->db->insert_id();
       return $id;
    }

    function login($table,$array){
        $this->db->where($array);
        $query = $this->db->get($table);
        $result= $query->result();
        return $result;
    }


    function select_in($table,$select_field,$field,$a){
        $this->db->select($select_field);
        $this->db->where_in($field,$a);
        $query = $this->db->get($table);
        $result = $query->result();
        return $result;
    }


   function select($table,$fields="*",$cond="",$num="",$offset="",$order='',$like='',$or_like=''){

        $this->db->select($fields);

        if($num!=''){
            $this->db->limit($num,$offset);
        }
        if ($cond){
            $this->db->where($cond);
        }

       if (!empty($like)){
            $this->db->like($like);
        }
        if (!empty($or_like)){
            $this->db->or_like($or_like);
        }

        if($order){
            foreach($order as $key=>$val){
                $this->db->order_by($key,$val);
            }
        }

        $query = $this->db->get($table);
        $result = $query->result(); 
        return $result;
    }


    function update($table,$data,$cond){
       $result = $this->db->update($table,$data,$cond);

       return $result;
    }

    function delete($table,$cond){
       $result = $this->db->delete($table,$cond);
       return $result;
    }
} 
