<?php
	include_once '../php_inc/core.inc.php';
	include_once PHP_INC_MODEL.'Interest.php';
	
	if(isset($_POST['key']) && !empty(trim($_POST['key']))){
		$interest = new Interest();
		$key = trim($_POST['key']);
		$interest->deletePostForUserByActivityKey($_SESSION['id'], $key);
	}
	
?>