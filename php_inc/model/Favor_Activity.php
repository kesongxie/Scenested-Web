<?php
	include_once 'Favor.php';
	class Favor_Activity extends Favor{
		private $table_name = "favor_activity";
		private $activity_id = null;
		private $activity = null;
		
		public function __construct($key = null){
			parent::__construct($this->table_name);
			if($key !== null){
				include_once 'Interest_Activity.php';
				$this->activity = new Interest_Activity();
				$this->activity_id = $this->activity->getActivityIdByKey($key);
			}
		}
		
		public function favorActivity(){
			if($this->activity_id !== null && $this->activity_id !== false ){
				$user_id_get = $this->activity->getPostUserByActivityId($this->activity_id );
				$this->addFavor($this->activity_id , $_SESSION['id'], $user_id_get);
			}
		}
		
		public function undoFavorActivity(){
			if($this->activity_id !== null && $this->activity_id !== false ){
				$this->undoFavorForSessionUser($this->activity_id);
			}
		}
		
		public function getFavorNumForActivity(){
			if($this->activity_id !== null && $this->activity_id !== false ){
				return $this->getFavorNum($this->activity_id);
			}
			return 0;
		}
		
		
		
		
		
		
		
	}
?>