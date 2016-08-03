<?php
	include_once '../../php_inc/core.inc.php';
	class RemoteNotification{
		const ApnsServer = 'ssl://gateway.sandbox.push.apple.com:2195';
		const PushCertAndKeyPemFile = 'PushCertificateAndKey.pem';
		const PriavteKeyPassphrase = 'woaini1314';
		const connectionTimeout = 20;
		const InfoMessageKey = "message";
		const InfoRespondUserIdKey = "respondUserId";
		const InfoRespondUserNameKey = "respondUserName";
		const InfoRespondFullNameKey = "respondFullName";
		const InfoRespondAvatorPathKey = "avatorPath";
		const InfoRespondCoverPathKey = "coverPath";
		const InfoRespondCoverBio = "bio";
		


		
		//remote notification sending status
		const SendingStatusKey = "status";
		const SendingStatusCodeKey = "statusCode";
		const SendingStatusMessageKey = "statusMessage";
		const SendingStatusCodeNoNeedToSend = 100;
		const SendingStatusCodeSentSucceed = 200;
		const SendingStatusCodePostKeyVariableNotSet = 300;
		const SendingStatusCodeSentFailed = 500;
		const SendingStatusCodeNoNeedToSendMessage = "Do not need to send notification";
		const SendingStatusCodeSentSucceedMessage = "The notification is delivered successfully";
		const SendingStatusCodeSentFailedMessage = "The notification failed to deliver";
		const SendingStatusPostKeyVariableNotSet = "Post varible not set";

		/*
			$deviceToken is a string that contains the device token
			$notification is a dictionary that contains the necessary info for sending notification,
			keys are defined in class constant with "Key" subfix.
			return true if notification was sent successfully, false otherwise
			$respondInfo is an array contains the additional infomation include in the remote notificaiton respond
			
			= [
									'userId' => $notificationInfo[self::InfoRespondUserIdKey],
									'username' =>  $notificationInfo[self::InfoRespondUserNameKey],
									'fullname' =>  $notificationInfo[self::InfoRespondFullNameKey]
								]
			
			
		*/
		public static function sendNotificationToDevice($deviceToken, $notificationInfo, $respondInfo){
			//replace this with the name of the file that you placed by your php script file, containing your private key and certificate that you generated
			$stream = stream_context_create();
			stream_context_set_option($stream, 'ssl', 'local_cert', self::PushCertAndKeyPemFile);
			stream_context_set_option($stream, 'ssl', 'passphrase', self::PriavteKeyPassphrase);
			$connecttionTimeout = self::connectionTimeout;
			$connectionType = STREAM_CLIENT_CONNECT | STREAM_CLIENT_PERSISTENT;
			$connection = stream_socket_client(self::ApnsServer, $errorNumber, $errorString, $connecttionTimeout, $connectionType, $stream);
			if ($connection){
				$payload = json_encode([
								'aps'=>[
										'alert' => $notificationInfo[self::InfoMessageKey],
										'sound' => 'default',
										'badge' => 1
										],
								'respondInfo' => $respondInfo
							]);
							
				var_dump($payload);			
							
				$notification = chr(0) .
								pack('n', 32) . 
								pack('H*', $deviceToken) .  //pack and specify the hexadecimal bytes of tthe deviceToken, * for repeating to the end of the input data
								pack('n', strlen($payload)) . //unsigned short 
								$payload;
				$wroteSuccessfully = fwrite($connection, $notification, strlen($notification));
				fclose($connection);
				return $wroteSuccessfully !== false;
			}
			return false;
		}
		
		/*
			$sendToDeviceToken: the device that need to be send
			$react_to_user: Of whom this notification is about
		*/
		
		public function sendNotificationForSimilarThemes($flatenSimilarThemeString, $sendToDeviceToken, $react_to_user){
			$react_to_user_info = $react_to_user->getMultipleUserInfo([User::IdKey, User::UserNameKey, User::FullNameKey, User::BioKey, User::AvatorKey, User::CoverKey]);
			$notificationBody[RemoteNotification::InfoMessageKey] = Theme::getSimilarThemeNotificationBodyText($react_to_user_info[User::UserNameKey], $flatenSimilarThemeString);
			
			//additional info for reacting
			$respondInfo[RemoteNotification::InfoRespondUserIdKey] = $react_to_user_info[User::IdKey];
			$respondInfo[RemoteNotification::InfoRespondUserNameKey] = $react_to_user_info[User::UserNameKey];
			$respondInfo[RemoteNotification::InfoRespondAvatorPathKey] = $react_to_user_info[User::AvatorKey];
			$respondInfo[RemoteNotification::InfoRespondCoverPathKey] = $react_to_user_info[User::CoverKey];
			$respondInfo[RemoteNotification::InfoRespondCoverBio] = $react_to_user_info[User::BioKey];

			return self::sendNotificationToDevice($sendToDeviceToken, $notificationBody, $respondInfo);
		
		}
		
	}

?>