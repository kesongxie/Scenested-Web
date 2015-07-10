<?php
	include_once 'core_table.php';
	include_once 'User_Table.php';
	include_once 'Interest.php';
	
	class Interest_Request extends Noti_Sendable{
		var $table_name = "interest_request";
		public function __construct(){
			parent::__construct($this->table_name);
		}	
		public function send_interest_request($user_id, $user_to_hash, $interest_id){
			$user = new User_Table();
			$user_to = $user->getUserIdByKey($user_to_hash);
			$interest = new Interest();
			if($user_id != $user_to &&  $interest->isInterestEditableByUser($interest_id, $user_id)){
				if(!$this->isRequestAlreadySentForInterestId($user_id, $user_to, $interest_id)){
					$hash = $this->generateUniqueHash();
					$stmt = $this->connection->prepare("INSERT INTO `$this->table_name` (`user_id`,`user_to`,`interest_id`,`sent_time`,`hash`) VALUES(?, ?, ?, ?, ?)");
					$time = date('Y-m-d H:i:s');
					if($stmt){
						$stmt->bind_param('iiiss',$user_id, $user_to, $interest_id, $time, $hash);
						$expire = date('Y-m-d H:i:s', COOKIE_EXPIRE_TIME);
						if($stmt->execute()){
							$stmt->close();
							$row_id = $this->connection->insert_id;
							if($this->noti_queue->addNotificationQueueForUser($user_to, $row_id) !== false){
								return true;
							}
						}
					}
				}
			}
			
			echo $this->connection->error;
			return false;
		}
		
		public function isRequestAlreadySentForInterestId($user_id, $user_to, $interest_id){
			$stmt = $this->connection->prepare("SELECT `id` FROM `$this->table_name` WHERE `user_id`=? AND `user_to`=? AND `interest_id`=? LIMIT 1");
			if($stmt){
				$stmt->bind_param('iii', $user_id, $user_to, $interest_id);
				if($stmt->execute()){
					 $result = $stmt->get_result();
					 if($result->num_rows == 1){
						return true;
					 }
				}
			}
			return false;
		}
		
	}
	
?>