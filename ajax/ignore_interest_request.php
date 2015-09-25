<?php
	include_once '../php_inc/core.inc.php';
	include_once PHP_INC_MODEL.'Interest_Request.php';

	
	if(isset($_POST['key']) && !empty(trim($_POST['key'])) ){
		$interest_request =new Interest_Request();
		$request = $interest_request->ignoreInterestRequestForUser(trim($_POST['key']), $_SESSION['id']);
		if($request === false){
 			echo '1';
 		}
	}
		
?>