<?php
	include_once '../php_inc/core.inc.php';
	include_once PHP_INC_MODEL.'Event.php';
	
	$event = new Event();
	$fetch_friend_block = $event->getAllUserFriendBlock();
	if($fetch_friend_block !== false){
		echo $fetch_friend_block;
	}else{
		echo '1';
	}
	
	
?>