<?php
	include_once '../php_inc/core.inc.php';
	include_once PHP_INC_MODEL.'Interest_Activity.php';
	$interest_activity = new Interest_Activity();
	echo $interest_activity->loadUpComingEventCollectionForUser($_SESSION['id']);

		
?>