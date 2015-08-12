<?php
	include_once '../php_inc/core.inc.php';
	include_once PHP_INC_MODEL.'Retrieve_Account_Code.php';
	include_once PHP_INC_MODEL.'User_Table.php';


	if( isset($_POST['key']) && !empty(trim($_POST['key'])) ){
		$user = new User_Table();
		$retrieve = new Retrieve_Account_Code();
		$user_id = $user->getUserIdByKey(trim($_POST['key']));
		if($user_id !== false){
			$firstname = $user->getUserFirstNameByUserIden($user_id);
			$to = $user->getUserEmailByUserIden($user_id);
			$hash = $retrieve->generateUniqueHash();
			$subject = 'Lsere account activation';
			echo $reset_dir = ROOTDIR."password.php?reset=".trim($_POST['key'])."&code=".$hash;

			ob_start();
			include('phtml/reset_password_email_template.phtml');
			$message = ob_get_clean();
			// To send HTML mail, the Content-type header must be set
			$headers  = 'MIME-Version: 1.0' . "\r\n";
			$headers .= 'Content-type: text/html; charset=iso-8859-1' . "\r\n";

			// Additional headers
			$headers .= 'From: Lsere <kesongxie1993@gmail.com>'."\r\n";
			
			$retrieve->insertEntry($user_id, $hash);
			
			// if($retrieve->insertEntry($user_id, $hash) && mail($to, $subject, $message, $headers)){
// 				echo 'true';
// 			}else{
// 				echo 'no';
// 			}
		}
		
	}
		
?>