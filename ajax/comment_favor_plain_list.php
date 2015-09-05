<?php
	include_once '../php_inc/core.inc.php';
	include_once PHP_INC_MODEL.'Comment.php';

	if( isset($_POST['key']) && !empty(trim($_POST['key'])) ){
		$comment = new Comment();
		$list =  $comment->getCommentFavorPlainListByKey(trim($_POST['key']));
		if($list !== false){
			echo $list;
		}else{
			echo '1';
		}
	}		
?>