<?php
	include_once '../php_inc/core.inc.php';
	if(isset($_FILES["file"])){
		$validator = new Media_Validation();
		
		if(!$validator->isValidImageFile($_FILES['file']) || !$validator->isValidImageSize($_FILES["file"])){
			echo '1'; //invalid media file
			exit();	
		}	
		if(isset($_POST['image_container_scale_width'], $_POST['image_container_scale_height'], $_POST['adjusted_ratio_width'], $_POST['adjusted_ratio_height'] ) ){
				$user = new User($_SESSION['id']);
				$url = $user->saveUserProfileCover($_FILES['file'], true, $_POST);
	 			echo ($url !== false) ? $url : '1';
		}
	}
?>