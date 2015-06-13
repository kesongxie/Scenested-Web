<?php
	include_once '../php_inc/core.inc.php';
	include_once PHP_INC_MODEL.'User_Table.php';
	if(isset($_POST['email']) && !empty($_POST['email'])){
	
		if(!filter_var($_POST['email'], FILTER_VALIDATE_EMAIL)){
			echo '1'; //invalid email address
			exit();
		}
		$user_table = new User_Table();
		if($user_table->checkUserRegistered($_POST['email'])>=1){
			echo '2'; //the email has been used
			exit();
		}
		echo '0';
	}
?>