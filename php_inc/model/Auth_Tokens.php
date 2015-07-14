<?php
	include_once 'core_table.php';
	class Auth_Tokens extends Core_Table{
		private $table_name = "auth_tokens";
		public function __construct(){
			parent::__construct($this->table_name);
		}	
		public function add_auth_tokens($selector, $token, $user_id){
			$stmt = $this->connection->prepare("INSERT INTO `$this->table_name` (`selector`,`token`,`user_id`,`expire`) VALUES(?, ?, ?, ?)");
			$stmt->bind_param('ssis',$selector, $token, $user_id, $expire);
			$expire = date('Y-m-d H:i:s', COOKIE_EXPIRE_TIME);
			if($stmt->execute()){
				$stmt->close();
				return $this->connection->insert_id;
			}
			return false;
		}
		
		public function auth_token_valified(){
			if(!isset($_SESSION['id'])){
				if (isset($_COOKIE['identifier']) && isset($_COOKIE['token'])){
					$stmt = $this->connection->prepare("SELECT `token`,`user_id` FROM `$this->table_name` WHERE `selector` = ? AND `expire` > NOW() LIMIT 1 ");
					$stmt->bind_param('s', $_COOKIE['identifier']);
					if($stmt->execute()){
						 $result = $stmt->get_result();
						 if($result !== false && $result->num_rows == 1){
							$row = $result->fetch_assoc();
							$stmt->close();
							if(hash_equals(hash('sha256',$_COOKIE['token']),$row['token'])){
								$_SESSION['id'] = $row['user_id'];
								return true;
							}
						 }
					}
				}
			}else{
				return true;
			}
			return false;
		}
		
		public function tokenGenerator(){
			if(isset($_SESSION['id'])){
				//make sure to generate unqiue selector
				do{
					$selector = password_hash($_SESSION['id'],PASSWORD_DEFAULT);
					$token = bin2hex(openssl_random_pseudo_bytes(12));
					$hashed_token = hash('sha256',$token);
					$token_found = $this->getColumnBySelector('token', 'selector', $selector);
				}while($token_found !== false);
				
				if($this->add_auth_tokens($selector, $hashed_token, $_SESSION['id']) !== false){
					setcookie("identifier", $selector, COOKIE_EXPIRE_TIME, '/');
					setcookie("token", $token, COOKIE_EXPIRE_TIME, '/');
					return true;
				}
			}
			return false;
		}
		
		public function deleteIdentifierAndToken(){
			if(isset( $_COOKIE["identifier"])){
				$this->deleteRowBySelector('selector', $_COOKIE["identifier"]);
			}
		}
		
		
	
	}	
?>