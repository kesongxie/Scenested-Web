<?php
	include_once '../php_inc/core.inc.php';
	include_once PHP_INC_MODEL.'Interest.php';
	
	if(isset($_POST['key']) && !empty(trim($_POST['key']))){
		$interest = new Interest();
		var_dump($interest->deleteInterestForUserByInterestId($_SESSION['id'], $_POST['key']));
		
	}
	
	
		
?>