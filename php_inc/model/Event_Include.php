<?php
	include_once 'Invitation.php';
	
	
	class Event_Include extends Invitation{
		private $table_name = "event_include";
		protected $request_block_template_path = TEMPLATE_PATH_CHILD.'popover_notification_event_include_request_block.phtml';
		protected $accept_request_block_template_path = TEMPLATE_PATH_CHILD.'popover_notification_event_include_accept_block.phtml';
		protected $invited_list_path = TEMPLATE_PATH_CHILD.'event_included_friend_list.phtml';
		protected $accept_noti_send_from_code = "eja-";
		
		public function __construct(){
			parent::__construct($this->table_name);
		}	
		
		
		
	}
	
?>