<?php
	include_once 'Noti_Sendable.php';
	class Favor extends Noti_Sendable{
		private $table_name = null;
		public function __construct($table_name){
			parent::__construct($table_name);
			$this->table_name = $table_name;
		}
		
		public function addFavor($target_id, $user_id, $user_id_get){
			if($this->isUserAlreadyFavorActivity($target_id, $user_id) === false){
				$unique_hash = $this->generateUniqueHash();
				$stmt = $this->connection->prepare("INSERT INTO `$this->table_name` (`target_id`,`user_id`,`user_id_get`,`sent_time`,`hash`) VALUES(?, ?, ?,  ?, ?)");
				$time = date('Y-m-d H:i:s');
				$stmt->bind_param('iiiss',$target_id, $user_id, $user_id_get, $time,$unique_hash);
				if($stmt->execute()){
					$stmt->close();
					if($user_id != $user_id_get){
						$favor_id = $this->connection->insert_id;
						$this->noti_queue->addNotificationQueueForUser($user_id_get, $favor_id);
					}
				}
			}
			return false;
		}
		
		public function getTotalFavorNumForActivity($target_id){
			return $this->getRowsNumberForNumericColumn('target_id',$target_id);
		}
		
		public function isUserAlreadyFavorActivity($target_id,$user_id){
			return $this->checkNumericColumnValueExistForUser('target_id',$target_id,$user_id);
		}
		
		
		public function isSessionUserAlreadyFavorActivity($target_id){
			return $this->checkNumericColumnValueExistForUser('target_id',$target_id,$_SESSION['id']);
		}
		
		
		public function undoFavorForSessionUser($target_id){
			if($this->isSessionUserAlreadyFavorActivity($target_id)){
				$key = $this->getColumnBySelectorForUser('hash','target_id',$target_id ,$_SESSION['id']);
				if($key !== false){
					$this->deleteNotiQueueForKey($key);
					$this->deleteRowBySelectorForUser('target_id', $target_id, $_SESSION['id'],true);
				}
			}
		}
		
		public function getFavorNum($target_id){
			return $this->getRowsNumberForNumericColumn('target_id',$target_id);
		}
		
		
		public function deleteAllFavorForTarget($target_id){
			$rows = $this->getAllRowsColumnBySelector('hash', 'target_id',$target_id);
			if($rows !== false && sizeof($rows) > 0){
				foreach($rows as $row){
					$this->deleteNotiQueueForKey($row['hash']);
				}
				$this->deleteRowByNumericSelector('target_id', $target_id);
			}
			

		}
		
		
		
	}
?>