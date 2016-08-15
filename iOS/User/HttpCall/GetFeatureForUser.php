<?php
	include_once $_SERVER['DOCUMENT_ROOT'].'/php_inc/core.inc.php';


	if(isset($_POST['userId'])){
		$user = new User($_POST['userId']);
		$respondInfo = $user->getUserFeatures();
		
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