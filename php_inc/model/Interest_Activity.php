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
				$post_time = convertDateTimeToAgo($interest_activity['post_time'], false);	
				$user_page_redirect =  USER_PROFILE_ROOT.$user->getUserAccessUrl($interest_activity['user_id']);
				$hash = $interest_activity['hash'];
				
				/*get data from the moment*/
				$this->event = new event($activity_id);
				$event = $this->event->loadEventResource();
				$time = "";
				if($event['date'] != null){
					$time .= returnShortDate($event['date'],',').' - '.getWeekDayFromDate($event['date']);
				}
				
				if($event['time'] != null){
					if($event['date'] != null){
						$time .= ', ';
					}
					$time .= convertTimeToAmPm($event['time']);
				}
				
				
				$title = $event['title'];
				
				$location = '';
				if($event['location'] != null){
					$location = $event['location'];
				}
				
				$isEventPassed = (time() > strtotime($event['date'].$event['time']));				
				$description = $event['description'];
				include_once 'User_Media_Prefix.php';
				$prefix = new User_Media_Prefix();
				$event_photo = $this->event->event_photo->getEventPhotoUrlByEventId($event['id']);
				$event_photo_num = $this->event->event_photo->getPhotoNumberForEvent($event['id']);
								
				
				$media_prefix = $prefix->getUserMediaPrefix($interest_activity['user_id']).'/';
				if($event_photo !== false && isMediaDisplayable($media_prefix.$event_photo)){
					$caption = $this->event->event_photo->getEventPhotoCaptionByPictureUrl($event_photo);
					$event_photo = $media_prefix.$event_photo;
				}else{
					//get the label photo as the event cover
					include_once 'User_Interest_Label_Image.php';
					$label_image = new User_Interest_Label_Image();
					$interest_id = $this->getColumnById('interest_id',$activity_id);
					$event_photo = $media_prefix.$label_image->getLabelImageUrlByInterestId($interest_id);
				}
				
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
					$post_time = convertDateTimeToAgo($interest_activity['post_time'], false);	
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
		
		
		public function getSlideShowCommentBlockByActivityId($activity_id){
			$comment_block = '';
			$idCollection =$this->comment->getSelfIdCollectionByTargetId($activity_id);
			if($idCollection !== false && sizeof($idCollection) > 0){
				$count = 0;
				foreach($idCollection as $row ){
					$firstComment = ( $count++ == 0 );
					$comment_block.= $this->comment->renderSlideShowCommentBlockByCommentId($row['id'], $firstComment);
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
		
		
		
		public function loadPassedEventCollectionForUser($user_id){
			$stmt = $this->connection->prepare("
			SELECT interest_activity.id AS activity_id,interest_activity.interest_id, interest_activity.hash,interest_activity.user_id,event.id AS event_id, event.title, event.description,event.date
			FROM  event 
			LEFT JOIN interest_activity
			ON interest_activity.id = event.interest_activity_id  WHERE interest_activity.user_id = ? AND interest_activity.type = 'e' AND TIMESTAMP(event.date, event.time) < NOW() ORDER BY interest_activity.id DESC
			");
			if($stmt){
				$stmt->bind_param('i',$user_id);
				if($stmt->execute()){
					 $result = $stmt->get_result();
					 if($result !== false && $result->num_rows >= 1){
						$rows = $result->fetch_all(MYSQLI_ASSOC);
						$stmt->close();
						if($rows !== false && sizeof($rows) > 0){
							$left_content = "";
							$right_content = "";
							$count = 0;
							foreach($rows as $row ){
								$hash = $row['hash'];
								
								$title = $row['title'];
								$description = $row['description'];  
								
								$prefix = new User_Media_Prefix();
								$this->event = new event($row['activity_id']);
								$event_photo = $this->event->event_photo->getEventPhotoUrlByEventId($row['event_id']);
								$event_photo_num = $this->event->event_photo->getPhotoNumberForEvent($row['event_id']);

								
								$media_prefix = $prefix->getUserMediaPrefix($row['user_id']).'/';
								if($event_photo !== false && isMediaDisplayable($media_prefix.$event_photo)){
									$caption = $this->event->event_photo->getEventPhotoCaptionByPictureUrl($event_photo);
									$event_photo = $media_prefix.$event_photo;
								}else{
									//get the label photo as the event cover
									include_once 'User_Interest_Label_Image.php';
									$label_image = new User_Interest_Label_Image();
									$event_photo = $media_prefix.$label_image->getLabelImageUrlByInterestId($row['interest_id']);
								}
								
								
								$date = returnShortDate($row['date'],'-');
								$comment_number = $this->comment->getCommentNumberForTarget($row['activity_id']);
								$comment_block = $this->getSlideShowCommentBlockByActivityId($row['activity_id']);
								ob_start();
								include(TEMPLATE_PATH_CHILD.'passed_event_block.phtml');
								$content = ob_get_clean();
							
								if($count++ % 2 == 0){
									$left_content.= $content;
								}else{
									$right_content.= $content;
								}
							}
			
							ob_start();
							include(TEMPLATE_PATH_CHILD.'event.phtml');
							$content = ob_get_clean();
							return $content;
			 				}
					 }
				}
			}
			echo $this->connection->error;
			
			return false;
		}
		
		
		public function deleteActivityForUserByActivityId($user_id, $key){
			$column_array = array('id','type');
			$result = $this->getMultipleColumnsBySelector($column_array, 'hash', $key);
			$activity_id = $result['id'];
			$type = $result['type'];
			if($activity_id !== false){
				if($this->deleteRowForUserById($user_id, $activity_id) !== false){
					if($type == 'm'){
						$this->moment = new Moment($activity_id);
						$this->moment->deleteMomentForUserByActivityId($user_id);
					}else if($type == 'e'){
						$this->event = new Event($activity_id);
						$this->event->deleteEventForUserByActivityId($user_id);
					}
					$this->comment->deleteAllCommentsByActivityId($activity_id);
				}
			}
		}
		
		public function loadEventPreviewBlockByKey($key, $user_id){
			$column_array = array('id','user_id','interest_id');
			$result = $this->getMultipleColumnsBySelector($column_array, 'hash', $key);
			if($result['id'] !== false){
				$this->event = new Event($result['id']);
				$isEventEditableForCurrentUser = $this->isEventEditableForCurrentUser($result['id'], $user_id);
				return $this->event->renderEventPrewviewBlock($result['user_id'],$result['interest_id'],$isEventEditableForCurrentUser );	
			}
			return false;
		}
		
		
		public function isEventEditableForCurrentUser($activity_id, $user_id){
			//check whether this event is posted by the given user
			$isPostForUser = $this->getAllRowsColumnBySelectorForUser('id','id',$activity_id,$user_id, $asc = false);
			//people in the joining list 
			if($isPostForUser !== false){
				return true;
			}
			return false;
		}
		
		
		public function isEvtPhotoUploadableByUserForEvent($user_id, $key){
			//either in the joining list or the post owner, and the event should have already passed. true return an event id, false otherwise
			$activity_id = $this->getRowIdByHashkey($key);
			$this->event = new Event($activity_id);
	
			if($this->event->isEventPassedForActivityId($activity_id)){
				return true;
			}	
			return false;
		}
		
		/* $user_id is the user who is uploading the photo*/
		public function uploadEvtPhotoByKey($key, $user_id, $photo_file){
			if($this->isEvtPhotoUploadableByUserForEvent($user_id, $key)){
				//upload the photo
				$event_id = $this->event->event_id;
				return $this->event->event_photo->uploadEventPhotoByEventId($photo_file, $user_id, $event_id);
			}
			return false;
		}	
		
	}
?>