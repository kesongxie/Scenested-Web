<?php
	include_once 'Favor.php';
	class Favor_Activity extends Favor{
		private $table_name = "favor_activity";
		private $popover_notification_template_path = TEMPLATE_PATH_CHILD."popover_notification_favor_activity.phtml";
		
		//key is the hash for activity
		public function __construct(){
			parent::__construct($this->table_name);
		}
		
		
		public function renderFavorActivityForNotificationBlock($row_id){
			$column_array = array('target_id','user_id','sent_time');
			$result = $this->getMultipleColumnsById($column_array, $row_id);
			if($result !== false){
				include_once 'User_Profile_Picture.php';
				$profile = new User_Profile_Picture();
				$profile_pic = $profile->getLatestProfileImageForUser($result['user_id']);
				include_once 'User_Table.php';
				$user = new User_Table();
				$fullname = $user->getUserFullnameByUserIden($result['user_id']);
				$time = convertDateTimeToAgo($result['sent_time'], false);	
				$user_page_redirect =  USER_PROFILE_ROOT.$user->getUserAccessUrl($result['user_id']);
				$unique_iden = $user->getUniqueIdenForUser($result['user_id']);
				
				include_once 'Interest_Activity.php';
				$activity  = new Interest_Activity();
				$post_detail = $activity->getNotificationPostDetailByActivityId($result['target_id']);
				ob_start();
				include($this->popover_notification_template_path);
				$content = ob_get_clean();
				return $content;
			}
			return false;
		}
		
	}
?>