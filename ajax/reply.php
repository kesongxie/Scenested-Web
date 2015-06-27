<?php
	include_once '../php_inc/core.inc.php';
	include_once PHP_INC_MODEL.'Reply.php';

	if(isset($_POST['keyForComment']) && !empty(trim($_POST['keyForComment'])) && isset($_POST['keyForTarget']) && !empty(trim($_POST['keyForTarget'])) && isset($_POST['text']) && !empty(trim($_POST['text'])) ){
		$reply = new Reply();
		if(trim($_POST['keyForComment']) != trim($_POST['keyForTarget'])){
			echo '1';
			exit();
		}
		
		$reply_block = $reply->addReplyToComment($_POST['keyForComment'], $_SESSION['id'], $_POST['text']);
		if($reply_block !== false)
		{
			echo  $reply_block;
		}
	}
		
?>