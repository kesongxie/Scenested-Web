<?php
	include_once '../php_inc/core.inc.php';
	include_once PHP_INC_MODEL.'Education.php';
	
	if(isset($_POST['name']) && !empty(trim($_POST['name']))){
		$education = new Education();
		$save = $education->addMajorForUser(trim($_POST['name']));	
		if($save === false){
			echo '1';
		}
	}
	
?>