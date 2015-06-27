<?php
	//core.inc.php includes global functions 
	session_start();
	include_once 'global_constant.php'; 
	function deleteCookie($cookie_name){
		setcookie ($cookie_name, "", time() - 3600, '/');
	}	
	
	function clearLoginCredential(){
		unset($_SESSION['id']);
		deleteCookie("identifier");
		deleteCookie("token");
	}
	
	function getRandomString(){
		return bin2hex(openssl_random_pseudo_bytes(12));
	}				

	function getMediaFileExtension($file){
		return strtolower(pathinfo(basename($file["name"]),PATHINFO_EXTENSION));
	}
	
	function getDefaultInterestLabelImageByNum($num){
		return DEFAULT_INTEREST_LABEL_IMAGE_PREFIX.$num.'.png';
	}
	
	function validateDate($date, $format = 'Y-m-d H:i:s')
	{
		$d = DateTime::createFromFormat($format, $date);
		return $d && $d->format($format) == $date;
	}
		
	function convertDateToNewFormat($date, $format){
		$d = DateTime::createFromFormat($format, $date);
		return $d;
	}	
	
	function isMediaDisplayable($media_url){
		return ($media_url !== false && file_exists(SCRIPT_INCLUDE_BASE.MEDIA_U.$media_url));
	}
	
	

	/*
		from format Y-m-d H:i:s to ago
	*/
	function convertDateTimeToAgo($str, $withAgo){
		list($date, $time) = explode(' ', $str);
    	list($year, $month, $day) = explode('-', $date);
    	list($hour, $minute, $second) = explode(':', $time);
    	$timestamp = mktime($hour, $minute, $second, $month, $day, $year);
    	
    	$difference = time() - $timestamp;
   		$periods = array("sec", "min", "hour", "day", "week", "mon", "year", "decade");
   		$lengths = array("60","60","24","7","4.35","12","10");
   		for($j = 0; $difference >= $lengths[$j]; $j++)
   			$difference /= $lengths[$j];
   		$difference = round($difference);
		if($difference != 0){	
			if($difference != 1) $periods[$j].= "s";
			$text = "$difference $periods[$j]";
			if($withAgo){
				$text.=" ago";
			}
   		}else{
   			$text = 'just now';
   		}
   		return $text;
    }
		
	/*
		for example, 2015 03 13 -> March 13 if the current year is the same, otherwise March 13 - 2013 
	*/
	function returnShortDate($date){
		$ts = strtotime($date);
		if(date('Y',$ts) ==  date('Y'))
			return date('M d',$ts);
		else{
			return date('M d - Y',$ts);
		}
	}		
	
	
		
	
?>