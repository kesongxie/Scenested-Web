<?php
	include_once MODEL_PATH.'Interest_Activity.php';
	$interest_activity = new Interest_Activity();
	echo $interest_activity->loadPassedEventCollectionForUser($request_user_page_id);
	
	
?>