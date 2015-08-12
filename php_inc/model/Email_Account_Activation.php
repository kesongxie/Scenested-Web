<?php
	include_once 'Email_Code_Validator.php';
	class Email_Account_Activation extends Email_Code_Validator{
		public $table_name = "email_account_activation";
		public function __construct(){
			parent::__construct($this->table_name);
		}
	}
?>