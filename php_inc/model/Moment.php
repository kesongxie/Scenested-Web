<?php
	include_once 'core_table.php';
	include_once 'Moment_Photo.php';
	
	class Moment extends Core_Table{
		private  $table_name = "moment";
		private  $activity_id = false;
		public $moment_photo = null;
		public function __construct($interest_activity_id){
			parent::__construct($this->table_name);
			$this->activity_id = $interest_activity_id;
			$this->moment_photo = new Moment_Photo();
		}
		
		public function addMomentForUser($user_id, $description, $date, $photoFile){
			$stmt = $this->connection->prepare("INSERT INTO `$this->table_name` (`interest_activity_id`,`description`,`date`) VALUES(?, ?, ?)");
			if($stmt){
				$stmt->bind_param('iss',$this->activity_id,$description,$date);
				if($stmt->execute()){
					if($photoFile != null){
						$moment_id = $this->connection->insert_id;
						$moment_photo_url = $this->moment_photo->uploadMomentPhotoByMomentId($photoFile, $user_id, $moment_id);
						if($moment_photo_url === false){
							$this->deleteRowById($moment_id);
							$stmt->close();
							return false;
						}
						$activity_id = $moment_id;
						
					}
					$stmt->close();
					return $this->activity_id;
				}
			}
			return false;
		}
		
		public function loadMomentResource(){
			$column_array = array('id','description','date');
			return $this->getMultipleColumnsBySelector($column_array, 'interest_activity_id', $this->activity_id);	
		}
		

	}
?>