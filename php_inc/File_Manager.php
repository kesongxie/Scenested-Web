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
				$destination_path = $randomFolderDir.'/'.$filename;
				
				if(move_uploaded_file($file["tmp_name"],$destination_path)){
					//create a thumbnail image 
					$target_file = $destination_path;
					$resized_file = $randomFolderDir.'/'.MEDIA_THUMBNAIL_PREFIX.$filename;
					$wmax = 500;
					$hmax = 400;
					ak_img_resize($target_file, $resized_file, $wmax, $hmax, $extension);
					return $result.'/'.MEDIA_THUMBNAIL_PREFIX.$filename;
				}
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
	}




?>