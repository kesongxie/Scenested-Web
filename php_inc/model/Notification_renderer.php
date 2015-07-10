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
		
		
		
		
	
	}



?>