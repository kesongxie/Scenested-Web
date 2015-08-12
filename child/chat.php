<?php
	include_once PHP_INC_MODEL_ROOT_REF.'Message_Queue.php';
	include_once PHP_INC_MODEL_ROOT_REF.'Education.php';
	
	$m_q = new Message_Queue();
	$contact_block = $m_q->getMessageQueueBlockForUserId($_SESSION['id']);
	$educ = new Education();
	$school_name = $educ->getSchoolName();
	include_once TEMPLATE_PATH_CHILD.'chat.phtml';
	
	
?>