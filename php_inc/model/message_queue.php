<?php
	include_once MODEL_PATH.'Queue.php';
	
	class Message_Queue extends Queue{
		private  $table_name = "message_queue";
		private $chat_box_template_path = TEMPLATE_PATH_CHILD."chat_box.phtml";
		private $group_chat_box_template_path = TEMPLATE_PATH_CHILD."group_chat_box.phtml";

		private  $message = null;
		private $group_message = null;
		private $group = null;
		
		public function __construct(){
			parent::__construct($this->table_name);
			include_once MODEL_PATH.'Message.php';
			$this->message =  new Message();
			include_once MODEL_PATH.'Group_Message.php';
			$this->group_message =  new Group_Message();
			include_once MODEL_PATH.'Groups.php';
			$this->group =  new Groups();
		}
		
		
		
		public function getMessageQueueBlockForUserId($user_id){
			$this->reArrangeMessageQueueForUser($user_id);
			$m_q = $this->getQueueForUser($user_id);
			return $this->getMessageContactByMessageQueue($m_q);
			
		}
		
		
		public function getMessageContactByMessageQueue($m_q){
			if($m_q !== false){
				$queues = explode(',',trim($m_q,","));
				if(sizeof($queues) > 0){
					$content = "";
					foreach($queues as $queue){
						$segment = explode('-',$queue); //can be either m or gm, stands for message and group message resptively
						if(sizeof($segment) == 2){				
							$sent_from = $segment[0];
							$row_id = $segment[1]; //this can be user_id or a group id
							switch($sent_from){
								case 'm':$content.= $this->message->renderMessageContactOfGivenUser($row_id);break;
								case 'g':$content.= $this->group_message->renderMessageContactOfGivenGroup($row_id);break;
								default:break;
							}
						}
					}
					return $content;
				}
			}
			return false; //no message contact
		}
		
		
		
		public function getNewMessageSentUserListForUser($user_id){
			$user_in_group = $user_id.',';
			$stmt = $this->connection->prepare("
				SELECT * 
				FROM
				(
					SELECT DISTINCT CONCAT('m-',message.user_id,',') AS sent_queue, message.sent_time AS sent_time, 'm' AS 'type'
					FROM message 
					WHERE message.user_id_get = ? AND message.view = 'n' AND message.user_id != ?
					UNION
					SELECT DISTINCT CONCAT('g-', group_message.group_id,',') AS sent_queue,group_message.sent_time AS sent_time, 'g' AS 'type'
					FROM group_message 
					LEFT JOIN groups
					ON group_message.group_id = groups.id WHERE groups.user_in LIKE ? AND  group_message.view_list NOT LIKE ? AND group_message.user_id != ?
				) dum ORDER BY sent_time ASC
			");
			if($stmt){
				$user_in_group = '%'.$user_in_group.'%';
				$stmt->bind_param('iissi' ,$user_id, $user_id, $user_in_group, $user_in_group, $user_id);
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
		
		
		
		public function reArrangeMessageQueueForUser($user_id){
			$sent_users = $this->getNewMessageSentUserListForUser($user_id);
			if($sent_users !== false){
				$current_message_queue = $this->getQueueForUser($user_id);
				foreach($sent_users as $u){
					$this->priorityQueue($user_id, $u['sent_queue']);
				}
			}
		}
		
		
		public function loadMessageChatBoxByKey($user_id, $conversation_with_key){
			include_once 'User_Table.php';
			$user = new User_Table();
			$fullname = $user->getUserFullnameByUserIden($conversation_with_key);
			$user_with = $user->getUserIdByKey($conversation_with_key);
			$user_page_redirect =  USER_PROFILE_ROOT.$user->getUserAccessUrl($user_with);
			$unique_iden = $user->getUniqueIdenForUser($user_with);
			$conversations = $this->message->getMessagesForUser($user_id, $user_with);
			ob_start();
			include($this->chat_box_template_path);
			$content = ob_get_clean();
			return $content;
		}
		
		public function loadGroupMessageChatBoxByKey($group_key){
			include_once 'User_Table.php';
			$user = new User_Table();
			$group_name =  $this->group->getGroupTitleByGroupKey($group_key);
			$conversations = $this->group->getGroupMessagesByKey($group_key);
			ob_start();
			include($this->group_chat_box_template_path);
			$content = ob_get_clean();
			return $content;
		}
		
		
		public function sentMessage($user_sent, $user_get_key, $text){
			include_once 'User_Table.php';
			$user = new User_Table();
			$user_get = $user->getUserIdByKey($user_get_key);
			return $this->message->sentMessage($user_sent, $user_get, $text);
		}
		
		
		
		public function  sentGroupMessage($group_key, $text){
			return $this->group_message->sentMessageForEventGroup($group_key, $text);
		}
		
		
		public function getNewMessageTotalNumForUser($user_id){
			$total = 0;
			$total += $this->message->getTotalMessageNumForUser($user_id);
			$total += $this->group_message->getTotalGroupMessageNumForUser($user_id);
			return $total;
		}
		
		
		public function loadFreshConversationWithGivenUser($user_id, $conversation_with_key){
			include_once 'User_Table.php';
			$user = new User_Table();
			$user_with = $user->getUserIdByKey($conversation_with_key);
			return $this->message->getUnreadMessagesForUser($user_id, $user_with);
		}
		
		public function loadFreshConversationWithGroup($group_key){
			return $this->group_message->getUnreadMessagesForGivenGroup($group_key);
		}
		
		
		public function removeIndividualFromMessageQueueByKey($key){
			include_once 'User_Table.php';
			$user = new User_Table();
			$user_with = $user->getUserIdByKey($key);
			$queue = 'm-'.$user_with.',';
			$this->removeFromQueueForUse($_SESSION['id'], $queue);
		}
		
		
		public function removeGroupFromMessageQueueByKey($group_key){
			include_once 'Groups.php';
			$group = new Groups();
			$group_id = $group->getGroupIdByGroupKey($group_key);
			$queue = 'g-'.$group_id.',';
			$this->removeFromQueueForUse($_SESSION['id'], $queue);
		}
		
		
		
		public function removeGroupFromMessageQueueForUserByGroupId($user_id, $group_id){
			$queue = 'g-'.$group_id.',';
			$this->removeFromQueueForUse($user_id, $queue);
		}
		
		
		
		public function makeIndividualTopAtContactLis($conversation_with_key){
			include_once 'User_Table.php';
			$user = new User_Table();
			$user_id = $user->getUserIdByKey($conversation_with_key);
			if($user_id !== false){
				$user_queue = 'm-'.$user_id.',';
				$queue= $this->getQueueForUser($_SESSION['id']);
				if(stripos($queue,$user_queue ) !== false){
					$queue = str_replace($user_queue, '', $queue);
				}
				$queue = $user_queue.$queue;
				$this->updateQueueForUser($_SESSION['id'], $queue);
				return true;
			}
			return false;
		}
		
		public function makeGroupTopAtContactList($group_key, $user_id = false){
			include_once 'Groups.php';
			$group = new Groups();
			if($user_id === false){
				$user_id = $_SESSION['id'];
			}
			$group_id = $group->isUserInGroup($user_id,$group_key);
			if($group_id !== false){
				$group_queue = 'g-'.$group_id.',';
				$queue= $this->getQueueForUser($user_id);
				if(stripos($queue,$group_queue ) !== false){
					$queue = str_replace($group_queue, '', $queue);
				}
				$queue = $group_queue.$queue;
				$this->updateQueueForUser($user_id, $queue);
				return true;
			}
			return false;
		}
		
		public function makeGroupTopAtContactListByGroupId($group_id, $user_id = false){
			$group_queue = 'g-'.$group_id.',';
			if($user_id === false){
				$user_id = $_SESSION['id'];
			}
			$queue= $this->getQueueForUser($user_id);
			if(stripos($queue,$group_queue ) !== false){
				$queue = str_replace($group_queue, '', $queue);
			}
			$queue = $group_queue.$queue;
			$this->updateQueueForUser($user_id, $queue);
				
		}
		
		
		
		
		
	}		
?>