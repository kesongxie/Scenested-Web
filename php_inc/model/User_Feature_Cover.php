<?php
	// include_once PHP_INC_PATH.'core.inc.php';

	class User_Feature_Cover extends User_Media_Base{
		private $table_name = "user_feature_cover";
		private $primary_key = "user_profile_cover_id";
		const FeatureCoverKey = "featureCover";

		public function __construct(){
			parent::__construct($this->table_name,$this->primary_key);
		}
		
		public function getFeatureCoverForFeature($feature_id){
			$user_media_prefix = new User_Media_Prefix();
			$prefix = $user_media_prefix->getUserMediaPrefix($user_id); //folder for the given user
			$image_file = $this->getColumnByUserId('picture_url',$user_id); //include the wrapper folder directory
			if($prefix && $image_file){
				return U_IMGDIR.$prefix.'/'.$image_file;
			}
			return false;
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
		
		// public function uploadCoverPicture($file, $ratio_scale_assoc, $user_id){
// 			$results = $this->getAllRowsMultipleColumnsByUserId(array($this->primary_key,'picture_url'), $user_id);	
// 			$dst_dimension = array(
// 				"large" => array("width" => COVER_PHOTO_MAX_WIDTH, "height" => COVER_PHOTO_MAX_HEIGHT ),
// 				"thumb" => array("width" => COVER_PHOTO_THUMB_WIDTH,"height" => COVER_PHOTO_THUMB_HEIGHT )
// 				);
// 			$upload = $this->uploadProfileMediaForUser($file, $user_id, $ratio_scale_assoc, $dst_dimension, false);
// 
// 			if($upload === false){
// 				return false;
// 			}
// 			//delete old images if applied
// 			if($results !== false){
// 				foreach($results as $result){
// 					$old_image_url = $result['picture_url'];
// 					$old_image_row_id = $result[$this->primary_key];
// 					$flile_m = new File_Manager();
// 					//remove the old record after successfully update the new media file
// 					$flile_m->removeMediaFileForUser($old_image_url, $user_id);
// 					$this->deleteRowById($old_image_row_id);
// 				}
// 			}
// 			return $upload;
// 		}
// 		
		
		function uploadFeatureCoverPicture($file, $user_id, $feature_id){
			$results = $this->getAllRowsMultipleColumnsByUserId(array($this->primary_key,'picture_url'), $user_id);	
			
			$dst_dimension = array(
				"large" => array("width" => FEATURE_PHOTO_MAX_WIDTH, "height" => FEATURE_PHOTO_MAX_HEIGHT ),
				"thumb" => array("width" => FEATURE_PHOTO_THUMB_WIDTH,"height" => FEATURE_PHOTO_THUMB_HEIGHT )
				);
				
			$featureCoverUrl = $this->uploadFeaturePhotoForUser($file, $user_id, $feature_id, $dst_dimension);
			$featureCoverHash = $this->getColumnBySelector('hash', 'feature_id', $feature_id, true);
			
			if($featureCoverUrl === false){
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
			return  array("featureCoverUrl" => $featureCoverUrl, "featureCoverHash" => $featureCoverHash ) ;
		}
		
		
		
	}		
?>