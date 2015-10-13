<?php
	include_once '../php_inc/core.inc.php';
	include_once PHP_INC_MODEL.'Prepare_Search.php';
	
	$l_m  = isset($_POST['l_m'])?$_POST['l_m']:false;
	$r_m  = isset($_POST['r_m'])?$_POST['r_m']:false;
	$l_e  = isset($_POST['l_e'])?$_POST['l_e']:false;
	$r_e  = isset($_POST['r_e'])?$_POST['r_e']:false;
	
	$search_obj = unserialize($_SESSION['search_obj']);
	$fetched_content = $search_obj->loadMoreContentPhoto($l_m, $r_m, $l_e, $r_e);
	if($fetched_content !== false){
		echo $fetched_content;
	}else{
		echo '1';
	}

?>
