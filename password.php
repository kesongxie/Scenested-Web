<?php
	session_start();
	include_once 'php_inc/global_constant.php';
	include_once PHP_INC_MODEL_ROOT_REF.'Auth_Tokens.php';
	include_once PHP_INC_MODEL_ROOT_REF.'Retrieve_Account_Code.php';
	include_once PHP_INC_MODEL_ROOT_REF.'User_Table.php';

	
	$auth_tokens = new Auth_Tokens();
	if(isset($_SESSION['id']) || $auth_tokens->auth_token_valified()){
		header('location:'.HOME_PAGE);
	}
	$retrieve = new Retrieve_Account_Code();
	$user = new User_Table();
	$resetable = false;
	if(isset($_GET['reset']) && isset($_GET['code'])){
		$user_id = $user->getUserIdByKey($_GET['reset']);
		if($user_id !== false){
			$resetable= $retrieve->checkCodeValid($user_id, $_GET['code']);
			if($resetable === false){
				header('location:'.HOME_PAGE);
			}
		}else{
			header('location:'.HOME_PAGE);
		}
	}
	
	require_once 'phtml/password.phtml';


?>