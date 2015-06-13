<?php
	include_once 'core_table.php';
	class User_Table extends Core_Table{
		var $table_name = "user";
	
		public function __construct(){
			parent::__construct($this->table_name);
		}
		
		public function registerUser($email, $password, $firstname, $lastname, $gender, $ip, $signupDatetime){
			$stmt = $this->connection->prepare("INSERT INTO `$this->table_name` (`email`,`password`,`firstname`,`lastname`,`gender`,`ip`,`signup_date`) VALUES(?, ?, ?, ?, ?, ?, ?)");
			$stmt->bind_param('sssssss',$email, $password, $firstname, $lastname, $gender, $ip, $signupDatetime);
			if($stmt->execute()){
				$stmt->close();
				return true;
			}else{
				return false;
			}
		}
		
		public function checkUserRegistered($email){
			$stmt = $this->connection->prepare("SELECT `id` FROM `$this->table_name` WHERE `email`= ? LIMIT 1 ");
			$stmt->bind_param('s',$email);
			if($stmt->execute()){
				$result = $stmt->get_result();
				return $result->num_rows;
			}
		}
		
	}
?>