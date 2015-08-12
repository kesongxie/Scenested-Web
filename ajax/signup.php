<?php
	include_once '../php_inc/core.inc.php';
	include_once PHP_INC_MODEL.'User_Table.php';
	include_once PHP_INC_MODEL.'Email_Account_Activation.php';
	
	if(isset($_POST['data']) && !empty($_POST['data'])){
		$user_table = new User_Table();
		$email_account_activation = new Email_Account_Activation();

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
		
		if($user_table->checkUserRegistered($email)){
			echo '3'; //the email has been used
			exit();
		}
		
		if(strtolower($data['signup-iden']) != strtolower($data['signup-re-iden'])){
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
		$code=md5(rand(100000,999999));
		
		
		$register_id = $user_table->registerUser($email, $password, $firstname, $lastname, $gender, $ip, $signupDatetime);
		
		if($register_id !== false){
			//send email
			$to  = $data['signup-iden']; 
			$subject = 'Lsere account activation';
			$rootDir = substr(ROOTDIR,0, strlen(ROOTDIR)-1);
			$message = <<< EOF
			<!DOCTYPE>
			<html>
				<body>
					<div style="padding:8px;">
						<div style="background: #780000;
									background: -webkit-linear-gradient(#a13030, #780000);
									background: -o-linear-gradient(#a13030,#780000);
									background: -moz-linear-gradient(#a13030,#780000);
									background: linear-gradient(#a13030,#780000);box-shadow: 1px 1px 10px gray;
								  border-radius: 5px;
								  text-align: center;
								  padding: 6px 10px;
								  color: #fff;
								  font-size: 16px;
								  font-weight: bold;display:inline-block;">
								L'sere
						</div>
						<div style="margin-top:30px;">
							<div>
								<div >
								<div style="margin-bottom:20px;">
									<div style="font: 300 14px/18px 'Lucida Grande',Lucida Sans,Lucida Sans Unicode,sans-serif,Arial,Helvetica,Verdana,sans-serif;color: #333;">Dear $firstname $lastname,</div>
								</div>

								<div style="margin-bottom:20px;">
									<div style="font: 300 14px/18px 'Lucida Grande',Lucida Sans,Lucida Sans Unicode,sans-serif,Arial,Helvetica,Verdana,sans-serif; color: #333;">Thank you for joining Lsere.</div>
								</div>

								<div style="margin-bottom:10px;">
									<div style="font: 300 14px/18px 'Lucida Grande',Lucida Sans,Lucida Sans Unicode,sans-serif,Arial,Helvetica,Verdana,sans-serif; color: #333;line-height:2.0;">You recently signed up on Lsere using the email account <a style="color:#000;font-weight:500;" href="$email">$email</a>. To verify this email address, please click the link below and then you can sign in using this email adress and your password.</div>
								</div>

								<div style="margin-bottom:30px;">
									<a href="$rootDir/activate.php?id=$register_id&code=$code" style="font: 300 14px/18px 'Lucida Grande',Lucida Sans,Lucida Sans Unicode,sans-serif,Arial,Helvetica,Verdana,sans-serif; color: #333;color:rgb(60, 169, 226);text-decoration:none;">Verify now ></a>
								</div>

								<div>
									<div style="font: 300 14px/18px 'Lucida Grande',Lucida Sans,Lucida Sans Unicode,sans-serif,Arial,Helvetica,Verdana,sans-serif;line-height:2.0;">Best,<br>Lsere Team </div>
								</div>

								</div>
							</div>
						</div>
					</div>
				</body>
			</html>
EOF;
				// To send HTML mail, the Content-type header must be set
				$headers  = 'MIME-Version: 1.0' . "\r\n";
				$headers .= 'Content-type: text/html; charset=iso-8859-1' . "\r\n";

				// Additional headers
				$headers .= 'From: Lsere <kesongxie1993@gmail.com>'."\r\n";
				
				if($email_account_activation->insertEntry($register_id, $code) && mail($to, $subject, $message, $headers)){
					echo '0';
				}else{
					echo '11'; //email failed
				}
			
		}
		
		

	}
?>