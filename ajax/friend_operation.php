<?php
	include_once '../php_inc/core.inc.php';
	include_once PHP_INC_MODEL.'Interest.php';
	include_once PHP_INC_MODEL.'User_Table.php';
	include_once PHP_INC_MODEL.'User_Media_Prefix.php';
	include_once PHP_INC_MODEL.'Interest_Request.php';
	include_once PHP_INC_MODEL.'User_In_Interest.php';


	if(isset($_POST['key']) && !empty(trim($_POST['key'])) ){
		$media_prefix = new User_Media_Prefix();
		$user = new User_Table();
		$user_to = $user->getUserIdByKey(trim($_POST['key']));
		if($user_to !== false){
			$interest = new Interest();
			$interest_request = new Interest_Request();
			$rows = $interest->getUserInterestsLabel($_SESSION['id']);
			$firstname = $user->getUserFirstNameByUserIden($user_to);
			$user_media_prefix = $media_prefix->getUserMediaPrefix($_SESSION['id']).'/';
			$in = new User_In_Interest();
			$rows_in = $in->getUserInInterest($_SESSION['id'],$user_to);
			ob_start();
			include(AJAX_TEMPLATE_PATH.'friend_operation.phtml');
			$content = ob_get_clean();
			echo $content;
		}
	}
	

?>