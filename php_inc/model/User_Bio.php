<?php
	// include_once PHP_INC_PATH.'core.inc.php';
	
	class User_Bio extends Core_Table{
		private $table_name = "user_bio";
		private $primary_key = "user_bio_id";
	
		public function __construct(){
			parent::__construct($this->table_name, $this->primary_key);
		}
		
		
		public function getBioForUser($user_id){
			return $this->getColumnByUserId('bio',$user_id);
		}
		
		public function isBioExistedForUser($user_id){
			return $this->isRowForUserExists($user_id);
		}
		
		
		public function updateBioForUser($bio, $user_id){
			$time = date('Y-m-d H:i:s');
			if($this->isBioExistedForUser($user_id)){
				$this->setColumnByNumericSelector('bio', $bio, 'user_id', $user_id);
				$this->setColumnByNumericSelector('time', $time, 'user_id', $user_id);
				return enRichText($this->getBioForUser($user_id));
			}else{
				//create row
				$stmt = $this->connection->prepare("INSERT INTO `$this->table_name` (`user_id`,`bio`, `time`) VALUES(?, ?, ?)");
				$stmt->bind_param('iss',$user_id,$bio, $time);
				if($stmt->execute()){
					$stmt->close();
					return enRichText($this->getBioForUser($user_id));
				}
				return false;
			}
		}
		
		
		
		
	}		
?>