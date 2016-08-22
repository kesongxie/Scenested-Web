<?php
	include_once $_SERVER['DOCUMENT_ROOT'].'/php_inc/core.inc.php';


	if(isset($_POST['userId'])){
		$user = new User($_POST['userId']);
		$respondInfo = $user->getMultipleUserInfo([User::UserIdKey, User::UserNameKey, User::FullNameKey, User::BioKey, User::AvatorKey, User::CoverKey, User::ProfileVisibleKey, User::ProfileFeatureKey, User::UserPostLikes]);
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