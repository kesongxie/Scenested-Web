<?php
	class Post extends Core_Table{
		private $table_name = "post";
		private $primary_key = "post_id";
		
		const PostKey = "post";
		const PostIdKey = "postId";
		const PostTextKey = "postText";
		const PostFeatureIdKey = "postFeatureId";
		const PostCreatedTimeKey = "postCreatedTime";
		
		public function __construct(){
			parent::__construct($this->table_name, $this->primary_key);
		}
		
		
		public function getPostUserIdByPostId($postId){
			return $this->getColumnById('user_id',$postId);
		}
		
		
		/*
			return all the post for a specific feature
		*/
		public function getPostsForFeature($featureId, $postIdOffset, $numberOfRequestedRow){
			$column_array = array("post_id", "user_id", "text", "created_time");
			$posts = $this->getLessRowsMultipleColumnsBySelectorWithOffSetAndLimit($column_array, "feature_id", $featureId, 'i', 'post_id', $postIdOffset, $numberOfRequestedRow);
			$post_photo = new Post_Photo();
			$post_like = new Post_Like();
			$post_comment = new Post_Comment();
			$user = new User();

			if($posts !== false){
				foreach($posts as &$post){
					$post["postPhoto"] = $post_photo->getPostPhotoCollection($post["post_id"]);
 				//	$post["post_like"] = $post_like->getPostLikeListForPost($post["post_id"]);
 				//	$post["post_comment"] = $post_comment->getPostCommentListForPost($post["post_id"]);
 					$post["postLikeCount"] = $post_like->getPostLikeCountForPost($post["post_id"]);
 					$post["postCommentCount"] = $post_comment->getPostCommentCountForPost($post["post_id"]);
 					$post["mentionedUserInfoList"] = $user->getMentionedUserInfoListFromText($post["text"]);
 				}
 				$lastRowIndex = sizeof($posts) -1;
				return array("posts" => $posts, "lastRowPostId" => $posts[$lastRowIndex]["post_id"]);
			}
			return array("posts" => array());
		}
		
		public function getPostIdListInFeature($featureId){
			$posts = $this->getAllRowsColumnBySelector('post_id', "feature_id", $featureId, 'i', true);
			return $posts;
		}
		
		public function getPostCountForFeature($featureId){
			return $this->getNumberOfRowsBySelector('feature_id', $featureId, 'i');
		}
		
		
		
		public function addPost($photoFiles, $textualParamInfo){
			if(!isset($photoFiles) || sizeof($photoFiles) < 1){
				return array("status" => false, self::PostKey => false, "errorCode" => 1); 
			}
			$created_time = date('Y-m-d H:i:s');
			$stmt = $this->connection->prepare("INSERT INTO `$this->table_name` (`user_id`, `text`, `feature_id`, `created_time`) VALUES(?, ?, ?, ?)");
			$stmt->bind_param('isis',$textualParamInfo["userId"], $textualParamInfo["postText"], $textualParamInfo["featureId"], $created_time);
			if($stmt->execute()){
				$stmt->close();
				$post_id = $this->connection->insert_id;
				$post_photo = new Post_Photo();
				$postPhotoInfo = $post_photo->uploadPhotosForPost($photoFiles, $textualParamInfo["userId"], $post_id);
				if($postPhotoInfo === false){
					$this->deleteRowById($post_id);
				}else{
					$user = new User();
					$post = array(
						"post_id" => $post_id,
						"text" => $textualParamInfo["postText"],
						"postPhoto" => $postPhotoInfo,
						"created_time" => $created_time,
						"postLikeCount" => 0,
 					 	"postCommentCount" => 0,
						"mentionedUserInfoList" => $user->getMentionedUserInfoListFromText($textualParamInfo["postText"])
					);
					 $postCount = $this->getPostCountForFeature($textualParamInfo["featureId"]);
					return array("status" => true, 
							    "post" => $post,
							    "postCountInFeature" => $postCount,
					 			"errorCode" => false);
				}
			}
			return array("status" => false, "post" => false,  "postCountInFeature" => $postCount, "errorCode" => 3); //unknown error
		}
		
		//return the total number of post on feature when succeed, false otherwise
		public function deletePost($postId, $userId, $featureId){
			if($this->deleteRowForUserById($userId, $postId)){
				$post_photo = new Post_Photo();
				$this->deleteCommentInPost($postId);
				$this->deleteLikesInPost($postId);
				$post_photo->deletePostPhotoForUserByPostId($userId, $postId);
				$postCount = $this->getPostCountForFeature($featureId);
				return array("status" => true, 
							    "postCountInFeature" => $postCount);
				}
			return array("status" => false);
		}
		
		public function getFreshPostLikeById($postId){
			$postLike = new Post_Like();
			return $postLike->getLikeUserInfoForPost($postId); 
		}
		
		public function deleteCommentInPost($postId){
			$comment = new Post_Comment();
			return $comment->deleteCommentInPost($postId);
		}
		
		public function deleteLikesInPost($postId){
			$postLike = new Post_Like();
			return $postLike->deleteLikesInPost($postId);
		}
		
	}		
?>