<?php
	include_once 'Email_Code_Validator.php';
	class Retrieve_Account_Code extends Email_Code_Validator{
		public $table_name = "retrieve_account_code";
		public function __construct(){
			parent::__construct($this->table_name);
		}
	}
?>