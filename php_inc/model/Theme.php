<?php
	include_once PHP_INC_PATH.'core.inc.php';
	
	class Theme extends Core_Table{
		const KeyForThemeNameColomn = "name";
		private $table_name = "theme";
		private $primary_key = "theme_id";
		
		
	
		public function __construct(){
			parent::__construct($this->table_name, $this->primary_key);
		}
		
		public function getSimilarThemeBetweenTwoUsers($first_user_id, $second_user_id){
			$query = "SELECT a.name 
			   		  FROM theme a
					  INNER JOIN theme b
					  ON a.name = b.name
					  WHERE a.user_id = ? AND b.user_id = ?";
				  
			$stmt = $this->connection->prepare($query);
			$stmt->bind_param('ii', $first_user_id, $second_user_id);
			if($stmt->execute()){
				$result = $stmt->get_result();
				$themes = $result->fetch_all(MYSQLI_ASSOC);
				$stmt->close();
				return $themes;
			}
 			return false;
		}
		
		// $username is the user who triggered the notification(not the who is going to recieve the notification)
		// $flatenSimilarThemeString is the common theme between two users
		public static function getSimilarThemeNotificationBodyText($username, $flatenSimilarThemeString){
			return $username." shares similar themes with you - ".$flatenSimilarThemeString;
		}
	
		
		
	}		
?>