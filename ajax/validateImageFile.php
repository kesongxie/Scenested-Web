<?php
	include_once '../php_inc/core.inc.php';
	$validator = new Media_Validation();
	if( !$validator->isValidImageFile($_FILES['file']) || !$validator->isValidImageSize($_FILES['file'])  ){
		echo '1';
	}else{
		echo '0';
	}
?>