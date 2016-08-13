<?php
	include_once '../../php_inc/core.inc.php';
	
 	$_POST['request_user_id'] = 33;
	$_POST['come_across_user_id'] = 34;
	//the requst user is the user to whom the final notification should forward
	if(isset($_POST['request_user_id']) && isset($_POST['come_across_user_id']) ){
		$feature = new Feature();
		$similarFeatures = $feature->getSimilarFeatureBetweenTwoUsers($_POST['request_user_id'], $_POST['come_across_user_id']);	
		if($similarFeatures !== false){
			$remoteNotification = new RemoteNotification();
			//flaten similar feature string
			$flatenSimilarFeatureString = '';
			foreach($similarFeatures as $entry){
				 $flatenSimilarFeatureString .= $entry[Feature::KeyForFeatureNameColomn].', ';
			}
			$flatenSimilarFeatureString = trim($flatenSimilarFeatureString,', ');
			
			//schedule a push notification
			$request_user = new User($_POST['request_user_id']);
			$deviceTokenOfRequestUser = $request_user->getDeviceToken();

			$come_across_user = new User($_POST['come_across_user_id']);
			$deviceTokenOfComeAcrossUser = $come_across_user->getDeviceToken();
			
			
			//send notification for request user, and pass the user object for the come_across_user for including additional info in the push notification
			$notifcationSentSuccessfullyToRequestUser = $remoteNotification->sendNotificationForSimilarFeatures($flatenSimilarFeatureString, $deviceTokenOfRequestUser, $come_across_user);
			
			//send notification for come across user
			$notifcationSentSuccessfullyToComeAcrossUser = $remoteNotification->sendNotificationForSimilarFeatures($flatenSimilarFeatureString, $deviceTokenOfComeAcrossUser, $request_user);
				
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