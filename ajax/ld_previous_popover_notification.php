<?php
	include_once '../php_inc/core.inc.php';
	include_once MODEL_PATH.'User_Notification_Queue.php';
	include_once MODEL_PATH.'Notification_Renderer.php';
	
	
    $notification_queue = new User_Notification_Queue(null);
	$queue_list = $notification_queue->getNotificationReadQueueForUser($_SESSION['id']);
	$queues =  ($queue_list != '')? explode(',',trim($queue_list,',' )):false;
	$notification_number = (($queues !== false)?sizeof($queues):0);
	$isFresh = false;
	$renderer = new Notification_Renderer();
	ob_start();
 	include(AJAX_TEMPLATE_PATH.'popover_notification.phtml');
	$content = ob_get_clean();
	echo $content;
?>