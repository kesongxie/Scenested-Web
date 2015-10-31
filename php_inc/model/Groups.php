<?php
	include_once MODEL_PATH.'Core_Table.php';

	class Groups extends Core_Table{
		private $table_name = "groups";
		public function __construct(){
			parent::__construct($this->table_name);
		}
		
		public function addEventGroup($user_in = false){
			$hash = $this->generateUniqueHash();
			if($user_in !== false){
				$stmt = $this->connection->prepare("INSERT INTO `$this->table_name` (`user_in`,`created_time`,`type`, `hash`) VALUES(?, ?, ?, ?)");
				$time = date('Y-m-d H:i:s');
				$type = 'e';
				if($stmt){
					$stmt->bind_param('ssss',$user_in, $time, $type, $hash);
					if($stmt->execute()){
						$stmt->close();
						$group_id =  $this->connection->insert_id;
						return array('group_id'=>$group_id,'hash'=>$hash);
					}
				}
			}
			return false;
		}
		
		
		public function getGroupMessagesByKey($group_key){
			$group_id = $this->getGroupIdByGroupKey($group_key);
			include_once 'Group_Message.php';
			$g_m = new Group_Message();
			$conversation =  $g_m->getGroupMessagesForUser($group_id);
			return $conversation;
			
		}
		
		
		public function getUserInGroup($group_id){
			return $this->getColumnById('user_in', $group_id);
		}
		
		
		public function getGroupTitleByGroupKey($group_key){
			$row = $this->getMultipleColumnsBySelector(array('id','type', 'group_name'), 'hash', $group_key);
			if($row !== false){
				$type = $row['type'];
				$group_name = '';
				switch($type){
					case 'e': 
						$group_name = $row['group_name'];
						if($group_name === null){
							$group_name = $this->getEventGroupTitleByGroupId($row['id']);
						}
						break;
					case 'r':
						$group_name = $row['group_name'];
						break;
					default:break;
				}
				return $group_name;
			}
			return false;
		}
		
		
		public function getGroupTitleByGroupId($group_id){
			$row = $this->getMultipleColumnsById(array('type', 'group_name'), $group_id);
			if($row !== false){
				$type = $row['type'];
				$group_name = '';
				switch($type){
					case 'e': 
						$group_name = $row['group_name'];
						if($group_name === null){
							$group_name = $this->getEventGroupTitleByGroupId($group_id);
						}
						break;
					case 'r':
						$group_name = $row['group_name'];
						break;
					default:break;
				}
				return $group_name;
				
			}
			return false;
		}
		
		
		// public function getGroupMemberTitleByGroupId($group_id){
// 			$user_in = $this->getColumnById('user_in',$group_id);
// 			if($user_in !== false){
// 				$title = "";
// 				$user_array = explode(',',trim($user_in,','));
// 				$name_array = array();
// 				include_once 'User_Table.php';
// 				$user = new User_Table();
// 				$count = 0;
// 				$names = "";
// 				foreach($user_array as $u){
// 					if(++$count < 3){
// 						$name = $user->getUserFirstNameByUserIden($u).', ';
// 						$title .= $name;
// 					}else{
// 						break;
// 					}
// 				}
// 				$title =  trim($title,', ');
// 				if( sizeof($user_array) > 2 ){
// 					$remaining_people = sizeof($user_array) - 2;
// 					$title .= ' and '.$remaining_people;
// 					if($remaining_people == 1){
// 						$title .= ' Person';
// 					}else{
// 						$title .= ' People';
// 					}
// 				}
// 				return $title;
// 			}
// 			return false;
// 			
// 		}
// 		
		
		public function getEventGroupTitleByGroupId($group_id){
			include_once 'Event_Group.php';
			$e_g = new Event_Group();
			$event_id = $e_g->getEventIdByGroupId($group_id);
			if($event_id !== false){
				include_once 'Event.php';
				$e = new Event();
				return $e->getEventTitleByEventId($event_id);
			}
			return false;
		}
		
		public function getGroupIdByGroupKey($group_key){
			return $this->getRowIdByHashkey($group_key);
		}
		
		public function getGroupKeyByGroupId($group_id){
			return $this->getColumnById('hash',$group_id);
		}
		
		
		public function getGroupMemberIdsPlainListByGroupId($group_id){
			return $this->getColumnById('user_in', $group_id );
		}
		
		
		
		
		public function getGroupResourceByKey($group_key){
			return $this->getMultipleColumnsBySelector(array('id','user_in'),'hash',$group_key);
		}

		public function searchContactGroupByKeyWord($key_word){
			$stmt = $this->connection->prepare("
			SELECT CONCAT('g-',groups.id) AS queue
			FROM groups 
			LEFT JOIN event_group
			ON groups.id = event_group.group_id 
			LEFT JOIN event
			ON event_group.event_id = event.id
			WHERE  groups.user_in LIKE ? AND (event.title LIKE ? || event.description LIKE ? || event.location LIKE ?)
			
			UNION 
			
			SELECT CONCAT('g-',groups.id) AS queue
			FROM groups 
			WHERE groups.user_in LIKE ?  AND  groups.group_name LIKE ?
			
			");			
			if($stmt){
				$user_in = '%'.$_SESSION['id'].',%';
				$key_word = '%' .$key_word. '%';
				$stmt->bind_param('ssssss',$user_in,$key_word,$key_word,$key_word,$user_in,  $key_word);
				if($stmt->execute()){
					 $result = $stmt->get_result();
					 if($result !== false && $result->num_rows >= 1){
						$row = $result->fetch_all(MYSQLI_ASSOC);
						$stmt->close();
						return $row;
					 }
				}
			}
			echo $this->connection->error;
			return false;
		}
		
		public function isUserInGroup($user_id, $group_key){
			$group_resource = $this->getGroupResourceByKey($group_key);
			if($group_resource !== false){
				if(stripos($group_resource['user_in'],$user_id.',') !== false){
					return $group_resource['id'];
				}
			}
			return false;
		}
		
		public function isUserInGroupByGroupId($user_id, $group_id){
			$user_in = $this->getUserInGroup($group_id);
			if(stripos($user_in,$user_id.',') !== false){
				return true;
			}
			return false;
		}
		
		
		public function removeUserFromGroup($user_id, $group_id){
			if($this->isUserInGroupByGroupId($user_id, $group_id)){
				$user_in = $this->getUserInGroup($group_id);
				$new_user_in = str_replace($user_id.',', '', $user_in);
				$this->updateUserInForGroupId($new_user_in, $group_id);
			}
		}
		
		public function updateUserInForGroupId($new_user_in, $group_id){
			$this->setColumnById('user_in', $new_user_in, $group_id);
		}
		
		
		
		
		public function deleteGroupByGroupId($group_id){
			$this->deleteRowById($group_id);
		}
		
		
		public function isGroupExists($group_id){
			$user_in = $this->getUserInGroup($group_id);
			if($user_in !== false){
				return true;
			}
			return false;
		}
		
		
		public function getGroupJoinedMemberByGroupKey($group_key){
			$row = $this->getMultipleColumnsBySelector(array('id','type', 'group_name'), 'hash', $group_key);
			if($row !== false){
				if($row['type'] == 'e' && $row['group_name'] === NULL){
					include_once 'Event_Group.php';
					$e_g = new Event_Group();
					$chat_members = $e_g->getEventGroupJoinedMemberByGroupId($row['id']);
				}else{
					$chat_members = $this->getChatMembersByGroupId($row['id']);
				}
				ob_start();
				include(TEMPLATE_PATH_CHILD.'group_chat_member.phtml');
				$content = ob_get_clean();
				return $content;
			}
			
		}
		
		public function getEventGroupEventInfoByGroupKey($group_key){
			$group_id = $this->getGroupIdByGroupKey($group_key);
			if($group_id !== false){
				include_once 'Event_Group.php';
				$e_g = new Event_Group();
				return $e_g->getEventGroupEventInfoByGroupId($group_id);
			}
			return false;
		}
		
		public function getChatMembersByGroupId($group_id){
			include_once 'User_Table.php';
			include_once 'Event_Group.php';
			$user = new User_Table();
			$user_in = $this->getUserInGroup($group_id); //22,28,29,
			if($user_in !== false){
				$users = explode(',',trim($user_in, ','));
				$content = '';
				foreach($users as $u){
					$profile_pic = $user->getLatestProfilePictureForuser($u);
					$firstname = $user->getUserFirstNameByUserIden($u);
					$user_page_redirect =  USER_PROFILE_ROOT.$user->getUserAccessUrl($u);
					$unique_iden = $user->getUniqueIdenForUser($u);
					$hash = $user->getUniqueIdenForUser($u);
					ob_start();
					include(TEMPLATE_PATH_CHILD.'list_item.phtml');
					$content .= ob_get_clean();
				}
				return $content;
			}
			return false;
		}
		
		public function updateGroupNameByGroupId($new_name, $group_id){
			$this->setColumnById('group_name', $new_name, $group_id);
		}
		
		//get all the unique users that in the parameter user's event
		public function getUserArrayWithEventConnectionToUser($user_id){
				$user_like = '%'.$user_id.',%';
				$stmt = $this->connection->prepare("SELECT `user_in` FROM `$this->table_name` WHERE `user_in` like ? AND `type`= 'e' AND `group_name` IS NULL");
				if($stmt){
					$stmt->bind_param('s',$user_like);
					if($stmt->execute()){
						 $result = $stmt->get_result();
						 if($result !== false && $result->num_rows >= 1){
							$rows = $result->fetch_all(MYSQLI_ASSOC);
							$stmt->close();
							$result_array = array();
							foreach($rows as $row){
								$temp_array = explode(',',trim($row['user_in'], ','));
								$result_array = array_merge($result_array, $temp_array);		
							}
							return array_diff(array_unique($result_array), array($user_id));
						 }
					}
				}
				return false;
		}
		/*get all the joined or added event for user*/
		public function getEventArrayForUser($user_id){
			$user_like = '%'.$user_id.',%';
				$stmt = $this->connection->prepare("
				SELECT event_group.event_id
				FROM groups
				LEFT JOIN event_group
				ON groups.id = event_group.group_id
				WHERE `user_in` like ? AND groups.type= 'e' AND groups.group_name IS NULL
				");
				if($stmt){
					$stmt->bind_param('s',$user_like);
					if($stmt->execute()){
						 $result = $stmt->get_result();
						 if($result !== false && $result->num_rows >= 1){
							$rows = $result->fetch_all(MYSQLI_ASSOC);
							$stmt->close();
							$result_array = array();
							foreach($rows as $row){
								$result_array = array_merge($result_array, array($row['event_id']));		
							}
							return $result_array;
						 }
					}
				}
				echo $this->connection->error;
				return false;
		}
		
		
		
		
	}
?>