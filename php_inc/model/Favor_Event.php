<?php
	include_once 'core_table.php';
	class Favor_Event extends Core_Table{
		public $table_name = "favor_event";
		private $favor_evt__block_template_path = TEMPLATE_PATH_CHILD."favor_evt_block.phtml";
		private $favor_evt_label_template_path = TEMPLATE_PATH_CHILD."favor_evt_Label.phtml";

	
		public function __construct(){
			parent::__construct($this->table_name);
		}
		
		public function addFavorEventForUser($title, $desc, $user_id){
			$hash = $this->generateUniqueHash();
			$stmt = $this->connection->prepare("INSERT INTO `$this->table_name` (`user_id`,`title`,`description`, `hash`) VALUES(?, ?, ?, ?)");
			$stmt->bind_param('isss',$user_id, $title, $desc, $hash);
			if($stmt->execute()){
				$stmt->close();
				$label_id = $this->connection->insert_id;
				return $this->getFavorEventLabelBlockById($label_id);
			}
			return false;
		}
		
		public function getFavorEventLabelForUser($user_id){
			$column_array = array('title','hash');
			return $this->getAllRowsMultipleColumnsByUserId($column_array, $user_id);	
		}
		
		public function getFavorEventLabelBlockById($id){
			$column_array = array('title','hash');
			$row = $this->getMultipleColumnsById($column_array, $id);	
			$title = $row['title'];
			$hash =  $row['hash'];
			ob_start();
			include($this->favor_evt_label_template_path);
			$content = ob_get_clean();
			return $content;
		}
		
		
		public function getFavorEventBlockForUser($user_id){
			$evts = $this->getFavorEventLabelForUser($user_id);	
			ob_start();
			include($this->favor_evt__block_template_path);
			$content = ob_get_clean();
			return $content;
		}
		
		
				
	}

?>