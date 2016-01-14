<?php
	include_once '../php_inc/core.inc.php';
	if(isset($_POST['scene_name'])){
		$user_scene_label = new User_Scene_Label();
		$result = $user_scene_label->addSceneForUser($_POST['scene_name'], $_SESSION['id']);
		echo ($result === false)?'1':$result;
	}
?>