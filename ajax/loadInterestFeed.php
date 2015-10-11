<?php
	include_once '../php_inc/core.inc.php';
	include_once PHP_INC_MODEL.'Interest.php';

	if( isset($_POST['last_key']) && !empty(trim($_POST['last_key'])) && isset($_POST['key']) && !empty(trim($_POST['key'])) ){
		$interest = new Interest();
		$content = $interest->loadMoreInterestFeed(trim($_POST['last_key']), trim($_POST['key']));
		if($content !== false){
			echo $content;
		}else{
			echo '1';
		}
	}		
?>