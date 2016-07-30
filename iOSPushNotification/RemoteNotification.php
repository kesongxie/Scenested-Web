<?php
	include_once '../php_inc/core.inc.php';
	class RemoteNotification{
		const ApnsServer = 'ssl://gateway.sandbox.push.apple.com:2195';
		const PushCertAndKeyPemFile = 'PushCertificateAndKey.pem';
		const PriavteKeyParaphrase = 'woaini1314';
		const connectionTimeout = 20;
		const RemoteNotificationInfoMessageKey = "message";

		/*
			$deviceToken is a string that contains the device token
			$notification is a dictionary that contains the necessary info for sending notification,
			keys are defined in class constant with "Key" subfix.
		*/
		public static function sendNotificationToDevice($deviceToken, $notificationInfo){
			//replace this with the name of the file that you placed by your php script file, containing your private key and certificate that you generated
			$stream = stream_context_create();
			stream_context_set_option($stream, 'ssl', 'local_cert', self::PushCertAndKeyPemFile);
			stream_context_set_option($stream, 'ssl', 'passphrase', self::PriavteKeyParaphrase);

			$connecttionTimeout = self::connectionTimeout;
			$connectionType = STREAM_CLIENT_CONNECT | STREAM_CLIENT_PERSISTENT;
	
			$connection = stream_socket_client(self::ApnsServer, $errorNumber, $errorString, $connecttionTimeout, $connectionType, $stream);
	
			if (!$connection){
				echo 'failed to connect to APNS Server<br/>';
			}else{
				echo 'successfully connected to APNS server, processing<br/>';
			}

			$payload = json_encode([
				'aps'=>[
						'alert' => $notificationInfo[self::RemoteNotificationInfoMessageKey],
						'sound' => 'default',
						'badge' => 1
						],
			]);
			$notification = chr(0) .
							pack('n', 32) . 
							pack('H*', $deviceToken) .  //pack and specify the hexadecimal bytes of tthe deviceToken, * for repeating to the end of the input data
							pack('n', strlen($payload)) . //unsigned short 
							$payload;
			$wroteSuccessfully = fwrite($connection, $notification, strlen($notification));
			if  (!$wroteSuccessfully){
				echo 'could not send the message<br/>';
			}else{
				echo 'succesffuly sent the message<br/>';
			}
			fclose($connection);
	
		}
	}

?>