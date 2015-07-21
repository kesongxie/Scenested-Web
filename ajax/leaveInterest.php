<?php
	include_once '../php_inc/core.inc.php';
	include_once MODEL_PATH.'User_In_Interest.php';

	if( isset($_POST['key']) && !empty(trim($_POST['key'])) ){
		$in = new User_In_Interest();
		if($in->leaveInterest($_SESSION['id'],trim($_POST['key'])) === false){
			echo '1';
		}
	}
		
?>