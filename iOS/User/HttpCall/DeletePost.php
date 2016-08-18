<?php
	include_once $_SERVER['DOCUMENT_ROOT'].'/php_inc/core.inc.php';


	if(isset($_REQUEST['userId']) && isset($_REQUEST['postId'])){
		$user = new User($_REQUEST['userId']);
		$respondInfo = $user->deletePost($_REQUEST['postId']);
		echo json_encode([
		"info" => ["status"=> $respondInfo],
		"statusCode" => 200
		]);
			
	
	}else{
		echo json_encode([
		"info" => "",
		"statusCode" => 500
		]);
	}
?>