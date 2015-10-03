<?php
	include_once MODEL_PATH.'core_table.php';
	class User_Notification_Queue extends Core_Table{
		private $table_name = "user_notification_queue";
		private $send_from = null;
	
		public function __construct($send_from){
			parent::__construct($this->table_name);
			$this->send_from = $send_from;
		}
		
		public function getNotificationQueueForUser($user_id){
			return $this->getColumnByUserId('queue',$user_id);
		}
		
		public function getNotificationReadQueueForUser($user_id){
			return $this->getColumnByUserId('read_queue',$user_id);
		}
		
		
		/*this function doesn't use the property send from, instead, custom send from code is provided */
		public function addNotificationQueueForUserWithCustomSendFrom($user_id, $row_id, $send_from){
			$queue_row = $this->queueRowForUserExist($user_id);
			if($queue_row !== false){
				//update
				$id = $queue_row['id'];
				$queue = $queue_row['queue'];
				$queue = $send_from.$row_id.','.$queue;
				$this->updateQueue($queue, $id);
			}else{
				//create new row for user
				$stmt = $this->connection->prepare("INSERT INTO `$this->table_name` (`user_id`,`queue`) VALUES(?,?)");
				$queue = $send_from.$row_id.',';
				$stmt->bind_param('is',$user_id,$queue);
				if($stmt->execute()){
					$stmt->close();
					return true;
				}
				return false;
			}
		}
		
		
		
		public function addNotificationQueueForUser($user_id, $row_id){
			$queue_row = $this->queueRowForUserExist($user_id);
			if($queue_row !== false){
				//update
				$id = $queue_row['id'];
				$queue = $queue_row['queue'];
				$queue = $this->send_from.$row_id.','.$queue;
				$this->updateQueue($queue, $id);
			}else{
				//create new row for user
				$stmt = $this->connection->prepare("INSERT INTO `$this->table_name` (`user_id`,`queue`) VALUES(?,?)");
				$queue = $this->send_from.$row_id.',';
				$stmt->bind_param('is',$user_id,$queue);
				if($stmt->execute()){
					$stmt->close();
					return true;
				}
				return false;
			}
		}
		
		public function queueRowForUserExist($user_id){
			//if exists, return the current queue, false otherwise
			$column_array = array('id','queue','read_queue');
			$queue = $this->getMultipleColumnsBySelector($column_array, 'user_id', $user_id);
			return $queue;
		}
		
		/* this would remove a queue text from both queue and read queue columns,
		this function is mainly used by the sender delete a post, comment and want to stop notificating other users.
		$queue_text format is code + row_id	
		*/
		public function removeQueueForUser($user_id, $row_id){
			$queue_row = $this->queueRowForUserExist($user_id);
			if($queue_row !== false){
				$queue = $queue_row['queue'];
				$read_queue = $queue_row['read_queue'];
				$id = $queue_row['id'];
				$queue_text = $this->send_from.$row_id.',';
				if(stripos($queue, $queue_text) !== false){
					$queue = str_replace($queue_text,'',$queue);
					$this->updateQueue($queue, $id);
				}
				
				if(stripos($read_queue, $queue_text) !== false){
					$read_queue = str_replace($queue_text,'',$read_queue);
					$this->updateReadQueue($read_queue, $id);
				}
			}
		}
		
		public function updateQueue($queue, $row_id){
			$this->setColumnById('queue', $queue, $row_id);
		}
		public function updateReadQueue($read_queue, $row_id){
			$this->setColumnById('read_queue', $read_queue, $row_id);
		}
		
		
		public function updateQueueToReadQueue($user_id){
			$queue = $this->getNotificationQueueForUser($user_id);
			$old_read_queue = $this->getNotificationReadQueueForUser($user_id);
			$new_read_queue = $queue.$old_read_queue;
			$this->setColumnByUserId('read_queue', $new_read_queue, $user_id);
			$this->setColumnByUserId('queue', '', $user_id);
		}
		
		
	}
?>