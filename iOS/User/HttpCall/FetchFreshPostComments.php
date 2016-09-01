<?php
	include_once $_SERVER['DOCUMENT_ROOT'].'/php_inc/core.inc.php';


	if(isset($_REQUEST['postId'])){
		$post = new Post();
		$respondInfo = $post->getCommentInfoForPost($_REQUEST['postId']);
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