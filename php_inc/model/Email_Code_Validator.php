<?php
	include_once 'core_table.php';
	class Email_Code_Validator extends Core_Table{
		public function __construct($table_name){
			parent::__construct($table_name);
		}
		
		public function insertEntry($user_id, $code){
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
		
		public function checkCodeValid($user_id, $code){
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
		
		public function deleteEntryWhenFinished($user_id){
			$this->deleteRowByUserId($user_id);	
		}
		
	}
?>