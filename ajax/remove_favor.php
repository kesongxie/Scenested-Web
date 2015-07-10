<?php
	include_once '../php_inc/core.inc.php';
	include_once PHP_INC_MODEL.'Favor_Event.php';
	
	if(isset($_POST['key']) && !empty(trim($_POST['key'])) ){
		$favor_evt = new Favor_Event();
		echo $favor_evt->removeFavor(trim($_POST['key']), $_SESSION['id']);
	}
	
?>