<?php

/**
 * make thumb image
 * 
 * @param <string> source folder
 * @param <string> filename
 * @param <float> width
 * @param <float> height
 * @return void
 * 
 * @author: Dheeraj Singh
 */
function makeThumbnails($updir, $img, $width="100", $height="100")
{
    $thumbnail_width = $width;
    $thumbnail_height = $height;
    // $thumb_beforeword = "thumbs/";
    $updir = $updir.'/';
    $dest_folder = $updir . 'thumbs/';
    if (!file_exists($dest_folder)) {
		mkdir($dest_folder, 0777);
		chmod($dest_folder, 0777);
	}
    $arr_image_details = getimagesize("$updir" . "$img"); // pass id to thumb name
    $original_width = $arr_image_details[0];
    $original_height = $arr_image_details[1];
    if ($original_width > $original_height) {
        $new_width = $thumbnail_width;
        $new_height = intval($original_height * $new_width / $original_width);
    } else {
        $new_height = $thumbnail_height;
        $new_width = intval($original_width * $new_height / $original_height);
    }
    $dest_x = intval(($thumbnail_width - $new_width) / 2);
    $dest_y = intval(($thumbnail_height - $new_height) / 2);
    if ($arr_image_details[2] == IMAGETYPE_GIF) {
        $imgt = "ImageGIF";
        $imgcreatefrom = "ImageCreateFromGIF";
    }
    if ($arr_image_details[2] == IMAGETYPE_JPEG) {
        $imgt = "ImageJPEG";
        $imgcreatefrom = "ImageCreateFromJPEG";
    }
    if ($arr_image_details[2] == IMAGETYPE_PNG) {
        $imgt = "ImagePNG";
        $imgcreatefrom = "ImageCreateFromPNG";
    }
    // die("$updir" . "$thumb_beforeword" . "$img");
    if ($imgt) {
        $old_image = $imgcreatefrom("$updir" . "$img");
        $new_image = imagecreatetruecolor($thumbnail_width, $thumbnail_height);
        imagecopyresized($new_image, $old_image, $dest_x, $dest_y, 0, 0, $new_width, $new_height, $original_width, $original_height);
        $imgt($new_image, $dest_folder . $img);
    }
}


/**
 * create thumbs
 * ! depricaited 
 */ 
function makeThumbnails1($img, $updir, $width="250", $height="250", $thumb='thumb_')
{
    $thumbnail_width = $width;
    $thumbnail_height = $height;
    $thumb_beforeword = "thumb";
    // $updir = $updir.'/';
    $arr_image_details = getimagesize("$updir".'/'."$img"); // pass id to thumb name
    $original_width = $arr_image_details[0];
    $original_height = $arr_image_details[1];
    if ($original_width > $original_height) {
        $new_width = $thumbnail_width;
        $new_height = intval($original_height * $new_width / $original_width);
    } else {
        $new_height = $thumbnail_height;
        $new_width = intval($original_width * $new_height / $original_height);
    }
    $dest_x = intval(($thumbnail_width - $new_width) / 2);
    $dest_y = intval(($thumbnail_height - $new_height) / 2);
    if ($arr_image_details[2] == IMAGETYPE_GIF) {
        $imgt = "ImageGIF";
        $imgcreatefrom = "ImageCreateFromGIF";
    }
    if ($arr_image_details[2] == IMAGETYPE_JPEG) {
        $imgt = "ImageJPEG";
        $imgcreatefrom = "ImageCreateFromJPEG";
    }
    if ($arr_image_details[2] == IMAGETYPE_PNG) {
        $imgt = "ImagePNG";
        $imgcreatefrom = "ImageCreateFromPNG";
    }
    if ($imgt) {
        $old_image = $imgcreatefrom("$updir".'/'."$img");
        $new_image = imagecreatetruecolor($thumbnail_width, $thumbnail_height);
        // echo basename($old_image); echo "<br>";
        // echo basename($new_image); echo "<br>";
        echo "$updir" . "$img" . $thumb;
        imagecopyresized($new_image, $old_image, $dest_x, $dest_y, 0, 0, $new_width, $new_height, $original_width, $original_height);
        $imgt($new_image, "$updir" . $thumb . "$img");
        die;
    }
}

/**
 * resize and create thumbs
 * 
 * @param <string> filename
 * @param <float> width
 * @param <float> height
 * @param <string> source folder
 * @param <string> destination folder
 * @return void
 * 
 * @author: Dheeraj Singh
 */ 
