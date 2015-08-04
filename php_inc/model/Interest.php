<?php
	include_once 'core_table.php';
	include_once 'Interest_Activity.php';
	include_once 'User_Interest_Label_Image.php';
	
	class Interest extends Core_Table{
		private  $table_name = "interest";
		private $interest_activity = null;
		private $similar_interest_block_template_path = TEMPLATE_PATH_CHILD."similar_interest_block.phtml";
		public function __construct(){
			parent::__construct($this->table_name);
			$this->interest_label_image = new User_Interest_Label_Image();
			$this->interest_activity = new Interest_Activity();
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
		
		
		
		public function addInterestForUser($user_id, $name, $description, $experience, $label_image_file){
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
				$main_content = $this->initContentForInterest($user_id,false); //main-block
				$side_content = $this->getInterestLabelByInterestId($interest_id);
				ob_start();
				include(TEMPLATE_PATH_CHILD.'new_interest.phtml');
				$content = ob_get_clean();
				return $content;
			}
			return false;
		}
		
		
		public function updateInterestForUserByInterestId($interest_id, $user_id, $name, $description, $experience, $label_image_file){
			$user_id_for_interest_id = $this->getColumnById('user_id',$interest_id);
			if($user_id_for_interest_id !== false && $user_id_for_interest_id == $user_id){
				//the same, then allow editing
				if($name !== false){
					$this->setColumnById('name', $name, $interest_id);
				}
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
				$idCollection = $this->interest_activity->getActivityIdCollectionByInterestId($interest['id']);
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
		
		public function getUserInterestsLabel($user_id){
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
						
						foreach($row as &$r){
							if(isset($r['picture_url']) && $r['picture_url'] !== null){
								$url = U_IMGDIR.$media_prefix.$r['picture_url'];
								$url = (is_url_exist($url)?$url:DEFAULT_INTEREST_LABEL_IMAGE);
							}else{
								$url = DEFAULT_INTEREST_LABEL_IMAGE;
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
		
		
		public function getInterestLabelByInterestId($interest_id){
			$label_image = new User_Interest_Label_Image();
			$url = $label_image->getLabelImageUrlByInterestId($interest_id);
			$name = $this->getInterestNameByInterestId($interest_id);
			ob_start();
			include(TEMPLATE_PATH_CHILD.'inetrest_label.phtml');
			$content = ob_get_clean();
			return $content;
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
		
		public function returnMatchedUserBySearchkeyWord($key_word, $limit){
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
					WHERE (interest.name LIKE ? || interest.description LIKE ?) AND education.school_id = ? 
					UNION 
					SELECT DISTINCT user.id,CONCAT(user.firstname,' ',user.lastname) AS fullname, user.id, user.unique_iden AS hash, user.user_access_url 
					FROM user 
					LEFT JOIN interest
					ON interest.user_id=user.id  WHERE interest.name LIKE ? || interest.description LIKE ? 
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
					WHERE (interest.name LIKE ? || interest.description LIKE ?) AND education.school_id = ? 
					UNION 
					SELECT DISTINCT user.id,CONCAT(user.firstname,' ',user.lastname) AS fullname, user.id, user.unique_iden AS hash, user.user_access_url 
					FROM user 
					LEFT JOIN interest
					ON interest.user_id=user.id  WHERE interest.name LIKE ? || interest.description LIKE ? 
					");
				}
			}else{
				if($limit > 0){
				$stmt = $this->connection->prepare("
					SELECT DISTINCT user.id,CONCAT(user.firstname,' ',user.lastname) AS fullname, user.id, user.unique_iden AS hash, user.user_access_url 
					FROM user 
					LEFT JOIN interest
					ON interest.user_id=user.id  WHERE interest.name LIKE ? || interest.description LIKE ? LIMIT ?
					");
				}else{
					$stmt = $this->connection->prepare("
					SELECT DISTINCT user.id,CONCAT(user.firstname,' ',user.lastname) AS fullname, user.id, user.unique_iden AS hash, user.user_access_url 
					FROM user 
					LEFT JOIN interest
					ON interest.user_id=user.id  WHERE interest.name LIKE ? || interest.description LIKE ? 
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
						$row = $result->fetch_all(MYSQLI_ASSOC);
						$stmt->close();
						return $row;
					 }
				}
			}
			echo $this->connection->error;
			return false;
		}
		
		
		
		public function returnMatchedUserForMineInterest(){
			$mine_interests = $this->getInterestNameForUser($_SESSION['id'], -1);
			$interest_like = '';
			if($mine_interests !== false){
				foreach($mine_interests as $interest){
 					$interest_like .= $interest['name'].'|';	
				}
				$interest_like = trim($interest_like,'|');
				include_once 'Education.php';
				$edu = new Education();
				$school_id = $edu->getSchoolIdByUserId($_SESSION['id']);
				if($school_id !== false){
					//use random offset to get random user
					$stmt = $this->connection->prepare("
					SELECT DISTINCT user.id, CONCAT(user.firstname,' ',user.lastname) AS fullname, user.id, user.unique_iden AS hash, user.user_access_url 
					FROM user 
					LEFT JOIN interest
					ON interest.user_id=user.id
					LEFT JOIN education
					ON user.id = education.user_id
					AND user.id !=? WHERE (interest.name REGEXP ?  || interest.description REGEXP ?) AND education.school_id = ? 
					UNION
					SELECT DISTINCT user.id, CONCAT(user.firstname,' ',user.lastname) AS fullname, user.id, user.unique_iden AS hash, user.user_access_url 
					FROM user 
					LEFT JOIN interest
					ON interest.user_id=user.id  AND user.id !=? WHERE interest.name REGEXP ?  || interest.description REGEXP ? 
					
					");
				}else{
					$stmt = $this->connection->prepare("
					SELECT DISTINCT user.id, CONCAT(user.firstname,' ',user.lastname) AS fullname, user.id, user.unique_iden AS hash, user.user_access_url 
					FROM user 
					LEFT JOIN interest
					ON interest.user_id=user.id  AND user.id !=? WHERE interest.name REGEXP ?  || interest.description REGEXP ?
					");
				}
				if($stmt){
					if($school_id !== false){
						$stmt->bind_param('issiiss',$_SESSION['id'],$interest_like,$interest_like, $school_id,$_SESSION['id'],$interest_like,$interest_like);
					}else{
						$stmt->bind_param('iss',$_SESSION['id'],$interest_like,$interest_like);
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
		
		
		public function initContentForFriend($user_id, $All){
			include_once 'User_In_Interest.php';
			include_once 'User_Table.php';
			$user = new User_Table();
			$in = new User_In_Interest();
			$user_found = $in->getAllFriendsInUsersInterestByUserId($user_id);
			$friend_block = null;
			if($user_found !== false){
				include_once 'User_Profile_Picture.php';
				$profile = new User_Profile_Picture();
				$friend_block = "";
				foreach($user_found as $u){
						$profile_pic = $profile->getLatestProfileImageForUser($u['id']);
						$cover_pic =  $user->getLatestCoverForuser($u['id']);
						$fullname = $u['fullname'];
						$hash = $u['hash'];
						$rows = $this->getInterestNameForUser($u['id'], 2);
						$user_page_redirect =  USER_PROFILE_ROOT.$user->getUserAccessUrl($u['id']);
						$user_id = $u['id'];
						$result_array = array();
						include_once 'Education.php';
						$educ = new Education();
						$education = $educ->getEducationByUserId($u['id']);
						
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
 						include(TEMPLATE_PATH_CHILD.'friend_profile_wrapper.phtml');
 						$friend_block .= ob_get_clean();
				}
				return $friend_block;
			}
		}
		
		
		public function getSimilarInterestBlock(){
			include_once 'User_Profile_Picture.php';
			$interest  = new Interest();
			$profile = new User_Profile_Picture();
			$user = new User_Table();
			$user_found = $this->returnMatchedUserForMineInterest();
			$content = null;
			if($user_found !== false){
				ob_start();
				include($this->similar_interest_block_template_path);
				$content = ob_get_clean();
			}
			return $content;
		}
		
		
	}

?>