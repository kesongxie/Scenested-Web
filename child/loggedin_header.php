<?php
	$session_user_profile_image_url = $user->getLatestProfilePictureForuser($_SESSION['id']);
	$session_user_access_url = $user->getUserAccessUrl($_SESSION['id']);
	$fullname =  $user->getUserFullnameByUserIden($_SESSION['id']);
	include_once TEMPLATE_PATH_CHILD.'loggedin_header.phtml';
?>