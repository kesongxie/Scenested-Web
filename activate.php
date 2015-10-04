<?php
	include_once 'php_inc/core.inc.php';
	include_once PHP_INC_MODEL_ROOT_REF.'User_Table.php';
	include_once PHP_INC_MODEL_ROOT_REF.'Email_Account_Activation.php';
	if(isset($_GET['register_id']) && isset($_GET['code']) && !empty($_GET['register_id']) && !empty($_GET['code'])){
		$email_account_activation = new Email_Account_Activation();
		$user_table = new User_Table();
		if($email_account_activation->checkCodeValid($_GET['register_id'], $_GET['code'])){
			//activate account request valid
			//delete the row and set to activated
			if($email_account_activation->deleteEntryForUser($_GET['register_id']) && $user_table->activateUser($_GET['register_id'])){
				//redirect to index.php
				$_SESSION['id'] = $_GET['register_id'];
				header('location:'.HOME_PAGE);
			}
		}else{
			header('location:'.LOGIN_PAGE);
		}
		
	
	
	}
?>