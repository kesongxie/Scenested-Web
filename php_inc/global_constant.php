<?php
	//	contains global constants such as folder structures
	/*************** page render ******************/
	include_once 'server_cred.php';
	define("IMGDIR",ROOTDIR."media/");
	define("U_IMGDIR",ROOTDIR."media_u/");
	define("DUMMY_MEDIA",IMGDIR."dummy/");
	define("E_LOGO",IMGDIR.'e/');
	define("SYSTEM_IMAGE",IMGDIR.'si/');


	
	/*************** file path ******************/
	define("DOCUMENT_ROOT",$_SERVER['DOCUMENT_ROOT'].'/');
	define("SCRIPT_INCLUDE_BASE",$_SERVER['DOCUMENT_ROOT'].'/');
	define("MEDIA_U",SCRIPT_INCLUDE_BASE."media_u/");
	define("TEMPLATE_PATH",SCRIPT_INCLUDE_BASE.'phtml/');
	define("TEMPLATE_PATH_CHILD",TEMPLATE_PATH.'child/');
	define("PHP_INC_PATH",SCRIPT_INCLUDE_BASE.'php_inc/');
	define("U_MEDAI_FOLDER_DIR",DOCUMENT_ROOT.'media_u/');
	

	

	
	
	/*************** global attributes ******************/
	
	//COVER ASPECT RATIO AND SIZE
	define("COVER_PHOTO_ASPECT_RATIO", 3.2);
	define("COVER_PHOTO_MAX_WIDTH", 1280);
	define("COVER_PHOTO_MAX_HEIGHT", COVER_PHOTO_MAX_WIDTH / COVER_PHOTO_ASPECT_RATIO);
	define("COVER_PHOTO_THUMB_WIDTH", 512 );
	define("COVER_PHOTO_THUMB_HEIGHT", COVER_PHOTO_THUMB_WIDTH / COVER_PHOTO_ASPECT_RATIO );
	
	//AVATOR ASPECT RATIO AND SIZE
	define("AVATOR_PHOTO_ASPECT_RATIO", 1);
	define("AVATOR_PHOTO_MAX_WIDTH", 640);
	define("AVATOR_PHOTO_MAX_HEIGHT", AVATOR_PHOTO_MAX_WIDTH / AVATOR_PHOTO_ASPECT_RATIO);
	define("AVATOR_PHOTO_THUMB_WIDTH", 260 );
	define("AVATOR_PHOTO_THUMB_HEIGHT", AVATOR_PHOTO_THUMB_WIDTH / AVATOR_PHOTO_ASPECT_RATIO );
	
	//POST PHOTO MAX SIZE FOR LARGE AND THUMB, NO NEEP FOR ASPECT RATIO, BECAUSE THEY DON'T NEED TO BE CROPPED
	define("POST_PHOTO_MAX_WIDTH", 1600);
	define("POST_PHOTO_MAX_HEIGHT", POST_PHOTO_MAX_WIDTH / 1);
	define("POST_PHOTO_THUMB_WIDTH", 800 );
	define("POST_PHOTO_THUMB_HEIGHT", POST_PHOTO_THUMB_WIDTH / 1 );
	
	//FEATURE PHOTO MAX SIZE
	define("FEATURE_PHOTO_MAX_WIDTH", 800);
	define("FEATURE_PHOTO_MAX_HEIGHT", FEATURE_PHOTO_MAX_WIDTH / 1);
	define("FEATURE_PHOTO_THUMB_WIDTH", 200 );
	define("FEATURE_PHOTO_THUMB_HEIGHT", FEATURE_PHOTO_THUMB_WIDTH / 1 );
	
	
	
	
	
	
	define("MAXIMUM_UPLOAD_IMAGE_SIZE",5000000);
	define("MEDIA_THUMBNAIL_PREFIX","thumb_");
	
	
	
	
	
	/*define("DEFAULT_IMAGE",IMGDIR.'default_image/');
	
		define("DEFAULT_IMAGE_PATH",IMGDIR.'default_image/');
	define("DEFAULT_PROFILE_IMAGE",DEFAULT_IMAGE_PATH."default_profile_100pl.png");
	
	define("SEARCH_PEOPLE_PATH",ROOTDIR.'search.php?q=');
	define("SEARCH_SIMILAR_PEOPLE_PATH",ROOTDIR.'search.php?r=mine');
	define("SEARCH_MINE_EVENT_PATH", ROOTDIR.'search.php?r=mine&t=event');
	define("SEARCH_MINE_PHOTO_PATH", ROOTDIR.'search.php?r=mine&t=photo');
	define("JS_PATH", ROOTDIR.'js/');
	define("CHILD_JS_PATH", JS_PATH.'child_js/');
	define("SPINNER_ICON", IMGDIR."spinner.gif");
	define("LOGO_URL",IMGDIR.'logo.png');

	//paths for php file system
	define("DOCUMENT_ROOT",'../');
	define("U_MEDAI_FOLDER_DIR",DOCUMENT_ROOT.'media_u/');
	

	define("SCRIPT_INCLUDE_BASE",$_SERVER['DOCUMENT_ROOT'].'/');
	define("MEDIA_U",SCRIPT_INCLUDE_BASE."media_u/");
	define("TEMPLATE_PATH",SCRIPT_INCLUDE_BASE.'phtml/');
	define("AJAX_TEMPLATE_PATH", SCRIPT_INCLUDE_BASE.'/ajax/phtml/');

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
	define("MAX_PHOTO_BOUND", 10000000);	*/
	
?>