<?php
	include_once '../php_inc/core.inc.php';
	include_once PHP_INC_MODEL.'Comment.php';

	if( isset($_POST['key']) && !empty(trim($_POST['key'])) ){
		$comment = new Comment();
		$delete = $comment->deleteComment($_SESSION['id'], $_POST['key']);
		if($delete === false){
			echo '1';
		}
	}
		
?>