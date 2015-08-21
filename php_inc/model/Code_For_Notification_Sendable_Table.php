<?php
	/*
	This class provides codes for notification sendable table
	*/
	class Code_For_Notification_Sendable_Table{
		//the most of the keys are the table name, the value is the code, and some of the key might be a child of a table and might not exists
		const CODE  = array("comment"=>"c-", "reply"=>"r-", "reply_notify_post_user"=>"rn-","interest_request"=>"ir-", "interest_request_accept"=>"ira-", "message_queue"=>"mq-", "favor_activity"=>"fa-");
		public static function getCodeForTable($table_name){
			return self::CODE[$table_name];
		}
		
		public static function getTableNameByCode($code){
			$code .='-';
			return array_search($code, self::CODE); 
		}
	}	

?>