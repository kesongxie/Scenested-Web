<?php
	include_once 'php_inc/core.inc.php';
	include_once PHP_INC_MODEL_ROOT_REF.'User_Table.php';
	include_once PHP_INC_MODEL_ROOT_REF.'Email_Account_Activation.php';
	if(isset($_GET['id']) && isset($_GET['code']) && !empty($_GET['id']) && !empty($_GET['code'])){
		$email_account_activation = new Email_Account_Activation();
		$user_table = new User_Table();
		if($email_account_activation->checkActivationValid($_GET['id'], $_GET['code'])){
			//activate account request valid
			//delete the row and set to activated
			if($email_account_activation->deleteRowByUserId($_GET['id']) && $user_table->activateUser($_GET['id'])){
				//redirect to index.php
				$_SESSION['id'] = $_GET['id'];
				$email_account_activation->deleteRowAfterActivation($_SESSION['id']);
			}
		}else{
			header('location:'.LOGIN_PAGE);
		}
		
	
	
	}
?>