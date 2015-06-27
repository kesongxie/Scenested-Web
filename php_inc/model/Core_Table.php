<?php
	require_once  $_SERVER['DOCUMENT_ROOT'].'/lsere/php_inc/global_constant.php';
	require_once 'Database_Connection.php';
	/*
		core_table is the base class for other table class
	*/
	class core_table{
		private $table_name;
		public $connection;
		
		public function __construct($t){
			$this->table_name = $t;
			$database_connection = new Database_Connection();
			$this->connection = $database_connection->getConnection();
		}
		
		
		public function deleteRowByUserId($id){
			$stmt = $this->connection->prepare("DELETE FROM `$this->table_name` WHERE `user_id` = ? ");
			if($stmt){
				$stmt->bind_param('i', $id);
				if($stmt->execute()){
					$stmt->close();
					return true;
				}
			}
			return false;
		}
		
		public function deleteRowForUserById($user_id, $id){
			$stmt = $this->connection->prepare("DELETE FROM `$this->table_name` WHERE `id` = ?  AND `user_id`=? LIMIT 1");
			$stmt->bind_param('ii', $id, $user_id);
			if($stmt->execute()){
				$stmt->close();
				return true;
			}
			return false;
		}
		
		public function deleteRowById($id){
			$stmt = $this->connection->prepare("DELETE FROM `$this->table_name` WHERE `id` = ? LIMIT 1");
			$stmt->bind_param('i', $id);
			if($stmt->execute()){
				$stmt->close();
				return true;
			}
			return false;
		}
		
		/*	$selector_column is the unique identifier that is in each row
			$selector_value is the value of the unique identifier
			this function requires $selector_column to be of none numeric type, such as string
		*/
		public function deleteRowBySelector($selector_column, $selector_value){
			$selector_column = $this->connection->escape_string($selector_column);
			$stmt = $this->connection->prepare("DELETE FROM `$this->table_name` WHERE `$selector_column` = ? ");
			if($stmt){
				$stmt->bind_param('s', $selector_value);
				if($stmt->execute()){
					$stmt->close();
					return true;
				}
			}
			return false;
		}
		
		public function deleteRowByNumericSelector($selector_column, $selector_value){
			$selector_column = $this->connection->escape_string($selector_column);
			$stmt = $this->connection->prepare("DELETE FROM `$this->table_name` WHERE `$selector_column` = ? ");
			if($stmt){
				$stmt->bind_param('i', $selector_value);
				if($stmt->execute()){
					$stmt->close();
					return true;
				}
			}
			return false;
		}
		
		
		
		
		
		public function getColumnById($column,$id){
			$column = $this->connection->escape_string($column);
			$stmt = $this->connection->prepare("SELECT `$column` FROM `$this->table_name` WHERE `id` = ? LIMIT 1 ");
			$stmt->bind_param('i',$id);
			if($stmt->execute()){
				 $result = $stmt->get_result();
				 if($result !== false && $result->num_rows == 1){
				 	$row = $result->fetch_assoc();
				 	$stmt->close();
					return $row[$column];
				 }
			}
			return false;
		}
		
		
		public function getColumnByUserId($column,$user_id){
			$column = $this->connection->escape_string($column);
			$stmt = $this->connection->prepare("SELECT `$column` FROM `$this->table_name` WHERE `user_id` = ?  ORDER BY `id` DESC LIMIT 1");
			if($stmt){
				$stmt->bind_param('i', $user_id);
				if($stmt->execute()){
					 $result = $stmt->get_result();
					 if($result !== false && $result->num_rows == 1){
						$row = $result->fetch_assoc();
						$stmt->close();
						return $row[$column];
					 }
				}
			}
			return false;
		}
		
		/*
			result order by id ascend if $ascend is set to true
		*/
		public function getColumnByUserIdFetchAll($column,$user_id, $ascend){
			$column = $this->connection->escape_string($column);
			if($ascend){
				$stmt = $this->connection->prepare("SELECT `$column` FROM `$this->table_name` WHERE `user_id` = ?  ORDER BY `id` ASC ");
			}else{
				$stmt = $this->connection->prepare("SELECT `$column` FROM `$this->table_name` WHERE `user_id` = ?  ORDER BY `id` DESC ");
			}
			if($stmt){
				$stmt->bind_param('i', $user_id);
				if($stmt->execute()){
					 $result = $stmt->get_result();
					 if($result !== false && $result->num_rows >= 1){
						$row = $result->fetch_assoc();
						$stmt->close();
						return $row[$column];
					 }
				}
			}
			return false;
		}
		
		
		public function getAllRowsColumnBySelector($column, $selector_column, $selector_value, $asc = false){
			$column = $this->connection->escape_string($column);
				$selector_column = $this->connection->escape_string($selector_column);
				if($asc){
					$stmt = $this->connection->prepare("SELECT `$column` FROM `$this->table_name` WHERE `$selector_column` = ? ");
				}else{
					$stmt = $this->connection->prepare("SELECT `$column` FROM `$this->table_name` WHERE `$selector_column` = ? ORDER BY `id` DESC");

				}
				if($stmt){
					$stmt->bind_param('s', $selector_value);
					if($stmt->execute()){
						 $result = $stmt->get_result();
						 if($result !== false && $result->num_rows >= 1){
							$row = $result->fetch_all(MYSQLI_ASSOC);
							$stmt->close();
							return $row;
						 }
					}
				}
				return false;
		}
		
		
		public function getColumnBySelector($column, $selector_column, $selector_value){
			$column = $this->connection->escape_string($column);
			$selector_column = $this->connection->escape_string($selector_column);
			$stmt = $this->connection->prepare("SELECT `$column` FROM `$this->table_name` WHERE `$selector_column` = ? LIMIT 1 ");
			if($stmt){
				$stmt->bind_param('s', $selector_value);
				if($stmt->execute()){
					 $result = $stmt->get_result();
					 if($result !== false && $result->num_rows == 1){
						$row = $result->fetch_assoc();
						$stmt->close();
						return $row[$column];
					 }
				}
			}
			return false;
		}
		
		public function getMultipleColumnsBySelector($column_array, $selector_column, $selector_value){
			$selector_column = $this->connection->escape_string($selector_column);
			$targets = implode('`,`',$column_array);
			$targets = '`'.$targets.'`';
			$stmt = $this->connection->prepare("SELECT $targets FROM `$this->table_name` WHERE `$selector_column` = ? LIMIT 1 ");
			if($stmt){
				$stmt->bind_param('s', $selector_value);
				if($stmt->execute()){
					 $result = $stmt->get_result();
					 if($result !== false && $result->num_rows == 1){
						$row = $result->fetch_all(MYSQLI_ASSOC);
						$stmt->close();
						return $row[0];
					 }
				}
			}
			return false;
		}
		
		
		
		public function checkColumnValueExistForUser($column, $column_value, $user_id){
			$column = $this->connection->escape_string($column);
			$column_value = $this->connection->escape_string($column_value);
			$stmt = $this->connection->prepare("SELECT `id` FROM `$this->table_name` WHERE `$column` = ? AND `user_id` = ? LIMIT 1 ");
			if($stmt){
				$stmt->bind_param('si', $column_value, $user_id);
				if($stmt->execute()){
					 $result = $stmt->get_result();
					 if($result !== false && $result->num_rows == 1){
						$stmt->close();
						return true;
					 }
				}
			}
			return false;
		}
		
		
		
		
		public function getMultipleColumnsById($column_array, $id){
			$targets = implode('`,`',$column_array);
			$targets = '`'.$targets.'`';
			$stmt = $this->connection->prepare("SELECT $targets FROM `$this->table_name` WHERE `id` = ? LIMIT 1");
			if($stmt){
				$stmt->bind_param('i', $id);
				if($stmt->execute()){
					 $result = $stmt->get_result();
					 if($result !== false && $result->num_rows == 1){
						$row = $result->fetch_all(MYSQLI_ASSOC);
						$stmt->close();
						return $row[0];
					 }
				}
			}
			return false;
		}
		
		
		
		
		
		
		/*
			this function is aim to select all rows that with the same $user_id
		*/
		public function getAllRowsMultipleColumnsByUserId($column_array, $user_id){
			$targets = implode('`,`',$column_array);
			$targets = '`'.$targets.'`';
			$stmt = $this->connection->prepare("SELECT $targets FROM `$this->table_name` WHERE `user_id` = ? ");
			if($stmt){
				$stmt->bind_param('i', $user_id);
				if($stmt->execute()){
					 $result = $stmt->get_result();
					 if($result !== false && $result->num_rows >= 1){
						$row = $result->fetch_all(MYSQLI_ASSOC);
						$stmt->close();
						return $row;
					 }
				}
			}
			return false;
		}
		
		/*
			this function is aim to select frist rows that with the same $user_id
		*/
		public function getFirstRowMultipleColumnsByUserId($column_array, $user_id){
			$targets = implode('`,`',$column_array);
			$targets = '`'.$targets.'`';
			$stmt = $this->connection->prepare("SELECT $targets FROM `$this->table_name` WHERE `user_id` = ? LIMIT 1");
			if($stmt){
				$stmt->bind_param('i', $user_id);
				if($stmt->execute()){
					 $result = $stmt->get_result();
					 if($result !== false && $result->num_rows == 1){
						$row = $result->fetch_all(MYSQLI_ASSOC);
						$stmt->close();
						return $row[0];
					 }
				}
			}
			return false;
		}
		
		
		/*
			this function is aim to select frist rows that with the same $user_id
		*/
		public function getLastRowMultipleColumnsByUserId($column_array, $user_id){
			$targets = implode('`,`',$column_array);
			$targets = '`'.$targets.'`';
			$stmt = $this->connection->prepare("SELECT $targets FROM `$this->table_name` WHERE `user_id` = ? ORDER BY `id` DESC LIMIT 1 ");
			if($stmt){
				$stmt->bind_param('i', $user_id);
				if($stmt->execute()){
					 $result = $stmt->get_result();
					 if($result !== false && $result->num_rows == 1){
						$row = $result->fetch_all(MYSQLI_ASSOC);
						$stmt->close();
						return $row[0];
					 }
				}
			}
			return false;
		}
		
		
		public function getRowsNumberForNumericColumn($column, $column_value){
			$column = $this->connection->escape_string($column);
			$stmt = $this->connection->prepare("SELECT `id` FROM `$this->table_name` WHERE `$column` = ? ");
			if($stmt){
				$stmt->bind_param('i', $column_value);
				if($stmt->execute()){
					 $result = $stmt->get_result();
					 if($result !== false){
						return $result->num_rows;
					 }
				}
			}
			return false;
		}
		
		
		
		
		
		
		
		
		
		
		public function setColumnByNumericSelector($column, $value, $selector_column, $selector_value ){
			$column = $this->connection->escape_string($column);
			$selector_column = $this->connection->escape_string($selector_column);
			$stmt = $this->connection->prepare("UPDATE `$this->table_name` SET `$column`=? WHERE `$selector_column`= ?  LIMIT 1");
			if($stmt){
				$stmt->bind_param('si', $value, $selector_value);
				if($stmt->execute()){
					$stmt->close();
					return true;
				}			
			}
			return false;
		}
		
		public function setColumnByStringSelector($column, $value, $selector_column, $selector_value ){
			$column = $this->connection->escape_string($column);
			$selector_column = $this->connection->escape_string($selector_column);
			$stmt = $this->connection->prepare("UPDATE `$this->table_name` SET `$column`=? WHERE `$selector_column`= ?  LIMIT 1");
			if($stmt){
				$stmt->bind_param('ss', $value, $selector_value);
				if($stmt->execute()){
					$stmt->close();
					return true;
				}			
			}
			return false;
		}
		
		
		
		
	
		public function setColumnById($column, $value, $id){
			$column = $this->connection->escape_string($column);
			$stmt = $this->connection->prepare("UPDATE `$this->table_name` SET `$column`=? WHERE `id` = ? LIMIT 1");
			if($stmt){
				$stmt->bind_param('si', $value, $id);
				if($stmt->execute()){
					$stmt->close();
					return true;
				}			
			}
			return false;
		}
		
		public function setColumnByUserId($column, $value, $id){
			$column = $this->connection->escape_string($column);
			$stmt = $this->connection->prepare("UPDATE `$this->table_name` SET `$column`=? WHERE `user_id` = ? LIMIT 1");
			if($stmt){
				$stmt->bind_param('si', $value, $id);
				if($stmt->execute()){
					$stmt->close();
					return true;
				}			
			}
			return false;
		}
		
		
		
		/*
			$value needs to be a string type
		*/
		public function isStringValueExistingForColumn($value, $column){
			$column = $this->connection->escape_string($column);
			$stmt = $this->connection->prepare("SELECT `id` FROM `$this->table_name` WHERE `$column` = ? LIMIT 1");
			if($stmt){
				$stmt->bind_param('s', $value);
				if($stmt->execute()){
					 $result = $stmt->get_result();
					 if($result->num_rows == 1){
						return true;
					 }
				}
			}
			return false;
		}
		
		public function generateUniqueHash(){
			$unique_hash = "";
			do{
				$unique_hash = getRandomString();
				$found = $this->isStringValueExistingForColumn($unique_hash, 'hash');
			}while($found);
			return $unique_hash;
		}
		
		public function getRowIdByHashkey($key){
			return $this->getColumnBySelector('id', 'hash', $key);

		}
		
		
		
	}
?>