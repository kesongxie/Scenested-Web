<?php
	include_once MODEL_PATH.'User_Table.php';
	include_once MODEL_PATH.'Interest.php';

	class Prepare_Search{
		private $search_keyword = null;
		private $search_type = null; //how the user specified the search, interest, name,etc
		private $search_default_type = ['name','interest','event','photo','moment'];
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
				}else if($this->search_type == 'moment'){
					$content =  $this->getContentMomentForMineInterestType();
				}else if($this->search_type == 'photo'){
					$content =  $this->getContentPhotoForMineInterestType(10);
				}
			}else{
				if($this->search_type == 'name'){
					//search poeple whose name match the keyword
					$content = $this->getContentForNameType(4);
				}else if($this->search_type == 'interest'){
					$content = $this->getContentForInterestType(4);
				}else if($this->search_type == 'event'){
					$content = $this->getContentForEventType(4);
				}else if($this->search_type == 'moment'){
					$content = $this->getContentForMomentType(4);
				}else if($this->search_type == 'photo'){
					$content = $this->getContentForPhotoType(20);
				}
			}
			
			return $content;
		}
		
		
		
		public function getContentForNameType($rows_need_to_fetch = 4){
			include_once MODEL_PATH.'User_Table.php';
			$user =  new User_Table();
			$result_found = $this->getresultFoundResultForPeople($rows_need_to_fetch);
			ob_start();
			include(TEMPLATE_PATH_CHILD.'search_people_result_wrapper.phtml');
			$content = ob_get_clean();
			return $content;
		}
		
		public function getresultFoundResultForPeople($rows_need_to_fetch = 4, $exclusive_list = "'-1'"){
			include_once MODEL_PATH.'Interest.php';
			include_once MODEL_PATH.'Education.php';
			$educ = new Education();
			$user = new User_Table();
			$result_found = array();
			$result_from_name = $user->returnMatchedUserBySearchkeyWord($this->search_keyword,$rows_need_to_fetch, $exclusive_list);
			$interest  = new Interest();
			
			
			if($exclusive_list != "'-1'"){
				$_SESSION['loaded_keyword_people_list'] = $exclusive_list.',';
			}else{
				$_SESSION['loaded_keyword_people_list'] = "'-1'";
			}
			
			if($result_from_name !== false){
				$result_found = array_merge($result_found, $result_from_name);
				$rows_need_to_fetch -= sizeof($result_found);
				//make sure not to load repeated user
				if($_SESSION['loaded_keyword_people_list'] == "'-1'"){
					$_SESSION['loaded_keyword_people_list'] = '';
				}
				foreach($result_from_name as $u){
					$_SESSION['loaded_keyword_people_list'].="'".$u['id']."',";
				}
			}
			
			//if it's still needed to load content from interest
			if($rows_need_to_fetch > 0){
				//once this is ture, that means 
				$result_from_interest = $interest->returnMatchedUserBySearchkeyWord($this->search_keyword, $rows_need_to_fetch, trim($_SESSION['loaded_keyword_people_list'],','));
				if($result_from_interest !== false){
					$result_found = array_merge($result_found, $result_from_interest );
					$rows_need_to_fetch -= sizeof($result_from_interest);
					if($_SESSION['loaded_keyword_people_list'] == "'-1'"){
						$_SESSION['loaded_keyword_people_list'] = '';
					}
					foreach($result_from_interest as $u){
						$_SESSION['loaded_keyword_people_list'].="'".$u['id']."',";
					}
				}
			}
		
			
			
			
			//if it's still needed to load content from school
			if($rows_need_to_fetch > 0){
				$result_from_school = $educ->returnMatchedUserForSchool($this->search_keyword,$rows_need_to_fetch, trim($_SESSION['loaded_keyword_people_list'],',') );
				if($result_from_school !== false){
					$result_found = array_merge($result_found, $result_from_school);
					$rows_need_to_fetch -= sizeof($result_from_school);
					if($_SESSION['loaded_keyword_people_list'] == "'-1'"){
						$_SESSION['loaded_keyword_people_list'] = '';
					}
					foreach($result_from_school as $u){
						$_SESSION['loaded_keyword_people_list'].="'".$u['id']."',";
					}
				}
			}
			$_SESSION['loaded_keyword_people_list'] = trim($_SESSION['loaded_keyword_people_list'],',');
			
			return (!empty($result_found))?$result_found:false;
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
						$interest_list = '';
						if($rows !== false){
							foreach($rows as $row){
								if(stripos($row['name'], $this->search_keyword) !== false || stripos($row['description'], $this->search_keyword) !== false){
									array_push($result_array, $row);
									unset($rows[$count]);
								}
								$count++;
							}
							$result_array = array_merge($result_array,$rows);
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
 						}
 						include_once 'Education.php';
						$educ = new Education();
						$education = $educ->getEducationByUserId($u['id']);
 						
						ob_start();
 						include(TEMPLATE_PATH_CHILD.'user_profile.phtml');
 						$user_profile= ob_get_clean();
 						ob_start();
 						include(TEMPLATE_PATH_CHILD.'friend_profile_wrapper.phtml');
 						$content .= ob_get_clean();
					}
			}
			return $content;
		}
		
		
	
		public function getContentForInterestType($limit = 4){
			$user = new User_Table();
			$interest  = new Interest();
			$result_found = $interest->returnMatchedUserBySearchkeyWord($this->search_keyword, $limit);
			$content = false;
			if($result_found !== false){
				include_once 'User_Profile_Picture.php';
				$profile = new User_Profile_Picture();
				$content = "";
				$_SESSION['loaded_keyword_people_list'] = '';
				foreach($result_found as $u){
						$_SESSION['loaded_keyword_people_list'].="'".$u['id']."',";
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
 						include(TEMPLATE_PATH_CHILD.'friend_profile_wrapper.phtml');
 						$content .= ob_get_clean();
					}
				$_SESSION['loaded_keyword_people_list'] = trim($_SESSION['loaded_keyword_people_list'], ',');
			}
			ob_start();
			include(TEMPLATE_PATH_CHILD.'search_interest_people_result_wrapper.phtml');
			$content = ob_get_clean();
			return $content;
			
		}
		
		public function getContentForEventType($rows_need_to_fetch = 4){
			include_once MODEL_PATH.'Interest_Activity.php';
			$interest_activity = new Interest_Activity();
			$rows = $interest_activity->returnMatchedEventBySearchkeyWord($this->search_keyword, $rows_need_to_fetch);
			$content = null;
			$left_content = "";
			$right_content = "";
			if($rows !== false){
				$count = 0;
				foreach($rows as $row){
					$content = $interest_activity->getEventInterestActivityBlockByActivityId($row['activity_id'], true);
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
		
				
		public function getContentForMomentType($rows_need_to_fetch = 4){
			include_once PHP_INC_MODEL_ROOT_REF.'Interest_Activity.php';
			$interest_activity = new Interest_Activity();
			$rows = $interest_activity->returnMatchedMomentBySearchkeyWord($this->search_keyword, $rows_need_to_fetch);
			$content = null;
			$feed_id_list = "";
			$left_content = "";
			$right_content = "";
			if($rows !== false){
				$count = 0;
				foreach($rows as $row){
					$content = '';
					$feed_id_list .= $row['activity_id'].',';
					$content = $interest_activity->getMomentInterestActivityBlockByActivityId($row['activity_id'], true);
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
		
	
		public function getContentForPhotoType($rows_need_to_fetch = -1 ){
			$rows = $this->getResultForPhoto($rows_need_to_fetch);
			return $this->renderPhotosByResource($rows);
		}
			
		public function getResultForPhoto($rows_need_to_fetch = 4, $last_m = MAX_PHOTO_BOUND , $last_e = MAX_PHOTO_BOUND){
			include_once MODEL_PATH.'Interest_Activity.php';
			$interest_activity = new Interest_Activity();
			include_once MODEL_PATH.'User_Media_Base.php';
			$media_base = new User_Media_Base();
			$rows = array();
			
			$rows_from_keyword = $interest_activity->returnMatchedPhotoBySearchkeyWord($this->search_keyword, $rows_need_to_fetch, $last_m, $last_e);
			if($rows_from_keyword !== false){
				$rows_need_to_fetch -= sizeof($rows_from_keyword);
				$rows = $rows_from_keyword;
			}
			if($rows_need_to_fetch > 0){
				$rows_from_school = $media_base->returnPhotoBySchoolKeyWord($this->search_keyword, $rows_need_to_fetch, $last_m, $last_e );
				if($rows_from_school !== false){
					$rows = array_merge($rows, $rows_from_school);
				}
			}
			return !empty($rows)?$rows:false;
		}	
			
	
	
		
		
		public function getContentEventForMineInterestType(){
			include_once MODEL_PATH.'Interest_Activity.php';
			$interest_activity  = new Interest_Activity();
			$rows = $interest_activity->returnMatchedEventForMineInterest(2);
			return $this->renderEventsByResource($rows);
		}
		
		
		
		public function renderPhotosByResource($rows, $scroll_load = false){
			include_once MODEL_PATH.'User_Media_Base.php';
			$media_base = new User_Media_Base();
			$left_content = "";
			$right_content = "";	
			if($rows !== false){
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
			if($scroll_load){
				include(TEMPLATE_PATH_CHILD.'loading_feed_wrapper.phtml');
			}else{
				include(TEMPLATE_PATH_CHILD.'double_column_photo_stream_block.phtml');
			}
			$content = ob_get_clean();
			return $content;
		}
		
		
		
		public function renderMomentsByResource($rows, $scroll_load = false){
			include_once MODEL_PATH.'Interest_Activity.php';
			$interest_activity  = new Interest_Activity();
			if($rows !== false){
				$count = 0;
				$left_content = "";
				$right_content = "";
				foreach($rows as $row){
					$content = $interest_activity->getMomentInterestActivityBlockByActivityId($row['activity_id'], true);
					if($count++ % 2 == 0){
						$left_content.= $content;
					}else{
						$right_content.= $content;
					}
				}
				ob_start();
				if($scroll_load){
					include(TEMPLATE_PATH_CHILD.'loading_feed_wrapper.phtml');
				}else{
					include(TEMPLATE_PATH_CHILD.'search_post.phtml');
				}
				$content = ob_get_clean();
				return $content;
			}
			return false;
		}
		
		public function renderEventsByResource($rows, $scroll_load = false){
			include_once MODEL_PATH.'Interest_Activity.php';
			$interest_activity  = new Interest_Activity();
			if($rows !== false){
				$left_content = "";
				$right_content = "";
				$count = 0;
				foreach($rows as $row){
					$content = $interest_activity->getEventInterestActivityBlockByActivityId($row['activity_id'], true);
					if($count++ % 2 == 0){
						$left_content.= $content;
					}else{
						$right_content.= $content;
					}
				}
			}
			if($scroll_load ){
				if($rows !== false){
					ob_start();
					include(TEMPLATE_PATH_CHILD.'loading_feed_wrapper.phtml');
					$content = ob_get_clean();
					return $content;
				}else{
					return false;
				}
			}else{
				
			}
			ob_start();
			include(TEMPLATE_PATH_CHILD.'search_mine_event.phtml');
			$content = ob_get_clean();
			return $content;
		}
		
		public function getContentPeopleForMineInterestType(){
			$user = new User_Table();
			$interest  = new Interest();
			$result_found = $interest->returnMatchedUserForMineInterest(4);
			$content = false;
			if($result_found !== false){
				include_once 'User_Profile_Picture.php';
				$profile = new User_Profile_Picture();
				$content = "";
				foreach($result_found as $u){
					$content .= $user->returnUserAvatorByResource($u);
				}
			}
			ob_start();
			include(TEMPLATE_PATH_CHILD.'search_mine_interest_people.phtml');
			$content= ob_get_clean();
			return $content;	
			
			
		}
		
		public function getContentMomentForMineInterestType(){
			include_once MODEL_PATH.'Interest_Activity.php';
			$interest_activity  = new Interest_Activity();
			$rows = $interest_activity->returnMatchedMomentForMineInterest(2);
			if($rows !== false){
				$left_content = "";
				$right_content = "";
				$count = 0;
				foreach($rows as $row){
					$content = $interest_activity->getMomentInterestActivityBlockByActivityId($row['activity_id'], true);
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
		
		
		public function getContentPhotoForMineInterestType($limit = -1){
			include_once MODEL_PATH.'Interest.php';
			include_once MODEL_PATH.'User_Media_Base.php';
			$media_base = new User_Media_Base();
			$interest  = new Interest();
			$rows = $interest->returnMatchedPhotoForMineInterest($limit);
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
			include(TEMPLATE_PATH_CHILD.'search_mine_photo.phtml');
			$content= ob_get_clean();
			return $content;
		}
		
		
		public function getSearchBarResult(){
			include_once 'User_Profile_Picture.php';
			$interest  = new Interest();
			include_once 'Education.php';
			$profile = new User_Profile_Picture();
			$user = new User_Table();
			$result_found = array();
			$user_found = $user->returnMatchedUserBySearchkeyWord($this->search_keyword, 5);
			$serach_result_user_id_array = array();
			if($user_found !== false){
				$result_found = $user_found;
				foreach($user_found as $u){
					array_push($serach_result_user_id_array,$u['id']);
				}			
			}	
			
			$count = sizeof($result_found);
			$interest_found = $interest->returnMatchedUserBySearchkeyWord($this->search_keyword, 5-$count);
			if($interest_found !== false){
				foreach($interest_found as $i){
					if($count <= 5){
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
		
		public function loadMoreContentPeople(){
			$user = new User_Table();
			$interest  = new Interest();
			$result_found = false;
			if($this->search_mine){
				$result_found = $interest->loadMoreMatchedUserForMineInterest(4);
			}else{
				//search poeple whose name match the keyword
				if($this->search_type == 'name'){
					//search poeple whose name match the keyword
					$result_found = $this->getresultFoundResultForPeople(4, $_SESSION['loaded_keyword_people_list']);
				}else if($this->search_type == 'interest'){
					$result_found = $interest->loadMoreMatchedUserForKeyWord($this->search_keyword, 4);
				}
			
			}
			$content = false;
			if($result_found !== false){
				$content = "";
				foreach($result_found as $u){
					ob_start();
					$content.= $user->returnUserAvatorByResource($u);
				}
				return $content;
			}
			return false;
		}
		
		public function loadMoreContentEvent(){
			include_once MODEL_PATH.'Interest_Activity.php';
			$interest_activity = new Interest_Activity();
			$result_found = false;
			if($this->search_mine){
				$result_found = $interest_activity->loadMoreMatchedEventForMineInterest();
			}else{
				//search poeple whose name match the keyword
				$result_found = $interest_activity->returnMatchedEventBySearchkeyWord($this->search_keyword, 4, $_SESSION['loaded_activity_list']);
			}
			return $this->renderEventsByResource($result_found, true);
		}
		
		
		public function loadMoreContentMoment(){
			include_once MODEL_PATH.'Interest_Activity.php';
			$interest_activity = new Interest_Activity();
			$result_found = false;
			if($this->search_mine){
				$result_found = $interest_activity->returnMatchedMomentForMineInterest(4, $_SESSION['loaded_activity_list']);
			}else{
				//search poeple whose name match the keyword
				$result_found = $interest_activity->returnMatchedMomentBySearchkeyWord($this->search_keyword, 4, $_SESSION['loaded_activity_list']);
			}
			return $this->renderMomentsByResource($result_found, true);
		}

		
		public function loadMoreContentPhoto($l_m, $r_m, $l_e, $r_e){
			include_once MODEL_PATH.'User_Media_Base.php';
			$base = new User_Media_Base();
			$last_m = $base->getLastLoadedStreamId($l_m, $r_m, 'm');
			$last_e = $base->getLastLoadedStreamId($l_e, $r_e, 'e');
			if($this->search_mine){
				include_once MODEL_PATH.'Interest.php';
				$interest  = new Interest();
				$rows = $interest->returnMatchedPhotoForMineInterest(6, $last_m, $last_e);
			}else{
				$rows = $this->getResultForPhoto(6, $last_m, $last_e);
			}
			if($rows !== false){
				return $this->renderPhotosByResource($rows, true);
			}else{
				return false;
			}
		}
		
	}		
	



?>