<?php
	session_start();
	include_once 'php_inc/global_constant.php';
	include_once PHP_INC_MODEL_ROOT_REF.'Auth_Tokens.php';
	if(isset($_SESSION['id'])){
		unset($_SESSION['id']);
	}
	setcookie ("identifier", "", time() - 3600, '/');
	setcookie ("token", "", time() - 3600, '/');
	$auth_token = new Auth_Tokens();
	$auth_token->deleteIdentifierAndToken();
	
	header('location:'.LOGIN_PAGE);	
?>