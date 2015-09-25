<?php
	include_once '../php_inc/core.inc.php';
	include_once PHP_INC_MODEL.'Include_Friends.php';
	
	if(isset($_POST['key']) && !empty(trim($_POST['key']))){
		
		echo Include_Friends::loadEventIncludeFriendDialog(trim($_POST['key']));
	}
	
	
		
?>