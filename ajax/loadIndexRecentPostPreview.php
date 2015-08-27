<?php
	include_once '../php_inc/core.inc.php';
	include_once PHP_INC_MODEL.'Interest.php';
	
	$interest = new Interest();
	$content =  $interest->getIndexInterestPreviewBlock($_SESSION['id']);
	if($content !== false){
		echo $content;
	}else{
		echo '1';
	}
	
	
?>