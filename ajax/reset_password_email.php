<?php
	include_once '../php_inc/core.inc.php';
	include_once MODEL_PATH.'Retrieve_Account_Code.php';
	include_once MODEL_PATH.'Email.php';
	include_once MODEL_PATH.'User_Table.php';

	
	if( isset($_POST['key']) && !empty(trim($_POST['key'])) ){
		$user = new User_Table();
		$retrieve = new Retrieve_Account_Code();
		$user_id = $user->getUserIdByKey(trim($_POST['key']));
		
		if($user_id !== false){
			$to = $user->getUserEmailByUserIden($user_id);
			$hash = $retrieve->generateUniqueHash();
			$subject = 'Password Reset';
			$message = Email::getResetPasswordEmailMessage($to, $user_id, $_POST['key'], $hash );
			$headers  = 'MIME-Version: 1.0' . "\r\n";
			$headers .= 'Content-type: text/html; charset=iso-8859-1' . "\r\n";
			$headers .= 'From: Higout '.SERVER_MAIL_FROM."\r\n";
			if($retrieve->insertEntry($user_id, $hash) && mail($to, $subject, $message, $headers)){
				echo '0';
			}else{
				echo '1';
			}
		}
		
	}
		
?>