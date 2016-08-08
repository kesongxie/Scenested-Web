<?php
	include_once $_SERVER['DOCUMENT_ROOT'].'/php_inc/core.inc.php';

	$userTextualInfo = json_decode($_POST["info"], true);
	$paramInfo =[
		"userTexttualInfo" => $userTextualInfo,
		"userMediaInfo" => $_FILES
	];	
	
	$user = new User();
	if($user->registerUser($paramInfo)){
		echo json_encode([
			"statusCode" => 200
		]);
	}else{
		echo json_encode([
			"statusCode" => 500
		]);
	}
	
?>