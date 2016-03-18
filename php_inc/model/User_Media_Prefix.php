<?php
	// include_once PHP_INC_PATH.'core.inc.php';
	
	class User_Media_Prefix extends Core_Table{
		private $table_name = "user_media_prefix";
		private $primary_key = "user_media_prefix_id";
		
		public function __construct(){
			parent::__construct($this->table_name, $this->primary_key);
		}
		
		public function getUserMediaPrefix($user_id){
			return $this->getColumnByUserId('prefix',$user_id);
		}
		
		public function createMediaPrefixForUser($user_id){
			if($this->getUserMediaPrefix($user_id) === false){
				do{
					$prefix = bin2hex(openssl_random_pseudo_bytes(12));
					if($this->isStringValueExistingForColumn($prefix,'prefix') === false){
						break;
					}
				}while(true);
				$stmt = $this->connection->prepare("INSERT INTO `$this->table_name` (`user_id`,`prefix`) VALUES(?, ?)");
				$stmt->bind_param('is',$user_id,$prefix);
				if($stmt->execute()){
					$stmt->close();
					return $prefix;
				}
			}
			return false;
		}
		
		//check whether the give media folder path belongs to the given user or not		
		public function isMediaFileForUser($path, $user_id){
			$user_media_prefix = $this->getUserMediaPrefix($user_id);
			if($user_media_prefix !== false){
				$path_parts = pathinfo($path);
				$fullPathForMediaFile = U_MEDAI_FOLDER_DIR.$user_media_prefix.'/'.$path_parts["dirname"];
				if(file_exists($fullPathForMediaFile)){
					return $fullPathForMediaFile;
				}
			}
			return false;
		}
	}		
?>