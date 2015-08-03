<?php
	include_once PHP_INC_MODEL_ROOT_REF.'Abouts.php';
	$abouts = new Abouts();
	echo  $abouts->getEducationBlockForUser($request_user_page_id);
	
?>