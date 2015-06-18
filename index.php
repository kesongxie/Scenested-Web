<?php
	include_once 'php_inc/core.inc.php';
	include_once PHP_INC_MODEL_ROOT_REF.'Auth_Tokens.php';
	include_once PHP_INC_MODEL_ROOT_REF.'User_Table.php';
	include_once PHP_INC_MODEL_ROOT_REF.'User_Profile_Picture.php';
	
	
	$user = new User_Table();
	$user_profile_pic = new User_Profile_Picture();
 	$auth_tokens = new Auth_Tokens();
	if(!isset($_SESSION['id']) && $auth_tokens->auth_token_valified()){
		header('location:'.LOGIN_PAGE);
	}

	$session_user_profile_image_url = $user_profile_pic->getLatestProfileImageForUser($_SESSION['id']);
	$session_user_access_url = $user->getUserAccessUrl($_SESSION['id']);
	
	include_once 'phtml/index.phtml';
?>