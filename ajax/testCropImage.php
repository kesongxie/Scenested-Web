<?php
	include_once '../php_inc/core.inc.php';
	
	/*
	
		array(2) {
			  ["file-0"]=>
			  array(5) {
				["name"]=>
				string(13) "IMG_9327.jpeg"
				["type"]=>
				string(10) "image/jpeg"
				["tmp_name"]=>
				string(36) "/Applications/MAMP/tmp/php/phppwbmg3"
				["error"]=>
				int(0)
				["size"]=>
				int(393216)
			  }
			  ["file-1"]=>
			  array(5) {
				["name"]=>
				string(13) "IMG_4173.jpeg"
				["type"]=>
				string(10) "image/jpeg"
				["tmp_name"]=>
				string(36) "/Applications/MAMP/tmp/php/phpfUzzP3"
				["error"]=>
				int(0)
				["size"]=>
				int(114688)
			  }
			}
			array(2) {
			  ["ratio-0"]=>
			  string(8) "-0.34375"
			  ["ratio-1"]=>
			  string(1) "0"
			}
	
		function crop_image($target, $newcopy, $w_desc, $h_desc, $x_position_ratio, $y_position_ratio, $ext, $crop_square = true, $cropped_aspect_ratio){
		list($w_orig, $h_orig) = getimagesize($target);
		$src_y = $h_orig * $y_position_ratio;
		$src_x = $w_orig * $x_position_ratio;
		$img = "";
		$ext = strtolower($ext);
		if ($ext == "gif"){ 
		  $img = imagecreatefromgif($target);
		} else if($ext =="png"){ 
		  $img = imagecreatefrompng($target);
		} else { 
		  $img = imagecreatefromjpeg($target);
		}
		$tci = imagecreatetruecolor($w_desc, $h_desc);
		if($crop_square){
			if($h_orig >= $w_orig){
				//portrain
				imagecopyresampled($tci, $img, 0, 0, $src_x, $src_y, $w_desc, $h_desc, $w_orig, $w_orig);
			}else{
				imagecopyresampled($tci, $img, 0, 0, $src_x, $src_y, $w_desc, $h_desc, $h_orig, $h_orig);
			}   
		}else{
			//vertical crop
			imagecopyresampled($tci, $img, 0, 0, $src_x, $src_y, $w_desc, $h_desc, $w_orig, $w_orig * $cropped_aspect_ratio );
		}
	
		imagejpeg($tci, $newcopy, 80);
	}

foreach($_POST['ratio_0'] as $ratio_key => $ratio_value){
		parse_str($ratio_value, $data);
		var_dump($data);
		// echo $ratio_value;
	}
	
	
	*/
	
	
	/*
		array(5) {
		  ["name"]=>
		  string(12) "IMG_0871.JPG"
		  ["type"]=>
		  string(10) "image/jpeg"
		  ["tmp_name"]=>
		  string(36) "/Applications/MAMP/tmp/php/phpL5V75T"
		  ["error"]=>
		  int(0)
		  ["size"]=>
		  int(745304)
		}
		array(4) {
		  ["adjusted_ratio_height"]=>
		  string(9) "0.2734375"
		  ["image_container_scale_height"]=>
		  string(18) "0.4166666666666667"
		  ["adjusted_ratio_width"]=>
		  string(1) "0"
		  ["image_container_scale_width"]=>
		  string(1) "1"
	*/
	
	
	$image_container_scale_width = $_POST['image_container_scale_width'];
	$image_container_scale_height = $_POST['image_container_scale_height'];
	$adjusted_ratio_width = $_POST['adjusted_ratio_width'];
	$adjusted_ratio_height = $_POST['adjusted_ratio_height'];
	
	
	// var_dump($image_container_scale_width);	
// 	var_dump($image_container_scale_height);	
// 	
	
	
	$file = $_FILES['file'];
	list($src_w, $src_h, $extension) = getimagesize($file['tmp_name']);
	$extension = getMediaFileExtension($file);
	$dst_w =  1280;
	$dst_h = 400;
	
	
	if ($extension == "gif"){ 
	  $src_image = imagecreatefromgif($file['tmp_name']);
	} else if($extension == "png"){ 
	  $src_image = imagecreatefrompng($file['tmp_name']);
	} else { 
	  $src_image = imagecreatefromjpeg($file['tmp_name']);
	}
	$dst_image = imagecreatetruecolor($dst_w, $dst_h); //create a black canvas, return image identifier
	imagecopyresampled ($dst_image, $src_image, 0, 0, $src_w * $adjusted_ratio_width, $src_h * $adjusted_ratio_height , $dst_w ,  $dst_h ,  $src_w * $image_container_scale_width ,  $src_h * $image_container_scale_height);
	imagejpeg($dst_image, MEDIA_U.'newimage.jpg', 100);
	
	
	
	
	
	
	//two column horizon	
	/*if($_POST['layout_mode'] == 'two-column-horizon'){
		for($i = 0; $i < sizeof($_FILES) ; $i++){
			parse_str($_POST['ratio_'.$i], $ratio);
			$ratio_container_scale = $ratio['image_container_scale'];
			$adjusted_margin_ratio = $ratio['adjusted_margin_ratio'];
			$file = $_FILES['file_'.$i];
			list($src_w, $src_h, $extension) = getimagesize($file['tmp_name']);
			$extension = getMediaFileExtension($file);
			$dst_w =  1024;
			$dst_h = 512;
			if ($extension == "gif"){ 
			  $src_image = imagecreatefromgif($file['tmp_name']);
			} else if($extension == "png"){ 
			  $src_image = imagecreatefrompng($file['tmp_name']);
			} else { 
			  $src_image = imagecreatefromjpeg($file['tmp_name']);
			}
			$dst_image = imagecreatetruecolor($dst_w, $dst_h); //create a black canvas, return image identifier
			imagecopyresampled ($dst_image, $src_image, 0, 0, 0, -$src_h * $adjusted_margin_ratio , $dst_w ,  $dst_h ,  $src_w ,  $src_h * $ratio_container_scale);
			imagejpeg($dst_image, MEDIA_U.$i.'new.jpeg', 100);
		}
	}else if($_POST['layout_mode'] == 'two-column-vertical'){
		//two column vertical
		for($i = 0; $i < sizeof($_FILES) ; $i++){
			parse_str($_POST['ratio_'.$i], $ratio);
			$ratio_container_scale = $ratio['image_container_scale'];
			$adjusted_margin_ratio = $ratio['adjusted_margin_ratio'];
			$file = $_FILES['file_'.$i];
			list($src_w, $src_h, $extension) = getimagesize($file['tmp_name']);
			$extension = getMediaFileExtension($file);
			$dst_w =  512;
			$dst_h = 1024;
			if ($extension == "gif"){ 
			  $src_image = imagecreatefromgif($file['tmp_name']);
			} else if($extension == "png"){ 
			  $src_image = imagecreatefrompng($file['tmp_name']);
			} else { 
			  $src_image = imagecreatefromjpeg($file['tmp_name']);
			}
			$dst_image = imagecreatetruecolor($dst_w, $dst_h); //create a black canvas, return image identifier
			imagecopyresampled ($dst_image, $src_image, 0, 0,  -$src_w * $adjusted_margin_ratio , 0,  $dst_w ,  $dst_h ,  $src_w * $ratio_container_scale,  $src_h );
			imagejpeg($dst_image, MEDIA_U.$i.'new.jpeg', 100);
		}
	}
	*/
	
	
?>