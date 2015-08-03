<?php
	include_once 'core_table.php';
	include_once 'School.php';
	class Education extends Core_Table{ 
		private  $table_name = "education";
		public function __construct(){
			parent::__construct($this->table_name);
		}
		
		public function getEducationByUserId($user_id){
			$education = $this->getMultipleColumnsByUserId(array('school_id','study'),$user_id);
			if($education !== false){
				$school_name = School::getSchoolNameBySchoolId($education['school_id']);
				if($school_name !== false){
					return array('school_name'=>$school_name, 'study'=>$education['study']);
				}
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
	}

?>