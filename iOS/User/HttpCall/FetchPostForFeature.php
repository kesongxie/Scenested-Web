<?php
	include_once $_SERVER['DOCUMENT_ROOT'].'/php_inc/core.inc.php';

	
	if(isset($_REQUEST['featureId'], $_REQUEST['numberOfRequestedRow'])){
		$feature = new Feature($_REQUEST['featureId']);
		$postIdOffset = isset($_REQUEST['postIdOffset'])? $_REQUEST['postIdOffset']: false;
		$respondInfo = $feature->getPosts($postIdOffset, $_REQUEST['numberOfRequestedRow']);
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