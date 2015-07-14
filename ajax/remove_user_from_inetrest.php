<?php
	include_once '../php_inc/core.inc.php';
	include_once PHP_INC_MODEL.'User_In_interest.php';
	
	if(isset($_POST['key']) && !empty(trim($_POST['key'])) && isset($_POST['hash']) && !empty(trim($_POST['hash']))  ){
		$in = new User_In_interest();
		$remove = $in->removeUserFromInterest($_SESSION['id'], trim($_POST['key']), trim($_POST['hash']) );
		if($remove === false){
			echo '1';
		}
	}
	
?>