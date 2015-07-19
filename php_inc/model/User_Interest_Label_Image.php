<?php
	include_once 'core_table.php';
	include_once 'User_Media_Base.php';

	class User_Interest_Label_Image extends User_Media_Base{
		private $table_name = "user_interest_label_image";
		public $url = null;
		public function __construct(){
			parent::__construct($this->table_name);
		}
		
		public function uploadInterestLabelImage($label_image_file,$user_id, $interest_id){
			$hash = $this->generateUniqueHash();
			$this->uploadMediaForAssocColumn($label_image_file, $user_id,$hash, 'interest_id', $interest_id);
		
		} 
		
		
		public function getLabelImageHashByInterestId($interest_id){
			return 	 $this->getColumnBySelector('hash', 'interest_id', $interest_id);
		}

		public function hasLabelImageForInterestMinPath($interest_id){
			return $this->getColumnBySelector('picture_url','interest_id',$interest_id);
		}
		
		public function hasLabelImageForInterest($interest_id){
			$result = $this->getMultipleColumnsBySelector(array('picture_url','user_id'), 'interest_id', $interest_id);
			if($result !== false){
				include_once 'User_Media_Prefix.php';
				$user_media_prefix = new User_Media_Prefix();
				$prefix = $user_media_prefix->getUserMediaPrefix($result['user_id']); //folder for the given user
				return U_IMGDIR.$prefix.'/'.$result['picture_url'];
			}else{
				return false;
			}	
		}

		
		public function getLabelImageUrlByInterestId($interest_id){
			$result = $this->getMultipleColumnsBySelector(array('picture_url','user_id'), 'interest_id', $interest_id);
			if($result !== false){
				include_once 'User_Media_Prefix.php';
				$user_media_prefix = new User_Media_Prefix();
				$prefix = $user_media_prefix->getUserMediaPrefix($result['user_id']); //folder for the given user
				return U_IMGDIR.$prefix.'/'.$result['picture_url'];
			}else{
				return DEFAULT_INTEREST_LABEL_IMAGE;
			}
		}
		
		public function getLabelImageFirstRowIdByInterestId($interest_id){
			return 	 $this->getColumnBySelector('id', 'interest_id', $interest_id);
		}
		
		public function deleteLabelImageForUserByInterestId($user_id, $interest_id){
			$url = $this->getColumnBySelector('picture_url','interest_id',$interest_id);
			if($url !== false){
				$this->deleteMediaByPictureUrl($url, $user_id);
				$this->deleteRowBySelector('interest_id', $interest_id);
			}
		}
		
	}		
?>