<?php
	include_once MODEL_PATH.'User_Media_Base.php';
	$media_base = new User_Media_Base();
	echo $media_base->getUserMediaBlockByUserId($request_user_page_id);
	
	
?>