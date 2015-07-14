<?php
	include_once 'core_table.php';
	include_once 'User_Notification_Queue.php';
	include_once 'Code_For_Notification_Sendable_Table.php';


	class Noti_Sendable extends Core_Table{
		public $noti_queue = null;
		public function __construct($table_name){
			parent::__construct($table_name);
			$from = Code_For_Notification_Sendable_Table::getCodeForTable($table_name);
			$this->noti_queue = new User_Notification_Queue($from);
		}
		
		
		public function deleteNotiQueueForKey($key){
			$id = false;
			$column_array = array('id','user_id_get');
			$row = $this->getMultipleColumnsBySelector($column_array, 'hash',$key);
			if($row !== false){
				$id = $row['id'];
				$user_id_get = $row['user_id_get'];
				$this->noti_queue->removeQueueForUser($user_id_get, $id);
			}
			return $id;
		}
		
		
		
		
		
		
	}
?>