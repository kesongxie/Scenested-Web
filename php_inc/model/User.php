<?php
	class User extends Core_Table{
		private $table_name = "User";
		private $primary_key = "user_id";
		private $user_id;
		
		
		public function __construct($user_id = null){
			parent::__construct($this->table_name, $this->primary_key);
			$this->user_id = $user_id;
		}
		
		public function getUserFullname(){
			if($this->user_id !== null){
				return $this->getColumnById('fullname', $this->user_id);
			}
			return false;
		}
		
		public function getUserAvatorUrl(){
			if($this->user_id !== null){
				$avator = new User_Profile_Avator();
				return $avator->getLatestProfileImageForUser($this->user_id);
			}
			return false;
		}
		
		public function getUserMediaPrefix(){
			if($this->user_id !== null){
				$prefix = new User_Media_Prefix();
				return $prefix->getUserMediaPrefix($this->user_id);
			}
			return false;
		}
		
		public function loadUserProfileScene(){
			if($this->user_id !== null){
				$scene = new Scene();
				return $scene->getProfileSceneForUser($this->user_id);
			}
			return false;
		}
		
		public function getSceneNumberForUser(){
			if($this->user_id !== null){
				$scene = new Scene();
				return $scene->getSceneNumberForUser($this->user_id);
			}
			return false;
			
		}
		
		
	}		
?>