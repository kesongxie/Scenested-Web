<?php
	include_once PHP_INC_MODEL_ROOT_REF.'Interest.php';
	$interest = new Interest();
	
		
	
	$active_interest_id = -1;
	if(isset($_GET['a'])){
		$interest_id = $interest->getInterestIdByNameForUser($_GET['a'],$request_user_page_id);
		if($interest_id !== false){
			$content = $interest->getUserInterestBlockByInterestId($interest_id);
			$active_interest_id = $interest_id;
		}else{
			$content = $interest->initContentForInterest($request_user_page_id,true);

		}
	}else{
		$content = $interest->initContentForInterest($request_user_page_id,true);
	}
	
	
	$request_user_page_has_interest = ($content !== false)?true:false;
	$user_interests =$interest->getUserInterestsLabel($request_user_page_id, 'interests');
	require_once TEMPLATE_PATH_CHILD.'interest.phtml';
?>