<?php
	include_once 'core_table.php';
	include_once 'User_Interest_Label_Image.php';
	
	class Interest extends Core_Table{
		private  $table_name = "interest";
		public $interest_label_image = null;
		public $new_interest_id = null; //new interest rows being added;
		public function __construct(){
			parent::__construct($this->table_name);
			$this->interest_label_image = new User_Interest_Label_Image();
		}


		//return the result set of the first row for the given user id
		public function getUserFirstInterestByUserId($user_id){
			$select_columns = array('id','user_id','name','description','experience');
			return $this->getFirstRowMultipleColumnsByUserId($select_columns, $user_id);
		}
		
		//return the result set of the last row for the given user id
		public function getUserLastInterestByUserId($user_id){
			$select_columns = array('id','user_id','name','description','experience');
			return $this->getLastRowMultipleColumnsByUserId($select_columns, $user_id);
		}
		
		//return teh set of the interest row for the given id
		public function getUserInterestBlockByInterestId($interest_id){
			$select_columns = array('id','user_id','name','description','experience');
			$interest_row = $this->getMultipleColumnsById($select_columns, $interest_id);
			return $this->loadInterestBlockByInterestResource($interest_row);
		}
		
		
		
		public function addInterestForUser($user_id, $name, $description, $experience, $label_image_file){
			$stmt = $this->connection->prepare("INSERT INTO `$this->table_name` (`user_id`,`name`,`description`,`experience`,`create_time`) VALUES(?, ?, ?, ?, ?)");
			$create_time = date('Y-m-d H:i:s');
			$name =  ucwords(strtolower($name));
			$stmt->bind_param('issis',$user_id, $name, $description, $experience, $create_time);
			if($stmt->execute()){
				$interest_id = $this->connection->insert_id;
				$label_image_url = "";
				if($label_image_file !== null){
					//upload image
					$label_image_url = $this->interest_label_image->uploadMediaForAssocColumn($label_image_file,$user_id, 'interest_id', $interest_id);
					if($label_image_url === false){
						$this->deleteRowById($interest_id);
						$stmt->close();
						return false;
					}
				}else{
					//random generate label image url
					include_once  'Default_User_Interest_Label_Image.php';
					$defualt_label_image = new Default_User_Interest_Label_Image();
					$random = rand(1,MAX_INTEREST_LABEL_COLOR_RANDOM_INDEX);
					$label_image_url = $random;
					$defualt_label_image->addDefaultInterestLabelImageForInterestId($interest_id,$label_image_url);
				}	
				$this->new_interest_id = $interest_id;
				$stmt->close();
				$this->interest_label_image->url = $label_image_url;
				return  $this->initContentForInterest($user_id,false);
				
			}
			return false;
		}
		
		
		public function updateInterestForUserByInterestId($interest_id, $user_id, $name, $description, $experience, $label_image_file){
			$user_id_for_interest_id = $this->getColumnById('user_id',$interest_id);
			if($user_id_for_interest_id !== false && $user_id_for_interest_id == $user_id){
				//the same, then allow editing
				if($name !== false){
					$this->setColumnById('name', $name, $interest_id);
				}
				if($description !== false){
					$this->setColumnById('description', $description, $interest_id);
				}
				if($experience !== false){
					$this->setColumnById('experience', $experience, $interest_id);
				}
				
				
				if($label_image_file != null){
					$old_image_url = $this->interest_label_image->getLabelImageUrllByInterestId($interest_id);
					$old_image_row_id =  $this->interest_label_image->getLabelImageFirstRowIdByInterestId($interest_id);
					$label_image_url = $this->interest_label_image->uploadMediaForAssocColumn($label_image_file,$user_id, 'interest_id', $interest_id);
					if($label_image_url === false){
						$stmt->close();
						return false;
					}
					include_once '../php_inc/File_Manager.php';
					$flile_m = new File_Manager();
					//remove the old record after successfully update the new media file
					$flile_m->removeMediaFileForUser($old_image_url, $user_id);
					$this->interest_label_image->deleteRowById($old_image_row_id);
					return true;
				}
			}
			echo   $this->connection->error;
			return false;
		}
		
		
		
		public function getLabelImageUrl(){
			return $this->interest_label_image->url;
		}
		
		public function interestExistForUser($interest_name, $user_id){
			return $this->checkColumnValueExistForUser('name',$interest_name, $user_id);
		}
		
		/*
			return array of content, left and right content
			$firstInterest set to true if the function return first interest
			otherwise return the last interest
		*/
		
		public function initContentForInterest($user_id, $firstInterest){
			if($firstInterest){
				$interestRow = $this->getUserFirstInterestByUserId($user_id);
			}else{
				$interestRow = $this->getUserLastInterestByUserId($user_id);
			}
			return $this->loadInterestBlockByInterestResource($interestRow);
			
		}
		
		public function getInterestNameByInterestId($interest_id){
			return $this->getColumnById('name',$interest_id);
		}
		
		public function getInterestDescriptionByInterestId($interest_id){
			return $this->getColumnById('description',$interest_id);
		}
		
		public function getInterestExperienceByInterestId($interest_id){
			return $this->getColumnById('experience',$interest_id);
		}
		
		
		public function loadInterestBlockByInterestResource($interestRow){
			if($interestRow !== false){
				$interest = $interestRow[0];
				//get interest profile
				$left_content = "";
				$right_content = "";
				$user_media_prefix = new User_Media_Prefix();
				$prefix = $user_media_prefix->getUserMediaPrefix($interest['user_id']).'/';
				$interest_label_image = $this->interest_label_image->getLabelImageUrllByInterestId($interest['id']);
				//var_dump($interest_label_image);
				if($interest_label_image !== false){
					$interest_label_image = $prefix.$interest_label_image;
				}
				$experience = $this->translateExperienceByNumber($interest['experience']);
				ob_start();
				include(SCRIPT_INCLUDE_BASE.'phtml/child/interest_profile.phtml');
				$interest_profile = ob_get_clean();
				$left_content = $interest_profile;
				//end getting interest profile
				
				ob_start();
				include(SCRIPT_INCLUDE_BASE.'phtml/child/interest_unit.phtml');
				$content = ob_get_clean();
				return $content;
			}
			return false;
		}
		
		
		
		
		
		public function translateExperienceByNumber($exp){
			if($exp >= 0){
				$experience= "";
				if($exp == 0){
					$experience = "Less than 1 year";
				}else if($exp == 1){
					$experience = "1 Year";
				}else if($exp >=2 && $exp<=10){
					$experience = $exp." Years";
				}else{
					$experience = "More than 10 years";
				}
				return $experience;
			}
			return false;
		}
		
		public function getUserInterestsLabel($user_id){
			$stmt = $this->connection->prepare("
			SELECT interest.name, interest.id, user_interest_label_image.picture_url
			FROM interest 
			LEFT JOIN user_interest_label_image
			ON interest.id=user_interest_label_image.interest_id  WHERE `user_id` = ? ORDER BY `id` ASC
			");
			if($stmt){
				$stmt->bind_param('i',$user_id);
				if($stmt->execute()){
					 $result = $stmt->get_result();
					 if($result !== false && $result->num_rows >= 1){
						$row = $result->fetch_all(MYSQLI_ASSOC);
						$stmt->close();
						return $row;
					 }
				}
			}
			return false;
		}
		public function deleteInterestForUserByInterestId($user_id, $interest_id){
			if($this->deleteRowForUserById($user_id, $interest_id)){
				return $this->interest_label_image->deleteLabelImageForUserByInterestId($user_id, $interest_id);
			}
		}
		
		
		
		
		

	}		
?>