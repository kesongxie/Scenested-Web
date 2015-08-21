<?php
	include_once '../php_inc/core.inc.php';
	include_once PHP_INC_MODEL.'Favor_Activity.php';

	if( isset($_POST['key']) && !empty(trim($_POST['key'])) ){
		$f = new Favor_Activity(trim($_POST['key']));
		echo $f->getFavorNumForActivity();
	}		
?>