function saveResizedImage($file_name, $width, $height, $source_folder, $destination_folder) {
	
	if (!file_exists($destination_folder)) {
		mkdir($destination_folder, 0777);
		chmod($destination_folder, 0777);
	}
	
	$source_path=$source_folder."/".$file_name;
	$destination_path=$destination_folder."/".$file_name;
	$ext=explode(".",$file_name);
	
	if($ext[1]=="gif" or $ext[1]=="GIF") {
		$mainimage=imagecreatefromgif($source_path);
	}
	elseif($ext[1]=="png" or $ext[1]=="PNG") {
		$mainimage=imagecreatefrompng($source_path);
	}
	elseif($ext[1]=="jpg" or $ext[1]=="JPG") {
		$mainimage=imagecreatefromjpeg($source_path);
	}
	$mainwidth=imagesx($mainimage);
	$mainheight=imagesy($mainimage);
	
	if($mainwidth<=$width and $mainheight<=$height) {
		$thumbleheight=$mainheight;
		$thumblewidth=$mainwidth;	
	}
	else {
		if($mainwidth>$width) {
			$thumblewidth=$width;
			$thumbleheight=(($width/$mainwidth)*$mainheight);	
			if ($thumbleheight > $height) {
				$thumbleheight=$height;
				$thumblewidth=(($height/$mainheight)*$mainwidth);	
			}
		}
		else if($mainheight>$height) {
			$thumbleheight=$height;
			$thumblewidth=(($height/$mainheight)*$mainwidth);	
			if ($thumblewidth > $width) {
				$thumblewidth=$width;
				$thumbleheight=(($width/$mainwidth)*$mainheight);	
			}
		}
	}
	
	$thumbleimage=imagecreate($thumblewidth,$thumbleheight);
	$thumbleimage=@imagecreatetruecolor($thumblewidth,$thumbleheight);
	$my_temp_file= ImageCopyResampled($thumbleimage,$mainimage,0,0,0,0,$thumblewidth,$thumbleheight,$mainwidth,$mainheight);
	imagejpeg($thumbleimage,$destination_path,100);
	imagedestroy($thumbleimage);
	imagedestroy($mainimage);
}

/**
 * get extension of a file
 * 
 * @param <string> filename
 * @return <string> file extension
 * 
 * @author: Dheeraj Singh
 */ 
function fileExt($file_name) {
	$path_parts = pathinfo($file_name);
	$ext = strtolower($path_parts["extension"]);
	return $ext;
}

/**
 * upload file
 * 
 * @param <string> new file name
 * @param <string> image
 * @param <string> folder name
 * @return <string> uploaded image name
 * 
 * @author: Dheeraj Singh
 */ 
function uploadFile($newname,$image,$folder) {
	$folder=SITE_DIR_PATH."/".$folder;
	if (!file_exists($folder)) {
		mkdir($folder, 0777);
		chmod($folder, 0777);
	}
	if($_FILES[$image]['name']!="") {
		$extension = fileExt($_FILES[$image]['name']);
		$image_name=$newname.".".$extension;
		$uploadedfile=$_FILES[$image]['tmp_name'];
		$tmp_imagename=$_FILES[$image]['name'];
		if($tmp_imagename<>"" && $uploadedfile<>"" && $image_name<>"") {		
			move_uploaded_file($uploadedfile, $folder."/".$image_name);
		}
	}
	return $image_name;
}

/**
 * update table
 * 
 * @param <string> table name
 * @param <string> condition (optional)
 * @return <bool> boolean
 * 
 * @author: Dheeraj Singh
 */ 
function updateTable($tableName,$cond="") {
	// write your db update query here
}

/**
 * update table
 * 
 * @param <string> table name
 * @param <string> condition (optional)
 * @param <string> field names (default all)
 * @return <array> result
 * 
 * @author: Dheeraj Singh
 */
function getResult($tableName, $cond="", $fields="*") {
	// write your db query for fetching data 
}

/**
 * strip tags
 * 
 * @param <string> var
 * @return <string> string
 * 
 * @author: Dheeraj Singh
 */ 
function ms_htmlentities_decode($var) {
	return is_array($var) ? array_map('ms_htmlentities_decode', $var) : stripslashes(html_entity_decode(trim($var),ENT_QUOTES));
}

/**
 * uplaod video
 * 
 * @param <string> video input field
 * @param <string> destination folder name
 * @return <string> new file name
 * 
 * @author: Dheeraj Singh
 */
function uploadVideo($filename, $image,$folder) {
	if($_FILES[$image]['name']!=""){ 
		$extension = fileExt($_FILES[$image]['name']);
		$extension = strtolower($extension);
		$newname = $filename.date('i_s');
		$uploadedfile = $_FILES[$image]['tmp_name'];
		$tmp_imagename = $_FILES[$image]['name'];

		if($extension=="mov" || $extension=="mpg" || $extension=="mpeg" || $extension=="wmv" || $extension=="aac" || $extension=="mp4" || $extension=="mp3" || $extension=="dat"){
			
			$image_name=$newname.".flv";	
			$uploadpath=SITE_DIR_PATH."/upload/".$image_name;
			// `/usr/local/bin/ffmpeg -i $uploadedfile -ar 22050 -s 400x200 $uploadpath`;
            $command = `/usr/local/bin/ffmpeg -i $uploadedfile -ar 22050 -s 400x200 $uploadpath`;
            system($command);	
		}
	}

	return $image_name;
}

?>