<?php
	include_once '../php_inc/core.inc.php';
	include_once PHP_INC_MODEL.'Favor_Event.php';
	
	if(isset($_POST['title']) && !empty(trim($_POST['title'])) &&  isset($_POST['desc']) && !empty(trim($_POST['desc'])) ){
		$favor_evt = new Favor_Event();
		echo $favor_evt->addFavorEventForUser(trim($_POST['title'],'#'),trim($_POST['desc']), $_SESSION['id']  );
	}
	
?>