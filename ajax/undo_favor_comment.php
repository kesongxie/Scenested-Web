<?php
	include_once '../php_inc/core.inc.php';
	include_once PHP_INC_MODEL.'Comment.php';

	if( isset($_POST['key']) && !empty(trim($_POST['key'])) ){
		$comment = new Comment();
		$undo = $comment->undoFavorComment(trim($_POST['key']));
	}		
?>