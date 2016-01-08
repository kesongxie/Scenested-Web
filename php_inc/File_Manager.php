<?php
	include_once 'core.inc.php';
	
	class File_Manager{
		/*
			$dir is the folder directory for the user's media
		*/
		public function upload_File_To_Dir($file, $dir){
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
				$medium_destination_path = $randomFolderDir.'/'.MEDIA_MEDIUM_THUMBNAIL_PREFIX.$filename;
				$min_destination_path = $randomFolderDir.'/'.MEDIA_THUMBNAIL_PREFIX.$filename;
					$target_file = $file["tmp_name"];
					//create a thumbnail image 
					$resized_file = $medium_destination_path;
					$wmax = 500;
					$hmax = 400;
					ak_img_resize($target_file, $resized_file, $wmax, $hmax, $extension);
					
					//create a medium image 
					$resized_file = $large_destination_path;
					$wmax = 1200;
					$hmax = 960;
					ak_img_resize($target_file, $resized_file, $wmax, $hmax, $extension);
					
					$resized_file = $min_destination_path;
					$wmax = 100;
					$hmax = 80;
					ak_img_resize($target_file, $resized_file, $wmax, $hmax, $extension);
					
					return $result.'/'.MEDIA_THUMBNAIL_PREFIX.$filename;
			}
			return false;
		}
		
		
		
		public function upload_cropped_file($file, $dir, $x_position_ratio, $y_position_ratio, $crop_square = true, $cropped_aspect_ratio){
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
				$medium_destination_path = $randomFolderDir.'/'.MEDIA_MEDIUM_THUMBNAIL_PREFIX.$filename;
				$min_destination_path = $randomFolderDir.'/'.MEDIA_THUMBNAIL_PREFIX.$filename;
				$target_file = $file["tmp_name"];
				
				
				$desc_width = 1200;
				$desc_height = 1200 * $cropped_aspect_ratio;
				
				crop_image($target_file, $large_destination_path, $desc_width, $desc_height, $x_position_ratio, $y_position_ratio, $extension, $crop_square, $cropped_aspect_ratio);
				
				
				$desc_width = 400;
				$desc_height = 400 * $cropped_aspect_ratio;	
				crop_image($target_file, $medium_destination_path, $desc_width, $desc_height, $x_position_ratio, $y_position_ratio,  $extension, $crop_square, $cropped_aspect_ratio);
				
				
				
				$desc_width = 120;
				$desc_height = 120 * $cropped_aspect_ratio;	
				crop_image($target_file, $min_destination_path, $desc_width, $desc_height, $x_position_ratio, $y_position_ratio, $extension, $crop_square, $cropped_aspect_ratio);
				
				
				return $result.'/'.MEDIA_MEDIUM_THUMBNAIL_PREFIX.$filename;
				
			}
			return false;
		}
		
		
		
		
		
		
		
		
		
		
		//$dir is where the media file located
		
		public function removeMediaFileForUser($dir, $user_id) { 
			include_once PHP_INC_MODEL.'User_Media_Prefix.php';
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