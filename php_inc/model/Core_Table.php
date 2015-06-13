<?php
	require_once 'Database_Connection.php';
	/*
		core_table is the base class for other table class
	*/
	class core_table{
		public $table_name;
		public $connection;
		
		public function __construct($t){
			$this->table_name = $t;
			$database_connection = new Database_Connection();
			$this->connection = $database_connection->getConnection();
		}
		
		
		public function deleteRowByUserId($id){
			$stmt = $this->connection->prepare("DELETE FROM `$this->table_name` WHERE `user_id` = ? LIMIT 1");
			$stmt->bind_param('i', $id);
			if($stmt->execute()){
				$stmt->close();
				return true;
			}else{
				return false;
			}
		}
		
		public function deleteRowById($id){
			$stmt = $this->connection->prepare("DELETE FROM `$this->table_name` WHERE `id` = ? LIMIT 1");
			$stmt->bind_param('i', $id);
			if($stmt->execute()){
				$stmt->close();
				return true;
			}else{
				return false;
			}
		}
	}
?>