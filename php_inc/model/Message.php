<?php
	include_once 'core_table.php';
	class Message extends Core_Table{
		private  $table_name = "Message";
		private  $contact_block_template_path = TEMPLATE_PATH_CHILD."contact_block.phtml";		
		private  $own_dialog_template_path = TEMPLATE_PATH_CHILD.'own_dialog.phtml';
		private  $others_dialog_template_path = TEMPLATE_PATH_CHILD.'others_dialog.phtml';
		private  $sent_from  = 'm-';


		public function __construct(){
			parent::__construct($this->table_name);
		}
		
		public function renderMessageContactOfGivenUser($user_id){
			include_once 'User_Profile_Picture.php';
			$profile = new User_Profile_Picture();
			$profile_pic = $profile->getLatestProfileImageForUser($user_id);
			include_once 'User_Table.php';
			$user = new User_Table();
			$fullname = $user->getUserFullnameByUserIden($user_id);
			$hasMessage = false;
			$latest_message = $this->getLatestMessageWithGivenUser($user_id);
			$new_message_num = $this->hasNewMessageFromGivenUser($_SESSION['id'], $user_id);

			if($latest_message !== false){
				$time = convertDateTimeToAgo($latest_message['sent_time'], false,true, true);
				$text = $latest_message['text'];
				if($latest_message['user_sent'] == $_SESSION['id']){
					$text = 'Me: '.$text;
				}
				$hasMessage = true;
			}
			
			$user_iden = $user->getUniqueIdenForUser($user_id);
			ob_start();
			include($this->contact_block_template_path);
			$content = ob_get_clean();
			return $content;
		}
		
		public function getLatestMessageWithGivenUser($user_id){
			$stmt = $this->connection->prepare("SELECT `user_id` AS `user_sent`,`text`,`sent_time` FROM `$this->table_name` WHERE  (`user_id`=? AND `user_id_get`=? ) || (`user_id_get`=? AND `user_id`=?) ORDER BY `id` DESC LIMIT 1    ");
			if($stmt){
				$stmt->bind_param('iiii',$_SESSION['id'], $user_id, $_SESSION['id'], $user_id);
				if($stmt->execute()){
					 $result = $stmt->get_result();
					 if($result !== false && $result->num_rows == 1){
						$rows = $result->fetch_assoc();
						$stmt->close();
						return $rows;
					 }
				}
			}
			return false;
		}
		
		
		public function getUnreadMessagesForUser($user_id, $user_with){
			$stmt = $this->connection->prepare("SELECT `user_id`,`user_id_get`,`text`,`sent_time` FROM `$this->table_name` WHERE  (`user_id`=? AND `user_id_get`=? ) AND `view`='n'  ");
			if($stmt){
				$stmt->bind_param('ii',$user_with, $user_id);
				if($stmt->execute()){
					 $result = $stmt->get_result();
					 if($result !== false && $result->num_rows >= 1){
					 	$this->updateMesasgeToSeenFromGivenUser($user_id, $user_with);
 						$rows = $result->fetch_all(MYSQLI_ASSOC);
						$stmt->close();
						$content = "";
						foreach($rows as $row){
							include_once 'User_Profile_Picture.php';
							$profile = new User_Profile_Picture();
							$profile_pic = $profile->getLatestProfileImageForUser($row['user_id']);
							include_once 'User_Table.php';
							$user = new User_Table();
							$fullname = $user->getUserFullnameByUserIden($row['user_id']);
							$text = $row['text'];
							ob_start();
							if($row['user_id'] == $user_id){
								//render my own dialog
								include($this->own_dialog_template_path);
							}else{
								//render other person's dialog
								include($this->others_dialog_template_path);
							}
							$content .= ob_get_clean();
						}
						return $content;
					 }
				}
			}
			return false;
		}
		
		
		public function getMessagesForUser($user_id, $user_with){
			$stmt = $this->connection->prepare("SELECT `user_id`,`user_id_get`,`text`,`sent_time` FROM `$this->table_name` WHERE  (`user_id`=? AND `user_id_get`=? ) || (`user_id_get`=? AND `user_id`=? )  ");
			if($stmt){
				$stmt->bind_param('iiii',$user_id, $user_with, $user_id, $user_with);
				if($stmt->execute()){
					 $result = $stmt->get_result();
					 if($result !== false && $result->num_rows >= 1){
					 	$this->updateMesasgeToSeenFromGivenUser($user_id, $user_with);
 						$rows = $result->fetch_all(MYSQLI_ASSOC);
						$stmt->close();
						$content = "";
						foreach($rows as $row){
							include_once 'User_Profile_Picture.php';
							$profile = new User_Profile_Picture();
							$profile_pic = $profile->getLatestProfileImageForUser($row['user_id']);
							include_once 'User_Table.php';
							$user = new User_Table();
							$fullname = $user->getUserFullnameByUserIden($row['user_id']);
							$text = $row['text'];
							ob_start();
							if($row['user_id'] == $user_id){
								//render my own dialog
								include($this->own_dialog_template_path);
							}else{
								//render other person's dialog
								include($this->others_dialog_template_path);
							}
							$content .= ob_get_clean();
						}
						return $content;
					 }
				}
			}
			return false;
		}
		
		
		public function updateMesasgeToSeenFromGivenUser($user_id, $user_with){
			$stmt = $this->connection->prepare("UPDATE `$this->table_name` SET `view`='y' WHERE `user_id` = ? AND `user_id_get`=? ");
			if($stmt){
				$stmt->bind_param('ii', $user_with, $user_id);
				if($stmt->execute()){
					$stmt->close();
					return true;
				}			
			}
			return false;
		}
		
		
		public function sentMessage($user_sent, $user_get, $text){
			if($user_get != $user_sent){
				$stmt = $this->connection->prepare("INSERT INTO `$this->table_name` (`user_id`,`user_id_get`,`text`,`sent_time`,`hash`) VALUES(?, ?, ?, ?,?)");
				$time = date('Y-m-d H:i:s');
				$hash = $this->generateUniqueHash();
				$stmt->bind_param('iisss',$user_sent, $user_get, $text, $time, $hash);
				if($stmt->execute()){
					include_once 'Message_Queue.php';
					$message_queue = new Message_Queue();
					$queue = $this->sent_from.$user_sent.',';
					$message_queue->priorityQueue($user_get, $queue);
					include_once 'User_Profile_Picture.php';
					$profile = new User_Profile_Picture();
					$profile_pic = $profile->getLatestProfileImageForUser($user_sent);
					include_once 'User_Table.php';
					$user = new User_Table();
					$fullname = $user->getUserFullnameByUserIden($user_sent);
					ob_start();
					include($this->own_dialog_template_path);
					$content = ob_get_clean();
					return $content;
				}
			}
			echo $this->connection->error;
			return false;
		}
		
		
		public function hasNewMessageFromGivenUser($user_id_get, $user_from){
			$stmt = $this->connection->prepare("SELECT `id` FROM `$this->table_name` WHERE `user_id_get`=? AND `user_id`= ?  AND `view` ='n' ");
			if($stmt){
				$stmt->bind_param('ii', $user_id_get, $user_from);
				if($stmt->execute()){
					 $result = $stmt->get_result();
					 if($result !== false){
						return $result->num_rows;
					 }
				}
			}
			echo $this->connection->error;
			return false;
		}
		
		
		public function getTotalMessageNumForUser($user_id){
			$stmt = $this->connection->prepare("SELECT `id` FROM `$this->table_name` WHERE `user_id_get`=?  AND `view` ='n' ");
			if($stmt){
				$stmt->bind_param('i', $user_id);
				if($stmt->execute()){
					 $result = $stmt->get_result();
					 if($result !== false){
						return $result->num_rows;
					 }
				}
			}
			echo $this->connection->error;
			return false;
		}
		
		public function getNewMessageSentUserListForUser($user_id){
			$stmt = $this->connection->prepare("SELECT DISTINCT `user_id` AS `sent_queue` FROM `$this->table_name` WHERE `user_id_get`=?  AND `view` ='n' ");
			if($stmt){
				$stmt->bind_param('i', $user_id);
				if($stmt->execute()){
					 $result = $stmt->get_result();
					 if($result !== false && $result->num_rows >= 1){
 						$rows = $result->fetch_all(MYSQLI_ASSOC);
						$stmt->close();
						return $rows;
					}
				}
			}
			echo $this->connection->error;
			return false;
		}
		
		public function getSentFrom(){
			return $this->sent_from;
		}
		
		
	}		
?>