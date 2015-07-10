<?php
	include_once '../php_inc/core.inc.php';
	include_once PHP_INC_MODEL.'Interest_Request.php';

	
	if(isset($_POST['keyForTarget']) && !empty(trim($_POST['keyForTarget'])) && isset($_POST['keyForInterest']) && !empty(trim($_POST['keyForInterest'])) ){
		$interest_request =new Interest_Request();
		$request = $interest_request->send_interest_request($_SESSION['id'], trim($_POST['keyForTarget']), trim($_POST['keyForInterest']));
		if($request === false){
			echo '1';
		}
	}
		
?>