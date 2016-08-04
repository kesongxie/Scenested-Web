<?php
	// Adam Khoury PHP Image Function Library 1.0
	// Function for resizing any jpg, gif, or png image files
	//the $newcopy contains the path you want to save your resized image
	function fixImageOrientation(&$src_image, $fileTmpName){
		$exif = @exif_read_data($fileTmpName);
		//fix the image orientation
		//see http://sylvana.net/jpegcrop/exif_orientation.html
		//var_dump($exif['Orientation']);
		
		if(!empty($exif['Orientation'])) {
			switch($exif['Orientation']) {
				case 8:
					$src_image = imagerotate($src_image, 90, 0);
					break;
				case 3:
					$src_image = imagerotate($src_image, 180, 0);
					break;
				case 6:
					$src_image = imagerotate($src_image, -90, 0);
					break;
			}
		}
		
	}
	
	
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
		fixImageOrientation($img, $target);
		$tci = imagecreatetruecolor($w, $h);
		// imagecopyresampled(dst_img, src_img, dst_x, dst_y, src_x, src_y, dst_w, dst_h, src_w, src_h)
		imagecopyresampled($tci, $img, 0, 0, 0, 0, $w, $h, $w_orig, $h_orig);
		imagejpeg($tci, $newcopy, 80);
	}

	//extend the libarary to add crop image
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
		
		
		
		//fix the image orientation
		fixImageOrientation($src_image, $file['tmp_name']);
		
		$dst_image = imagecreatetruecolor($dst_w, $dst_h); //create a black canvas, return image identifier
		imagecopyresampled ($dst_image, $src_image, 0, 0, $src_w * $adjusted_ratio_width, $src_h * $adjusted_ratio_height , $dst_w ,  $dst_h ,  $src_w * $image_container_scale_width ,  $src_h * $image_container_scale_height);
		imagejpeg($dst_image, $newcopy, 100);
	}
		
		


?>