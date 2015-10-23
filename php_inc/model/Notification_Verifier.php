<?php
	include_once 'Database_Connection.php';
	include_once 'Code_For_Notification_Sendable_Table.php';
	class Notification_Verifier{
		public static function isNotificationDisplayable($code, $row_id){
			$table_name = Code_For_Notification_Sendable_Table::getTableNameByCode($code);
			if($table_name == 'interest_request_accept'){
				$table_name = 'interest_request';
			}else if($table_name == 'event_invitation_accept'){
				$table_name = 'event_invitation';
			}else if($table_name == 'event_include_accept'){
				$table_name = 'event_include';
			}
			
			$database_connection = new Database_Connection();
			$stmt = $database_connection->getConnection()->prepare("SELECT `id` FROM `$table_name` WHERE `id` = ? LIMIT 1");
			if($stmt){
				$stmt->bind_param('i', $row_id);
				if($stmt->execute()){
					 $result = $stmt->get_result();
					 if($result->num_rows == 1){
					 	return true;
					 }
				}
			}
			return false;
		}
	}



?>