<?php
	$f = $_FILES['file-0']['tmp_name'];
	
	var_dump(getimagesize($f));
	
	
?>