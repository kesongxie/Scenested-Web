<?php

	//when it's in production enviroment, replace this with ssl://gateway.push.apple.com:2195
	$apnsServer = 'ssl://gateway.sandbox.push.apple.com:2195';
	
	//password for private key when export the .gem file on the OS X
	$priavteKeyPassword = 'woaini1314';
	
	$message = 'Kesong is sending message through push notification :)';
	
	//device token
	//$deviceToken = 'a0986413bf681b74b8c45286f5e23ed7fed8e1d7e36dacc893b4778736600016'; //my own iphone
	$deviceToken = '77c18f569281daf2d838bccd5c6062b03511b0b19ed207a88c83a10a248bd8c1'; //my ipad
	//$deviceToken = 'ae2da65fa980dd906e2865027b83e8e6dee2adee5be8c31a4431b09ee5bb3105'; //silvia's iphone
	
	
	
	//replace this with the name of the file that you placed by your php script file, containing your private key and certificate that you generated
	$pushCertAndKeyPemFile = 'PushCertificateAndKey.pem';
	
	$stream = stream_context_create();
	
	stream_context_set_option($stream, 'ssl', 'local_cert', $pushCertAndKeyPemFile);
	
	stream_context_set_option($stream, 'ssl', 'passphrase', $priavteKeyPassword);

	
	$connecttionTimeout = 20;
	$connectionType = STREAM_CLIENT_CONNECT | STREAM_CLIENT_PERSISTENT;
	
	$connection = stream_socket_client($apnsServer, $errorNumber, $errorString, $connecttionTimeout, $connectionType, $stream);
	
	if (!$connection){
		echo 'failed to connect to APNS Server<br/>';
	}else{
		echo 'successfully connected to APNS server, processing<br/>';
	}
	
// 	$messageBody['aps'] = array('alert' => $message,
// 								'sound' => 'default',
// 								'badge' => 4
// 								);
// 		
	$payload = json_encode([
		'aps'=>[
				'alert' => $message,
				'sound' => 'default',
				'badge' => 4
				],
		'info'=>[
				'userId' => '10837'
				]
	
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
	
?>