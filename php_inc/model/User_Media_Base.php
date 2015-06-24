<?php
	include_once 'core_table.php';
	include_once 'User_Media_Prefix.php';
	include_once SCRIPT_INCLUDE_BASE.'/php_inc/File_Manager.php';



	class User_Media_Base extends Core_Table{
		private $file_m = null;
		private $table_name;
		
		public function __construct($table_name){
			parent::__construct($table_name);
			$this->table_name = $table_name;
			$this->file_m = new File_Manager();
		}
		
		public function uploadMediaForUser($file,$user_id){
			$user_media_prefix = new User_Media_Prefix();
			$prefix = $user_media_prefix->getUserMediaPrefix($user_id);
			if($prefix === false){
				$prefix = $user_media_prefix->createMediaPrefixForUser($user_id);
			}
			
			if($prefix !== false){
				$picture_url = $this->flile_m->upload_File_To_Dir($file, $prefix);
				if($picture_url !== false){
					$stmt = $this->connection->prepare("INSERT INTO `$this->table_name` (`user_id`,`picture_url`,`upload_time`) VALUES(?, ?, ?)");
					$time = date("Y-m-d H:i:s");
					$stmt->bind_param('iss',$user_id, $picture_url, $time);
					if($stmt->execute()){
						$stmt->close();
						return true;
					}
				}
			}
			return false;	
		}
		
		/*
			this method is used for the case when the user id is not a column in the table structure, 
			the column name is $assoc_name instead of "user_id"
		*/
		public function uploadMediaForAssocColumn($file,$user_id, $assoc_name, $assoc_id){
			$user_media_prefix = new User_Media_Prefix();
			$prefix = $user_media_prefix->getUserMediaPrefix($user_id);
			if($prefix === false){
				$prefix = $user_media_prefix->createMediaPrefixForUser($user_id);
			}
			
			if($prefix !== false){
				$picture_url = $this->file_m->upload_File_To_Dir($file, $prefix);
				if($picture_url !== false){
					$stmt = $this->connection->prepare("INSERT INTO `$this->table_name` (`$assoc_name`,`picture_url`,`upload_time`) VALUES(?, ?, ?)");
					$time = date("Y-m-d H:i:s");
					$stmt->bind_param('iss',$assoc_id, $picture_url, $time);
					if($stmt->execute()){
						$stmt->close();
						return $picture_url;
					}
				}
			}
			return false;	
		}
		
		public function deleteMediaByPictureUrl($url, $user_id){
			return $this->file_m->removeMediaFileForUser($url, $user_id);
		}
		
		
		
		
	}		
?>