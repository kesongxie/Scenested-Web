<?php
	include_once '../php_inc/core.inc.php';
	include_once PHP_INC_MODEL.'Event_Invitation.php';

	
	if(isset($_POST['key']) && !empty(trim($_POST['key'])) ){
		$invitation = new Event_Invitation();
		$request = $invitation->ignoreEventInvitationRequestForUser(trim($_POST['key']), $_SESSION['id']);
		if($request === false){
 			echo '1';
 		}
	}
		
?>