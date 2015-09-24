<?php
	include_once 'core_table.php';
	class Group_Message extends Core_Table{
		private  $table_name = "group_message";
		private  $group_contact_block_template_path = TEMPLATE_PATH_CHILD."group_contact_block.phtml";		
		private  $own_dialog_template_path = TEMPLATE_PATH_CHILD.'own_dialog.phtml';
		private  $others_dialog_template_path = TEMPLATE_PATH_CHILD.'others_dialog.phtml';
		private $new_member_template_path = TEMPLATE_PATH_CHILD.'new_member_template_path.phtml';
		private  $sent_from  = 'g-';


		public function __construct(){
			parent::__construct($this->table_name);
		}
		
		public function renderMessageContactOfGivenGroup($group_id){
			include_once 'Groups.php';
			$groups = new Groups();
			if($groups->isGroupExists($group_id)){
				$group_title = $groups->getGroupTitleByGroupId($group_id);
				include_once 'User_Profile_Picture.php';
				$profile = new User_Profile_Picture();
				include_once 'User_Table.php';
				$user = new User_Table();
			
				$latest_message = $this->getLatestMessageWithGivenGroup($group_id);
				$unique_sent_user = $this->getUniqueSentUserByGroupId($group_id);
			
				$group_users = explode(',',$groups->getUserInGroup($group_id));
				$hash = $groups->getColumnById('hash',$group_id);
				if($unique_sent_user !== false){
					if(sizeof($unique_sent_user) == 1 ){
						$sent_user = $unique_sent_user[0]['user_sent'];
						$first_user_profile = $profile->getLatestProfileImageForUser($sent_user);
						foreach($group_users as $u){
							if($sent_user != $u){
								$second_user_profile = $profile->getLatestProfileImageForUser($u);
								break;
							}	
						}
					}else if(sizeof($unique_sent_user) >= 2){
						$first_user_profile = $profile->getLatestProfileImageForUser($unique_sent_user[0]['user_sent']);
						$second_user_profile = $profile->getLatestProfileImageForUser($unique_sent_user[1]['user_sent']);
					}
				}else{
						$first_user_profile = $profile->getLatestProfileImageForUser($group_users[0]);
						$second_user_profile = $profile->getLatestProfileImageForUser($group_users[1]);
				}
			
				$new_message_num = $this->getTotalMessageNumForGroup($group_id);
				$hasMessage = false;
				if($latest_message !== false){
					$time = convertDateTimeToAgo($latest_message['sent_time'], false,true, true,true);
					if($latest_message['message_type'] == 'n'){
						$fullname = $user->getUserFullnameByUserIden($latest_message['user_sent']);
						$text = $fullname.' now is in the event';
					}else{
						$text = $latest_message['text'];
						if($latest_message['user_sent'] == $_SESSION['id']){
							$text = 'Me: '.$text;
						}else{
							$text =  $user->getUserFirstnameByUserIden($latest_message['user_sent']).': '.$text;
						}
					}
					$hasMessage = true;
				}
			
				ob_start();
				include($this->group_contact_block_template_path);
				$content = ob_get_clean();
				return $content;
			}
			return false;
		}
		
	
		public function getLatestMessageWithGivenGroup($group_id){
			$join_time = $this->getUserJoinTime($_SESSION['id'], $group_id);
			$stmt = $this->connection->prepare("SELECT `user_id` AS `user_sent`,`text`,`sent_time`,`message_type` FROM `$this->table_name` WHERE  `group_id`=? AND `sent_time` >= ? ORDER BY `id` DESC LIMIT 1    ");
			if($stmt){
				$stmt->bind_param('is', $group_id, $join_time);
				if($stmt->execute()){
					 $result = $stmt->get_result();
					 if($result !== false && $result->num_rows == 1){
						$row = $result->fetch_assoc();
						$stmt->close();
						if($row['message_type'] == 'n' && $row['user_sent'] == $_SESSION['id']){
							return false;
						}
						return $row;
					 }
				}
			}
			return false;
		}
		
		
		public function getUserJoinTime($user_id, $group_id){
			$stmt = $this->connection->prepare("SELECT `sent_time` FROM `$this->table_name` WHERE  `group_id`=? AND `user_id` = ? AND `message_type` = 'n' ORDER BY `id` DESC LIMIT 1    ");
			if($stmt){
				$stmt->bind_param('ii', $group_id, $user_id);
				if($stmt->execute()){
					 $result = $stmt->get_result();
					 if($result !== false && $result->num_rows == 1){
						$row = $result->fetch_assoc();
						$stmt->close();
						return $row['sent_time'];
					 }
				}
			}
			return false;
		}
		
		
		
	
		public function getTotalMessageNumForGroup($group_id){
			$join_time = $this->getUserJoinTime($_SESSION['id'], $group_id);
			$user_in_group = $_SESSION['id'].',';
			$stmt = $this->connection->prepare("SELECT `id` FROM `$this->table_name` WHERE group_id = ? AND `user_id` != ? AND `view_list` NOT LIKE ? AND `sent_time` >= ?");
			if($stmt){
				$user_in_group = '%'.$user_in_group.'%';
				$stmt->bind_param('iiss' ,$group_id, $_SESSION['id'], $user_in_group, $join_time);
				if($stmt->execute()){
					 $result = $stmt->get_result();
					 if($result !== false){
 						$stmt->close();
 						return $result->num_rows;
					}
				}
			}
			echo $this->connection->error;
			return false;
		}
		
		
		public function getTotalGroupMessageNumForUser($user_id){
			$user_in_group = $user_id.',';
			$stmt = $this->connection->prepare("
			SELECT DISTINCT groups.id
			FROM groups 
			LEFT JOIN group_message
			ON groups.id = group_message.group_id WHERE groups.user_in LIKE ? AND group_message.user_id != ? AND  group_message.view_list NOT LIKE ?");
			if($stmt){
				$user_in_group = '%'.$user_in_group.'%';
				$stmt->bind_param('sis' ,$user_in_group, $user_id, $user_in_group);
				if($stmt->execute()){
					 $result = $stmt->get_result();
					 if($result !== false && $result->num_rows >=1){
 						$stmt->close();
 						$group_ids = $result->fetch_all(MYSQLI_ASSOC);
 						$count = 0;
 						foreach($group_ids as $id){
 							 $count += $this->getTotalMessageNumForGroup($id['id']);
 						}
 						return $count;	
					}
				}
			}
			echo $this->connection->error;
			return false;
		}
		
		
		
	
		public function getGroupMessagesForUser($group_id){
			$join_time = $this->getUserJoinTime($_SESSION['id'], $group_id);
			$stmt = $this->connection->prepare("SELECT `user_id`,`text`,`sent_time`,`message_type` FROM `$this->table_name` WHERE `group_id` = ?  AND `sent_time` >= ? ");
			if($stmt){
				$stmt->bind_param('is',$group_id, $join_time);
				if($stmt->execute()){
					 $result = $stmt->get_result();
					 if($result !== false && $result->num_rows >= 1){
					 	$this->updateMesasgeToSeenFromGivenUser($group_id);
 						$rows = $result->fetch_all(MYSQLI_ASSOC);
						$stmt->close();
						$content = "";
						foreach($rows as $row){
							include_once 'User_Profile_Picture.php';
							$profile = new User_Profile_Picture();
							$profile_pic = $profile->getLatestProfileImageForUser($row['user_id']);
							include_once 'User_Table.php';
							$user = new User_Table();
							$text = $row['text'];
							ob_start();
							
							$user_page_redirect =  USER_PROFILE_ROOT.$user->getUserAccessUrl($row['user_id']);
							$unique_iden = $user->getUniqueIdenForUser($row['user_id']);
							
							if($row['message_type'] == 'n'){
								if($row['user_id'] != $_SESSION['id']){
									$fullname = $user->getUserFullnameByUserIden($row['user_id']);
									$time = convertDateTimeToAgo($row['sent_time'], true);
									include($this->new_member_template_path);
								}
							}else if($row['message_type'] == null){
								if($row['user_id'] == $_SESSION['id']){
									//render my own dialog
									include($this->own_dialog_template_path);
								}else{
									//render other person's dialog
									include($this->others_dialog_template_path);
								}
							}
							$content .= ob_get_clean();
						}
						return $content;
					 }
				}
			}
			return false;
		}
		
		
		
	
		
		
		
		
		public function updateMesasgeToSeenFromGivenUser($group_id){
			$user_id_in = $this->connection->escape_string($_SESSION['id'].',');
			$stmt = $this->connection->prepare("UPDATE `$this->table_name` SET view_list=CONCAT(view_list, '$user_id_in') WHERE group_id = ? AND  view_list NOT LIKE ?  ");
			if($stmt){
				$user_id_in = '%'.$user_id_in.'%';
				$stmt->bind_param('is', $group_id, $user_id_in);
				if($stmt->execute()){
					$stmt->close();
					return true;
				}			
			}
			return false;
		}
		
		
	
		public function getSentFrom(){
			return $this->sent_from;
		}
		
		
		public function getUniqueSentUserByGroupId($group_id){
			$stmt = $this->connection->prepare("SELECT DISTINCT `user_id` AS `user_sent` FROM `$this->table_name` WHERE  `group_id`=?   ORDER BY `id` DESC LIMIT 2");
			if($stmt){
				$stmt->bind_param('i', $group_id);
				if($stmt->execute()){
					 $result = $stmt->get_result();
					 if($result !== false && $result->num_rows >= 1){
						$rows = $result->fetch_all(MYSQLI_ASSOC);
						$stmt->close();
						return $rows;
					 }
				}
			}
			return false;
		}
		
		public function sentMessageForEventGroup($group_key, $text){	
			include_once 'Groups.php';
			$groups = new Groups();
			$group_resource = $groups->getGroupResourceByKey($group_key);
			if($group_resource !== false){
				$stmt = $this->connection->prepare("INSERT INTO `$this->table_name` (`user_id`,`group_id`,`text`,`sent_time`,`hash`) VALUES(?, ?, ?, ?,?)");
				$time = date('Y-m-d H:i:s');
				$hash = $this->generateUniqueHash();
				$stmt->bind_param('iisss',$_SESSION['id'], $group_resource['id'], $text, $time, $hash);
				if($stmt->execute()){
					include_once 'Message_Queue.php';
					$message_queue = new Message_Queue();
					$queue = $this->sent_from.$group_resource['id'].',';
					
					$user_in_group = explode(',',trim($group_resource['user_in'],','));
					foreach($user_in_group as $u){
						$message_queue->priorityQueue($u, $queue);
					}
					
					
					include_once 'User_Profile_Picture.php';
					$profile = new User_Profile_Picture();
					$profile_pic = $profile->getLatestProfileImageForUser($_SESSION['id']);
					ob_start();
					include($this->own_dialog_template_path);
					$content = ob_get_clean();
					return $content;
				}
			}
			echo $this->connection->error;
			return false;
		}
		
		
		public function sentNewMemberMessageForEventGroup($new_member, $group_key = false, $group_id = false){	
			include_once 'Groups.php';
			$groups = new Groups();
			
			if($group_key !== false){
				$group_resource = $groups->getGroupResourceByKey($group_key);
				if($group_resource !== false){
					$group_id =  $group_resource['id'];
					$user_in = $group_resource['user_in'];
				}else{
					return false;
				}
			}else{
				//get by group_id
				$user_in = $groups->getUserInGroup($group_id);
				if($user_in === false){
					return false;
				}
			}
			$stmt = $this->connection->prepare("INSERT INTO `$this->table_name` (`user_id`,`group_id`,`sent_time`,`message_type`, `hash`) VALUES(?, ?, ?, ?,  ?)");
			$time = date('Y-m-d H:i:s');
			$hash = $this->generateUniqueHash();
			$type='n';
			$stmt->bind_param('iisss',$new_member, $group_id, $time,$type, $hash);
			if($stmt->execute()){
				include_once 'Message_Queue.php';
				$message_queue = new Message_Queue();
				$queue = $this->sent_from.$group_id.',';
				
				$user_in_group = explode(',',trim($user_in,','));
				foreach($user_in_group as $u){
					$message_queue->priorityQueue($u, $queue);
				}
			}
			echo $this->connection->error;
			return false;
		}
		
		
		
		
		
		public function getUnreadMessagesForGivenGroup($group_key){
			$user_in_group = '%'.$_SESSION['id'].',%';
			include_once 'Groups.php';
			$groups = new Groups();
			$group_id = $groups->getGroupIdByGroupKey($group_key);
			if($group_id !== false){
				$stmt = $this->connection->prepare("SELECT `user_id`,`text`,`sent_time` FROM `$this->table_name` WHERE `group_id` = ? AND `user_id` != ? AND `view_list` NOT LIKE ? ");
				if($stmt){
					$stmt->bind_param('iis',$group_id, $_SESSION['id'], $user_in_group);
					if($stmt->execute()){
						 $result = $stmt->get_result();
						 if($result !== false && $result->num_rows >= 1){
							$this->updateMesasgeToSeenFromGivenUser($group_id);
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
								if($row['user_id'] == $_SESSION['id']){
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
			}
			return false;
		}
		
		public function deleteAllMessageForGroup($group_id){
			$this->deleteRowBySelector('group_id',$group_id);
		}
		
		
		
	}		
?>