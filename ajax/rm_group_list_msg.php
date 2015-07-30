<?php
	include_once '../php_inc/core.inc.php';
	include_once PHP_INC_MODEL.'Message_Queue.php';

	if( isset($_POST['key']) && !empty(trim($_POST['key'])) ){
		$queue = new Message_Queue(); 
		$delete = $queue->removeGroupFromMessageQueueByKey(trim($_POST['key']));
		if($delete === false){
			echo '1';
		}
	}
?>