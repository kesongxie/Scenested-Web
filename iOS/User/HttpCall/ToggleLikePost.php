<?php
	include_once $_SERVER['DOCUMENT_ROOT'].'/php_inc/core.inc.php';


	if(isset($_REQUEST['userId']) && isset($_REQUEST['postId'])){
		$postLike = new Post_Like();
		$respondInfo = $postLike->toggleLikePostForUser($_REQUEST['postId'], $_REQUEST['userId'] );
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