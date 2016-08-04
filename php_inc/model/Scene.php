<?php
	include_once PHP_INC_PATH.'core.inc.php';
	
	class Scene extends Core_Table{
		private $table_name = "scene";
		private $primary_key = "scene_id";
		private $post_block_template_path = TEMPLATE_PATH_CHILD."post_block.phtml";
	
		public function __construct(){
			parent::__construct($this->table_name, $this->primary_key);
		}
		
		public function addScene($photoFiles, $user_id, $caption, $date, $label, $location){
			if(!isset($photoFiles) || sizeof($photoFiles) < 1){
				return false;
			}
			$post_time = date('Y-m-d H:i:s');
			$stmt = $this->connection->prepare("INSERT INTO `$this->table_name` (`user_id`, `caption`, `date`, `label`, `location`, `post_time`) VALUES(?, ?, ?, ?, ?, ?)");
			$stmt->bind_param('isssss',$user_id,$caption, $date, $label, $location, $post_time);
			if($stmt->execute()){
				$stmt->close();
				$scene_id = $this->connection->insert_id;
				$scene_photo = new Scene_Photo();
				if($scene_photo->uploadPhotosForScene($photoFiles, $user_id, $scene_id) === false){
					$this->deleteRowById($scene_id);
				}else{
					return $this->renderScene($scene_id);
				}
			}
			return false;
		}
		
		public function renderScene($scene_id){
			$scheme_obj = new Photo_Layout_Scheme();
			$scene = $this->getAllColumnsById($scene_id);
			$user_obj = new User($scene['user_id']);
			
			$scene['user_full_name'] = $user_obj->getUserName();
			
			$scene['post_user_avator_url'] = $user_obj->getUserAvatorUrl();
			//photo
			$scene_photo_obj = new Scene_Photo();
			
			$photoCollection = $scene_photo_obj->getScenePhotoCollection($scene_id);
			
			if($photoCollection !== false){
				foreach($photoCollection as &$photo){
					$photo['picture_url'] = U_IMGDIR.$user_obj->getUserMediaPrefix().'/'.$photo['picture_url'];
				}
			}
			$photoBlock = $scheme_obj->getPhotoLayoutBlock($photoCollection);
			//date
			$scene['day'] = getDayFromDate($scene['date']);
			$scene['month'] =  getMonthAbbrFromDate($scene['date']);
		 	$scene['year'] = getYearFromDate($scene['date']);
			//time
			$scene['post_time'] = convertDateTimeToAgo($scene['post_time']);
			ob_start();
			include($this->post_block_template_path);
			$post_block = ob_get_clean();
			return $post_block;
		}
		
		public function getProfileSceneForUser($user_id){
			$scenes = '';
			$sceneCollection = $this->getAllRowsColumnBySelector('scene_id', 'user_id', $user_id);
			if($sceneCollection !== false){
				foreach($sceneCollection as $s){
					$scenes.=$this->renderScene($s['scene_id']);
				}
				return $scenes;
			}
			return false;
		}
		
		public function getSceneNumberForUser($user_id){
			return $this->getRowsNumberForNumericColumn('user_id',$user_id);
		}
	



		
		
	}		
?>