<?php
	include_once $_SERVER['DOCUMENT_ROOT'].'/php_inc/core.inc.php';
	
	if(isset($_REQUEST['userId'], $_REQUEST['deviceToken'])){
		$user = new User($_REQUEST['userId']);
		$respondInfo = $user->updateDeviceToken($_REQUEST['deviceToken']);
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