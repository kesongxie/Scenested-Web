<?php
	include_once 'core_table.php';
	include_once 'User_Media_Base.php';

	class Default_User_Interest_Label_Image extends User_Media_Base{
		var $table_name = "default_user_interest_label_image";
		
		public function __construct(){
			parent::__construct($this->table_name);
		}
		
		public function getLabelImageUrllByInterestId($interest_id){
			 return  $this->getColumnBySelector('picture_url', 'interest_id', $interest_id);
		}
		
		public function addDefaultInterestLabelImageForInterestId($interest_id,$picture_url){
			$stmt = $this->connection->prepare("INSERT INTO `$this->table_name` (`interest_id`,`picture_url`,`upload_time`) VALUES(?, ?, ?)");
			$time = date('Y-m-d H:i:s');
			if($stmt){
				$stmt->bind_param('iss',$interest_id,$picture_url,$time);
				if($stmt->execute()){
					$stmt->close();
					return true;
				}
			}
			return false;
		}
		
		
		
		
		
		
	}		
?>