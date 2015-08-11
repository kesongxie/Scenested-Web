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
	
	$title = "";
	if(!isset($_POST['title'])|| empty(trim($_POST['title']))  ){
		echo '3'; //need a title
		exit();
	}
	$title = trim($_POST['title']);
	
	
	
	$description = "";
	if(!isset($_POST['description'])|| empty(trim($_POST['description']))  ){
		echo '4'; //need desctiption
		exit();
	}
	$description = trim($_POST['description']);
	
	$location = "";
	if(isset($_POST['location']) && !empty(trim($_POST['location']))  ){
		$location = trim($_POST['location']);
	
	}
	
	
	
	
	if(isset($_POST['date']) ){
		if(empty(trim($_POST['date']))){
			$date = null;
		}else{
			if(!validateDate(trim($_POST['date']), $format = 'm-d-Y')){
				echo '5';
				exit();
			}else{
				$date = trim($_POST['date']);
				$date = substr($date,6).'-'.substr($date,0,5);
			}
		}
	}else{
		$date = null;
	}
	
	
	
	if(isset($_POST['time']) ){
		if(empty(trim($_POST['time']))){
			$time = null;
		}else{
			$valid_time = validateTime(trim($_POST['time']));
			if(!$valid_time){
				echo '6';
				exit();
			}else{
				$time = $valid_time;
			}
		}
	}else{
		$date = null;
	}
	
	
	
	
	
	$interest_activity = new Interest_Activity();
	
	
	$attached_picture = ((isset($_FILES["attached-picture"]))?$_FILES["attached-picture"]:null);
	$caption = '';
	if($attached_picture !== null){
		
		if(isset($_POST['caption']) && !empty($_POST['caption'])){
			$caption = $_POST['caption'];
		}
	}
	
	$with_interest_name = isset($_POST['i']);
	$event = $interest_activity->addEventInterestActivityForUserByInterestId($_SESSION['id'],$key, $title,$description,$location, $date, $time, $attached_picture, $caption, $with_interest_name);
	
	
	 if($event !== false){
 		echo $event;
 	}
 	
	
	

?>