<?php
	//	contains global constants such as folder structures
	
	
	//for page rendering 
	define("ROOTDIR","http://localhost:8888/lsere/");
	define("IMGDIR",ROOTDIR."media/");
	define("DEFAULT_IMAGE",IMGDIR.'default_image/');
	define("U_IMGDIR",ROOTDIR."media_u/");
	define("SEARCH_PEOPLE_PATH",ROOTDIR.'search.php?q=');

	
	//paths for php file system
	define("DOCUMENT_ROOT",'../');
	define("U_MEDAI_FOLDER_DIR",DOCUMENT_ROOT.'media_u/');
	define("MEDIA_THUMBNAIL_PREFIX","thumb_");
	define("MEDIA_U","media_u/");
	define("SCRIPT_INCLUDE_BASE",$_SERVER['DOCUMENT_ROOT'].'/lsere/');
	define("TEMPLATE_PATH",SCRIPT_INCLUDE_BASE.'phtml/');
	define("AJAX_TEMPLATE_PATH", SCRIPT_INCLUDE_BASE.'/ajax/phtml/');
	define("TEMPLATE_PATH_CHILD",TEMPLATE_PATH.'child/');
	define("PHP_INC_PAHT",SCRIPT_INCLUDE_BASE.'php_inc/' );
	define("MODEL_PATH",PHP_INC_PAHT.'model/');
	
	define("USER_PROFILE_ROOT", ROOTDIR.'user/'); //user profile page root
	
	define("PHP_INC_MODEL_ROOT_REF","php_inc/model/");
	define("PHP_INC_MODEL","../".PHP_INC_MODEL_ROOT_REF);
	define("LOADING_GIF",IMGDIR."loading.gif");
	
	define("LOGIN_PAGE",ROOTDIR."login.php");
	define("HOME_PAGE","index.php");
	define("ERROR_PAGE",ROOTDIR."404.php");
	define("COOKIE_EXPIRE_TIME",time()+ 90 * 86400);
	define("MAXIMUM_UPLOAD_IMAGE_SIZE",5000000);
	define("DEFAULT_PROFILE_IMAGE",DEFAULT_IMAGE."default_profile_100pl.png");
	define("DEFAULT_COVER_IMAGE",DEFAULT_IMAGE."beach-230.jpg");
	define("DEFAULT_INTEREST_LABEL_IMAGE_PREFIX",DEFAULT_IMAGE.'interest-label-');
	define("MAX_INTEREST_LABEL_COLOR_RANDOM_INDEX", 6);
	
	
	
	
?>