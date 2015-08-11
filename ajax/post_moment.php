<?php
	include_once '../php_inc/core.inc.php';
	include_once '../php_inc/Media_Validation.php';
	include_once PHP_INC_MODEL.'Interest.php';
	include_once PHP_INC_MODEL.'Interest_Activity.php';
	
	if(isset($_FILES["attached-picture"])){
		$validator = new Media_Validation();
		if(!$validator->isValidImageFile($_FILES["attached-picture"]) || !$validator->isValidImageSize($_FILES["attached-picture"])){
			echo '1'; //invalid media file
			exit();	
		}	
	}
	
	
	if(!isset($_POST['key']) || empty(trim($_POST['key'])) || !is_numeric($_POST['key'])  ){
		echo '2'; //bad key
		exit();
	}
	$key = trim($_POST['key']);
	$interest = new Interest();
	
	if(!$interest->isInterestEditableByUser($key, $_SESSION['id'])){
		echo '2';
		exit();
	}
	
	
	$description = "";
	
	if(!isset($_POST['description'])|| empty(trim($_POST['description']))  ){
		echo '3'; //need desctiption
		exit();
	}
	$description = trim($_POST['description']);
	
	$date = "";
	if(!isset($_POST['date'])|| empty(trim($_POST['date'])) || !validateDate(trim($_POST['date']), $format = 'm-d-Y')  ){
		echo '4'; //need a date
		exit();
	}
	$date = trim($_POST['date']);
	$date = substr($date,6).'-'.substr($date,0,5);
	
	$interest_activity = new Interest_Activity();
	
	
	$attached_picture = ((isset($_FILES["attached-picture"]))?$_FILES["attached-picture"]:null);
	$caption = '';
	if($attached_picture !== null){
		if(isset($_POST['caption']) && !empty($_POST['caption'])){
			$caption = $_POST['caption'];
		}
	}
	
	$with_interest_name = isset($_POST['i']);
	$moment = $interest_activity->addMomentInterestActivityForUserByInterestId($_SESSION['id'],$key, $description, $date, $attached_picture, $caption, $with_interest_name);
	if($moment !== false){
		echo $moment;
	}
	
	
	
	

?>