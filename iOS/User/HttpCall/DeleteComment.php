<?php
	include_once $_SERVER['DOCUMENT_ROOT'].'/php_inc/core.inc.php';


	if(isset( $_REQUEST['commentId']) ){
		$postComment = new Post_Comment();
		$respondInfo = $postComment->deleteComment($_REQUEST['commentId']);
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