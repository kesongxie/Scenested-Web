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
			$comment_time = date("Y-m-d H:i:s");
			$stmt = $this->connection->prepare("INSERT INTO `$this->table_name` (`post_id`, `post_user_id`, `comment_user_id`, `comment_text`, `comment_time`, `view`) VALUES (?, ?, ?, ?, ?, ?) ");
			$stmt->bind_param('iiisss', $commentInfo["postId"], $postUserId, $commentInfo["comment_user_id"], $commentInfo["text"], $comment_time, $view );
			if($stmt->execute()){
				$post_comment_id = $this->connection->insert_id;
				$stmt->close();	
				//retrive the mentioned list 
				$mentioned = new Post_Comment_Mentioned();
				$user = new User();
				$mentionedUserIdList = $user->getMentionedUserIdListFromText($commentInfo["text"]);
				foreach($mentionedUserIdList as $mentionedUserId){
					if($mentionedUserId !== false){
						$mentionedInfo["postId"] = $commentInfo["postId"];
						$mentionedInfo["mentionedUserId"] = $mentionedUserId;
						$mentionedInfo["commentUserId"] =  $commentInfo["comment_user_id"];
						$mentionedInfo["postCommentId"] = $post_comment_id;
						$mentioned->insertCommentMentioned($mentionedInfo);
					}
				}
				
				$comment =  $this->getMultipleColumnsById(array('post_comment_id', 'comment_time', 'comment_user_id', 'comment_text'), $post_comment_id);
			// 	$taggedUserIdList = array();
// 				$mentionedUserIdList = $mentioned->getMentionedUserIdListByCommentId($post_comment_id);
// 				if($mentionedUserIdList !== false){
// 					foreach($mentionedUserIdList as $mentionedUserId){
// 						array_push($taggedUserIdList, $mentionedUserId['mentioned_user_id']);
// 					}
// 				}
				$comment["mentionedUserInfoList"] = $user->getMentionedUserInfoListFromText($commentInfo["text"]);
				return $comment;
			}
			echo $this->connection->error;
			return false;
		}

		public function getPostCommentListForPost($post_id){
			$postCommentList = $this->getAllRowsMultipleColumnsBySelector(array('post_comment_id', 'comment_time', 'comment_user_id', 'comment_text'), 'post_id', $post_id, true);
			if($postCommentList !== false){
				$mentioned = new Post_Comment_Mentioned();
				foreach($postCommentList as &$comment){
					$taggedUserIdList = array();
					$mentionedUserIdList = $mentioned->getMentionedUserIdListByCommentId($comment['post_comment_id']);
					if($mentionedUserIdList !== false){
						foreach($mentionedUserIdList as $mentionedUserId){
							array_push($taggedUserIdList, $mentionedUserId['mentioned_user_id']);
						}
					}
					$comment["mentionedUserIdList"] = $taggedUserIdList;
				}
			}
			return $postCommentList !== false ? $postCommentList: array(); 
		}

		
		public function deleteComment($postCommentId){
			if($this->deleteRowById($postCommentId)){
				$mentioend = new Post_Comment_Mentioned();
				return $mentioend->deleteMentionedByPostCommentId($postCommentId);	
			}
			
			
		}
		
		public function deleteCommentInPost($postId){
			$mentioend = new Post_Comment_Mentioned();
			return $this->deleteRowBySelector("post_id", $postId, 'i') && $mentioend->deleteMentionedByPostId($postId);
		}
		
	}		
?>