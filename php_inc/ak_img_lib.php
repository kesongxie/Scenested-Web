<?php
	// Adam Khoury PHP Image Function Library 1.0
	// Function for resizing any jpg, gif, or png image files
	//the $newcopy contains the path you want to save your resized image
	function ak_img_resize($target, $newcopy, $w, $h, $ext) {
		list($w_orig, $h_orig) = getimagesize($target);
		$scale_ratio = $w_orig / $h_orig;
		if (($w / $h) > $scale_ratio) {
			   $w = $h * $scale_ratio;
		} else {
			   $h = $w / $scale_ratio;
		}
		$img = "";
		$ext = strtolower($ext);
		if ($ext == "gif"){ 
		  $img = imagecreatefromgif($target);
		} else if($ext =="png"){ 
		  $img = imagecreatefrompng($target);
		} else { 
		  $img = imagecreatefromjpeg($target);
		}
		$tci = imagecreatetruecolor($w, $h);
		// imagecopyresampled(dst_img, src_img, dst_x, dst_y, src_x, src_y, dst_w, dst_h, src_w, src_h)
		imagecopyresampled($tci, $img, 0, 0, 0, 0, $w, $h, $w_orig, $h_orig);
		imagejpeg($tci, $newcopy, 80);
	}

	//extend the libarary to add crop image
	// function crop_image($target, $newcopy, $w_desc, $h_desc, $x_position_ratio, $y_position_ratio, $ext, $crop_square = true, $cropped_aspect_ratio){
// 		list($w_orig, $h_orig) = getimagesize($target);
// 		$src_y = $h_orig * $y_position_ratio;
// 		$src_x = $w_orig * $x_position_ratio;
// 		$img = "";
// 		$ext = strtolower($ext);
// 		if ($ext == "gif"){ 
// 		  $img = imagecreatefromgif($target);
// 		} else if($ext =="png"){ 
// 		  $img = imagecreatefrompng($target);
// 		} else { 
// 		  $img = imagecreatefromjpeg($target);
// 		}
// 		$tci = imagecreatetruecolor($w_desc, $h_desc);
// 		if($crop_square){
// 			if($h_orig >= $w_orig){
// 				//portrain
// 				imagecopyresampled($tci, $img, 0, 0, $src_x, $src_y, $w_desc, $h_desc, $w_orig, $w_orig);
// 			}else{
// 				imagecopyresampled($tci, $img, 0, 0, $src_x, $src_y, $w_desc, $h_desc, $h_orig, $h_orig);
// 			}   
// 		}else{
// 			//vertical crop
// 			imagecopyresampled($tci, $img, 0, 0, $src_x, $src_y, $w_desc, $h_desc, $w_orig, $w_orig * $cropped_aspect_ratio );
// 		}
// 	
// 		imagejpeg($tci, $newcopy, 80);
// 	}

	function crop_upload_file($file, $newcopy, $ratio_scale_assoc, $dst_w, $dst_h  ){
		$image_container_scale_width = $ratio_scale_assoc['image_container_scale_width'];
		$image_container_scale_height = $ratio_scale_assoc['image_container_scale_height'];
		$adjusted_ratio_width = $ratio_scale_assoc['adjusted_ratio_width'];
		$adjusted_ratio_height = $ratio_scale_assoc['adjusted_ratio_height'];
		list($src_w, $src_h, $extension) = getimagesize($file['tmp_name']);
		$extension = getMediaFileExtension($file);
		if ($extension == "gif"){ 
		  $src_image = imagecreatefromgif($file['tmp_name']);
		} else if($extension == "png"){ 
		  $src_image = imagecreatefrompng($file['tmp_name']);
		} else { 
		  $src_image = imagecreatefromjpeg($file['tmp_name']);
		}
		$dst_image = imagecreatetruecolor($dst_w, $dst_h); //create a black canvas, return image identifier
		imagecopyresampled ($dst_image, $src_image, 0, 0, $src_w * $adjusted_ratio_width, $src_h * $adjusted_ratio_height , $dst_w ,  $dst_h ,  $src_w * $image_container_scale_width ,  $src_h * $image_container_scale_height);
		imagejpeg($dst_image, $newcopy, 100);
	}
		
		


?>