<?php
	include_once '../php_inc/core.inc.php';
	include_once 'RemoteNotification.php';
	
	$_POST['request_user_id'] = 33;
	$_POST['come_across_user_id'] = 34;
	
	//the requst user is the user to whom the final notification should forward
	if(isset($_POST['request_user_id']) && isset($_POST['come_across_user_id']) ){
		$theme = new Theme();
		$similarThemes = $theme->getSimilarThemeBetweenTwoUsers($_POST['request_user_id'], $_POST['come_across_user_id']);	
		if($similarThemes !== false){
			//flaten similar theme string
			//array(2) { [0]=> array(1) { [0]=> string(6) "guitar" } [1]=> array(1) { [0]=> string(11) "programming" } }
			$flatenSimilarThemeString = '';
			foreach($similarThemes as $entry){
				 $flatenSimilarThemeString .= $entry[Theme::KeyForThemeNameColomn].', ';
			}
			
			$flatenSimilarThemeString = trim($flatenSimilarThemeString,', ');
			//schedule a push notification
			//get the device token of the request_user_id
			$request_user = new User($_POST['request_user_id']);
			$deviceTokenOfRequestUser = $request_user->getDeviceToken();
			
			$come_across_user = new User($_POST['come_across_user_id']);
			$deviceTokenOfComeAcrossUser = $come_across_user->getDeviceToken();
		
			$remoteNotification = new RemoteNotification();
			$notificationForReuqestUser['message'] = $come_across_user->getUserName()." shares similar themes with you - ".$flatenSimilarThemeString;
			$notificationForComeAcrossUser['message'] = $request_user->getUserName()." shares similar themes with you - ".$flatenSimilarThemeString;
	
				
			$remoteNotification->sendNotificationToDevice($deviceTokenOfRequestUser, $notificationForReuqestUser);
			$remoteNotification->sendNotificationToDevice($deviceTokenOfComeAcrossUser, $notificationForComeAcrossUser);
		}
	}
	


?>