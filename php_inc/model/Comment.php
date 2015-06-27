<?php
	include_once 'Comment_Base.php';
	class Comment extends Comment_Base{
		private $table_name = "Comment";
		private $template_path = "phtml/child/comment_block.phtml";
		public function __construct(){
			parent::__construct($this->table_name, $this->template_path);
		}
		
		public function addPostComment($key, $user_sent, $text){
			$unique_hash = $this->generateUniqueHash();
			$stmt = $this->connection->prepare("INSERT INTO `$this->table_name` (`target_id`,`user_id`,`user_id_get`,`text`,`sent_time`,`hash`) VALUES(?, ?, ?, ?, ?, ?)");
			include_once 'Interest_Activity.php';
			$activity = new Interest_Activity();
			$activity_id = $activity->getRowIdByHashkey($key); //get the target id, comment on which 
			if($activity_id !== false){
				$user_id_get = $activity->getColumnById('user_id',$activity_id);
				$time = date('Y-m-d H:i:s');
				$stmt->bind_param('iiisss',$activity_id, $user_sent, $user_id_get, $text, $time,$unique_hash);
				if($stmt->execute()){
					$stmt->close();
					$comment_id = $this->connection->insert_id;
					return $this->renderCommentBlockByCommentId($comment_id);
				}
			}
			return false;
		}
		
		
		public function renderCommentBlockByCommentId($comment_id){
			$column_array = array('user_id','text','sent_time','hash');
			$comment = $this->getMultipleColumnsById($column_array, $comment_id);
			include_once 'User_Profile_Picture.php';
			$profile = new User_Profile_Picture();
			$post_owner_pic = $profile->getLatestProfileImageForUser($comment['user_id']);
			include_once 'User_Table.php';
			$user = new User_Table();
			$fullname = $user->getUserFullnameByUserIden($comment['user_id']);
			$post_time = convertDateTimeToAgo($comment['sent_time'], true);	
			$user_page_redirect =  USER_PROFILE_ROOT.$user->getUserAccessUrl($comment['user_id']);
			$text = $comment['text'];
			$user_id = $comment['user_id'];
			$hash = $comment['hash'];
			$reply_block = $this->getReplyBlockByCommentId($comment_id);
			$post_owner_id = $this->getPostOwnerIdByCommentId($comment_id);
			ob_start();
			include(SCRIPT_INCLUDE_BASE.$this->template_path);
			$comment_block = ob_get_clean();
			return $comment_block;
		}
		
		//which is the user own the post
		public function getPostOwnerIdByCommentId($comment_id){
			$post_id = $this->getColumnById('target_id',$comment_id);
			include_once 'Interest_Activity.php';
			$activity = new Interest_Activity();
			return $activity->getColumnById('user_id',$post_id);
		}
		
		public function getPostOwnerIdByKey($key){
			$post_id = $this->getColumnBySelector('target_id','hash',$key);
			include_once 'Interest_Activity.php';
			$activity = new Interest_Activity();
			return $activity->getColumnById('user_id',$post_id);
		}
		
		
		public function getReplyBlockByCommentId($comment_id){
			include_once 'Reply.php';
			$reply = new Reply();
			$reply_block = '';
			$idCollection = $reply->getSelfIdCollectionByTargetId($comment_id);
			if($idCollection !== false && sizeof($idCollection) > 0){
				foreach($idCollection as $row ){
					$reply_block.= $reply->renderReplyBlockByReplyId($row['id']);
				}
			}
			return $reply_block;
		}
		
		public function getCommentNumberForTarget($target_id){
			include_once 'Reply.php';
			$reply = new Reply();
			$numComment = $this->getRowsNumberForNumericColumn('target_id', $target_id);
			$commentIdColleciton = $this->getSelfIdCollectionByTargetId($target_id);
			$numReply = 0;
			foreach($commentIdColleciton as $commentId){
				$numReply += $reply->getRowsNumberForNumericColumn('target_id', $commentId['id']);
			}
			$num = $numComment + $numReply;
			return ($num !== false)?$num:0;
		}
		
		public function deleteComment($user_id, $key){
			$deletedCommentId = $this->deleteCommentForUserByKey($user_id, $key);
			include_once 'Reply.php';
			$reply = new Reply();
			$reply->deleteAllCommentsForTarget($deletedCommentId);
		}

		
		
	}
	
	
?>	