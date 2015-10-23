<?php
	include_once '../php_inc/core.inc.php';
	include_once MODEL_PATH.'User_Notification_Queue.php';
	include_once MODEL_PATH.'Notification_Verifier.php';
	$notification_queue = new User_Notification_Queue(null);
	$queue_list = $notification_queue->getNotificationQueueForUser($_SESSION['id']);
	$queues =  ($queue_list != '')? explode(',',trim($queue_list,',' )):false;
	$count = 0;	
	if($queues !== false){
		$notification_verifier = new Notification_Verifier();
		foreach($queues as $queue){
			$queue_info = explode('-',$queue);	
			$code = $queue_info[0];
			$row_id = $queue_info[1];
			if($notification_verifier->isNotificationDisplayable($code, $row_id)){
				$count++;
			}
		}
	}
	echo  $count;
?>