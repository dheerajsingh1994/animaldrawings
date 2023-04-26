
<?php
include("includes/config.php");
// Check if form was submitted
if(isset($_POST['submit'])) {

    $allowed_types = array('jpg', 'png', 'jpeg', 'gif', 'mp4'); // Configure upload directory and allowed file types
    $maxsize = 2 * 1024 * 1024; // Define maxsize for files i.e 2MB
    $countgif =  count(array_filter($_FILES['files']['name']));
    $serverfolder = 'upload'; // folder name for server
    $serverthumbfolder = 'thumbs'; // thumbs folder name [NOTE: this folder will be creaated within the main folder] 

    $videofolder = 'video'; // folder name for video
    $videothumbfolder = 'thumbnail'; // thumbs folder name [NOTE: this folder will be creaated within the main folder] 

    $bucket = 'animal-drawing';

    // Checks if user sent an empty form
	if(!empty(array_filter($_FILES['files']['name']))) {
        $renamefile = !empty($_POST["renamefile"]) ? $_POST["renamefile"] : "";
        echo "No of files uploading: " . $countgif; echo "<br>";
		// Loop through each file in files[] array
		foreach ($_FILES['files']['tmp_name'] as $key => $value) {
			
			$file_tmpname = $_FILES['files']['tmp_name'][$key];
			$filename = $_FILES['files']['name'][$key];
			$file_size = $_FILES['files']['size'][$key];
			$file_ext = pathinfo($filename, PATHINFO_EXTENSION);
            
            // Set upload file path
            $tmp = $countgif;
            if($renamefile != null){
                $renamefile = strtolower(str_replace(" ", "-", $renamefile));
                $file_name =   $renamefile.$tmp . "." . $file_ext;
                $countgif--;
                echo "<br>"; echo "file renamed to: " . $file_name; echo "<br>";
            }else{
                $file_name = $filename;
                echo "<br>"; echo "file was not renamed. Name: " . $file_name; echo "<br>";
            }

			// Check file type is allowed or not
			if(in_array(strtolower($file_ext), $allowed_types)) {
                $file_Path = __DIR__ . '/'.$serverfolder.'/'. $file_name;
                $key = basename($file_Path);
                $path = 'GifImg_dir'; 
                
                if(move_uploaded_file($file_tmpname, $file_Path)){
                    $bucket = 'animal-drawing';
                    //  $file_Path = __DIR__ . '/upload/'. $filename;
                    $key = basename($file_Path);
                    try {
                        $result = $s3Client->putObject([
                            'Bucket' => $bucket,
                            'Key'    => $path. '/' .$key,
                            'Body'   => fopen($file_Path, 'r'),
                            'ACL'    => 'public-read', // make file 'public'
                        ]);
                        $status = true;
                        echo "upload to AWS bucket"; echo "<br>";
                        echo "removing this file from server"; echo "<br>";
                        $file = SITE_DIR_PATH."/".$serverfolder."/".$file_name;
                        if(file_exists($file)) {
                            unlink($file);
                        }
                        echo "gif file: ".$file_name. " removed from server";echo "<br>"; 
                    } catch (Aws\S3\Exception\S3Exception $e) {
                        $status = false;
                        echo "There was an error uploading the file.\n";
                        echo $e->getMessage();
                    }     
                } else {
                    $status = false;			
                    echo "Error uploading {$file_name} <br />";
                }
			
			} else {
				$status = false;
				// If file extension not valid
				echo "Error uploading {$file_name} ";
				echo "({$file_ext} file type is not allowed)<br / >";
                echo "file upload error";
			}
		}
        die("All Finished");
	}
    // else {
	// 	// If no files
	// 	echo "No files selected.";
	// }


    // Check if file was uploaded without errors
    if(isset($_FILES["uploadfile"]) && $_FILES["uploadfile"]["error"] == 0){

        $allowed = array("jpg" => "image/jpg", "jpeg" => "image/jpeg", "gif" => "image/gif", "png" => "image/png", "mp4"=> "video/mp4");

        $uploadedfile = !empty($_FILES["uploadfile"]['name']) ? $_FILES["uploadfile"]['name'] : '';
        $renamefile = !empty($_POST["rename_file"]) ? $_POST["rename_file"] : '' ;

        echo "<br>";
        if($renamefile != null){
            $itemN = str_replace(" ", "-", $renamefile);
			$filename = $itemN.date("i_s");
        }else{
            // $filename = $_FILES["uploadfile"]["name"];
            $filename = time().date("i_s");
        }

        $filetype = $_FILES["uploadfile"]["type"];
        $filesize = $_FILES["uploadfile"]["size"];

        // Validate file extension
        $ext = pathinfo($_FILES["uploadfile"]['name'], PATHINFO_EXTENSION);

        if(!array_key_exists($ext, $allowed)) die("Error: Please select a valid file format.");

        // Validate file size - 10MB maximum
        $maxsize = 10 * 1024 * 1024;
        if($filesize > $maxsize) die("Error: File size is larger than the allowed limit.");
        // Validate type of the file
        if(in_array($filetype, $allowed)){
            if($ext == 'jpg' || $ext == 'png'|| $ext == 'jpeg'){
                $path = 'Img_dir';
            } else {
                $path = 'Video_dir';
            }

            if($path == "Img_dir") {
                $img = uploadFile($filename, "uploadfile" , $serverfolder);
                echo "image uploaded: " . $img; echo "<br><br>";
                if($img){
                    $existsfile = $img;

                    $width = "250";
                    $height = "250";
                    $source_folder = SITE_DIR_PATH.'/'.$serverfolder;
                    makeThumbnails($source_folder, $img, $width, $height); // img and source folder are mandatory [default 100*100]

                    try {
                        echo "uploading image to s3";echo "<br>"; 
                        $filePathForAWS = __DIR__ . '/'.$serverfolder.'/'. $img;
                        $key = basename($filePathForAWS);
                        $result = $s3Client->putObject([
                            'Bucket' => $bucket,
                            'Key'    => $path. '/' .$key,
                            'Body'   => fopen($filePathForAWS, 'r'),
                            'ACL'    => 'public-read', // make file 'public'
                        ]);

                        $filePathForAWSThumb = __DIR__ . '/'.$serverfolder.'/thumbs/'.$existsfile;
                        $keyThumb = basename($filePathForAWSThumb);
                        $result = $s3Client->putObject([
                            'Bucket' => $bucket,
                            'Key'    => $path. '/thumbs/' .$keyThumb,
                            'ACL'    => 'public-read', // make file 'public'
                            'SourceFile' => $filePathForAWSThumb
                        ]);
                        $status = true;

                        // delete image from server
                        echo "uploaded on s3 successfull";echo "<br>"; 
                        if($existsfile!="") {
                            $file = SITE_DIR_PATH."/".$serverfolder."/".$existsfile;
                            if(file_exists($file)) {
                                unlink($file);
                            }
                            
                            $file_thumb = SITE_DIR_PATH."/".$serverfolder."/thumbs/".$existsfile;
                            if(file_exists($file_thumb)) {
                                unlink($file_thumb);
                            }
                            echo "removed from server";echo "<br>"; 

                        }
                        die();
                    } catch (Aws\S3\Exception\S3Exception $e) {
                        echo "There was an error uploading the file.\n";
                        echo $e->getMessage();die;
                        $status = false;
                    }                    
                } else {
                    $status = false;
                    echo "File is not uploaded";
                }
            } 
            else if($path == "Video_dir") {
                echo "rename to: " . $filename; echo "<br>";
                echo "video extension: " . $ext; echo "<br>";
                $video_name = $filename.".".$ext;
                echo "final video name: " . $video_name; echo "<br><br>";

                $videoThumbnailfile = !empty($_FILES["thumbnail"]['name']) ? $_FILES["thumbnail"]['name'] : '';
                if($videoThumbnailfile != null){
                    $thumb_exten = fileExt($videoThumbnailfile);
                    $itemN = str_replace(" ", "-", $renamefile);
                    $thumbnailName = $itemN.date("i_s");
                    $thumbnailNewName = $thumbnailName.'.'.$thumb_exten;
                    echo "thumbnail image: " . $videoThumbnailfile; echo "<br>";
                    echo "thumbnail extension: " . $thumb_exten; echo "<br>";
                } else {
                    $thumbnailName = $filename;
                    $thumbnailNewName = $thumbnailName.'.jpg';
                }
                echo "video thumbnail name: " . $thumbnailNewName; echo "<br>";
                $video_item = move_uploaded_file($_FILES["uploadfile"]["tmp_name"], $videofolder."/" . $video_name);
                if($videoThumbnailfile) {
                    $folder=SITE_DIR_PATH."/".$videofolder."/".$videothumbfolder;
                    if (!file_exists($folder)) {
                        mkdir($folder, 0777);
                        chmod($folder, 0777);
                    }
                    $thumbnail = move_uploaded_file($_FILES["thumbnail"]["tmp_name"], $videofolder."/".$videothumbfolder ."/". $thumbnailNewName);
                }
                // $thumbnail = uploadFile($thumbnailNewName, "thumbnail" , '/'.$videofolder.'/'.$videothumbfolder);
                try {
                    $filePathForVideoAWS = __DIR__ . '/'.$videofolder.'/'. $video_name;
                    $key = basename($filePathForVideoAWS);
                    $result = $s3Client->putObject([
                        'Bucket' => $bucket,
                        'Key'    => $path. '/' .$key,
                        'Body'   => fopen($filePathForVideoAWS, 'r'),
                        'ACL'    => 'public-read', // make file 'public'
                    ]);
                
                    if($videoThumbnailfile) {
                        $filePathForVideoThumbAWS = __DIR__ . '/'.$videofolder.'/'.$videothumbfolder.'/'. $thumbnailNewName;
                        $key = basename($filePathForVideoThumbAWS);
                        $result = $s3Client->putObject([
                            'Bucket' => $bucket,
                            'Key'    => $path. '/'.$videothumbfolder.'/' .$key,
                            'Body'   => fopen($filePathForVideoThumbAWS, 'r'),
                            'ACL'    => 'public-read', // make file 'public'
                        ]);
                    }
                    echo "video uploaded to s3";
                    $file = SITE_DIR_PATH."/".$videofolder."/".$video_name;
                    if(file_exists($file)) {
                        unlink($file);
                    }
                
                    $fileThumbnail = SITE_DIR_PATH."/".$videofolder."/".$videothumbfolder."/".$thumbnailNewName;
                    if(file_exists($fileThumbnail)) {
                        unlink($fileThumbnail);
                    }
                    echo "<br>"; die("video files deleted from server");

                } catch (Aws\S3\Exception\S3Exception $e) {
                    echo "There was an error uploading the file.\n";
                    echo $e->getMessage();die;
                    $status = false;
                } catch (\Exception $exp) {
                    echo $exp->getMessage();
                    die;
                }
            
            }
        } else {
            echo "Error: There was a problem uploading your file. Please try again."; 
            $status = false;
        }
    }
    else {
        $status = false;
        // echo "Error: " . $_FILES["uploadfile"]["error"];
    }

    if($status == true){
        echo "<script> location.href='d-list.php'; </script>";
    }
}

?>
