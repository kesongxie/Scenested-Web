<?php
	include_once $_SERVER['DOCUMENT_ROOT'].'/php_inc/core.inc.php';


	if(isset($_REQUEST['userId'], $_REQUEST['postId'], $_REQUEST['featureId'])){
		$user = new User($_REQUEST['userId']);
		$respondInfo = $user->deletePost($_REQUEST['postId'], $_REQUEST['featureId']);
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