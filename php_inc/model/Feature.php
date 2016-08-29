<?php
	include_once PHP_INC_PATH.'core.inc.php';
	
	class Feature extends Core_Table{
		private $table_name = "feature";
		private $primary_key = "feature_id";
		private $featureId;
		
// 		const KeyForFeatureName = "name";
		const FeatureKey = "feature";
 		const FeatureIdKey = "featureId";
 		const FeatureNameKey = "featureName";
 		const FeatureCoverUrlKey = "featureCoverUrl";
 		const FeatureCoverHashKey = "featureCoverHash";
		
	
		public function __construct($featureId = NULL){
			parent::__construct($this->table_name, $this->primary_key);
			if($featureId != NULL){
				$this->featureId = $featureId;
			}
		}
		public function getPosts(){
			if($this->featureId != NULL){
				$post = new Post();
				return $post->getPostsForFeature($this->featureId);
			}
		}
		
		
		
		public function getSimilarFeatureBetweenTwoUsers($first_user_id, $second_user_id){
			$query = "SELECT a.name 
			   		  FROM feature a
					  INNER JOIN feature b
					  ON a.name = b.name
					  WHERE a.user_id = ? AND b.user_id = ?";
				  
			$stmt = $this->connection->prepare($query);
			$stmt->bind_param('ii', $first_user_id, $second_user_id);
			if($stmt->execute()){
				$result = $stmt->get_result();
				$features = $result->fetch_all(MYSQLI_ASSOC);
				$stmt->close();
				return $features;
			}
 			return false;
		}
		
		// $username is the user who triggered the notification(not the who is going to recieve the notification)
		// $flatenSimilarFeatureString is the common features between two users
		public static function getSimilarFeatureNotificationBodyText($username, $flatenSimilarFeatureString){
			return $username." shares similar features with you - ".$flatenSimilarFeatureString;
		}
	
		public function addFeature($photoFiles, $user_id, $feature_name){
			if(!isset($photoFiles) || sizeof($photoFiles) < 1){
				return array("status" => false, self::FeatureKey => false, "errorCode" => 1); //missing cover for feature
			}
			if($this->isFeatureExistsForUser($feature_name, $user_id)){
				return array("status" => false, self::FeatureKey => false, "errorCode" => 2); //exists feature for user
			}
			$craeted_time = date('Y-m-d H:i:s');
			$stmt = $this->connection->prepare("INSERT INTO `$this->table_name` (`user_id`, `name`, `created_time`) VALUES(?, ?, ?)");
			$stmt->bind_param('iss',$user_id, $feature_name, $craeted_time);
			if($stmt->execute()){
				$stmt->close();
				$feature_id = $this->connection->insert_id;
				$user_feature_cover = new User_Feature_Cover();
				$fearturCoverInfo = $user_feature_cover->uploadFeatureCoverPicture($photoFiles, $user_id, $feature_id);
				if($fearturCoverInfo === false){
					$this->deleteRowById($feature_id);
				}else{
					$feature = array(
						"feature_id" => $feature_id,
						"name" => $feature_name,
						"photo" => array("url" => $fearturCoverInfo["featureCoverUrl"],
						  				 "hash" => $fearturCoverInfo["featureCoverHash"])
					);
					return array("status" => true, 
							    self::FeatureKey => $feature,
					 			"errorCode" => false);
				}
			}
			return array("status" => false, self::FeatureKey => false, "errorCode" => 3); //unknown error
		}
		
		public function isFeatureExistsForUser($feature_name, $user_id){
			return $this->checkStringColumnValueExistsForUser("name", $feature_name, $user_id);
		}
		
		public function deleteFeature($featureId, $userId){
			if($this->deleteRowForUserById($userId, $featureId)){
				//delete feature picture
				$feature_cover = new User_Feature_Cover();
				$feature_cover->deleteCoverForFeature($featureId, $userId);
				$post = new Post();
				$postIdList = $post->getPostIdListInFeature($featureId);
				if($postIdList){
					foreach($postIdList as $postId){
						$post->deletePost($postId["post_id"], $userId);
					}
				}
				return true;
			}
			return false;
		}
		
		public function getUserFeatureString($userId){
			$featureString = "";
			$features = $this->getAllRowsColumnBySelector('name', 'user_id', $userId, 'i');
			if($features !== false){
				foreach($features as $feature){
					$featureString .= $feature["name"].', ';
				}
			}
			$featureString = trim($featureString, " ,");
			return $featureString;
		}
		
		
		
	}		
?>