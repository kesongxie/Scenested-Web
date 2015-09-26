<?php
	include_once 'core_table.php';
	
	class Event extends Core_Table{
		private  $table_name = "event";
		private  $activity_id = false;
		private $preview_block_path = TEMPLATE_PATH_CHILD."evt_preview_block.phtml";
		private $event_invitation_path = TEMPLATE_PATH_CHILD."invitation.phtml";
		private $invitation_contact_path = TEMPLATE_PATH_CHILD."invitation_contact.phtml";
		private $invitation_contact_group_path = TEMPLATE_PATH_CHILD."invitation_contact_group.phtml";

		public $event_id = null;
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
		
		public function getInterestActivityIdByEventId($event_id){
			return $this->getColumnById('interest_activity_id',$event_id);
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
						$unique_iden = $user->getUniqueIdenForUser($u);
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
			$resource = $this->getEventTextResource($event_id);
			if($resource !== false){
				ob_start();
				include(TEMPLATE_PATH_CHILD.'group_chat_event_info.phtml');
				$content = ob_get_clean();
				return $content;
			}
			return false;
		}
		
		
		public function getEventTextResource($event_id){
			$evt_resource = $this->loadEventResourceByEventId($event_id);
			if($evt_resource !== false){
				$title = $evt_resource['title'];
				$description = $evt_resource['description'];
				$location = $evt_resource['location'];
				$time = "";
				if($evt_resource['date'] != null){
					$time .= returnShortDate($evt_resource['date'],',').' - '.getWeekDayFromDate($evt_resource['date']);
				}
				
				if($evt_resource['time'] != null){
					if($evt_resource['date'] != null){
						$time .= ', ';
					}
					$time .= convertTimeToAmPm($evt_resource['time']);
				}
				
				include_once MODEL_PATH.'User_Media_Prefix.php';
				$prefix = new User_Media_Prefix();
				$event_photo = $this->event_photo->getEventPhotoResourceByMomentId($event_id);
				$media_prefix = $prefix->getUserMediaPrefix($event_photo['user_id']).'/';
				if($event_photo !== false && isMediaDisplayable($media_prefix.$event_photo['picture_url'])){
					$event_photo_url = U_IMGDIR.$media_prefix.$event_photo['picture_url'];
				}
				return array("title"=>$title, "description"=>$description, "location"=>$location, "time"=>$time, "event_photo_url"=>$event_photo_url);
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
			$event_id = $activity->isEventExistsForActivityKey($key);
			if($event_id !== false){
				$labels = $interest->getUserInterestsLabel($_SESSION['id']);
				include_once 'User_In_Interest.php';
				$in = new User_In_Interest();
				include_once MODEL_PATH.'Event_Invitation.php';
				$invitation = new Event_Invitation();
				$invitation_num = $invitation->getEventInvitedUserNum($event_id);
				$all_friend_plain_list = $in->getFriendPlainListForUser($_SESSION['id']);
				if($all_friend_plain_list !== false){
					$all_friend_block = $activity->getAllFriendContactByPlainList($all_friend_plain_list, $event_id);
				}
				$header_title = 'Invite Friends';
				$button_action = 'Invite';
				$action = 'Invited';
				ob_start();
				include($this->event_invitation_path);
				$content = ob_get_clean();
				return $content;
			}
			return false;
		}
		

		
		public function isUserInvitedInEvent($user_id, $event_id){
			include_once MODEL_PATH.'Event_Invitation.php';
			$invitation = new Event_Invitation();
			return $invitation->isUserInvitedInEvent($user_id, $event_id);
		}
		
		
		
		/* this method is for loading friends for event invitation */
		public function getInvitationUserFriendBlockByInterestId($interest_id, $post_key, $limit_num = -1, $offset = 0 ){
			include_once MODEL_PATH.'User_In_Interest.php';
			$in = new User_In_Interest();
			include_once MODEL_PATH.'Interest_Activity.php';
			$activity = new Interest_Activity();
			$user_found = $in->getUserInInterestByInterestId($interest_id, $limit_num, $offset);
			include_once MODEL_PATH.'Event_Invitation.php';
			return $activity->renderInvitationContactBlockByResource($user_found, $post_key, new Invitation());	
			
		}
		
		/* this method is for loading friends for event invitation */
		public function getInvitationAllUserFriendBlock($post_key){
			include_once 'User_In_Interest.php';
			$in = new User_In_Interest();
			include_once 'User_Table.php';
			$user = new User_Table();
			include_once 'Interest_Activity.php';
			$activity = new Interest_Activity();
			$user_found = $in->getAllFriendsInUsersInterestByUserId($_SESSION['id']);
			include_once MODEL_PATH.'Event_Invitation.php';
			return $activity->renderInvitationContactBlockByResource($user_found, $post_key, new Invitation());	
		}
		
		/* this method is for loading friends for event include */
		public function getIncludeUserFriendBlockByInterestId($interest_id, $post_key, $limit_num = -1, $offset = 0 ){
			include_once 'User_In_Interest.php';
			$in = new User_In_Interest();
			include_once 'Interest_Activity.php';
			$activity = new Interest_Activity();
			$user_found = $in->getUserInInterestByInterestId($interest_id, $limit_num, $offset);
			include_once MODEL_PATH.'Event_Include.php';
			return $activity->renderInvitationContactBlockByResource($user_found, $post_key, new Event_Include());	
			
		}
		
		
		/* this method is for loading friends for event include */
		public function getIncludeAllUserFriendBlock($post_key){
			include_once 'User_In_Interest.php';
			$in = new User_In_Interest();
			include_once 'User_Table.php';
			$user = new User_Table();
			include_once 'Interest_Activity.php';
			$activity = new Interest_Activity();
			$user_found = $in->getAllFriendsInUsersInterestByUserId($_SESSION['id']);
			include_once MODEL_PATH.'Event_Include.php';
			return $activity->renderInvitationContactBlockByResource($user_found, $post_key, new Event_Include());	
		}
		
		
		//for now the event is 
		public function isEventEditableByUser($event_id, $user_id){
			return $this->getPostUserByEventId($event_id) == $user_id;
		}
		
		//return the event id if found, otherwise return false;
		public function isEventExistsForActivityId($activity_id){
			return $this->getColumnBySelector('id', 'interest_activity_id', $activity_id);
		}
		
		public function loadEventInvitedList($event_id){
			include_once MODEL_PATH.'Event_Invitation.php';
			$invitation = new Event_Invitation();
			return $invitation->loadInvitedListForEvent($event_id);
		}
		
		public function loadEventIncludedList($event_id){
			include_once MODEL_PATH.'Event_Include.php';
			$include = new Event_Include();
			return $include->loadInvitedListForEvent($event_id);
		}
		
		
	}
?>