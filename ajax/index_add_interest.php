<?php
	include_once '../php_inc/core.inc.php';
	include_once '../php_inc/Media_Validation.php';
	include_once PHP_INC_MODEL.'Interest.php';
	include_once PHP_INC_MODEL.'User_Media_Prefix.php';
	
	if(isset($_FILES["image-label"])){
		$validator = new Media_Validation();
		if(!$validator->isValidImageFile($_FILES["image-label"]) || !$validator->isValidImageSize($_FILES["image-label"])){
			echo '1'; //invalid media file
			exit();	
		}	
	}
	$interest = new Interest();
	if(!isset($_POST['name']) || empty(trim($_POST['name']))){
		echo '2'; //bad interest name or is not set
		exit();
	}
	
	$name = trim($_POST['name']);
	if($interest->interestExistForUser($name,$_SESSION['id'])){
		echo '3'; //interest has already existed
		exit();
	}
	$description = "";
	if(isset($_POST['description'])){
		$description = trim($_POST['description']);
	}
	
	$experience = -1;
	if(isset($_POST['experience']) && is_numeric($_POST['experience'])){
		if( $_POST['experience'] >=-1 &&  $_POST['experience'] <=11)
		$experience =  $_POST['experience'];
	}
	
	
	
	$image_label = ((isset($_FILES["image-label"]))?$_FILES["image-label"]:null);
	
	$content = $interest->addInterestForUser($_SESSION['id'], $name, $description, $experience, $image_label, false);
	if($content === false){
		echo '1';
	}
?>