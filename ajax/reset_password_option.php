<?php
	include_once '../php_inc/core.inc.php';
	include_once PHP_INC_MODEL.'User_Table.php';
	
	if(isset($_POST['email']) && !empty(trim($_POST['email']))){
		$user = new User_Table();
		if($user->checkUserRegistered(trim($_POST['email']))){
			$email  = strtolower($_POST['email']);
			$hash = $user->getUniqueIdenForUser($email);
			include(AJAX_TEMPLATE_PATH.'reset_password_option.phtml');
		}else{
			echo '1';
		}
	}
	
	
?>