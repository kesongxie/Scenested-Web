<?php
	include_once 'Code_For_Notification_Sendable_Table.php';
	class Notification_Renderer{
		public function getNotificationBlockByQueue($queue){
			$result = explode('-',$queue);
			$code = $result[0];
			$row_id = $result[1];
			$name = Code_For_Notification_Sendable_Table::getTableNameByCode($code);
			switch($name){
				case 'comment': return $this->commentRenderer($row_id);break;
				case 'reply':return $this->replyRenderer($row_id);break;
				case 'reply_notify_post_user':return $this->replyNotifyPostUserRenderer($row_id);break;
				case 'interest_request':return $this->interestRequestRenderer($row_id);break;
				case 'interest_request_accept':return $this->interestRequestAcceptRenderer($row_id);break;
				default:break;			
			}
		}
		
		public function commentRenderer($row_id){
			include_once 'Comment.php';
			$comment = new Comment();
			return $comment->renderCommentForNotificationBlock($row_id);
		}
		
		public function replyRenderer($row_id){
			include_once 'Reply.php';
			$reply = new Reply();
			return $reply->renderReplyForNotificationBlock($row_id);
		}
		
		public function replyNotifyPostUserRenderer($row_id){
			include_once 'Reply_Notify_Post_User.php';
			$notify = new Reply_Notify_Post_User();
			return $notify->renderNotifyPostUserReplyForNotificationBlock($row_id);
		}
		
		public function interestRequestRenderer($row_id){
			include_once 'Interest_Request.php';
			$request = new Interest_Request();
			return $request->renderInterestRequestForNotificationBlock($row_id);
		}
		
		public function interestRequestAcceptRenderer($row_id){
			include_once 'Interest_Request.php';
			$request = new Interest_Request();
			return $request->renderInterestRequestAcceptForNotificationBlock($row_id);
		}
		
		
		
	
	}



?>