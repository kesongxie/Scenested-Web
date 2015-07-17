<?php
	include_once 'core_table.php';
	class User_In_Interest extends Core_Table{
		private $table_name = "User_In_Interest";
		public function __construct(){
			parent::__construct($this->table_name);
		}
		
		public function addUserInInterest($interest_id, $user_id, $user_in){
			$hash = $this->generateUniqueHash();
			$stmt = $this->connection->prepare("INSERT INTO `$this->table_name` (`interest_id`,`user_id`,`user_in`,`in_time`,`hash`) VALUES(?, ?, ?, ?, ?)");
			if($stmt){
				$stmt->bind_param('iiiss',$interest_id, $user_id, $user_in, date('Y-m-d H:i:s'), $hash);
				$expire = date('Y-m-d H:i:s', COOKIE_EXPIRE_TIME);
				if($stmt->execute()){
					$stmt->close();
					return $this->connection->insert_id;
				}
			}
			echo $this->connection->error;
			return false;
		}
		
		
		public function isUserInInterest($user_id,$user_in, $interest_id){
			$stmt = $this->connection->prepare("SELECT `hash` FROM `$this->table_name` WHERE `interest_id`=? && `user_in`=? && `user_id`=? LIMIT 1");
			if($stmt){
				$stmt->bind_param('iii',$interest_id, $user_in, $user_id);
				if($stmt->execute()){
					$result = $stmt->get_result();
					if($result && $result->num_rows == 1){
						$stmt->close();
						$row = $result->fetch_assoc();
						return $row['hash'];
					}
				}
			}
			echo $this->connection->error;
			return false;
		}
		
		public function removeUserFromInterest($user_id, $key, $hash){
			include_once 'User_Table.php';
			$user = new User_Table();
			$user_in = $user->getUserIdByKey($key);
			if($user_in !== false){
				$stmt = $this->connection->prepare("DELETE FROM `$this->table_name` WHERE `hash` = ? AND `user_in`=? AND `user_id`=? LIMIT 1");
				if($stmt){
					$stmt->bind_param('sii', $hash, $user_in, $user_id);
					if($stmt->execute()){
						$stmt->close();
						return true;
					}
				}
			}
			return false;
		}
		
		
		
		public function getAllFriendsInUsersInterestByUserId($user_id, $limit_num = -1, $offset = 0){
			if($limit_num > 0){
				$stmt = $this->connection->prepare("
				SELECT DISTINCT user.id, CONCAT(user.firstname,' ',user.lastname) AS fullname, user.id, user.unique_iden AS hash, user.user_access_url 
				FROM user_in_interest 
				LEFT JOIN user
				ON user_in_interest.user_in = user.id WHERE user_in_interest.user_id = ? LIMIT ?,?
				");
			}else{
				$stmt = $this->connection->prepare("
				SELECT DISTINCT user.id, CONCAT(user.firstname,' ',user.lastname) AS fullname, user.id, user.unique_iden AS hash, user.user_access_url 
				FROM user_in_interest 
				LEFT JOIN user
				ON user_in_interest.user_in = user.id WHERE user_in_interest.user_id = ?
				");			
			}
			if($stmt){
				if($limit_num > 0){
					$stmt->bind_param('iii',$user_id,$offset, $limit_num);
				}else{
					$stmt->bind_param('i',$user_id);
				}
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
		
		
		public function getUserFriendBlockByInterestId($interest_id, $limit_num = -1, $offset = 0){
			include_once 'Interest.php';
			$interest = new Interest();
			$user_id = $interest->getInterestUserIdByInterestId($interest_id);
			if($limit_num > 0){
				$stmt = $this->connection->prepare("
				SELECT DISTINCT user.id, CONCAT(user.firstname,' ',user.lastname) AS fullname, user.id, user.unique_iden AS hash, user.user_access_url 
				FROM user_in_interest 
				LEFT JOIN user
				ON user_in_interest.user_in = user.id WHERE user_in_interest.interest_id = ? AND user_in_interest.user_id = ? LIMIT ?,?
				");
			}else{
				$stmt = $this->connection->prepare("
				SELECT DISTINCT user.id, CONCAT(user.firstname,' ',user.lastname) AS fullname, user.id, user.unique_iden AS hash, user.user_access_url 
				FROM user_in_interest 
				LEFT JOIN user
				ON user_in_interest.user_in = user.id WHERE user_in_interest.interest_id = ? AND user_in_interest.user_id = ?
				");			
			}
			if($stmt){
				if($limit_num > 0){
					$stmt->bind_param('iiii',$interest_id,$user_id,$offset, $limit_num);
				}else{
					$stmt->bind_param('ii',$interest_id, $user_id);
				}
				if($stmt->execute()){
					 $result = $stmt->get_result();
					 if($result !== false){
						$user_found = $result->fetch_all(MYSQLI_ASSOC);
						$stmt->close();
						
						
						$content = false;
						if($user_found !== false && sizeof($user_found) >=1 ){
							include_once 'User_Profile_Picture.php';
							$profile = new User_Profile_Picture();
							include_once 'User_Table.php';
							$user = new User_Table();
							foreach($user_found as $u){
								$profile_pic = $profile->getLatestProfileImageForUser($u['id']);
								$cover_pic =  $user->getLatestCoverForuser($u['id']);
								$fullname = $u['fullname'];
								$hash = $u['hash'];
								$rows = $interest->getInterestNameForUser($u['id'], 2);
								$user_page_redirect =  USER_PROFILE_ROOT.$user->getUserAccessUrl($u['id']);
								$user_id = $u['id'];
								$result_array = array();
					
								$interest_list = '';
								if($rows !== false){
									$count = 1;
									foreach($rows as $row){
										if($count == sizeof($rows) -1 ){
											$interest_list .= $row['name'].' and ';
										}else if($count < sizeof($rows)){
											$interest_list .= $row['name'].', ';
										}else{
											$interest_list .= $row['name'];
										}
										$count++;
									}
								}
								$interest_list = trim($interest_list,', ');
						
						
								ob_start();
								include(TEMPLATE_PATH_CHILD.'user_profile.phtml');
								$user_profile= ob_get_clean();
								ob_start();
								include(TEMPLATE_PATH_CHILD.'friend_profile_wrapper.phtml');
								$content .= ob_get_clean();
							}
						}
						else{
						 	$interest_name = $interest->getInterestNameByInterestId($interest_id);
						}
						 ob_start();
						include(TEMPLATE_PATH_CHILD.'friend-content-inner-wrapper-block.phtml');
						$friend_block= ob_get_clean();
						return $friend_block;
					}
				}
			}
			return false;
		}
		
		
			
		
	}
	
?>