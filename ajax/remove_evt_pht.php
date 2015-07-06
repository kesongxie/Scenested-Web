<?php
	include_once '../php_inc/core.inc.php';
	include_once PHP_INC_MODEL.'Event_Photo.php';

	if( isset($_POST['key']) && !empty(trim($_POST['key'])) ){
		$pht = new Event_Photo();
		$delete = $pht-> deleteEventPhotoByKeyForUser(trim($_POST['key']), $_SESSION['id']);
		if($delete === false){
			echo '1';
		}
	}
		
?>