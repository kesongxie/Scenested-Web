<?php
	include_once MODEL_PATH.'Core_Table.php';
	class User_Table extends Core_Table{
		private $table_name = "user";
	
		public function __construct(){
			parent::__construct($this->table_name);
		}
		
		public function registerUser($email, $password, $firstname, $lastname, $gender, $ip, $signupDatetime){
			$password = password_hash($password, PASSWORD_DEFAULT);
			$email = strtolower($email);
			$firstname = strtolower($firstname);
			$lastname = strtolower($lastname);
			$user_access_url = $firstname.'.'.$lastname;
			$found = $this->isStringValueExistingForColumn($user_access_url, 'user_access_url');
			$count = 0;
			while($found){
				//regenerate
				$count++;
				$user_access_url = $firstname.'.'.$lastname.'.'.$count;
				$found = $this->isStringValueExistingForColumn($user_access_url, 'user_access_url');
			}
			$firstname = ucfirst($firstname);
			$lastname = ucfirst($lastname);
			$stmt = $this->connection->prepare("INSERT INTO `$this->table_name` (`user_iden`,`password`,`firstname`,`lastname`,`gender`,`ip`,`signup_date`,`user_access_url`) VALUES(?, ?, ?, ?, ?, ?, ?, ?)");
			$stmt->bind_param('ssssssss',$email, $password, $firstname, $lastname, $gender, $ip, $signupDatetime, $user_access_url);
			if($stmt->execute()){
				$stmt->close();
				$user_id = $this->connection->insert_id;
				$unique_iden = $this->generateUniqueHash();
				$this->setColumnById('unique_iden',$unique_iden, $user_id);
				return $user_id;
			}
			return false;
		}
		
		public function resetPassword($password, $user_id){
			$password = password_hash($password, PASSWORD_DEFAULT);
			$this->setColumnById('password',$password, $user_id);
			
			//include_once 'Retrieve_Account_Code.php';
// 			$retrive = new Retrieve_Account_Code();
// 			$retrive->deleteEntryForUser($user_id);
		}
		
		
		public function checkUserRegistered($iden){
			$stmt = $this->connection->prepare("SELECT `id` FROM `$this->table_name` WHERE ( `id` = ? || `user_iden`=? || `unique_iden` = ?) LIMIT 1 ");
			$stmt->bind_param('iss',$iden,$iden, $iden);
			if($stmt->execute()){
				$result = $stmt->get_result();
				if($result->num_rows == 1){
					$stmt->close();
					return true;
				}
			}
			return false;
		}
		
		public function activateUser($iden){
			if($this->setColumnById('activated', 1 , $iden)){
				return true;
			}
			return false;
		}
		
		
		public function getUserInfoByUserIden($column,$user_iden){
			$column = $this->connection->escape_string($column);
			$stmt = $this->connection->prepare("SELECT `$column` FROM `$this->table_name` WHERE ( `id` = ? || `user_iden`=? || `unique_iden` = ?) LIMIT 1 ");
			if($stmt){
				$stmt->bind_param('iss', $user_iden,$user_iden, $user_iden);
				if($stmt->execute()){
					 $result = $stmt->get_result();
					 if($result !== false && $result->num_rows == 1){
						$row = $result->fetch_assoc();
						$stmt->close();
						return $row[$column];
					 }
				}
			}
			return false;
		}
		
		public function getUserFirstNameByUserIden($user_iden){
			return $this->getUserInfoByUserIden('firstname',$user_iden);
		}
		
		public function getUserEmailByUserIden($user_iden){
			return $this->getUserInfoByUserIden('user_iden',$user_iden);
		}
		
		
		public function getUserFullnameByUserIden($user_iden){
			$stmt = $this->connection->prepare("SELECT CONCAT(firstname,' ',lastname) AS fullname FROM `$this->table_name` WHERE ( `id` = ? || `user_iden`=? || `unique_iden` = ?) LIMIT 1  ");
			if($stmt){
				$stmt->bind_param('iss', $user_iden, $user_iden,$user_iden);
				if($stmt->execute()){
					 $result = $stmt->get_result();
					 if($result !== false && $result->num_rows == 1){
						$row = $result->fetch_assoc();
						$stmt->close();
						return $row['fullname'];
					 }
				}
			}
			return false;
		}
		
		public function getWhatShouldCallForUser($user_iden){
			$gender = $this->getUserInfoByUserIden('gender',$user_iden);
			if($gender == '1'){
				return array('she','her');
			}else{
				return array('he','his');
			}
		}
		
		public function getUniqueIdenForUser($user_iden){
			return $this->getUserInfoByUserIden('unique_iden',$user_iden);
		}
		
		public function getUserIdByKey($key){
			return $this->getColumnBySelector('id','unique_iden',$key);
		}
		
		
		
		
		
		public function availableToLogin($user_iden, $password){
			$hash = $this->getUserInfoByUserIden('password',$user_iden);
			if($hash !== false){
				if(password_verify($password, $hash)){
					return true;
				}
			}
			return false;
		}
		
		public function getUserAccessUrl($user_iden){
			return $this->getUserInfoByUserIden('user_access_url',$user_iden);
		}
		
		
		/*
			return the user id if the $request_page is valid
		*/
		public function requestUserPageValid($request_page){
			$request_info = explode('/',trim($request_page,'/'));
			$access_url = $request_info[1]; 
			$user_id = $this->getUserIdByAccessUrl($access_url);
			if($user_id !== false){
				return array("id"=>$user_id, "access_url"=>$access_url);
			}
			return false;
		}
		
		public function getUserIdByAccessUrl($access_url){
			return  $this->getColumnBySelector('id', 'user_access_url', $access_url);
		}
		
		public function getLatestCoverForuser($user_id){
			include_once 'User_Profile_Cover.php';
			$cover = new User_Profile_Cover($user_id);
			return $cover->getLatestProfileImageForUser($user_id);
		}
		
		public function getLatestProfilePictureForuser($user_id){
			include_once 'User_Profile_Picture.php';
			$profile = new User_Profile_Picture();
			return $profile->getLatestProfileImageForUser($user_id);
		}
		
			
		
		public function getResultForUserByKeyWord($key_word, $limit = 2, $exculsive_list = "'-1'"){
			include_once 'Education.php';
			$edu = new Education();
			$school_id = $edu->getSchoolIdByUserId($_SESSION['id']);
			if($school_id !== false){
				if($limit > 0){
					$stmt = $this->connection->prepare("
					SELECT DISTINCT user.id,  CONCAT(user.firstname,' ',user.lastname) AS fullname, user.unique_iden AS hash, user.user_access_url
					FROM user
					LEFT JOIN education
					ON user.id = education.user_id 
					WHERE CONCAT(user.firstname,' ',user.lastname) LIKE ?  AND education.school_id = ? AND user.id NOT IN($exculsive_list)
					UNION 
					SELECT  DISTINCT user.id, CONCAT(user.firstname,' ',user.lastname) AS fullname, user.unique_iden AS hash, user.user_access_url
					FROM user
					WHERE CONCAT(user.firstname,' ',user.lastname) LIKE ? AND user.id NOT IN($exculsive_list) 
					LIMIT ?
					");
				}else{
					$stmt = $this->connection->prepare("
					SELECT DISTINCT user.id,  CONCAT(user.firstname,' ',user.lastname) AS fullname, user.unique_iden AS hash, user.user_access_url
					FROM user
					LEFT JOIN education
					ON user.id = education.user_id 
					WHERE CONCAT(user.firstname,' ',user.lastname) LIKE ? AND education.school_id = ? AND user.id NOT IN($exculsive_list)
					UNION 
					SELECT  DISTINCT user.id, CONCAT(user.firstname,' ',user.lastname) AS fullname, user.unique_iden AS hash, user.user_access_url
					FROM user
					WHERE CONCAT(user.firstname,' ',user.lastname) LIKE ? AND user.id NOT IN($exculsive_list) 
					");
				}
			}else{
				if($limit > 0){
					$stmt = $this->connection->prepare("SELECT CONCAT(firstname,' ',lastname) AS fullname, `id`, `unique_iden` AS hash, `user_access_url` FROM `$this->table_name` WHERE CONCAT(firstname,' ',lastname) LIKE ?  AND user.id NOT IN($exculsive_list) LIMIT ? ");
				}else{
					$stmt = $this->connection->prepare("SELECT CONCAT(firstname,' ',lastname) AS fullname, `id`, `unique_iden` AS hash, `user_access_url` FROM `$this->table_name` WHERE CONCAT(firstname,' ',lastname) AND user.id NOT IN($exculsive_list) LIKE ?");
				}
			}
			if($stmt){
				$key_word = '%' .$key_word. '%';
				if($school_id !== false){
					if($limit > 0){
						$stmt->bind_param('sisi',$key_word,$school_id,$key_word, $limit);
					}else{
						$stmt->bind_param('sis',$key_word, $school_id, $key_word);
					}
				}else{
					if($limit > 0){
						$stmt->bind_param('si',$key_word, $limit);
					}else{
						$stmt->bind_param('s',$key_word);
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
		
		
		public function returnContactMatchedUserBySearchkeyWord($key_word, $limit){
			if($limit > 0){
				$stmt = $this->connection->prepare("SELECT  CONCAT('m-',id) AS queue FROM `$this->table_name` WHERE CONCAT(firstname,' ',lastname) LIKE ? AND `id` != ?  LIMIT ? ");
			}else{
				$stmt = $this->connection->prepare("SELECT  CONCAT('m-',id) AS queue FROM `$this->table_name` WHERE CONCAT(firstname,' ',lastname) LIKE ? AND `id` != ?  ");
			}
			if($stmt){
				$key_word = '%' .$key_word. '%';
				if($limit > 0){
					$stmt->bind_param('sii',$key_word,$_SESSION['id'], $limit);
				}else{
					$stmt->bind_param('si',$_SESSION['id'],$key_word);
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
		
		
		public function loadUserHoverProfilenByUiqueIden($iden){
			$resource = $this->getUserAvatorResourceUniqueIden($iden);
			if($resource !== false){
				return $this->returnUserAvatorByResource($resource, true);
			}
			return false;
		}
		
		
		
		public function getUserAvatorResourceUniqueIden($iden){
			$resource = $this->getMultipleColumnsBySelector(array('id','firstname','lastname'), 'unique_iden', $iden);
			if($resource !== false){
				$resource['fullname'] = $resource['firstname'].' '.$resource['lastname'];
				$resource['hash'] = $iden;
				return $resource;
			}
			return false;
		}
		
		
		
		/*
			the $u is an asscotive array contains the user's information, including id, fullname, hash, 
		*/
		public function returnUserAvatorByResource($u, $hover = false){
			include_once 'Interest.php';
			$interest = new Interest();
			include_once 'User_Profile_Picture.php';
			$profile = new User_Profile_Picture();
			include_once 'User_Table.php';
			$user = new User_Table();
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
			if($hover){
				include(TEMPLATE_PATH_CHILD.'hover_profile_wrapper.phtml');
			}else{
				include(TEMPLATE_PATH_CHILD.'friend_profile_wrapper.phtml');
			}
			$content = ob_get_clean();
			return $content;
		}
		
		
		
		
		
		public function isUserActivated($iden){
			return $this->getUserInfoByUserIden('activated',$iden) == '1';
		}
		
		public function getUserIden($iden){
			return $this->getUserInfoByUserIden('user_iden',$iden);
		}
		
		
		
		
		public function loadMoreMatchedUserForKeyWord($key_word, $limit = 4){
			$list = "'-1'";
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
		
		
	
		
	}
		
		
		
?>