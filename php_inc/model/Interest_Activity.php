<?php
	include_once 'core_table.php';
	include_once 'User_Table.php';
	include_once 'User_Media_Prefix.php';
	include_once 'Moment.php';
	include_once 'Event.php';
	
	class Interest_Activity extends Core_Table{
		private  $table_name = "interest_activity";
		private $moment = null;
		private $event = null;
		
		public function __construct(){
			parent::__construct($this->table_name);
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
				$joined_user = $this->event->getJoinedUserByEventId($event['id']);
				$joined_user_num = ($joined_user !== false)?sizeof(explode(',',$joined_user['members'])):1;
				$isEventPassed = (time() > strtotime($event['date'].$event['time']));				
				$description = $event['description'];
				$event_joined = $this->event->hasUserJoinedEvent($_SESSION['id'], $event['id']);
				include_once 'User_Media_Prefix.php';
				$prefix = new User_Media_Prefix();
				$event_photo = $this->event->event_photo->getEventPhotoResourceByMomentId($event['id']);
				$event_photo_num = $this->event->event_photo->getPhotoNumberForEvent($event['id']);
								
				$media_prefix = $prefix->getUserMediaPrefix($interest_activity['user_id']).'/';
				if($event_photo !== false && isMediaDisplayable($media_prefix.$event_photo['picture_url'])){
					$event_photo_url = U_IMGDIR.$media_prefix.$event_photo['picture_url'];
					$event_photo_hash = $event_photo['hash'];
				}
				include_once 'Comment.php';
				$comment = new Comment();
				$comment_number = $comment->getCommentNumberForTarget($activity_id);
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
					$moment_photo = $this->moment->moment_photo->getMomentPhotoResourceByMomentId($moment['id']);
					if($moment_photo !== false){
						$photo_url = $prefix->getUserMediaPrefix($interest_activity['user_id']).'/'.$moment_photo['picture_url'];
						$photo_hash = $moment_photo['hash'];
					}
					
					$caption =  $this->moment->moment_photo->getMomentPhotoCaptionByMomentId($moment['id']);
					
					include_once 'Comment.php';
					$comment = new Comment();
					$comment_number = $comment->getCommentNumberForTarget($activity_id);
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
			include_once 'Comment.php';
			$comment = new Comment();
			$idCollection =$comment->getSelfIdCollectionByTargetId($activity_id);
			if($idCollection !== false && sizeof($idCollection) > 0){
				foreach($idCollection as $row ){
					$comment_block.= $comment->renderCommentBlockByCommentId($row['id']);
				}
			}
			return $comment_block;
		}
		
		
		public function getSlideShowCommentBlockByActivityId($activity_id){
			$comment_block = '';
			include_once 'Comment.php';
			$comment = new Comment();
			$idCollection =$comment->getSelfIdCollectionByTargetId($activity_id);
			if($idCollection !== false && sizeof($idCollection) > 0){
				$count = 0;
				foreach($idCollection as $row ){
					$firstComment = ( $count++ == 0 );
					$comment_block.= $comment->renderSlideShowCommentBlockByCommentId($row['id'], $firstComment);
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
		
		
		
		
		
		
		
		
		
		
		public function loadEventCollectionForUser($user_id, $passed = false){
			if($passed){
				$stmt = $this->connection->prepare("
				SELECT * 
				FROM(
					SELECT DISTINCT interest_activity.id AS activity_id,interest_activity.interest_id, interest_activity.hash,interest_activity.user_id,event.id AS event_id, event.title, event.description,event.date AS date, event.time AS time
					FROM  event 
					LEFT JOIN interest_activity
					ON interest_activity.id = event.interest_activity_id  WHERE interest_activity.user_id = ? AND interest_activity.type = 'e' AND TIMESTAMP(date, time) < NOW() 
					 
					UNION 
					SELECT DISTINCT interest_activity.id AS activity_id,interest_activity.interest_id, interest_activity.hash,interest_activity.user_id, event.id AS event_id, event.title, event.description,event.date AS date, event.time AS time
					FROM groups
					LEFT JOIN event_group
					ON groups.id = event_group.group_id 
					LEFT JOIN event
					ON event_group.event_id = event.id
					LEFT JOIN interest_activity
					ON event.interest_activity_id = interest_activity.id WHERE TIMESTAMP(event.date, event.time) < NOW()
		 
				) dum ORDER BY TIMESTAMP(date,time) DESC
				");
			}else{
				$stmt = $this->connection->prepare("
				SELECT * 
				FROM(
					SELECT DISTINCT interest_activity.id AS activity_id,interest_activity.interest_id, interest_activity.hash,interest_activity.user_id, event.id AS event_id, event.title, event.description,event.date AS date, event.time AS time
					FROM  event 
					LEFT JOIN interest_activity
					ON interest_activity.id = event.interest_activity_id  WHERE interest_activity.user_id = ? AND interest_activity.type = 'e'  
					UNION 
					SELECT DISTINCT interest_activity.id AS activity_id,interest_activity.interest_id, interest_activity.hash,interest_activity.user_id, event.id AS event_id, event.title, event.description,event.date AS date, event.time AS time
					FROM groups
					LEFT JOIN event_group
					ON groups.id = event_group.group_id 
					LEFT JOIN event
					ON event_group.event_id = event.id
					LEFT JOIN interest_activity
					ON event.interest_activity_id = interest_activity.id 
				) dum ORDER BY TIMESTAMP(date,time) DESC
				");
			}
			
			if($stmt){
				$stmt->bind_param('i',$user_id);
				if($stmt->execute()){
					 $result = $stmt->get_result();
					 if($result !== false){
						$rows = $result->fetch_all(MYSQLI_ASSOC);
						$stmt->close();
						if($rows !== false){
							$left_content = "";
							$right_content = "";
							$count = 0;
							foreach($rows as $row ){
								$content = $this->getEventInterestActivityBlockByActivityId($row['activity_id']);
								if($count++ % 2 == 0){
									$left_content.= $content;
								}else{
									$right_content.= $content;
								}
							}
							
							$passed_event_num = sizeof($rows);
							// include_once 'User_Upcoming_Event.php';
// 							$upcoming_evt = new User_Upcoming_Event();
// 							$upcoming_event_num = $upcoming_evt->getUpComingEventNumForUser($user_id);
// 							
							$user = new User_Table();
							$firstname = $user->getUserFirstNameByUserIden($user_id);
							$gender_call = $user->getWhatShouldCallForUser($user_id);
							$heOrShe = $gender_call[0];
							$hisOrHer = $gender_call[1];
							$hash = $user->getUniqueIdenForUser($user_id);
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
		
		
		
		
		
		
		
		
		public function loadUpComingEventCollectionForUser($key){
			
			$user = new User_Table();
			$user_id = $user->getUserIdByKey($key);
			include_once 'User_Upcoming_Event.php';
			$upcoming_evt = new User_Upcoming_Event();
			$rows = $upcoming_evt->getUpcomingEventForUser($user_id);
			include_once 'Comment.php';
			$comment = new Comment();
			
			if($rows !== false && sizeof($rows) > 0){
				$left_content = "";
				$right_content = "";
				$count = 0;
				foreach($rows as $row ){
					$content = $this->getEventInterestActivityBlockByActivityId($row['activity_id']);
					if($count++ % 2 == 0){
						$left_content.= $content;
					}else{
						$right_content.= $content;
					}
				}
			}
				
			$upcoming_event_num = ($rows !==false )?sizeof($rows):0;
			$firstname = $user->getUserFirstNameByUserIden($user_id);
			$gender_call = $user->getWhatShouldCallForUser($user_id);
			$heOrShe = $gender_call[0];
			$hisOrHer = $gender_call[1];
			ob_start();
			include(TEMPLATE_PATH_CHILD.'upcoming_event.phtml');
			$content = ob_get_clean();
			return $content;
			

			
		}
		
		
		
		public function deleteActivityForUserByActivityKey($user_id, $key){
			include_once 'Comment.php';
			$comment = new Comment();
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
					$comment->deleteAllCommentsByActivityId($activity_id);
				}
			}
		}
		
		
		public function deleteActivityForUserByActivityId($user_id, $activity_id){
			include_once 'Comment.php';
			$comment = new Comment();
			$type = $this->getColumnById('type', $activity_id);
			if($activity_id !== false){
				if($this->deleteRowForUserById($user_id, $activity_id) !== false){
					if($type == 'm'){
						$this->moment = new Moment($activity_id);
						$this->moment->deleteMomentForUserByActivityId($user_id);
					}else if($type == 'e'){
						$this->event = new Event($activity_id);
						$this->event->deleteEventForUserByActivityId($user_id);
					}
					$comment->deleteAllCommentsByActivityId($activity_id);
				}
			}
		}
		
		
		
		
		
		
		
		public function deleteAllActivityForUserByInterestId($user_id, $interest_id){
			$rows = $this->getAllRowsColumnBySelectorForUser('id','interest_id',$interest_id,$user_id);
			if($rows !== false){
				foreach($rows as $row){
					$this->deleteActivityForUserByActivityId($user_id, $row['id']);
				}	
			}
		}
		
		
		public function getInterestIdByActivityId($activity_id){
			return $this->getColumnById('interest_id', $activity_id);
		}
		
		
		
		public function getPostTextByActivityId($activity_id){
			$type = $this->getColumnById('type',$activity_id);
			if($type == 'm'){
				$this->moment = new Moment($activity_id);
				return $this->moment->getPostText();
			}else if($type == 'e'){
				$this->event = new Event($activity_id);
				return $this->event->getPostText();
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
		
		
		public function returnMatchedEventBySearchkeyWord($key_word){
			$stmt = $this->connection->prepare("
			SELECT event.title, event.description, event.location, event.date, event.time,interest_activity.id AS activity_id, interest_activity.user_id, interest_activity.post_time,interest_activity.hash
			FROM event 
			LEFT JOIN interest_activity
			ON event.interest_activity_id = interest_activity.id  WHERE interest_activity.type = 'e'  AND  (event.title LIKE ? || event.description LIKE ? || event.location LIKE ?) ORDER BY TIMESTAMP(event.date, event.time) DESC
			");			
			if($stmt){
				$key_word = '%' .$key_word. '%';
				$stmt->bind_param('sss',$key_word,$key_word,$key_word);
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
	
		
		
		
		public function returnMatchedPostBySearchkeyWord($key_word){
			//select based on interest name
			$stmt = $this->connection->prepare("
			SELECT * 
			FROM(
				SELECT moment.id, moment.description, moment.date, interest_activity.id AS activity_id, interest_activity.user_id, interest_activity.post_time,interest_activity.hash
				FROM interest 
				LEFT JOIN interest_activity
				ON interest.id = interest_activity.interest_id 
				LEFT JOIN moment
				ON moment.interest_activity_id = interest_activity.id 
				WHERE interest.name Like ? AND interest_activity.type = 'm' 
				UNION
				SELECT  moment.id, moment.description, moment.date, interest_activity.id AS activity_id, interest_activity.user_id, interest_activity.post_time,interest_activity.hash
				FROM moment 
				LEFT JOIN interest_activity
				ON moment.interest_activity_id = interest_activity.id  WHERE interest_activity.type = 'm' And   ( moment.description LIKE ? ) 
			) dum ORDER BY activity_id DESC
			");	
			if($stmt){
				$key_word = '%' .$key_word. '%';
				$stmt->bind_param('ss',$key_word, $key_word);
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
		
		
		public function returnMatchedPhotoBySearchkeyWord($key_word){
			$stmt = $this->connection->prepare("
			SELECT *
			FROM
			(
				SELECT 'm' AS `source_from`,  interest_activity.id as interest_activity_id, moment_photo.picture_url,moment_photo.user_id,  moment_photo.hash 
				FROM interest 
				LEFT JOIN interest_activity
				ON interest.id = interest_activity.interest_id 
				LEFT JOIN moment
				ON interest_activity.id = moment.interest_activity_id 
				LEFT JOIN moment_photo
				ON moment.id = moment_photo.moment_id 
				WHERE interest.name Like ?
			
				UNION 
			
				SELECT  'e' AS `source_from`, interest_activity.id as interest_activity_id,  event_photo.picture_url,event_photo.user_id, event_photo.hash
				FROM interest 
				LEFT JOIN interest_activity
				ON interest.id = interest_activity.interest_id 
				LEFT JOIN event
				ON interest_activity.id = event.interest_activity_id 
				LEFT JOIN event_photo
				ON event.id = event_photo.event_id
				WHERE interest.name Like ?
				
				UNION 
				
				SELECT 'm' AS `source_from`,  interest_activity.id as interest_activity_id, moment_photo.picture_url,moment_photo.user_id,  moment_photo.hash 
				FROM moment 
				LEFT JOIN interest_activity
				ON moment.interest_activity_id = interest_activity.id
				LEFT JOIN moment_photo
				ON moment.id = moment_photo.moment_id  WHERE  (moment.description LIKE ? || moment_photo.caption LIKE ?)
			
				UNION 
				SELECT  'e' AS `source_from`, interest_activity.id as interest_activity_id,  event_photo.picture_url,event_photo.user_id, event_photo.hash
 				FROM event 
 				LEFT JOIN interest_activity
				ON event.interest_activity_id = interest_activity.id
 				LEFT JOIN event_photo
 				ON event.id = event_photo.event_id  WHERE  (event.title LIKE  ?|| event.description LIKE ? || event_photo.caption LIKE ?)

				)dum ORDER BY interest_activity_id DESC
			
			");	

			if($stmt){
				$key_word = '%' .$key_word. '%';
				$stmt->bind_param('sssssss',$key_word,$key_word, $key_word, $key_word,$key_word, $key_word, $key_word);
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
		
		
		public function returnMatchedEventForMineInterest(){
			include_once MODEL_PATH.'Interest.php';
			$interest = new Interest();
			$mine_interests = $interest->getInterestNameForUser($_SESSION['id'], -1);
			$interest_like = '';
			if($mine_interests !== false){
				foreach($mine_interests as $interest){
 					$interest_like .= $interest['name'].'|';	
				}
				$interest_like = trim($interest_like,'|');
				$result_rows = false;
				$rowFromUpcoming = $this->returnUpcomingMatchedEventForMinInterest($interest_like);
				if($rowFromUpcoming !== false){
					$result_rows = $rowFromUpcoming;
				}
				
				$rowFromPassed = $this->returnPassedMatchedEventForMinInterest($interest_like);
				if($rowFromPassed !== false){
					if($result_rows !== false){
						$result_rows = array_merge($result_rows, $rowFromPassed);
					}else{
						$result_rows = $rowFromPassed;
					}
				}
				return $result_rows;
				//use random offset to get random user
				
			}
			echo $this->connection->error;
			return false;
		}
		
		
		public function returnUpcomingMatchedEventForMinInterest($interest_like){
				$stmt = $this->connection->prepare("
				SELECT * 
				FROM
				(
					SELECT event.title, event.description, event.location, event.date AS date, event.time AS time,interest_activity.id AS activity_id, interest_activity.user_id, interest_activity.post_time,interest_activity.hash
					FROM interest 
					LEFT JOIN interest_activity
					ON interest.id = interest_activity.interest_id 
					LEFT JOIN event
					ON event.interest_activity_id = interest_activity.id 
					WHERE TIMESTAMP(date, time) > NOW() AND interest.name REGEXP ? AND interest_activity.type = 'e' 
			
					UNION
			
					SELECT event.title, event.description, event.location, event.date AS date, event.time AS time,interest_activity.id AS activity_id, interest_activity.user_id, interest_activity.post_time,interest_activity.hash
					FROM event 
					LEFT JOIN interest_activity
					ON   event.interest_activity_id = interest_activity.id  WHERE TIMESTAMP(date, time) > NOW() AND interest_activity.type = 'e'  AND  (event.title REGEXP ?  ||  event.description  REGEXP ? || event.location REGEXP ?)
				
					
				) dum ORDER BY TIMESTAMP(date, time) ASC
				");	
				
				if($stmt){
					$stmt->bind_param('ssss',$interest_like,$interest_like,$interest_like, $interest_like);
					if($stmt->execute()){
						 $result = $stmt->get_result();
						 if($result !== false && $result->num_rows >= 1){
							$row = $result->fetch_all(MYSQLI_ASSOC);
							$stmt->close();
							return $row;
						 }
					}
				}
		}
		
		public function returnPassedMatchedEventForMinInterest($interest_like){
				$stmt = $this->connection->prepare("
				SELECT * 
				FROM
				(
					SELECT event.title, event.description, event.location, event.date AS date, event.time AS time,interest_activity.id AS activity_id, interest_activity.user_id, interest_activity.post_time,interest_activity.hash
					FROM interest 
					LEFT JOIN interest_activity
					ON interest.id = interest_activity.interest_id 
					LEFT JOIN event
					ON event.interest_activity_id = interest_activity.id 
					WHERE interest.name REGEXP ? AND interest_activity.type = 'e' AND TIMESTAMP(date, time) <= NOW()
			
					UNION
			
					SELECT event.title, event.description, event.location, event.date AS date, event.time AS time,interest_activity.id AS activity_id, interest_activity.user_id, interest_activity.post_time,interest_activity.hash
					FROM event 
					LEFT JOIN interest_activity
					ON   event.interest_activity_id = interest_activity.id  WHERE  interest_activity.type = 'e'  AND  (event.title REGEXP ?  ||  event.description  REGEXP ? || event.location REGEXP ?) AND TIMESTAMP(date, time) <= NOW() 
		
					
				) dum ORDER BY TIMESTAMP(date, time) DESC
				");	
				
				if($stmt){
					$stmt->bind_param('ssss',$interest_like,$interest_like,$interest_like, $interest_like);
					if($stmt->execute()){
						 $result = $stmt->get_result();
						 if($result !== false && $result->num_rows >= 1){
							$row = $result->fetch_all(MYSQLI_ASSOC);
							$stmt->close();
							return $row;
						 }
					}
				}
		}
		
		
		
		
		
		
		
		public function returnMatchedMomentForMineInterest(){
			include_once MODEL_PATH.'Interest.php';
			$interest = new Interest();
			$mine_interests = $interest->getInterestNameForUser($_SESSION['id'], -1);
			$interest_like = '';
			if($mine_interests !== false){
				foreach($mine_interests as $interest){
 					$interest_like .= $interest['name'].'|';	
				}
				$interest_like = trim($interest_like,'|');
				
				//use random offset to get random user
				$stmt = $this->connection->prepare("
			SELECT * 	
			FROM
			(
				SELECT moment.id, moment.description, moment.date, interest_activity.id AS activity_id, interest_activity.user_id, interest_activity.post_time,interest_activity.hash
				FROM interest 
				LEFT JOIN interest_activity
				ON interest.id = interest_activity.interest_id 
				LEFT JOIN moment
				ON moment.interest_activity_id = interest_activity.id 
				WHERE interest.name REGEXP ? AND interest_activity.type = 'm' 
			
				UNION
			
				SELECT moment.id, moment.description, moment.date, interest_activity.id AS activity_id, interest_activity.user_id, interest_activity.post_time,interest_activity.hash
				FROM moment 
				LEFT JOIN interest_activity
				ON moment.interest_activity_id = interest_activity.id  WHERE interest_activity.type = 'm' And   ( moment.description REGEXP ? ) 
			) dum ORDER BY activity_id DESC
				");	
				
				
				if($stmt){
					$stmt->bind_param('ss',$interest_like, $interest_like);
					if($stmt->execute()){
						 $result = $stmt->get_result();
						 if($result !== false && $result->num_rows >= 1){
							$row = $result->fetch_all(MYSQLI_ASSOC);
							$stmt->close();
							return $row;
						 }
					}
				}
			}
			echo $this->connection->error;
			return false;
		}
		
		
		public function joinEventForUser($user_id, $activity_key){
			$activity_id = $this->getRowIdByHashkey($activity_key);
			if($activity_id !== false){
				$this->event = new Event($activity_id);
				if(!$this->event->isEventPassedForActivityId($activity_id)){
					include_once 'Event_Group.php';
					$post_user = $this->getEventPostUserByActivityId($activity_id);
					$e_g = new Event_Group();
					$e_g->joinEventForUser($user_id, $this->event->event_id, $post_user);
					
				}
			}	
			return false;
		}
		
		public function getEventPostUserByActivityId($activity_id){
			return $this->getColumnById('user_id', $activity_id);
		}
		
		public function unjoinEventForUser($user_id, $activity_key){
			$activity_id = $this->getRowIdByHashkey($activity_key);
			if($activity_id !== false){
				$this->event = new Event($activity_id);
				include_once 'Event_Group.php';
				$post_user = $this->getEventPostUserByActivityId($activity_id);
				$e_g = new Event_Group();
				$e_g->unjoinEventForUser($user_id, $this->event->event_id, $post_user);
				return true;
			}	
			return false;
		}
			
		
			
		
	}
?>