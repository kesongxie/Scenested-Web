<?php
	include_once '../php_inc/core.inc.php';
	include_once PHP_INC_PAHT.'Media_Validation.php';
	include_once PHP_INC_MODEL.'User_Profile_Picture.php';

	if(isset($_FILES["profile-pic"])){
		$validator = new Media_Validation();
		if(!$validator->isValidImageFile($_FILES['profile-pic']) || !$validator->isValidImageSize($_FILES["profile-pic"])){
			echo '1'; //invalid media file
			exit();	
		}	
		$user_profile = new User_Profile_Picture();
		$user_profile->uploadProfilePicture($_FILES['profile-pic'],$_SESSION['id']);
	}
	

?>