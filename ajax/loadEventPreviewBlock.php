<?php
	include_once '../php_inc/core.inc.php';
	include_once PHP_INC_MODEL.'Interest_Activity.php';

	if( isset($_POST['key']) && !empty(trim($_POST['key'])) ){
		$Interest_Activity = new Interest_Activity();
		$preview_block = $Interest_Activity->loadEventPreviewBlockByKey($_POST['key'], $_SESSION['id']);
		if($preview_block !== false){
			echo $preview_block;
		}
	}
		
?>