<?php
	include_once PHP_INC_PATH.'core.inc.php';
	
	class User_Scene_Label extends Core_Table{
		private $table_name = "user_scene_label";
	
		public function __construct(){
			parent::__construct($this->table_name);
		}
		
		
		public function getSceneLabelsForUser($user_id){
			return $this->getAllRowsColumnBySelector('name', 'user_id', $user_id);
		}
		
		
		public function isSceneLabelExistedForUser($scene_label_name, $user_id){
			return $this->checkColumnValueExistForUser('name', $scene_label_name, $user_id);
		}
		
		public function addSceneForUser($scene_label_name, $user_id){
			if(!$this->isSceneLabelExistedForUser($scene_label_name, $user_id)){
				//create row
				$stmt = $this->connection->prepare("INSERT INTO `$this->table_name` (`user_id`,`name`) VALUES(?, ?)");
				$stmt->bind_param('is', $user_id, $scene_label_name);
				if($stmt->execute()){
					$stmt->close();
					ob_start();
					include(TEMPLATE_PATH_CHILD.'new_scene_label.phtml');
					$label = ob_get_clean();
					return $label;
				}
			}
			return false;
		}
		
		
		// public function renderProfileAddSceneSegue($user_id){
// 			
// 			ob_start();
// 			include(TEMPLATE_PATH_CHILD.'profile_add_scene_segue.phtml');
// 			$segue = ob_get_clean();
// 			return $segue;
// 		}



		
		
	}		
?>