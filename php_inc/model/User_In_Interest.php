<?php
	include_once MODEL_PATH.'Core_Table.php';
	class User_In_Interest extends Core_Table{
		private $table_name = "user_in_interest";
		private $initial_interest_friend = TEMPLATE_PATH_CHILD."initial_profile_interest_friend_block.phtml";
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
		
		//get which interest the $user_in was added in $user_id's interest
		public function getUserInInterest($user_in, $user_id){
			$stmt = $this->connection->prepare("
			SELECT user_in_interest.hash, user_in_interest.interest_id, interest.name, user_interest_label_image.picture_url, user.firstname
			FROM user_in_interest 
			LEFT JOIN interest
			ON user_in_interest.interest_id = interest.id
			LEFT JOIN user
			ON interest.user_id = user.id
			LEFT JOIN user_interest_label_image
			ON interest.id = user_interest_label_image.interest_id  WHERE user_in_interest.user_in = ? && user_in_interest.user_id = ?
			");		
			if($stmt){
				$stmt->bind_param('ii', $user_in, $user_id);
				if($stmt->execute()){
					$result = $stmt->get_result();
					if($result && $result->num_rows >= 1){
						$row = $result->fetch_all(MYSQLI_ASSOC);
						$stmt->close();
						include_once 'User_Media_Prefix.php';
						$prefix = new User_Media_Prefix();
						$media_prefix = $prefix->getUserMediaPrefix($user_id).'/';
						
						foreach($row as &$r){
							if(isset($r['picture_url']) && $r['picture_url'] !== null){
								$url = U_IMGDIR.$media_prefix.$r['picture_url'];
								$url = (is_url_exist($url)?$url:DEFAULT_INTEREST_LABEL_IMAGE);
							}else{
								$url = DEFAULT_INTEREST_LABEL_IMAGE;
							}
							$r['picture_url'] = $url;
						}
						return $row;
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
		
		
		
		public function getAllFriendsInUsersInterest(){
			$stmt = $this->connection->prepare("
			SELECT DISTINCT `user_id` FROM user_in_interest WHERE (`user_in` = ? AND `user_id` != ?)
			UNION 
			SELECT DISTINCT `user_in` FROM user_in_interest WHERE (`user_id` = ? AND `user_in` != ?)
			");			
			
			if($stmt){
				$stmt->bind_param('iiii',$_SESSION['id'], $_SESSION['id'], $_SESSION['id'], $_SESSION['id']);
				if($stmt->execute()){
					 $result = $stmt->get_result();
					 if($result !== false && $result->num_rows >= 1){
						$row = $result->fetch_all(MYSQLI_ASSOC);
						$stmt->close();
						return $row;
					 }
				}
			}
			echo $this->connection->error;
			return false;
		}
		
		
		
		public function getAllFriendsPlainListInUsersInterestByUserId($user_id, $limit_num = -1, $offset = 0){
			if($limit_num > 0){
				$stmt = $this->connection->prepare("
				SELECT DISTINCT user.id
				FROM user_in_interest 
				LEFT JOIN user
				ON user_in_interest.user_in = user.id || user_in_interest.user_id = user.id WHERE (user.id != ? && (user_in_interest.user_id = ? || user_in_interest.user_in = ?)) LIMIT ?,?
				");
			}else{
				$stmt = $this->connection->prepare("
				SELECT DISTINCT user.id
				FROM user_in_interest 
				LEFT JOIN user
				ON user_in_interest.user_in = user.id || user_in_interest.user_id = user.id WHERE (user.id != ? && (user_in_interest.user_id = ? || user_in_interest.user_in = ?)) 
				");			
			}
			if($stmt){
				if($limit_num > 0){
					$stmt->bind_param('iiiii',$user_id,$user_id, $user_id,$offset, $limit_num);
				}else{
					$stmt->bind_param('iii',$user_id, $user_id,$user_id);
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
		
		public function getAllFriendsInUsersInterestByUserId($user_id, $limit_num = -1, $exclusive_list = "'-1'"){
			if($limit_num > 0){
				$stmt = $this->connection->prepare("
				SELECT DISTINCT user.id, CONCAT(user.firstname,' ',user.lastname) AS fullname, user.unique_iden AS hash, user.user_access_url 
				FROM user_in_interest 
				LEFT JOIN user
				ON user_in_interest.user_in = user.id || user_in_interest.user_id = user.id
				WHERE (user.id != ? && (user_in_interest.user_id = ? || user_in_interest.user_in = ?)) AND user.id NOT IN($exclusive_list) LIMIT ?
				");
			}else{
				$stmt = $this->connection->prepare("
				SELECT DISTINCT user.id, CONCAT(user.firstname,' ',user.lastname) AS fullname, user.unique_iden AS hash, user.user_access_url 
				FROM user_in_interest 
				LEFT JOIN user
				ON user_in_interest.user_in = user.id || user_in_interest.user_id = user.id
				WHERE (user.id != ? && (user_in_interest.user_id = ? || user_in_interest.user_in = ?)) AND user.id NOT IN($exclusive_list) 
				");			
			}
			if($stmt){
				if($limit_num > 0){
					$stmt->bind_param('iiii',$user_id,$user_id, $user_id, $limit_num);
				}else{
					$stmt->bind_param('iii',$user_id, $user_id,$user_id);
				}
				if($stmt->execute()){
					 $result = $stmt->get_result();
					 if($result !== false && $result->num_rows >= 1){
						$rows = $result->fetch_all(MYSQLI_ASSOC);
						$stmt->close();
						
						
						if($exclusive_list == "'-1'"){
							$_SESSION['loaded_all_friend_list'] = "";	
						}else{
							$_SESSION['loaded_all_friend_list'] = $exclusive_list.',';	
						}
						
						foreach($rows as $row){
							$_SESSION['loaded_all_friend_list'].="'".$row['id']."',";
						}
						
						$_SESSION['loaded_all_friend_list'] = trim($_SESSION['loaded_all_friend_list'], ',');
						return $rows;
					 }
				}
			}
			return false;
		}
		
		
		public function getUserInInterestByInterestId($interest_id, $limit_num = -1, $exclusive_list = "'-1'"){
			include_once 'Interest.php';
			$interest = new Interest();
			$user_id = $interest->getInterestUserIdByInterestId($interest_id);
			if($limit_num > 0){
				$stmt = $this->connection->prepare("
				SELECT DISTINCT user.id, CONCAT(user.firstname,' ',user.lastname) AS fullname, user.id, user.unique_iden AS hash, user.user_access_url 
				FROM user_in_interest 
				LEFT JOIN user
				ON user_in_interest.user_in = user.id
				WHERE user_in_interest.interest_id = ? AND user_in_interest.user_id = ? AND user.id NOT IN($exclusive_list) LIMIT ?
				");
			}else{
				$stmt = $this->connection->prepare("
				SELECT DISTINCT user.id, CONCAT(user.firstname,' ',user.lastname) AS fullname, user.id, user.unique_iden AS hash, user.user_access_url 
				FROM user_in_interest 
				LEFT JOIN user
				ON user_in_interest.user_in = user.id
				WHERE user_in_interest.interest_id = ? AND user_in_interest.user_id = ?  AND user.id NOT IN($exclusive_list)
				");			
			}
			if($stmt){
				if($limit_num > 0){
					$stmt->bind_param('iii',$interest_id,$user_id, $limit_num);
				}else{
					$stmt->bind_param('ii',$interest_id, $user_id);
				}
				if($stmt->execute()){
					 $result = $stmt->get_result();
					 if($result !== false && $result->num_rows >= 1){
						$rows = $result->fetch_all(MYSQLI_ASSOC);
						$stmt->close();
						
						if($exclusive_list == "'-1'"){
							$_SESSION['loaded_friend_list_'.$interest_id] = "";	
						}else{
							$_SESSION['loaded_friend_list_'.$interest_id] = $exclusive_list.',';	
						}
						
						foreach($rows as $row){
							$_SESSION['loaded_friend_list_'.$interest_id].="'".$row['id']."',";
						}
						$_SESSION['loaded_friend_list_'.$interest_id] = trim($_SESSION['loaded_friend_list_'.$interest_id], ',');
						return $rows;
					}
				}
			}
			return false;
		}
		
		
		
		public function getContactSearchByKeyWord($key_word){
			$stmt = $this->connection->prepare("
			
			SELECT DISTINCT CONCAT('m-',user_in_interest.user_in) AS queue
			FROM user_in_interest 
			LEFT JOIN user
			ON user_in_interest.user_in = user.id  WHERE user_in_interest.user_id = ?  && CONCAT(user.firstname,' ',user.lastname) LIKE ? 
			
			UNION 
			
			SELECT DISTINCT CONCAT('m-',user_in_interest.user_id) AS queue
			FROM user_in_interest 
			LEFT JOIN user
			ON user_in_interest.user_id = user.id  WHERE user_in_interest.user_in = ?  && CONCAT(user.firstname,' ',user.lastname) LIKE ? 
			
			UNION
			
			SELECT DISTINCT  CONCAT('m-',user_in_interest.user_in) AS queue
			FROM user_in_interest 
			LEFT JOIN interest
			ON user_in_interest.interest_id = interest.id  WHERE (user_in_interest.user_id = ?  && (interest.name LIKE ? || interest.description LIKE ?)  )
			
			UNION 
			
			SELECT DISTINCT  CONCAT('m-',user_in_interest.user_id) AS queue
			FROM user_in_interest 
			LEFT JOIN interest
			ON user_in_interest.interest_id = interest.id  WHERE (user_in_interest.user_in = ?  && (interest.name LIKE ? || interest.description LIKE ?)  )
			
			
			
			UNION 
			
			SELECT DISTINCT  CONCAT('m-',user_in_interest.user_in) AS queue
			FROM user_in_interest 
			LEFT JOIN interest
			ON user_in_interest.user_in = interest.user_id  WHERE ( user_in_interest.user_id = ?  && (interest.name LIKE ? || interest.description LIKE ?)  )
			
			UNION 
			
			SELECT DISTINCT  CONCAT('m-',user_in_interest.user_id) AS queue
			FROM user_in_interest 
			LEFT JOIN interest
			ON user_in_interest.user_id = interest.user_id  WHERE ( user_in_interest.user_in = ?  && (interest.name LIKE ? || interest.description LIKE ?)  )
			
			
			
			");
			
			if($stmt){
				$key_word = '%'.$key_word.'%';
				$stmt->bind_param('isisississississ',$_SESSION['id'], $key_word, $_SESSION['id'], $key_word, $_SESSION['id'],$key_word,$key_word,$_SESSION['id'],$key_word,$key_word, $_SESSION['id'],$key_word,$key_word,  $_SESSION['id'],$key_word,$key_word);
				if($stmt->execute()){
					 $result = $stmt->get_result();
					 if($result !== false && $result->num_rows >= 1){
						$row = $result->fetch_all(MYSQLI_ASSOC);
						$stmt->close();
						return $row;
					 }
				}
			}
			echo $this->connection->error;
			return false;
		}
		
		
		
		
		public function getUserFriendBlockByInterestId($interest_id, $limit_num = -1, $exclusive_list = "'-1'"){
			$user_found = $this->getUserInInterestByInterestId($interest_id, $limit_num, $exclusive_list);
			$content = false;
			include_once 'User_Table.php';
			$user = new User_Table();
			if($user_found !== false && sizeof($user_found) >=1 ){
				foreach($user_found as $u){
					$content .= $user->returnUserAvatorByResource($u);
				}
				ob_start();
				include(TEMPLATE_PATH_CHILD.'friend-content-inner-wrapper-block.phtml');
				$friend_block= ob_get_clean();
				return $friend_block;
			}
			else{
				include_once 'Interest.php';
				$interest = new Interest();
				$interest_name = $interest->getInterestNameByInterestId($interest_id);
				$request_user_page_id = $interest->getInterestUserIdByInterestId($interest_id);
				if($request_user_page_id != $_SESSION['id']){
					include_once 'User_Table.php';
					$user = new User_Table();
					$request_user_page_firstname = $user->getUserFirstNameByUserIden($request_user_page_id);
				}else{
					$user_found = $interest->returnMatchedUserBySearchkeyWord($interest_name, 5);
				}
				ob_start();
				include($this->initial_interest_friend);
				$content = ob_get_clean();
				return $content;
			}
		}
		
		public function leaveInterest($user_in, $hash){
			$stmt = $this->connection->prepare("DELETE FROM `$this->table_name` WHERE `user_in` = ? && `hash` = ? ");
			if($stmt){
				$stmt->bind_param('is', $user_in, $hash);
				if($stmt->execute()){
					$stmt->close();
					return true;
				}
			}
			return false;
		}
		
		
		public function deleteAllUserInByInterestId($user_id, $interest_id){
			$this->deleteRowBySelectorForUser('interest_id', $interest_id, $user_id, true);
		}
		
		
		public function getFriendPlainListForUser($user_id){
			$user_friends = $this->getAllFriendsPlainListInUsersInterestByUserId($user_id);
			$list = "";
			if($user_friends !== false && sizeof($user_friends) > 0){
				foreach($user_friends as $f){
					$list.="'".$f['id']."',";
				}
				$list = trim($list,',');
			}
			return $list;
		}
		
		public function returnInvitationSearchForAllFriends($key_word){
			$stmt = $this->connection->prepare("
			SELECT DISTINCT user.id, CONCAT(user.firstname,' ',user.lastname) AS fullname, user.unique_iden AS hash, user.user_access_url 
			FROM user_in_interest 
			LEFT JOIN user
			ON user_in_interest.user_in = user.id  WHERE user_in_interest.user_id = ?  && CONCAT(user.firstname,' ',user.lastname) LIKE ? 
			
			UNION 
			
			SELECT DISTINCT user.id, CONCAT(user.firstname,' ',user.lastname) AS fullname, user.unique_iden AS hash, user.user_access_url 
			FROM user_in_interest 
			LEFT JOIN user
			ON user_in_interest.user_id = user.id  WHERE user_in_interest.user_in = ?  && CONCAT(user.firstname,' ',user.lastname) LIKE ? 
			
			");
			
			if($stmt){
				$key_word = '%'.$key_word.'%';
				$stmt->bind_param('isis',$_SESSION['id'], $key_word, $_SESSION['id'], $key_word);
				if($stmt->execute()){
					 $result = $stmt->get_result();
					 if($result !== false && $result->num_rows >= 1){
						$row = $result->fetch_all(MYSQLI_ASSOC);
						$stmt->close();
						return $row;
					 }
				}
			}
			echo $this->connection->error;
			return false;
		}
		
		
		public function returnInvitationSearchByInterestId($key_word, $interest_id){
			include_once 'Interest.php';
			$interest = new Interest();
			$user_id = $interest->getInterestUserIdByInterestId($interest_id);
			
			$stmt = $this->connection->prepare("
			SELECT DISTINCT user.id, CONCAT(user.firstname,' ',user.lastname) AS fullname, user.id, user.unique_iden AS hash, user.user_access_url 
			FROM user_in_interest 
			LEFT JOIN user
			ON user_in_interest.user_in = user.id WHERE user_in_interest.interest_id = ? AND user_in_interest.user_id = ? && CONCAT(user.firstname,' ',user.lastname) LIKE ? 
			");			
			if($stmt){
				$key_word = '%'.$key_word.'%';
				$stmt->bind_param('iis',$interest_id, $user_id, $key_word);
				if($stmt->execute()){
					 $result = $stmt->get_result();
					 if($result !== false && $result->num_rows >= 1 ){
						$user_found = $result->fetch_all(MYSQLI_ASSOC);
						$stmt->close();
						return $user_found;
					}
				}
			}
			return false;
		}
		
		/*
			get which interest id the $user_in is in the $interest_owner
		*/
		public function getUserConnectionInterestIdForUser($user_id, $user_in){
			$stmt = $this->connection->prepare("
			SELECT `interest_id` FROM  `$this->table_name` WHERE `user_id` = ? AND `user_in` = ? LIMIT 1
			");			
			if($stmt){
				$stmt->bind_param('ii', $user_id, $user_in);
				if($stmt->execute()){
					 $result = $stmt->get_result();
					 if($result !== false && $result->num_rows >= 1 ){
						$result = $result->fetch_assoc();
						$stmt->close();
						return $result['interest_id'];
					}
				}
			}
			return false;
		}
		
		//$key is the id for the interest and -1 stands for all friends 
		//$user_key the key for the user page
		public function loadMoreFriendFeedForInterestId($user_key, $key){
			include_once MODEL_PATH.'User_Table.php';
			$user = new User_Table();
			$page_user_id = $user->getUserIdByKey($user_key);
			if($page_user_id !== false){
				if($key == '-1'){
					//load more from all friends 
					$user_found = $this->getAllFriendsInUsersInterestByUserId($page_user_id, 4, $_SESSION['loaded_all_friend_list']);
				}else{
					//load more from a specific interest
					$user_found = $this->getUserInInterestByInterestId($key, 4, $_SESSION['loaded_friend_list_'.$key]);
				}
				if($user_found !== false){
					$content ="";	
					foreach($user_found as $u){
						$content .= $user->returnUserAvatorByResource($u);
					}
					return $content;
				}
			}
			return false;
		}
		
			
			
		
	}
	
?>