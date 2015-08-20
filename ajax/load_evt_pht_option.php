<?php
	include_once '../php_inc/core.inc.php';
	include_once PHP_INC_MODEL.'Event_Photo.php';
	
	if(isset($_POST['key']) && !empty(trim($_POST['key'])) ){
		$evt_pht = new Event_Photo();
		$deletable = $evt_pht->isPhotoDeletable(trim($_POST['key']));
		var_dump($deletable);
		ob_start();
		include(AJAX_TEMPLATE_PATH.'evt_photo_option.phtml');
		$content = ob_get_clean();
		echo $content;
	
	}
	

?>