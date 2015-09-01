<?php
	include_once '../php_inc/core.inc.php';
	include_once PHP_INC_MODEL.'User_Table.php';

	if( isset($_POST['key']) && !empty(trim($_POST['key']))){
		$user = new User_Table(); 
		$hover_profile = $user->loadUserHoverProfilenByUiqueIden(trim($_POST['key']));
		if($hover_profile !== false){
			echo $hover_profile;
		}
		
	}
?>