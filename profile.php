<?php
	include_once 'php_inc/core.inc.php';
	$_SESSION['id'] = 1;
	$user_profile_cover = new User_Profile_Cover();
	$user_profile_cover_image = $user_profile_cover->getLatestProfileImageForUser($_SESSION['id']);
	
	
	$user_profile_avator =  new User_Profile_Avator();
	$user_profile_avator_image = $user_profile_avator->getLatestProfileImageForUser($_SESSION['id']);
	
	
	$user_bio = new User_Bio();
	$bio = $user_bio->getBioForUser($_SESSION['id']);
	
	$user_scene_label = new User_Scene_Label();
	$labels = $user_scene_label->getSceneLabelsForUser($_SESSION['id']);
	
	include_once TEMPLATE_PATH.'profile.phtml';
?>