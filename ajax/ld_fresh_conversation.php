<?php
	include_once '../php_inc/core.inc.php';
	include_once MODEL_PATH.'Message_Queue.php';
	if(isset($_POST['key']) && !empty(trim($_POST['key'])) ){
		$m_q = new Message_Queue();
		$conversation = $m_q->loadFreshConversationWithGivenUser($_SESSION['id'],trim($_POST['key']));	
		if($conversation !== false){
			echo $conversation;
		}else{
			echo '1';
		}
	}
	

?>