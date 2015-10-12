<?php
	include_once '../php_inc/core.inc.php';
	include_once PHP_INC_MODEL.'Prepare_Search.php';
	
	$search_obj = unserialize($_SESSION['search_obj']);
	$fetched_content = $search_obj->loadMoreContentEvent();
	if($fetched_content !== false){
		echo $fetched_content;
	}else{
		echo '1';
	}
	
?>
