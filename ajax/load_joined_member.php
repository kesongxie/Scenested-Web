<?php
	include_once '../php_inc/core.inc.php';
	include_once PHP_INC_MODEL.'Interest_Activity.php';

	if( isset($_POST['key']) && !empty(trim($_POST['key'])) ){
		$Interest_Activity = new Interest_Activity();
		$members = $Interest_Activity->getJoinedMemberBlockByActivityKey($_POST['key']);
		if($members !== false){
			echo $members;
		}else{
			echo '1';
		}
	}
		
?>