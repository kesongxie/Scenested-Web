<?php
	include_once '../php_inc/core.inc.php';
	include_once PHP_INC_MODEL.'Message_Queue.php';
	
	if(isset($_POST['key']) && !empty(trim($_POST['key']))){
		$queue = new Message_Queue();
		echo $queue->loadMessageChatBoxByKey($_SESSION['id'], $_POST['key']);
	}
	
	
		
?>