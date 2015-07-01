<?php
	include_once 'core_table.php';
	include_once 'User_Media_Base.php';

	class Event_Photo extends User_Media_Base{
		private $table_name = "event_photo";
		public function __construct(){
			parent::__construct($this->table_name);
		}
		
		public function getEventPhotoUrlByEventId($event_id){
			 return  $this->getColumnBySelector('picture_url', 'event_id', $event_id);
		}
		
		
		public function deleteEventPhotoForUserByEventId($user_id, $event_id){
			$url = $this->getEventPhotoUrlByEventId($event_id);
			if($url != false){
				$this->deleteMediaByPictureUrl($url, $user_id);
				$this->deleteRowBySelector('event_id', $event_id);
			}
		}
		
		
		public function uploadEventPhotoByEventId($photo_file, $user_id, $event_id, $caption){
			return $this->uploadCaptionableMediaForAssocColumn($photo_file, $user_id, $caption, 'event_id' , $event_id);
		}
		
		public function getEventPhotoCaptionByPictureUrl($picture_url){
			return $this->getColumnBySelector('caption', 'picture_url',$picture_url);
		}
		
		
	}		
?>