<?php
	include_once PHP_INC_MODEL_ROOT_REF.'User_Table.php';
	include_once PHP_INC_MODEL_ROOT_REF.'Interest.php';

	class Prepare_Search{
		private $search_keyword = null;
		private $search_type = null; //how the user specified the search, interest, name,etc
		private $search_default_type = ['name','interest','event','photo','post'];
		private $seach_render_template = ['name'=>TEMPLATE_PATH_CHILD.'search_people.phtml'];
		public function __construct($key_word, $type = 'name'){
			$this->search_keyword = $key_word;
			if(in_array($type, $this->search_default_type)){
				$this->search_type = $type;
			}else{
				$this->search_type = 'name';
			}
		}
		
		public function getSearchResultMainBlock(){
			$user = new User_Table();
			$interest  = new Interest();
			
			if($this->search_type == 'name'){
				//search poeple whose name match the keyword
				$content = $this->getContentForNameType();
			}else if($this->search_type == 'interest'){
				$content = $this->getContentForInterestType();
			}else if($this->search_type == 'event'){
				$content = $this->getContentForEventType();
			}
			
			return $content;
		}
		
		public function getContentForNameType(){
			$user = new User_Table();
			$result_found = $user->returnMatchedUserBySearchkeyWord($this->search_keyword);
			$interest  = new Interest();
			$content = null;
			if($result_found !== false){
				include_once 'User_Profile_Picture.php';
				$profile = new User_Profile_Picture();
				$content = "";
				foreach($result_found as $u){
						$profile_pic = $profile->getLatestProfileImageForUser($u['id']);
						$cover_pic =  $user->getLatestCoverForuser($u['id']);
						$fullname = $u['fullname'];
						$hash = $u['hash'];
						$rows = $interest->getInterestNameForUser($u['id'], 2);
						$user_page_redirect =  USER_PROFILE_ROOT.$user->getUserAccessUrl($u['id']);
						$user_id = $u['id'];
						$result_array = array();
						
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
 						
 						
						ob_start();
 						include(TEMPLATE_PATH_CHILD.'user_profile.phtml');
 						$user_profile= ob_get_clean();
 						ob_start();
 						include(TEMPLATE_PATH_CHILD.'search_people_profile_wrapper.phtml');
 						$content .= ob_get_clean();
					}
			}
			return $content;
		}
		
	
		public function getContentForInterestType(){
			$user = new User_Table();
			$interest  = new Interest();
			$result_found = $interest->returnMatchedUserBySearchkeyWord($this->search_keyword);
			$content = null;
			if($result_found !== false){
				include_once 'User_Profile_Picture.php';
				$profile = new User_Profile_Picture();
				$content = "";
				foreach($result_found as $u){
						$profile_pic = $profile->getLatestProfileImageForUser($u['id']);
						$cover_pic =  $user->getLatestCoverForuser($u['id']);
						$fullname = $u['fullname'];
						$hash = $u['hash'];
						$rows = $interest->getInterestNameForUser($u['id'], -1);
						$user_page_redirect =  USER_PROFILE_ROOT.$user->getUserAccessUrl($u['id']);
						$user_id = $u['id'];
	
						$result_array = array();
						$count = 0;	
						foreach($rows as $row){
							if(stripos($row['name'], $this->search_keyword) !== false){
								array_push($result_array, $row);
								unset($rows[$count]);
							}
							$count++;
						}
						array_push($result_array,current($rows));
						
						
 						$interest_list = '';
 						if($result_array !== false){
 							$count = 1;
 							foreach($result_array as $row){
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
						ob_start();
 						include(TEMPLATE_PATH_CHILD.'user_profile.phtml');
 						$user_profile= ob_get_clean();
 						
 						
 						
 						ob_start();
 						include(TEMPLATE_PATH_CHILD.'search_people_profile_wrapper.phtml');
 						$content .= ob_get_clean();
					}
			}
			return $content;
		}
		
		public function getContentForEventType(){
			include_once PHP_INC_MODEL_ROOT_REF.'Interest_Activity.php';
			$interest_activity = new Interest_Activity();
			$rows = $interest_activity->returnMatchedUserBySearchkeyWord($this->search_keyword);
			$content = null;
			if($rows !== false){
				$left_content = "";
				$right_content = "";
				$count = 0;
				foreach($rows as $row){
					$content = $interest_activity->getEventInterestActivityBlockByActivityId($row['activity_id']);
					if($count++ % 2 == 0){
						$left_content.= $content;
					}else{
						$right_content.= $content;
					}
				}
				
				ob_start();
				include(TEMPLATE_PATH_CHILD.'search_event.phtml');
				$content= ob_get_clean();
			}
			
			return $content;	
		}
		
		
	}		



?>