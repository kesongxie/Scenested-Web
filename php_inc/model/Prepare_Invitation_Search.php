<?php
	class Prepare_Invitation_Search{
		public function getInvitationSearchContact($key_word, $active_interest_key = -1, $post_key){
			if($active_interest_key == -1){
				$user_found = $this->returnInvitationSearchForAllFriends($key_word);
			}else{
				$user_found = $this->returnInvitationSearchByInterestId($key_word, $active_interest_key);
			}
			include_once 'Interest_Activity.php';
			$activity = new Interest_Activity();
			
			return $activity->renderInvitationContactBlockByResource($user_found,$post_key);
			
		}
		
		
		public function returnInvitationSearchForAllFriends($key_word){
			include_once 'User_In_Interest.php';
			$in = new User_In_Interest();
			return $in->returnInvitationSearchForAllFriends($key_word);
		}
		
		
		public function returnInvitationSearchByInterestId($key_word, $active_interest_key){
			include_once 'User_In_Interest.php';
			$in = new User_In_Interest();
			return $in->returnInvitationSearchByInterestId($key_word,$active_interest_key );
		}
		
		
		
		
	}
		
?>