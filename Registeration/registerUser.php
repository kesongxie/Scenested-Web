<?php
	include_once '../php_inc/core.inc.php';
	$data = [];
	// 
// 	$_POST['username'] = "cxcsajask";
// 	$_POST['password'] = "chuchuc";
// 	$_POST['deviceToken'] = "askjfnksafksajfkjasfaj";
	if (isset($_POST['username']) && isset($_POST['password']) && isset($_POST['deviceToken'])){
		$user = New User();
		$registeredUser = $user->registerUser($_POST['username'], $_POST['password'], $_POST['deviceToken']);
		if($registeredUser !== false){
			$data = [
				'userInfo' => [
								'user_id' => $registeredUser->getUserId(),
								'username' => $registeredUser->getUserName(),
								'deviceToken' => $registeredUser->getDeviceToken()
				],
				'statusCode' => '200',
				'error' => ''
			];
		}
	}else{
		$data = [
		'statusCode' => '300',
		'error' => 'username or password not set'
		];
	}
	
	echo json_encode($data);
?>