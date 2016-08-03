<?php
	class User extends Core_Table{
		private $table_name = "User";
		private $primary_key = "user_id";
		private $user_id;
		
		
		//the column name in the current table schema
		const IdKey = "user_id";
		const UserNameKey = "username";
		const FullNameKey = "fullname";
		
		//the column name that is not in the current table schema
		const BioKey = "bio";
		const AvatorKey = "avator";
		const CoverKey = "cover";
		

		public function __construct($user_id = null){
			parent::__construct($this->table_name, $this->primary_key);
			if($user_id !== null){
				$this->user_id = $user_id;
			}
		}
		
		public function registerUser($username, $password){
			$stmt = $this->connection->prepare("INSERT INTO `$this->table_name` (`username`,`password`, `created_time`) VALUES(?, ?, ?)");
				$username = strtolower($username);
				$password = @password_hash($password_hash, PASSWORD_DEFAULT);
				$created_time = date('Y-m-d H:i:s');
				$stmt->bind_param('sss',$username, $password, $created_time);
				if($stmt->execute()){
					$stmt->close();
					$user_id = $this->connection->insert_id;
					return new User($user_id);
				}
			return false;
		}
		
		public function getUserId(){
			if($this->user_id !== null){
				return $this->user_id;
			}
			return false;
		}
		
		public function getUserName(){
			if($this->user_id !== null){
				return $this->getColumnById('username', $this->user_id);
			}
			return false;
		}
		
		public function getDeviceToken(){
			if($this->user_id !== null){
				return $this->getColumnById('deviceToken', $this->user_id);
			}
			return false;
		}
		
		public function getUserFullname(){
			if($this->user_id !== null){
				return $this->getColumnById('fullname', $this->user_id);
			}
			return false;
		}
		
		public function getUserAvatorUrl(){
			if($this->user_id !== null){
				$avator = new User_Profile_Avator();
				return $avator->getLatestProfileImageForUser($this->user_id);
			}
			return false;
		}
		
		public function getUserCoverUrl(){
			if($this->user_id !== null){
				$cover = new User_Profile_Cover();
				return $cover->getLatestProfileCoverForUser($this->user_id);
			}
			return false;
		}
		
		public function getBio(){
			if($this->user_id !== null){
				$bio = new User_Bio();
				return $bio->getBioForUser($this->user_id);
			}
			return false;
		}
		
		
		public function getUserMediaPrefix(){
			if($this->user_id !== null){
				$prefix = new User_Media_Prefix();
				return $prefix->getUserMediaPrefix($this->user_id);
			}
			return false;
		}
		
		public function loadUserProfileScene(){
			if($this->user_id !== null){
				$scene = new Scene();
				return $scene->getProfileSceneForUser($this->user_id);
			}
			return false;
		}
		
		public function getSceneNumberForUser(){
			if($this->user_id !== null){
				$scene = new Scene();
				return $scene->getSceneNumberForUser($this->user_id);
			}
			return false;
		}
		
		public function getMultipleUserInfo($fields){
			$extractFileds = $fields; //$extractFields are the field that is in the current user table
			if(in_array(self::BioKey, $fields)){
				 //need to fetch user bio and append to the return 	
				 $user_info[self::BioKey] = $this->getBio();
				 $extractFileds = array_diff($extractFileds, array(self::BioKey));
			}
			if(in_array(self::AvatorKey, $fields)){
				$user_info[self::AvatorKey] = $this->getUserAvatorUrl();
				$extractFileds = array_diff($extractFileds, array(self::AvatorKey));
			} 
			if(in_array(self::CoverKey, $fields)){
				$user_info[self::CoverKey] = $this->getUserCoverUrl();
				$extractFileds = array_diff($extractFileds, array(self::CoverKey));
			} 
			return array_merge($user_info, $this->getMultipleColumnsById($extractFileds, $this->user_id));
		}
		
		
		
		
		// public function getSimilarThemeWithUser($other_user_id){
// 			$theme = new Theme();
// 			//return true;
// 			//return $theme->getSimilarThemeBetweenTwoUsers($user_id, $other_user_id);
// 		}
		
		
		
		
		
		
		
	}		
?>