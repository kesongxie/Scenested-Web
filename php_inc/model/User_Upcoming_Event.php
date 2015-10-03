<?php
	include_once MODEL_PATH.'core_table.php';

	class User_Upcoming_Event extends Core_Table{
		private $table_name = "user_upcoming_event";
		private $upcoming_evt_template = TEMPLATE_PATH_CHILD."upcoming_evt_block.phtml";

		public function __construct(){
			parent::__construct($this->table_name);
		}
		
		public function template(){
			return $this->upcoming_evt_template;
		}
		
		public function getUpcomingEventForUser($user_id){
			$stmt = $this->connection->prepare("
			SELECT user_upcoming_event.hash, event.interest_activity_id AS activity_id, event.id AS event_id, event.title, event.description,event.location,event.date,event.time
			FROM user_upcoming_event 
			LEFT JOIN event
			ON  user_upcoming_event.event_id = event.id  WHERE user_upcoming_event.user_id = ? AND  TIMESTAMP(event.date, event.time) > NOW()   ORDER BY event.date, event.time ASC
			");
			if($stmt){
				$stmt->bind_param('i',$user_id);
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
		
		public function getUpComingEventNumForUser($user_id){
			$stmt = $this->connection->prepare("
			SELECT user_upcoming_event.id
			FROM user_upcoming_event 
			LEFT JOIN event
			ON  user_upcoming_event.event_id = event.id  WHERE user_upcoming_event.user_id = ? AND  TIMESTAMP(event.date, event.time) > NOW() 
			");
			if($stmt){
				$stmt->bind_param('i',$user_id);
				if($stmt->execute()){
					 $result = $stmt->get_result();
					 if($result !== false){
						return $result->num_rows;
					 }
				}
			}
			echo $this->connection->error;
			return false;
		}
		
		
	
		
		
		
		
	}
		
?>