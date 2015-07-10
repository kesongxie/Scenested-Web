<?php
	include_once '../php_inc/core.inc.php';
	include_once PHP_INC_MODEL.'Favor_Event.php';

	if( isset($_POST['key']) && !empty(trim($_POST['key'])) ){
		$favor_evt = new Favor_Event();
		$desc = $favor_evt->getFavorEvtDescByKey(trim($_POST['key']));
		echo $desc['description'].'<div style="margin-top:10px;font-size:13px;opacity:0.8;color: #fff;">'.convertDateTimeToAgo($desc['update_time'], true).'</div>';
	 		
 	}
		
?>