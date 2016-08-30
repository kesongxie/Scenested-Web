<?php
	include_once $_SERVER['DOCUMENT_ROOT'].'/php_inc/core.inc.php';


	if(!isset($_FILES) || !isset($_POST)){
		exit();
	}
	
	$userInfo = json_decode($_POST["info"], true);
	$paramInfo =[
		"userInfo" => $userInfo,
		"images" => $_FILES
	];	
	$user = new User($paramInfo["userInfo"]["userId"]);
	
	
	
	
	$result = $user->sharePost($paramInfo);
	if($result !== false){
		if($result['status']){
			echo json_encode([
				"statusCode" => 200,
				"post" => $result["post"],
				"postCountInFeature" => $result["postCountInFeature"],
				"errorCode" => false
			]);
		}else{
			echo json_encode([
				"statusCode" => 200,
				"post" => false,
				"postCountInFeature" => $result["postCountInFeature"],
				"errorCode" => $result['errorCode']
			]);
		}
	}else{
		echo json_encode([
			"statusCode" => 500
		]);
	}
 	
 	



	
?>