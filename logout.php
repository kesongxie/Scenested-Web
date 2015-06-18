<?php
	include_once 'php_inc/core.inc.php';	
	include_once PHP_INC_MODEL_ROOT_REF.'Auth_Tokens.php';
	
	$auth_token = new Auth_Tokens();
	$auth_token->deleteIdentifierAndToken();
	clearLoginCredential();
	header('location:'.LOGIN_PAGE);	
?>