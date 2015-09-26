<?php
	include_once '../php_inc/core.inc.php';
	include_once PHP_INC_MODEL.'Event.php';
	
	if(isset($_POST['post_key']) && !empty(trim($_POST['post_key']))){
		$event = new Event();
		$fetch_friend_block = $event->getIncludeAllUserFriendBlock(trim($_POST['post_key']));
		if($fetch_friend_block !== false){
	 		echo $fetch_friend_block;
	 	}else{
	 		echo '1';
	 	}
	}
?>