<?php
	include_once 'Education.php';
	class Abouts{
		private $education = null;
		private $abouts_side_block_tempate_path = TEMPLATE_PATH_CHILD."abouts_side_block.phtml";
		public function __construct(){
			$this->education = new Education();
		}
		public function getEducationBlockForUser($user_id){
			$education = $this->education->getEducationByUserId($user_id);
			include_once 'User_Table.php';
			$user = new User_Table();
			$firstname = $user->getUserFirstNameByUserIden($user_id);
			ob_start();
			include($this->abouts_side_block_tempate_path);
			$content = ob_get_clean();
			return $content;
		}	
	
		
	
	
	}

?>