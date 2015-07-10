<?php
	/*
	This class provides codes for notification sendable table
	*/
	class Code_For_Notification_Sendable_Table{
		//the key is the table name, the value is the code
		const CODE  = array("comment"=>"c-", "reply"=>"r-", "reply_notify_post_user"=>"rn-","interest_request"=>"ir-");
		public static function getCodeForTable($table_name){
			return self::CODE[$table_name];
		}
		
		public static function getTableNameByCode($code){
			$code .='-';
			return array_search($code, self::CODE); 
		}
	}	

?>