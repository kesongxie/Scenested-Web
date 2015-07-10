<?php
	include_once MODEL_PATH.'User_Notification_Queue.php';
	$session_user_profile_image_url = $user->getLatestProfilePictureForuser($_SESSION['id']);
	$session_user_access_url = $user->getUserAccessUrl($_SESSION['id']);
	$notification_queue = new User_Notification_Queue(null);
	
	$queue_list = $notification_queue->getNotificationQueueForUser($_SESSION['id']);
	
	$queues =  ($queue_list != '')? explode(',',trim($queue_list,',' )):false;
	
	$notification_number = ($queues !== false)?sizeof($queues):0;
	include_once TEMPLATE_PATH_CHILD.'loggedin_header.phtml';
?>