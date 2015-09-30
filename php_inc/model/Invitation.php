<?php
	include_once 'Noti_Sendable.php';
	include_once 'User_Table.php';
	class Invitation extends Noti_Sendable{
		private $table_name = null;
		public function __construct($table_name){
			parent::__construct($table_name);
			$this->table_name = $table_name;
		}
		
		public function sendInvitation($user_id, $user_id_get_hash, $event_id){
			$user = new User_Table();
			$user_id_get = $user->getUserIdByKey($user_id_get_hash);
			include_once 'Event.php';
			$event = new Event();
			if($user_id_get !== false && !$event->hasUserJoinedEvent($user_id_get, $event_id) && $user_id != $user_id_get &&  $event->isEventEditableByUser($event_id, $user_id)){
				if(!$this->isRequestAlreadySentForEventId($user_id, $user_id_get, $event_id)){
					$hash = $this->generateUniqueHash();
					$stmt = $this->connection->prepare("INSERT INTO `$this->table_name` (`user_id`,`user_id_get`,`event_id`,`sent_time`,`hash`) VALUES(?, ?, ?, ?, ?)");
					$time = date('Y-m-d H:i:s');
					if($stmt){
						$stmt->bind_param('iiiss',$user_id, $user_id_get, $event_id, $time, $hash);
						$expire = date('Y-m-d H:i:s', COOKIE_EXPIRE_TIME);
						if($stmt->execute()){
							$stmt->close();
							$row_id = $this->connection->insert_id;
							if($this->noti_queue->addNotificationQueueForUser($user_id_get, $row_id) !== false){
								return true;
							}
						}
					}
				}
			}
			
			echo $this->connection->error;
			return false;
		}
		
		
		
		
		
		
		
		public function isRequestAlreadySentForEventId($user_id, $user_id_get, $event_id){
			$stmt = $this->connection->prepare("SELECT `id` FROM `$this->table_name` WHERE `user_id`=? AND `user_id_get`=? AND (`process`='n' || `process` = 'i')AND `event_id`=? LIMIT 1");
			if($stmt){
				$stmt->bind_param('iii', $user_id, $user_id_get, $event_id);
				if($stmt->execute()){
					 $result = $stmt->get_result();
					 if($result->num_rows == 1){
						return true;
					 }
				}
			}
			return false;
		}
		
		public function isUserInvitedInEvent($user_id_get, $event_id){
			$stmt = $this->connection->prepare("SELECT `id` FROM `$this->table_name` WHERE `user_id_get`=? AND `event_id`=? LIMIT 1");
			if($stmt){
				$stmt->bind_param('ii' ,$user_id_get, $event_id);
				if($stmt->execute()){
					 $result = $stmt->get_result();
					 if($result->num_rows == 1){
						return true;
					 }
				}
			}
			return false;
		}
		
		public function isRequestAlreadyAccepted($hash){
			if($this->getColumnBySelector('process','hash',$hash) == 'y'){
				return true;
			}
			return false;
		}
		
		public function isRequestAlreadyIgnored($hash){
			if($this->getColumnBySelector('process','hash',$hash) == 'i'){
				return true;
			}
			return false;
		}
		
		
		public function renderEventInvitationRequestForNotificationBlock($row_id){
			$select_columns = array('user_id','event_id','sent_time','hash');
			$request_row = $this->getMultipleColumnsById($select_columns, $row_id);
			if($request_row !== false){
				include_once 'User_Profile_Picture.php';
				$profile = new User_Profile_Picture();
				$sender_pic = $profile->getLatestProfileImageForUser($request_row['user_id']);
				$user = new User_Table();
				$fullname = $user->getUserFullnameByUserIden($request_row['user_id']);
				$sent_time = convertDateTimeToAgo($request_row['sent_time'], false);	
				$user_page_redirect =  USER_PROFILE_ROOT.$user->getUserAccessUrl($request_row['user_id']);
				$unique_iden =  $user->getUniqueIdenForUser($request_row['user_id']);
				$hash = $request_row['hash'];
				include_once MODEL_PATH.'Event.php';
				$event = new Event();
				$event_title = $event->getEventTitleByEventId($request_row['event_id']);
				$event_title = (strlen($event_title) > 60)?substr($event_title,0,120).'...':$event_title;
			
				$isRequestAccepted = $this->isRequestAlreadyAccepted($hash);
				$isRequestIgnored = false;
				if($isRequestAccepted !== true){
					$isRequestIgnored = $this->isRequestAlreadyIgnored($hash);
				}
				
				if($event->isEventEditableByUser($request_row['event_id'], $request_row['user_id'])){
					$gender_call = $user->getWhatShouldCallForUser($request_row['user_id']);
					$pronoun = $gender_call[1];
				}else{
					$pronoun = 'the';
				}
				
				ob_start();
				include($this->request_block_template_path);
				$content = ob_get_clean();
				return $content;
			}
			return false;
		}
		
		public function renderEventInvitationRequestAcceptForNotificationBlock($row_id){
			$select_columns = array('user_id_get','event_id','process_time','hash');
			$request_row = $this->getMultipleColumnsById($select_columns, $row_id);
			if($request_row !== false){
				include_once 'User_Profile_Picture.php';
				$profile = new User_Profile_Picture();
				$sender_pic = $profile->getLatestProfileImageForUser($request_row['user_id_get']);
				$user = new User_Table();
				$fullname = $user->getUserFullnameByUserIden($request_row['user_id_get']);
				$sent_time = convertDateTimeToAgo($request_row['process_time'], false);	
				$user_page_redirect =  USER_PROFILE_ROOT.$user->getUserAccessUrl($request_row['user_id_get']);
				$unique_iden =  $user->getUniqueIdenForUser($request_row['user_id_get']);
				$hash = $request_row['hash'];
			
				include_once MODEL_PATH.'Event.php';
				$event = new Event();
				$event_title = $event->getEventTitleByEventId($request_row['event_id']);
				$event_title = (strlen($event_title) > 60)?substr($event_title,0,120).'...':$event_title;
			
				ob_start();
				include($this->accept_request_block_template_path);
				$content = ob_get_clean();
				return $content;
			}
			return false;
		}
		

		public function acceptEventInvitationRequestForUser($key, $accept_user){
			$stmt = $this->connection->prepare("SELECT `id`,`user_id`,`event_id` FROM `$this->table_name` WHERE `user_id_get`=? AND `process`='n' AND `hash`=? LIMIT 1");
			if($stmt){
				$stmt->bind_param('is', $accept_user, $key);
				if($stmt->execute()){
					 $result = $stmt->get_result();
					 if($result->num_rows == 1){
						//allow to accept
						$row = $result->fetch_assoc();
						//update process to y
						if($this->setColumnById('process', 'y', $row['id']) && $this->setColumnById('process_time', date('Y-m-d H:i:s'), $row['id'])  ){
							//push to the notification queue
							$this->noti_queue->addNotificationQueueForUserWithCustomSendFrom($row['user_id'], $row['id'], $this->accept_noti_send_from_code);				
							include_once MODEL_PATH.'Interest_Activity.php';
							$activity = new Interest_Activity();
							include_once MODEL_PATH.'Event.php';
							$event = new Event();
							$activity_id = $event->getInterestActivityIdByEventId($row['event_id']);
							if($activity_id !== false){
								$activity->joinEventForUser($accept_user, false, $activity_id);
							}
							return true;
						}
					 }
				}
			}
			echo $this->connection->error;
			return false;
		}
		
		
		public function ignoreEventInvitationRequestForUser($key, $ignore_user){
			$stmt = $this->connection->prepare("SELECT `id` FROM `$this->table_name` WHERE `user_id_get`=? AND `process`='n' AND `hash`=? LIMIT 1");
			if($stmt){
				$stmt->bind_param('is', $ignore_user, $key);
				if($stmt->execute()){
					 $result = $stmt->get_result();
					 if($result->num_rows == 1){
						//allow to accept
						$row = $result->fetch_assoc();
						$this->setColumnById('process', 'i', $row['id']);
						$this->setColumnById('process_time', date('Y-m-d H:i:s'), $row['id']);
						return true;
					 }
				}
			}
			echo $this->connection->error;
			return false;
		}
		
		public function deleteAllRequestByEventId($user_id, $event_id){
			$this->deleteRowBySelectorForUser('event_id', $event_id, $user_id, true);
		}
		
		public function getEventInvitedUserNum($event_id){
			return $this->getRowsNumberForNumericColumn('event_id', $event_id);
		}
		
		public function loadInvitedListForEvent($event_id){
			$rows = $this->getAllRowsMultipleColumnsBySelector(array('user_id_get','hash'), 'event_id', $event_id);
			$list = '';
			if($rows !== false){
				$user = new User_Table();
				foreach($rows as $row){
					$fullname = $user->getUserFullnameByUserIden($row['user_id_get']);
					$unique_iden = $user->getUniqueIdenForUser($row['user_id_get']);
					$profile_pic = $user->getLatestProfilePictureForuser($row['user_id_get']);
					$hash = $row['hash'];
					ob_start();
					include($this->invited_list_path);
					$list .= ob_get_clean();
				}
			}
			return $list;
		}
		
		public function deleteEventInvitation($key){
			$this->deleteNotiQueueForKey($key);
			return $this->deleteRowBySelectorForUser('hash', $key, $_SESSION['id'], true);
		}
		
		
		
	}
?>