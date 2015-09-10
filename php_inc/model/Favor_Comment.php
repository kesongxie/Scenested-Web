<?php
	include_once 'Favor.php';
	class Favor_Comment extends Favor{
		private $table_name = "favor_comment";
		private $popover_notification_template_path = TEMPLATE_PATH_CHILD."popover_notification_favor_comment.phtml";
		
		//key is the hash for activity
		public function __construct(){
			parent::__construct($this->table_name);
		}
		
		
		public function renderFavorCommentForNotificationBlock($row_id){
			$column_array = array('target_id','user_id','sent_time', 'hash');
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
				$hash = $result['hash'];
				include_once 'Comment.php';
				$cmt  = new Comment();
				$comment = $cmt->getCommentTextByCommentId($result['target_id']);
				ob_start();
				include($this->popover_notification_template_path);
				$content = ob_get_clean();
				return $content;
			}
			return false;
		}
		
	}
?>