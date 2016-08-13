<?php
	include_once $_SERVER['DOCUMENT_ROOT'].'/php_inc/core.inc.php';


	//$_POST["info"] contains textual info
	$userInfo = json_decode($_POST["info"], true);
	$paramInfo =[
		"userInfo" => $userInfo,
		"images" => $_FILES
	];	
	$user = new User($paramInfo["userInfo"]["userId"]);
	

 // ["userInfo"]=>
//   array(2) {
//     ["userId"]=>
//     int(107)
//     ["name"]=>
//     string(5) "Swift"
//   }
//   ["images"]=>
//   array(1) {
//     ["featurePhoto"]=>
//     array(5) {
//       ["name"]=>
//       string(16) "featurePhoto.jpg"
//       ["type"]=>
//       string(9) "image/jpg"
//       ["tmp_name"]=>
//       string(36) "/Applications/MAMP/tmp/php/phpi2sgtj"
//       ["error"]=>
//       int(0)
//       ["size"]=>
//       int(69734)
//     }
//   }

	$result = $user->addUserFeature($paramInfo);
	
	
	/*
	
		Optional(array(3) {
  ["status"]=>
  bool(true)
  ["feature"]=>
  array(3) {
    ["featureId"]=>
    int(16)
    ["featureName"]=>
    string(9) "hjjjkk be"
    ["featureCoverUrl"]=>
    string(118) "http://192.168.1.104:8888/media_u/7563a498264036382646ac5b/5148d45c8f5b6d68ff6d9c96/thumb_5885f86b993d1229081fddda.jpg"
  }
  ["errorCode"]=>
  bool(false)
}
)
	*/
	
	
	if($result !== false){
		if($result['status']){
			$feature = $result[Feature::FeatureKey] ;
			echo json_encode([
				"statusCode" => 200,
				Feature::FeatureKey => [ 
								Feature::FeatureIdKey => $feature[Feature::FeatureIdKey],
								Feature::FeatureNameKey =>  $feature[Feature::FeatureNameKey],
								Feature::FeatureCoverUrlKey => $feature[Feature::FeatureCoverUrlKey],
								Feature::FeatureCoverHashKey => $feature[Feature::FeatureCoverHashKey]
								],
				"errorCode" => false
			]);
		}else{
			echo json_encode([
				"statusCode" => 200,
				Feature::FeatureKey => false,
				"errorCode" => $result['errorCode']
			]);
		}
	}else{
		echo json_encode([
			"statusCode" => 500
		]);
	}
 	
 	



	
?>