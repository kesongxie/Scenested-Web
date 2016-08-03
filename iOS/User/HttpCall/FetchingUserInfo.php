<?php
	include_once '../../php_inc/core.inc.php';
	$_POST['userId'] = 34;
	if(isset($_POST['userId'])){
		$user = new User($_POST['userId']);
		$user_info = $user->getMultipleUserInfo([User::IdKey, User::UserNameKey, User::FullNameKey, User::BioKey, User::AvatorKey, User::CoverKey]);
		$respondInfo[HttpRequestResponse::UserIdKey] = $user_info[User::IdKey];
		$respondInfo[HttpRequestResponse::UserNameKey] = $user_info[User::UserNameKey];
		$respondInfo[HttpRequestResponse::AvatorPathKey] = ($user_info[User::AvatorKey] === false) ? "": $user_info[User::AvatorKey];
		$respondInfo[HttpRequestResponse::CoverPathKey] =  ($user_info[User::CoverKey] === false) ? "": $user_info[User::CoverKey];
		$respondInfo[HttpRequestResponse::BioKey] = ($user_info[User::BioKey] === false) ? "": $user_info[User::BioKey]  ;
		echo json_encode([
		"info" => $respondInfo,
		"statusCode" => "200"
		]);
			
	
	}else{
		echo json_encode([
		"info" => "",
		"statusCode" => "500"
		]);
	}
?>