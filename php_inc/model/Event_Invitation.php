<?php
	include_once 'Noti_Sendable.php';
	include_once 'User_Table.php';
	
	
	class Event_Invitation extends Noti_Sendable{
		private $table_name = "event_invitation";
		private $request_block_template_path = TEMPLATE_PATH_CHILD.'popover_notification_interest_request_block.phtml';
		private $accept_request_block_template_path = TEMPLATE_PATH_CHILD.'popover_notification_interest_request_accept_block.phtml';
		private $invited_list_path = TEMPLATE_PATH_CHILD.'event_invited_friend_list.phtml';
		private $accept_noti_send_from_code = "eia-";
		
		public function __construct(){
			parent::__construct($this->table_name);
		}	
		public function sendInvitation($user_id, $user_to_hash, $event_id){
			$user = new User_Table();
			$user_to = $user->getUserIdByKey($user_to_hash);
			include_once 'Event.php';
			$event = new Event();
			if($user_to !== false && $user_id != $user_to &&  $event->isEventEditableByUser($event_id, $user_id)){
				if(!$this->isRequestAlreadySentForEventId($user_id, $user_to, $event_id)){
					$hash = $this->generateUniqueHash();
					$stmt = $this->connection->prepare("INSERT INTO `$this->table_name` (`user_id`,`user_to`,`event_id`,`sent_time`,`hash`) VALUES(?, ?, ?, ?, ?)");
					$time = date('Y-m-d H:i:s');
					if($stmt){
						$stmt->bind_param('iiiss',$user_id, $user_to, $event_id, $time, $hash);
						$expire = date('Y-m-d H:i:s', COOKIE_EXPIRE_TIME);
						if($stmt->execute()){
							$stmt->close();
							$row_id = $this->connection->insert_id;
							if($this->noti_queue->addNotificationQueueForUser($user_to, $row_id) !== false){
								return true;
							}
						}
					}
				}
			}
			
			echo $this->connection->error;
			return false;
		}
		
		public function isRequestAlreadySentForEventId($user_id, $user_to, $event_id){
			$stmt = $this->connection->prepare("SELECT `id` FROM `$this->table_name` WHERE `user_id`=? AND `user_to`=? AND (`process`='n' || `process` = 'i')AND `event_id`=? LIMIT 1");
			if($stmt){
				$stmt->bind_param('iii', $user_id, $user_to, $event_id);
				if($stmt->execute()){
					 $result = $stmt->get_result();
					 if($result->num_rows == 1){
						return true;
					 }
				}
			}
			return false;
		}
		
		public function isUserInvitedInEvent($user_to, $event_id){
			$stmt = $this->connection->prepare("SELECT `id` FROM `$this->table_name` WHERE `user_to`=? AND (`process`='n' || `process` = 'i')AND `event_id`=? LIMIT 1");
			if($stmt){
				$stmt->bind_param('ii' ,$user_to, $event_id);
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
			
				include_once 'Interest.php';
				$interest = new Interest();
				$interest_name = $interest->getInterestNameByEventId($request_row['event_id']);
				$interest_description = $interest->getInterestDescriptionByEventId($request_row['event_id']);
				$interest_description = (strlen($interest_description) > 60)?substr($interest_description,0,120).'...':$interest_description;
			
				$isRequestAccepted = $this->isRequestAlreadyAccepted($hash);
				$isRequestIgnored = false;
				if($isRequestAccepted !== true){
					$isRequestIgnored = $this->isRequestAlreadyIgnored($hash);
				}
				$gender_call = $user->getWhatShouldCallForUser($request_row['user_id']);
				$herorhis = $gender_call[1];
				ob_start();
				include($this->request_block_template_path);
				$content = ob_get_clean();
				return $content;
			}
			return false;
		}
		
		public function renderEventInvitationRequestAcceptForNotificationBlock($row_id){
			$select_columns = array('user_to','event_id','process_time','hash');
			$request_row = $this->getMultipleColumnsById($select_columns, $row_id);
			if($request_row !== false){
				include_once 'User_Profile_Picture.php';
				$profile = new User_Profile_Picture();
				$sender_pic = $profile->getLatestProfileImageForUser($request_row['user_to']);
				$user = new User_Table();
				$fullname = $user->getUserFullnameByUserIden($request_row['user_to']);
				$sent_time = convertDateTimeToAgo($request_row['process_time'], false);	
				$user_page_redirect =  USER_PROFILE_ROOT.$user->getUserAccessUrl($request_row['user_to']);
				$unique_iden =  $user->getUniqueIdenForUser($request_row['user_to']);
				$hash = $request_row['hash'];
			
				include_once 'Interest.php';
				$interest = new Interest();
				$interest_name = $interest->getInterestNameByEventId($request_row['event_id']);
				$interest_description = $interest->getInterestDescriptionByEventId($request_row['event_id']);
				$interest_description = (strlen($interest_description) > 60)?substr($interest_description,0,120).'...':$interest_description;
			
				ob_start();
				include($this->accept_request_block_template_path);
				$content = ob_get_clean();
				return $content;
			}
			return false;
		}
		

		public function acceptEventInvitationRequestForUser($key, $accept_user){
			$stmt = $this->connection->prepare("SELECT `id`,`user_id`,`event_id` FROM `$this->table_name` WHERE `user_to`=? AND `process`='n' AND `hash`=? LIMIT 1");
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
							include_once 'User_In_Interest.php';
							$in = new User_In_Interest();
							return $in->addUserInInterest($row['event_id'], $row['user_id'], $accept_user);
						}
					 }
				}
			}
			return false;
		}
		
		
		public function ignoreEventInvitationRequestForUser($key, $ignore_user){
			$stmt = $this->connection->prepare("SELECT `id` FROM `$this->table_name` WHERE `user_to`=? AND `process`='n' AND `hash`=? LIMIT 1");
			if($stmt){
				$stmt->bind_param('is', $ignore_user, $key);
				if($stmt->execute()){
					 $result = $stmt->get_result();
					 if($result->num_rows == 1){
						//allow to accept
						$row = $result->fetch_assoc();
						
						$this->setColumnById('process', 'i', $row['id']);
						$this->setColumnById('process_time', date('Y-m-d H:i:s'), $row['id']);
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
			$rows = $this->getAllRowsMultipleColumnsBySelector(array('user_to','hash'), 'event_id', $event_id);
			$list = '';
			if($rows !== false){
				$user = new User_Table();
				foreach($rows as $row){
					$fullname = $user->getUserFullnameByUserIden($row['user_to']);
					$key = $user->getUniqueIdenForUser($row['user_to']);
					$profile_pic = $user->getLatestProfilePictureForuser($row['user_to']);
					$hash = $row['hash'];
					ob_start();
					include($this->invited_list_path);
					$list .= ob_get_clean();
				}
			}
			return $list;
		}
		
		
		public function deleteEventInvitation($key){
			return $this->deleteRowBySelectorForUser('hash', $key, $_SESSION['id'], true);
		}
		
		
		
		
	}
	
?>