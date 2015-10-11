<?php
	include_once '../php_inc/core.inc.php';
	include_once PHP_INC_MODEL.'Interest_Activity.php';

	if( isset($_POST['last_key']) && !empty(trim($_POST['last_key'])) ){
		$activity = new Interest_Activity();
		$content = $activity->loadMoreFeed(trim($_POST['last_key']));
		if($content !== false){
			echo $content;
		}else{
			echo '1';
		}
	}		
?>