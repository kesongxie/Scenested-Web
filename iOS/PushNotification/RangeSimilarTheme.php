<?php
	include_once '../../php_inc/core.inc.php';
	
 	$_POST['request_user_id'] = 33;
	$_POST['come_across_user_id'] = 34;
	//the requst user is the user to whom the final notification should forward
	if(isset($_POST['request_user_id']) && isset($_POST['come_across_user_id']) ){
		$theme = new Theme();
		$similarThemes = $theme->getSimilarThemeBetweenTwoUsers($_POST['request_user_id'], $_POST['come_across_user_id']);	
		if($similarThemes !== false){
			$remoteNotification = new RemoteNotification();
			//flaten similar theme string
			$flatenSimilarThemeString = '';
			foreach($similarThemes as $entry){
				 $flatenSimilarThemeString .= $entry[Theme::KeyForThemeNameColomn].', ';
			}
			$flatenSimilarThemeString = trim($flatenSimilarThemeString,', ');
			
			//schedule a push notification
			$request_user = new User($_POST['request_user_id']);
			$deviceTokenOfRequestUser = $request_user->getDeviceToken();

			$come_across_user = new User($_POST['come_across_user_id']);
			$deviceTokenOfComeAcrossUser = $come_across_user->getDeviceToken();
			
			
			//send notification for request user, and pass the user object for the come_across_user for including additional info in the push notification
			$notifcationSentSuccessfullyToRequestUser = $remoteNotification->sendNotificationForSimilarThemes($flatenSimilarThemeString, $deviceTokenOfRequestUser, $come_across_user);
			
			//send notification for come across user
			$notifcationSentSuccessfullyToComeAcrossUser = $remoteNotification->sendNotificationForSimilarThemes($flatenSimilarThemeString, $deviceTokenOfComeAcrossUser, $request_user);
				
			if($notifcationSentSuccessfullyToRequestUser && $notifcationSentSuccessfullyToComeAcrossUser){
				$sendingStatus = json_encode([
					RemoteNotification::SendingStatusKey => [
							RemoteNotification::SendingStatusCodeKey => RemoteNotification::SendingStatusCodeSentSucceed,
							RemoteNotification::SendingStatusMessageKey => RemoteNotification::SendingStatusCodeSentSucceedMessage
							]
					]);
			}else{
				 $sendingStatus = json_encode([
					RemoteNotification::SendingStatusKey => [
							RemoteNotification::SendingStatusCodeKey => RemoteNotification::SendingStatusCodeSentFailed,
							RemoteNotification::SendingStatusMessageKey => RemoteNotification::SendingStatusCodeSentFailedMessage
							]
					]);
			}
			echo $sendingStatus;	
		}else{
			$sendingStatus = json_encode([
			RemoteNotification::SendingStatusKey => [
					RemoteNotification::SendingStatusCodeKey => RemoteNotification::SendingStatusCodeNoNeedToSend,
					RemoteNotification::SendingStatusMessageKey => RemoteNotification::SendingStatusCodeNoNeedToSendMessage
					]
			]);
			echo $sendingStatus;
		}
	}else{
		$sendingStatus = json_encode([
		RemoteNotification::SendingStatusKey => [
				RemoteNotification::SendingStatusCodeKey => RemoteNotification::SendingStatusCodePostKeyVariableNotSet,
				RemoteNotification::SendingStatusMessageKey => RemoteNotification::SendingStatusPostKeyVariableNotSet
				]
		]);
		echo $sendingStatus;
 	}
?>