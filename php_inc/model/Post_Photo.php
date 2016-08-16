<?php
	class Post_Photo extends User_Media_Base{
		private $table_name = "post_photo";
		private $primary_key = "post_photo_id";
		const PostPhotoKey = "postPhoto";
		
		public function __construct(){
			parent::__construct($this->table_name, $this->primary_key);
		}
		
		
		
		public function uploadPhotosForPost($photoFiles, $user_id, $post_id){
			$dst_dimension = array(
				"large" => array("width" => POST_PHOTO_MAX_WIDTH, "height" => POST_PHOTO_MAX_HEIGHT ),
				"thumb" => array("width" => POST_PHOTO_THUMB_WIDTH,"height" => POST_PHOTO_THUMB_HEIGHT )
				);
			$photosUploaded = array();
			if($photoFiles !== false){
				foreach($photoFiles as $file){
					$photoInfo = $this->uploadPostPhotoForUser($file, $user_id, $post_id, $dst_dimension);
					if($photoInfo === false){
						return false;
					}
					array_push($photosUploaded, $photoInfo);
				}
			}
			return $photosUploaded;
		}
		
		public function getPostPhotoCollection($post_id){
			$photosInfo = $this->getAllRowsMultipleColumnsBySelector(array('picture_url', 'user_id', 'hash', 'aspect_ratio'), 'post_id', $post_id, true, true);
			$photoUrls = array();
			
			$media_prefix = new User_Media_Prefix();
			foreach($photosInfo as $photoInfo){
				$wholePath = U_IMGDIR.$media_prefix->getUserMediaPrefix($photoInfo["user_id"]).'/'.$photoInfo["picture_url"];
				$hash = $photoInfo["hash"];
				$aspectRatio = $photoInfo["aspect_ratio"];
				$photo = array("url" => $wholePath, "hash" => $hash, "aspectRatio" => $aspectRatio);
				array_push($photoUrls, $photo);
			}
			return 	$photoUrls;
		
		}
		
		
		
		
		
	}		
?>