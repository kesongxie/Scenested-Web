<?php
	include_once '../php_inc/core.inc.php';
	include_once PHP_INC_MODEL.'User_In_interest.php';
	
	if(isset($_POST['url']) && !empty(trim($_POST['url']))){
		$in = new User_In_interest();
		//  /user/kesong.xie/friends/tennis
		
		$fetch_friend_block = $in->getUserFriendBlockByFriendUrl($_POST['url']);
	 	if($fetch_friend_block !== false){
	 		echo $fetch_friend_block;
	 	}else{
	 		echo '1';
	 	}
	}
	
?>