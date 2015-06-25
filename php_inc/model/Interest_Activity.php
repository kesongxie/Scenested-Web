<?php
	include_once 'core_table.php';
	include_once 'User_Media_Prefix.php';
	include_once 'Moment.php';
	
	class Interest_Activity extends Core_Table{
		private  $table_name = "interest_activity";
		private $moment = null;
		
		public function __construct(){
			parent::__construct($this->table_name);
		}
		
		public function addMomentInterestActivityForUserByInterestId($user_id,$interest_id, $description, $date, $attached_picture){
			//the interest is editable by the given user
			$stmt = $this->connection->prepare("INSERT INTO `$this->table_name` (`user_id`,`interest_id`,`type`,`post_time`) VALUES(?, ?, ?, ?)");
			if($stmt){
				$time = date('Y-m-d H:i:s');
				$type = 'm';
				$stmt->bind_param('iiss',$user_id, $interest_id,$type,$time);
				if($stmt->execute()){
					$interest_activity_id = $this->connection->insert_id;
					$this->moment = new Moment($interest_activity_id);
					if($this->moment->addMomentForUser($user_id, $description, $date, $attached_picture) === false){
						//failed
						$this->deleteRowById($interest_activity_id);
						return false;
					}
					
					$stmt->close();
					return $this->getInterestActivityBlockByActivityId($interest_activity_id);
				}
				
			}
			return false;
		
		}
		
		
		public function getInterestActivityBlockByActivityId($activity_id){
			$column_array = array('user_id','type','post_time');
			$interest_activity = $this->getMultipleColumnsById($column_array, $activity_id);
			if($interest_activity !== false){
				if($interest_activity['type'] == 'm'){
					/* get data based on self table columns*/
					include_once 'User_Profile_Picture.php';
					$profile = new User_Profile_Picture();
					$post_owner_pic = $profile->getLatestProfileImageForUser($interest_activity['user_id']);
					include_once 'User_Table.php';
					$user = new User_Table();
					$fullname = $user->getUserFullnameByUserIden($interest_activity['user_id']);
					$post_time = convertDateTimeToAgo($interest_activity['post_time'], true);	
					
					/*get data from the moment*/
					$this->moment = new Moment($activity_id);
					$moment = $this->moment->loadMomentResource();
					$date = returnShortDate($moment['date']);
					$description = $moment['description'];
					include_once 'User_Media_Prefix.php';
					$prefix = new User_Media_Prefix();
					$moment_photo = $this->moment->moment_photo->getMomentPhotoUrlByMomentId($moment['id']);
					if($moment_photo !== false){
						$moment_photo = $prefix->getUserMediaPrefix($interest_activity['user_id']).'/'.$moment_photo;
					}
					ob_start();
					include(SCRIPT_INCLUDE_BASE.'phtml/child/post_moment_block.phtml');
					$moment_block = ob_get_clean();
					return $moment_block;
				}else{
					//return event
					return false;
				}
			}
			return false;
		}		
		
		
		public function getActivityIdCollectionByInterestId($interest_id){
			return $this->getAllRowsColumnBySelector('id', 'interest_id', $interest_id);
		}
		

	}
?>