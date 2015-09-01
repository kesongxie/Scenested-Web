<?php
	include_once 'core_table.php';
	include_once 'User_Media_Base.php';

	class Moment_Photo extends User_Media_Base{
		private $table_name = "moment_photo";
		public function __construct(){
			parent::__construct($this->table_name);
		}
		
		public function getMomentPhotoUrlByMomentId($moment_id){
			 return  $this->getColumnBySelector('picture_url', 'moment_id', $moment_id);
		}
		
		
		public function getMomentPhotoResourceByMomentId($moment_id){
			return  $this->getMultipleColumnsBySelector(array('hash','picture_url'), 'moment_id', $moment_id);

		}
		
		public function deleteMomentPhotoForUserByMomentId($user_id, $moment_id){
			$url = $this->getMomentPhotoUrlByMomentId($moment_id);
			if($url != false){
				$this->deleteMediaByPictureUrl($url, $user_id);
				$this->deleteRowBySelector('moment_id', $moment_id);
			}
		}
		
		
		public function uploadMomentPhotoByMomentId($photo_file, $user_id, $moment_id, $caption){
			$hash = $this->generateUniqueHash();
			return $this->uploadCaptionableMediaForAssocColumn($photo_file, $user_id, $caption, $hash, 'moment_id' , $moment_id);
		}
		
		public function getMomentPhotoCaptionByMomentId($moment_id){
			return $this->getColumnBySelector('caption', 'moment_id',$moment_id);
		}
		
		public function loadMomentPhotoPreviewBlock($hash){
			$stmt = $this->connection->prepare("
			SELECT moment_photo.user_id, moment_photo.picture_url, moment_photo.upload_time, moment_photo.caption, moment.description, moment.date
			FROM moment_photo 
			LEFT JOIN moment
			ON moment_photo.moment_id=moment.id WHERE moment_photo.hash = ? LIMIT 1
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
						$caption  = ($row['caption'] !== null)?$row['caption']:'';
						include TEMPLATE_PATH_CHILD.'photo-preview.phtml';
					 }
				}
			}
			echo $this->connection->error;
			return false;
		}
		
		
	}		
?>