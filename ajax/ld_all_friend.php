<?php
	include_once '../php_inc/core.inc.php';
	include_once PHP_INC_MODEL.'Interest.php';
	
	if(isset($_POST['key']) && !empty($_POST['key'])){
		$in = new Interest();
		$fetch_friend_block = $in->initContentForFriendForUserKey($_POST['key']);
	 	if($fetch_friend_block !== false){
	 		echo $fetch_friend_block;
	 	}else{
	 		echo '1';
	 	}
	}
	
?>