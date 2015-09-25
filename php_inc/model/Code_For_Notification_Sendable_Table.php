<?php
	/*
	This class provides codes for notification sendable table
	and provide ways for encode and decode
	*/
	class Code_For_Notification_Sendable_Table{
		//the most of the keys are the table name, the value is the code, and some of the key might be a child of a table and might not exists
		const CODE  = array(
			"comment"=>"c-", "reply"=>"r-", "reply_notify_post_user"=>"rn-","interest_request"=>"ir-", 
			"interest_request_accept"=>"ira-", "message_queue"=>"mq-", "favor_activity"=>"fa-", "favor_comment"=>"fc-",
			"favor_reply"=>"fr-", "event_invitation"=>"ei-", "event_invitation_accept"=>"eia-",
			"event_include"=>"eji-","event_include_accept"=>"eja-");
		public static function getCodeForTable($table_name){
			return self::CODE[$table_name];
		}
		
		public static function getTableNameByCode($code){
			$code .='-';
			return array_search($code, self::CODE); 
		}
	}	

?>