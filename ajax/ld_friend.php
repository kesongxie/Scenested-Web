<?php
	include_once '../php_inc/core.inc.php';
	include_once MODEL_PATH.'User_In_Interest.php';
	
	if(isset($_POST['label_key']) && !empty($_POST['label_key'])){
		$in = new User_In_interest();
		$fetch_friend_block = $in->getUserFriendBlockByInterestId($_POST['label_key'], 4);
	 	if($fetch_friend_block !== false){
	 		echo $fetch_friend_block;
	 	}else{
	 		echo '1';
	 	}
	}
	
?>