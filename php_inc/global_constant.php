<?php
	//	contains global constants such as folder structures
	//for page rendering 
	include_once 'server_cred.php';
	define("IMGDIR",ROOTDIR."media/");
	define("E_IMGDIR",IMGDIR.'e/');
	define("DEFAULT_IMAGE",IMGDIR.'default_image/');
	define("U_IMGDIR",ROOTDIR."media_u/");
	define("SEARCH_PEOPLE_PATH",ROOTDIR.'search.php?q=');
	define("SEARCH_SIMILAR_PEOPLE_PATH",ROOTDIR.'search.php?r=mine');
	define("SEARCH_MINE_EVENT_PATH", ROOTDIR.'search.php?r=mine&t=event');
	define("SEARCH_MINE_PHOTO_PATH", ROOTDIR.'search.php?r=mine&t=photo');
	define("JS_PATH", ROOTDIR.'js/');
	define("CHILD_JS_PATH", JS_PATH.'child_js/');
	define("SPINNER_ICON", IMGDIR."spinner.gif");
	
	//paths for php file system
	define("DOCUMENT_ROOT",'../');
	define("U_MEDAI_FOLDER_DIR",DOCUMENT_ROOT.'media_u/');
	define("MEDIA_THUMBNAIL_PREFIX","thumb_");

	define("SCRIPT_INCLUDE_BASE",$_SERVER['DOCUMENT_ROOT'].'/');
	define("MEDIA_U",SCRIPT_INCLUDE_BASE."media_u/");
	define("TEMPLATE_PATH",SCRIPT_INCLUDE_BASE.'phtml/');
	define("AJAX_TEMPLATE_PATH", SCRIPT_INCLUDE_BASE.'/ajax/phtml/');
	define("TEMPLATE_PATH_CHILD",TEMPLATE_PATH.'child/');
	define("TEMPLATE_PATH_EMAIL",TEMPLATE_PATH.'email/');
	define("PHP_INC_PAHT",SCRIPT_INCLUDE_BASE.'php_inc/' );
	define("MODEL_PATH",PHP_INC_PAHT.'model/');
	
	define("USER_PROFILE_ROOT", ROOTDIR.'user/'); //user profile page root
	
	define("PHP_INC_MODEL_ROOT_REF","php_inc/model/");
	define("PHP_INC_MODEL","../".PHP_INC_MODEL_ROOT_REF);
	define("LOADING_GIF",IMGDIR."loading.gif");
	
	define("LOGIN_PAGE",ROOTDIR.'login');
	define("HOME_PAGE",ROOTDIR);
	define("PASSWORD_RESET", ROOTDIR."password/reset");
	define("ERROR_PAGE",ROOTDIR."404.php");
	define("COOKIE_EXPIRE_TIME",time()+ 90 * 86400);
	define("MAXIMUM_UPLOAD_IMAGE_SIZE",5000000);
	define("DEFAULT_PROFILE_IMAGE",DEFAULT_IMAGE."default_profile_100pl.png");
	define("DEFAULT_COVER_IMAGE",DEFAULT_IMAGE."beach-230.jpg");
	define("DEFAULT_INTEREST_LABEL_IMAGE",DEFAULT_IMAGE."interest-label.png");
	define("DEFAULT_INTEREST_LABEL_IMAGE_PREFIX",DEFAULT_IMAGE.'interest-label-');
	define("MAX_INTEREST_LABEL_COLOR_RANDOM_INDEX", 6);
	define("MAX_PHOTO_BOUND", 10000000);	
	
	
?>