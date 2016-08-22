<?php
	class Post_Like extends Core_Table{
		private $table_name = "post_like";
		private $primary_key = "post_like_id";
		
	
		public function __construct(){
			parent::__construct($this->table_name, $this->primary_key);
		}
		
		
		/*
			returns an array of like user infomation for a specific post
		*/
		// public function getLikeInfoForPost($post_id){
// 			$stmt = $this->connection->prepare(
// 			"SELECT post_like.post_like_id, post_like.like_time
// 			from post_like
// 			LEFT JOIN user
// 			ON post_like.liked_user_id = user.user_id
// 			WHERE post_like.post_id = ?");
// 			$stmt->bind_param("i", $post_id);
// 			if($stmt->execute()){
// 				 $result = $stmt->get_result();
// 				 if($result !== false && $result->num_rows > 0){
// 					$multipleLikeUserInfo = $result->fetch_all(MYSQLI_ASSOC);
// 					$stmt->close();	
// 					// foreach($multipleLikeUserInfo as &$likeUserInfo){
// // 						$likeUser = new User($likeUserInfo["like_user_id"]);
// // 						$likeUserInfo["like_user_avator"] = $likeUser->getUserAvator();
// // 						$likeUserInfo["like_user_feature_string"] = $likeUser->getUserFeatureString();
// // 					}
// 
// // user.fullname as `like_user_fullname`
// 					return $multipleLikeUserInfo;
// 				}
// 			}
// 			return array();
// 			
// 			
// 		}

		//if the user has already liked, delete the like, insert otherwise
		public function toggleLikePostForUser($post_id, $like_user_id){
			$postLikeId = $this->hasUserLikedPostAlready($post_id, $like_user_id);
			if($postLikeId !== false){
				//yes, delete the like
				$this->unLikePost($postLikeId);
				return $postLikeId; //just deleted
			}
			//otherwise insert new like
		
			$post = new Post();
			$postUserId = $post->getPostUserIdByPostId($post_id);
			if($postUserId === false){
				return false;
			}
			$view = ($postUserId == $like_user_id)? '1': '0';
			$like_time = date("Y-m-d H:i:s");
			$stmt = $this->connection->prepare("INSERT INTO `$this->table_name` (`post_id`,`post_user_id`, `liked_user_id`, `like_time`, `view`) VALUES(?, ?, ?, ?, ?)");
				$stmt->bind_param('iiiss', $post_id, $postUserId, $like_user_id, $like_time, $view );
				if($stmt->execute()){
					$stmt->close();
					$postLikeId = $this->connection->insert_id;
					return $this->getMultipleColumnsById(array('post_like_id', 'like_time', 'liked_user_id'), $postLikeId);
				}
			return false;
		}
		
		//return a post_like_id if succeed, so that it can be used from unLikePost
		public function hasUserLikedPostAlready($post_id, $like_user_id){
			return $this->isRowExsitedForTwoColumns('post_id', $post_id, 'i', 'liked_user_id', $like_user_id, 'i');
		}
		
		//this should called after hasUserLikedPostAlready returns(if succeed) 
		public function unLikePost($post_like_id){
			return $this->deleteRowById($post_like_id);
		}
		


		
		public function getPostLikeListForPost($post_id){
			$postLikeIdList = $this->getAllRowsMultipleColumnsBySelector(array('post_like_id', 'like_time', 'liked_user_id'), 'post_id', $post_id, true);
			return $postLikeIdList !== false ? $postLikeIdList: array(); 
		}

		
		public function getPostLikeListForUser($userId){
			$postLikeList = $this->getAllRowsMultipleColumnsBySelector(array('post_like_id', 'post_id'), 'liked_user_id', $userId, true);
			return $postLikeList !== false ? $postLikeList: array(); 
		}
		
		
		
		
		
	}		
?>