<?php
	include_once $_SERVER['DOCUMENT_ROOT'].'/php_inc/core.inc.php';


	if(isset($_REQUEST['username']) && isset($_REQUEST['password'])){
		$user = new User();
		$userId = $user->authenticateUserWithUserNameAndPassword($_REQUEST['username'], $_REQUEST['password']);
		
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