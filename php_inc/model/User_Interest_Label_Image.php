<?php
	include_once 'core_table.php';
	include_once 'User_Media_Base.php';

	class User_Interest_Label_Image extends User_Media_Base{
		var $table_name = "user_interest_label_image";
		var $url = null;
		public function __construct(){
			parent::__construct($this->table_name);
		}
		
		public function getLabelImageUrllByInterestId($interest_id){
			 return  $this->getColumnBySelector('picture_url', 'interest_id', $interest_id);
		}
		
		public function getLabelImageFirstRowIdByInterestId($interest_id){
			return 	 $this->getColumnBySelector('id', 'interest_id', $interest_id);

		}
		
	}		
?>