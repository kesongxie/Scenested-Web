<?php
	include_once '../php_inc/core.inc.php';
	include_once PHP_INC_MODEL.'Prepare_Search.php';
	
	
	if(isset($_POST['q'])){
		$prepare_search = new Prepare_Search($_POST['q'],null);
		$search_bar_result =  $prepare_search->getSearchBarResult();
		if($search_bar_result !== null){
			echo $search_bar_result;
		}else{
			echo '1';
		}
	}
	
	
?>