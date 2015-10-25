<?php

	include_once 'php_inc/core.inc.php';
	include_once PHP_INC_MODEL_ROOT_REF.'User_Table.php';
	include_once PHP_INC_MODEL_ROOT_REF.'Auth_Tokens.php';

	$auth_tokens = new Auth_Tokens();
	$user = new User_Table();

	if(!isset($_SESSION['id']) && !$auth_tokens->auth_token_valified()){
		include_once 'phtml/login_about.phtml';
	}else{
		include_once 'phtml/loggedin_about.phtml';
	}
	
?>