<?php
	include_once 'core_table.php';
	include_once 'User_Media_Base.php';

	class Moment_Photo extends User_Media_Base{
		private $table_name = "moment_photo";
		public function __construct(){
			parent::__construct($this->table_name);
		}
		
		public function getMomentPhotoUrlByMomentId($moment_id){
			 return  $this->getColumnBySelector('picture_url', 'moment_id', $moment_id);
		}
		
		
		public function deleteMomentPhotoForUserByMomentId($user_id, $moment_id){
			$url = $this->getMomentPhotoUrlByMomentId($moment_id);
			if($url != false){
				$this->deleteMediaByPictureUrl($url, $user_id);
				$this->deleteRowBySelector('moment_id', $moment_id);
			}
		}
		
		
		public function uploadMomentPhotoByMomentId($photo_file, $user_id, $moment_id){
			return $this->uploadMediaForAssocColumn($photo_file, $user_id, 'moment_id' , $moment_id);
		}
		
		
	}		
?>