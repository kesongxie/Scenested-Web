<?php
	include_once 'php_inc/core.inc.php';
	include_once PHP_INC_MODEL_ROOT_REF.'Auth_Tokens.php';
	$auth_tokens = new Auth_Tokens();
	if($auth_tokens->auth_token_valified()){
		header('location:'.HOME_PAGE);
	}
	include_once 'phtml/login.phtml';
	

?>

