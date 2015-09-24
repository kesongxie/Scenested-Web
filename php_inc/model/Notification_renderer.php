<?php
	include_once 'Code_For_Notification_Sendable_Table.php';
	class Notification_Renderer{
		 public function getNotificationBlockByQueue($queue){
		 	
			$result = explode('-',$queue);
			if(sizeof($result) == 2){
				$code = $result[0];
				$row_id = $result[1];
				$name = Code_For_Notification_Sendable_Table::getTableNameByCode($code);
				switch($name){
					case 'comment': return $this->commentRenderer($row_id);break;
					case 'reply':return $this->replyRenderer($row_id);break;
					case 'reply_notify_post_user':return $this->replyNotifyPostUserRenderer($row_id);break;
					case 'interest_request':return $this->interestRequestRenderer($row_id);break;
					case 'interest_request_accept':return $this->interestRequestAcceptRenderer($row_id);break;
					case 'favor_activity':return $this->favorActivityRenderer($row_id);break;
					case 'favor_comment':return $this->favorCommentRenderer($row_id);break;
					case 'favor_reply':return $this->favorReplyRenderer($row_id);break;
					case 'event_invitation':return $this->eventInvitationRenderer($row_id);break;
					case 'event_invitation_accept':return $this->eventInvitationAcceptRenderer($row_id);break;
					default:break;			
				}
			}
		}
		
		public function commentRenderer($row_id){
			include_once MODEL_PATH.'Comment.php';
			$comment = new Comment();
			return $comment->renderCommentForNotificationBlock($row_id);
		}
		
		public function replyRenderer($row_id){
			include_once MODEL_PATH.'Reply.php';
			$reply = new Reply();
			return $reply->renderReplyForNotificationBlock($row_id);
		}
		
		public function replyNotifyPostUserRenderer($row_id){
			include_once MODEL_PATH.'Reply_Notify_Post_User.php';
			$notify = new Reply_Notify_Post_User();
			return $notify->renderNotifyPostUserReplyForNotificationBlock($row_id);
		}
		
		public function interestRequestRenderer($row_id){
			include_once MODEL_PATH.'Interest_Request.php';
			$request = new Interest_Request();
			return $request->renderInterestRequestForNotificationBlock($row_id);
		}
		
		public function interestRequestAcceptRenderer($row_id){
			include_once MODEL_PATH.'Interest_Request.php';
			$request = new Interest_Request();
			return $request->renderInterestRequestAcceptForNotificationBlock($row_id);
		}
		
		public function favorActivityRenderer($row_id){
			include_once 'Favor_Activity.php';
			$favor = new Favor_Activity();
			return $favor->renderFavorActivityForNotificationBlock($row_id);
		}
		
		public function favorCommentRenderer($row_id){
			include_once MODEL_PATH.'Favor_Comment.php';
			$favor = new Favor_Comment();
			return $favor->renderFavorCommentForNotificationBlock($row_id);
		}
		
		public function favorReplyRenderer($row_id){
			include_once MODEL_PATH.'Favor_Reply.php';
			$favor = new Favor_Reply();
			return $favor->renderFavorReplyForNotificationBlock($row_id);
		}
		
		public function eventInvitationRenderer($row_id){
			include_once MODEL_PATH.'Event_Invitation.php';
			$invitation = new Event_Invitation();
			return $invitation->renderEventInvitationRequestForNotificationBlock($row_id);
		}
		
		public function eventInvitationAcceptRenderer($row_id){
			include_once MODEL_PATH.'Event_Invitation.php';
			$invitation = new Event_Invitation();
			return $invitation->renderEventInvitationRequestAcceptForNotificationBlock($row_id);
		}
	
	}



?>