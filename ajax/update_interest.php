<?php
	include_once '../php_inc/core.inc.php';
	include_once '../php_inc/Media_Validation.php';
	include_once PHP_INC_MODEL.'Interest.php';
	include_once PHP_INC_MODEL.'User_Media_Prefix.php';
	
	if(isset($_FILES["image-label"])  && isset($_POST['imageSet']) ){
		$validator = new Media_Validation();
		if(!$validator->isValidImageFile($_FILES["image-label"]) || !$validator->isValidImageSize($_FILES["image-label"])){
			echo '1'; //invalid media file
			exit();	
		}	
	} 
	$interest = new Interest();
	
	if(!isset($_POST['key']) || empty(trim($_POST['key']))){
		echo '4'; //key is not available
		exit();
	}
	
	
	$description = false;
	if(isset($_POST['description'])){
		$description = trim($_POST['description']);
		$description =(($description == $interest->getInterestDescriptionByInterestId($_POST['key']))?false:$description);

	}

	
	$experience = false;
	if(isset($_POST['experience']) && is_numeric($_POST['experience'])){
		if( $_POST['experience'] >=-1 &&  $_POST['experience'] <=11)
		$experience =  $_POST['experience'];
		$experience =(($experience == $interest->getInterestExperienceByInterestId($_POST['key']))?false:$experience);
	}
	
	$image_label = ((isset($_FILES["image-label"]) && $_POST['imageSet'] == true )?$_FILES["image-label"]:null); //see whether the image is changed or not
	
	
	
	$content = $interest->updateInterestForUserByInterestId($_POST['key'], $_SESSION['id'], $description, $experience, $image_label);
	

?>