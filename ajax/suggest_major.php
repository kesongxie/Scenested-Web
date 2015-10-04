<?php
	include_once '../php_inc/core.inc.php';
	include_once MODEL_PATH.'Major.php';
	
	if(isset($_POST['name']) && !empty(trim($_POST['name']))){
		$major = new Major();
		echo $major->getSuggestMajorBlockByKeyWord(trim($_POST['name']),10);	
	}
	
?>