<?php
	include_once $_SERVER['DOCUMENT_ROOT'].'/php_inc/core.inc.php';
	
	
	if(isset($_REQUEST['commentInfo'])){
		$postComment = new Post_Comment();
		$respondInfo = $postComment->insertComment($_REQUEST['commentInfo']);
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
