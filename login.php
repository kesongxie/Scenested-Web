<?php
	session_start();
	include_once 'php_inc/global_constant.php';
	include_once PHP_INC_MODEL_ROOT_REF.'Auth_Tokens.php';
	
	$auth_tokens = new Auth_Tokens();
	if(isset($_SESSION['id']) || $auth_tokens->auth_token_valified()){
		header('location:'.HOME_PAGE);
	}
	
	include_once 'phtml/login.phtml';
	

?>

