<?php
	include_once '../php_inc/core.inc.php';
	include_once PHP_INC_MODEL.'User_Media_Base.php';

	if( isset($_POST['key']) && !empty(trim($_POST['key'])) && isset($_POST['from'])  &&  !empty(trim($_POST['from'])) ){
		$base = new User_Media_Base();
		$base->getPreviewImage(trim($_POST['key']),trim($_POST['from']) );
	}
		
?>