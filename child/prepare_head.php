<?php
	include_once 'php_inc/core.inc.php';
	include_once MODEL_PATH.'Message_Queue.php';
	include_once MODEL_PATH.'User_Notification_Queue.php';
	include_once MODEL_PATH.'Notification_Verifier.php';
	include_once PHP_INC_MODEL_ROOT_REF.'Auth_Tokens.php';
	$auth_tokens = new Auth_Tokens();
	if(!isset($_SESSION['id']) && !$auth_tokens->auth_token_valified()){
		header('location:'.LOGIN_PAGE);
	}
	
	$notification_queue = new User_Notification_Queue(null);
	$queue_list = $notification_queue->getNotificationQueueForUser($_SESSION['id']);
	$queues =  ($queue_list != '')? explode(',',trim($queue_list,',' )):false;
	$notification_number = 0;	
	if($queues !== false){
		$notification_verifier = new Notification_Verifier();
		foreach($queues as $queue){
			$queue_info = explode('-',$queue);	
			$code = $queue_info[0];
			$row_id = $queue_info[1];
			if($notification_verifier->isNotificationDisplayable($code, $row_id)){
				$notification_number++;
			}
		}
	}
	
	$m_q = new Message_Queue();
	
	$new_message_notification_num = $m_q->getNewMessageTotalNumForUser($_SESSION['id']);	

	if($new_message_notification_num > 0){
		$title = 'Message('.$new_message_notification_num.')';
	}else if($notification_number > 0){
		$title = 'Notification('.$notification_number.')';
	}else{
		$title = 'Higout';
	}
	
	
?>