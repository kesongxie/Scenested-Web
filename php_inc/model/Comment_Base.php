<?php
	include_once MODEL_PATH.'Noti_Sendable.php';
	class Comment_Base extends Noti_Sendable{
		public function __construct($table_name){
			parent::__construct($table_name);
		}
		
		
		public function getPostUserIdByActivityId($activity_id){
			include_once 'Interest_Activity.php';
			$activity = new Interest_Activity();
			return $activity->getColumnById('user_id',$activity_id);
		}
		
		
		public function getTargetIdByRowId($row_id){
			return $this->getColumnById('target_id',$row_id);
		}
		
		
		// public function getSelfIdCollectionByTargetId($target_id){
// 			return $this->getAllRowsColumnBySelector('id', 'target_id', $target_id);
// 		}
// 		
		
	// 	public function getCommentNumberForTarget($target_id){
// 			 $num = $this->getRowsNumberForNumericColumn('target_id', $target_id);
// 			 return ($num !== false)?$num:0;
// 		}
		
		
		public function deleteCommentForUserByKey($user_id, $key){
			$comment_id = $this->getRowIdByHashkey($key);
			if($comment_id !== false){
			 	if($this->deleteRowForUserById($user_id, $comment_id)){
			 		return $comment_id;
			 	}
			}
			return false;
		}	
		
		
		
	}
?>