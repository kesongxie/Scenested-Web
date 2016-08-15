<?php
	// include_once PHP_INC_PATH.'core.inc.php';

	class User_Media_Base extends Core_Table{
		public $file_m = null;
// 		private $photo_stream_template_path = TEMPLATE_PATH_CHILD."photo_stream.phtml";
		private $table_name;
		private $primary_key;
		
		public function __construct($table_name = null, $primary_key = null){
			parent::__construct($table_name, $primary_key);
			$this->table_name = $table_name;
			$this->primary_key = $primary_key;
			$this->file_m = new File_Manager();
		}
		
		

		public function getPicture($selectorValue, $selectorColumn){
			$photoInfo = $this->getMultipleColumnsBySelector(array('user_id', 'picture_url', 'hash'), $selectorColumn,  $selectorValue , true); //include the wrapper folder directory
			if($photoInfo){
				$user_media_prefix = new User_Media_Prefix();
				$prefix = $user_media_prefix->getUserMediaPrefix($photoInfo["user_id"]); //folder for the given user
				return array("url" => U_IMGDIR.$prefix.'/'.$photoInfo["picture_url"], "hash" => $photoInfo["hash"]);
			}
			return false;
		}
		
		
		
		/*
			@param $cropped
				specify whether the uploading image need to be cropped or not
			@param @dst_dimension
				if $cropped is set to true, then need the caller pass the dimension of the destination image
				and has the format nested two dimensional array, ['large'=>{width, height}, 'thumb'=>{width, height}]
				For example  $dst_dimension['large']['width'], would give you the width of the large version of the 
				destination
		*/
		
		// public function uploadMediaForUser($file, $user_id, $ratio_scale_assoc, $thumb_return = true, $cropped = false, $dst_dimension = NULL){
// 			$user_media_prefix = new User_Media_Prefix();
// 			$hash = $this->generateUniqueHash();
// 			$prefix = $user_media_prefix->getUserMediaPrefix($user_id);
// 			if($prefix === false){
// 				$prefix = $user_media_prefix->createMediaPrefixForUser($user_id);
// 			}
// 			if($prefix !== false){
// 				if($cropped && $dst_dimension !== NULL){
// 					$picture_url = $this->uploadProfileMedia($file, $prefix, $ratio_scale_assoc, $dst_dimension);
// 				}else{
// 					$picture_url = $this->uploadPostMedia($file, $prefix);
// 				}
// 				if($picture_url !== false){
// 					$stmt = $this->connection->prepare("INSERT INTO `$this->table_name` (`user_id`,`picture_url`,`upload_time`,`hash`) VALUES(?, ?, ?,?)");
// 					$time = date("Y-m-d H:i:s");
// 					$stmt->bind_param('isss',$user_id, $picture_url, $time,$hash);
// 					if($stmt->execute()){
// 						$stmt->close();
// 						if($thumb_return === false){
// 							$picture_url = convertThumbPathToOriginPath($picture_url);
// 						}
// 						return U_IMGDIR.$prefix.'/'.$picture_url;
// 					}
// 				}
// 			}
// 			return false;	
// 		}
// 		


		/*
			return the user media prefix if existed, generated a new one otherwise. 
		*/

		public function getUserMediaPrefixForUpload($user_id){
			$user_media_prefix = new User_Media_Prefix();
			$prefix = $user_media_prefix->getUserMediaPrefix($user_id);
			if($prefix === false){
				$prefix = $user_media_prefix->createMediaPrefixForUser($user_id);
			}
			return $prefix;
		}
	
		public function uploadProfileMediaForUser($file, $user_id, $ratio_scale_assoc, $dst_dimension, $thumb_return = true){
			$prefix = $this->getUserMediaPrefixForUpload($user_id);
			if($prefix !== false && $dst_dimension !== NULL){
				$hash = $this->generateUniqueHash();
				$picture_url = $this->uploadProfileMedia($file, $prefix, $ratio_scale_assoc, $dst_dimension);
				if($picture_url !== false){
					$stmt = $this->connection->prepare("INSERT INTO `$this->table_name` (`user_id`,`picture_url`,`upload_time`,`hash`) VALUES(?, ?, ?,?)");
					$time = date("Y-m-d H:i:s");
					$stmt->bind_param('isss',$user_id, $picture_url, $time,$hash);
					if($stmt->execute()){
						$stmt->close();
						if($thumb_return === false){
							$picture_url = convertThumbPathToOriginPath($picture_url);
						}
						return array("url" => U_IMGDIR.$prefix.'/'.$picture_url, "hash" => $hash);
					}
				}
			}
			return false;	
		}
		
		
		
		public function uploadPostPhotoForUser($photoFile, $user_id, $user_scene_id, $dst_dimension, $thumb_return = true){
			$prefix = $this->getUserMediaPrefixForUpload($user_id);
			if($prefix !== false && $dst_dimension !== NULL){
				$hash = $this->generateUniqueHash();
				$picture_url = $this->uploadPostPhotos($photoFile, $prefix, $dst_dimension);
				if($picture_url !== false){
					$stmt = $this->connection->prepare("INSERT INTO `$this->table_name` (`user_id`,`scene_id`, `picture_url`,`upload_time`,`hash`) VALUES(?, ?, ?, ?,?)");
					$time = date("Y-m-d H:i:s");
					$stmt->bind_param('iisss',$user_id,$user_scene_id, $picture_url, $time, $hash);
					if($stmt->execute()){
						$stmt->close();
						if($thumb_return === false){
							$picture_url = convertThumbPathToOriginPath($picture_url);
						}
						return U_IMGDIR.$prefix.'/'.$picture_url;
					}
				}
			}
			return false;	
		}
		
		
		public function uploadFeaturePhotoForUser($photoFile, $user_id, $feature_id, $dst_dimension, $thumb_return = true){
			$userPrefix = $this->getUserMediaPrefixForUpload($user_id);
			if($userPrefix !== false && $dst_dimension !== NULL){
				$hash = $this->generateUniqueHash();
				$picture_url = $this->uploadFeaturePhoto($photoFile, $userPrefix, $dst_dimension);
				if($picture_url !== false){
					$stmt = $this->connection->prepare("INSERT INTO `$this->table_name` (`user_id`,`feature_id`, `picture_url`,`upload_time`,`hash`) VALUES(?, ?, ?, ?,?)");
					$time = date("Y-m-d H:i:s");
					$stmt->bind_param('iisss', $user_id, $feature_id, $picture_url, $time, $hash);
					if($stmt->execute()){
						$stmt->close();
						if($thumb_return === false){
							$picture_url = convertThumbPathToOriginPath($picture_url);
						}
						return U_IMGDIR.$userPrefix.'/'.$picture_url;
					}
				}
			}
			return false;	
		}
		
		
		

		public function uploadPostPhotos($photoFile, $user_media_prefix, $dst_dimension){
			return $this->file_m->upload_post_photo($photoFile, $user_media_prefix, $dst_dimension);
		}		
		
		public function uploadProfileMedia($file, $user_media_prefix, $ratio_scale_assoc, $dst_dimension){
			return $this->file_m->upload_cropped_file($file, $user_media_prefix, $ratio_scale_assoc, $dst_dimension);
		}		
		
		public function uploadFeaturePhoto($photoFile, $user_media_prefix, $dst_dimension){
			return $this->file_m->upload_photo($photoFile, $user_media_prefix, $dst_dimension);

		}
		
		
		
		
		/*
			this method is used for the case when the user id is not a column in the table structure, 
			the column name is $assoc_name instead of "user_id"
		*/
		public function uploadMediaForAssocColumn($file, $user_id, $hash, $assoc_name, $assoc_id){
			$user_media_prefix = new User_Media_Prefix();
			$prefix = $user_media_prefix->getUserMediaPrefix($user_id);
			if($prefix === false){
				$prefix = $user_media_prefix->createMediaPrefixForUser($user_id);
			}
			
			if($prefix !== false){
				$picture_url = $this->file_m->upload_File_To_Dir($file, $prefix);
				if($picture_url !== false){
					$stmt = $this->connection->prepare("INSERT INTO `$this->table_name` (`$assoc_name`,`user_id`,`picture_url`,`upload_time`,`hash`) VALUES(?,?, ?, ?, ?)");
					$time = date("Y-m-d H:i:s");
					$stmt->bind_param('iisss',$assoc_id,$user_id, $picture_url, $time, $hash);
					if($stmt->execute()){
						$stmt->close();
						return $picture_url;
					}
				}
			}
			return false;	
		}
		
		// public function uploadCaptionableMediaForAssocColumn($file,$user_id, $caption, $hash, $assoc_name, $assoc_id){
// 			$user_media_prefix = new User_Media_Prefix();
// 			$prefix = $user_media_prefix->getUserMediaPrefix($user_id);
// 			if($prefix === false){
// 				$prefix = $user_media_prefix->createMediaPrefixForUser($user_id);
// 			}
// 			
// 			if($prefix !== false){
// 				$picture_url = $this->file_m->upload_File_To_Dir($file, $prefix);
// 				if($picture_url !== false){
// 					$stmt = $this->connection->prepare("INSERT INTO `$this->table_name` (`$assoc_name`,`user_id`,`picture_url`,`upload_time`,`caption`,`hash`) VALUES(?,?, ?, ?, ?,?)");
// 					if($stmt){
// 						$time = date("Y-m-d H:i:s");
// 						$stmt->bind_param('iissss',$assoc_id, $user_id,$picture_url, $time, $caption, $hash);
// 						if($stmt->execute()){
// 							$stmt->close();
// 							
// 							return array('picture_url'=>$picture_url,'insert_id'=>$this->connection->insert_id);
// 						}
// 					}
// 				}
// 			}
// 			echo $this->connection->error;
// 			return false;	
// 		}
// 		
// 		
		
		public function deleteMediaByPictureUrl($url, $user_id){
			return $this->file_m->removeMediaFileForUser($url, $user_id);
		}
		
		
		public function renderPhotoStreamByPictureUrl($url, $user_id, $source_from, $hash){
			$user_media_prefix = new User_Media_Prefix();
			$prefix = $user_media_prefix->getUserMediaPrefix($user_id);
			$post_key = false;
			if($source_from == 'e'){
				include_once MODEL_PATH.'Event_Photo.php';
				$evt_pht = new Event_Photo();
				$event_id = $evt_pht->getColumnBySelector('event_id','hash',$hash);
				if($event_id !== false){
					include_once MODEL_PATH.'Event.php';
					$evt = new Event(null, false);
					$activity_id = $evt->getColumnById('interest_activity_id',$event_id);
					if($activity_id !== false){
						include_once MODEL_PATH.'Interest_Activity.php';
						$activity = new Interest_Activity();
						$post_key  = $activity->getColumnById('hash',$activity_id);
					}
				}
			}
			if($prefix !== false){
				if(!empty($url) && !empty($prefix) && isMediaDisplayable($prefix.'/'.$url)){
					$url =  U_IMGDIR.$prefix.'/'.$url;
					ob_start();
					include($this->photo_stream_template_path);
					$content = ob_get_clean();
					return $content;
				}
			}
			return false;
		}
		
		
		
		
		public function getUserMediaBlockByUserId($user_id){
			/*get users that are in the $user_id's events*/
			include_once MODEL_PATH.'Groups.php';
			$group = new Groups();
			
			$event_array = $group->getEventArrayForUser($user_id);
			$list = false;
			if($event_array !== false && sizeof($event_array) > 0){
				$list = '';
				foreach($event_array as $event){
					$list.="'".$event."',";
				}
				$list = trim($list, ',');
			}
			$query = "SELECT  'm' AS `source_from`, `id`, `user_id`,`picture_url`, `upload_time`, `hash`  FROM moment_photo WHERE `user_id` = ?
				UNION  
				SELECT  'e' AS `source_from`, `id`, `user_id`,`picture_url`, `upload_time`, `hash`  FROM event_photo WHERE `user_id` = ?
				UNION  
				SELECT  'p' AS `source_from`, `id`,`user_id`,`picture_url`, `upload_time`, `hash`  FROM user_profile_picture WHERE `user_id` = ?
				UNION  
				SELECT  'c' AS `source_from`, `id`,`user_id`,`picture_url`, `upload_time`, `hash`  FROM user_profile_cover WHERE `user_id` = ?
				";
			
			if($list !== false){
				$query .=  "UNION  
				SELECT  'e' AS `source_from`, `id`,`user_id`, `picture_url`, `upload_time`, `hash`  FROM event_photo WHERE `event_id` IN($list) 
				";
			}
			
			$query .= "ORDER BY upload_time DESC LIMIT 10";
			$stmt = $this->connection->prepare($query);
			
			if($stmt){
					$stmt->bind_param('iiii',$user_id, $user_id,$user_id,$user_id);
					if($stmt->execute()){
						 $result = $stmt->get_result();
						 $content = false;
						 if($result !== false && $result->num_rows >= 1){
							$rows = $result->fetch_all(MYSQLI_ASSOC);
							$stmt->close();
							if($rows !== false){
								$left_content = "";
								$right_content = "";
								$count = 0;
								foreach($rows as $row){
									$content= $this->renderPhotoStreamByPictureUrl($row['picture_url'], $row['user_id'], $row['source_from'], $row['hash']);
									if($content !== false){
										if($count++ % 2 == 0){
											$left_content.= $content;
										}else{
											$right_content.= $content;
										}
									}
								}
								$content = true;
							}
						 }
						 if($_SESSION['id'] != $user_id){
						 	include_once MODEL_PATH.'User_Table.php';
						 	$user = new User_Table();
						 	$firstname = $user->getUserFirstNameByUserIden($user_id);
						 }
						ob_start();
						include(TEMPLATE_PATH_CHILD.'photo.phtml');
						$content= ob_get_clean();
						return $content;
					}
			}
			echo $this->connection->error;
			return false;
		}
		
		/*from can be m, e, p, c and stand for moment, event, profile, cover respectively, ei is interest label image*/
		public function getPreviewImage($hash, $from){
			switch($from){
				case 'm' :return  $this->loadMomentPreviewImage($hash);
				case 'e': return  $this->loadEventPreviewImage($hash);
				case 'p': return  $this->loadProfilePreviewImage($hash);
				case 'c': return  $this->loadPorfileCoverPreviewImage($hash);
				default:break;
			}
		}
		
		
		public function loadMomentPreviewImage($hash){
			include_once MODEL_PATH.'Moment_Photo.php';
			$m_p = new Moment_Photo();
			return $m_p->loadMomentPhotoPreviewBlock($hash);
		}
		
		
		public function loadEventPreviewImage($hash){
			include_once MODEL_PATH.'Event_Photo.php';
			$e_p = new Event_Photo();
			return $e_p->loadEventPhotoPreviewBlock($hash);
		}
		
		public function loadProfilePreviewImage($hash){
			include_once MODEL_PATH.'User_Profile_Picture.php';
			$p = new User_Profile_Picture();
			return $p->loadProfilePhotoPreviewBlock($hash);
		}
		
		public function loadPorfileCoverPreviewImage($hash){
			include_once MODEL_PATH.'User_Profile_Cover.php';
			$c = new User_Profile_Cover();
			return $c->loadProfileCoverPhotoPreviewBlock($hash);
		}
		
		public function returnPhotoBySchoolKeyWord($school_key_word, $limit = -1, $last_m = MAX_PHOTO_BOUND, $last_e = MAX_PHOTO_BOUND ){
			include_once MODEL_PATH.'School.php';
			$search_school_array = School::getSchooIdsLikeSchoolName($school_key_word);
			$search_school_id = '';
			if($search_school_array !== false){
				foreach($search_school_array as $id){
					$search_school_id .= "'".$id['id']."',";
				}
				$search_school_id = trim($search_school_id,',');
			}else{
				return false;
			}
			if($limit > 0){
				$stmt = $this->connection->prepare("
				SELECT * 	
				FROM
				(
				SELECT 'm' AS `source_from`, moment_photo.user_id, moment_photo.picture_url, moment_photo.upload_time AS time, moment_photo.hash
				FROM  education
				LEFT JOIN moment_photo
				ON education.user_id = moment_photo.user_id
				WHERE  moment_photo.id < ? AND education.school_id IN($search_school_id)
			
				UNION 
			
				SELECT 'e' AS `source_from`, event_photo.user_id, event_photo.picture_url, event_photo.upload_time AS time, event_photo.hash
				FROM  education
				LEFT JOIN event_photo
				ON education.user_id = event_photo.user_id
				WHERE   event_photo.id < ? AND education.school_id IN($search_school_id)
				) dum ORDER BY time DESC LIMIT ?
				");		
			}else{
				$stmt = $this->connection->prepare("
				SELECT * 	
				FROM
				(
				SELECT 'm' AS `source_from`, moment_photo.user_id, moment_photo.picture_url, moment_photo.upload_time AS time, moment_photo.hash
				FROM  education
				LEFT JOIN moment_photo
				ON education.user_id = moment_photo.user_id
				WHERE  moment_photo.id < ? AND education.school_id IN($search_school_id)
			
				UNION 
			
				SELECT 'e' AS `source_from`, event_photo.user_id, event_photo.picture_url, event_photo.upload_time AS time, event_photo.hash
				FROM  education
				LEFT JOIN event_photo
				ON education.user_id = event_photo.user_id
				WHERE   event_photo.id < ? AND education.school_id IN($search_school_id)
				) dum ORDER BY time DESC
				");		
			}
			
			if($stmt){
				if($limit > 0){
					$stmt->bind_param('iii',$last_m, $last_e, $limit);
				}else{
					$stmt->bind_param('ii',$last_m, $last_e);
				}
				if($stmt->execute()){
					 $result = $stmt->get_result();
					 if($result !== false && $result->num_rows >= 1){
						$rows = $result->fetch_all(MYSQLI_ASSOC);
						$stmt->close();
						// $left_content = "";
// 						$right_content = "";
// 						$count = 0;
						// foreach($rows as $row){
// 							$content= $this->renderPhotoStreamByPictureUrl($row['picture_url'], $row['user_id'],$row['source_from'], $row['hash']);
// 							if($content !== false){
// 								if($count++ % 2 == 0){
// 									$left_content.= $content;
// 								}else{
// 									$right_content.= $content;
// 								}
// 							}
// 						}
						
						return $rows;
					}
				}
			}
			echo $this->connection->error;
			return false;
		}
		
		public function getLastLoadedStreamId($l, $r, $type){
			//l is the last key for left column 
			//r is the last key for right column 
			//type can be either m, e, c, p stands for photos of moment, event, cover, profile repsecitvely
			$obj = false;
			switch($type){
				case 'm':
					include_once MODEL_PATH.'Moment_Photo.php';
					$obj = new Moment_Photo();break;
				case 'e';
					include_once MODEL_PATH.'Event_Photo.php';
					$obj = new Event_Photo();break;
				case 'c':
					include_once MODEL_PATH.'User_Profile_Cover.php';
					$obj = new User_Profile_Cover();break;
				case 'p':
					include_once MODEL_PATH.'User_Profile_Picture.php';
					$obj = new User_Profile_Picture();break;
				default:break;	
			}
			if($obj !== false){
				if($l !== false || $r !== false){
					$temp_1 = MAX_PHOTO_BOUND;
					$temp_2 = MAX_PHOTO_BOUND;
					if($l !== false){
						$temp_1 = $obj->getRowIdByHashkey($l);
						if($temp_1 === false){
							$temp_1 = MAX_PHOTO_BOUND;
						}
					}
					if($r !== false){
						$temp_2 = $obj->getRowIdByHashkey($r);
						if($temp_2 === false){
							$temp_2 = MAX_PHOTO_BOUND;
						}
					}
					return min($temp_1, $temp_2);
				}
			}
			return MAX_PHOTO_BOUND;	
		}
		
		
		public function loadProfilePhotoStream($l_m, $r_m, $l_e, $r_e,$l_p, $r_p,$l_c, $r_c, $user_key){
		include_once MODEL_PATH.'Groups.php';
		include_once MODEL_PATH.'User_Table.php';
		
		include_once MODEL_PATH.'Groups.php';
			$user = new User_Table();
			$user_id = $user->getUserIdByKey($user_key);
			if($user_id !== false){
				$group = new Groups();
				$event_array = $group->getEventArrayForUser($user_id);
				$list = false;  //$list not false when there is event group for users
				if($event_array !== false && sizeof($event_array) > 0){
					$list = '';
					foreach($event_array as $event){
						$list.="'".$event."',";
					}
					$list = trim($list, ',');
				}
				$query = "SELECT  'm' AS `source_from`, `id`, `user_id`,`picture_url`, `upload_time`, `hash`  FROM moment_photo WHERE `user_id` = ? AND `id` < ?
					UNION  
					SELECT  'e' AS `source_from`, `id`, `user_id`,`picture_url`, `upload_time`, `hash`  FROM event_photo WHERE `user_id` = ? AND `id` < ?
					UNION  
					SELECT  'p' AS `source_from`, `id`,`user_id`,`picture_url`, `upload_time`, `hash`  FROM user_profile_picture WHERE `user_id` = ? AND `id` < ?
					UNION  
					SELECT  'c' AS `source_from`, `id`,`user_id`,`picture_url`, `upload_time`, `hash`  FROM user_profile_cover WHERE `user_id` = ? AND `id` < ?
					";
			
				if($list !== false){
					$query .=  "UNION  
					SELECT  'e' AS `source_from`, `id`,`user_id`, `picture_url`, `upload_time`, `hash`  FROM event_photo WHERE `event_id` IN($list) AND `id` < ?
					"; //include other users' photos to the same collection as well
				}
				
				$query .= "ORDER BY upload_time DESC LIMIT 10";
				$stmt = $this->connection->prepare($query);
				if($stmt){
					$last_m = $this->getLastLoadedStreamId($l_m, $r_m, 'm');
					$last_e = $this->getLastLoadedStreamId($l_e, $r_e, 'e');
					$last_p = $this->getLastLoadedStreamId($l_p, $r_p, 'p');
					$last_c = $this->getLastLoadedStreamId($l_c, $r_c, 'c');
					if($list !== false){
						$stmt->bind_param('iiiiiiiii',$user_id,$last_m, $user_id, $last_e, $user_id, $last_p,$user_id, $last_c, $last_e);
					}else{
						$stmt->bind_param('iiiiiiii',$user_id,$last_m, $user_id, $last_e, $user_id, $last_p,$user_id, $last_c);
					}
					if($stmt->execute()){
						 $result = $stmt->get_result();
						 if($result !== false && $result->num_rows >= 1){
							$rows = $result->fetch_all(MYSQLI_ASSOC);
							$stmt->close();
							if($rows !== false){
								$left_content = "";
								$right_content = "";
								$count = 0;
								foreach($rows as $row){
									$content= $this->renderPhotoStreamByPictureUrl($row['picture_url'], $row['user_id'], $row['source_from'], $row['hash']);
									if($content !== false){
										if($count++ % 2 == 0){
											$left_content.= $content;
										}else{
											$right_content.= $content;
										}
									}
								}
								ob_start();
								include(TEMPLATE_PATH_CHILD.'loading_feed_wrapper.phtml');
								$content = ob_get_clean();
								return $content;
							}
						
						 }
					}
				}
			}
			echo $this->connection->error;
			return false;
		
			
		}

		
	}		
?>