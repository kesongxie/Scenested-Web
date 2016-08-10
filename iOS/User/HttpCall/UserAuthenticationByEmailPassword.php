<?php
	include_once $_SERVER['DOCUMENT_ROOT'].'/php_inc/core.inc.php';

	if(isset($_REQUEST['email']) && isset($_REQUEST['password'])){
		$user = new User();
		$userId = $user->authenticateUserWithEmailAndPassword($_REQUEST['email'], $_REQUEST['password']);
		
		if($userId !== false){
			$respondInfo = [
				'valid' => true,
				'userId'=> $userId
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