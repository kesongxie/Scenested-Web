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
		public function getPostsForFeature($featureId){
			$column_array = array("post_id", "user_id", "text", "feature_id", "created_time");
			$posts = $this->getAllRowsMultipleColumnsBySelector($column_array, "feature_id", $featureId, true);
			// {"post_id":2,"user_id":107,"text":"US Open 2015 final","feature_id":114,"created_time":"2016-08-12 00:00:00"}
			
			$post_photo = new Post_Photo();
			$post_like = new Post_Like();
			$post_comment = new Post_Comment();

			if($posts !== false){
				foreach($posts as &$post){
					$post["post_photo"] = $post_photo->getPostPhotoCollection($post["post_id"]);
					$post["post_like"] = $post_like->getPostLikeListForPost($post["post_id"]);
					$post["post_comment"] = $post_comment->getPostCommentListForPost($post["post_id"]);
				}
				return array("posts" => $posts);
			}
			return array("posts" => array());
		}
		
		public function getPostIdListInFeature($featureId){
			$posts = $this->getAllRowsColumnBySelector('post_id', "feature_id", $featureId, true);
			return $posts;
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
					$post = array(
						"postId" => $post_id,
						"postText" => $textualParamInfo["postText"],
						"photo" => $postPhotoInfo,
						"featureId" =>  $textualParamInfo["featureId"],
						"createdTime" => $created_time
					);
					return array("status" => true, 
							    self::PostKey => $post,
					 			"errorCode" => false);
				}
			}
			return array("status" => false, self::PostKey => false, "errorCode" => 3); //unknown error
		}
		
		public function deletePost($postId, $userId){
			if($this->deleteRowForUserById($userId, $postId)){
				$post_photo = new Post_Photo();
				return $post_photo->deletePostPhotoForUserByPostId($userId, $postId);
			}
			return false;
		}
		
		public function getFreshPostLikeById($postId){
			$postLike = new Post_Like();
			return $postLike->getLikeUserInfoForPost($postId); 
		}
		
	}		
?>