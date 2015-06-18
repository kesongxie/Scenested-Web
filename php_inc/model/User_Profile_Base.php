<?php
	include_once 'core_table.php';
	include_once 'User_Media_Prefix.php';

	class User_Profile_Base extends Core_Table{
		public function __construct($table_name){
			parent::__construct($table_name);
		}
		
		public function uploadProfilePictureForUser($file,$user_id){
			$user_media_prefix = new User_Media_Prefix();
			$prefix = $user_media_prefix->getUserMediaPrefix($user_id);
			if($prefix === false){
				$prefix = $user_media_prefix->createMediaPrefixForUser($user_id);
			}
			
			if($prefix !== false){
				include_once '../php_inc/File_Manager.php';
				$flile_m = new File_Manager();
				$picture_url = $flile_m->upload_File_To_Dir($file, $prefix);
				if($picture_url !== false){
					$stmt = $this->connection->prepare("INSERT INTO `$this->table_name` (`user_id`,`picture_url`,`upload_time`) VALUES(?, ?, ?)");
					$time = date("Y-m-d H:i:s");
					$stmt->bind_param('iss',$user_id, $picture_url, $time);
					if($stmt->execute()){
						$stmt->close();
						return true;
					}
					echo $this->connection->error;
					return false;
				}
			}	
		}
		
		
		
		
		
	}		
?>