<?php
	include_once MODEL_PATH.'User_In_Interest.php';
	include_once MODEL_PATH.'Groups.php';
	include_once MODEL_PATH.'Message_Queue.php';
	
	class Prepare_Contact_Search{
		public function searchContactInSearchBarByKeyWord($key_word){
			$queue_array = array();
			$in = new User_In_Interest();
			$group = new Groups();
			$result = $in->getContactSearchByKeyWord($key_word);
			if($result !== null && $result !== false){
				foreach($result as $queue){
					if(!in_array($queue['queue'],$queue_array)){
						array_push($queue_array, $queue['queue']);
					}
				}
			}
			
			$result = $group->searchContactGroupByKeyWord($key_word);
			if($result !== null && $result !== false){
				foreach($result as $queue){
					if(!in_array($queue['queue'],$queue_array)){
						array_push($queue_array, $queue['queue']);
					}
				}
			}
			
			
			
			
			
			$message_queue = implode(',',$queue_array);
			$m_q = new Message_Queue();
			$content = $m_q->getMessageContactByMessageQueue($message_queue);
			return $content;
		}
	}
		
?>