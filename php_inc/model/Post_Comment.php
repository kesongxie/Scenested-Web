<?php
	class Post_Comment extends Core_Table{
		private $table_name = "post_comment";
		private $primary_key = "post_comment_id";
		
	
		public function __construct(){
			parent::__construct($this->table_name, $this->primary_key);
		}
		


		public function insertComment($commentInfo){
			$post = new Post();
			$postUserId = $post->getPostUserIdByPostId($commentInfo["postId"]);
			if($postUserId === false){
				return false;
			}
			$view = ($postUserId == $commentInfo["comment_user_id"])? '1': '0';
			$like_time = date("Y-m-d H:i:s");
			$stmt = $this->connection->prepare("INSERT INTO `$this->table_name` (`post_id`, `post_user_id`, `comment_user_id`, `comment_text`, `comment_time`, `view`) VALUES (?, ?, ?, ?, ?, ?) ");
			$stmt->bind_param('iiisss', $commentInfo["postId"], $postUserId, $commentInfo["comment_user_id"], $commentInfo["text"], $like_time, $view );
			if($stmt->execute()){
				$post_comment_id = $this->connection->insert_id;
				$stmt->close();	
				return $this->getMultipleColumnsById(array('post_comment_id', 'comment_time', 'comment_user_id', 'comment_text'), $post_comment_id);
			}
			echo $this->connection->error;
			return false;
		}



		
		
	// 	return a post_like_id if succeed, so that it can be used from unLikePost
// 		public function hasUserCommentPostAlready($post_id, $comment_user_id){
// 			return $this->isRowExsitedForTwoColumns('post_id', $post_id, 'i', 'comment_user_id', $comment_user_id, 'i');
// 		}
// 		
		
		


		
		public function getPostCommentListForPost($post_id){
			$postCommentList = $this->getAllRowsMultipleColumnsBySelector(array('post_comment_id', 'comment_time', 'comment_user_id', 'comment_text'), 'post_id', $post_id, true);
			return $postCommentList !== false ? $postCommentList: array(); 
		}

		
// 		public function getPostLikeListForUser($userId){
// 			$postLikeList = $this->getAllRowsMultipleColumnsBySelector(array('post_like_id', 'post_id'), 'liked_user_id', $userId, true);
// 			return $postLikeList !== false ? $postLikeList: array(); 
// 		}
// 		
// 		
// 		
		
		
	}		
?>