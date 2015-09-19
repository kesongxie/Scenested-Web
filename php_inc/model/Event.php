<?php
	include_once 'core_table.php';
	
	class Event extends Core_Table{
		private  $table_name = "event";
		private  $activity_id = false;
		private $preview_block_path = TEMPLATE_PATH_CHILD."evt_preview_block.phtml";
		private $event_invitation_path = TEMPLATE_PATH_CHILD."event_invitation.phtml";
		private $invitation_contact_path = TEMPLATE_PATH_CHILD."invitation_contact.phtml";
		private $invitation_contact_group_path = TEMPLATE_PATH_CHILD."invitation_contact_group.phtml";

		public $event_id;
		public $event_photo = null;
		
		public function __construct($interest_activity_id = null, $include_photo = true){
			parent::__construct($this->table_name);
			if($interest_activity_id !== null){
				$this->activity_id = $interest_activity_id;
				$this->event_id = $this->getColumnBySelector('id', 'interest_activity_id', $this->activity_id);
			}
			if($include_photo){
				include_once 'Event_Photo.php';
				$this->event_photo = new Event_Photo();
			}
		}
		
		public function addEventForUser($user_id, $title, $description, $location, $date, $evt_time, $photoFile, $caption){
			$stmt = $this->connection->prepare("INSERT INTO `$this->table_name` (`interest_activity_id`,`title`,`description`,`location`,`date`,`time`) VALUES(?, ?, ?, ?, ?,?)");
			if($stmt){
				if(empty($location)){
					$location = null;
				}
				if(empty($evt_time)){
					$evt_time = null;
				}
			
				$stmt->bind_param('isssss',$this->activity_id, $title, $description, $location, $date,$evt_time);
				if($stmt->execute()){
					$event_id = $this->connection->insert_id;
					 if($photoFile != null){
						$event_photo_url = $this->event_photo->uploadEventPhotoByEventId($photoFile, $user_id, $event_id, $caption);
						if($event_photo_url === false){
							$this->deleteRowById($event_id);
							$stmt->close();
							return false;
						}
					}else{
						//insert a row that copies the user's interest label image
						 $this->event_photo->copyInterestLabelImageAsEventPhoto($user_id, $event_id,$this->activity_id);
					}
					$stmt->close();
					return $this->activity_id;
				}
			}
			return false;
		}
		
		
		public function loadEventResource(){
			$column_array = array('id','title','description','location','date','time');
			return $this->getMultipleColumnsBySelector($column_array, 'interest_activity_id', $this->activity_id);	
		}
		
		public function loadEventResourceByEventId($event_id){
			$column_array = array('id','title','description','location','date','time');
			return $this->getMultipleColumnsById($column_array,$event_id);	
		}
		
		public function getPostText(){
			return $this->getColumnBySelector('description', 'interest_activity_id', $this->activity_id);	
		}
		
		public function getPostTitle(){
			return $this->getColumnBySelector('title', 'interest_activity_id', $this->activity_id);	
		}
	
		
		public function deleteEventForUserByActivityId($user_id){
			//delete photos in this event
			$event_id = $this->getColumnBySelector('id', 'interest_activity_id', $this->activity_id);
			$this->event_photo->deleteEventPhotoForUserByEventId($user_id, $event_id);
			
			//delete comments in this moment
			
			//delete the row itself
			$this->deleteRowByNumericSelector('interest_activity_id', $this->activity_id);
		}
		
		public function renderEventPrewviewBlock($post_owner,$interest_id, $isEventEditableForCurrentUser = false){
			$evt_resource = $this->loadEventResource();
			$title = $evt_resource['title'];
			$description = $evt_resource['description'];
			$date = returnShortDate($evt_resource['date'],'-');
			
			
			$event_photo = $this->event_photo->getEventPhotoUrlByEventId($evt_resource['id']);
			include_once 'User_Media_Prefix.php';
			$prefix = new User_Media_Prefix();	
			$media_prefix = $prefix->getUserMediaPrefix($post_owner).'/';
			
			if($event_photo !== false && isMediaDisplayable($media_prefix.$event_photo)){
				$caption = $this->event_photo->getEventPhotoCaptionByPictureUrl($event_photo);
				$event_photo = $media_prefix.$event_photo;
			}else{
				//get the label photo as the event cover
				include_once 'User_Interest_Label_Image.php';
				$label_image = new User_Interest_Label_Image();
				$event_photo = $media_prefix.$label_image->getLabelImageUrlByInterestId($interest_id);
			}
			$regular_event_photo = str_replace('thumb_','',$event_photo);
			$isEventPassed = (time() > strtotime($evt_resource['date'].$evt_resource['time']));				

			
			$photos = $this->event_photo->getAllEventPhotoByEventId($evt_resource['id']);
			//load all the photos in this event $photos
			ob_start();
			include($this->preview_block_path);
			$preview_block = ob_get_clean();
			return $preview_block;
		}
		
		public function isEventPassedForActivityId($activity_id){
			$column_array = array('date','time');
			$event = $this->getMultipleColumnsBySelector($column_array, 'interest_activity_id', $activity_id);
			return (time() > strtotime($event['date'].$event['time']));				
		}
		
	
		public function getEventTitleByEventId($event_id){
			return $this->getColumnById('title',$event_id);
		}
		
		public function getPostUserByEventId($event_id){
			$activity_id = $this->getColumnById('interest_activity_id', $event_id);
			if($activity_id !== false){
				include_once 'Interest_Activity.php';
				$activity = new Interest_Activity();
				return $activity->getEventPostUserByActivityId($activity_id);
			}
			return false;
		}
		
		public function getJoinedUserByEventId($event_id){
			include_once 'Groups.php';
			include_once 'Event_Group.php';
			$group = new Groups();
			$e_group = new Event_Group();
			$group_id = $e_group->getGroupIdByEventId($event_id);
			if($group_id !== false){
				return $group->getGroupMemberTitleByGroupId($group_id);
			}else{
				include_once 'User_Table.php';
				$post_user = $this->getPostUserByEventId($event_id);
				$user = new User_Table();
				if($post_user !== false){
					$firstname = $user->getUserFirstNameByUserIden($post_user);
					return array('title'=>$firstname, 'members'=>$firstname);
				}
			}
			return false;
		}
		
		public function hasUserJoinedEvent($user_id, $event_id){
			include_once 'Groups.php';
			$group = new Groups();
			include_once 'Event_Group.php';
			$e_g = new Event_Group();
			$group_id = $e_g->getGroupIdByEventId($event_id);
			if($group_id !== false){
				$user_in = $group->getUserInGroup($group_id);
				if($user_in !== false){
					if( stripos($user_in, $user_id.',') !== false){
						return true;
					}
				}
			}
			return false;
		}
		
		public function getJoinedMemberByEventId($event_id, $group_id = false){
			include_once 'User_Table.php';
			include_once 'Event_Group.php';
			$user = new User_Table();
			include_once 'User_Profile_Picture.php';
			$profile = new User_Profile_Picture();
			$e_g = new Event_Group();
			if($group_id === false){
				$group_id = $e_g->getGroupIdByEventId($event_id);
			}
			$content = "";
			
			if($group_id !== false){
				include_once 'Groups.php';
				$g = new Groups();
				$user_in = $g->getUserInGroup($group_id); //22,28,29,
				if($user_in !== false){
					$users = explode(',',trim($user_in, ','));
					foreach($users as $u){
						$profile_pic = $profile->getLatestProfileImageForUser($u);
						$firstname = $user->getUserFirstNameByUserIden($u);
						$user_page_redirect =  USER_PROFILE_ROOT.$user->getUserAccessUrl($u);
						$hash = $user->getUniqueIdenForUser($u);
						ob_start();
						include(TEMPLATE_PATH_CHILD.'list_item.phtml');
						$content .= ob_get_clean();
					}
				}else{
					return false;
				}
			}else{
				$u = $this->getPostUserByEventId($event_id);
				if($u !== false){
					$profile_pic = $profile->getLatestProfileImageForUser($u);
					$firstname = $user->getUserFirstNameByUserIden($u);
					$user_page_redirect =  USER_PROFILE_ROOT.$user->getUserAccessUrl($u);
					$hash = $user->getUniqueIdenForUser($u);
					ob_start();
					include(TEMPLATE_PATH_CHILD.'list_item.phtml');
					$content .= ob_get_clean();
				}else{
					return false;
				}
			}
			return $content;
		}
		
		public function getEventInforByEventId($event_id, $group_id = false){
			$evt_resource = $this->loadEventResourceByEventId($event_id);
			if($evt_resource !== false){
				$title = $evt_resource['title'];
				$description = $evt_resource['description'];
				$location = $evt_resource['location'];
				$time = returnShortDate($evt_resource['date'],'-');
				ob_start();
				include(TEMPLATE_PATH_CHILD.'group_chat_event_info.phtml');
				$content = ob_get_clean();
				return $content;
			}
			return false;
		}
		
		
		public function isEvtPhotoUploadableByUserForEvent($user_id, $event_id){
			return  $this->hasUserJoinedEvent($user_id, $event_id) || ($this->getPostUserByEventId($event_id) == $user_id);
		}
		
		public function loadEventInvitationDialog($key){
			include_once 'Interest.php';
			$interest = new Interest();
			include_once 'Interest_Activity.php';
			$activity = new Interest_Activity();
			$user_id = $activity->getPostUserByActivityKey($key);
			if($user_id !== false){
				$labels = $interest->getUserInterestsLabel($user_id);
				include_once 'User_In_Interest.php';
				$in = new User_In_Interest();
				$all_friend_plain_list = $in->getFriendPlainListForUser($user_id);
				if($all_friend_plain_list !== false){
					$all_friend_block = $this->getAllFriendContactByPlainList($all_friend_plain_list);
				}
				ob_start();
				include($this->event_invitation_path);
				$content = ob_get_clean();
				return $content;
			}
			return false;
		}
		
		public function getAllFriendContactByPlainList($all_friend_plain_list){
			$contact = '';
			if($all_friend_plain_list !== false){
				include_once 'User_Table.php';
				$user = new User_Table();
				$list = explode(',',$all_friend_plain_list);
				foreach($list as $u){
					$user_id = trim($u,"'");
					$fullname = $user->getUserFullnameByUserIden($user_id);
					$profile_pic = $user->getLatestProfilePictureForuser($user_id);
					$user_page_redirect =  USER_PROFILE_ROOT.$user->getUserAccessUrl($user_id);
					$unique_iden = $user->getUniqueIdenForUser($user_id);
					ob_start();
					include($this->invitation_contact_path);
					$content = ob_get_clean();
					$contact .= $content;
				}
			}
			return $contact;
			
		}
		
		public function getUserFriendBlockByInterestId($interest_id,$limit_num = -1, $offset = 0 ){
			include_once 'User_In_Interest.php';
			$in = new User_In_Interest();
			$user_found = $in->getUserInInterestByInterestId($interest_id, $limit_num, $offset);
			return $this->renderInvitationContactBlockByResource($user_found);	
		}
		
		
		public function getAllUserFriendBlock(){
			include_once 'User_In_Interest.php';
			$in = new User_In_Interest();
			include_once 'User_Table.php';
			$user = new User_Table();
			$user_found = $in->getAllFriendsInUsersInterestByUserId($_SESSION['id']);
			return $this->renderInvitationContactBlockByResource($user_found);	
		}
		
		public function renderInvitationContactBlockByResource($user_found){
			$contact = false;		
			if($user_found !== false){
				$contact = '';
				include_once 'User_Table.php';
				$user = new User_Table();
				foreach($user_found as $u){
					$fullname = $u['fullname'];
					$profile_pic = $user->getLatestProfilePictureForuser($u['id']);
					$unique_iden = $u['hash'];
					ob_start();
					include($this->invitation_contact_path);
					$content = ob_get_clean();
					$contact .= $content;
				}
			}
			
			ob_start();
			include($this->invitation_contact_group_path);
			$content = ob_get_clean();
			return $content;
			
		}
		
		
		
		
		
		
	}
?>