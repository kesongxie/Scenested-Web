<?php
	include_once '../php_inc/core.inc.php';
	include_once PHP_INC_MODEL.'Prepare_Contact_Search.php';
	
	
	if(isset($_POST['q']) && !empty(trim($_POST['q']))){
		$search = new Prepare_Contact_Search();
		$search_bar_result =  $search->searchContactInSearchBarByKeyWord(trim($_POST['q']));
		if($search_bar_result == ''){
			echo '<div style="margin: 10px 0px;font-size: 15px;">No contact found</div>';
		}else{
			echo $search_bar_result;
		}
	}
	
	
?>



