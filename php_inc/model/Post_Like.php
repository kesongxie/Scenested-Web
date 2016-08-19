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
		public function getLikeUserInfoForPost($post_id){
			$stmt = $this->connection->prepare(
			"SELECT post_like.post_like_id, post_like.like_time, user.user_id as `like_user_id`, user.fullname
			from post_like
			LEFT JOIN user
			ON post_like.liked_user_id = user.user_id
			WHERE post_like.post_id = ?");
			$stmt->bind_param("i", $post_id);
			if($stmt->execute()){
				 $result = $stmt->get_result();
				 if($result !== false && $result->num_rows > 0){
					$likeUserInfo = $result->fetch_all(MYSQLI_ASSOC);
					$stmt->close();	
					return $likeUserInfo;
				}
			}
			return array();
			
			
		}
		
		
		
		
	}		
?>