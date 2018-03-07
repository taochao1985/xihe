<?php

function generate_star($count){
  $str = "";
  for( $i = 0 ; $i < $count ; $i++ ){
    $str.="#";
  }
  return $str;
}

function restrict_word_check($content, $repalce_flag = true) {

    $resTrie = trie_filter_load('./dirty_words.dic'); 
    $keyword_res = trie_filter_search_all($resTrie, $content); 
    $filted_words = array();
    if ( count($keyword_res) > 0 ){
      foreach ( $keyword_res as $key => $val ){
        $filter_start = $val[0];
        $filter_end   = $val[1];
        $filted_words[] = substr($content, $filter_start, $filter_end);
        if ( $repalce_flag ){
          $star = generate_star($filter_end);
          $content = substr_replace($content, $star, $filter_start, $filter_end);
        }
      }
      return array(
        'content' => $content,
        'words'   => implode(' ', $filted_words)
      );
    }else{
      return array();
    }
}

	function access_token(){
	    $access = file_get_contents("https://api.weixin.qq.com/cgi-bin/token?grant_type=client_credential&appid=wx798a94ca49cd4396&secret=e3e147c2c0d0eec5566734f9639c1e4b");
	    $result = json_decode($access,true);
	    //print_r($result);
	    if( $result['access_token'] ){
	        return $result['access_token'];
	    }else{
	        return NULL;
	    }
	}

function get_access_token(){
	$expire = 7000;
  $tokenFile= 'assets/token_file.txt';
  $lastTimeFile= 'assets/last_time.txt';

  if(!file_exists($tokenFile)){
      $fh = fopen($tokenFile,"w");
      fclose($fh);
  }

  if(!file_exists($lastTimeFile)){
      $fh = fopen($lastTimeFile,"w");
      fclose($fh);
  }

  $needLogin=true;
  $nowTime=time();
  if($lastTime=file_get_contents($lastTimeFile)){

  }else{
      $lastTime=0;
  }

  if(($nowTime-$lastTime)<=$expire){
      $needLogin=false;
  }

  if($needLogin==true){
      $token = access_token();
      if($token){
          file_put_contents($lastTimeFile,$nowTime);
          file_put_contents($tokenFile,$token);
           
          return $token;
      }else{
          return false;
      }
  }else{

      if($token=file_get_contents($tokenFile)){
           
          return $token;
      }else{
          return false;
      }
  }
}

function api_notice_increment($url, $data){
    $ch = curl_init($url);
    $header = "Accept-Charset: utf-8"; 
    curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
    curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, FALSE);
    //curl_setopt($curl, CURLOPT_HTTPHEADER, $header);
    curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (compatible; MSIE 5.01; Windows NT 5.0)');
    curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
    curl_setopt($ch, CURLOPT_AUTOREFERER, 1);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    $tmpInfo = curl_exec($ch);
    if (curl_errno($ch)) {
        return false;
    }else{
        return $tmpInfo;
    }
}

function get_common_userinfo($db, $uid, $fields = '*'){
    $userinfo = $db->select('users', $fields, array('uid' => $uid));
    $userinfo = $userinfo[0];
    if ( $fields == '*' ){
      $userinfo->nickname = base64_decode($userinfo->nickname);
      if ($userinfo->userinfo) {
        $userinfo->userinfo = base64_decode($userinfo->userinfo);
      }else {
        $userinfo->userinfo = "";
      }
    }
    return $userinfo;
}

function get_collect_stack_list($db, $uid, $start=0){
	$collects = $db->select_collections($start);
  $collects = reoragnize_collections($collects, $uid, $db);
  return $collects;
}

function deal_post_time($time){
	$minus_time = time() - $time;
	if ( $minus_time < 3600 ){
		return ceil($minus_time / 60).'分钟前';
	}else if ( $minus_time < 3600 * 24 ){
		return ceil($minus_time / 3600).'小时前';
	}else{
		return date('Y-m-d', $time);
	}
}

function reoragnize_collections($collections, $uid, $db){
	$urls = "";
	$base_url = rtrim(base_url(), '/');
	foreach( $collections as $key => $val ){
    $collections[$key]->image_path_origin = $base_url.$val->image_path;
    $urls.= $base_url.$val->image_path.";";
		$im_path = $base_url.get_rename_image($val->image_path);
		$collections[$key]->image_path = $im_path;
		
		$mine_check = $db->select('image_collects', '*', array('image_id' => $val->image_id, 'user_id' => $uid ));
		$collect = 0;
		if( $mine_check ){
			$collect = 1;
		}
		$collections[$key]->is_collect = $collect;
	}
	return array('collections' => $collections, 'urls' => $urls);
}

