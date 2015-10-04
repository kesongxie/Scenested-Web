<?php
	include_once MODEL_PATH.'Core_Table.php';
	class Email_Code_Validator extends Core_Table{
		private $table_name;
		public function __construct($table_name){
			parent::__construct($table_name);
			$this->table_name = $table_name;
		}
		
		public function insertEntry($user_id, $code){
			if($this->deleteEntryForUser($user_id)){
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
		
		public function deleteEntryForUser($user_id){
			return $this->deleteRowByUserId($user_id);	
		}
		
	}
?>