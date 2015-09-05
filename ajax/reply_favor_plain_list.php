<?php
	include_once '../php_inc/core.inc.php';
	include_once PHP_INC_MODEL.'Reply.php';

	if( isset($_POST['key']) && !empty(trim($_POST['key'])) ){
		$reply = new Reply();
		$list =  $reply->getReplyFavorPlainListByKey(trim($_POST['key']));
		if($list !== false){
			echo $list;
		}else{
			echo '1';
		}
	}		
?>