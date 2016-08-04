<?php
	include_once $_SERVER['DOCUMENT_ROOT'].'/php_inc/core.inc.php';
	
	
	
	/*
		 images =     {
        avator =         {
            error = 0;
            name = avator;
            size = 28251;
            "tmp_name" = "/Applications/MAMP/tmp/php/phpcs6Y19";
            type = "image/jpg";
        };
        cover =         {
            error = 0;
            name = cover;
            size = 125772;
            "tmp_name" = "/Applications/MAMP/tmp/php/phpFEQLo5";
            type = "image/jpg";
        };
    };
    userInfo =     {
        bio = "A programming nerd.";
        fullname = "Kesong Xie";
        profileVisible = 0;
    };
	
	*/
	
	$userInfo = json_decode($_POST["info"], true);
	$paramInfo =[
		"userInfo" => $userInfo,
		"images" => $_FILES
	];	
	$user = new User($paramInfo["userInfo"]["userId"]);
	if($user->saveUserProfile($paramInfo)){
		echo json_encode([
			"statusCode" => 200
		]);
	}else{
		echo json_encode([
			"statusCode" => 500
		]);
	}
 	




	
?>