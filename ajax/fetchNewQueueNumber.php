<?php
	include_once '../php_inc/core.inc.php';
	include_once MODEL_PATH.'User_Notification_Queue.php';
	$notification_queue = new User_Notification_Queue(null);
	$queue_list = $notification_queue->getNotificationQueueForUser($_SESSION['id']);
	$queues =  ($queue_list != '')? explode(',',trim($queue_list,',' )):false;
	echo  (($queues !== false)?sizeof($queues):0);
?>