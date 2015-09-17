<?php
	include_once '../php_inc/core.inc.php';
	include_once PHP_INC_MODEL.'Event.php';
	
	if(isset($_POST['key']) && !empty(trim($_POST['key']))){
		$event = new Event();
		echo $event->loadEventInvitationDialog(trim($_POST['key']));
	}
	
	
		
?>