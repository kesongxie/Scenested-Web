<?php
	class Post extends Core_Table{
		private $table_name = "post";
		private $primary_key = "post_id";
	
		public function __construct(){
			parent::__construct($this->table_name, $this->primary_key);
		}
		
		
		/*
			return all the post for a specific feature
		*/
		public function getPostsForFeature($featureId){
			$column_array = array("post_id", "user_id", "text", "feature_id", "created_time");
			$posts = $this->getAllRowsMultipleColumnsBySelector($column_array, "feature_id", $featureId, $numericSelector = true);
			// {"post_id":2,"user_id":107,"text":"US Open 2015 final","feature_id":114,"created_time":"2016-08-12 00:00:00"}
			
			$post_photo = new Post_Photo();
			if($posts !== false){
				foreach($posts as &$post){
					$post["post_photo"] = $post_photo->getPostPhotoCollection($post["post_id"]);
				}
				return array("posts" => $posts);
			}
			return false;
		}
	}		
?>