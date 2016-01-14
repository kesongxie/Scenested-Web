<?php
	include_once '../php_inc/core.inc.php';
	if(isset($_POST['bio_text'])){
		$user_bio = new User_Bio();
		$bio = $user_bio->updateBioForUser($_POST['bio_text'], $_SESSION['id']);
		echo ($bio !== false) ? $bio : '1';
	}
?>