<?php
	include_once '../php_inc/core.inc.php';
	include_once PHP_INC_MODEL.'Message_Queue.php';
	
	if(isset($_POST['key']) && !empty(trim($_POST['key'])) && isset($_POST['text']) && !empty(trim($_POST['text']))   ){
		$queue = new Message_Queue();
		echo $queue->sentMessage($_SESSION['id'], $_POST['key'], $_POST['text']);
	}
	
	
		
?>