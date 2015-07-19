<?php
	include_once '../php_inc/core.inc.php';
	include_once PHP_INC_MODEL.'Event_Photo.php';

	
	if(isset($_POST['key']) && !empty(trim($_POST['key'])) ){
		$e_p =new Event_Photo();
		$e_p->loadEventPhotoByKey(trim($_POST['key']));
	}
		
?>