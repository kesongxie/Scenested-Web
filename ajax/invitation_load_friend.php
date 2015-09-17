<?php
	include_once '../php_inc/core.inc.php';
	include_once PHP_INC_MODEL.'Event.php';
	
	if(isset($_POST['key']) && !empty($_POST['key'])){
		$event = new Event();
		$fetch_friend_block = $event->getUserFriendBlockByInterestId($_POST['key']);
		if($fetch_friend_block !== false){
	 		echo $fetch_friend_block;
	 	}else{
	 		echo '1';
	 	}
	}
	
?>