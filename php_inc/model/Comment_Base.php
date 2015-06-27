<?php
	include_once 'core_table.php';
	class Comment_Base extends Core_Table{
		private $table_name;
		private $template_path;
		public function __construct($table_name, $template_path){
			parent::__construct($table_name);
			$this->table_name = $table_name;
			$this->template_path = $template_path;
			
		}
		
		
		
		public function getSelfIdCollectionByTargetId($target_id){
			return $this->getAllRowsColumnBySelector('id', 'target_id', $target_id);
		}
		
		
		public function getCommentNumberForTarget($target_id){
			 $num = $this->getRowsNumberForNumericColumn('target_id', $target_id);
			 return ($num !== false)?$num:0;
		}
		
		
		public function deleteCommentForUserByKey($user_id, $key){
			$comment_id = $this->getRowIdByHashkey($key);
			if($comment_id !== false){
			 	if($this->deleteRowForUserById($user_id, $comment_id)){
			 		return $comment_id;
			 	}
			}
			return false;
		}	
		
		public function deleteAllCommentsForTarget($target_id){
			$this->deleteRowBySelector('target_id', $target_id);
		}
		
	}
	
	
?>	