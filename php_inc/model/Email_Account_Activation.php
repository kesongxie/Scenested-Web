<?php
	include_once 'core_table.php';
	class Email_Account_Activation extends Core_Table{
		public $table_name = "email_account_activation";
	
		public function __construct(){
			parent::__construct($this->table_name);
		}
		
		public function insertRegisterEntry($user_id, $code){
			if($this->deleteRowByUserId($user_id)){
				$stmt = $this->connection->prepare("INSERT INTO `$this->table_name` (`user_id`,`code`) VALUES(?, ?)");
				if($stmt){
					$stmt->bind_param('is',$user_id,$code);
					if($stmt->execute()){
						$stmt->close();
						return true;
					}
				}
			}
			return false;
		}
		
		public function checkActivationValid($user_id, $code){
			$stmt = $this->connection->prepare("SELECT `id` FROM `$this->table_name` WHERE `user_id`=? && `code`=? LIMIT 1");
			if($stmt){
				$stmt->bind_param('is',$user_id,$code);
				if($stmt->execute()){
					$result = $stmt->get_result();
					if($result && $result->num_rows == 1){
						$stmt->close();
						return true;
					}
				}
			}
			return false;
		}
	}
?>