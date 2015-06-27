<?php
	include_once '../php_inc/core.inc.php';
	include_once PHP_INC_MODEL.'Reply.php';

	if( isset($_POST['key']) && !empty(trim($_POST['key'])) ){
		$reply = new Reply();
		$delete = $reply->deleteCommentForUserByKey($_SESSION['id'], $_POST['key']);
		if($delete === false){
			echo '1';
		}
	}
		
?>