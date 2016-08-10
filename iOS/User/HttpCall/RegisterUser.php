<?php
	include_once $_SERVER['DOCUMENT_ROOT'].'/php_inc/core.inc.php';

	$userTextualInfo = json_decode($_POST["info"], true);
	$paramInfo =[
		"userTexttualInfo" => $userTextualInfo,
		"userMediaInfo" => $_FILES
	];	
	
	$user = new User();
	$registeredUser = $user->registerUser($paramInfo);
	
	$respondInfo = [ "userId" => $registeredUser->getUserId()];
	
	if($registeredUser !== false){
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