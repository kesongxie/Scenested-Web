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
			$stmt = $this->connection->prepare("INSERT INTO `$this->table_name` (`user_id`,`title`,`description`,`update_time`, `hash`) VALUES(?, ?, ?,?, ?)");
			$time = date('Y-m-d H:i:s');
			$stmt->bind_param('issss',$user_id, $title, $desc,$time, $hash);
			if($stmt->execute()){
				$stmt->close();
				$label_id = $this->connection->insert_id;
				return $this->getFavorEventLabelBlockById($label_id);
			}
			return false;
		}
		
		public function getFavorEvtDescByKey($key){
			return $this->getMultipleColumnsBySelector(array('description','update_time'),'hash',$key);
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
			include_once 'User_Table.php';
			$user = new User_Table();
			$fullname = $user->getUserFirstNameByUserIden($user_id);
			
			ob_start();
			include($this->favor_evt__block_template_path);
			$content = ob_get_clean();
			return $content;
		}
		
		public function removeFavor($key, $user_id){
			$this->deleteRowBySelectorForUser('hash', $key, $user_id);
		}
		
		
		
		
		
				
	}

?>