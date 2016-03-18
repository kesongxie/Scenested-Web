<?php
	include_once 'core.inc.php';
	class Photo_Layout_Scheme{
	
		/*
			@param $photoUrls, an array that contains the url for photos
		*/
		public function getPhotoLayoutBlock($photoCollection){
			$block = '';
			if($photoCollection !== false){
				$template_path = '';
				switch(sizeof($photoCollection)){
					case 1:
						//one-photo-layout-scheme 
						$template_path = TEMPLATE_PATH_CHILD.'one-photo-layout-scheme.phtml';
						break;
					case 2:
					//two-photo-layout-scheme 
						$template_path = TEMPLATE_PATH_CHILD.'two-photo-layout-scheme.phtml';
						break;
					break;
					case 3:
					//three-photo-layout-scheme 
						$template_path = TEMPLATE_PATH_CHILD.'three-photo-layout-scheme.phtml';
						break;
					break;
					case 4:
						//four-photo-layout-scheme 
						$template_path = TEMPLATE_PATH_CHILD.'four-photo-layout-scheme.phtml';	
					break;
					default:break;
				}
				ob_start();
				include($template_path);
				$block = ob_get_clean();
			}
			return $block;
		}
	
	
	
	}
	


?>