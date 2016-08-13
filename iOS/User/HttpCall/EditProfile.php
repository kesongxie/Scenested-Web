<?php
	include_once $_SERVER['DOCUMENT_ROOT'].'/php_inc/core.inc.php';


	//$_POST["info"] contains textual info
	$userInfo = json_decode($_POST["info"], true);
	$paramInfo =[
		"userInfo" => $userInfo,
		"images" => $_FILES
	];	
	$user = new User($paramInfo["userInfo"]["userId"]);
	if($user->saveUserProfile($paramInfo)){
		echo json_encode([
			"statusCode" => 200
		]);
	}else{
		echo json_encode([
			"statusCode" => 500
		]);
	}
 	




	
?>