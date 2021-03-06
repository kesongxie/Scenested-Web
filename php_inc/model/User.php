<?php
	class User extends Core_Table{
		private $table_name = "User";
		private $primary_key = "user_id";
		private $user_id;
		
		
		//the column name in the current table schema
		const UserIdKey = "user_id";
		const UserNameKey = "username";
		const FullNameKey = "fullname";
		const EmailKey = "email";
		const PasswordKey = "password";
		const ProfileVisibleKey = "profileVisible";
		const ProfileFeatureKey = "profileFeature";
		
		//the column name that is not in the current table schema
		const BioKey = "bio";
		const AvatorKey = "avator";
		const CoverKey = "cover";
		const UserPostLikes = "postLike";
		

		public function __construct($user_id = null){
			parent::__construct($this->table_name, $this->primary_key);
			if($user_id !== null){
				$this->user_id = $user_id;
			}
		}
		
	
		public function registerUser($paramInfo){
			$userTextualInfo = $paramInfo["userTexttualInfo"];
			if($userTextualInfo[User::UserNameKey] === false  || $userTextualInfo[User::PasswordKey] === false || $userTextualInfo[User::EmailKey] === false || $userTextualInfo[User::FullNameKey] === false){
				return false;
			}
			$userMediaInfo = $paramInfo["userMediaInfo"];
			$stmt = $this->connection->prepare("INSERT INTO `$this->table_name` (`username`, `password`, `email`, `fullname`, `created_time`) VALUES(?, ?, ?, ?, ?)");
				$username = strtolower($userTextualInfo[User::UserNameKey]);
				$password = @password_hash($userTextualInfo[User::PasswordKey], PASSWORD_DEFAULT);
				$email = $userTextualInfo[User::EmailKey];
				$fullname  = $userTextualInfo[User::FullNameKey];
				$created_time = date('Y-m-d H:i:s');
				$stmt->bind_param('sssss', $username, $password, $email, $fullname, $created_time);
				if($stmt->execute()){
					$stmt->close();
					$user_id = $this->connection->insert_id;
					//update the profile picture
					$registeredUser = new User($user_id);
					$registeredUser->saveUserProfileAvator($userMediaInfo[User_Profile_Avator::AvatorKey], false);
					return $registeredUser;
				}
				echo $this->connection->error;
			return false;
		}
		
		//authenticate a user based upon email and corresponding password
		public function authenticateUserWithEmailAndPassword($email, $password){
			//get the password based email
			$result = $this->getMultipleColumnsBySelector([self::PasswordKey, self::UserIdKey], self::EmailKey, $email);
			if($result !== false){
				if(@password_verify($password, $result[self::PasswordKey])){
					return $result[self::UserIdKey];
				}
			}
			return false;
		}
		
		public function authenticateUserWithUserNameAndPassword($username, $password){
			//get the password based username
			$result = $this->getMultipleColumnsBySelector([self::PasswordKey, self::UserIdKey], self::UserNameKey, $username);
			if($result !== false){
				if(@password_verify($password, $result[self::PasswordKey])){
					return $result[self::UserIdKey];
				}
			}
			return false;
		}
		
		
		public function authenticateUserWithUserIdAndPassword($userId, $password){
			//get the password based user id
			$passwordHash = $this->getColumnBySelector(self::PasswordKey, self::UserIdKey, $userId, 'i');
			if($passwordHash !== false){
				if(@password_verify($password, $passwordHash)){
					return true;
				}
			}
			return false;
		}
		
		
		public function getUserIdByUserName($username){
			return $this->getColumnBySelector($this->primary_key, 'username', $username, 's');
		}
		
		
		public function getFullUserInfo(){
			return $this->getMultipleUserInfo([User::UserIdKey, User::UserNameKey, User::FullNameKey, User::BioKey, User::AvatorKey, User::CoverKey, User::ProfileVisibleKey, User::ProfileFeatureKey, User::UserPostLikes]);
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
		
		
		
		public function getUserFullName(){
			if($this->user_id !== null){
				return $this->getColumnById('fullname', $this->user_id);
			}
			return false;
		}
		
		public function getUserAvator(){
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
		
		public function getDeviceToken(){
			if($this->user_id !== null){
				$deviceToken = new Device_Token();
				return $deviceToken->getDeviceTokenForUser($this->user_id);
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
				$user_info[self::AvatorKey] = $this->getUserAvator();
				$extractFileds = array_diff($extractFileds, array(self::AvatorKey));
			} 
			if(in_array(self::CoverKey, $fields)){
				$user_info[self::CoverKey] = $this->getUserCoverUrl();
				$extractFileds = array_diff($extractFileds, array(self::CoverKey));
			} 
			if(in_array(self::ProfileFeatureKey, $fields)){
				$user_info[self::ProfileFeatureKey] = $this->getUserFeatures();
				$extractFileds = array_diff($extractFileds, array(self::ProfileFeatureKey));
			}
			if(in_array(self::UserPostLikes, $fields)){
				$user_info[self::UserPostLikes] = $this->getUserPostLikes();
				$extractFileds = array_diff($extractFileds, array(self::UserPostLikes));
			}
			return array_merge($user_info, $this->getMultipleColumnsById($extractFileds, $this->user_id));
		}
		
		
		public function updateUserFullName($fullname){
			return $this->setColumnById(self::FullNameKey, $fullname, $this->user_id);
		}
		
		// $visibleOption is either 0 or 1
		public function updateProfileVisibleSetting($visibleOption){
			return $this->setColumnById(self::ProfileVisibleKey, $visibleOption, $this->user_id);
		}
		
		public function updateDeviceToken($deviceToken){
			$devieToken = new Device_Token();
			return $devieToken->updateTokenForUser($deviceToken, $this->user_id);	
		}
		
		
		
		public function addUserFeature($paramInfo){
			$images = $paramInfo["images"]; //an array of file, contains the images files $_FILES
			$featureCoverFile = $images[User_Feature_Cover::FeatureCoverKey];
 			$userInfo = $paramInfo["userInfo"]; //contains infomation such as fullname, bio, and profile visible
			$feature_name = $userInfo[Feature::FeatureNameKey];
			$feature = new Feature();
			return $feature->addFeature($featureCoverFile, $this->user_id, $feature_name);
		}
		
		public function sharePost($paramInfo){
			$post = new Post();
			return $post->addPost($paramInfo["images"], $paramInfo["userInfo"]);
		}
		
		
		public function getUserFeatures(){
			$stmt = $this->connection->prepare(
			"SELECT feature.feature_id, feature.name, User_Feature_Cover.picture_url, User_Feature_Cover.hash
			FROM feature
			LEFT JOIN User_Feature_Cover
			ON feature.feature_id = User_Feature_Cover.feature_id
			WHERE feature.user_id = ? ORDER BY feature.feature_id DESC"
			);
			$stmt->bind_param('i', $this->user_id);
			if($stmt->execute()){
				 $result = $stmt->get_result();
				 if($result !== false && $result->num_rows > 0){
					$rows = $result->fetch_all(MYSQLI_ASSOC);
					$stmt->close();
					$features = array();
					foreach($rows as &$row){
						$featureCoverUrl = U_IMGDIR.$this->getUserMediaPrefix().'/'.$row["picture_url"];
						$coverHash = $row["hash"];
						$featureName = $row["name"];
						$feature["feature_id"] =  $row["feature_id"];
						$feature["name"] = $row["name"];
						$feature["photo"] = array("url" => $featureCoverUrl, "hash" => $coverHash);
						$post = new Post();
						$feature["postCount"] =  $post->getPostCountForFeature($row["feature_id"]);
						array_push($features, $feature);
					}
					return $features;
				 }
			}
		}
		
		

		public function saveUserProfile($paramInfo){
			$images = $paramInfo["images"]; //an array of file, contains the images files $_FILES
			if(isset($images[User_Profile_Cover::CoverKey])){
				$coverFile = $images[User_Profile_Cover::CoverKey];
				if ($this->saveUserProfileCover($coverFile, false) === false){
					//IOS Call don't need to crop cover
					//failed
					return false; 
				} 
 			}
 			
 			if(isset($images[User_Profile_Avator::AvatorKey])){
				$avatorFile = $images[User_Profile_Avator::AvatorKey];
				if ($this->saveUserProfileAvator($avatorFile, false) === false){
					return false; //IOS Call don't need to crop avator
				} 
			}
			
			$userInfo = $paramInfo["userInfo"]; //contains infomation such as fullname, bio, and profile visible
			//update profile visible setting
			if($this->updateProfileVisibleSetting($userInfo[self::ProfileVisibleKey]) === false){
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
			return $this->getMultipleUserInfo([User::UserIdKey, User::UserNameKey, User::FullNameKey, User::BioKey, User::AvatorKey, User::CoverKey, User::ProfileVisibleKey]);

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
	 		return $user_cover->uploadCoverPicture($file, $ratio_scale_assoc, $this->user_id);
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
	 		return $user_avator->uploadAvatorPicture($file, $ratio_scale_assoc, $this->user_id);
		}		
		
		
		/*
			return true if exsists, false otherwise
		*/
		public function isUserForEmailExists($email){
			return $this->isStringValueExistingForColumn(self::EmailKey, $email);
		}
		
		public function isUserForUserNameExists($username){
			return $this->isStringValueExistingForColumn(self::UserNameKey, $username);
		}
		
		public function deletePost($postId, $featureId){
			$post = new Post();
			return $post->deletePost($postId, $this->user_id, $featureId);
		}
		
		public function deleteFeature($featureId){
			$feature = new Feature();
			return $feature->deleteFeature($featureId, $this->user_id);
		}
		
		
		//get the user's feature as string
		public function getUserFeatureString(){
			$feature = new Feature();
			return $feature->getUserFeatureString($this->user_id);
		}
		
		public function getUserPostLikes(){
			$postLike = new Post_Like();
			return $postLike->getPostLikeListForUser($this->user_id);
		}
		
		public function LikePost($postId){
			$postLike = new Post_Like();
			return $postLike->likePostForUser($postId, $this->user_id);
		}
		
		
		public function loadMentionedUserInfoFromText($text){
			$mentionedList = retrieveMentionUsernameFromText($text); //an array of username that is mentioned in the text
			$userInfoList = array();	
			foreach($mentionedList as $mentionedUserName){
				$mentionedUserId = $this->getUserIdByUserName($mentionedUserName);
				if($mentionedUserId !== false){
					$user = new User($mentionedUserId);
					 array_push($userInfoList, $user->getFullUserInfo());
				}
			}
			return $userInfoList;
		}
		
		public function getMentionedUserIdListFromText($text){
			$mentionedList = retrieveMentionUsernameFromText($text); //an array of username that is mentioned in the text
			$idList = array();		
			foreach($mentionedList as $mentionedUserName){
				$mentionedUserId = $this->getUserIdByUserName($mentionedUserName);
				if($mentionedUserId !== false){
					array_push($idList, $mentionedUserId);
				}
			}
			return $idList;
			
		}
		
		public function getMentionedUserInfoListFromText($text){
			$idList = $this->getMentionedUserIdListFromText($text);
			$userInfoList = array();
			foreach($idList as $userId){
				$user = new User($userId);
				array_push($userInfoList, $user->getFullUserInfo());
			}
			return $userInfoList; 
		}
		
		
	}		
?>