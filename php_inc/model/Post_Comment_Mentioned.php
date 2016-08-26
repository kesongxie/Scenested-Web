<?php
	class Post_Comment_Mentioned extends Core_Table{
		private $table_name = "post_comment_mentioned";
		private $primary_key = "post_comment_mentioned_id";
		
	
		public function __construct(){
			parent::__construct($this->table_name, $this->primary_key);
		}
		


		public function insertCommentMentioned($mentionedInfo){
			$view = ($mentionedInfo["mentionedUserId"] == $mentionedInfo["commentUserId"]) ? '1': '0';
			$mentionedTime = date("Y-m-d H:i:s");
			$stmt = $this->connection->prepare("INSERT INTO `$this->table_name` (`post_id`,`mentioned_user_id`, `comment_user_id`, `post_comment_id`, `mentioned_time`, `view`) VALUES (?, ?, ?,  ?, ?, ?) ");
			$stmt->bind_param('iiiiss', $mentionedInfo["postId"],  $mentionedInfo["mentionedUserId"],  $mentionedInfo["commentUserId"], $mentionedInfo["postCommentId"], $mentionedTime, $view);
			if($stmt->execute()){
				$post_comment_mentioned_id = $this->connection->insert_id;
				$stmt->close();	
				return true;
			}
			echo $this->connection->error;
			return false;
		}
		
		public function deleteMentionedByPostCommentId($postCommentId){
			return $this->deleteRowBySelector('post_comment_id', $postCommentId, 'i');
		}
		
		public function deleteMentionedByPostId($postId){
			return $this->deleteRowBySelector('post_id', $postId, 'i');
		}
		
		public function getMentionedUserIdListByCommentId($postCommentId){
			return $this->getAllRowsColumnBySelector('mentioned_user_id', 'post_comment_id', $postCommentId, 'i');
		}
		
		
		
	}		
?>