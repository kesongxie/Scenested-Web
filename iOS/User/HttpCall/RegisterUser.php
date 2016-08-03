<?php
	include_once $_SERVER['DOCUMENT_ROOT'].'/php_inc/core.inc.php';

	if(isset($_POST['username']) && isset($_POST['password'])){
		$user = new User();
		$registeredUser = $user->registerUser($_POST['username'], $_POST['password']);
		$respondInfo[HttpRequestResponse::UserIdKey] = $registeredUser->getUserId();
		$respondInfo[HttpRequestResponse::UserNameKey] = $registeredUser->getUserName();
		
		echo json_encode([
		"info" => $respondInfo,
		"statusCode" => "200"
		]);
			
	
	}else{
		echo json_encode([
		"info" => "",
		"statusCode" => "500"
		]);
	}
?>