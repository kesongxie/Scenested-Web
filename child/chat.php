<?php
	include_once PHP_INC_MODEL_ROOT_REF.'Message_Queue.php';
	
	
	$m_q = new Message_Queue();
	$contact_block = $m_q->getMessageQueueBlockForUserId($_SESSION['id']);
	//$m_q->reArrangeMessageQueueForUser($_SESSION['id']);
	
	include_once TEMPLATE_PATH_CHILD.'chat.phtml';
?>