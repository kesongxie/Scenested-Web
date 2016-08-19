<?php
	// require_once  $_SERVER['DOCUMENT_ROOT'].'/php_inc/model/Database_Connection.php';
// 	require_once  $_SERVER['DOCUMENT_ROOT'].'/php_inc/global_constant.php';

	/*
		core_table is the base class for other table class
	*/
	class Core_Table{
		private $table_name;
		private $primary_key;
		public $connection;
		
		public function __construct($t, $p){
			$this->table_name = $t;
			$this->primary_key = $p;
			$database_connection = new Database_Connection();
			$this->connection = $database_connection->getConnection();
		}
		
		public function getColumnById($column,$id){
			$column = $this->connection->escape_string($column);
			$stmt = $this->connection->prepare("SELECT `$column` FROM `$this->table_name` WHERE `$this->primary_key` = ? LIMIT 1 ");
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
		
		public function getAllColumnsById($id){
			$stmt = $this->connection->prepare("SELECT * FROM `$this->table_name` WHERE `$this->primary_key` = ? LIMIT 1");
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
		
		public function getMultipleColumnsById($column_array, $id){
			$columns = implode('`,`',$column_array);
			$columns = '`'.$columns.'`';
			$stmt = $this->connection->prepare("SELECT $columns FROM `$this->table_name` WHERE `$this->primary_key` = ? LIMIT 1");
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
		
		
		public function getAllRowsColumnBySelector($column, $selector_column, $selector_value, $asc = false){
				$column = $this->connection->escape_string($column);
				$selector_column = $this->connection->escape_string($selector_column);
				$query = "SELECT `$column` FROM `$this->table_name` WHERE `$selector_column` = ? ";
				if(!$asc){
					$query.="ORDER BY `$this->primary_key` DESC";
				}
				$stmt = $this->connection->prepare($query);
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
				echo $this->connection->error;
				return false;
		}
		
		public function getAllRowsMultipleColumnsBySelector($column_array, $selector_column, $selector_value, $numericSelector = false,   $asc = false){
			$selector_column = $this->connection->escape_string($selector_column);
			$columns = implode('`,`',$column_array);
			$columns = '`'.$columns.'`';
			
			$query = "SELECT $columns FROM `$this->table_name` WHERE `$selector_column` = ? ";
			if(!$asc){
				$query.="ORDER BY `$this->primary_key` DESC";
			}
			$stmt = $this->connection->prepare($query);
			if($stmt){
				if($numericSelector){
					$stmt->bind_param('i', $selector_value);
				}else{
					$stmt->bind_param('s', $selector_value);
				}
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

		
		public function deleteRowById($id){
			$stmt = $this->connection->prepare("DELETE FROM `$this->table_name` WHERE `$this->primary_key` = ? LIMIT 1");
			$stmt->bind_param('i', $id);
			if($stmt->execute()){
				$stmt->close();
				return true;
			}
			return false;
		}
		
		/*
			$value needs to be a string type
		*/
		public function isStringValueExistingForColumn($column, $value){
			$column = $this->connection->escape_string($column);
			$primary_key = $this->table_name.'_id';
			$stmt = $this->connection->prepare("SELECT `$primary_key` FROM `$this->table_name` WHERE `$column` = ? LIMIT 1");
			if($stmt){
				$stmt->bind_param('s', $value);
				if($stmt->execute()){
					 $result = $stmt->get_result();
					 if($result->num_rows == 1){
					 	$stmt->close();
						return true;
					 }
				}
			}
			return false;
		}
		
		public function getColumnBySelector($column, $selector_column, $selector_value, $numericSelector = false){
			$column = $this->connection->escape_string($column);
			$selector_column = $this->connection->escape_string($selector_column);
			$stmt = $this->connection->prepare("SELECT `$column` FROM `$this->table_name` WHERE `$selector_column` = ? LIMIT 1 ");
			if($stmt){
				if($numericSelector){
					$stmt->bind_param('i', $selector_value);
				}else{
					$stmt->bind_param('s', $selector_value);
				}
				if($stmt->execute()){
					 $result = $stmt->get_result();
					 if($result !== false && $result->num_rows == 1){
						$row = $result->fetch_assoc();
						$stmt->close();
						return $row[$column];
					 }
				}
			}
			echo $this->connection->error;
			return false;
		}
		
		
		
		
		
		public function getMultipleColumnsBySelector($column_array, $selector_column, $selector_value, $numericSelector = false){
			$selector_column = $this->connection->escape_string($selector_column);
			$targets = implode('`,`',$column_array);
			$targets = '`'.$targets.'`';
			$stmt = $this->connection->prepare("SELECT $targets FROM `$this->table_name` WHERE `$selector_column` = ? LIMIT 1 ");
			if($stmt){
				if($numericSelector){
					$stmt->bind_param('i', $selector_value);
				}else{
					$stmt->bind_param('s', $selector_value);
				}
				if($stmt->execute()){
					 $result = $stmt->get_result();
					 if($result !== false && $result->num_rows == 1){
						$row = $result->fetch_all(MYSQLI_ASSOC);
						$stmt->close();
						return $row[0];
					 }
				}
			}
			echo $this->connection->error;
			return false;
		}
		
		public function checkStringColumnValueExistsForUser($column, $column_value, $user_id){
			$column = $this->connection->escape_string($column);
			$column_value = $this->connection->escape_string($column_value);
			$stmt = $this->connection->prepare("SELECT `$this->primary_key` FROM `$this->table_name` WHERE `$column` = ? AND `user_id` = ? LIMIT 1 ");
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
		
		public function deleteRowForUserById($user_id, $id){
			$stmt = $this->connection->prepare("DELETE FROM `$this->table_name` WHERE `$this->primary_key` = ?  AND `user_id`=? LIMIT 1");
			$stmt->bind_param('ii', $id, $user_id);
			if($stmt->execute()){
				$stmt->close();
				return true;
			}
			return false;
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
		
		public function deleteRowBySelectorForUser($selector_column, $selector_value, $user_id, $all = false){
			$selector_column = $this->connection->escape_string($selector_column);
			if($all){
				$stmt = $this->connection->prepare("DELETE FROM `$this->table_name` WHERE `$selector_column` = ? AND `user_id`=?");
			}else{
				$stmt = $this->connection->prepare("DELETE FROM `$this->table_name` WHERE `$selector_column` = ? AND `user_id`=? LIMIT 1");
			}
			if($stmt){
				$stmt->bind_param('si', $selector_value, $user_id);
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
		
		
		
		
		
		
		
		
		public function getColumnByUserId($column,$user_id){
			$column = $this->connection->escape_string($column);
			$primary_key = $this->table_name.'_id';
			$stmt = $this->connection->prepare("SELECT `$column` FROM `$this->table_name` WHERE `user_id` = ?  ORDER BY `$primary_key` DESC LIMIT 1");
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
			echo $this->connection->error;
			return false;
		}
		
		
		
		public function getColumnBySelectorForUser($column,$selector_column,$selector_value,$user_id){
				$column = $this->connection->escape_string($column);
				$selector_column = $this->connection->escape_string($selector_column);
				$stmt = $this->connection->prepare("SELECT `$column` FROM `$this->table_name` WHERE `user_id`=? AND `$selector_column` = ? LIMIT 1");
				if($stmt){
					$stmt->bind_param('is',$user_id, $selector_value);
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
		
		
		
		public function getAllRowsColumnBySelectorForUser($column,$selector_column,$selector_value,$user_id, $asc = false){
				$column = $this->connection->escape_string($column);
				$selector_column = $this->connection->escape_string($selector_column);
				$primary_key = $this->table_name.'_id';
				if($asc){
					$stmt = $this->connection->prepare("SELECT `$column` FROM `$this->table_name` WHERE `user_id`=? AND `$selector_column` = ? ");
				}else{
					$stmt = $this->connection->prepare("SELECT `$column` FROM `$this->table_name` WHERE  `user_id`=? AND `$selector_column` = ? ORDER BY `$primary_key` DESC");
				}
				if($stmt){
					$stmt->bind_param('is',$user_id, $selector_value);
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
			result order by id ascend if $ascend is set to true
		*/
		public function getColumnByUserIdFetchAll($column,$user_id, $ascend = false){
			$column = $this->connection->escape_string($column);
			$primary_key = $this->table_name.'_id';
			if($ascend){
				$stmt = $this->connection->prepare("SELECT `$column` FROM `$this->table_name` WHERE `user_id` = ?  ORDER BY `$primary_key` ASC ");
			}else{
				$stmt = $this->connection->prepare("SELECT `$column` FROM `$this->table_name` WHERE `user_id` = ?  ORDER BY `$primary_key` DESC ");
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
		
		
		
		public function getRowsColumnBySelector($column, $selector_column, $selector_value, $limit_num = 1, $offset = 0, $asc = false){
			$column = $this->connection->escape_string($column);
				$selector_column = $this->connection->escape_string($selector_column);
				$primary_key = $this->table_name.'_id';
				if($offset != 0){
					if($asc){
						$stmt = $this->connection->prepare("SELECT `$column` FROM `$this->table_name` WHERE `$selector_column` = ? AND `$primary_key` < ? LIMIT ? ");
					}else{
						$stmt = $this->connection->prepare("SELECT `$column` FROM `$this->table_name` WHERE `$selector_column` = ? AND `$primary_key` < ? ORDER BY `$primary_key` DESC LIMIT ? ");
					}
				}else{
					if($asc){
						$stmt = $this->connection->prepare("SELECT `$column` FROM `$this->table_name` WHERE `$selector_column` = ?  LIMIT ? ");
					}else{
						$stmt = $this->connection->prepare("SELECT `$column` FROM `$this->table_name` WHERE `$selector_column` = ? ORDER BY `$primary_key` DESC LIMIT ? ");
					}
				}
				if($stmt){
					if($offset != 0){
						$stmt->bind_param('sii', $selector_value, $offset,$limit_num);
					}else{
						$stmt->bind_param('si', $selector_value,$limit_num);
					}
					if($stmt->execute()){
						 $result = $stmt->get_result();
						 if($result !== false && $result->num_rows >= 1){
							$row = $result->fetch_all(MYSQLI_ASSOC);
							$stmt->close();
							return $row;
						 }
					}
				}
				echo $this->connection->error;
				return false;
		}
		
		
		public function getRowsMultipleColumnsBySelectorWithFilter($column_array, $selector_column, $selector_value, $limit_num = 1, $offset = 0, $asc = false){
				$selector_column = $this->connection->escape_string($selector_column);
				$targets = implode('`,`',$column_array);
				$targets = '`'.$targets.'`';
				$primary_key = $this->table_name.'_id';
				if($asc){
					$stmt = $this->connection->prepare("SELECT $targets FROM `$this->table_name` WHERE `$selector_column` = ? LIMIT ?,? ");
				}else{
					$stmt = $this->connection->prepare("SELECT $targets FROM `$this->table_name` WHERE `$selector_column` = ? ORDER BY `$primary_key` DESC LIMIT ?,? ");
				}
				if($stmt){
				$stmt->bind_param('sii', $selector_value, $offset,$limit_num);
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
		
		
		
		
	
		
		
		
		
		
		public function getMultipleColumnsByUserId($column_array, $user_id){
			$targets = implode('`,`',$column_array);
			$targets = '`'.$targets.'`';
			$stmt = $this->connection->prepare("SELECT $targets FROM `$this->table_name` WHERE `user_id` = ? LIMIT 1 ");
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
		
		
		
		
		
		
		
		public function checkNumericColumnValueExistForUser($column, $column_value, $user_id){
			$column = $this->connection->escape_string($column);
			$column_value = $this->connection->escape_string($column_value);
			$primary_key = $this->table_name.'_id';
			$stmt = $this->connection->prepare("SELECT `$primary_key` FROM `$this->table_name` WHERE `$column` = ? AND `user_id` = ? LIMIT 1 ");
			if($stmt){
				$stmt->bind_param('ii', $column_value, $user_id);
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
		
		
		
		public function isRowForUserExists($user_id){
			$primary_key = $this->table_name.'_id';
			$stmt = $this->connection->prepare("SELECT `$primary_key` FROM `$this->table_name` WHERE  `user_id` = ? LIMIT 1 ");
			if($stmt){
				$stmt->bind_param('i', $user_id);
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
		
		
		
	
		
		
		
		
		
		/*
			this function is aim to select all rows that with the same $user_id
		*/
		public function getAllRowsMultipleColumnsByUserId($column_array, $user_id, $asc = false){
			$targets = implode('`,`',$column_array);
			$targets = '`'.$targets.'`';
			$primary_key = $this->table_name.'_id';
			if($asc){
				$stmt = $this->connection->prepare("SELECT $targets FROM `$this->table_name` WHERE `user_id` = ? ");
			}else{
				$stmt = $this->connection->prepare("SELECT $targets FROM `$this->table_name` WHERE `user_id` = ? ORDER BY `$primary_key` DESC");
			}
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
			$primary_key = $this->table_name.'_id';
			$stmt = $this->connection->prepare("SELECT $targets FROM `$this->table_name` WHERE `user_id` = ? ORDER BY `$primary_key` DESC LIMIT 1 ");
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
		
		
		
		
		public function getRowsNumberForStringColumn($column, $column_value){
			$column = $this->connection->escape_string($column);
			$primary_key = $this->table_name.'_id';
			$stmt = $this->connection->prepare("SELECT `$primary_key` FROM `$this->table_name` WHERE `$column` = ? ");
			if($stmt){
				$stmt->bind_param('s', $column_value);
				if($stmt->execute()){
					 $result = $stmt->get_result();
					 if($result !== false){
					 	$stmt->close();
						return $result->num_rows;
					 }
				}
			}
			return false;
		}
		
		
		public function getColumnRowsGreaterThanSelector($column,$selector_column, $selector_value,  $limit = -1, $asc = false ){
			$column = $this->connection->escape_string($column);
			$selector_column = $this->connection->escape_string($selector_column);
			$primary_key = $this->table_name.'_id';
			if($asc){
				if($limit > 0){
					$stmt = $this->connection->prepare("SELECT `$column` FROM `$this->table_name` WHERE `$selector_column` > ? LIMIT ? ");
				}else{
					$stmt = $this->connection->prepare("SELECT `$column` FROM `$this->table_name` WHERE `$selector_column` > ? ");
				}
			}else{
				if($limit > 0){
					$stmt = $this->connection->prepare("SELECT `$column` FROM `$this->table_name` WHERE `$selector_column` > ? ORDER BY `$primary_key` DESC LIMIT ? ");
				}else{
					$stmt = $this->connection->prepare("SELECT `$column` FROM `$this->table_name` WHERE `$selector_column` > ? ORDER BY `$primary_key` DESC ");
				}
			}	
			if($stmt){
				if($limit > 0){
					$stmt->bind_param('si', $selector_value,$limit);
				}else{
					$stmt->bind_param('s', $selector_value);
				}
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
		
		
		//the selector is of int type
		public function getColumnRowsGreaterThanRowId($column, $row_id,$selector_name, $selector_value,  $limit = -1, $asc = false ){
			$selector_name = $this->connection->escape_string($selector_name);
			$column = $this->connection->escape_string($column);
			$primary_key = $this->table_name.'_id';
			if($asc){
				if($limit > 0){
					$stmt = $this->connection->prepare("SELECT `$column` FROM `$this->table_name` WHERE `$primary_key` > ?  AND `$selector_name` = ? LIMIT ? ");
				}else{
					$stmt = $this->connection->prepare("SELECT `$column` FROM `$this->table_name` WHERE `$primary_key` > ? AND `$selector_name` = ?  ");
				}
			}else{
				if($limit > 0){
					$stmt = $this->connection->prepare("SELECT `$column` FROM `$this->table_name` WHERE `$primary_key` > ? AND `$selector_name` = ?  ORDER BY `$primary_key` DESC LIMIT ? ");
				}else{
					$stmt = $this->connection->prepare("SELECT `$column` FROM `$this->table_name` WHERE `$primary_key` > ? AND `$selector_name` = ?  ORDER BY `$primary_key` DESC ");
				}
			}	
			if($stmt){
				if($limit > 0){
					$stmt->bind_param('iii', $row_id,$selector_value,$limit);
				}else{
					$stmt->bind_param('ii', $selector_value, $row_id);
				}
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
		
		public function getColumnRowsLessThanRowId($column, $row_id,$selector_name, $selector_value,  $limit = -1, $asc = false ){
			$selector_name = $this->connection->escape_string($selector_name);
			$column = $this->connection->escape_string($column);
			$primary_key = $this->table_name.'_id';
			if($asc){
				if($limit > 0){
					$stmt = $this->connection->prepare("SELECT `$column` FROM `$this->table_name` WHERE `$primary_key` < ?  AND `$selector_name` = ? LIMIT ? ");
				}else{
					$stmt = $this->connection->prepare("SELECT `$column` FROM `$this->table_name` WHERE `$primary_key` < ? AND `$selector_name` = ?  ");
				}
			}else{
				if($limit > 0){
					$stmt = $this->connection->prepare("SELECT `$column` FROM `$this->table_name` WHERE `$primary_key` < ? AND `$selector_name` = ?  ORDER BY `$primary_key` DESC LIMIT ? ");
				}else{
					$stmt = $this->connection->prepare("SELECT `$column` FROM `$this->table_name` WHERE `$primary_key` < ? AND `$selector_name` = ?  ORDER BY `$primary_key` DESC ");
				}
			}	
			if($stmt){
				if($limit > 0){
					$stmt->bind_param('iii', $row_id,$selector_value,$limit);
				}else{
					$stmt->bind_param('ii', $selector_value, $row_id);
				}
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
			$primary_key = $this->table_name.'_id';
			$stmt = $this->connection->prepare("UPDATE `$this->table_name` SET `$column`=? WHERE `$primary_key` = ? LIMIT 1");
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
		
		
		
		
		
		
		
		public function isNumericValueExistingForColumn($value, $column){
			$column = $this->connection->escape_string($column);
			$primary_key = $this->table_name.'_id';
			$stmt = $this->connection->prepare("SELECT `$primary_key` FROM `$this->table_name` WHERE `$column` = ? LIMIT 1");
			if($stmt){
				$stmt->bind_param('i', $value);
				if($stmt->execute()){
					 $result = $stmt->get_result();
					 if($result->num_rows == 1){
					 	$stmt->close();
						return true;
					 }
				}
			}
			return false;
		}
		
		
		
		public function isRowExists($row_id){
			$primary_key = $this->table_name.'_id';
			$stmt = $this->connection->prepare("SELECT `$primary_key` FROM `$this->table_name` LIMIT 1");
			if($stmt){
				$stmt->bind_param('i', $value);
				if($stmt->execute()){
					 $result = $stmt->get_result();
					 if($result->num_rows == 1){
					 	$stmt->close();
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
			$primary_key = $this->table_name.'_id';
			return $this->getColumnBySelector($primary_key, 'hash', $key);
		}
		
		
		
		
		
		
	}
?>