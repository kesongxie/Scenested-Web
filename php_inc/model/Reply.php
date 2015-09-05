<?php
	include_once 'Comment_Base.php';
	include_once 'Reply_Notify_Post_User.php';
	class Reply extends Comment_Base{
		private $table_name = "reply";
		private $noti_post_user = null;
		private $reply_template_path = "phtml/child/reply_block.phtml";
		private $popover_notification_template_path = "phtml/child/popover_notification_reply_block.phtml";
		private $noti_user_popover_notification_template_path = "phtml/child/popover_notification_noti_user_reply_block.phtml";

		private $sub_reply_template_path = "phtml/child/sub_reply_block.phtml";
		public function __construct(){
			parent::__construct($this->table_name);
			$this->noti_post_user = new Reply_Notify_Post_User();
		}
		
		public function addReplyToComment($key, $user_sent, $text){
			$unique_hash = $this->generateUniqueHash();
			$stmt = $this->connection->prepare("INSERT INTO `$this->table_name` (`activity_id`,`comment_id`,`user_id`,`user_id_get`,`text`,`sent_time`,`hash`) VALUES(?, ?, ?, ?, ?, ?, ?)");
			include_once 'Comment.php';
			$comment = new Comment();
			$comment_id = $comment->getRowIdByHashkey($key);
			if($comment_id !== false){
				//$user_id_get = $comment->getColumnById('user_id',$comment_id);
				$column_array = array('user_id','activity_id');
				$result = $comment->getMultipleColumnsById($column_array, $comment_id);
				$user_id_get = $result['user_id'];
				$activity_id = $result['activity_id'];
				if($user_id_get == $user_sent){
					return false;
				}
				$time = date('Y-m-d H:i:s');
				$stmt->bind_param('iiiisss',$activity_id, $comment_id, $user_sent, $user_id_get, $text, $time,$unique_hash);
				if($stmt->execute()){
					$stmt->close();
					$reply_id = $this->connection->insert_id;
					
					//notify the comment user
					if($user_sent != $user_id_get){
						$this->noti_queue->addNotificationQueueForUser($user_id_get, $reply_id);
					}
					
					//also notify the post user
					$post_user_id =  $this->getPostUserIdByActivityId($activity_id);
					if($user_sent != $post_user_id && $user_id_get != $post_user_id){
						$this->noti_post_user->addReplyNotiForUser($post_user_id, $reply_id, $time, $unique_hash);
					}
					return $this->renderReplyBlockByReplyId($reply_id);
				}
				echo $this->connection->error;
			}
			return false;
		}
		
		
	
		
		
		public function addSubReplyToReply($key, $user_sent, $text){
			$unique_hash = $this->generateUniqueHash();
			$stmt = $this->connection->prepare("INSERT INTO `$this->table_name` (`activity_id`,`comment_id`,`user_id`,`user_id_get`,`text`,`sent_time`,`target_id`,`hash`) VALUES(?,?,?, ?, ?, ?, ?, ?)");
			
			$reply_id = $this->getRowIdByHashkey($key); //get the target id, comment on which 
			
			if($reply_id !== false){
				//$user_id_get = $reply->getColumnById('user_id',$reply_id);
				$column_array = array('user_id','activity_id','comment_id');
				$result = $this->getMultipleColumnsById($column_array, $reply_id);
				$user_id_get = $result['user_id'];
				$activity_id = $result['activity_id'];
				$comment_id = $result['comment_id'];
				
				if($user_id_get == $user_sent){
					return false;
				}
				$time = date('Y-m-d H:i:s');
				$stmt->bind_param('iiiissis',$activity_id, $comment_id, $user_sent, $user_id_get, $text, $time,$reply_id,$unique_hash);
				if($stmt->execute()){
					$stmt->close();
					$sub_reply_id = $this->connection->insert_id;
					if($user_sent != $user_id_get){
						$this->noti_queue->addNotificationQueueForUser($user_id_get, $sub_reply_id);
					}
					//also notify the post user
				
					$post_user_id =  $this->getPostUserIdByActivityId($activity_id);
					if($user_sent != $post_user_id && $user_id_get != $post_user_id){
						$this->noti_post_user->addReplyNotiForUser($post_user_id, $reply_id, $time, $unique_hash);
					}
					
					return $this->renderSubReplyBlockBySubReplyId($sub_reply_id);
				}
			}
			return false;
		}
		
		
		
		public function renderReply($reply_id){
			$target_id = $this->getColumnById('target_id',$reply_id);
			if($target_id !== false){
				if($target_id === null){
					return $this->renderReplyBlockByReplyId($reply_id);
				}
				return $this->renderSubReplyBlockBySubReplyId($reply_id);
			}
		
		}
		
		
		/*
			render the reply block
		*/
		public function renderReplyBlockByReplyId($reply_id){
			$column_array = array('activity_id','user_id','text','sent_time','hash');
			$reply = $this->getMultipleColumnsById($column_array, $reply_id);
			include_once 'User_Profile_Picture.php';
			$profile = new User_Profile_Picture();
			$post_owner_pic = $profile->getLatestProfileImageForUser($reply['user_id']);
			include_once 'User_Table.php';
			$user = new User_Table();
			$fullname = $user->getUserFullnameByUserIden($reply['user_id']);
			$post_time = convertDateTimeToAgo($reply['sent_time'], false);	
			$user_page_redirect =  USER_PROFILE_ROOT.$user->getUserAccessUrl($reply['user_id']);
			$unique_iden = $user->getUniqueIdenForUser($reply['user_id']);
			$text = $reply['text'];
			$user_id = $reply['user_id'];
			$hash = $reply['hash'];
			$post_owner_id =$this->getPostUserIdByActivityId($reply['activity_id']);
			
			
			$favor_number = $this->getReplyLikeNumber($reply_id);
			$favored = $this->isSessionUserFavorReply($reply_id);
			
			ob_start();
			include(SCRIPT_INCLUDE_BASE.$this->reply_template_path);
			$reply_block = ob_get_clean();
			return $reply_block;
		}
	
		
		/*
			render the sub-reply block
		*/
		
		public function renderSubReplyBlockBySubReplyId($sub_reply_id){
			$column_array = array('activity_id','user_id','user_id_get','text','sent_time','hash');
			$sub_reply = $this->getMultipleColumnsById($column_array, $sub_reply_id);
			include_once 'User_Profile_Picture.php';
			$profile = new User_Profile_Picture();
			$post_owner_pic = $profile->getLatestProfileImageForUser($sub_reply['user_id']);
			include_once 'User_Table.php';
			$user = new User_Table();
			
			$fullname = $user->getUserFullnameByUserIden($sub_reply['user_id']);
			$at_fullname = $user->getUserFullnameByUserIden($sub_reply['user_id_get']);
			$post_time = convertDateTimeToAgo($sub_reply['sent_time'], false);	
			$user_page_redirect =  USER_PROFILE_ROOT.$user->getUserAccessUrl($sub_reply['user_id']);
			$unique_iden = $user->getUniqueIdenForUser($sub_reply['user_id']);
			$text = $sub_reply['text'];
			$user_id = $sub_reply['user_id'];
			$hash = $sub_reply['hash'];
			$post_owner_id = $this->getPostUserIdByActivityId($sub_reply['activity_id']);
			ob_start();
			include(SCRIPT_INCLUDE_BASE.$this->sub_reply_template_path);
			$reply_block = ob_get_clean();
			return $reply_block;
		}
	
		
		
		public function deleteReply($user_id, $key){
			$row = $this->getMultipleColumnsBySelector(array('activity_id','id'), 'hash', $key);
			$reply_id = $row['id'];
			$post_owner_id = $this->getPostUserIdByActivityId($row['activity_id']);
			if($post_owner_id == $user_id){
				$this->deleteNotiQueueForKey($key);
				$this->noti_post_user->deleteNotiQueue($key);
				$this->deleteRowById($reply_id);
			}else{
				$this->deleteNotiQueueForKey($key);
				$this->noti_post_user->deleteNotiQueue($key);

				$this->deleteCommentForUserByKey($user_id, $key);
			}
		}
		
		public function deleteAllReplyByActivityId($activity_id){
			$this->deleteRowByNumericSelector('activity_id', $activity_id);
		}
		
		
		public function deleteAllReplysForCommentId($comment_id){
			//select id and user_id_get
			$reply_rows = $this->getAllRowsColumnBySelector('hash', 'comment_id', $comment_id, $asc = true);
			if($reply_rows !== false && sizeof($reply_rows) > 0 ){
				foreach($reply_rows as $row){
					$this->deleteNotiQueueForKey($row['hash']);
					$this->noti_post_user->deleteNotiQueue($row['hash']);
				}
			}
			$this->deleteRowBySelector('comment_id', $comment_id);
		}
		
		
		public function getSelfIdCollectionByCommentId($comment_id){
			return $this->getAllRowsColumnBySelector('id', 'comment_id', $comment_id);
		}
		
		
		public function checkWhetherUserHasRepliedToReply($user_id, $reply_id){
			$stmt = $this->connection->prepare("SELECT `id` FROM `$this->table_name` WHERE `user_id` = ? AND `target_id`=? ORDER BY `id` DESC LIMIT 1 ");
			$stmt->bind_param('ii',$user_id,$reply_id);
			if($stmt->execute()){
				 $result = $stmt->get_result();
				 if($result !== false && $result->num_rows == 1){
				 	$row = $result->fetch_assoc();
				 	$stmt->close();
					return $row['id']; 
				 }
			}
			echo $this->connection->error;
			return false;
		}
		
		
		
		public function renderReplyForNotificationBlock($reply_id){
			$column_array = array('activity_id','comment_id','user_id','text','sent_time','target_id','hash');
			$reply = $this->getMultipleColumnsById($column_array, $reply_id);
			if($reply !== false){
				include_once 'User_Profile_Picture.php';
				$profile = new User_Profile_Picture();
				$post_owner_pic = $profile->getLatestProfileImageForUser($reply['user_id']);
				include_once 'User_Table.php';
				$user = new User_Table();
				$fullname = $user->getUserFullnameByUserIden($reply['user_id']);
				$post_time = convertDateTimeToAgo($reply['sent_time'], false);	
				$user_page_redirect =  USER_PROFILE_ROOT.$user->getUserAccessUrl($reply['user_id']);
				$unique_iden = $user->getUniqueIdenForUser($reply['user_id']);
				$text = $reply['text'];
				$user_id = $reply['user_id'];
				$hash = $reply['hash'];
				$post_owner_id =$this->getPostUserIdByActivityId($reply['activity_id']);
				if($reply['target_id'] === null){
					include_once 'Comment.php';
					$comment = new Comment();
					$comment_text =  $comment->getColumnById('text',$reply['comment_id']);
				}else{
					$comment_text =  $this->getColumnById('text',$reply['target_id']);
				}
				$isReply = $this->checkWhetherUserHasRepliedToReply($_SESSION['id'], $reply_id);
				ob_start();
				include(SCRIPT_INCLUDE_BASE.$this->popover_notification_template_path);
				$reply_block = ob_get_clean();
				return $reply_block;
			}
			return false;
		}
		
		
		
		public function renderNotifyPostUserReplyForNotificationBlock($reply_id){
			$column_array = array('activity_id','comment_id','user_id','user_id_get','text','sent_time','target_id','hash');
			$reply = $this->getMultipleColumnsById($column_array, $reply_id);
			if($reply !== false){
				include_once 'User_Profile_Picture.php';
				$profile = new User_Profile_Picture();
				$post_owner_pic = $profile->getLatestProfileImageForUser($reply['user_id']);
				include_once 'User_Table.php';
				$user = new User_Table();
				$fullname = $user->getUserFullnameByUserIden($reply['user_id']);
				$post_time = convertDateTimeToAgo($reply['sent_time'], false);	
				$user_page_redirect =  USER_PROFILE_ROOT.$user->getUserAccessUrl($reply['user_id']);
				$unique_iden = $user->getUniqueIdenForUser($reply['user_id']);
				$text = $reply['text'];
				$user_id = $reply['user_id'];
				$hash = $reply['hash'];
			
				$at_fullname = $user->getUserFullnameByUserIden($reply['user_id_get']);

				$post_owner_id =$this->getPostUserIdByActivityId($reply['activity_id']);
			
				include_once 'Interest_Activity.php';
				$activity  = new Interest_Activity();
				$post_text = $activity->getPostTextByActivityId($reply['activity_id']);
			
			
				$isReply = $this->checkWhetherUserHasRepliedToReply($_SESSION['id'], $reply_id);
				ob_start();
				include(SCRIPT_INCLUDE_BASE.$this->noti_user_popover_notification_template_path);
				$reply_block = ob_get_clean();
				return $reply_block;
			}
			return false;
		}
		
		public function getReplyTextByReplyId($reply_id){
			return $this->getColumnById('text', $reply_id);
		}
		
		public function favorReply($key){
			$result = $this->getMultipleColumnsBySelector(array('id', 'user_id'), 'hash', $key);
			if($result !== false){
				include_once MODEL_PATH.'Favor_Reply.php';
				$favor = new Favor_Reply();
				$user_id_get = $result['user_id'];
				$favor->addFavor($result['id'], $_SESSION['id'], $user_id_get);
			}
			return false;
		}
		
		public function getReplyLikeNumber($reply_id){
			include_once MODEL_PATH.'Favor_Reply.php';
			$favor = new Favor_Reply();
			return $favor->getTotalFavorNumForTarget($reply_id);
		}
		
		
		public function getReplyLikeNumberByKey($key){
			$reply_id = $this->getRowIdByHashkey($key);
			if($reply_id !== false){
				include_once MODEL_PATH.'Favor_Reply.php';
				$favor = new Favor_Reply();
				return $favor->getTotalFavorNumForTarget($reply_id);
			}
			return 0;
		}
		
		public function isSessionUserFavorReply($reply_id){
			include_once MODEL_PATH.'Favor_Reply.php';
			$favor = new Favor_Reply();
			return $favor->isSessionUserAlreadyFavor($reply_id);
		}
		
		
		public function undoFavorReply($key){
			$reply_id = $this->getRowIdByHashkey($key);
			if($reply_id !== false){
				include_once MODEL_PATH.'Favor_Reply.php';
				$favor = new Favor_Reply();
				$favor->undoFavorForSessionUser($reply_id);
			}
		}
		
		public function getReplyFavorPlainListByKey($key){
			$reply_id = $this->getRowIdByHashkey($key);
			if($reply_id !== false){
				include_once MODEL_PATH.'Favor_Reply.php';
				$favor = new Favor_Reply();
				return $favor->getFavorPlainListForTarget($reply_id);
			}
			return false;
		}
		
		
		
	}
	
	
?>