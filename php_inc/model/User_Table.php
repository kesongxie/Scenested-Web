<?php
	include_once 'core_table.php';
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
				$unique_iden = password_hash($user_id, PASSWORD_DEFAULT);
				$this->setColumnById('unique_iden',$unique_iden, $user_id);
				return $user_id;
			}
			return false;
		}
		
		public function checkUserRegistered($iden){
			$stmt = $this->connection->prepare("SELECT `id` FROM `$this->table_name` WHERE (`user_iden`= ?  || `id`= ?) LIMIT 1 ");
			$stmt->bind_param('si',$iden,$iden);
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
			$stmt = $this->connection->prepare("SELECT `$column` FROM `$this->table_name` WHERE (`user_iden` = ? || `id` = ?) LIMIT 1 ");
			if($stmt){
				$stmt->bind_param('si', $user_iden, $user_iden);
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
		
		
		public function getUserFullnameByUserIden($user_iden){
			$stmt = $this->connection->prepare("SELECT CONCAT(firstname,' ',lastname) AS fullname FROM `$this->table_name` WHERE (`id` = ? || `user_iden` = ? ) LIMIT 1  ");
			if($stmt){
				$stmt->bind_param('is', $user_iden, $user_iden);
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
			$activated = $this->getUserInfoByUserIden('activated',$user_iden);
			if($hash !== false){
				if(password_verify($password, $hash) && $activated == '1'){
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
			$access_url = $request_info[2]; //when is site is live, should change this to request_info[1]
			$user_id = $this->getColumnBySelector('id', 'user_access_url', $access_url);	
			if($user_id !== false && $this->getUserInfoByUserIden('activated',$user_id) == 1){
				return array("id"=>$user_id, "access_url"=>$access_url);
			}
			return false;
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
		
		
		public function returnMatchedUserBySearchkeyWord($key_word, $limit){
			if($limit > 0){
				$stmt = $this->connection->prepare("SELECT CONCAT(firstname,' ',lastname) AS fullname, `id`, `unique_iden` AS hash, `user_access_url` FROM `$this->table_name` WHERE CONCAT(firstname,' ',lastname) LIKE ? AND `activated` = '1' LIMIT ? ");
			}else{
				$stmt = $this->connection->prepare("SELECT CONCAT(firstname,' ',lastname) AS fullname, `id`, `unique_iden` AS hash, `user_access_url` FROM `$this->table_name` WHERE CONCAT(firstname,' ',lastname) LIKE ? AND `activated` = '1'");
			}
			if($stmt){
				$key_word = '%' .$key_word. '%';
				if($limit > 0){
					$stmt->bind_param('si',$key_word, $limit);
				}else{
					$stmt->bind_param('s',$key_word);
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
		
		
		
	}
?>