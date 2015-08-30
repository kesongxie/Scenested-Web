<?php
	include_once '../php_inc/core.inc.php';
	include_once PHP_INC_MODEL.'Education.php';
	
	$educ = new Education();
	$educ->removeStudyForSessionUser();
?>