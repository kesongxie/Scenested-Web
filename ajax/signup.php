<?php
	if(mail(
  	'kesongxie646@gmail.com', // your email address
  'Test', // email subject
  'This is an email', // email body
  'From: Lsere <kesongxie1993@gmail.com>'."\r\n"
	)){
		echo 'true';
	}else{
		echo 'false';
	}
	exit();
	
?>

<?php
	include_once '../php_inc/core.inc.php';
	include_once PHP_INC_MODEL.'User_Table.php';
	
	if(isset($_POST['data']) && !empty($_POST['data'])){
		parse_str($_POST['data'], $data);
		
		$email = trim($data['signup-iden']);
		if(empty($email)){
			echo '1'; //empty email address
			exit();
		}
		
		if(!filter_var($email, FILTER_VALIDATE_EMAIL)){
			echo '2'; //invalid email address
			exit();
		}
		$user_table = new User_Table();
		if($user_table->checkUserRegistered($email)>=1){
			echo '3'; //the email has been used
			exit();
		}
		
		if($data['signup-iden'] != $data['signup-re-iden']){
			echo '4'; //emails don't match
			exit();
		}
		
		$firstname = trim($data['signup-firstname']);
		if(empty($firstname)){
			echo '5'; //firstname empty
			exit();
		}
		
		
		$lastname = trim($data['signup-lastname']);
		if(empty($lastname)){
			echo '6'; //lastname empty
			exit();
		}
		
		$password = trim($data['signup-password']);
		if(empty($password)){
			echo '7'; //password empty
			exit();
		}
		if(strlen($password) < 6){
			echo '8'; //password too short
			exit();
		}
		
		if($data['signup-password'] != $data['signup-re-password']){
			echo '9'; //passwords don't match
			exit();
		}
		
		$gender = trim($data['signup-gender']);
		if(empty($gender) || ($gender != '1' &&  $gender != '2') ){
			echo '10'; //gender is empty
			exit();
		}
		
		
		//valid parameters
		$ip = $_SERVER['REMOTE_ADDR'];
		$signupDatetime = date('Y-m-d H:i:s');
		
		
		// multiple recipients
		$to  = $data['signup-iden']; // note the comma
		
		// subject
		$subject = 'Lsere account activation';

		
		$message = <<< EOF
		  <!DOCTYPE>
				<html>
			<body>
			<div style="padding:20px;">
				<div style="background: #780000;
							background: -webkit-linear-gradient(#a13030, #780000);
							background: -o-linear-gradient(#a13030,#780000);
							background: -moz-linear-gradient(#a13030,#780000);
							background: linear-gradient(#a13030,#780000);box-shadow: 1px 1px 10px gray;
						  border-radius: 5px;
						  text-align: center;
						  padding: 6px 10px;
						  color: #fff;
						  font-size: 18px;
						  font-weight: bold;display:inline-block;">
						L'sere
				</div>
				<div style="margin-top:30px;">
					<table>
						<tbody >
						<tr>
							<td style="font: 300 14px/18px 'Lucida Grande',Lucida Sans,Lucida Sans Unicode,sans-serif,Arial,Helvetica,Verdana,sans-serif;color: #333;">Dear Kesong Xie,</td>
						</tr>
				
						<tr>
							<td style="font: 300 14px/18px 'Lucida Grande',Lucida Sans,Lucida Sans Unicode,sans-serif,Arial,Helvetica,Verdana,sans-serif; color: #333;">Thank you for joining Lsere.</td>
						</tr>
				
						<tr>
							<td style="font: 300 14px/18px 'Lucida Grande',Lucida Sans,Lucida Sans Unicode,sans-serif,Arial,Helvetica,Verdana,sans-serif; color: #333;">You recently signed up on Lsere using <strong>kesong_xie@yahoo.com</strong>. To verify this email address, please click the link below and then you can sign in using this email adress and your password</td>
						</tr>
				
						<tr>
							<a><td style="font: 300 14px/18px 'Lucida Grande',Lucida Sans,Lucida Sans Unicode,sans-serif,Arial,Helvetica,Verdana,sans-serif; color: #333;color:rgb(60, 169, 226)">Verify now > </td></a>
						</tr>
				
						<tr>
							<td style="font: 300 14px/18px 'Lucida Grande',Lucida Sans,Lucida Sans Unicode,sans-serif,Arial,Helvetica,Verdana,sans-serif;">Lsere Team </td>
						</tr>
				
						</tbody>
					</table>
				</div>
			</div>
		
			</body>
		</html>
EOF;

// To send HTML mail, the Content-type header must be set
$headers  = 'MIME-Version: 1.0' . "\r\n";
$headers .= 'Content-type: text/html; charset=iso-8859-1' . "\r\n";

// Additional headers
$headers .= 'From: Lsere <kesongxie646@gmail.com>'."\r\n";
		
		if(mail($to, $subject, $message, $headers)){
			if($user_table->registerUser($email, $password, $firstname, $lastname, $gender, $ip, $signupDatetime)){
				echo '0';
			}
		}else{
			echo '11';
		}

	}
?>