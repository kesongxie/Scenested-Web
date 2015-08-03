<?php
	include_once '../php_inc/core.inc.php';
	include_once PHP_INC_MODEL.'School.php';
	
	if(isset($_POST['name']) && !empty(trim($_POST['name']))){
		$school = new School();
		echo $school->getSuggestSchoolBlockByKeyWord(trim($_POST['name']),10);	
	}
	
?>