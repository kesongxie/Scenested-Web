<?php
	include_once '../php_inc/core.inc.php';
	include_once PHP_INC_MODEL.'Comment.php';

	if(isset($_POST['keyForComment']) && !empty(trim($_POST['keyForComment'])) && isset($_POST['keyForPost']) && !empty(trim($_POST['keyForPost'])) && isset($_POST['text']) && !empty(trim($_POST['text'])) ){
		$comment = new Comment();
		if(trim($_POST['keyForComment']) != trim($_POST['keyForPost'])){
			echo '1';
			exit();
		}
		$comment_block = $comment->addPostComment($_POST['keyForComment'], $_SESSION['id'], $_POST['text']);
		if($comment_block !== false)
		{
			echo  $comment_block;
		}
	}
		
?>