<?php
	include_once PHP_INC_MODEL_ROOT_REF.'Favor_Event.php';
	$favor_evt_label = new Favor_Event();
	echo $favor_evt_label->getFavorEventBlockForUser($_SESSION['id']);

?>