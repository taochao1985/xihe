<?php
class Photo extends CI_Model {

    function select_count_where($table,$cond='',$like='',$or_like='', $where_in = ''){
        if ($cond){
           $this->db->where($cond);
        }
        if (!empty($like)){
            $this->db->like($like);
        }
        if (!empty($or_like)){
            $this->db->or_like($or_like);
        }

        if( !empty($where_in) ){
            foreach($where_in as $key=>$val){
                $this->db->where_in($key,$val);
            }
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
    function select_collect_count(){
        return count($this->personal_select("SELECT DISTINCT image_id FROM  `image_collects` WHERE 1 "));
    }

    function select_collections($start = 0){
        $this->db->select('count(*) as count ,image_collects.image_id,attachments.image_path, publishes.uid');
        $this->db->from('image_collects');
        $this->db->join('attachments', 'attachments.id = image_collects.image_id ', 'left');
        $this->db->join('publishes', 'publishes.id = attachments.item_id', 'left');
        $this->db->group_by('image_collects.image_id');
        $this->db->order_by('count','desc');
        $this->db->order_by('image_collects.image_id','desc');
        $this->db->limit(8,$start);
        $query = $this->db->get();
        $result = $query->result(); 
        return $result;
    }

    function select_follow_count(){
        return count($this->personal_select("SELECT DISTINCT target_uid FROM  `user_follows` WHERE 1 "));
    }

    function select_follows($start = 0){
        $this->db->select('count(*) as count ,users.avatarurl,user_follows.target_uid');
        $this->db->from('user_follows');
        $this->db->join('users', 'users.uid = user_follows.target_uid ', 'left');
        $this->db->group_by('user_follows.target_uid');
        $this->db->order_by('count','desc');
        $this->db->order_by('user_follows.target_uid','desc');
        $this->db->limit(8,$start);
        $query = $this->db->get();
        $result = $query->result(); 
        return $result;
    }

    function select_user_collections($select_cond){
        $this->db->select('attachments.image_path, attachments.item_id, image_collects.id as collect_id ,image_collects.folder_id,image_collects.user_id, image_collects.created,users.nickname, publishes.uid, publishes.id');
        $this->db->from('image_collects');
        $this->db->join('attachments', 'attachments.id = image_collects.image_id ', 'left');
        $this->db->join('publishes', 'publishes.id = attachments.item_id ', 'left');
        $this->db->join('users', 'users.uid = publishes.uid ', 'right');
        $this->db->where($select_cond);
        $this->db->order_by('image_collects.created','desc');
        $query = $this->db->get();
        $result = $query->result(); 
        return $result;
    }

    function select_user_follows($select_cond){
        $this->db->select('users.nickname, users.avatarurl,users.uid');
        $this->db->from('user_follows');
        $this->db->join('users', 'users.uid = user_follows.target_uid ', 'left');
        $this->db->where($select_cond);
        $this->db->order_by('user_follows.created','desc');
        $query = $this->db->get();
        $result = $query->result(); 
        return $result;
    }

    function select_post_publishes_count($select_cond){
        
        $this->db->select('publishes.*, users.pay_status, users.uid');
        $this->db->from('publishes');
        $this->db->join('users', 'users.uid = publishes.uid ', 'left');
        $this->db->where($select_cond);
        $query = $this->db->get();
        $result = $query->result();
        return count($result);
    }

    function select_post_publishes($select_cond, $start, $perpage){
        $this->db->select('publishes.*, users.pay_status, users.uid');
        $this->db->from('publishes');
        $this->db->join('users', 'users.uid = publishes.uid ', 'left');
        $this->db->where($select_cond);
        $this->db->limit($perpage, $start);
        $this->db->order_by('publishes.created','desc');
        $query = $this->db->get();
        $result = $query->result();
        return $result;
    }

    function select_post_comments($select_cond){
        $this->db->select('user_comments.*, users.nickname, users.uid');
        $this->db->from('user_comments');
        $this->db->join('users', 'users.uid = user_comments.uid ', 'left');
        $this->db->where($select_cond);
        $this->db->order_by('user_comments.created','desc');
        $query = $this->db->get();
        $result = $query->result(); 
        return $result;
    }

    function select_lessions_join_attachments($select_cond=array(), $order = 'desc', $fields="lessions.*"){
        $this->db->select($fields.', attachments.image_path, attachments.type');
        $this->db->from('lessions');
        $this->db->join('attachments', 'attachments.item_id = lessions.id and attachments.type="lession" ', 'left');
        $this->db->where($select_cond);
        $this->db->order_by('lessions.created',$order);
        $query = $this->db->get();
        $result = $query->result(); 
        return $result;
    }

    function select_publishes_join_attachments($uid, $num = "", $offset = "", $limit_time = 0){
        $where = "";
        if( $uid ) {
            $where = " where uid = ". $uid;
        }else {
            $where = " where uid != 0 ";
            
            if ( $limit_time ){
                $where.=" and created > $limit_time";
            }
        }

        $limit_cond = "";    
        if ( $offset ){
            $limit_cond = "limit $num, $offset";
        }

        $sql  = "select s.*, attachments.image_path, attachments.id as aid, attachments.type, users.uid, users.nickname, users.avatarurl";
        $sql .= " from (select *, created as pub_created from  publishes $where order by created desc $limit_cond) s left join attachments on attachments.item_id = s.id and attachments.type='publish' and attachments.image_path !='' ";
        $sql .= " left join users on users.uid = s.uid order by pub_created desc, attachments.id desc";  
        return $this->personal_select($sql);
    }

    function select_user_cover($uid) {     
        $sql = "select count(*) as count ,image_id, image_path  from `image_collects` left join attachments on attachments.id = image_id where image_uid in (select target_uid from user_follows where uid=$uid)  group by image_id order by count desc limit 1";
        return $this->personal_select($sql);
    }

   function select($table,$fields="*",$cond="",$num="",$offset="",$order='',$like='',$or_like='', $where_in=''){

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

        if( !empty($where_in) ){
            foreach($where_in as $key=>$val){
                $this->db->where_in($key,$val);
            }
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
