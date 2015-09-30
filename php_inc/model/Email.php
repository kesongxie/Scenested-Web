<?php 
	require_once  $_SERVER['DOCUMENT_ROOT'].'/php_inc/global_constant.php';
	class Email{
		const SIGNUP_EMAIL_PATH = TEMPLATE_PATH_EMAIL.'signup.phtml';
	
		public static function getSignUpEmailMessage($email, $firstname, $lastname){
			ob_start();
			include(self::SIGNUP_EMAIL_PATH);
			$content = ob_get_clean();
			return $content;
		}
	}



?>