<?php
	include_once 'core_table.php';
	include_once 'User_Media_Base.php';

	class User_Profile_Cover extends User_Media_Base{
		private $table_name = "user_profile_cover";
		
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
				return DEFAULT_COVER_IMAGE;
			}
		}
	}		
?>