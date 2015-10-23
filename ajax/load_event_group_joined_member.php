<?php
	include_once '../php_inc/core.inc.php';
	include_once MODEL_PATH.'Groups.php';

	if( isset($_POST['key']) && !empty(trim($_POST['key'])) ){
		$g = new Groups();
		$members = $g->getGroupJoinedMemberByGroupKey($_POST['key']);
		if($members !== false){
			echo $members;
		}else{
			echo '1';
		}
	}
		
?>