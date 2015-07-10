<?php
	include_once '../php_inc/core.inc.php';
	include_once MODEL_PATH.'User_Notification_Queue.php';
	$queue = new User_Notification_Queue();
	$queue->updateQueueToReadQueue($_SESSION['id']);
?>