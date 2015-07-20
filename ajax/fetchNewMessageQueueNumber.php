<?php
	include_once '../php_inc/core.inc.php';
	include_once MODEL_PATH.'Message_Queue.php';
	$m_q = new Message_Queue();
	$new_notification_num = $m_q->getNewMessageTotalNumForUser($_SESSION['id']);
	echo $new_notification_num;

?>