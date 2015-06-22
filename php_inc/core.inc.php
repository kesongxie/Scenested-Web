<?php
	//core.inc.php includes global functions 
	session_start();
	include_once 'global_constant.php'; 
	function deleteCookie($cookie_name){
		setcookie ($cookie_name, "", time() - 3600, '/');
	}	
	
	function clearLoginCredential(){
		unset($_SESSION['id']);
		deleteCookie("identifier");
		deleteCookie("token");
	}
	
	function getRandomString(){
		return bin2hex(openssl_random_pseudo_bytes(12));
	}				

	function getMediaFileExtension($file){
		return strtolower(pathinfo(basename($file["name"]),PATHINFO_EXTENSION));
	}
	
	function getDefaultInterestLabelImageByNum($num){
		return DEFAULT_INTEREST_LABEL_IMAGE_PREFIX.$num.'.png';
	}
		
	
?>