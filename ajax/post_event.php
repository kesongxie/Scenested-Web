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
	
	
	
	
	$date = "";
	if(isset($_POST['date']) && !empty(trim($_POST['date'])) && !validateDate(trim($_POST['date']), $format = 'm-d-Y')  ){
		echo '5'; //wrong date
		exit();
	}
	$date = trim($_POST['date']);
	$date = substr($date,6).'-'.substr($date,0,5);
	
	
	$time = "";
	$valid_time = validateTime(trim($_POST['time']));
	if(isset($_POST['time']) && !empty(trim($_POST['time'])) && !$valid_time  ){
		echo '6'; //wrong time
		exit();
	}else{
		$time = $valid_time;
	}
	
	
	$interest_activity = new Interest_Activity();
	
	
	$attached_picture = ((isset($_FILES["attached-picture"]))?$_FILES["attached-picture"]:null);
	$caption = '';
	if($attached_picture !== null){
		
		if(isset($_POST['caption']) && !empty($_POST['caption'])){
			$caption = $_POST['caption'];
		}
	}
	
	$event = $interest_activity->addEventInterestActivityForUserByInterestId($_SESSION['id'],$key, $title,$description,$location, $date, $time, $attached_picture, $caption);
	
	
	 if($moment !== false){
 		echo $moment;
 	}
 	
	
	

?>