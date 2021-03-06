<?php
	// include_once PHP_INC_PATH.'core.inc.php';

	class User_Profile_Cover extends User_Media_Base{
		private $table_name = "user_profile_cover";
		private $primary_key = "user_profile_cover_id";
		const CoverKey = "cover";

		public function __construct(){
			parent::__construct($this->table_name,$this->primary_key);
		}
		
		public function getLatestProfileCoverForUser($user_id){
			return $this->getPicture($user_id, "user_id");
		}
		
		
		/* 
			@param $ratio_scale_assoc
				the $_POST form the client side script, including image_container_scale_width, image_container_scale_height, 
		 		adjusted_ratio_width, adjusted_ratio_height
		 		
		 	@param $file
		 		the upload file $_FILES['file']
		 	
		 	@param $useSessionUserId 
		 		set to true if use the current session user id. When set to false, need to provide user id for method call
		 	
		 	@param $user_id
		 		the current session's user id, $_SESSION['id']	
		 		
		 		
		 */
		
		public function uploadCoverPicture($file, $ratio_scale_assoc, $user_id){
			$results = $this->getAllRowsMultipleColumnsByUserId(array($this->primary_key,'picture_url'), $user_id);	

			$dst_dimension = array(
				"large" => array("width" => COVER_PHOTO_MAX_WIDTH, "height" => COVER_PHOTO_MAX_HEIGHT ),
				"thumb" => array("width" => COVER_PHOTO_THUMB_WIDTH,"height" => COVER_PHOTO_THUMB_HEIGHT )
				);
			$photoInfo = $this->uploadProfileMediaForUser($file, $user_id, $ratio_scale_assoc, $dst_dimension, false);

			if($photoInfo === false){
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
			return $photoInfo;
		}
		
		public function loadProfileCoverPhotoPreviewBlock($hash){
			$stmt = $this->connection->prepare("
			SELECT `user_id`, `picture_url`, `upload_time` FROM `user_profile_cover` WHERE `hash` = ? LIMIT 1
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
						include TEMPLATE_PATH_CHILD.'profile-cover-photo-preview.phtml';
					 }
				}
			}
			echo $this->connection->error;
			return false;
		}
		
	}		
?>