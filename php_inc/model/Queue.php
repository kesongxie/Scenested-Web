<?php
	include_once MODEL_PATH.'core_table.php';
	include_once MODEL_PATH.'Code_For_Notification_Sendable_Table.php';
	
	class Queue extends Core_Table{
		private $table_name = null;
		private $sent_from = null;
		public function __construct($table_name){
			parent::__construct($table_name);
			$this->table_name = $table_name;
			$this->sent_from = Code_For_Notification_Sendable_Table::getCodeForTable($table_name);
		}
		
		public function getQueueForUser($user_id){
			return $this->getColumnByUserId('queue',$user_id);
		}
		
		
		public function priorityQueue($user_id, $queue){
			// m-29, 
			$existed_queue = $this->getQueueForUser($user_id); //m-28,m-29,m-30,m-31,
			if($existed_queue !== false){
				if(stripos($existed_queue, $queue) !== false){
					$new_queue =  $queue.str_replace($queue, '',$existed_queue);
				}else{
					$new_queue =  $queue.$existed_queue;
				}
				$this->updateQueueForUser($user_id, $new_queue);
			}else{
				$this->addQueueForUserWithQueue($user_id, $queue);
			}
		}
		
		
		public function addQueueForUserWithQueue($user_id, $queue){
			$stmt = $this->connection->prepare("INSERT INTO `$this->table_name` (`user_id`,`queue`) VALUES(?,?)");
			if($stmt){
				$stmt->bind_param('is',$user_id,$queue);
				if($stmt->execute()){
					$stmt->close();
					return true;
				}
			}
			return false;
		}
		
		
		public function addQueueForUser($user_id, $row_id){
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
		
		
		public function removeFromQueueForUse($user_id, $queue){
			$old_queue = $this->getQueueForUser($user_id);
			$new_queue = str_replace($queue, '', $old_queue);
			$this->updateQueueForUser($user_id, $new_queue);
		}
		
		
		public function queueRowForUserExist($user_id){
			//if exists, return the current queue, false otherwise
			$column_array = array('id','queue');
			$queue = $this->getMultipleColumnsBySelector($column_array, 'user_id', $user_id);
			return $queue;
		}
		
		public function updateQueue($queue, $row_id){
			$this->setColumnById('queue', $queue, $row_id);
		}
		
		public function updateQueueForUser($user_id, $queue){
			$this->setColumnByUserId('queue', $queue, $user_id);
		}
	
		
		
		
		
		
		
	}		
?>