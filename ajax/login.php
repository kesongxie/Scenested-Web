<?php
	include_once '../php_inc/core.inc.php';
	include_once PHP_INC_MODEL.'User_Table.php';
	include_once PHP_INC_MODEL.'Auth_Tokens.php';

	if(isset($_POST['data']) && !empty($_POST['data'])){
		$user_table = new User_Table();
		$auth_tokens = new Auth_Tokens();
		parse_str($_POST['data'], $data);
		
		$iden = trim($data['login-iden']);
		if(empty($iden)){
			echo '1'; //empty email address
			exit();
		}
		
		if(!filter_var($iden, FILTER_VALIDATE_EMAIL)){
			echo '1'; //invalid email address
			exit();
		}
		
		$password = trim($data['login-password']);
		if(empty($password)){
			echo '1'; //password empty
			exit();
		}
		if(strlen($password) < 6){
			echo '1'; //password too short
			exit();
		}
		
		
		if($user_table->availableToLogin($iden, $password)){
			//login the user
			$_SESSION['id'] = $user_table->getUserInfoByUserIden('id',$iden);
			if(isset($data['login-remember']) && $data['login-remember'] == 'on'){
				//delete previous identifier and token
				if(isset($_COOKIE['identifier'])){
					//delete row with this selector
					$auth_tokens->deleteRowBySelector('selector',$_COOKIE['identifier']);
					clearLoginCredential();
				}
				//update cookie
				$auth_tokens->tokenGenerator();
			}
			echo '0';
		}else{
			echo '1';
		}
		
		
		
		
			




	}
?>