<?php
	include_once 'core_table.php';
	include_once 'User_Table.php';
	include_once 'User_Media_Prefix.php';
	include_once 'Moment.php';
	include_once 'Event.php';
	include_once  MODEL_PATH.'Favor_Activity.php';
	
	class Interest_Activity extends Core_Table{
		private  $table_name = "interest_activity";
		private $moment = null;
		private $event = null;
		private $feed_id_list = '-1'; //activity_id in the user's feed
		public function __construct(){
			parent::__construct($this->table_name);
		}
		
		
		public function addEventInterestActivityForUserByInterestId($user_id,$interest_id, $title,$description,$location, $date, $evt_time, $attached_picture, $caption, $with_interest_name){
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
					return $this->getEventInterestActivityBlockByActivityId($interest_activity_id, $with_interest_name);
				}
			}
			return false;
		}
		
		
		
		public function getInitialPageFeed(){
			include_once 'User_In_Interest.php';
			$in = new User_In_Interest();
			$friends = $in->getAllFriendsInUsersInterest();
			$left_content = "";
			$left_content = $this->getIndexAvator();						
			$right_content = "";
			
			include_once 'Interest.php';
			$interest  = new Interest();
			if($interest->isUserHasInterest($_SESSION['id'])){
				$right_content = $this->getRecentPostPreview();			
			}else{
				$content = $interest->getAddNewInterestBlock();
				ob_start();
				include(TEMPLATE_PATH_CHILD.'index_post_wrapper.phtml');
				$right_content = ob_get_clean();			
			}
			
			$user_in = '';
			if($friends !== false && sizeof($friends >= 1 )){
				foreach($friends as $friend){
					$user_in.="'".$friend['user_id']."',";
				}
			}
			$user_in = $user_in.$_SESSION['id'];
			$stmt = $this->connection->prepare("SELECT `id`,`type` FROM `$this->table_name` WHERE `user_id` IN ($user_in) ORDER BY `id` DESC");			
			if($stmt){
				if($stmt->execute()){
					 $result = $stmt->get_result();
					 if($result !== false && $result->num_rows >= 1){
						$rows = $result->fetch_all(MYSQLI_ASSOC);
						$stmt->close();
						$count = 1;
						$feed_id_list = '';
						foreach($rows as $row){
							$content = '';
							$feed_id_list .= $row['id'].',';
							if($row['type'] == 'm'){
								$content = $this->getMomentInterestActivityBlockByActivityId($row['id'], true);
							}else if($row['type'] == 'e'){
								$content = $this->getEventInterestActivityBlockByActivityId($row['id'], true);
							}
							if($count++ % 2 == 0){
								$left_content.= $content;
							}else{
								$right_content.= $content;
							}
						}
						$this->feed_id_list = trim($feed_id_list,',');
					 }
				}
			}
			
			$suggest_content = $this->getSuggestPost();
			if($suggest_content !== false){
				$left_content .= $suggest_content['suggest_left_content'];
				$right_content .= $suggest_content['suggest_right_content'];
			}

			ob_start();
			include(TEMPLATE_PATH_CHILD.'index_new_feed.phtml');
			$content = ob_get_clean();
			return $content;
		}
		
		
		
		public function getIndexAvator(){
			include_once 'Interest.php';
			$interest  = new Interest();
			include_once 'User_Profile_Picture.php';
			$profile = new User_Profile_Picture();
			include_once 'User_Table.php';
			$user = new User_Table();
			include_once 'Education.php';
			$educ = new Education();
			
			$profile_pic = $profile->getLatestProfileImageForUser($_SESSION['id']);
			$cover_pic =  $user->getLatestCoverForuser($_SESSION['id']);
			$fullname = $user->getUserFullnameByUserIden($_SESSION['id']);
			$rows = $interest->getInterestNameForUser($_SESSION['id'], 2);
			$user_page_redirect =  USER_PROFILE_ROOT.$user->getUserAccessUrl($_SESSION['id']);
			$interest_list = '';
			if($rows !== false){
				$count = 1;
				foreach($rows as $row){
					if($count == sizeof($rows) -1 ){
						$interest_list .= $row['name'].' and ';
					}else if($count < sizeof($rows)){
						$interest_list .= $row['name'].', ';
					}else{
						$interest_list .= $row['name'];
					}
					$count++;
				}
			}
			$interest_list = trim($interest_list,', ');
			$education = $educ->getEducationByUserId($_SESSION['id']);
			
			
			ob_start();
			include(TEMPLATE_PATH_CHILD.'index_avator.phtml');
			$content= ob_get_clean();
			return $content;
 		
	}
		
		
		public function getRecentPostPreview(){
			$stmt = $this->connection->prepare("SELECT `id`,`interest_id`,`type` FROM `$this->table_name` WHERE `user_id` = ? ORDER BY `id` DESC LIMIT 1");			
			if($stmt){
				$stmt->bind_param('i',$_SESSION['id']);
				if($stmt->execute()){
					 $result = $stmt->get_result();
					 if($result !== false && $result->num_rows == 1){
						$row = $result->fetch_assoc();
						$content =  $this->getRecentPostBlockByInterestId($row['interest_id'], $row['id'], $row['type']);
						
					}else{
						include_once 'Interest.php';
						$interest = new Interest();
						$content =  $interest->getIndexInterestPreviewBlock($_SESSION['id']);
					}
					ob_start();
					include(TEMPLATE_PATH_CHILD.'index_post_wrapper.phtml');
					$content = ob_get_clean();
					return $content;
				}
			}
			
			
			return false;
		}
		
		public function renderRecentByActivityIdAndType($activity_id, $type){
			$body = false;
			if($type == 'm'){
				$body =  $this->loadLatestMomentPostByActivityId($activity_id);
			}else if($type == 'e'){
				$body =  $this->loadLatestEventPostByActivityId($activity_id);
			}
			return $body;
		}
		
		
		/* if $activity_id is not equal to false, then type must not be false*/
		public function getRecentPostBlockByInterestId($interest_id, $activity_id = false, $type = false){
			include_once MODEL_PATH.'Interest.php';
			$interest = new Interest();
			if($interest->isInterestEditableByUser($interest_id, $_SESSION['id'])){
				if($activity_id !== false){
					$body = $this->renderRecentByActivityIdAndType($activity_id, $type);
				}else{
					$stmt = $this->connection->prepare("SELECT `id`,`type` FROM `$this->table_name` WHERE `user_id` = ? AND `interest_id` = ? ORDER BY `id` DESC LIMIT 1");			
					if($stmt){
						$stmt->bind_param('ii',$_SESSION['id'], $interest_id);
						if($stmt->execute()){
							 $result = $stmt->get_result();
							 if($result !== false && $result->num_rows == 1){
								$row = $result->fetch_assoc();
								$body = $this->renderRecentByActivityIdAndType($row['id'], $row['type']);
							}
						}
					}
				}
				$interest_name = $interest->getInterestNameByInterestId($interest_id);
				ob_start();
				include(TEMPLATE_PATH_CHILD.'index_recent_post_preview_block.phtml');
				$content = ob_get_clean();
				return $content;
			}
			return false;
		}
		
		
		
		public function getEventInterestActivityBlockByActivityId($activity_id, $with_interest_name = false){
			$column_array = array('user_id','interest_id','post_time','hash');
			$interest_activity = $this->getMultipleColumnsById($column_array, $activity_id);
			if($interest_activity !== false){
				/* get data based on self table columns*/
				include_once 'User_Profile_Picture.php';
				$profile = new User_Profile_Picture();
				$post_owner_pic = $profile->getLatestProfileImageForUser($interest_activity['user_id']);
				$user = new User_Table();
				$fullname = $user->getUserFullnameByUserIden($interest_activity['user_id']);
				
				if($with_interest_name){
					include_once MODEL_PATH.'Interest.php';
					$interest = new Interest();
					$interest_name = $interest->getInterestNameByInterestId($interest_activity['interest_id']);
					$access_url = $user->getUserAccessUrl($interest_activity['user_id']);
					$interest_access_url = USER_PROFILE_ROOT.$access_url.'/interests/'.strtolower($interest_name);
					$post_time = convertDateTimeToAgo($interest_activity['post_time'], false);	
				}else{
					$post_time = convertDateTimeToAgo($interest_activity['post_time'], false);	
				}
				$user_page_redirect =  USER_PROFILE_ROOT.$user->getUserAccessUrl($interest_activity['user_id']);
				$unique_iden = $user->getUniqueIdenForUser($interest_activity['user_id']);

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
				//$event_photo_num = $this->event->event_photo->getPhotoNumberForEvent($event['id']);
				
				$media_prefix = $prefix->getUserMediaPrefix($interest_activity['user_id']).'/';
				if($event_photo !== false && isMediaDisplayable($media_prefix.$event_photo['picture_url'])){
					$event_photo_url = U_IMGDIR.$media_prefix.$event_photo['picture_url'];
					$event_photo_hash = $event_photo['hash'];
				}
				
				
				include_once 'Comment.php';
				$comment = new Comment();
				$comment_number = $comment->getCommentNumberForTarget($activity_id);
				
				$group_key = false;
				include_once 'Event_Group.php';
				$e_group = new Event_Group();
				$group_id = $e_group->getGroupIdByEventId($event['id']);
				if($group_id !== false){
					include_once 'Groups.php';
					$group = new Groups();
					if($group->isUserInGroupByGroupId($_SESSION['id'], $group_id)){
						$group_key = $group->getGroupKeyByGroupId($group_id);
					}
				}
				
				$operation_dispable = ($group_key !== false || $interest_activity['user_id'] == $_SESSION['id']);
				
				ob_start();
				include(SCRIPT_INCLUDE_BASE.'phtml/child/post_event_block.phtml');
				$event_block = ob_get_clean();
				return $event_block;
			}
			return false;
		}	
		
		
		
		public function getEventCoverForEventByPostKey($key){
			$activity = $this->getMultipleColumnsBySelector(array('id','user_id'),'hash',$key);
			if($activity !== false){
				$this->event = new event($activity['id']);
				$event_photo = $this->event->event_photo->getEventPhotoResourceByMomentId($this->event->event_id);
				include_once 'User_Media_Prefix.php';
				$prefix = new User_Media_Prefix();
				$media_prefix = $prefix->getUserMediaPrefix($activity['user_id']).'/';
				if($event_photo !== false && isMediaDisplayable($media_prefix.$event_photo['picture_url'])){
					$event_photo_url = U_IMGDIR.$media_prefix.$event_photo['picture_url'];
					$event_photo_hash = $event_photo['hash'];
					ob_start();
					include(TEMPLATE_PATH_CHILD.'event_cover.phtml');
					$cover = ob_get_clean();
					return $cover;
				}
			}
			return false;
		}
		
		
		
		public function addMomentInterestActivityForUserByInterestId($user_id,$interest_id, $description, $date, $attached_picture, $caption, $with_interest_name = false){
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
					return $this->getMomentInterestActivityBlockByActivityId($interest_activity_id, $with_interest_name);
				}
			}
			return false;
		}
		
		
		public function getMomentInterestActivityBlockByActivityId($activity_id, $with_interest_name = false){
			$column_array = array('user_id','type','interest_id','post_time','hash');
			$interest_activity = $this->getMultipleColumnsById($column_array, $activity_id);
			if($interest_activity !== false){
				if($interest_activity['type'] == 'm'){
					/* get data based on self table columns*/
					include_once 'User_Profile_Picture.php';
					$profile = new User_Profile_Picture();
					$post_owner_pic = $profile->getLatestProfileImageForUser($interest_activity['user_id']);
					$user = new User_Table();
					
					$fullname = $user->getUserFullnameByUserIden($interest_activity['user_id']);
					if($with_interest_name){
						include_once MODEL_PATH.'Interest.php';
						$interest = new Interest();
						$interest_name = $interest->getInterestNameByInterestId($interest_activity['interest_id']);
						$access_url = $user->getUserAccessUrl($interest_activity['user_id']);
						$interest_access_url = USER_PROFILE_ROOT.$access_url.'/interests/'.strtolower($interest_name);
						$post_time = convertDateTimeToAgo($interest_activity['post_time'], false);
					}else{
						$post_time = convertDateTimeToAgo($interest_activity['post_time'], false);	
					}
					
					$user_page_redirect =  USER_PROFILE_ROOT.$user->getUserAccessUrl($interest_activity['user_id']);
					$unique_iden = $user->getUniqueIdenForUser($interest_activity['user_id']);
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
					
					$favor = new Favor_Activity();
					$favor_number = $favor->getFavorNum($activity_id);
					$favored = $favor->isSessionUserAlreadyFavor($activity_id);
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
		
		
		public function loadLatestMomentPostByActivityId($activity_id){
			$this->moment = new Moment($activity_id);
			$moment = $this->moment->loadMomentResource();
			$date = returnShortDate($moment['date']);
			$description = $moment['description'];
			include_once 'User_Media_Prefix.php';
			$prefix = new User_Media_Prefix();
			$photo = $this->moment->moment_photo->getMomentPhotoResourceByMomentId($moment['id']);
			$photo_url = false;
			if($photo !== false){
				$photo_url = $prefix->getUserMediaPrefix($_SESSION['id']).'/'.$photo['picture_url'];
			}
			ob_start();
			include(TEMPLATE_PATH_CHILD.'latest_post_preview_body.phtml');
			$content = ob_get_clean();
			return $content;
		}	
		
		
		public function loadLatestEventPostByActivityId($activity_id){
			$this->event = new Event($activity_id);
			$event = $this->event->loadEventResource();
			if($event['date'] !== null){
				$date = returnShortDate($event['date']);
			}else{
				$date = 'Not Specified';
			}
			
			$description = $event['title'];
			include_once 'User_Media_Prefix.php';
			$prefix = new User_Media_Prefix();
			
			$photo_url = $this->event->event_photo->getEventPhotoUrlByEventId($event['id']);
			if($photo_url !== false){
				$photo_url = $prefix->getUserMediaPrefix($_SESSION['id']).'/'.$photo_url;
			}
			ob_start();
			include(TEMPLATE_PATH_CHILD.'latest_post_preview_body.phtml');
			$content = ob_get_clean();
			return $content;
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
					ON event.interest_activity_id = interest_activity.id WHERE groups.user_in LIKE ? AND  TIMESTAMP(event.date, event.time) < NOW()
		 
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
					ON event.interest_activity_id = interest_activity.id WHERE groups.user_in LIKE ?
				) dum ORDER BY TIMESTAMP(date,time) DESC
				");
			}
			
			if($stmt){
				$user_in = '%'.$user_id.',%';
				$stmt->bind_param('is',$user_id, $user_in);
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
			$favor = new Favor_Activity();
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
					$favor->deleteAllFavorForTarget($activity_id);
				}
			}
		}
		
		
		public function deleteActivityForUserByActivityId($user_id, $activity_id){
			include_once 'Comment.php';
			$comment = new Comment();
			$favor = new Favor_Activity();
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
					$favor->deleteAllFavorForTarget($activity_id);
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
		
		
		
		public function getNotificationPostDetailByActivityId($activity_id){
			$type = $this->getColumnById('type',$activity_id);
			if($type == 'm'){
				$this->moment = new Moment($activity_id);
				$text =  $this->moment->getPostText();
				$from = 'moment';
			}else if($type == 'e'){
				$this->event = new Event($activity_id);
				$text = $this->event->getPostTitle();
				$from = 'event';
			}
			if($type !== false && $text !== false){
				return array('from'=>$from, 'text'=>$text);
			}
			return false;
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
		
		
	
		
		/* $user_id is the user who is uploading the photo*/
		public function uploadEvtPhotoByKey($key, $user_id, $photo_file){
			$activity_id  = $this->getRowIdByHashkey($key);
			if($activity_id !== false){
				$this->event = new Event($activity_id);
				if($this->event->isEvtPhotoUploadableByUserForEvent($user_id, $this->event->event_id)){
					//upload the photo
					$event_id = $this->event->event_id;
					return $this->event->event_photo->uploadEventPhotoByEventId($photo_file, $user_id, $event_id);
				}
			}
			return false;
		}
		
		
		public function returnMatchedEventBySearchkeyWord($key_word){
			$stmt = $this->connection->prepare("
			SELECT *
			FROM (
				SELECT interest_activity.id AS activity_id
				FROM event 
				LEFT JOIN interest_activity
				ON event.interest_activity_id = interest_activity.id  WHERE interest_activity.type = 'e'  AND  (event.title LIKE ? || event.description LIKE ? || event.location LIKE ?) 
			
				UNION 
			
				SELECT interest_activity.id AS activity_id
				FROM interest
				LEFT JOIN interest_activity
				ON interest.id = interest_activity.interest_id
				LEFT JOIN event
				ON interest_activity.id = event.interest_activity_id
				WHERE (interest.name LIKE ? || interest.description LIKE ?) AND interest_activity.type = 'e' 
			) dum ORDER BY activity_id DESC
			");			
			if($stmt){
				$key_word = '%' .$key_word. '%';
				$stmt->bind_param('sssss',$key_word,$key_word,$key_word, $key_word, $key_word);
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
				SELECT interest_activity.id AS activity_id
				FROM interest 
				LEFT JOIN interest_activity
				ON interest.id = interest_activity.interest_id 
				LEFT JOIN moment
				ON moment.interest_activity_id = interest_activity.id 
				WHERE interest.name Like ? AND interest_activity.type = 'm' 
				
				UNION
				
				SELECT  interest_activity.id AS activity_id
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
					SELECT interest_activity.id AS activity_id, event.date AS date, event.time AS time
					FROM interest 
					LEFT JOIN interest_activity
					ON interest.id = interest_activity.interest_id 
					LEFT JOIN event
					ON event.interest_activity_id = interest_activity.id 
					WHERE TIMESTAMP(date, time) > NOW() AND interest.name REGEXP ? AND interest_activity.type = 'e' 
			
					UNION
			
					SELECT interest_activity.id AS activity_id,  event.date AS date, event.time AS time
					FROM event 
					LEFT JOIN interest_activity
					ON   event.interest_activity_id = interest_activity.id  WHERE TIMESTAMP(date, time) > NOW() AND interest_activity.type = 'e'  AND  (event.title REGEXP ?  ||  event.description  REGEXP ? || event.location REGEXP ?)
				
				) dum ORDER BY TIMESTAMP(date, time) ASC
				");	
				
				if($stmt){
					//$stmt->bind_param('ssss',$interest_like,$interest_like,$interest_like, $interest_like);
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
				return false;
		}
		
		public function returnPassedMatchedEventForMinInterest($interest_like){
				$stmt = $this->connection->prepare("
				SELECT * 
				FROM
				(
					SELECT interest_activity.id AS activity_id, event.date AS date, event.time AS time
					FROM interest 
					LEFT JOIN interest_activity
					ON interest.id = interest_activity.interest_id 
					LEFT JOIN event
					ON event.interest_activity_id = interest_activity.id 
					WHERE interest.name REGEXP ? AND interest_activity.type = 'e' AND TIMESTAMP(date, time) <= NOW()
			
					UNION
			
					SELECT interest_activity.id AS activity_id, event.date AS date, event.time AS time
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
				SELECT interest_activity.id AS activity_id
				FROM interest 
				LEFT JOIN interest_activity
				ON interest.id = interest_activity.interest_id 
				LEFT JOIN moment
				ON moment.interest_activity_id = interest_activity.id 
				WHERE interest.name REGEXP ? AND interest_activity.type = 'm' 
			
				UNION
			
				SELECT interest_activity.id AS activity_id
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
		
		
		public function getActivityIdByKey($key){
			return $this->getRowIdByHashkey($key);
		}
		
		
		public function getPostUserByActivityId($activity_id){
			return $this->getColumnById('user_id', $activity_id);
		}
		
		public function getPostUserByActivityKey($activity_key){
			return $this->getColumnBySelector('user_id', 'hash', $activity_key);
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
		
		
		public function getJoinedMemberBlockByActivityKey($key){
			$activity_id = $this->getRowIdByHashkey($key);
			if($activity_id !== false){
				$this->event = new Event($activity_id);
				if($this->event->event_id !== false){
					return $this->event->getJoinedMemberByEventId($this->event->event_id);
				}
			}
			return false;
		}
		
		public function getFavorMemberBlockByActivityKey($key){
			$activity_id = $this->getRowIdByHashkey($key);
			if($activity_id !== false){
				include_once 'User_Profile_Picture.php';
				$profile = new User_Profile_Picture();
				include_once 'User_Table.php';
				$user = new User_Table();
				$favor = new Favor_Activity();
				$user_favor = $favor->getFavorListForTarget($activity_id); //22,28,29,
				$content = '';
				if($user_favor !== false){
					$users = explode(',',trim($user_favor, ','));
					foreach($users as $u){
						$profile_pic = $profile->getLatestProfileImageForUser($u);
						$firstname = $user->getUserFirstNameByUserIden($u);
						$user_page_redirect =  USER_PROFILE_ROOT.$user->getUserAccessUrl($u);
						$hash = $user->getUniqueIdenForUser($u);
						ob_start();
						include(TEMPLATE_PATH_CHILD.'list_item.phtml');
						$content .= ob_get_clean();
					}
					return $content;
				}
			}
			return false;
		}
		
		
		
		public function favorActivity($key){
			$activity_id = $this->getRowIdByHashkey($key);
			if($activity_id !== false){
				$favor = new Favor_Activity();
				$user_id_get = $this->getPostUserByActivityId($activity_id);
				$favor->addFavor($activity_id, $_SESSION['id'], $user_id_get);
			}
		}
		
		public function undoFavorActivity($key){
			$activity_id = $this->getRowIdByHashkey($key);
			if($activity_id !== false){
				$favor = new Favor_Activity();
				$favor->undoFavorForSessionUser($activity_id);
			}
		}
		
		public function getFavorNumForActivity($key){
			$activity_id = $this->getRowIdByHashkey($key);
			if($activity_id !== false){
				$favor = new Favor_Activity();
				return $favor->getTotalFavorNumForTarget($activity_id);
			}
		}
		
		
		
		
		
		public function getSuggestPost(){
			include_once MODEL_PATH.'Interest.php';
			$interest = new Interest();
			$mine_interests = $interest->getInterestNameForUser($_SESSION['id'], -1);
			$interest_like = '';
			$suggest_feed  = false;
			if($mine_interests !== false){
				foreach($mine_interests as $interest){
 					$interest_like .= $interest['name'].'|';	
				}
				$interest_like = trim($interest_like,'|');
			}	
			include_once 'Education.php';
			$edu = new Education();
			$school_id =  $edu->getSchoolIdByUserId($_SESSION['id']);
			$school_id = ($school_id === false)?-1:$school_id;
			
			//use random offset to get random user
			$result_from_interest_and_school = $this->returnSimilarInterestPostInSameCampus($interest_like, $school_id);
			$result_from_interest_and_school = ($result_from_interest_and_school !== false)?$result_from_interest_and_school:array();
			$result_from_interest = $this->returnSimilarInterestPost($interest_like);
			$result_from_interest = ($result_from_interest !== false)?$result_from_interest:array();
			$result_from_school = $this->returnPostFromSameSchool($school_id);
			$result_from_school = ($result_from_school !== false)?$result_from_school:array();
			$rows = array_merge($result_from_interest_and_school,$result_from_interest, $result_from_school);
			if(sizeof($rows) >= 1 ){
				$count = 0;
				$left_content = "";
				$right_content = "";
				foreach($rows as $row){
					$content = '';
					if($row['type'] == 'm'){
						$content = $this->getMomentInterestActivityBlockByActivityId($row['activity_id'], true);
					}else if($row['type'] == 'e'){
						$content = $this->getEventInterestActivityBlockByActivityId($row['activity_id'], true);
					}
					if($count++ % 2 == 0){
						$left_content.= $content;
					}else{
						$right_content.= $content;
					}
				}
			
				return array("suggest_left_content"=>$left_content,"suggest_right_content"=>$right_content );
			}
			return false;
		
		}
		
		
		public function returnSimilarInterestPostInSameCampus($interest_like = false, $school_id = false){
			//there is no interest_like passed as parameter
			if($interest_like === false ){
				include_once MODEL_PATH.'Interest.php';
				$interest = new Interest();
				$mine_interests = $interest->getInterestNameForUser($_SESSION['id'], -1);
				$interest_like = '';
				if($mine_interests !== false){
					foreach($mine_interests as $interest){
						$interest_like .= $interest['name'].'|';	
					}
					$interest_like = trim($interest_like,'|');
				}else{
					return false;
				}
			}
			if($school_id === false){
				//there is no school_id passed as parameter
				include_once 'Education.php';
				$edu = new Education();
				$school_id =  $edu->getSchoolIdByUserId($_SESSION['id']);
				if($school_id === false){
					return false;
				}
			}
			
			if($interest_like != ''){
				$stmt = $this->connection->prepare("
				SELECT * 	
				FROM
				(
					SELECT  interest_activity.id AS activity_id, interest_activity.type
					FROM interest 
					LEFT JOIN interest_activity
					ON interest.id = interest_activity.interest_id 
					LEFT JOIN moment
					ON moment.interest_activity_id = interest_activity.id 
					LEFT JOIN education
					ON interest_activity.user_id = education.user_id
					WHERE interest_activity.id NOT IN($this->feed_id_list) AND interest.name REGEXP ? AND interest_activity.type = 'm' AND education.school_id = '$school_id'
			
					UNION
			
					SELECT interest_activity.id AS activity_id, interest_activity.type
					FROM moment 
					LEFT JOIN interest_activity
					ON moment.interest_activity_id = interest_activity.id
					LEFT JOIN education
					ON interest_activity.user_id = education.user_id
					WHERE interest_activity.id NOT IN($this->feed_id_list) AND interest_activity.type = 'm' AND   ( moment.description REGEXP ? ) AND education.school_id = '$school_id'
			
					UNION 
				
					SELECT  interest_activity.id AS activity_id, interest_activity.type
					FROM interest 
					LEFT JOIN interest_activity
					ON interest.id = interest_activity.interest_id 
					LEFT JOIN event
					ON event.interest_activity_id = interest_activity.id 
					LEFT JOIN education
					ON interest_activity.user_id = education.user_id
					WHERE interest_activity.id NOT IN($this->feed_id_list) AND interest.name REGEXP ? AND interest_activity.type = 'e' AND education.school_id = '$school_id'
				
					UNION
				
					SELECT interest_activity.id AS activity_id, interest_activity.type
					FROM event 
					LEFT JOIN interest_activity
					ON   event.interest_activity_id = interest_activity.id
					LEFT JOIN education
					ON interest_activity.user_id = education.user_id
					WHERE  interest_activity.id NOT IN($this->feed_id_list) AND interest_activity.type = 'e'  AND  (event.title REGEXP ?  ||  event.description  REGEXP ? || event.location REGEXP ?)  AND education.school_id = '$school_id'
			
				) dum ORDER BY activity_id DESC
				");	
			
				if($stmt){
						$stmt->bind_param('ssssss',$interest_like, $interest_like,$interest_like, $interest_like, $interest_like,$interest_like);
						if($stmt->execute()){
							 $result = $stmt->get_result();
							 if($result !== false && $result->num_rows >= 1){
								$rows = $result->fetch_all(MYSQLI_ASSOC);
								$stmt->close();
							
							
								foreach($rows as $row){
									if($this->feed_id_list != '-1'){
										$this->feed_id_list .= ','.$row['activity_id'];
									}else{
										$this->feed_id_list = ','.$row['activity_id'];
									}
								}
								$this->feed_id_list = trim($this->feed_id_list , ',');
								return $rows;
						}
					}
			}
			}
			echo $this->connection->error;
			return false;
			
			
			
		
		}
		
		
		public function returnSimilarInterestPost($interest_like = false){
			if($interest_like === false ){
				include_once MODEL_PATH.'Interest.php';
				$interest = new Interest();
				$mine_interests = $interest->getInterestNameForUser($_SESSION['id'], -1);
				$interest_like = '';
				if($mine_interests !== false){
					foreach($mine_interests as $interest){
						$interest_like .= $interest['name'].'|';	
					}
					$interest_like = trim($interest_like,'|');
				}else{
					return false;
				}
			}
			
			if($interest_like != ''){
				$stmt = $this->connection->prepare("
				SELECT * 	
				FROM
				(
					SELECT  interest_activity.id AS activity_id, interest_activity.type
					FROM interest 
					LEFT JOIN interest_activity
					ON interest.id = interest_activity.interest_id 
					LEFT JOIN moment
					ON moment.interest_activity_id = interest_activity.id 
					LEFT JOIN education
					ON interest_activity.user_id = education.user_id
					WHERE interest_activity.id NOT IN($this->feed_id_list) AND interest.name REGEXP ? AND interest_activity.type = 'm' 
			
					UNION
			
					SELECT interest_activity.id AS activity_id, interest_activity.type
					FROM moment 
					LEFT JOIN interest_activity
					ON moment.interest_activity_id = interest_activity.id
					LEFT JOIN education
					ON interest_activity.user_id = education.user_id
					WHERE interest_activity.id NOT IN($this->feed_id_list) AND interest_activity.type = 'm' AND   ( moment.description REGEXP ? )
			
					UNION 
				
					SELECT  interest_activity.id AS activity_id, interest_activity.type
					FROM interest 
					LEFT JOIN interest_activity
					ON interest.id = interest_activity.interest_id 
					LEFT JOIN event
					ON event.interest_activity_id = interest_activity.id 
					LEFT JOIN education
					ON interest_activity.user_id = education.user_id
					WHERE interest_activity.id NOT IN($this->feed_id_list) AND interest.name REGEXP ? AND interest_activity.type = 'e' 
				
					UNION
				
					SELECT interest_activity.id AS activity_id, interest_activity.type
					FROM event 
					LEFT JOIN interest_activity
					ON   event.interest_activity_id = interest_activity.id
					LEFT JOIN education
					ON interest_activity.user_id = education.user_id
					WHERE  interest_activity.id NOT IN($this->feed_id_list) AND interest_activity.type = 'e'  AND  (event.title REGEXP ?  ||  event.description  REGEXP ? || event.location REGEXP ?) 
			
				) dum ORDER BY activity_id DESC
				");	
			
				if($stmt){
						$stmt->bind_param('ssssss',$interest_like, $interest_like,$interest_like, $interest_like, $interest_like,$interest_like);
						if($stmt->execute()){
							 $result = $stmt->get_result();
							 if($result !== false && $result->num_rows >= 1){
								$rows = $result->fetch_all(MYSQLI_ASSOC);
								$stmt->close();
							
							
								foreach($rows as $row){
									if($this->feed_id_list != '-1'){
										$this->feed_id_list .= ','.$row['activity_id'];
									}else{
										$this->feed_id_list = ','.$row['activity_id'];
									}
								}
								$this->feed_id_list = trim($this->feed_id_list , ',');
								return $rows;
						}
					}
				}
			}
			echo $this->connection->error;
			return false;
			
			
		}
		
		public function returnPostFromSameSchool($school_id = false){
			if($school_id === false){
				//there is no school_id passed as parameter
				include_once 'Education.php';
				$edu = new Education();
				$school_id =  $edu->getSchoolIdByUserId($_SESSION['id']);
				if($school_id === false){
					return false;
				}
			}
			$stmt = $this->connection->prepare("
			SELECT interest_activity.id AS activity_id, interest_activity.type
			FROM  education
			LEFT JOIN interest_activity
			ON education.user_id = interest_activity.user_id
			WHERE interest_activity.id NOT IN($this->feed_id_list) AND education.school_id = '$school_id' ORDER BY interest_activity.id DESC");			
			if($stmt){
				if($stmt->execute()){
					 $result = $stmt->get_result();
					 if($result !== false && $result->num_rows >= 1){
						$rows = $result->fetch_all(MYSQLI_ASSOC);
						$stmt->close();
						return $rows;
					}
				}
			}
			return false;
		}
		
		public function returnMomentFromSchoolKeyWord($school_key_word, $exclusive_list = "'-1'"){
			include_once 'School.php';
			$search_school_array = School::getSchooIdsLikeSchoolName($school_key_word);
			$search_school_id = '';
			if($search_school_array !== false){
				foreach($search_school_array as $id){
					$search_school_id .= "'".$id['id']."',";
				}
				$search_school_id = trim($search_school_id,',');
			}else{
				return false;
			}
			$stmt = $this->connection->prepare("
			SELECT interest_activity.id AS activity_id, interest_activity.type
			FROM  education
			LEFT JOIN interest_activity
			ON education.user_id = interest_activity.user_id
			WHERE interest_activity.id NOT IN($exclusive_list) AND education.school_id IN($search_school_id) AND interest_activity.type = 'm' ORDER BY interest_activity.id DESC");			
			if($stmt){
				if($stmt->execute()){
					 $result = $stmt->get_result();
					 if($result !== false && $result->num_rows >= 1){
						$rows = $result->fetch_all(MYSQLI_ASSOC);
						$stmt->close();
						$left_content = "";
						$right_content = "";
						$count = 0;
						foreach($rows as $row){
							$content = '';
							$content = $this->getMomentInterestActivityBlockByActivityId($row['activity_id']);
							if($count++ % 2 == 0){
								$left_content.= $content;
							}else{
								$right_content.= $content;
							}
						}
						return array('left_content'=>$left_content, 'right_content'=>$right_content);
							
					}
				}
			}
			echo $this->connection->error;
			return false;
		}
		
		
		public function returnEventFromSchoolKeyWord($school_key_word, $exclusive_list = "'-1'"){
			include_once 'School.php';
			$search_school_array = School::getSchooIdsLikeSchoolName($school_key_word);
			$search_school_id = '';
			if($search_school_array !== false){
				foreach($search_school_array as $id){
					$search_school_id .= "'".$id['id']."',";
				}
				$search_school_id = trim($search_school_id,',');
			}else{
				return false;
			}
			
			
			
			$stmt = $this->connection->prepare("
			SELECT interest_activity.id AS activity_id, interest_activity.type
			FROM  education
			LEFT JOIN interest_activity
			ON education.user_id = interest_activity.user_id
			WHERE interest_activity.id NOT IN($exclusive_list) AND education.school_id IN($search_school_id) AND interest_activity.type = 'e' ORDER BY interest_activity.id DESC");			
			if($stmt){
				if($stmt->execute()){
					 $result = $stmt->get_result();
					 if($result !== false && $result->num_rows >= 1){
						$rows = $result->fetch_all(MYSQLI_ASSOC);
						$stmt->close();
						$left_content = "";
						$right_content = "";
						$count = 0;
						foreach($rows as $row){
							$content = '';
							$content = $this->getEventInterestActivityBlockByActivityId($row['activity_id']);
							if($count++ % 2 == 0){
								$left_content.= $content;
							}else{
								$right_content.= $content;
							}
						}
						return array('left_content'=>$left_content, 'right_content'=>$right_content);
					
					}
				}
			}
			
			echo $this->connection->error;
			return false;
		}
		
		
		public function isEventExistsForActivityId($activity_id){
			include_once 'Event.php';
			$event = new Event();
			return $event->isEventExistsForActivityId($activity_id);
		}
		
		
		
		public function sendEventInvitation($post_key, $list_string){
			$invited_list = explode(',',  trim($list_string,','));
			include_once MODEL_PATH.'Event_Invitation.php';
			$invitation = new Event_Invitation();
			$activity_id = $this->getActivityIdByKey($post_key);
			if($activity_id !== false){
				include_once 'Event.php';
				$event = new Event();
				$event_id = $this->isEventExistsForActivityId($activity_id);
				if($event_id !== false){
					var_dump($invited_list);
					foreach($invited_list as $user_to_hash){
						$invitation->sendInvitation($_SESSION['id'], $user_to_hash, $event_id);
					}
					return true;
				}
			}
			return false;
		}
		
		
		
		
		
		
	}
?>