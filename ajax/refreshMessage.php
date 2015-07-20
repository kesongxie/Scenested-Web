<?php
	include_once '../php_inc/core.inc.php';
	include_once PHP_INC_MODEL.'Message_Queue.php';
	$m_q = new Message_Queue();
	echo  $m_q->getMessageQueueBlockForUserId($_SESSION['id']);
?>