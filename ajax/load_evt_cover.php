<?php
	include_once '../php_inc/core.inc.php';
	include_once PHP_INC_MODEL.'Interest_Activity.php';
	
	if(isset($_POST['key']) && !empty(trim($_POST['key']))){
		$activity = new Interest_Activity();
		$cover = $activity->getEventCoverForEventByPostKey(trim($_POST['key']));
		if($cover !== false){
			echo $cover;
		}else{
			echo '1';
		}
	}
	
	
		
?>