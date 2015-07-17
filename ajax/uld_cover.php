<?php
	include_once '../php_inc/core.inc.php';
	include_once '../php_inc/Media_Validation.php';
	include_once PHP_INC_MODEL.'User_Profile_Cover.php';

	if(isset($_FILES["profile-pic"])){
		$validator = new Media_Validation();
		if(!$validator->isValidImageFile($_FILES['profile-pic']) || !$validator->isValidImageSize($_FILES["profile-pic"])){
			echo '1'; //invalid media file
			exit();	
		}	
		$user_cover = new User_Profile_Cover();
		$user_cover->uploadCoverPicture($_FILES['profile-pic'],$_SESSION['id']);
	}
	

?>