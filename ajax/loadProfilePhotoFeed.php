<?php
	include_once '../php_inc/core.inc.php';
	include_once PHP_INC_MODEL.'User_Media_Base.php';

	if(isset($_POST['key']) && !empty(trim($_POST['key']))) {
		$l_m  = isset($_POST['l_m'])?$_POST['l_m']:false;
		$r_m  = isset($_POST['r_m'])?$_POST['r_m']:false;
		$l_e  = isset($_POST['l_e'])?$_POST['l_e']:false;
		$r_e  = isset($_POST['r_e'])?$_POST['r_e']:false;
		$l_p  = isset($_POST['l_p'])?$_POST['l_p']:false;
		$r_p  = isset($_POST['r_p'])?$_POST['r_p']:false;
		$l_c  = isset($_POST['l_c'])?$_POST['l_c']:false;
		$r_c  = isset($_POST['r_c'])?$_POST['r_c']:false;
		$base = new User_Media_Base();
		$feed = $base->loadProfilePhotoStream($l_m, $r_m, $l_e, $r_e,$l_p, $r_p,$l_c, $r_c,  trim($_POST['key']) );
		if($feed !== false){
			echo $feed;
		}else{
			echo '1';
		}
	}
	
		
?>