<?php
	include_once $_SERVER['DOCUMENT_ROOT'].'/php_inc/core.inc.php';

	if(isset($_REQUEST['postId'])){
		$post_like = new Post_Like();
		$respondInfo = $post_like->getLikeUserInfoForPost($_REQUEST['postId']);
		
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