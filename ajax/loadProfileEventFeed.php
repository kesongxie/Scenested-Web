<?php
	include_once '../php_inc/core.inc.php';
	include_once PHP_INC_MODEL.'Interest_Activity.php';

	if( isset($_POST['last_key']) && !empty(trim($_POST['last_key'])) && isset($_POST['key']) && !empty(trim($_POST['key'])) ){
		$activity = new Interest_Activity();
		$content = $activity->loadMoreEventFeed(trim($_POST['last_key']), trim($_POST['key']));
		if($content !== false){
			echo $content;
		}else{
			echo '1';
		}
	}		
?>