<?php
	include_once PHP_INC_MODEL_ROOT_REF.'Interest.php';
	$interest = new Interest();
	$content = $interest->initContentForFriend($request_user_page_id,true);
	//$interest_right_content
	$request_user_page_has_interest = ($content !== false)?true:false;
	$user_interests =$interest->getUserInterestsLabel($request_user_page_id);
	require_once TEMPLATE_PATH_CHILD.'friend.phtml';
?>