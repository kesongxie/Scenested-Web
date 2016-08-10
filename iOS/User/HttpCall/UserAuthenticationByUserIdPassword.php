<?php
	include_once $_SERVER['DOCUMENT_ROOT'].'/php_inc/core.inc.php';

	if(isset($_REQUEST['userId']) && isset($_REQUEST['password'])){
		$user = new User();
		$valid = $user->authenticateUserWithUserIdAndPassword($_REQUEST['userId'], $_REQUEST['password']);
		
		if($valid){
			$respondInfo = [
				'valid' => true
			];
		}else{
			$respondInfo = [
				'valid' => false
			];
		}		
		
		echo json_encode([
		"info" => $respondInfo,
		"statusCode" => 200
		]);
			
	
	}else{
		echo json_encode([
		"info" => "",
		"statusCode" => 500
		]);
	}
?>