<?php
	include_once MODEL_PATH.'Core_Table.php';
	include_once MODEL_PATH.'User_Interest_Label_Image.php';
	
	class Interest extends Core_Table{
		private  $table_name = "interest";
		private $interest_activity = null;
		private $suggest_friend_block = TEMPLATE_PATH_CHILD."suggest_friend_block.phtml";
		private $initial_friend_block =  TEMPLATE_PATH_CHILD."initial_profile_friend_block.phtml";

		public function __construct(){
			parent::__construct($this->table_name);
			include_once 'Interest_Activity.php';
			$this->interest_label_image = new User_Interest_Label_Image();
			$this->interest_activity = new Interest_Activity();
		}
		
		
		public function isUserHasInterest($user_id){
			return $this->isRowForUserExists($user_id);
		}

		public function getInterestNameForUser($user_id, $limit_row){
			if($limit_row > 0){
				return $this->getRowsColumnBySelector('name', 'user_id', $user_id, $limit_row);
			}else{
				return $this->getAllRowsColumnBySelector('name', 'user_id', $user_id);
			}
		}
		
		public function getInterestNameAndDescriptionForUser($user_id, $limit_row){
			if($limit_row > 0){
				return $this->getRowsMultipleColumnsBySelectorWithFilter(array('name','description'), 'user_id', $user_id, $limit_row, true);
			}else{
				return $this->getAllRowsMultipleColumnsBySelector(array('name','description'), 'user_id', $user_id, true);
			}
		}
		
		
		public function getInterestIdByNameForUser($name, $user_id){
			 return $this->getColumnBySelectorForUser('id','name',$name,$user_id);
		}
		
		

		//return the result set of the first row for the given user id
		public function getUserFirstInterestByUserId($user_id){
			$select_columns = array('id','user_id','name','description','experience');
			return $this->getFirstRowMultipleColumnsByUserId($select_columns, $user_id);
		}
		
		//return the result set of the last row for the given user id
		public function getUserLastInterestByUserId($user_id){
			$select_columns = array('id','user_id','name','description','experience');
			return $this->getLastRowMultipleColumnsByUserId($select_columns, $user_id);
		}
		
		//return teh set of the interest row for the given id
		public function getUserInterestBlockByInterestId($interest_id){
			$select_columns = array('id','user_id','name','description','experience');
			$interest_row = $this->getMultipleColumnsById($select_columns, $interest_id);
			return $this->loadInterestBlockByInterestResource($interest_row);
		}
		
		
		
		public function addInterestForUser($user_id, $name, $description, $experience, $label_image_file, $with_return_render = true){
			$stmt = $this->connection->prepare("INSERT INTO `$this->table_name` (`user_id`,`name`,`description`,`experience`,`create_time`) VALUES(?, ?, ?, ?, ?)");
			$create_time = date('Y-m-d H:i:s');
			$name =  ucwords(strtolower($name));
			$stmt->bind_param('issis',$user_id, $name, $description, $experience, $create_time);
			if($stmt->execute()){
				$interest_id = $this->connection->insert_id;
				
					$label_image_url = "";
					if($label_image_file !== null){
						//upload image
						$hash = $this->interest_label_image->generateUniqueHash();
						$label_image_url = $this->interest_label_image->uploadMediaForAssocColumn($label_image_file,$user_id, $hash,'interest_id', $interest_id);
						if($label_image_url === false){
							$this->deleteRowById($interest_id);
							$stmt->close();
							return false;
						}
					}
					$stmt->close();
					if($with_return_render){
						$main_content = $this->initContentForInterest($user_id,false); //main-block
						$side_content = $this->getInterestLabelByInterestId($interest_id, true, 'interests');
						ob_start();
						include(TEMPLATE_PATH_CHILD.'new_interest.phtml');
						$content = ob_get_clean();
						return $content;
					}
				return true;
			}
			return false;
		}
		
		
		public function updateInterestForUserByInterestId($interest_id, $user_id, $description, $experience, $label_image_file){
			$user_id_for_interest_id = $this->getColumnById('user_id',$interest_id);
			if($user_id_for_interest_id !== false && $user_id_for_interest_id == $user_id){
				//the same, then allow editing
				if($description !== false){
					$this->setColumnById('description', $description, $interest_id);
				}
				if($experience !== false){
					$this->setColumnById('experience', $experience, $interest_id);
				}
				
				
				if($label_image_file != null){
					$old_image_url = $this->interest_label_image->getLabelImageUrlByInterestId($interest_id);
					$old_image_row_id =  $this->interest_label_image->getLabelImageFirstRowIdByInterestId($interest_id);
					$label_image_url = $this->interest_label_image->uploadInterestLabelImage($label_image_file,$user_id, $interest_id);
					
					if($label_image_url === false){
						$stmt->close();
						return false;
					}
					include_once '../php_inc/File_Manager.php';
					$flile_m = new File_Manager();
					//remove the old record after successfully update the new media file
					$flile_m->removeMediaFileForUser($old_image_url, $user_id);
					$this->interest_label_image->deleteRowById($old_image_row_id);
					return true;
				}
			}
			echo   $this->connection->error;
			return false;
		}
		
		
		
	
		public function interestExistForUser($interest_name, $user_id){
			return $this->checkColumnValueExistForUser('name',$interest_name, $user_id);
		}
		
		/*
			return array of content, left and right content
			$firstInterest set to true if the function return first interest
			otherwise return the last interest
		*/
		
		public function initContentForInterest($user_id, $firstInterest){
			if($firstInterest){
				$interestRow = $this->getUserFirstInterestByUserId($user_id);
			}else{
				$interestRow = $this->getUserLastInterestByUserId($user_id);
			}
			return $this->loadInterestBlockByInterestResource($interestRow);
			
		}
		
		public function getInterestUserIdByInterestId($interest_id){
			return $this->getColumnById('user_id',$interest_id);
		}
		
		public function getInterestNameByInterestId($interest_id){
			return $this->getColumnById('name',$interest_id);
		}
		
		public function getInterestDescriptionByInterestId($interest_id){
			return $this->getColumnById('description',$interest_id);
		}
		
		public function getInterestExperienceByInterestId($interest_id){
			return $this->getColumnById('experience',$interest_id);
		}
		
		
		public function loadInterestBlockByInterestResource($interest){
			if($interest !== false){
				include_once 'User_Table.php';
				$user = new User_Table();
				$access_url = $user->getUserAccessUrl($interest['user_id']);
				$url_to_friend = USER_PROFILE_ROOT.$access_url.'/friends/'.strtolower($interest['name']);
				//get interest profile
				$left_content = "";
				$right_content = "";
				$labelImage = $this->interest_label_image->hasLabelImageForInterest($interest['id']);
				$experience = $this->translateExperienceByNumber($interest['experience']);
				ob_start();
				include(TEMPLATE_PATH_CHILD.'interest_profile.phtml');
				$interest_profile = ob_get_clean();
				$left_content = $interest_profile;
				//end getting interest profile
				
				$count = 1;
				$idCollection = $this->interest_activity->getActivityIdCollectionByInterestId($interest['id'], 10, 0);
				if($idCollection !== false && sizeof($idCollection) > 0){
					foreach($idCollection as $row ){
						if(++$count % 2 == 0){
							$right_content.=$this->interest_activity->getInterestActivityBlockByActivityId($row['id']);
						}else{
							$left_content.=$this->interest_activity->getInterestActivityBlockByActivityId($row['id']);
						}
					}
				}
				ob_start();
				include(TEMPLATE_PATH_CHILD.'interest_unit.phtml');
				$content = ob_get_clean();
				return $content;
			}
			return false;
		}
		
		
		
		
		
		
		
		public function translateExperienceByNumber($exp){
			if($exp >= 0){
				$experience= "";
				if($exp == 0){
					$experience = "Less than 1 year";
				}else if($exp == 1){
					$experience = "1 Year";
				}else if($exp >=2 && $exp<=10){
					$experience = $exp." Years";
				}else{
					$experience = "More than 10 years";
				}
				return $experience;
			}
			return false;
		}
		
		public function getUserInterestsLabel($user_id, $session = false){
			$stmt = $this->connection->prepare("
			SELECT interest.name, interest.id, user_interest_label_image.picture_url
			FROM interest 
			LEFT JOIN user_interest_label_image
			ON interest.id=user_interest_label_image.interest_id AND interest.user_id = user_interest_label_image.user_id  WHERE interest.user_id = ? ORDER BY `id` ASC
			");
			if($stmt){
				$stmt->bind_param('i',$user_id);
				if($stmt->execute()){
					 $result = $stmt->get_result();
					 if($result !== false && $result->num_rows >= 1){
						$row = $result->fetch_all(MYSQLI_ASSOC);
						include_once 'User_Media_Prefix.php';
						$prefix = new User_Media_Prefix();
						$media_prefix = $prefix->getUserMediaPrefix($user_id).'/';
						
						
						include_once 'User_Table.php';
						$user = new User_Table();
						foreach($row as &$r){
							if(isset($r['picture_url']) && $r['picture_url'] !== null){
								$url = U_IMGDIR.$media_prefix.$r['picture_url'];
								$url = (is_url_exist($url)?$url:DEFAULT_INTEREST_LABEL_IMAGE);
							}else{
								$url = DEFAULT_INTEREST_LABEL_IMAGE;
							}
							$access_url = $user->getUserAccessUrl($user_id);
							if($session !== false){
								$r['data_href'] = USER_PROFILE_ROOT.$access_url.'/'.$session.'/'.strtolower($r['name']);
							}else{
								$r['data_href'] = 'null';
							}
							$r['picture_url'] = $url;
						}
						$stmt->close();
						return $row;
					 }
				}
			}
			echo $this->connection->error;
			return false;
		}
		
		
		public function getInterestLabelByInterestId($interest_id, $set_to_active = false, $session){
			$label_image = new User_Interest_Label_Image();
			$url = $label_image->getLabelImageUrlByInterestId($interest_id);
			$name = $this->getInterestNameByInterestId($interest_id);
			
			include_once 'User_Table.php';
			$user = new User_Table();
			$user_id = $this->getInterestUserIdByInterestId($interest_id);
			if($user_id !== false){
				$access_url = $user->getUserAccessUrl($user_id);
				if($session !== false){
					$data_href = USER_PROFILE_ROOT.$access_url.'/'.$session.'/'.strtolower($name);
				}else{
					$data_href = 'null';
				}
			
			
				ob_start();
				include(TEMPLATE_PATH_CHILD.'inetrest_label.phtml');
				$content = ob_get_clean();
				return $content;
			}
			return false;
		}
		
		
		public function deleteInterestForUserByInterestId($user_id, $interest_id){
			if($this->deleteRowForUserById($user_id, $interest_id)){
				$this->interest_label_image->deleteLabelImageForUserByInterestId($user_id, $interest_id);
				$this->interest_activity->deleteAllActivityForUserByInterestId($user_id,$interest_id);
				include_once 'User_In_Interest.php';
				$user_in = new User_In_Interest();
				$user_in->deleteAllUserInByInterestId($user_id, $interest_id);
				include_once 'Interest_Request.php';
				$request = new Interest_Request();
				$request->deleteAllRequestByInterestId($user_id, $interest_id);
				
			}
		}
		
		public function isInterestEditableByUser($interest_id, $user_id){
			$user_id_for_interest_id = $this->getColumnById('user_id',$interest_id);
			if($user_id_for_interest_id !== false && $user_id_for_interest_id == $user_id){
				return true;
			}
			return false;
		}
		
		public function deletePostForUserByActivityKey($user_id, $key){
			return $this->interest_activity->deleteActivityForUserByActivityKey($user_id, $key);
		}
		
		public function getResultForUserByKeyWord($key_word, $limit = 2, $exculsive_list = "'-1'"){
			include_once 'Education.php';
			$edu = new Education();
			$school_id = $edu->getSchoolIdByUserId($_SESSION['id']);
			if($school_id !== false){
				if($limit > 0){
					$stmt = $this->connection->prepare("
					SELECT DISTINCT user.id,CONCAT(user.firstname,' ',user.lastname) AS fullname, user.id, user.unique_iden AS hash, user.user_access_url 
					FROM user 
					LEFT JOIN interest
					ON interest.user_id=user.id 
					LEFT JOIN education
					ON user.id = education.user_id
					WHERE (interest.name LIKE ? || interest.description LIKE ?) AND education.school_id = ? AND user.id  NOT IN($exculsive_list) 
					UNION 
					SELECT DISTINCT user.id,CONCAT(user.firstname,' ',user.lastname) AS fullname, user.id, user.unique_iden AS hash, user.user_access_url 
					FROM user 
					LEFT JOIN interest
					ON interest.user_id=user.id  WHERE (interest.name LIKE ? || interest.description LIKE ? ) AND user.id  NOT IN($exculsive_list) 
					LIMIT ?
					");
				}else{
					$stmt = $this->connection->prepare("
					SELECT DISTINCT user.id,CONCAT(user.firstname,' ',user.lastname) AS fullname, user.id, user.unique_iden AS hash, user.user_access_url 
					FROM user 
					LEFT JOIN interest
					ON interest.user_id=user.id 
					LEFT JOIN education
					ON user.id = education.user_id
					WHERE (interest.name LIKE ? || interest.description LIKE ?) AND education.school_id = ? AND user.id  NOT IN($exculsive_list) 
					UNION 
					SELECT DISTINCT user.id,CONCAT(user.firstname,' ',user.lastname) AS fullname, user.id, user.unique_iden AS hash, user.user_access_url 
					FROM user 
					LEFT JOIN interest
					ON interest.user_id=user.id  WHERE (interest.name LIKE ? || interest.description LIKE ?) AND user.id  NOT IN($exculsive_list) 
					");
				}
			}else{
				if($limit > 0){
				$stmt = $this->connection->prepare("
					SELECT DISTINCT user.id,CONCAT(user.firstname,' ',user.lastname) AS fullname, user.id, user.unique_iden AS hash, user.user_access_url 
					FROM user 
					LEFT JOIN interest
					ON interest.user_id=user.id  WHERE (interest.name LIKE ? || interest.description LIKE ?)  AND user.id  NOT IN($exculsive_list)  LIMIT ?
					");
				}else{
					$stmt = $this->connection->prepare("
					SELECT DISTINCT user.id,CONCAT(user.firstname,' ',user.lastname) AS fullname, user.id, user.unique_iden AS hash, user.user_access_url 
					FROM user 
					LEFT JOIN interest
					ON interest.user_id=user.id  WHERE (interest.name LIKE ? || interest.description LIKE ?) AND user.id  NOT IN($exculsive_list) 
					");
				}
			}
			if($stmt){
				$key_word = '%' .$key_word. '%';
				if($school_id !== false){
					if($limit > 0){
						$stmt->bind_param('ssissi',$key_word,$key_word, $school_id, $key_word,$key_word, $limit);
					}else{
						$stmt->bind_param('ssiss',$key_word,$key_word, $school_id, $key_word,$key_word);
					}
				}else{
					if($limit > 0){
						$stmt->bind_param('ssi',$key_word,$key_word, $limit);
					}else{
						$stmt->bind_param('ss',$key_word,$key_word);
					}
				}
				if($stmt->execute()){
					 $result = $stmt->get_result();
					if($result !== false && $result->num_rows >= 1){
						$stmt->close();
						return $result;
					}
				}
			}
			return false;
		}
		
		public function returnMatchedUserBySearchkeyWord($key_word, $limit = 2, $exclusive_list = "'-1'"){
			$result = $this->getResultForUserByKeyWord($key_word, $limit, $exclusive_list);
			if($result !== false){	
				$rows = $result->fetch_all(MYSQLI_ASSOC);
				return $rows;
			}
			echo $this->connection->error;
			return false;
		}
		
		
		// $list_type is either m or s, m stands for the main block, s stands for the side block
		public function returnMatchedUserForMineInterest($limit = -1, $exclue_existed_friend = false, $list_type="m"){
			$list = '';
			if($exclue_existed_friend){
				include_once 'User_In_Interest.php';
				$in = new User_In_Interest();
				$list = $in->getFriendPlainListForUser($_SESSION['id']);
			}
			if($list == ''){
				$list = "'-1'";
			}
			
			$result = $this->getResultForMineUser($limit, $list);
			if($result !== false){
				$rows = $result->fetch_all(MYSQLI_ASSOC);
				if($list_type == 'm'){
					$_SESSION['loaded_mine_people_List'] = '';
					foreach($rows as $row){
						$_SESSION['loaded_mine_people_List'].="'".$row['id']."',";
					}
					$_SESSION['loaded_mine_people_List'] = trim($_SESSION['loaded_mine_people_List'], ',');
				}
				return $rows;
			}
			
			echo $this->connection->error;
			return false;
		}
		
		/*
		public function returnMatchedPhotoForMineInterest(){
			$mine_interests = $this->getInterestNameForUser($_SESSION['id'], -1);
			$interest_like = '';
			if($mine_interests !== false){
				foreach($mine_interests as $interest){
 					$interest_like .= $interest['name'].'|';	
				}
				$interest_like = trim($interest_like,'|');
					$stmt = $this->connection->prepare("
			SELECT *
			FROM
			(
				SELECT 'm' AS `source_from`,  interest_activity.id as interest_activity_id, moment_photo.picture_url,moment_photo.user_id,  moment_photo.hash 
				FROM interest 
				LEFT JOIN interest_activity
				ON interest.id = interest_activity.interest_id 
				LEFT JOIN moment
				ON interest_activity.id = moment.interest_activity_id 
				LEFT JOIN moment_photo
				ON moment.id = moment_photo.moment_id 
				WHERE interest.name REGEXP ? 
			
				UNION 
			
				SELECT  'e' AS `source_from`, interest_activity.id as interest_activity_id,  event_photo.picture_url,event_photo.user_id, event_photo.hash
				FROM interest 
				LEFT JOIN interest_activity
				ON interest.id = interest_activity.interest_id 
				LEFT JOIN event
				ON interest_activity.id = event.interest_activity_id 
				LEFT JOIN event_photo
				ON event.id = event_photo.event_id
				WHERE interest.name REGEXP ? 
				
				UNION 
				
				SELECT 'm' AS `source_from`,  interest_activity.id as interest_activity_id, moment_photo.picture_url,moment_photo.user_id,  moment_photo.hash 
				FROM moment 
				LEFT JOIN interest_activity
				ON moment.interest_activity_id = interest_activity.id
				LEFT JOIN moment_photo
				ON moment.id = moment_photo.moment_id  WHERE  (moment.description REGEXP  ? || moment_photo.caption REGEXP ?)
			
				UNION 
				SELECT  'e' AS `source_from`, interest_activity.id as interest_activity_id,  event_photo.picture_url,event_photo.user_id, event_photo.hash
 				FROM event 
 				LEFT JOIN interest_activity
				ON event.interest_activity_id = interest_activity.id
 				LEFT JOIN event_photo
 				ON event.id = event_photo.event_id  WHERE  (event.title REGEXP ? || event.description REGEXP ?  || event_photo.caption REGEXP ?)

				)dum ORDER BY interest_activity_id DESC
			
			");	
				if($stmt){
					$stmt->bind_param('sssssss',$interest_like, $interest_like,$interest_like, $interest_like,$interest_like,$interest_like,$interest_like);
					if($stmt->execute()){
						 $result = $stmt->get_result();
						 if($result !== false && $result->num_rows >= 1){
							$row = $result->fetch_all(MYSQLI_ASSOC);
							$stmt->close();
							return $row;
						 }
					}
				}
			}
			echo $this->connection->error;
			return false;		
		
		}
		
		*/
		
		
		public function returnMatchedPhotoForMineInterest($limit, $last_m = MAX_PHOTO_BOUND, $last_e = MAX_PHOTO_BOUND ){
			$mine_interests = $this->getInterestNameForUser($_SESSION['id'], -1);
			$interest_like = '';
			if($mine_interests !== false){
				foreach($mine_interests as $interest){
 					$interest_like .= strtolower($interest['name']).'|';	
				}
				$interest_like = trim($interest_like,'|');
				if($limit > 0){
					$stmt = $this->connection->prepare("
					SELECT *
					FROM
					(
						SELECT 'm' AS `source_from`,  interest_activity.id as interest_activity_id, moment_photo.picture_url,  moment_photo.upload_time as time, moment_photo.user_id,  moment_photo.hash 
						FROM interest 
						LEFT JOIN interest_activity
						ON interest.id = interest_activity.interest_id 
						LEFT JOIN moment
						ON interest_activity.id = moment.interest_activity_id 
						LEFT JOIN moment_photo
						ON moment.id = moment_photo.moment_id 
						WHERE interest.name REGEXP ? AND moment_photo.picture_url IS NOT NULL AND moment_photo.id < ?
			
						UNION 
			
						SELECT  'e' AS `source_from`, interest_activity.id as interest_activity_id,  event_photo.picture_url,  event_photo.upload_time as time, event_photo.user_id, event_photo.hash
						FROM interest 
						LEFT JOIN interest_activity
						ON interest.id = interest_activity.interest_id 
						LEFT JOIN event
						ON interest_activity.id = event.interest_activity_id 
						LEFT JOIN event_photo
						ON event.id = event_photo.event_id
						WHERE interest.name REGEXP ? AND event_photo.picture_url IS NOT NULL  AND event_photo.id < ?
				
						UNION 
				
						SELECT 'm' AS `source_from`,  interest_activity.id as interest_activity_id, moment_photo.picture_url,  moment_photo.upload_time as time, moment_photo.user_id,  moment_photo.hash 
						FROM moment 
						LEFT JOIN interest_activity
						ON moment.interest_activity_id = interest_activity.id
						LEFT JOIN moment_photo
						ON moment.id = moment_photo.moment_id
					  	WHERE  (moment.description REGEXP  ? || moment_photo.caption REGEXP ?) AND moment_photo.picture_url IS NOT NULL AND moment_photo.id < ?
			
						UNION 
						SELECT  'e' AS `source_from`, interest_activity.id as interest_activity_id,  event_photo.picture_url, event_photo.upload_time as time, event_photo.user_id, event_photo.hash
						FROM event 
						LEFT JOIN interest_activity
						ON event.interest_activity_id = interest_activity.id
						LEFT JOIN event_photo
						ON event.id = event_photo.event_id
						WHERE  (event.title REGEXP ? || event.description REGEXP ?  || event_photo.caption REGEXP ?)  AND event_photo.picture_url IS NOT NULL AND event_photo.id < ?

						)dum ORDER BY time DESC LIMIT ?
			
					");	
				}else{
					$stmt = $this->connection->prepare("
					SELECT *
					FROM
					(
						SELECT 'm' AS `source_from`,  interest_activity.id as interest_activity_id, moment_photo.picture_url,  moment_photo.upload_time as time, moment_photo.user_id,  moment_photo.hash 
						FROM interest 
						LEFT JOIN interest_activity
						ON interest.id = interest_activity.interest_id 
						LEFT JOIN moment
						ON interest_activity.id = moment.interest_activity_id 
						LEFT JOIN moment_photo
						ON moment.id = moment_photo.moment_id 
						WHERE interest.name REGEXP ? AND moment_photo.picture_url IS NOT NULL AND moment_photo.id < ?
			
						UNION 
			
						SELECT  'e' AS `source_from`, interest_activity.id as interest_activity_id,  event_photo.picture_url,  event_photo.upload_time as time, event_photo.user_id, event_photo.hash
						FROM interest 
						LEFT JOIN interest_activity
						ON interest.id = interest_activity.interest_id 
						LEFT JOIN event
						ON interest_activity.id = event.interest_activity_id 
						LEFT JOIN event_photo
						ON event.id = event_photo.event_id
						WHERE interest.name REGEXP ? AND event_photo.picture_url IS NOT NULL  AND event_photo.id < ?
				
						UNION 
				
						SELECT 'm' AS `source_from`,  interest_activity.id as interest_activity_id, moment_photo.picture_url,  moment_photo.upload_time as time, moment_photo.user_id,  moment_photo.hash 
						FROM moment 
						LEFT JOIN interest_activity
						ON moment.interest_activity_id = interest_activity.id
						LEFT JOIN moment_photo
						ON moment.id = moment_photo.moment_id
					  	WHERE  (moment.description REGEXP  ? || moment_photo.caption REGEXP ?) AND moment_photo.picture_url IS NOT NULL AND moment_photo.id < ?
			
						UNION 
						SELECT  'e' AS `source_from`, interest_activity.id as interest_activity_id,  event_photo.picture_url, event_photo.upload_time as time, event_photo.user_id, event_photo.hash
						FROM event 
						LEFT JOIN interest_activity
						ON event.interest_activity_id = interest_activity.id
						LEFT JOIN event_photo
						ON event.id = event_photo.event_id
						WHERE  (event.title REGEXP ? || event.description REGEXP ?  || event_photo.caption REGEXP ?)  AND event_photo.picture_url IS NOT NULL AND event_photo.id < ?

						)dum ORDER BY time DESC
			
					");	
				}
				if($stmt){
					if($limit > 0){
						$stmt->bind_param('sisississsii',$interest_like, $last_m, $interest_like,$last_e, $interest_like, $interest_like,  $last_m, $interest_like, $interest_like,$interest_like,$last_e, $limit);
					}else{
						$stmt->bind_param('sisississsi',$interest_like, $last_m, $interest_like,$last_e, $interest_like, $interest_like,  $last_m, $interest_like, $interest_like,$interest_like,$last_e);
					}
					if($stmt->execute()){
						 $result = $stmt->get_result();
						 if($result !== false && $result->num_rows >= 1){
							$row = $result->fetch_all(MYSQLI_ASSOC);
							$stmt->close();
							return $row;
						 }
					}
				}
			}
			echo $this->connection->error;
			return false;		
		
		}
		
		
		
		public function initContentForFriendForUserKey($user_key){
			include_once 'User_Table.php';
			$user = new User_Table();
			$user_id = $user->getUserIdByKey($user_key);
			if($user_id !== false){
				return $this->initContentForFriend($user_id);
			}
			return false;
		}
		public function initContentForFriend($user_id, $all = true){
			include_once MODEL_PATH.'User_In_Interest.php';
			$in = new User_In_Interest();
			include_once MODEL_PATH.'User_Table.php';
			$user = new User_Table();

			$user_found = $in->getAllFriendsInUsersInterestByUserId($user_id);
			$friend_block = null;
			$content = '';
			if($user_found !== false){
				$friend_block = "";
				foreach($user_found as $u){
					$content .= $user->returnUserAvatorByResource($u);
 				}
				ob_start();
				include(TEMPLATE_PATH_CHILD.'friend-initial-content-inner-wrapper-block.phtml');
				$friend_block= ob_get_clean();
				return $friend_block;
			}
			
			return false;
		}
		
		
		
		public function getSimilarInterestBlock(){
			include_once 'User_Profile_Picture.php';
			$interest  = new Interest();
			$profile = new User_Profile_Picture();
			$user = new User_Table();
			$user_found = $this->returnSuggestUser(4);
			$content = null;
			if($user_found !== false){
				ob_start();
				include($this->suggest_friend_block);
				$content = ob_get_clean();
			}
			return $content;
		}
		
		
		public function returnSuggestUser($maximum_user_count){
			$user_found = $this->returnMatchedUserForMineInterest($maximum_user_count, true,'s'); //side block
			 if($user_found === false){
				//get suggest user 
				$user_found = $this->getSuggestFriends();
			}else if(sizeof($user_found) < $maximum_user_count){
				//load 4-sizeof suggest friends, and make sure the friends doesn't repeat
				$exclusive_users = '';
				foreach($user_found as $u){
					$exclusive_users.="'".$u['id']."',";
				}
				$addition_suggest = $this->getSuggestFriends($maximum_user_count-sizeof($user_found), trim($exclusive_users,','));
				if($addition_suggest !== false){
					$user_found = array_merge($user_found, $addition_suggest);
				}
			}
			return $user_found;
		}
		
		
		public function getInitialFriendsBlock($request_user_page_id){
			include_once 'User_Table.php';
			$user = new User_Table();
			$request_user_page_firstname = $user->getUserFirstNameByUserIden($request_user_page_id);
			if($_SESSION['id'] == $request_user_page_id){
				$user_found = $this->returnSuggestUser(4);
			}
			ob_start();
			include($this->initial_friend_block);
			$content = ob_get_clean();
			return $content;
		
		}
		
		
		public function getSuggestFriends($limit = 4, $exclusive_users = false){
			include_once 'User_In_Interest.php';
			$in = new User_In_Interest();
			$list = $in->getFriendPlainListForUser($_SESSION['id']);
			if($list == ''){
				$list = "'-1'";
			}
			if($exclusive_users !== false){
				$stmt = $this->connection->prepare("
					SELECT DISTINCT interest.user_id AS user_id, CONCAT(user.firstname,' ',user.lastname) AS fullname, user.id, user.unique_iden AS hash, user.user_access_url 
					FROM interest 
					LEFT JOIN user
					ON interest.user_id = user.id WHERE  user.id NOT IN($list) AND interest.user_id != ?  AND interest.user_id NOT IN($exclusive_users) ORDER BY RAND() LIMIT ?  
				");
			}else{
				$stmt = $this->connection->prepare("
					SELECT DISTINCT interest.user_id AS user_id, CONCAT(user.firstname,' ',user.lastname) AS fullname, user.id, user.unique_iden AS hash, user.user_access_url 
					FROM interest 
					LEFT JOIN user
					ON interest.user_id = user.id  WHERE  user.id NOT IN($list) AND  interest.user_id != ? AND interest.user_id ORDER BY RAND() LIMIT ? 
				");
			}
			if($stmt){
				$stmt->bind_param('ii',$_SESSION['id'],$limit);
				if($stmt->execute()){
					 $result = $stmt->get_result();
					 if($result !== false && $result->num_rows >= 1){
						$row = $result->fetch_all(MYSQLI_ASSOC);
						return $row;
					 }
				}
			}
			echo $this->connection->error;
			return false;
			
		}
		
		
		public function getAddNewInterestBlock(){
			$request_user_page_has_interest = false;
			ob_start();
			include(TEMPLATE_PATH_CHILD.'add_new_interest_block.phtml');
			$content = ob_get_clean();
			return $content;
		}
		
		
		public function getIndexInterestPreviewBlock($user_id){
			$interest = $this->getUserLastInterestByUserId($user_id);
			if($interest !== false){
				$interest_id = $interest['id'];
				$labelImage = $this->interest_label_image->hasLabelImageForInterest($interest['id']);
				$experience = $this->translateExperienceByNumber($interest['experience']);
				$description = $interest['description'];
				ob_start();
				include(TEMPLATE_PATH_CHILD.'interest_preview_block.phtml');
				$body = ob_get_clean();
				$interest_name = $interest['name'];
				ob_start();
				include(TEMPLATE_PATH_CHILD.'index_recent_post_preview_block.phtml');
				$content = ob_get_clean();
				return $content;
			}
			return false;
		}
		
		
		public function loadMoreInterestFeed($last_key, $interest_id){
			$count = 1;
			$left_content = "";
			$right_content = "";
			$activity_id = $this->interest_activity->getActivityIdByKey($last_key);
			$idCollection = $this->interest_activity->getActivityIdCollectionByInterestId($interest_id, 10, $activity_id);
			if($idCollection !== false && sizeof($idCollection) > 0){
				foreach($idCollection as $row ){
					if(++$count % 2 == 0){
						$right_content.=$this->interest_activity->getInterestActivityBlockByActivityId($row['id']);
					}else{
						$left_content.=$this->interest_activity->getInterestActivityBlockByActivityId($row['id']);
					}
				}
				ob_start();
				include(TEMPLATE_PATH_CHILD.'loading_feed_wrapper.phtml');
				$content = ob_get_clean();
				return $content;
			}
			return false;
			
		}
	
	
		public function loadMoreMatchedUserForMineInterest($limit = 4){
			$list = '';
			if(isset($_SESSION['loaded_mine_people_List'])){
				$list = $_SESSION['loaded_mine_people_List'];
			}
				$result = $this->getResultForMineUser($limit, $list);
				if($result !== false){
					$rows = $result->fetch_all(MYSQLI_ASSOC);
					foreach($rows as $row){
						if(isset($_SESSION['loaded_mine_people_List'])){
							$_SESSION['loaded_mine_people_List'].=",'".$row['id']."'";
						}else{
							$_SESSION['loaded_mine_people_List'] ="'".$row['id']."'";
						}
					}
					return $rows;
				 }
			echo $this->connection->error;
			return false;
		}
		
		 public function loadMoreMatchedUserForKeyWord($key_word, $limit = 4){
			$list = '';
			if(isset($_SESSION['loaded_keyword_people_list'])){
				$list = $_SESSION['loaded_keyword_people_list'];
			}
			
			$result = $this->getResultForUserByKeyWord($key_word, $limit, $list);
			if($result !== false){	
				$rows = $result->fetch_all(MYSQLI_ASSOC);
					foreach($rows as $row){
						if(isset($_SESSION['loaded_keyword_people_list'])){
							$_SESSION['loaded_keyword_people_list'].=",'".$row['id']."'";
						}else{
							$_SESSION['loaded_keyword_people_list'] ="'".$row['id']."'";
						}
					}
					$_SESSION['loaded_keyword_people_list'] = trim($_SESSION['loaded_keyword_people_list'], ',');
					return $rows;
			}
			
			echo $this->connection->error;
			return false;
		}
		
		public function getResultForMineUser($limit = 2, $exculsive_list = "'-1'"){
			$mine_interests = $this->getInterestNameForUser($_SESSION['id'], -1);
			$interest_like = '';
			if($mine_interests !== false){
				foreach($mine_interests as $interest){
 					$interest_like .= $interest['name'].'|';	
				}
				$interest_like = trim($interest_like,'|');
			}
			include_once 'Education.php';
			$edu = new Education();
			$school_id = $edu->getSchoolIdByUserId($_SESSION['id']);
			if($interest_like != ''){
				if($limit < 0){
					if($school_id !== false){
						//use random offset to get random user
						$stmt = $this->connection->prepare("
						SELECT DISTINCT user.id, CONCAT(user.firstname,' ',user.lastname) AS fullname, user.id, user.unique_iden AS hash, user.user_access_url 
						FROM user 
						LEFT JOIN interest
						ON interest.user_id=user.id
						LEFT JOIN education
						ON user.id = education.user_id  AND  user.id !=? 
						WHERE user.id  NOT IN($exculsive_list) AND  (interest.name REGEXP ?  || interest.description REGEXP ?) AND education.school_id = ? 
					
						UNION
						SELECT DISTINCT user.id, CONCAT(user.firstname,' ',user.lastname) AS fullname, user.id, user.unique_iden AS hash, user.user_access_url 
						FROM user 
						LEFT JOIN interest
						ON interest.user_id=user.id   AND  user.id !=? WHERE  user.id  NOT IN($exculsive_list) AND  (interest.name REGEXP ?  || interest.description REGEXP ?) 
						");
					}
					else{
						$stmt = $this->connection->prepare("
						SELECT DISTINCT user.id, CONCAT(user.firstname,' ',user.lastname) AS fullname, user.id, user.unique_iden AS hash, user.user_access_url 
						FROM user 
						LEFT JOIN interest
						ON interest.user_id=user.id  AND  user.id !=?   WHERE user.id  NOT IN($exculsive_list) AND  ( interest.name REGEXP ?  || interest.description REGEXP ?)
						");
					}
				}
				else{
					if($school_id !== false){
						//use random offset to get random user
						$stmt = $this->connection->prepare("
						SELECT DISTINCT user.id, CONCAT(user.firstname,' ',user.lastname) AS fullname, user.id, user.unique_iden AS hash, user.user_access_url 
						FROM user 
						LEFT JOIN interest
						ON interest.user_id=user.id
						LEFT JOIN education
						ON user.id = education.user_id  AND  user.id !=? 
						WHERE  user.id  NOT IN($exculsive_list) AND (interest.name REGEXP ?  || interest.description REGEXP ?) AND education.school_id = ? 
					
						UNION
						SELECT DISTINCT user.id, CONCAT(user.firstname,' ',user.lastname) AS fullname, user.id, user.unique_iden AS hash, user.user_access_url 
						FROM user 
						LEFT JOIN interest
						ON interest.user_id=user.id   AND  user.id !=?   WHERE user.id  NOT IN($exculsive_list) AND   (interest.name REGEXP ?  || interest.description REGEXP ? )
						LIMIT ?
					
						");
					}
					else{
						$stmt = $this->connection->prepare("
						SELECT DISTINCT user.id, CONCAT(user.firstname,' ',user.lastname) AS fullname, user.id, user.unique_iden AS hash, user.user_access_url 
						FROM user 
						LEFT JOIN interest
						ON interest.user_id=user.id  AND  user.id !=?   WHERE  user.id  NOT IN($exculsive_list) AND (interest.name REGEXP ?  || interest.description REGEXP ?)
						LIMIT ?
						");
					}
				}
			}else{
				if($school_id !== false){
					if($limit < 0){
						$stmt = $this->connection->prepare("
							SELECT DISTINCT user.id, CONCAT(user.firstname,' ',user.lastname) AS fullname, user.id, user.unique_iden AS hash, user.user_access_url 
							FROM user 
							LEFT JOIN education
							ON user.id = education.user_id  AND  user.id !=?  WHERE user.id  NOT IN($exculsive_list) AND  education.school_id = ? 
						");
					}else{
						$stmt = $this->connection->prepare("
							SELECT DISTINCT user.id, CONCAT(user.firstname,' ',user.lastname) AS fullname, user.id, user.unique_iden AS hash, user.user_access_url 
							FROM user 
							LEFT JOIN education
							ON user.id = education.user_id  AND  user.id !=? WHERE user.id  NOT IN($exculsive_list) AND  education.school_id = ? LIMIT ?
						");
					}
				}else{
					$stmt = false;
				}
			}
			
			if($stmt){
				if($interest_like != ''){
					if($limit < 0){
						if($school_id !== false){
							$stmt->bind_param('issiiss',$_SESSION['id'],$interest_like,$interest_like, $school_id,$_SESSION['id'],$interest_like,$interest_like);
						}else{
							$stmt->bind_param('iss',$_SESSION['id'],$interest_like,$interest_like);
						}
					}else{
						if($school_id !== false){
							$stmt->bind_param('issiissi',$_SESSION['id'],$interest_like,$interest_like, $school_id,$_SESSION['id'],$interest_like,$interest_like, $limit);
						}else{
							$stmt->bind_param('issi',$_SESSION['id'],$interest_like,$interest_like, $limit);
						}
					}
				}else{
					if($school_id !== false){
						if($limit < 0){
							$stmt->bind_param('ii',$_SESSION['id'], $school_id);
						}else{
							$stmt->bind_param('iii',$_SESSION['id'], $school_id, $limit);
						}
					}
				}
				if($stmt->execute()){
					 $result = $stmt->get_result();
					 if($result !== false && $result->num_rows >= 1){
						$stmt->close();
						return $result;
					 }
				}
			}
			return false;
		}
		
		
		
		
	}

?>