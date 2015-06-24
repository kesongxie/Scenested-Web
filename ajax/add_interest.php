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
	
	$content = $interest->addInterestForUser($_SESSION['id'], $name, $description, $experience, $image_label);
	$url = $interest->getLabelImageUrl();
	if($content !== false){
		echo '<div id="node-mid-content">'.$content.'</div>';
		
		if($image_label == null){
			$url = getDefaultInterestLabelImageByNum($url);
		}else{
			$mediaPrefix = new User_Media_Prefix();
			$url = U_IMGDIR.$mediaPrefix->getUserMediaPrefix($_SESSION['id']).'/'.$url;
		}
		echo '<div id="node-side-content"><div class="interest-side-label interest-sider-navi pointer" data-labelfor="'.$interest->new_interest_id.'" title="'.htmlentities($name).'" ><div class="vertical-center"><img src="'.$url.'"><div class="inline-blk txt_ofl" style="width:95px;margin-top:2px;">'.htmlentities($name).'</div></div></div></div>';
	
	}
	
	
	

?>