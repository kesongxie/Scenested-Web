<?php
	include_once 'core_table.php';
	include_once 'User_Media_Prefix.php';
	include_once 'Moment.php';
	include_once 'Event.php';
	include_once 'comment.php';
	
	class Interest_Activity extends Core_Table{
		private  $table_name = "interest_activity";
		private $moment = null;
		private $event = null;
		private $comment = null;
		
		public function __construct(){
			parent::__construct($this->table_name);
			$this->comment = new Comment();
		}
		
		
		public function addEventInterestActivityForUserByInterestId($user_id,$interest_id, $title,$description,$location, $date, $evt_time, $attached_picture, $caption){
			$unique_hash = $this->generateUniqueHash();
			$stmt = $this->connection->prepare("INSERT INTO `$this->table_name` (`user_id`,`interest_id`,`type`,`post_time`,`hash`) VALUES(?, ?, ?, ?, ?)");
			if($stmt){
				$time = date('Y-m-d H:i:s');
				$type = 'e';
				$stmt->bind_param('iisss',$user_id, $interest_id,$type,$time,$unique_hash);
				if($stmt->execute()){
					$interest_activity_id = $this->connection->insert_id;
					 $this->event = new Event($interest_activity_id);
					if($this->event->addEventForUser($user_id, $title, $description, $location, $date, $evt_time, $attached_picture, $caption) === false){
						//failed
						$this->deleteRowById($interest_activity_id);
						return false;
					}
					$stmt->close();
					return $this->getEventInterestActivityBlockByActivityId($interest_activity_id);
				}
			}
			return false;
			
		}
		
		
		public function getEventInterestActivityBlockByActivityId($activity_id){
			$column_array = array('user_id','post_time','hash');
			$interest_activity = $this->getMultipleColumnsById($column_array, $activity_id);
			if($interest_activity !== false){
				/* get data based on self table columns*/
				include_once 'User_Profile_Picture.php';
				$profile = new User_Profile_Picture();
				$post_owner_pic = $profile->getLatestProfileImageForUser($interest_activity['user_id']);
				include_once 'User_Table.php';
				$user = new User_Table();
				$fullname = $user->getUserFullnameByUserIden($interest_activity['user_id']);
				$post_time = convertDateTimeToAgo($interest_activity['post_time'], true);	
				$user_page_redirect =  USER_PROFILE_ROOT.$user->getUserAccessUrl($interest_activity['user_id']);
				$hash = $interest_activity['hash'];
				
				/*get data from the moment*/
				$this->event = new event($activity_id);
				$event = $this->event->loadEventResource();
				$date = returnShortDate($event['date']);
				$time = $event['time'];
				$title = $event['title'];
				$location = $event['location'];

				$description = $event['description'];
				include_once 'User_Media_Prefix.php';
				$prefix = new User_Media_Prefix();
				$event_photo = $this->event->event_photo->getEventPhotoUrlByEventId($event['id']);
				if($event_photo !== false){
					$caption = $this->event->event_photo->getEventPhotoCaptionByPictureUrl($event_photo);
					$event_photo = $prefix->getUserMediaPrefix($interest_activity['user_id']).'/'.$event_photo;
				}
				
				
				//$comment_block = $this->getCommentBlockByActivityId($activity_id);
				$comment_number = $this->comment->getCommentNumberForTarget($activity_id);
				ob_start();
				include(SCRIPT_INCLUDE_BASE.'phtml/child/post_event_block.phtml');
				$event_block = ob_get_clean();
				return $event_block;
			}
			return false;
		}	
		
		
		
		
		
		
		
		
		public function addMomentInterestActivityForUserByInterestId($user_id,$interest_id, $description, $date, $attached_picture, $caption){
			//the interest is editable by the given user
			$unique_hash = $this->generateUniqueHash();
			$stmt = $this->connection->prepare("INSERT INTO `$this->table_name` (`user_id`,`interest_id`,`type`,`post_time`,`hash`) VALUES(?, ?, ?, ?, ?)");
			if($stmt){
				$time = date('Y-m-d H:i:s');
				$type = 'm';
				$stmt->bind_param('iisss',$user_id, $interest_id,$type,$time,$unique_hash);
				if($stmt->execute()){
					$interest_activity_id = $this->connection->insert_id;
					$this->moment = new Moment($interest_activity_id);
					if($this->moment->addMomentForUser($user_id, $description, $date, $attached_picture, $caption) === false){
						//failed
						$this->deleteRowById($interest_activity_id);
						return false;
					}
					$stmt->close();
					return $this->getMomentInterestActivityBlockByActivityId($interest_activity_id);
				}
			}
			return false;
		}
		
		
		public function getMomentInterestActivityBlockByActivityId($activity_id){
			$column_array = array('user_id','type','post_time','hash');
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
					$user_page_redirect =  USER_PROFILE_ROOT.$user->getUserAccessUrl($interest_activity['user_id']);
					$hash = $interest_activity['hash'];
					
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
					
					$caption =  $this->moment->moment_photo->getMomentPhotoCaptionByMomentId($moment['id']);
					
					//$comment_block = $this->getCommentBlockByActivityId($activity_id);
					$comment_number = $this->comment->getCommentNumberForTarget($activity_id);
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
		
		
		
		public function getInterestActivityBlockByActivityId($activity_id){
			$type = $this->getColumnById('type',$activity_id);
			if($type == 'm'){
				return $this->getMomentInterestActivityBlockByActivityId($activity_id);
			}else if($type == 'e'){
				return $this->getEventInterestActivityBlockByActivityId($activity_id);
			}
		
		}
		
		public function getCommentBlockByActivityId($activity_id){
			$comment_block = '';
			$idCollection =$this->comment->getSelfIdCollectionByTargetId($activity_id);
			if($idCollection !== false && sizeof($idCollection) > 0){
				foreach($idCollection as $row ){
					$comment_block.= $this->comment->renderCommentBlockByCommentId($row['id']);
				}
			}
			return $comment_block;
		}
		
		public function getCommentBlockByActivityKey($key){
			$activity_id = $this->getRowIdByHashkey($key);
			if($activity_id !== false){
				return $this->getCommentBlockByActivityId($activity_id);
			}
		}
		
		
		
		public function getActivityIdCollectionByInterestId($interest_id){
			return $this->getAllRowsColumnBySelector('id', 'interest_id', $interest_id);
		}
		
		
		public function deleteActivityForUserByActivityId($user_id, $key){
			 $activity_id = $this->getRowIdByHashkey($key);
			 if($activity_id !== false){
			 	if($this->deleteRowForUserById($user_id, $activity_id) !== false){
			 		$this->moment = new Moment($activity_id);
					$this->moment->deleteMomentForUserByActivityId($user_id);
					$this->comment->deleteAllCommentsForTarget($activity_id);
				}
			 }
		}
		
		
		
	}
?>