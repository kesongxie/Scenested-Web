<?php
	include_once '../php_inc/core.inc.php';
	include_once PHP_INC_MODEL.'Reply.php';

	if( isset($_POST['key']) && !empty(trim($_POST['key'])) ){
		$Reply = new Reply();
		echo $Reply->getReplyLikeNumberByKey(trim($_POST['key']));
	}		
?>