function reoragnize_follows($follows, $uid, $db){
	foreach( $follows as $key => $val ){
		$mine_follow = $db->select('user_follows', '*', array('uid' => $uid, 'target_uid' => $val->target_uid ));
		$follow = 0;
		if( $mine_follow ){
			$follow = 1;
		}
		$follows[$key]->is_follow = $follow;
	}
	return $follows;
}

function get_rename_image($path){
  if( $path ){
    $name = explode('.', $path);
    return $name[0].'_thumb.'.$name[1];  
  }else{
    return $path;
  }
  
}

function deal_image_path($publishes, $uid, $db){
	$final_result = array();
	$base_url     = rtrim(base_url(), '/');
	foreach($publishes as $key => $val){

    if( $val->image_path ){
      $image_path = $base_url.get_rename_image($val->image_path);
  		if ( $uid != 0 ){
  			$is_collect= 0 ;
  			$collect = $db->select('image_collects','id',array('user_id' => $uid, 'image_id' => $val->aid));
  			if ( $collect ){
  				$is_collect = 1;
  			}
  			$final_result[$val->id]['image_path_collect'][] = array('url'=> $image_path, 'is_collect' => $is_collect, 'aid' => $val->aid);
  		}

  		$final_result[$val->id]['image_path2'][] = $base_url.$val->image_path;
      $final_result[$val->id]['image_path'][] = $image_path;
  		$final_result[$val->id]['description']  = $val->description;
  		$final_result[$val->id]['post_id']      = $val->id;
  		$final_result[$val->id]['desc_length']  = strlen($val->description);	
  		$final_result[$val->id]['created']      = $val->created;
  		$final_result[$val->id]['nickname']     = $val->nickname;
  		$final_result[$val->id]['avatarurl']    = $val->avatarurl;
  		$final_result[$val->id]['uid']    		= $val->uid;
  		$final_result[$val->id]['time_info']    = deal_post_time($val->created);

    }
	}
	return $final_result;
}
/*
*   reorganize publishes data
*/

function reorganize_publishes($publishes, $db=''){
	$publishes = deal_image_path($publishes, 0, '');

	$final_result = array(); 
	foreach($publishes as $key => $val){
		$date = date('Y-m-d', $val['created']);
		$comments = $db->select_post_comments(array('post_id'=>$val['post_id']));
		if ( count($comments) > 0 ){
			foreach($comments as $k => $v){
				$comments[$k]->nickname = base64_decode($v->nickname);
			}
		}
		$temp_data = array(
			'id'          => $key,
			'description' => $val['description'],
			'comments'    => $comments,
			'image_path'  => $val['image_path'],
			'image_url'   => implode(';', $val['image_path'])
		); 
		$final_result[] = array(
			'date' => date('Y-m-d', $val['created']),
			'data' => $temp_data,
			'date_day' => date('d', $val['created']),
			'date_month' => date('m月', $val['created'])
		);
	}
	return $final_result;
}


function check_friend_follow($db, $uid, $target_uid){
	$check_follow = $db->select('user_follows', '*', array('uid' => $uid, 'target_uid' => $target_uid));
	if( $check_follow ){
		return 1;
	}else {
		return 0;
	}
}


function reorganize_index_publishes($db, $publishes, $uid){
	$publishes = deal_image_path($publishes, $uid, $db);
	$post_array = array();
	foreach($publishes as $key => $val){
		$post_array[] = $key;
		$comments = $db->select_post_comments(array('post_id'=>$val['post_id']));
		if ( count($comments) > 0 ){
			foreach($comments as $k => $v){
				$comments[$k]->nickname = base64_decode($v->nickname);
			}
		}
		$follow   = check_friend_follow($db, $uid, $val['uid']);
		$publishes[$key]['is_follow'] = $follow;
		$publishes[$key]['image_urls'] = implode(';', $val['image_path2']);
		$publishes[$key]['comments'] = $comments;
		$publishes[$key]['nickname'] = base64_decode($val['nickname']);
	}
	array_multisort($post_array, SORT_DESC , $publishes);
	return $publishes;
}

/*
* 	save image to attachments
*/
function save_image($db, $data){
	$id = $db->insert('attachments', $data);
	return $id;
}

/*
* 	delete image from attachments
*/
function delete_image($db, $cond){
	$attachments = $db->select('attachments', 'id', $cond);
	foreach( $attachments as $key => $val ){
		$db->delete('image_collects', array('image_id' => $val->id));
		$db->delete('attachments', array('id' => $val->id));
	}
	return true;
}
