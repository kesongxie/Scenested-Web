<?php
	include_once MODEL_PATH.'Core_Table.php';
	include_once MODEL_PATH. 'User_Media_Prefix.php';
	include_once PHP_INC_PAHT.'File_Manager.php';



	class User_Media_Base extends Core_Table{
		public $file_m = null;
		private $photo_stream_template_path = TEMPLATE_PATH_CHILD."photo_stream.phtml";
		private $table_name;
		
		public function __construct($table_name = null){
			parent::__construct($table_name);
			$this->table_name = $table_name;
			$this->file_m = new File_Manager();
		}
		
		public function uploadMediaForUser($file,$user_id){
			$user_media_prefix = new User_Media_Prefix();
			$hash = $this->generateUniqueHash();
			$prefix = $user_media_prefix->getUserMediaPrefix($user_id);
			if($prefix === false){
				$prefix = $user_media_prefix->createMediaPrefixForUser($user_id);
			}
			
			if($prefix !== false){
				$picture_url = $this->file_m->upload_File_To_Dir($file, $prefix);
				if($picture_url !== false){
					$stmt = $this->connection->prepare("INSERT INTO `$this->table_name` (`user_id`,`picture_url`,`upload_time`,`hash`) VALUES(?, ?, ?,?)");
					$time = date("Y-m-d H:i:s");
					$stmt->bind_param('isss',$user_id, $picture_url, $time,$hash);
					if($stmt->execute()){
						$stmt->close();
						return true;
					}
				}
			}
			return false;	
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
		
		public function uploadCaptionableMediaForAssocColumn($file,$user_id, $caption, $hash, $assoc_name, $assoc_id){
			$user_media_prefix = new User_Media_Prefix();
			$prefix = $user_media_prefix->getUserMediaPrefix($user_id);
			if($prefix === false){
				$prefix = $user_media_prefix->createMediaPrefixForUser($user_id);
			}
			
			if($prefix !== false){
				$picture_url = $this->file_m->upload_File_To_Dir($file, $prefix);
				if($picture_url !== false){
					$stmt = $this->connection->prepare("INSERT INTO `$this->table_name` (`$assoc_name`,`user_id`,`picture_url`,`upload_time`,`caption`,`hash`) VALUES(?,?, ?, ?, ?,?)");
					if($stmt){
						$time = date("Y-m-d H:i:s");
						$stmt->bind_param('iissss',$assoc_id, $user_id,$picture_url, $time, $caption, $hash);
						if($stmt->execute()){
							$stmt->close();
							
							return array('picture_url'=>$picture_url,'insert_id'=>$this->connection->insert_id);
						}
					}
				}
			}
			echo $this->connection->error;
			return false;	
		}
		
		
		
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
			
			$stmt = $this->connection->prepare("
				SELECT  'm' AS `source_from`, `id`,`picture_url`, `upload_time`, `hash`  FROM moment_photo WHERE `user_id` = ?
				UNION  
				SELECT  'e' AS `source_from`, `id`, `picture_url`, `upload_time`, `hash`  FROM event_photo WHERE `user_id` = ?
				UNION  
				SELECT  'p' AS `source_from`, `id`,`picture_url`, `upload_time`, `hash`  FROM user_profile_picture WHERE `user_id` = ?
				UNION  
				SELECT  'c' AS `source_from`, `id`,`picture_url`, `upload_time`, `hash`  FROM user_profile_cover WHERE `user_id` = ?
				ORDER BY upload_time DESC
			");	
		
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
									$content= $this->renderPhotoStreamByPictureUrl($row['picture_url'], $user_id, $row['source_from'], $row['hash']);
									if($content !== false){
										if($count++ % 2 == 0){
											$left_content.= $content;
										}else{
											$right_content.= $content;
										}
									}
								}
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
		
		public function returnPhotoBySchoolKeyWord($school_key_word){
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
			
			$stmt = $this->connection->prepare("
			SELECT * 	
			FROM
			(
			SELECT 'm' AS `source_from`, moment_photo.user_id, moment_photo.picture_url, moment_photo.upload_time AS time, moment_photo.hash
			FROM  education
			LEFT JOIN moment_photo
			ON education.user_id = moment_photo.user_id
			WHERE  education.school_id IN($search_school_id)
			
			UNION 
			
			SELECT 'e' AS `source_from`, event_photo.user_id, event_photo.picture_url, event_photo.upload_time AS time, event_photo.hash
			FROM  education
			LEFT JOIN event_photo
			ON education.user_id = event_photo.user_id
			WHERE  education.school_id IN($search_school_id)
			) dum ORDER BY time DESC
			");			
			
			if($stmt){
				if($stmt->execute()){
					 $result = $stmt->get_result();
					 if($result !== false && $result->num_rows >= 1){
						$rows = $result->fetch_all(MYSQLI_ASSOC);
						$stmt->close();
						$left_content = "";
						$right_content = "";
						$count = 0;
						foreach($rows as $row){
							$content= $this->renderPhotoStreamByPictureUrl($row['picture_url'], $row['user_id'],$row['source_from'], $row['hash']);
							if($content !== false){
								if($count++ % 2 == 0){
									$left_content.= $content;
								}else{
									$right_content.= $content;
								}
							}
						}
						
						return array('left_content'=>$left_content, 'right_content'=>$right_content);
					}
				}
			}
			echo $this->connection->error;
			return false;
		}
		
		
		
		
	}		
?>