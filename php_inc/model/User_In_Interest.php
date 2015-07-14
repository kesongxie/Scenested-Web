<?php
	include_once 'core_table.php';
	class User_In_Interest extends Core_Table{
		private $table_name = "User_In_Interest";
		public function __construct(){
			parent::__construct($this->table_name);
		}
		
		public function addUserInInterest($interest_id, $user_id, $user_in){
			$hash = $this->generateUniqueHash();
			$stmt = $this->connection->prepare("INSERT INTO `$this->table_name` (`interest_id`,`user_id`,`user_in`,`in_time`,`hash`) VALUES(?, ?, ?, ?, ?)");
			if($stmt){
				$stmt->bind_param('iiiss',$interest_id, $user_id, $user_in, date('Y-m-d H:i:s'), $hash);
				$expire = date('Y-m-d H:i:s', COOKIE_EXPIRE_TIME);
				if($stmt->execute()){
					$stmt->close();
					return $this->connection->insert_id;
				}
			}
			echo $this->connection->error;
			return false;
		}
		
		
		public function isUserInInterest($user_id,$user_in, $interest_id){
			$stmt = $this->connection->prepare("SELECT `hash` FROM `$this->table_name` WHERE `interest_id`=? && `user_in`=? && `user_id`=? LIMIT 1");
			if($stmt){
				$stmt->bind_param('iii',$interest_id, $user_in, $user_id);
				if($stmt->execute()){
					$result = $stmt->get_result();
					if($result && $result->num_rows == 1){
						$stmt->close();
						$row = $result->fetch_assoc();
						return $row['hash'];
					}
				}
			}
			echo $this->connection->error;
			return false;
		}
		
		public function removeUserFromInterest($user_id, $key, $hash){
			include_once 'User_Table.php';
			$user = new User_Table();
			$user_in = $user->getUserIdByKey($key);
			if($user_in !== false){
				$stmt = $this->connection->prepare("DELETE FROM `$this->table_name` WHERE `hash` = ? AND `user_in`=? AND `user_id`=? LIMIT 1");
				if($stmt){
					$stmt->bind_param('sii', $hash, $user_in, $user_id);
					if($stmt->execute()){
						$stmt->close();
						return true;
					}
				}
			}
			return false;
		}
			
		
	}
	
?>