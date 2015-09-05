<?php
	include_once 'Noti_Sendable.php';
	class Favor extends Noti_Sendable{
		private $table_name = null;
		public function __construct($table_name){
			parent::__construct($table_name);
			$this->table_name = $table_name;
		}
		
		public function addFavor($target_id, $user_id, $user_id_get){
			if($this->isUserAlreadyFavor($target_id, $user_id) === false){
				$unique_hash = $this->generateUniqueHash();
				$stmt = $this->connection->prepare("INSERT INTO `$this->table_name` (`target_id`,`user_id`,`user_id_get`,`sent_time`,`hash`) VALUES(?, ?, ?,  ?, ?)");
				$time = date('Y-m-d H:i:s');
				if($stmt){
					$stmt->bind_param('iiiss',$target_id, $user_id, $user_id_get, $time,$unique_hash);
					if($stmt->execute()){
						$stmt->close();
						if($user_id != $user_id_get){
							$favor_id = $this->connection->insert_id;
							$this->noti_queue->addNotificationQueueForUser($user_id_get, $favor_id);
						}
					}
				}
				echo $this->connection->error;
			}
			return false;
		}
		
		public function getTotalFavorNumForTarget($target_id){
			return $this->getRowsNumberForNumericColumn('target_id',$target_id);
		}
		
		
		
		public function isUserAlreadyFavor($target_id,$user_id){
			return $this->checkNumericColumnValueExistForUser('target_id',$target_id,$user_id);
		}
		
		
		public function isSessionUserAlreadyFavor($target_id){
			return $this->checkNumericColumnValueExistForUser('target_id',$target_id,$_SESSION['id']);
		}
		
		
		public function undoFavorForSessionUser($target_id){
			if($this->isSessionUserAlreadyFavor($target_id)){
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
		
		public function getFavorListForTarget($target_id){
			$rows = $this->getAllRowsColumnBySelector('user_id', 'target_id',$target_id);
			if($rows !== false && sizeof($rows) > 0){
				$list = '';
				foreach($rows as $row){
					$list.=$row['user_id'].',';
				}
				return trim($list,',');
			}
			return false;	
		}	
		
		public function getFavorPlainListForTarget($target_id){
			$rows = $this->getAllRowsColumnBySelector('user_id', 'target_id',$target_id);
			if($rows !== false){
				include_once 'User_Table.php';
				$user = new User_Table();
				if(sizeof($rows) > 1){
					$result = '';
					foreach($rows as $row){
						 $result .= $user->getUserFullnameByUserIden($row['user_id']).', ';
					}
					return trim($result,', ').' favor this';
					
				}else{
					$fullname = $user->getUserFullnameByUserIden($rows[0]['user_id']);
					return $fullname.' favors this';
				}
			}
			return false;
		}
		
	}
?>