<?php
	include_once $_SERVER['DOCUMENT_ROOT'].'/php_inc/core.inc.php';


	//$_POST["info"] contains textual info
	$userInfo = json_decode($_POST["info"], true);
	$paramInfo =[
		"userInfo" => $userInfo,
		"images" => $_FILES
	];	
	$user = new User($paramInfo["userInfo"]["userId"]);
	$result = $user->addUserFeature($paramInfo);

	if($result !== false){
		if($result['status']){
			echo json_encode([
				"statusCode" => 200,
				Feature::FeatureKey => $result[Feature::FeatureKey],
				"errorCode" => false
			]);
		}else{
			echo json_encode([
				"statusCode" => 200,
				Feature::FeatureKey => false,
				"errorCode" => $result['errorCode']
			]);
		}
	}else{
		echo json_encode([
			"statusCode" => 500
		]);
	}
 	
 	



	
?>