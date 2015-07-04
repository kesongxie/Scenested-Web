<?php
	include_once 'php_inc/core.inc.php';
	include_once PHP_INC_MODEL_ROOT_REF.'Auth_Tokens.php';
	include_once PHP_INC_MODEL_ROOT_REF.'User_Table.php';
	include_once PHP_INC_MODEL_ROOT_REF.'User_Media_Prefix.php';
	
	
	$user = new User_Table();
	
	$media_prefix = new User_Media_Prefix();
	
	$auth_tokens = new Auth_Tokens();
	if(!isset($_SESSION['id']) && !$auth_tokens->auth_token_valified()){
		header('location:'.LOGIN_PAGE);
	}
	$session_user_profile_image_url = $user->getLatestProfilePictureForuser($_SESSION['id']);
	$session_user_access_url = $user->getUserAccessUrl($_SESSION['id']);
	
	
	$request_info = $user->requestUserPageValid($_SERVER['REQUEST_URI']);

	
	if($request_info === false){
		header('location:'.ERROR_PAGE);
		exit();
	}
	$request_user_page_id = $request_info['id'];	
	$user_profile_url = USER_PROFILE_ROOT.$request_info['access_url'];
			
	$page_own = ($request_user_page_id == $_SESSION['id']); // if it's true, allow editting
	$request_user_page_fullname = $user->getUserFullnameByUserIden($request_user_page_id);	
	$request_user_page_firstname = $user->getUserFirstNameByUserIden($request_user_page_id);
	$request_user_page_gender_call = $user->getWhatShouldCallForUser($request_user_page_id);
	
	
	//user media prefix
	$_USER_MEDIA_PREFIX  = $media_prefix->getUserMediaPrefix($request_user_page_id).'/';
	
	require_once 'phtml/profile.phtml';
	
	
?>