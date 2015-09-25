<?php
	include_once '../php_inc/core.inc.php';
	include_once PHP_INC_MODEL.'Event_Include.php';

	
	if(isset($_POST['key']) && !empty(trim($_POST['key'])) ){
		$include = new Event_Include();
		$request = $include->acceptEventInvitationRequestForUser(trim($_POST['key']), $_SESSION['id']);
		if($request === false){
 			echo '1';
 		}
	}
		
?>