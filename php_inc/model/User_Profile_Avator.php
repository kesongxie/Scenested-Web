<?php
	// include_once PHP_INC_PATH.'core.inc.php';

	class User_Profile_Avator extends User_Media_Base{
		private $table_name = "user_profile_avator";
		private $primary_key = "user_profile_avator_id";
	
		public function __construct(){
			parent::__construct($this->table_name, $this->primary_key);
		}
		
		public function getLatestProfileImageForUser($user_id){
			$user_media_prefix = new User_Media_Prefix();
			$prefix = $user_media_prefix->getUserMediaPrefix($user_id); //folder for the given user
			$image_file = $this->getColumnByUserId('picture_url',$user_id); //include the wrapper folder directory
			if($prefix && $image_file){
				return U_IMGDIR.$prefix.'/'.$image_file;
			}else{
				return DEFAULT_PROFILE_IMAGE;
			}
		}
		
		// public function uploadProfilePicture($file, $x_position_ratio, $y_position_ratio, $user_id){
// 			$results = $this->getAllRowsMultipleColumnsByUserId(array('id','picture_url'), $user_id);	
// 			$upload = $this->uploadMediaForUser($file, $user_id, true, $x_position_ratio, $y_position_ratio, true, PROFILE_PICTURE_ASPECT_RATIO);
// 			if($upload === false){
// 				return false;
// 			}
// 			//delete old images if applied
// 			if($results !== false){
// 				foreach($results as $result){
// 					$old_image_url = $result['picture_url'];
// 					$old_image_row_id = $result['id'];
// 					include_once '../php_inc/File_Manager.php';
// 					$flile_m = new File_Manager();
// 					//remove the old record after successfully update the new media file
// 					$flile_m->removeMediaFileForUser($old_image_url, $user_id);
// 					$this->deleteRowById($old_image_row_id);
// 				}
// 			}
// 			return $upload;
// 		}
// 		
// 		
// 		
		
		
		
		/* 
			@param $ratio_scale_assoc
				the $_POST form the client side script, including image_container_scale_width, image_container_scale_height, 
		 		adjusted_ratio_width, adjusted_ratio_height
		 		
		 	@param $file
		 		the upload file $_FILES['file']
		 	
		 	@user_id
		 		the current session's user id, $_SESSION['id']	
		 */
		
		public function uploadAvatorPicture($file, $ratio_scale_assoc){
			$user_id = $_SESSION['id'];
			$results = $this->getAllRowsMultipleColumnsByUserId(array($this->primary_key,'picture_url'), $user_id);	
			$dst_dimension = array(
							"large" => array("width" => AVATOR_PHOTO_MAX_WIDTH, "height" => AVATOR_PHOTO_MAX_HEIGHT ),
							"thumb" => array("width" => AVATOR_PHOTO_THUMB_WIDTH,"height" => AVATOR_PHOTO_THUMB_HEIGHT )
							);
	
	
	
			$upload = $this->uploadProfileMediaForUser($file, $user_id, $ratio_scale_assoc, $dst_dimension);
			if($upload === false){
				return false;
			}
			//delete old images if applied
			if($results !== false){
				foreach($results as $result){
					$old_image_url = $result['picture_url'];
					$old_image_row_id = $result[$this->primary_key];
					$flile_m = new File_Manager();
					//remove the old record after successfully update the new media file
					$flile_m->removeMediaFileForUser($old_image_url, $user_id);
					$this->deleteRowById($old_image_row_id);
				}
			}
			return $upload;
		}
	
		public function loadProfilePhotoPreviewBlock($hash){
			$stmt = $this->connection->prepare("
			SELECT `user_id`, `picture_url`, `upload_time` FROM `user_profile_picture` WHERE `hash` = ? LIMIT 1
			");
			if($stmt){
				$stmt->bind_param('s',$hash);
				if($stmt->execute()){
					 $result = $stmt->get_result();
					 if($result !== false && $result->num_rows == 1){
						$row = $result->fetch_assoc();
						$stmt->close();
						include_once 'User_Table.php';
						include_once 'User_Profile_Picture.php';
						$profile = new User_Profile_Picture();
						$profile_pic = $profile->getLatestProfileImageForUser($row['user_id']);
						$post_time = convertDateTimeToAgo($row['upload_time'], false);	
						$user = new User_Table();
						$fullname = $user->getUserFullnameByUserIden($row['user_id']);
						$user_page_redirect =  USER_PROFILE_ROOT.$user->getUserAccessUrl($row['user_id']);
						$unique_iden = $user->getUniqueIdenForUser($row['user_id']);
						include TEMPLATE_PATH_CHILD.'profile-photo-preview.phtml';
					 }
				}
			}
			echo $this->connection->error;
			return false;
		}
		
	}		
?>