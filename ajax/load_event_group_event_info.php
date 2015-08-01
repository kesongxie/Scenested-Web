<?php
	include_once '../php_inc/core.inc.php';
	include_once PHP_INC_MODEL.'Groups.php';

	if( isset($_POST['key']) && !empty(trim($_POST['key'])) ){
		$g = new Groups();
		$info = $g->getEventGroupEventInfoByGroupKey($_POST['key']);
		if($info !== false){
			echo $info;
		}else{
			echo '1';
		}
	}
		
?>