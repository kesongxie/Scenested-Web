<?php
	include_once PHP_INC_MODEL_ROOT_REF.'Interest.php';
	include_once PHP_INC_MODEL_ROOT_REF.'User_In_Interest.php';
	$interest = new Interest();
	$in = new User_In_Interest();
	$active_interest_id = -1;
	$user_has_friend = true;
	if(isset($_GET['a'])){
		$interest_id = $interest->getInterestIdByNameForUser($_GET['a'], $request_user_page_id);
		if($interest_id !== false){
			$content = $in->getUserFriendBlockByInterestId($interest_id);
			$active_interest_id = $interest_id;
		}else{
			$content = $interest->initContentForFriend($request_user_page_id,true);
		}
	}else{
		$content = $interest->initContentForFriend($request_user_page_id,true);
	}
	
	if($content === false){
		$user_has_friend = false;
		$content = $interest->getInitialFriendsBlock($request_user_page_id);
	}

	$request_user_page_has_interest = ($content !== false)?true:false;
	$user_interests =$interest->getUserInterestsLabel($request_user_page_id, 'friends');
	require_once TEMPLATE_PATH_CHILD.'friend.phtml';
?>