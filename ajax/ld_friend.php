<?php
	include_once '../php_inc/core.inc.php';
	include_once PHP_INC_MODEL.'User_In_interest.php';
	
	if(isset($_POST['label_key']) && !empty($_POST['label_key'])){
		$in = new User_In_interest();
	 	$fetch_friend_block = $in->getUserFriendBlockByInterestId($_POST['label_key']);
	 	if($fetch_friend_block !== false){
	 		echo $fetch_friend_block;
	 	}else{
	 		echo '1';
	 	}
	}
	
?>