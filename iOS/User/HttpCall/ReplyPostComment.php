<?php
	include_once $_SERVER['DOCUMENT_ROOT'].'/php_inc/core.inc.php';
	
	
	if(isset($_REQUEST['replyInfo'])){
		$postCommentReply = new Post_Comment_Reply();
		
		var_dump($_REQUEST['replyInfo']);
		exit();
		$respondInfo = $postCommentReply->insertReply($_REQUEST['replyInfo'])
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