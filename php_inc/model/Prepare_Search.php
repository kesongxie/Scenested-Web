<?php
	include_once MODEL_PATH.'User_Table.php';
	include_once MODEL_PATH.'Interest.php';

	class Prepare_Search{
		private $search_keyword = null;
		private $search_type = null; //how the user specified the search, interest, name,etc
		private $search_default_type = ['name','interest','event','photo','post'];
		private $seach_render_template = ['name'=>TEMPLATE_PATH_CHILD.'search_people.phtml'];
		private $search_mine = false;
		
		
		public function __construct($key_word = null, $type = 'name', $mine = false){
			if($mine){
				//search my interest
				$this->search_mine = true;
			}else{
				$this->search_keyword = $key_word;
			}
			if(in_array($type, $this->search_default_type)){
					$this->search_type = $type;
			}else{
					$this->search_type = 'name';
			}
		}
		
		public function getSearchResultMainBlock(){
			$user = new User_Table();
			$interest  = new Interest();
			$content = null;
			if($this->search_mine){
				if($this->search_type == 'name'){
					//search poeple whose name match the keyword
					$content = $this->getContentPeopleForMineInterestType();
				}else if($this->search_type == 'event'){
					$content =  $this->getContentEventForMineInterestType();
				}else if($this->search_type == 'post'){
					$content =  $this->getContentMomentForMineInterestType();
				}else if($this->search_type == 'photo'){
					$content =  $this->getContentPhotoForMineInterestType();
				}
			}else{
				if($this->search_type == 'name'){
					//search poeple whose name match the keyword
					$content = $this->getContentForNameType();
				}else if($this->search_type == 'interest'){
					$content = $this->getContentForInterestType();
				}else if($this->search_type == 'event'){
					$content = $this->getContentForEventType();
				}else if($this->search_type == 'post'){
					$content = $this->getContentForPostType();
				}else if($this->search_type == 'photo'){
					$content = $this->getContentForPhotoType();
				}
			}
			
			return $content;
		}
		
		
		
		
		
		public function getContentForNameType(){
			include_once 'Education.php';
			$educ = new Education();
			$user = new User_Table();
			$result_found = $user->returnMatchedUserBySearchkeyWord($this->search_keyword,-1);
			
			$interest  = new Interest();
			$content = null;
			$search_for_interest_block = null;
			$search_for_school_block = null;
			if($result_found !== false){
				include_once 'User_Profile_Picture.php';
				$profile = new User_Profile_Picture();
				$search_for_name_block = "";
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
 						$education = $educ->getEducationByUserId($u['id']);
						
 						
						ob_start();
 						include(TEMPLATE_PATH_CHILD.'user_profile.phtml');
 						$user_profile= ob_get_clean();
 						ob_start();
 						include(TEMPLATE_PATH_CHILD.'search_people_profile_wrapper.phtml');
 						$search_for_name_block .= ob_get_clean();
				}
				
			}else{
				$search_for = $this->search_keyword;
				$search_for_interest_block = $this->getContentForInterestType();
				if($search_for_interest_block === null){
					$search_for_school_block = $this->getContentForSchoolType();
 				}
 				ob_start();
				include(TEMPLATE_PATH_CHILD.'search_people_result_wrapper.phtml');
				$content = ob_get_clean();
			}
			return $content;
		}
		
		
		
		
		public function getContentForSchoolType(){
			$user = new User_Table();
			$interest  = new Interest();
			include_once 'Education.php';
			$educ = new Education();
			$result_found = $educ->returnMatchedUserForSchool($this->search_keyword);
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
						$rows = $interest->getInterestNameAndDescriptionForUser($u['id'], -1);
						$user_page_redirect =  USER_PROFILE_ROOT.$user->getUserAccessUrl($u['id']);
						$user_id = $u['id'];
	
						$result_array = array();
						$count = 0;	
						foreach($rows as $row){
							if(stripos($row['name'], $this->search_keyword) !== false || stripos($row['description'], $this->search_keyword) !== false){
								array_push($result_array, $row);
								unset($rows[$count]);
							}
							$count++;
						}
						
						$result_array = array_merge($result_array,$rows);
						$interest_list = '';
 						if($result_array !== false){
							$results = array_slice($result_array,0,2);
							$count = 1;
							foreach($results as $result){
								if($count == 2){
									$interest_list .= ' and '.$result['name'];
								}else{
									$interest_list .=  $result['name'];
								}
								$count++;
							}
 						}
 						
 						include_once 'Education.php';
						$educ = new Education();
						$education = $educ->getEducationByUserId($u['id']);
 						
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
			$result_found = $interest->returnMatchedUserBySearchkeyWord($this->search_keyword, -1);
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
						$rows = $interest->getInterestNameAndDescriptionForUser($u['id'], -1);
						$user_page_redirect =  USER_PROFILE_ROOT.$user->getUserAccessUrl($u['id']);
						$user_id = $u['id'];
	
						$result_array = array();
						$count = 0;	
						foreach($rows as $row){
							if(stripos($row['name'], $this->search_keyword) !== false || stripos($row['description'], $this->search_keyword) !== false){
								array_push($result_array, $row);
								unset($rows[$count]);
							}
							$count++;
						}
						
						$result_array = array_merge($result_array,$rows);
						$interest_list = '';
 						if($result_array !== false){
							$results = array_slice($result_array,0,2);
							$count = 1;
							foreach($results as $result){
								if($count == 2){
									$interest_list .= ' and '.$result['name'];
								}else{
									$interest_list .=  $result['name'];
								}
								$count++;
							}
 						}
 						
 						include_once 'Education.php';
						$educ = new Education();
						$education = $educ->getEducationByUserId($u['id']);
 						
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
			$rows = $interest_activity->returnMatchedEventBySearchkeyWord($this->search_keyword);
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
			}	
			ob_start();
			include(TEMPLATE_PATH_CHILD.'search_event.phtml');
			$content= ob_get_clean();
			
			
			return $content;	
		}
		
		
		public function getSearchBarResult(){
			include_once 'User_Profile_Picture.php';
			$interest  = new Interest();
			$profile = new User_Profile_Picture();
			$user = new User_Table();
			$result_found = array();
			$user_found = $user->returnMatchedUserBySearchkeyWord($this->search_keyword, 8);
			$serach_result_user_id_array = array();
			if($user_found !== false){
				$result_found = $user_found;
				foreach($user_found as $u){
					array_push($serach_result_user_id_array,$u['id']);
				}			
			}	
			
			$count = sizeof($result_found);
			$interest_found = $interest->returnMatchedUserBySearchkeyWord($this->search_keyword, 8);
			if($interest_found !== false){
				foreach($interest_found as $i){
					if($count <= 8){
						if(!in_array($i['id'], $serach_result_user_id_array)){
							array_push($result_found,$i);
							$count++;
						}
					}else{
						break;
					}
				}
			}
			
			ob_start();
			include(TEMPLATE_PATH_CHILD.'search_bar_result.phtml');
			$content = ob_get_clean();
			return $content;
		}
		
		
		public function getContentForPostType(){
			include_once PHP_INC_MODEL_ROOT_REF.'Interest_Activity.php';
			$interest_activity = new Interest_Activity();
			$rows = $interest_activity->returnMatchedPostBySearchkeyWord($this->search_keyword);
			$content = null;
			if($rows !== false){
				$left_content = "";
				$right_content = "";
				$count = 0;
				foreach($rows as $row){
					$content = $interest_activity->getMomentInterestActivityBlockByActivityId($row['activity_id']);
					if($count++ % 2 == 0){
						$left_content.= $content;
					}else{
						$right_content.= $content;
					}
				}
			}	
			ob_start();
			include(TEMPLATE_PATH_CHILD.'search_post.phtml');
			$content= ob_get_clean();
			return $content;	
		}
		
		public function getContentForPhotoType(){
			include_once PHP_INC_MODEL_ROOT_REF.'Interest_Activity.php';
			include_once PHP_INC_MODEL_ROOT_REF.'User_Media_Base.php';
			$interest_activity = new Interest_Activity();
			$media_base = new User_Media_Base();
			$rows = $interest_activity->returnMatchedPhotoBySearchkeyWord($this->search_keyword);
			$content = null;
			if($rows !== false){
				$left_content = "";
				$right_content = "";
				$count = 0;
				foreach($rows as $row){
					$content= $media_base->renderPhotoStreamByPictureUrl($row['picture_url'], $row['user_id'],$row['source_from'], $row['hash']);
					if($content !== false){
						if($count++ % 2 == 0){
							$left_content.= $content;
						}else{
							$right_content.= $content;
						}
					}
				}
			}
			ob_start();
			include(TEMPLATE_PATH_CHILD.'double_column_photo_stream_block.phtml');
			$content= ob_get_clean();
			return $content;
			
		}
		
		
		public function getContentPeopleForMineInterestType(){
			$user = new User_Table();
			$interest  = new Interest();
			$result_found = $interest->returnMatchedUserForMineInterest();
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
 						include_once 'Education.php';
						$educ = new Education();
						$education = $educ->getEducationByUserId($u['id']);
 						
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
		
		
		public function getContentEventForMineInterestType(){
			include_once MODEL_PATH.'Interest_Activity.php';
			$interest_activity  = new Interest_Activity();
			$rows = $interest_activity->returnMatchedEventForMineInterest();
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
			}	
			ob_start();
			include(TEMPLATE_PATH_CHILD.'search_mine_event.phtml');
			$content= ob_get_clean();
			return $content;	
		}
		
		
		public function getContentMomentForMineInterestType(){
			include_once MODEL_PATH.'Interest_Activity.php';
			$interest_activity  = new Interest_Activity();
			$rows = $interest_activity->returnMatchedMomentForMineInterest();
			$content = null;
			if($rows !== false){
				$left_content = "";
				$right_content = "";
				$count = 0;
				foreach($rows as $row){
					$content = $interest_activity->getMomentInterestActivityBlockByActivityId($row['activity_id']);
					if($count++ % 2 == 0){
						$left_content.= $content;
					}else{
						$right_content.= $content;
					}
				}
			}	
			ob_start();
			include(TEMPLATE_PATH_CHILD.'search_mine_post.phtml');
			$content= ob_get_clean();
			return $content;	
		}
		
		
		public function getContentPhotoForMineInterestType(){
			include_once MODEL_PATH.'Interest.php';
			include_once PHP_INC_MODEL_ROOT_REF.'User_Media_Base.php';
			$media_base = new User_Media_Base();
			$interest  = new Interest();
			$rows = $interest->returnMatchedPhotoForMineInterest();
			$content = null;
			if($rows !== false){
				$left_content = "";
				$right_content = "";
				$count = 0;
				foreach($rows as $row){
					$content= $media_base->renderPhotoStreamByPictureUrl($row['picture_url'], $row['user_id'], $row['source_from'], $row['hash']);
					if($content !== false){
						if($count++ % 2 == 0){
							$left_content.= $content;
						}else{
							$right_content.= $content;
						}
					}
				}
			}
			ob_start();
			include(TEMPLATE_PATH_CHILD.'double_column_photo_stream_block.phtml');
			$content= ob_get_clean();
			return $content;
		}
		
		
		
		
		
		
		
	}		



?>