<?php
	include_once 'php_inc/core.inc.php';
	include_once PHP_INC_MODEL_ROOT_REF.'Auth_Tokens.php';
	include_once PHP_INC_MODEL_ROOT_REF.'User_Table.php';
	include_once PHP_INC_MODEL_ROOT_REF.'User_Profile_Picture.php';
	include_once PHP_INC_MODEL_ROOT_REF.'User_Profile_Cover.php';

	
	
	$user = new User_Table();
	$user_profile_pic = new User_Profile_Picture();
	$user_profile_cover = new User_Profile_Cover();

 	$auth_tokens = new Auth_Tokens();
	if(!isset($_SESSION['id']) && $auth_tokens->auth_token_valified()){
		header('location:'.LOGIN_PAGE);
	}

	$session_user_profile_image_url = $user_profile_pic->getLatestProfileImageForUser($_SESSION['id']);
	$session_user_access_url = $user->getUserAccessUrl($_SESSION['id']);
	
	$request_path = pathinfo($_SERVER['REQUEST_URI']);
	
	$request_user_page_id = $user->requestUserPageValid($request_path['basename']);
	if($request_user_page_id === false){
		header('location:'.ERROR_PAGE);
		exit();
	}
	
	$page_own = ($request_user_page_id == $_SESSION['id']);
	$request_user_page_fullname = $user->getUserFullnameByUserIden($request_user_page_id);	
	$request_user_page_firstname = $user->getUserFirstNameByUserIden($request_user_page_id);
	$request_user_page_gender_call = $user->getWhatShouldCallForUser($request_user_page_id);
	require_once 'phtml/profile.phtml';
	
?>