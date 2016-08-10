<?php
	include_once $_SERVER['DOCUMENT_ROOT'].'/php_inc/core.inc.php';

	if(isset($_GET['username'])){
		$user = new User();
		$exists = $user->isUserForUserNameExists($_GET['username']);
		
		if($exists){
			$respondInfo = [
				'exists' => 'true'
			];
		}else{
			$respondInfo = [
				'exists' => 'false'
			];
		}		
		
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