<?php
	include_once '../php_inc/core.inc.php';
	include_once PHP_INC_MODEL.'Interest.php';
	
	if(isset($_POST['label_key']) && !empty($_POST['label_key'])){
		$interest = new Interest();
	 	$fetch_interest_block = $interest->getUserInterestBlockByInterestId($_POST['label_key']);
	 	if($fetch_interest_block !== false){
	 		echo $fetch_interest_block;
	 	}else{
	 		echo '1';
	 	}
	}
	
?>