<?php
	include_once 'core_table.php';
	class Group_Message extends Core_Table{
		private  $table_name = "group_message";
		private  $group_contact_block_template_path = TEMPLATE_PATH_CHILD."group_contact_block.phtml";		
		private  $own_dialog_template_path = TEMPLATE_PATH_CHILD.'own_dialog.phtml';
		private  $others_dialog_template_path = TEMPLATE_PATH_CHILD.'others_dialog.phtml';
		private  $sent_from  = 'g-';


		public function __construct(){
			parent::__construct($this->table_name);
		}
		
		public function renderMessageContactOfGivenGroup($group_id){
			//  include_once 'User_Profile_Picture.php';
// 			$profile = new User_Profile_Picture();
// 			$profile_pic = $profile->getLatestProfileImageForUser($user_id);
// 			include_once 'User_Table.php';
// 			$user = new User_Table();
			// $fullname = $user->getUserFullnameByUserIden($user_id);
// 			$hasMessage = false;
// 			$latest_message = $this->getLatestMessageWithGivenUser($user_id);
// 			$new_message_num = $this->hasNewMessageFromGivenUser($_SESSION['id'], $user_id);
// 
// 			if($latest_message !== false){
// 				$time = convertDateTimeToAgo($latest_message['sent_time'], false,true, true);
// 				$text = $latest_message['text'];
// 				if($latest_message['user_sent'] == $_SESSION['id']){
// 					$text = 'Me: '.$text;
// 				}
// 				$hasMessage = true;
// 			}
// 			
// 			$user_iden = $user->getUniqueIdenForUser($user_id);
			ob_start();
			include($this->group_contact_block_template_path);
			$content = ob_get_clean();
			return $content;
		}
		
	
		
	
		public function getSentFrom(){
			return $this->sent_from;
		}
		
		
	}		
?>