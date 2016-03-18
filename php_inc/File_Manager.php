<?php
	include_once 'core.inc.php';
	
	class File_Manager{
		/*
			@param user_media_prefix, the folder directory for the user's media, not the full path
			@param dst_dimension, the dimension of the photos that need to be resized into 
		*/
		public function upload_post_photo($file, $user_media_prefix, $dst_dimension){
			$fulldir = U_MEDAI_FOLDER_DIR.$user_media_prefix;
			$randomFolderName = "";
			if(!file_exists($fulldir)){
				//create media folder for the user if it hasn't existed yet
				mkdir($fulldir);	
			}
			//each files is wrapped in a random folder
			do{
				$randomFolderName = getRandomString(); //random wrapper folder for the media file
				$randomFolderDir = $fulldir.'/'.$randomFolderName;
			}while(file_exists($randomFolderDir));
			if(mkdir($randomFolderDir)){
				//upload file here 
				$extension = getMediaFileExtension($file);
				$filename = getRandomString().'.'.$extension; //rename the file
				$large_destination_path = $randomFolderDir.'/'.$filename;
				$thumb_destination_path = $randomFolderDir.'/'.MEDIA_THUMBNAIL_PREFIX.$filename;
				$target_file = $file["tmp_name"];
				$this->resizePhotoToUpload($target_file, $large_destination_path, $dst_dimension['large']['width'], $dst_dimension['large']['height'], $extension);
				$this->resizePhotoToUpload($target_file, $thumb_destination_path, $dst_dimension['thumb']['width'], $dst_dimension['thumb']['height'], $extension);
				return $randomFolderName.'/'.MEDIA_THUMBNAIL_PREFIX.$filename;
			}
			return false;
		}
		
		/*
			@param target_file, the path to the original file that need to be uploaded, the $file["tmp_name"]
			@param resized_file, which destination path the copy is stored into
			@param wmax,  the max width of this given copy that allow to resize
			@param hmax, the max height of this given copy that allow to resize
			@param extension, the extension that this file will be stored as 
		*/
		
		public function resizePhotoToUpload($target_file, $resized_file, $wmax, $hmax, $extension){
			include_once 'ak_img_lib.php';
			ak_img_resize($target_file, $resized_file, $wmax, $hmax, $extension);
		}
		
		
		
		
		
		
		
		/* 
			@param $ratio_scale_assoc
				the $_POST form the client side script, including image_container_scale_width, image_container_scale_height, 
		 		adjusted_ratio_width, adjusted_ratio_height
		 */
		public function upload_cropped_file($file, $dir, $ratio_scale_assoc, $dst_dimension){
			include_once 'ak_img_lib.php';
			$fulldir = U_MEDAI_FOLDER_DIR.$dir;
			$result = "";
			if(!file_exists($fulldir)){
				//create media folder for the user if it hasn't existed yet
				mkdir($fulldir);	
			}
			//each files is wrapped in a random folder
			do{
				$result = getRandomString(); //random wrapper folder for the media file
				$randomFolderDir = $fulldir.'/'.$result;
			}while(file_exists($randomFolderDir));
			if(mkdir($randomFolderDir)){
				//upload file here 
				$extension = getMediaFileExtension($file);
				$filename = getRandomString().'.'.$extension; //rename the file
				$large_destination_path = $randomFolderDir.'/'.$filename;
 				$min_destination_path = $randomFolderDir.'/'.MEDIA_THUMBNAIL_PREFIX.$filename;
				$target_file = $file["tmp_name"];
				
				$dst_w = $dst_dimension['large']['width'];
				$dst_h = $dst_dimension['large']['height'];
				
				// $dst_w = COVER_PHOTO_MAX_WIDTH;
// 				$dst_h = COVER_PHOTO_MAX_HEIGHT;
				crop_upload_file($file, $large_destination_path, $ratio_scale_assoc, $dst_w, $dst_h  );
				
				
				// $dst_w = COVER_PHOTO_THUMB_WIDTH;
// 				$dst_h = COVER_PHOTO_THUMB_HEIGHT;
				
				$dst_w = $dst_dimension['thumb']['width'];
				$dst_h = $dst_dimension['thumb']['height'];
				crop_upload_file($file, $min_destination_path, $ratio_scale_assoc, $dst_w, $dst_h  );
				
				
				return $result.'/'.MEDIA_THUMBNAIL_PREFIX.$filename;
				
			}
			return false;
		}
		
		
		
		
		
		
		
		//$dir is where the media file located
		
		public function removeMediaFileForUser($dir, $user_id) { 
			$user_media_prefix = new User_Media_Prefix();
			$fullPathForFile = $user_media_prefix->isMediaFileForUser($dir, $user_id);
			if($fullPathForFile !== false){
			   self::rrmdir($fullPathForFile);
			   return true;
		   	}
		   	return false;
		}	 
		
		public static function rrmdir($dir){
			if (is_dir($dir)) { 
				 $objects = scandir($dir); 
				 foreach ($objects as $object) { 
				   if ($object != "." && $object != "..") { 
					 if (filetype($dir."/".$object) == "dir") rrmdir($dir."/".$object); else unlink($dir."/".$object); 
				   } 
				 } 
				 reset($objects); 
				 rmdir($dir); 
			}
		}
		
		
	
		public function recurse_copy($src,$dst) { 
			$dir = opendir($src); 
			@mkdir($dst); 
			while(false !== ( $file = readdir($dir)) ) { 
				if (( $file != '.' ) && ( $file != '..' )) { 
					if ( is_dir($src . '/' . $file) ) { 
						recurse_copy($src . '/' . $file,$dst . '/' . $file); 
					} 
					else { 
						copy($src . '/' . $file,$dst . '/' . $file); 
					} 
				} 
			} 
			closedir($dir); 
		} 
		
		public function getNewRandomNonRepeatedFolderNameInDir($dir){
			do{
				$random_name = getRandomString();
				$folder_path = $dir.'/'.$random_name;
			}while(file_exists($folder_path));
			return $random_name;
		}
	}




?>