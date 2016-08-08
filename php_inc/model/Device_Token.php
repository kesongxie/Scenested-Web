<?php
	class Device_Token extends Core_Table{
		private $table_name = "device_token";
		private $primary_key = "device_token_id";
		const DeviceTokenKey = "deviceToken";
	
		public function __construct(){
			parent::__construct($this->table_name, $this->primary_key);
		}
		
		public function getDeviceTokenForUser($user_id){
			return $this->getColumnByUserId('deviceToken',$user_id);
		}
		
		public function isTokenExistedForUser($user_id){
			return $this->isRowForUserExists($user_id);
		}
		
		
		public function updateTokenForUser($deviceToken, $user_id){
			$time = date('Y-m-d H:i:s');
			if($this->isTokenExistedForUser($user_id)){
				$this->setColumnByNumericSelector('deviceToken', $deviceToken, 'user_id', $user_id);
				$this->setColumnByNumericSelector('time', $time, 'user_id', $user_id);
				return $this->getDeviceTokenForUser($user_id);
			}else{
				//create row
				$stmt = $this->connection->prepare("INSERT INTO `$this->table_name` (`user_id`,`deviceToken`, `created_time`) VALUES(?, ?, ?)");
				$stmt->bind_param('iss', $user_id, $deviceToken, $time);
				if($stmt->execute()){
					$stmt->close();
					return $this->getDeviceTokenForUser($user_id);
				}
				return false;
			}
		}
	}		
?>