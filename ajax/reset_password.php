<?php
	include_once '../php_inc/core.inc.php';
	include_once PHP_INC_MODEL.'User_Table.php';

	if( isset($_POST['key']) && !empty(trim($_POST['key'])) && isset($_POST['p']) && !empty($_POST['p'])  ){
		$user = new User_Table(); 
		$user_id = $user->getUserIdByKey(trim($_POST['key']));
		
		if(!checkPasswordValid($_POST['p'])){
			echo '1';
			exit();
		}
		if($user_id !== false){
			$reset = $user->resetPassword(trim($_POST['p']), $user_id);
		}
	}
?>