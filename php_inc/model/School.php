<?php
	require_once 'Database_Connection.php';
	class School{
		public static function getSchoolNameBySchoolId($school_id){
			$database_connection = new Database_Connection();
			$stmt = $database_connection->getConnection()->prepare("SELECT `school_name` FROM `school` WHERE `id` = ? LIMIT 1 ");
			$stmt->bind_param('i',$school_id);
			if($stmt->execute()){
				 $result = $stmt->get_result();
				 if($result !== false && $result->num_rows == 1){
				 	$row = $result->fetch_assoc();
				 	$stmt->close();
					return $row['school_name'];
				 }
			}
			return false;
		}
		
		public static function getSchooIdBySchoolName($school_name){
			$database_connection = new Database_Connection();
			$stmt = $database_connection->getConnection()->prepare("SELECT `id` FROM `school` WHERE `school_name` = ? LIMIT 1 ");
			$school_name = trim($school_name);
			$stmt->bind_param('s',$school_name);
			if($stmt->execute()){
				 $result = $stmt->get_result();
				 if($result !== false && $result->num_rows == 1){
				 	$row = $result->fetch_assoc();
				 	$stmt->close();
					return $row['id'];
				 }
			}
			return false;
		}
		
		
		public static function getSuggestSchoolBlockByKeyWord($key_word,$limit){
			$database_connection = new Database_Connection();
			$connection = $database_connection->getConnection();
			if($limit > 0){
				$stmt = $connection->prepare("SELECT  `school_name`,`picture_url` FROM `school` WHERE `school_name` LIKE ? || `picture_url` LIKE ?   LIMIT ? ");
			}else{
				$stmt = $connection->prepare("SELECT  `school_name`,`picture_url` FROM `school` WHERE `school_name` LIKE ? || `picture_url` LIKE ?  ");
			}
			if($stmt){
				$key_word = '%' .$key_word. '%';
				if($limit > 0){
					$stmt->bind_param('ssi',$key_word, $key_word,  $limit);
				}else{
					$stmt->bind_param('ss', $key_word, $key_word);
				}
				if($stmt->execute()){
					 $result = $stmt->get_result();
					 if($result !== false && $result->num_rows >= 1){
						$rows = $result->fetch_all(MYSQLI_ASSOC);
						$stmt->close();
						ob_start();
						include(TEMPLATE_PATH_CHILD."school_name_suggest.phtml");
						$content = ob_get_clean();
						return $content;
					 }
				}
			}
			return false;
		}
	}	
?>