<?php
	include_once MODEL_PATH.'core_table.php';
	include_once MODEL_PATH.'Noti_Sendable.php';
	
	class Reply_Notify_Post_User extends Noti_Sendable{
		private  $table_name = "reply_notify_post_user";
		public function __construct(){
			parent::__construct($this->table_name);
		}
		
		public function addReplyNotiForUser($user_id_get, $reply_id, $sent_time,$hash){
			$stmt = $this->connection->prepare("INSERT INTO `$this->table_name` (`user_id_get`,`reply_id`,`sent_time`,`hash`) VALUES(?, ?, ?, ?)");
			$stmt->bind_param('ssis',$user_id_get, $reply_id, $sent_time, $hash);
			if($stmt->execute()){
				$stmt->close();
				$noti_id = $this->connection->insert_id;
				$this->noti_queue->addNotificationQueueForUser($user_id_get, $noti_id);
			}
			return false;
		}
		
		public function deleteNotiQueue($key){
			$row_id = $this->deleteNotiQueueForKey($key); //remove from the noti queue
			$this->deleteRowById($row_id); //delete the row
		}
		
		
		public function renderNotifyPostUserReplyForNotificationBlock($row_id){
			$reply_id = $this->getColumnById('reply_id',$row_id);
			include_once 'Reply.php';
			$reply = new Reply();
			if($reply_id !== false){
				return $reply->renderNotifyPostUserReplyForNotificationBlock($reply_id);
			}
			return false;
		}
		
		
	}
?>