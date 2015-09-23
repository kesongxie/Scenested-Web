<?php
	include_once '../php_inc/core.inc.php';
	include_once PHP_INC_MODEL.'Interest_Activity.php';

	if( isset($_POST['key']) && !empty(trim($_POST['key'])) ){
		$activity = new Interest_Activity();
		$list =  $activity->loadEventInvitedList(trim($_POST['key']));
		if($list !== false){
			echo $list;
		}else{
			echo '1';
		}
	}		
?>