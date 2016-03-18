<?php
	include_once '../php_inc/core.inc.php';
	
	$user_scene_label = new User_Scene_Label();
	$labels = $user_scene_label->getSceneLabelsForUser($_SESSION['id']);

	include_once 'phtml/include_segues.phtml';
?>