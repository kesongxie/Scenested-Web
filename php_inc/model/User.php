<?php
	class User extends Core_Table{
		private $table_name = "User";
		private $primary_key = "user_id";
		private $user_id;
		
		
		//the column name in the current table schema
		const IdKey = "user_id";
		const UserNameKey = "username";
		const FullNameKey = "fullname";
		const ProfileVisibleSettingKey = "profileVisible";
		
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
		
		public function getUserFullName(){
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
		
		
		public function updateUserFullName($fullname){
			return $this->setColumnById(self::FullNameKey, $fullname, $this->user_id);
		}
		
		// $visibleOption is either 0 or 1
		public function updateProfileVisibleSetting($visibleOption){
			return $this->setColumnById(self::ProfileVisibleSettingKey, $visibleOption, $this->user_id);
		}
		
		

		public function saveUserProfile($paramInfo){
			$images = $paramInfo["images"]; //an array of file, contains the images files $_FILES
			
			$coverFile = $images[User_Profile_Cover::CoverKey];
 			if ($this->saveUserProfileCover($coverFile, false) === false){
 				return false; //IOS Call don't need to crop cover
 			} 
 			
			$avatorFile = $images[User_Profile_Avator::AvatorKey];
			if ($this->saveUserProfileAvator($avatorFile, false) === false){
				return false; //IOS Call don't need to crop avator
			} 
			
			$userInfo = $paramInfo["userInfo"]; //contains infomation such as fullname, bio, and profile visible
			//update profile visible setting
			if($this->updateProfileVisibleSetting($userInfo[self::ProfileVisibleSettingKey]) === false){
				return false;
			}
			
			//update fullname
			if ($this->updateUserFullName($userInfo[self::FullNameKey]) === false){
				return false;
			}
			//update user bio
			$bio = new User_Bio();
			if($bio->updateBioForUser($userInfo[User_Bio::BioKey], $this->user_id) === false){
				return false;
			}
			return true;
		}
		
		
		/*
			@param $file
				the file to be uploaded $_FILES["name"]
			@param $cropAvator
				specify whether the avator need to be cropped
			@param $ratio_scale_assoc
				when $cropAvator is true, this parameter must not be NULL
				additional information for cropping image 
				Required Keys 
				image_container_scale_width, image_container_scale_height, adjusted_ratio_width, adjusted_ratio_height
		*/
		public function saveUserProfileCover($file, $cropCover, $ratio_scale_assoc = NULL){
			if($cropCover === false){
				//no need to crop avator image
				$ratio_scale_assoc['image_container_scale_width'] = 1;
				$ratio_scale_assoc['image_container_scale_height'] = 1;
				$ratio_scale_assoc['adjusted_ratio_width'] = 0;
				$ratio_scale_assoc['adjusted_ratio_height'] = 0;
			}
			$user_cover = new User_Profile_Cover();
	 		$url = $user_cover->uploadCoverPicture($file, $ratio_scale_assoc, $this->user_id);
			return $url;
		}		
		
		public function saveUserProfileAvator($file, $cropAvator, $ratio_scale_assoc = NULL){
			if($cropAvator === false){
				//no need to crop avator image
				$ratio_scale_assoc['image_container_scale_width'] = 1;
				$ratio_scale_assoc['image_container_scale_height'] = 1;
				$ratio_scale_assoc['adjusted_ratio_width'] = 0;
				$ratio_scale_assoc['adjusted_ratio_height'] = 0;
			}
			$user_avator = new User_Profile_Avator();
	 		$url = $user_avator->uploadAvatorPicture($file, $ratio_scale_assoc, $this->user_id);
			return $url;
		}		
		
		
		
		
		
		
		
		
	}		
?>