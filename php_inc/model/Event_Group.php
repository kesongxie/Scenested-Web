<?php
	include_once MODEL_PATH.'core_table.php';
	
	class Event_Group extends Core_Table{
		private  $table_name = "event_group";
	
		public function __construct(){
			parent::__construct($this->table_name);
		}
		
		public function getEventIdByGroupId($group_id){
			return $this->getColumnBySelector('event_id','group_id',$group_id);
		}
		
		public function getGroupIdByEventId($event_id){
			return $this->getColumnBySelector('group_id','event_id',$event_id);
		}
		
		
		public function isEventGroupForEventExist($event_id){
			return $this->isNumericValueExistingForColumn($event_id, 'event_id');
		}
		
		public function joinEventForUser($user_id, $event_id, $post_user){
			include_once 'Event.php';
			$e = new Event();
			if($user_id != $post_user){
				if(!$this->isEventGroupForEventExist($event_id)){
					//insert new group
					$user_in = $user_id.','.$post_user.',';
					include_once 'Groups.php';
					$g  = new Groups();
					$result = $g->addEventGroup($user_in);	 //create group
					if($result !== false){
						$group_id = $result['group_id'];
						$group_key = $result['hash'];
						if($group_id !== false){
							if($user_in !== false){
								$stmt = $this->connection->prepare("INSERT INTO `$this->table_name` (`event_id`,`group_id`) VALUES(?, ?)");
								$stmt->bind_param('ii',$event_id, $group_id);
								if($stmt->execute()){
									$stmt->close();
									include_once 'Message_Queue.php';
									$queue = new Message_Queue();
									$queue->makeGroupTopAtContactList($group_key, $user_id);
									include_once 'Group_Message.php';
									$g_m = new Group_Message();
									$g_m->sentNewMemberMessageForEventGroup($user_id, $group_key, false);
									return true;
								}
							}
						}
					}
				}else{
					//update group member
					$group_id = $this->getGroupIdByEventId($event_id);
					if($group_id !== false){
						include_once 'Groups.php';
						$g  = new Groups();
						include_once 'Message_Queue.php';
						$queue = new Message_Queue();
						$user_in = $g->getUserInGroup($group_id);
						if(stripos($user_in, $_SESSION['id'].',') === false){
							$user_in = $_SESSION['id'].','.$user_in;
							$g->updateUserInForGroupId($user_in,$group_id);	
						}else{
							return false;
						}
						$queue->makeGroupTopAtContactListByGroupId($group_id, $user_id);
						include_once 'Group_Message.php';
						$g_m = new Group_Message();
						$g_m->sentNewMemberMessageForEventGroup($user_id,false, $group_id);
						return true;
					}
					
				}
			}
			echo $this->connection->error;
			return false;
		}
		
		public function unjoinEventForUser($user_id,$event_id, $post_user){
			$group_id = $this->getGroupIdByEventId($event_id);
			if($group_id !== false){
				if($user_id != $post_user){
					include_once 'Message_Queue.php';
					$queue = new Message_Queue();
					include_once 'Groups.php';
					$g  = new Groups();
					$queue->removeGroupFromMessageQueueForUserByGroupId($user_id, $group_id);
					$g->removeUserFromGroup($user_id, $group_id);
					$user_in = trim($g->getUserInGroup($group_id),',');
					$remaining_user = explode(',',$user_in);
					if(sizeof($remaining_user) == 1){
						//delete group
						$this->deleteEventGroupByEventId($event_id);
						$queue->removeGroupFromMessageQueueForUserByGroupId($user_in, $group_id);
						$g->deleteGroupByGroupId($group_id);
						//delete all message by group_id
						include_once 'Group_Message.php';
						$g_m = new Group_Message();
						$g_m->deleteAllMessageForGroup($group_id);
					}
					
				}
			}
		}
		
		public function deleteEventGroupByEventId($event_id){
			$this->deleteRowBySelector('event_id', $event_id);
		}
		
		
		public function getEventGroupJoinedMemberByGroupId($group_id){
			$event_id = $this->getEventIdByGroupId($group_id);
			if($event_id !== false){
				include_once 'Event.php';
				$e = new Event();
				return $e->getJoinedMemberByEventId($event_id, $group_id);
			}
			return false;
		}
		
		public function getEventGroupEventInfoByGroupId($group_id){
			$event_id = $this->getEventIdByGroupId($group_id);
			if($event_id !== false){
				include_once 'Event.php';
				$e = new Event();
				return $e->getEventInforByEventId($event_id, $group_id);
			}
			return false;
		}
		
		public function getEventTextInfoByGroupId($group_id){
			$event_id = $this->getEventIdByGroupId($group_id);
			if($event_id !== false){
				include_once 'Event.php';
				$e = new Event();
				return $e->getEventTextResource($event_id);
			}
		}
		
		
	}		
	
?>