<?php
	include_once '../php_inc/core.inc.php';
	include_once MODEL_PATH.'User_Table.php';
	include_once MODEL_PATH.'Auth_Tokens.php';
	include_once MODEL_PATH.'Email.php';
	include_once PHP_INC_MODEL.'Email_Account_Activation.php';
	
	
	
	
	
	if(isset($_POST['data']) && !empty($_POST['data'])){
		$user_table = new User_Table();
		$email_account_activation = new Email_Account_Activation();

		parse_str($_POST['data'], $data);
		
		$email = trim($data['signup-iden']);
		if(empty($email)){
			echo '1'; //empty email address
			exit();
		}
		
		if(!filter_var($email, FILTER_VALIDATE_EMAIL)){
			echo '2'; //invalid email address
			exit();
		}
		
		if($user_table->checkUserRegistered($email)){
			echo '3'; //the email has been used
			exit();
		}
		
		
		
		$firstname = trim($data['signup-firstname']);
		if(empty($firstname)){
			echo '5'; //firstname empty
			exit();
		}
		
		
		$lastname = trim($data['signup-lastname']);
		if(empty($lastname)){
			echo '6'; //lastname empty
			exit();
		}
		
		$password = trim($data['signup-password']);
		if(empty($password)){
			echo '7'; //password empty
			exit();
		}
		if(strlen($password) < 6){
			echo '8'; //password too short
			exit();
		}

		
		$gender = trim($data['signup-gender']);
		if(empty($gender) || ($gender != '1' &&  $gender != '2') ){
			echo '10'; //gender is empty
			exit();
		}
		
		
		//valid parameters
		$ip = $_SERVER['REMOTE_ADDR'];
		$signupDatetime = date('Y-m-d H:i:s');
		$code=md5(rand(100000,999999));
		$register_id = $user_table->registerUser($email, $password, $firstname, $lastname, $gender, $ip, $signupDatetime);
		
		if($register_id !== false){
			//send email
			$to  = $data['signup-iden']; 
			$subject = 'Account Activation';
			$rootDir = substr(ROOTDIR,0, strlen(ROOTDIR)-1);
			$message = EMAIL::getSignUpEmailMessage($email, $firstname, $lastname, $code, $register_id);
			$headers  = 'MIME-Version: 1.0' . "\r\n";
			$headers .= 'Content-type: text/html; charset=iso-8859-1' . "\r\n";
			$headers .= 'From: Higout <no-reply@lsere.com>'."\r\n";
			if($email_account_activation->insertEntry($register_id, $code) && mail($to, $subject, $message, $headers)){
				$user_table = new User_Table();
				$auth_tokens = new Auth_Tokens();
				if($user_table->availableToLogin($email, $password)){
					//login the user
					$_SESSION['id'] = $register_id;
						//delete previous identifier and token
					if(isset($_COOKIE['identifier'])){
						//delete row with this selector
						$auth_tokens->deleteRowBySelector('selector',$_COOKIE['identifier']);
						clearLoginCredential();
					}
					//update cookie
					$auth_tokens->tokenGenerator();
					echo '0';
				}
			}else{
				echo '11'; //email failed
			}
		
		}
		
		

	}
?>