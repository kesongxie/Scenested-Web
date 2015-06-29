<?php
	include_once '../php_inc/core.inc.php';
	include_once PHP_INC_MODEL.'Interest_Activity.php';

	if( isset($_POST['key']) && !empty(trim($_POST['key'])) ){
		$Interest_Activity = new Interest_Activity();
		$comment_block = $Interest_Activity->getCommentBlockByActivityKey($_POST['key']);
		if($comment_block !== false){
			echo $comment_block;
		}
	}
		
?>