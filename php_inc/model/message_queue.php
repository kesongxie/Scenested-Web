<?php
	include_once MODEL_PATH.'Queue.php';
	
	class Message_Queue extends Queue{
		private  $table_name = "message_queue";
		private $chat_box_template_path = TEMPLATE_PATH_CHILD."chat_box.phtml";
		private  $message = null;
		//private  $group_message = null;
		
		public function __construct(){
			parent::__construct($this->table_name);
			include_once MODEL_PATH.'Message.php';
			$this->message =  new Message();
		}
		
		public function getMessageQueueBlockForUserId($user_id){

			$this->reArrangeMessageQueueForUser($user_id);
			$m_q = $this->getQueueForUser($user_id);
			if($m_q !== false){
				$queues = explode(',',trim($m_q,","));
				if(sizeof($queues) > 0){
					$content = "";
					foreach($queues as $queue){
						$segment = explode('-',$queue); //can be either m or gm, stands for message and group message resptively
						$sent_from = $segment[0];
						$row_id = $segment[1]; //this can be user_id or a group id
						switch($sent_from){
							case 'm':$content.= $this->message->renderMessageContactOfGivenUser($row_id);break;
						//	case 'mg':$content.= $this->group_message->renderMessageContactOfGivenGroup($row_id);break;
							default:break;
						}
					}
					return $content;
				}else{
					return false; //no message contact
				}
			}
			
		}
		
		
		public function reArrangeMessageQueueForUser($user_id){
			$sent_users = $this->message->getNewMessageSentUserListForUser($user_id);
			if($sent_users !== false){
				foreach ($sent_users as &$user)
    				$user['sent_queue'] = $this->message->getSentFrom().$user['sent_queue'].',';
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
			$conversations = $this->message->getMessagesForUser($user_id, $user_with);
			ob_start();
			include($this->chat_box_template_path);
			$content = ob_get_clean();
			return $content;
		}
		
		public function sentMessage($user_sent, $user_get_key, $text){
			include_once 'User_Table.php';
			$user = new User_Table();
			$user_get = $user->getUserIdByKey($user_get_key);
			return $this->message->sentMessage($user_sent, $user_get, $text);
		}
		
		public function getNewMessageTotalNumForUser($user_id){
			$total = 0;
			$total += $this->message->getTotalMessageNumForUser($user_id);
			return $total;
		}
		
		
		public function loadFreshConversationWithGivenUser($user_id, $conversation_with_key){
			include_once 'User_Table.php';
			$user = new User_Table();
			$user_with = $user->getUserIdByKey($conversation_with_key);
			return $this->message->getUnreadMessagesForUser($user_id, $user_with);
		}
		
		
	}		
?>