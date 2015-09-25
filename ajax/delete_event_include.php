<?php
	include_once '../php_inc/core.inc.php';
	include_once PHP_INC_MODEL.'Event_Include.php';
	
	if(isset($_POST['key']) && !empty(trim($_POST['key'])) ){
		$include = new Event_Include();
		$delete = $include->deleteEventInvitation(trim($_POST['key']));
		var_dump($delete);
		if($delete === false){
			echo '1';
		}
	}
		
?>