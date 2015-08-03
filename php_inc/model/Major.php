<?php
	require_once 'Database_Connection.php';
	class Major{
		public static function getMajorNameByMajorId($Major_id){
			$database_connection = new Database_Connection();
			$stmt = $database_connection->getConnection()->prepare("SELECT `major_name` FROM `Major` WHERE `id` = ? LIMIT 1 ");
			$stmt->bind_param('i',$Major_id);
			if($stmt->execute()){
				 $result = $stmt->get_result();
				 if($result !== false && $result->num_rows == 1){
				 	$row = $result->fetch_assoc();
				 	$stmt->close();
					return $row['major_name'];
				 }
			}
			return false;
		}
		
		public static function getSchooIdByMajorName($major_name){
			$database_connection = new Database_Connection();
			$stmt = $database_connection->getConnection()->prepare("SELECT `id` FROM `Major` WHERE `major_name` = ? LIMIT 1 ");
			$major_name = trim($major_name);
			$stmt->bind_param('s',$major_name);
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
		
		
		public static function getSuggestMajorBlockByKeyWord($key_word,$limit){
			$database_connection = new Database_Connection();
			$connection = $database_connection->getConnection();
			if($limit > 0){
				$stmt = $connection->prepare("SELECT  `major_name` FROM `Major` WHERE `major_name` LIKE ?    LIMIT ? ");
			}else{
				$stmt = $connection->prepare("SELECT  `major_name` FROM `Major` WHERE `major_name` LIKE ?   ");
			}
			if($stmt){
				$key_word = '%' .$key_word. '%';
				if($limit > 0){
					$stmt->bind_param('si',$key_word, $limit);
				}else{
					$stmt->bind_param('s', $key_word);
				}
				if($stmt->execute()){
					 $result = $stmt->get_result();
					 if($result !== false && $result->num_rows >= 1){
						$rows = $result->fetch_all(MYSQLI_ASSOC);
						$stmt->close();
						ob_start();
						include(TEMPLATE_PATH_CHILD."major_name_suggest.phtml");
						$content = ob_get_clean();
						return $content;
					 }
				}
			}
			return false;
		}
	}	
?>