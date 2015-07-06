<?php
	include_once '../php_inc/core.inc.php';
	include_once '../php_inc/Media_Validation.php';
	include_once PHP_INC_MODEL.'Interest_Activity.php';

	if(isset($_FILES["profile-pic"]) && isset($_POST['key']) && !empty(trim($_POST['key']))    ){
		$validator = new Media_Validation();
		if(!$validator->isValidImageFile($_FILES['profile-pic']) || !$validator->isValidImageSize($_FILES["profile-pic"])){
			echo '1'; //invalid media file
			exit();	
		}
		
		
		$interest_activity = new Interest_Activity();
		
		$evt_photo = $interest_activity->uploadEvtPhotoByKey(trim($_POST['key']), $_SESSION['id'], $_FILES["profile-pic"]);
		if($evt_photo === false){
			echo '2'; //can't upload the photo now
		}else{
			echo $evt_photo;
		}
	
		
	}
	

?>