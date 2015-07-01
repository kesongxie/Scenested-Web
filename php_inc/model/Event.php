<?php
	include_once 'core_table.php';
	include_once 'Event_Photo.php';
	
	class Event extends Core_Table{
		private  $table_name = "event";
		private  $activity_id = false;
		public $event_photo = null;
		public function __construct($interest_activity_id){
			parent::__construct($this->table_name);
			$this->activity_id = $interest_activity_id;
			$this->event_photo = new Event_Photo();
		}
		
		public function addEventForUser($user_id, $title, $description, $location, $date, $evt_time, $photoFile, $caption){
			$stmt = $this->connection->prepare("INSERT INTO `$this->table_name` (`interest_activity_id`,`title`,`description`,`location`,`date`,`time`) VALUES(?, ?, ?, ?, ?,?)");
			if($stmt){
				if(empty($location)){
					$location = null;
				}
				if(empty($date)){
					$date = null;
				}
				if(empty($evt_time)){
					$evt_time = null;
				}
			
				$stmt->bind_param('isssss',$this->activity_id, $title, $description, $location, $date,$evt_time);
				if($stmt->execute()){
					 if($photoFile != null){
						$event_id = $this->connection->insert_id;
						$event_photo_url = $this->event_photo->uploadEventPhotoByEventId($photoFile, $user_id, $event_id, $caption);
						if($event_photo_url === false){
							$this->deleteRowById($event_id);
							$stmt->close();
							return false;
						}
					}
					$stmt->close();
					return $this->activity_id;
				}
			}
			echo $this->connection->error;
			return false;
		}
		
		
		
		public function loadEventResource(){
			$column_array = array('id','title','description','location','date','time');
			return $this->getMultipleColumnsBySelector($column_array, 'interest_activity_id', $this->activity_id);	
		}
		
	}
?>