<?php
function get_common_userinfo($db, $uid){
    $userinfo = $db->select('users', '*', array('uid' => $uid));
    $userinfo = $userinfo[0];
    $userinfo->nickname = base64_decode($userinfo->nickname);
    $userinfo->userinfo = base64_decode($userinfo->userinfo);
    return $userinfo;
}

function get_collect_stack_list($db, $uid){
	$collects = $db->select_collections();
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
		$im_path = $base_url.$val->image_path;
		$collections[$key]->image_path = $im_path;
		$urls.= $im_path.";";
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

function deal_image_path($publishes, $uid, $db){
	$final_result = array();
	$base_url     = rtrim(base_url(), '/');
	foreach($publishes as $key => $val){
		if ( $uid != 0 ){
			$is_collect= 0 ;
			$collect = $db->select('image_collects','id',array('user_id' => $uid, 'image_id' => $val->aid));
			if ( $collect ){
				$is_collect = 1;
			}
			$final_result[$val->id]['image_path_collect'][] = array('url'=> $base_url.$val->image_path, 'is_collect' => $is_collect, 'aid' => $val->aid);
		}
		$final_result[$val->id]['image_path'][] = $base_url.$val->image_path;
		$final_result[$val->id]['description']  = $val->description;
		$final_result[$val->id]['post_id']      = $val->id;
		$final_result[$val->id]['desc_length']  = strlen($val->description);	
		$final_result[$val->id]['created']      = $val->created;
		$final_result[$val->id]['nickname']     = $val->nickname;
		$final_result[$val->id]['avatarurl']    = $val->avatarurl;
		$final_result[$val->id]['uid']    		= $val->uid;
		$final_result[$val->id]['time_info']    = deal_post_time($val->created);
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
		$publishes[$key]['image_urls'] = implode(';', $val['image_path']);
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
