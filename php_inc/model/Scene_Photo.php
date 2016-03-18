<?php
	include_once PHP_INC_PATH.'core.inc.php';

	class Scene_Photo extends User_Media_Base{
		private $table_name = "scene_photo";
		private $primary_key = "scene_photo_id";
		public function __construct(){
			parent::__construct($this->table_name, $this->primary_key);
		}
		
		public function uploadPhotosForScene($photoFiles, $user_id, $user_scene_id){
			$dst_dimension = array(
				"large" => array("width" => SCENE_PHOTO_MAX_WIDTH, "height" => SCENE_PHOTO_MAX_HEIGHT ),
				"thumb" => array("width" => SCENE_PHOTO_THUMB_WIDTH,"height" => SCENE_PHOTO_THUMB_HEIGHT )
				);
			$photosUploaded = array();
			if($photoFiles !== false){
				foreach($photoFiles as $file){
					$upload = $this->uploadPostPhotoForUser($file, $user_id, $user_scene_id, $dst_dimension);
					if($upload === false){
						return false;
					}
					array_push($photosUploaded, $upload);
				}
			}
			return $photosUploaded;
		}
		
		public function getScenePhotoCollection($scene_id){
			return $this->getAllRowsMultipleColumnsBySelector(array('picture_url', 'user_id'), 'scene_id', $scene_id, true);
		}
		
		
		
		
		
	}		
?>