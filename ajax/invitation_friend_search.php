<?php
	include_once '../php_inc/core.inc.php';
	include_once PHP_INC_MODEL.'Prepare_Invitation_Search.php';
	
	if(isset($_POST['q']) && !empty(trim($_POST['q'])) && isset($_POST['key']) && !empty(trim($_POST['key']))  ){
		$search = new Prepare_Invitation_Search();
		$fetch_friend_block = $search->getInvitationSearchContact(trim($_POST['q']), trim($_POST['key']));
		if($fetch_friend_block !== false){
	 		echo $fetch_friend_block;
	 	}else{
	 		echo '1';
	 	}
	}else{
		echo '1';
	}
	
?>