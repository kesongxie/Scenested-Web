<?php
	include_once 'php_inc/core.inc.php';
	include_once PHP_INC_MODEL_ROOT_REF.'Auth_Tokens.php';
	include_once PHP_INC_MODEL_ROOT_REF.'Interest.php';
	include_once PHP_INC_MODEL_ROOT_REF.'User_Table.php';
	include_once PHP_INC_MODEL_ROOT_REF.'User_Media_Prefix.php';
	include_once PHP_INC_MODEL_ROOT_REF.'Prepare_Search.php';
	
	
	$user = new User_Table();
	$media_prefix = new User_Media_Prefix();
	
	
	$auth_tokens = new Auth_Tokens();
	if(!isset($_SESSION['id']) && !$auth_tokens->auth_token_valified()){
		header('location:'.LOGIN_PAGE);
	}
		
	$url = parse_url($_SERVER['REQUEST_URI']);
	$BASE_PATH = $url['path'];
		
	$session_user_profile_image_url = $user->getLatestProfilePictureForuser($_SESSION['id']);
	$session_user_access_url = $user->getUserAccessUrl($_SESSION['id']);
		
	$search_result_main_block = '';	
	$_GET['t'] = isset($_GET['t'])?$_GET['t']:null;
	if(isset($_GET['q'])){
		$prepare_search = new Prepare_Search($_GET['q'],$_GET['t']);
		$search_result_main_block =  $prepare_search->getSearchResultMainBlock();
	}else if(isset($_GET['r']) && $_GET['r'] == 'mine'){
		$prepare_search = new Prepare_Search(null,$_GET['t'] , true);
		$search_result_main_block =  $prepare_search->getSearchResultMainBlock();
	}else{
		header('location:'.ERROR_PAGE);
	}
	
	$interest = new Interest();
	$similar_interest_block = $interest->getSimilarInterestBlock();
	require_once 'phtml/search.phtml';
?>