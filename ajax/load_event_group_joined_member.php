<?php
	include_once '../php_inc/core.inc.php';
	include_once PHP_INC_MODEL.'Groups.php';

	if( isset($_POST['key']) && !empty(trim($_POST['key'])) ){
		$g = new Groups();
		$members = $g->getEventGroupJoinedMemberByGroupKey($_POST['key']);
		if($members !== false){
			echo $members;
		}else{
			echo '1';
		}
	}
		
?>