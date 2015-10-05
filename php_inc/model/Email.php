<?php 
	require_once  $_SERVER['DOCUMENT_ROOT'].'/php_inc/global_constant.php';
	class Email{
		const SIGNUP_EMAIL_PATH = TEMPLATE_PATH_EMAIL.'signup.phtml';
		const RESET_PASSWORD_EMAIL_PATH = AJAX_TEMPLATE_PATH.'reset_password_email_template.phtml';
	
		public static function getSignUpEmailMessage($email, $firstname, $lastname, $code, $register_id){
			$email = strtolower($email);
			$firstname = ucfirst(strtolower($firstname));
			$lastname = ucfirst(strtolower($lastname));
			ob_start();
			include(self::SIGNUP_EMAIL_PATH);
			$content = ob_get_clean();
			return $content;
		}
		
		public function getResetPasswordEmailMessage($email, $user_id, $key, $hash){
			include_once MODEL_PATH.'User_Table.php';
			$user = new User_Table();
			$fullname = $user->getUserFullnameByUserIden($user_id);
			$reset_dir = ROOTDIR."password.php?reset=".$key."&code=".$hash;
			ob_start();
			include($this->RESET_PASSWORD_EMAIL_PATH);
			$content = ob_get_clean();
			return $content;
		}
	}



?>