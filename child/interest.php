<?php
	include_once PHP_INC_MODEL_ROOT_REF.'Interest.php';
	include_once PHP_INC_MODEL_ROOT_REF.'Default_User_Interest_Label_Image.php';
	$interest = new Interest();
	$defualr_label_image = new Default_User_Interest_Label_Image();
	$content = $interest->initContentForInterest($request_user_page_id,true);
	//$interest_right_content
	$request_user_page_has_interest = ($content !== false)?true:false;
	$user_interests =$interest->getUserInterestsLabel($request_user_page_id);
	require_once TEMPLATE_PATH_CHILD.'interest.phtml';
?>