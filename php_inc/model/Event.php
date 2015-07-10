<?php
	include_once 'core_table.php';
	include_once 'Event_Photo.php';
	
	class Event extends Core_Table{
		private  $table_name = "event";
		private  $activity_id = false;
		private $preview_block_path = TEMPLATE_PATH_CHILD."evt_preview_block.phtml";
		public $event_id;
		public $event_photo = null;
		
		public function __construct($interest_activity_id){
			parent::__construct($this->table_name);
			$this->activity_id = $interest_activity_id;
			$this->event_id = $this->getColumnBySelector('id', 'interest_activity_id', $this->activity_id);
			$this->event_photo = new Event_Photo();
		}
		
		public function addEventForUser($user_id, $title, $description, $location, $date, $evt_time, $photoFile, $caption){
			$stmt = $this->connection->prepare("INSERT INTO `$this->table_name` (`interest_activity_id`,`title`,`description`,`location`,`date`,`time`) VALUES(?, ?, ?, ?, ?,?)");
			if($stmt){
				if(empty($location)){
					$location = null;
				}
				if(empty($date)){
					$date = null;
				}
				if(empty($evt_time)){
					$evt_time = null;
				}
			
				$stmt->bind_param('isssss',$this->activity_id, $title, $description, $location, $date,$evt_time);
				if($stmt->execute()){
					 if($photoFile != null){
						$event_id = $this->connection->insert_id;
						$event_photo_url = $this->event_photo->uploadEventPhotoByEventId($photoFile, $user_id, $event_id, $caption);
						if($event_photo_url === false){
							$this->deleteRowById($event_id);
							$stmt->close();
							return false;
						}
					}
					$stmt->close();
					return $this->activity_id;
				}
			}
			return false;
		}
		
		
		public function loadEventResource(){
			$column_array = array('id','title','description','location','date','time');
			return $this->getMultipleColumnsBySelector($column_array, 'interest_activity_id', $this->activity_id);	
		}
		
		public function getPostText(){
			return $this->getColumnBySelector('description', 'interest_activity_id', $this->activity_id);	
		}
	
		
		public function deleteEventForUserByActivityId($user_id){
			//delete photos in this event
			$event_id = $this->getColumnBySelector('id', 'interest_activity_id', $this->activity_id);
			$this->event_photo->deleteEventPhotoForUserByEventId($user_id, $event_id);
			
			//delete comments in this moment
			
			//delete the row itself
			$this->deleteRowByNumericSelector('interest_activity_id', $this->activity_id);
		}
		
		public function renderEventPrewviewBlock($post_owner,$interest_id, $isEventEditableForCurrentUser = false){
			$evt_resource = $this->loadEventResource();
			$title = $evt_resource['title'];
			$description = $evt_resource['description'];
			$date = returnShortDate($evt_resource['date'],'-');
			
			
			$event_photo = $this->event_photo->getEventPhotoUrlByEventId($evt_resource['id']);
			include_once 'User_Media_Prefix.php';
			$prefix = new User_Media_Prefix();	
			$media_prefix = $prefix->getUserMediaPrefix($post_owner).'/';
			
			if($event_photo !== false && isMediaDisplayable($media_prefix.$event_photo)){
				$caption = $this->event_photo->getEventPhotoCaptionByPictureUrl($event_photo);
				$event_photo = $media_prefix.$event_photo;
			}else{
				//get the label photo as the event cover
				include_once 'User_Interest_Label_Image.php';
				$label_image = new User_Interest_Label_Image();
				$event_photo = $media_prefix.$label_image->getLabelImageUrlByInterestId($interest_id);
			}
			$regular_event_photo = str_replace('thumb_','',$event_photo);
			$isEventPassed = (time() > strtotime($evt_resource['date'].$evt_resource['time']));				

			
			$photos = $this->event_photo->getAllEventPhotoByEventId($evt_resource['id']);
			//load all the photos in this event $photos
			ob_start();
			include($this->preview_block_path);
			$preview_block = ob_get_clean();
			return $preview_block;
		}
		
		public function isEventPassedForActivityId($activity_id){
			$column_array = array('date','time');
			$event = $this->getMultipleColumnsBySelector($column_array, 'interest_activity_id', $activity_id);
			return (time() > strtotime($event['date'].$event['time']));				
		}
		
	
		
		
		
		
	}
?>