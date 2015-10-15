<?php
	include_once '../php_inc/core.inc.php';
	include_once MODEL_PATH.'User_In_Interest.php';

	if(isset($_POST['key']) && !empty(trim($_POST['key'])) && isset($_POST['u_key']) && !empty(trim($_POST['u_key']))   ){
		$in = new User_In_Interest();
		$fetched_content = $in->loadMoreFriendFeedForInterestId(trim($_POST['u_key']), trim($_POST['key']));
		if($fetched_content !== false){
			echo $fetched_content;
		}else{
			echo '1';
		}
	}		
?>