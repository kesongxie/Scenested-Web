<?php
	include_once 'core_table.php';
	include_once 'User_Profile_Base.php';

	class User_Profile_Picture extends User_Profile_Base{
		var $table_name = "user_profile_picture";
		
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
				return DEFAULT_PROFILE_IMAGE;
			}
		}
	}		
?>