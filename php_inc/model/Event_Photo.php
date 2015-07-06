<?php
	include_once 'core_table.php';
	include_once 'User_Media_Base.php';

	class Event_Photo extends User_Media_Base{
		private $table_name = "event_photo";
		public function __construct(){
			parent::__construct($this->table_name);
		}
		
		/* this would return the first event image*/
		public function getEventPhotoUrlByEventId($event_id){
			 return  $this->getColumnBySelector('picture_url', 'event_id', $event_id);
		}
		
		public function getAllEventPhotoByEventId($event_id){
			$column_array = array('picture_url','user_id','hash');
			$rows = $this->getAllRowsMultipleColumnsBySelector($column_array, 'event_id', $event_id);
			
			$result  = array();
			include_once 'User_Media_Prefix.php';
			$prefix = new User_Media_Prefix();
			if($rows !== false && sizeof($rows) >0){
				foreach($rows as $row){
					$url = $prefix->getUserMediaPrefix($row['user_id']).'/'.$row['picture_url'];
					array_push($result, array('url'=>$url, 'hash'=>$row['hash']) );
				}
			}
			return $result;
		}
		
		public function getEventPhotoNumber($event_id){
			
		}
		
		
		public function deleteEventPhotoForUserByEventId($user_id, $event_id){
			$url = $this->getEventPhotoUrlByEventId($event_id);
			if($url != false){
				$this->deleteMediaByPictureUrl($url, $user_id);
				$this->deleteRowBySelector('event_id', $event_id);
			}
		}
		
		
		public function deleteEventPhotoByKeyForUser($key, $user_id){
			$url = $this->getColumnBySelector('picture_url', 'hash', $key);
			$this->deleteMediaByPictureUrl($url, $user_id);
			return $this->deleteRowBySelectorForUser('hash', $key, $user_id);
		}
		
		
		
		public function getPhotoNumberForEvent($event_id){
			return $this->getRowsNumberForStringColumn('event_id',$event_id);
		}
		
		public function uploadEventPhotoByEventId($photo_file, $user_id, $event_id, $caption = null){
			$hash = $this->generateUniqueHash();
			if($this->uploadCaptionableMediaForAssocColumn($photo_file, $user_id, $caption,$hash, 'event_id' , $event_id)!==false){
				return $hash;
			}
			return false;
		}
		
		public function getEventPhotoCaptionByPictureUrl($picture_url){
			return $this->getColumnBySelector('caption', 'picture_url',$picture_url);
		}
		
		
	}		
?>