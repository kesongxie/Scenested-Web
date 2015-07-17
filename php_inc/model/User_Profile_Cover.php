<?php
	include_once 'core_table.php';
	include_once 'User_Media_Base.php';

	class User_Profile_Cover extends User_Media_Base{
		private $table_name = "user_profile_cover";
		
		public function __construct(){
			parent::__construct($this->table_name);
		}
		
		public function getLatestProfileImageForUser($user_id){
			$user_media_prefix = new User_Media_Prefix();
			$prefix = $user_media_prefix->getUserMediaPrefix($user_id); //folder for the given user
			$image_file = $this->getColumnByUserId('picture_url',$user_id); //include the wrapper folder directory
			if($prefix && $image_file){
				return U_IMGDIR.$prefix.'/'.$image_file;
			}else{
				return DEFAULT_COVER_IMAGE;
			}
		}
		
		
		public function uploadCoverPicture($file, $user_id){
			$result = $this->getMultipleColumnsBySelector(array('id','picture_url'), 'user_id', $user_id);	
			$upload = $this->uploadMediaForUser($file, $user_id);
			if($upload === false){
				return false;
			}
			//delete old images if applied
			if($result !== false){
				$old_image_url = $result['picture_url'];
				$old_image_row_id = $result['id'];
				include_once '../php_inc/File_Manager.php';
				$flile_m = new File_Manager();
				//remove the old record after successfully update the new media file
				$flile_m->removeMediaFileForUser($old_image_url, $user_id);
				$this->deleteRowById($old_image_row_id);
			}
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
						include TEMPLATE_PATH_CHILD.'profile-cover-photo-preview.phtml';
					 }
				}
			}
			echo $this->connection->error;
			return false;
		}
		
	}		
?>