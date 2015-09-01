<?php
	include_once 'Comment_Base.php';
	class Comment extends Comment_Base{
		private $table_name = "comment";
		private $template_path = "phtml/child/comment_block.phtml";
		private $popover_notification_template_path = "phtml/child/popover_notification_comment_block.phtml";
		private $slid_show_template_path = "phtml/child/slideshow_comment_block.phtml";
		public function __construct(){
			parent::__construct($this->table_name);
		}
		
		public function addPostComment($key, $user_sent, $text){
			$unique_hash = $this->generateUniqueHash();
			$stmt = $this->connection->prepare("INSERT INTO `$this->table_name` (`activity_id`,`user_id`,`user_id_get`,`text`,`sent_time`,`hash`) VALUES(?, ?, ?, ?, ?, ?)");
			include_once 'Interest_Activity.php';
			$activity = new Interest_Activity();
			$activity_id = $activity->getRowIdByHashkey($key); //get the target id, comment on which 
			if($activity_id !== false){
				$user_id_get = $activity->getColumnById('user_id',$activity_id);
				$time = date('Y-m-d H:i:s');
				$stmt->bind_param('iiisss',$activity_id, $user_sent, $user_id_get, $text, $time,$unique_hash);
				if($stmt->execute()){
					$stmt->close();
				 	$comment_id = $this->connection->insert_id;
					if($user_sent != $user_id_get){
						$this->noti_queue->addNotificationQueueForUser($user_id_get, $comment_id);
					}
					return $this->renderCommentBlockByCommentId($comment_id);
				}
			}
			return false;
		}
		
		
		public function renderCommentBlockByCommentId($comment_id){
			$column_array = array('activity_id','user_id','text','sent_time','hash');
			$comment = $this->getMultipleColumnsById($column_array, $comment_id);
			include_once 'User_Profile_Picture.php';
			$profile = new User_Profile_Picture();
			$post_owner_pic = $profile->getLatestProfileImageForUser($comment['user_id']);
			include_once 'User_Table.php';
			$user = new User_Table();
			$fullname = $user->getUserFullnameByUserIden($comment['user_id']);
			$post_time = convertDateTimeToAgo($comment['sent_time'], false);	
			$user_page_redirect =  USER_PROFILE_ROOT.$user->getUserAccessUrl($comment['user_id']);
			$unique_iden = $user->getUniqueIdenForUser($comment['user_id']);
			$text = $comment['text'];
			$user_id = $comment['user_id'];
			$hash = $comment['hash'];
			$post_owner_id = $this->getPostUserIdByActivityId($comment['activity_id']);
			$reply_block = $this->getReplyBlockByCommentId($comment_id);
			ob_start();
			include(SCRIPT_INCLUDE_BASE.$this->template_path);
			$comment_block = ob_get_clean();
			return $comment_block;
		}
		
		
		
	 	public function renderSlideShowCommentBlockByCommentId($comment_id, $firstComment){
			$column_array = array('activity_id','user_id','text','hash');
			$comment = $this->getMultipleColumnsById($column_array, $comment_id);
			include_once 'User_Profile_Picture.php';
			$profile = new User_Profile_Picture();
			$post_owner_pic = $profile->getLatestProfileImageForUser($comment['user_id']);
			include_once 'User_Table.php';
			$user = new User_Table();
			$fullname = $user->getUserFullnameByUserIden($comment['user_id']);
			$hash = $comment['hash'];
			$user_page_redirect =  USER_PROFILE_ROOT.$user->getUserAccessUrl($comment['user_id']);
			$text = $comment['text'];
			//$reply_block = $this->getSlideShowReplyBlockByCommentId($comment_id);
			ob_start();
			include(SCRIPT_INCLUDE_BASE.$this->slid_show_template_path);
			$comment_block = ob_get_clean();
			return $comment_block;	
		}
		
		
		
		
		
		
		public function getReplyBlockByCommentId($comment_id){
			include_once 'Reply.php';
			$reply = new Reply();
			$reply_block = '';
			$idCollection = $reply->getSelfIdCollectionByCommentId($comment_id);
			if($idCollection !== false && sizeof($idCollection) > 0){
				foreach($idCollection as $row ){
					$reply_block.= $reply->renderReply($row['id']);
				}
			}
			return $reply_block;
		}
		
		
		public function getCommentNumberForTarget($activity_id){
			include_once 'Reply.php';
			$reply = new Reply();
			$numComment = $this->getRowsNumberForNumericColumn('activity_id', $activity_id);
			$numComment = ($numComment !== false)?$numComment:0;
			$numReply = $reply->getRowsNumberForNumericColumn('activity_id', $activity_id);
			$numReply = ($numReply !== false)?$numReply:0;
			$num = $numComment + $numReply;
			return ($num !== false)?$num:0;
		}
		
		public function deleteComment($user_id, $key){
			$post_id = $this->getColumnBySelector('activity_id', 'hash', $key);
			$post_owner =  $this->getPostUserIdByActivityId($post_id);
			if($user_id == $post_owner){
				$comment_id = $this->deleteNotiQueueForKey($key);
				$this->deleteRowById($comment_id);
			}else{
				$this->deleteNotiQueueForKey($key);
				$comment_id = $this->deleteCommentForUserByKey($user_id, $key);
			}
			if($comment_id !== false){
				include_once 'Reply.php';
				$reply = new Reply();
				$reply->deleteAllReplysForCommentId($comment_id);
			}
		}
		
		public function deleteAllCommentsByActivityId($activity_id){
			$rows = $this->getAllRowsMultipleColumnsBySelector(array('id','hash'), 'activity_id',$activity_id);
			include_once 'Reply.php';
			$reply = new Reply();
			if($rows !== false && sizeof($rows) > 0){
				foreach($rows as $row){
					$this->deleteNotiQueueForKey($row['hash']);
					$reply->deleteAllReplysForCommentId($row['id']);
				}
				$this->deleteRowByNumericSelector('activity_id', $activity_id);
			}
		}
		
		public function getSelfIdCollectionByTargetId($target_id){
			return $this->getAllRowsColumnBySelector('id', 'activity_id', $target_id);
		}
		
		public function renderCommentForNotificationBlock($comment_id){
			$column_array = array('activity_id','user_id','text','sent_time','hash');
			$comment = $this->getMultipleColumnsById($column_array, $comment_id);
			if($comment !== false){
				include_once 'User_Profile_Picture.php';
				$profile = new User_Profile_Picture();
				$post_owner_pic = $profile->getLatestProfileImageForUser($comment['user_id']);
				include_once 'User_Table.php';
				$user = new User_Table();
				$fullname = $user->getUserFullnameByUserIden($comment['user_id']);
				$post_time = convertDateTimeToAgo($comment['sent_time'], false);	
				$user_page_redirect =  USER_PROFILE_ROOT.$user->getUserAccessUrl($comment['user_id']);
				$unique_iden = $user->getUniqueIdenForUser($comment['user_id']);
				$text = $comment['text'];
				$user_id = $comment['user_id'];
				$hash = $comment['hash'];
			
				include_once 'Reply.php';
				$reply = new Reply();
				$isReply = $reply->getSelfIdCollectionByCommentId($comment_id);
			
				include_once 'Interest_Activity.php';
				$activity  = new Interest_Activity();
				
				$post_detail = $activity->getNotificationPostDetailByActivityId($comment['activity_id']);
				$post_owner_id = $this->getPostUserIdByActivityId($comment['activity_id']);
				ob_start();
				include(SCRIPT_INCLUDE_BASE.$this->popover_notification_template_path);
				$comment_block = ob_get_clean();
				return $comment_block;
			}
			return false;
		
		}
		
		
		
	}
	
	
?>