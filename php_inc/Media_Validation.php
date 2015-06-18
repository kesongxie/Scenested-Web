<?php
	include_once 'core.inc.php';
	class Media_Validation{
		public function isValidImageFile($file){
			$extension = getMediaFileExtension($file);
			if($extension == 'jpg' || $extension == 'jpeg' || $extension == 'png'){
				return true;
			}else{
				return false;
			}
		}
		
		public function isValidImageSize($file){
			if($file["size"] <= MAXIMUM_UPLOAD_IMAGE_SIZE){
				return true;
			}else{
				return false;
			}
		}
		
		
	
	}
	



?>