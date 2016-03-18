<?php
	include_once '../php_inc/core.inc.php';
	if(isset($_POST['file_length'], $_FILES) && sizeof($_FILES) != $_POST['file_length']){
		echo '1';
		die();
	}
	$scene = new Scene();
	$result = $scene->addScene($_FILES, $_SESSION['id'], $_POST['scene_caption'], $_POST['scene_date'],  $_POST['scene_label'], $_POST['scene_location']);
	if($result !== false){
		echo $result;
	}else{
		echo '1';
	}
	
		
?>