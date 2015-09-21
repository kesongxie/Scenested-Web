<?php
	include_once '../php_inc/core.inc.php';
	include_once PHP_INC_MODEL.'Interest_Activity.php';
	
	if(isset($_POST['key']) && !empty(trim($_POST['key'])) && isset($_POST['keys']) && !empty(trim($_POST['keys']))){
		$interest_activity = new Interest_Activity();
		$sent = $interest_activity->sendEventInvitation(trim($_POST['key']), trim($_POST['keys']));
		if($sent !== true){
			echo '1';
		}
	}
?>