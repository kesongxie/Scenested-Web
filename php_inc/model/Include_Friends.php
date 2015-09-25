<?php
	class Include_Friends{
		const event_invitation_path = TEMPLATE_PATH_CHILD."invitation.phtml";
		
		public function __construct(){
			
		}
		
		
		
		public static function loadEventIncludeFriendDialog($key){
			include_once 'Interest.php';
			$interest = new Interest();
			include_once 'Interest_Activity.php';
			$activity = new Interest_Activity();
			$event_id = $activity->isEventExistsForActivityKey($key);
			if($event_id !== false){
				$labels = $interest->getUserInterestsLabel($_SESSION['id']);
				include_once 'User_In_Interest.php';
				$in = new User_In_Interest();
				include_once MODEL_PATH.'Event_Include.php';
				$include = new Event_Include();
				$invitation_num = $include->getEventInvitedUserNum($event_id);
				$all_friend_plain_list = $in->getFriendPlainListForUser($_SESSION['id']);
				if($all_friend_plain_list !== false){
					$all_friend_block = $activity->getAllFriendContactByPlainList($all_friend_plain_list, $event_id, $include);
				}
				$header_title = 'Include Friends';
				$button_action = 'Include';
				$action = 'Included';

				ob_start();
				include(self::event_invitation_path);
				$content = ob_get_clean();
				return $content;
			}
			return false;
		}
		
		
	}
?>