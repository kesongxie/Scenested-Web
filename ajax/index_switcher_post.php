<?php
	include_once '../php_inc/core.inc.php';
	include_once PHP_INC_MODEL.'Interest.php';


	if(isset($_POST['key']) && !empty(trim($_POST['key'])) ){
		$interest  = new Interest();
		$rows = $interest->getUserInterestsLabel($_SESSION['id']);
		$active_key = $_POST['key'];
		ob_start();
		include(AJAX_TEMPLATE_PATH.'index_switcher_post.phtml');
		$content = ob_get_clean();
		echo $content;
	}
	

?>