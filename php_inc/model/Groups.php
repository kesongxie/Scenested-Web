<?php
	include_once 'core_table.php';

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
			return $g_m->getGroupMessagesForUser($group_id);
		}
		
		
		public function getUserInGroup($group_id){
			return $this->getColumnById('user_in', $group_id);
		}
		
		
		public function getGroupTitleByGroupKey($group_key){
			$row = $this->getMultipleColumnsBySelector(array('id','type'), 'hash', $group_key);
			if($row !== false){
				$type = $row['type'];
				switch($type){
				case 'e': 
					$group_name = $this->getGroupMemberTitleByGroupId($row['id']);
					return $group_name;
				default:break;
				}
			}
			return false;
		}
		
		
		public function getGroupTitleByGroupId($group_id){
			$type = $this->getColumnById('type', $group_id);
			if($type !== false){
				switch($type){
				case 'e': 
					$group_name = $this->getEventGroupTitleByGroupId($group_id);
					$group_name = ($group_name !== false)?$group_name:$this->getGroupMemberTitleByGroupId($group_id);
					return $group_name;
				default:break;
				}
			}
			return false;
		}
		
		
		public function getGroupMemberTitleByGroupId($group_id){
			$user_in = $this->getColumnById('user_in',$group_id);
			if($user_in !== false){
				$title = "";
				$user_array = explode(',',trim($user_in,','));
				$name_array = array();
				include_once 'User_Table.php';
				$user = new User_Table();
				$count = 0;
				$names = "";
				foreach($user_array as $u){
					$name = $user->getUserFirstNameByUserIden($u).', ';
					if($count != 2){
						$title .= $name;
					}
					$names.=$name;
					$count++;
				}
				$title =  trim($title,', ');
				$names =  trim($names,', ');
				$name_array['members'] = $names;
				if( sizeof($user_array) > 2 ){
					$remaining_people = sizeof($user_array) - 2;
					$title .= ' and '.$remaining_people;
					if($remaining_people == 1){
						$title .= ' Person';
					}else{
						$title .= ' People';
					}
				}
				
				$name_array['title'] = $title;
				return $name_array;
				
			}
			return false;
			
		}
		
		
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
		
		
		
		public function getGroupResourceByKey($group_key){
			return $this->getMultipleColumnsBySelector(array('id','user_in'),'hash',$group_key);
		}

		
		public function searchContactEventGroupByKeyWord($key_word){
			$stmt = $this->connection->prepare("
			SELECT CONCAT('g-',groups.id) AS queue
			FROM groups 
			LEFT JOIN event_group
			ON groups.id = event_group.group_id 
			LEFT JOIN event
			ON event_group.event_id = event.id
			WHERE  groups.user_in LIKE ? AND (event.title LIKE ? || event.description LIKE ? || event.location LIKE ?)
			");			
			if($stmt){
				$user_in = '%'.$_SESSION['id'].',%';
				$key_word = '%' .$key_word. '%';
				$stmt->bind_param('ssss',$user_in,$key_word,$key_word,$key_word);
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
		
		
		public function getEventGroupJoinedMemberByGroupKey($group_key){
			$group_id = $this->getGroupIdByGroupKey($group_key);
			if($group_id !== false){
				include_once 'Event_Group.php';
				$e_g = new Event_Group();
				$chat_members = $e_g->getEventGroupJoinedMemberByGroupId($group_id);
				ob_start();
				include(TEMPLATE_PATH_CHILD.'group_chat_member.phtml');
				$content = ob_get_clean();
				return $content;
			}
			return false;
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
		
	}
?>