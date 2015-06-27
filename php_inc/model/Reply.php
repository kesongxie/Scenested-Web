<?php
	include_once 'Comment_Base.php';
	class Reply extends Comment_Base{
		private $table_name = "reply";
		private $template_path = "phtml/child/reply_block.phtml";
		public function __construct(){
			parent::__construct($this->table_name, $this->template_path);
		}
		
		public function addReplyToComment($key, $user_sent, $text){
			
		
			$unique_hash = $this->generateUniqueHash();
			
			$stmt = $this->connection->prepare("INSERT INTO `$this->table_name` (`target_id`,`user_id`,`user_id_get`,`text`,`sent_time`,`hash`) VALUES(?, ?, ?, ?, ?, ?)");
			include_once 'Comment.php';
			$comment = new Comment();
			$comment_id = $comment->getRowIdByHashkey($key); //get the target id, comment on which 
			if($comment_id !== false){
				$user_id_get = $comment->getColumnById('user_id',$comment_id);
				$time = date('Y-m-d H:i:s');
				$stmt->bind_param('iiisss',$comment_id, $user_sent, $user_id_get, $text, $time,$unique_hash);
				if($stmt->execute()){
					$stmt->close();
					$reply_id = $this->connection->insert_id;
					return $this->renderReplyBlockByReplyId($reply_id);
				}
			}
			return false;
		}
		
		/*
			render the reply block
		*/
		public function renderReplyBlockByReplyId($reply_id){
			$column_array = array('user_id','text','sent_time','hash');
			$reply = $this->getMultipleColumnsById($column_array, $reply_id);
			include_once 'User_Profile_Picture.php';
			$profile = new User_Profile_Picture();
			$post_owner_pic = $profile->getLatestProfileImageForUser($reply['user_id']);
			include_once 'User_Table.php';
			$user = new User_Table();
			$fullname = $user->getUserFullnameByUserIden($reply['user_id']);
			$post_time = convertDateTimeToAgo($reply['sent_time'], true);	
			$user_page_redirect =  USER_PROFILE_ROOT.$user->getUserAccessUrl($reply['user_id']);
			$text = $reply['text'];
			$user_id = $reply['user_id'];
			$hash = $reply['hash'];
			ob_start();
			include(SCRIPT_INCLUDE_BASE.$this->template_path);
			$reply_block = ob_get_clean();
			return $reply_block;
		}
	
		
	
	
	}
	
	
?>	