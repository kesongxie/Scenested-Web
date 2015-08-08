<?php
	include_once 'core_table.php';
	include_once 'School.php';
	include_once 'Major.php';
	class Education extends Core_Table{ 
		private  $table_name = "education";
		public function __construct(){
			parent::__construct($this->table_name);
		}
		
		
		
		public function getSchoolIdByUserId($user_id){
			$school_id = $this->getColumnByUserId('school_id',$user_id);
			return $school_id;
		}
		
		
		public function getEducationByUserId($user_id){
			$education = $this->getMultipleColumnsByUserId(array('school_id','major_id'),$user_id);
			if($education !== false){
				$school_name = false;
				$major_name = false;
				if($education['school_id'] !== null){
					$school_name = School::getSchoolNameBySchoolId($education['school_id']);
				}
				if($education['major_id'] !== null){
					$major_name = Major::getMajorNameByMajorId($education['major_id']);
				}
				return array('school_name'=>$school_name, 'study'=>$major_name);
			}
			return false;
		}
		
		public function addSchoolNameForUser($school_name){
			$school_id = School::getSchooIdBySchoolName($school_name);
			if($school_id !== false){
				if($this->isRowForUserExists($_SESSION['id'])){
					$this->setColumnByUserId('school_id', $school_id, $_SESSION['id']);
					return true;
				}else{
					$hash =  $this->generateUniqueHash();
					$stmt = $this->connection->prepare("INSERT INTO `$this->table_name` (`user_id`,`school_id`,`hash`) VALUES(?,  ?, ?)");
					$stmt->bind_param('iis',$_SESSION['id'], $school_id, $hash);
					if($stmt->execute()){
						$stmt->close();
						return true;
					}
				}
			}
			return false;
		}
		
		public function addMajorForUser($major_name){
			$major_id = Major::getMajorIdByMajorName($major_name);
			if($major_id !== false){
				if($this->isRowForUserExists($_SESSION['id'])){
					$this->setColumnByUserId('major_id', $major_id, $_SESSION['id']);
					return true;
				}else{
					$hash =  $this->generateUniqueHash();
					$stmt = $this->connection->prepare("INSERT INTO `$this->table_name` (`user_id`,`school_id`,`hash`) VALUES(?,  ?, ?)");
					$stmt->bind_param('iis',$_SESSION['id'], $school_id, $hash);
					if($stmt->execute()){
						$stmt->close();
						return true;
					}
				}
			}
			return false;
		}
		
		public function returnMatchedUserForSchool($school_name){
			include_once 'Interest.php';
			$interest = new Interest();
			$mine_interests = $interest->getInterestNameForUser($_SESSION['id'], -1);
			$interest_like = '';
			if($mine_interests !== false){
				foreach($mine_interests as $interest){
 					$interest_like .= $interest['name'].'|';	
				}
				$interest_like = trim($interest_like,'|');
				$search_school_id = School::getSchooIdBySchoolName($school_name);
			
				if($search_school_id !== false){
					//use random offset to get random user
					$stmt = $this->connection->prepare("
					SELECT DISTINCT user.id, CONCAT(user.firstname,' ',user.lastname) AS fullname, user.id, user.unique_iden AS hash, user.user_access_url 
					FROM education 
					LEFT JOIN user
					ON education.user_id=user.id
					LEFT JOIN interest
					ON user.id = interest.user_id
					WHERE education.school_id = ? AND (interest.name REGEXP ?  || interest.description REGEXP ?) AND user.id !=? 
					
					UNION 
					
					SELECT DISTINCT user.id,  CONCAT(user.firstname,' ',user.lastname) AS fullname, user.id, user.unique_iden AS hash, user.user_access_url 
					FROM education 
					LEFT JOIN user
					ON education.user_id=user.id
					WHERE  education.school_id = ?  AND user.id !=? 
					");
					if($stmt){
						$stmt->bind_param('issiii',$search_school_id, $interest_like,$interest_like,$_SESSION['id'],$search_school_id, $_SESSION['id']);
						if($stmt->execute()){
							 $result = $stmt->get_result();
							 if($result !== false && $result->num_rows >= 1){
								$row = $result->fetch_all(MYSQLI_ASSOC);
								$stmt->close();
								return $row;
							 }
						}
					}
				}
			}
			echo $this->connection->error;
			return false;
		}
		
		
		
		
		
		
		
		
		
		
	}

?>