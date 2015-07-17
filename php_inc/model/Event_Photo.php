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
		
		public function getEventPhotoResourceByMomentId($event_id){
			return  $this->getMultipleColumnsBySelector(array('hash','picture_url'), 'event_id', $event_id);

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
		
		
		public function loadEventPhotoPreviewBlock($hash){
			$stmt = $this->connection->prepare("
			SELECT event_photo.user_id, event_photo.picture_url, event_photo.upload_time, event_photo.caption,event.title, event.description, event.location, event.date, event.time
			FROM event_photo 
			LEFT JOIN event
			ON event_photo.event_id=event.id WHERE event_photo.hash = ? LIMIT 1
			");
			if($stmt){
				$stmt->bind_param('s',$hash);
				if($stmt->execute()){
					 $result = $stmt->get_result();
					 if($result !== false && $result->num_rows == 1){
					 	
						$row = $result->fetch_assoc();
 						$stmt->close();
						include_once 'User_Table.php';
						include_once 'User_Profile_Picture.php';
						$profile = new User_Profile_Picture();
						$profile_pic = $profile->getLatestProfileImageForUser($row['user_id']);
						$post_time = convertDateTimeToAgo($row['upload_time'], false);	
						$user = new User_Table();
						$caption = ($row['caption']!==null)?$row['caption']:$row['title'];
						$time = "";
						if($row['date'] != null){
							$time .= returnShortDate($row['date'],',').' - '.getWeekDayFromDate($row['date']);
						}
			
						if($row['time'] != null){
							if($row['date'] != null){
								$time .= ', ';
							}
							$time .= convertTimeToAmPm($row['time']);
						}
						$fullname = $user->getUserFullnameByUserIden($row['user_id']);
						$user_page_redirect =  USER_PROFILE_ROOT.$user->getUserAccessUrl($row['user_id']);
						include TEMPLATE_PATH_CHILD.'evt-photo-preview.phtml';
					 }
				}
			}
			echo $this->connection->error;
			return false;
		}
		
		
	}		
?>