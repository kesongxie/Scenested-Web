<?php
	include_once '../php_inc/core.inc.php';
	include_once PHP_INC_MODEL.'Event_Invitation.php';
	
	if(isset($_POST['key']) && !empty(trim($_POST['key'])) ){
		$invitation = new Event_Invitation();
		$delete = $invitation->deleteEventInvitation(trim($_POST['key']));
		if($delete === false){
			echo '1';
		}
	}
		
?>