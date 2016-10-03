<?php
	include_once '../../php_inc/core.inc.php';
	
	//the requst user is the user to whom the final notification should forward
	if(isset($_REQUEST['request_user_id']) && isset($_REQUEST['come_across_user_id']) ){
		$feature = new Feature();
		$similarFeatures = $feature->getSimilarFeatureBetweenTwoUsers($_REQUEST['request_user_id'], $_REQUEST['come_across_user_id']);	
		if($similarFeatures !== false){
			$remoteNotification = new RemoteNotification();
			//flaten similar feature string
			$flatenSimilarFeatureString = '';
			foreach($similarFeatures as $entry){
				 $flatenSimilarFeatureString .= $entry[Feature::KeyForFeatureName].', ';
			}
			$flatenSimilarFeatureString = trim($flatenSimilarFeatureString,', ');
			
			//schedule a push notification
			$request_user = new User($_REQUEST['request_user_id']);
			$deviceTokenOfRequestedUser = $request_user->getDeviceToken();
			$come_across_user = new User($_REQUEST['come_across_user_id']);

			//send notification for request user, and pass the user object for the come_across_user for including additional info in the push notification
			$notifcationSentSuccessfullyToRequestedsUser = $remoteNotification->sendNotificationForSimilarFeatures($flatenSimilarFeatureString, $deviceTokenOfRequestedUser, $come_across_user);
				
			if($notifcationSentSuccessfullyToRequestedsUser){
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