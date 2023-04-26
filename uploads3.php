
<?php
include("includes/config.php");
// Check if form was submitted
if(isset($_POST['submit'])) {

	// Configure upload directory and allowed file types

	$allowed_types = array('jpg', 'png', 'jpeg', 'gif', 'mp4');
	
	// Define maxsize for files i.e 2MB
	$maxsize = 2 * 1024 * 1024;
    
   $countgif =  count(array_filter($_FILES['files']['name']));
   
	// Checks if user sent an empty form
	if(!empty(array_filter($_FILES['files']['name']))) {
        $renamefile = $_POST["renamefile"];
		// Loop through each file in files[] array
		foreach ($_FILES['files']['tmp_name'] as $key => $value) {
			
			$file_tmpname = $_FILES['files']['tmp_name'][$key];
			$filename = $_FILES['files']['name'][$key];
			$file_size = $_FILES['files']['size'][$key];
			$file_ext = pathinfo($filename, PATHINFO_EXTENSION);

          // Set upload file path
          $tmp = $countgif;
          if($renamefile != null){
            $file_name =   $renamefile.$tmp.$filename;
            $countgif--;
          }else{
            $file_name = $filename ;  
            }

			// Check file type is allowed or not
			if(in_array(strtolower($file_ext), $allowed_types)) {

				
                    $file_Path = __DIR__ . '/upload/'. $file_name;
                    
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
                        } catch (Aws\S3\Exception\S3Exception $e) {
                            $status = false;
                            echo "There was an error uploading the file.\n";
                            echo $e->getMessage();
                        }  
                        //  echo "<script> location.href='list.php'; </script>";
                    //echo "file uploaded successfully";
                     
                        }

					else {		
                        $status = false;			
						echo "Error uploading {$file_name} <br />";
					}
				
			}
			else {
				$status = false;
				// If file extension not valid
				echo "Error uploading {$file_name} ";
				echo "({$file_ext} file type is not allowed)<br / >";
              echo "file upload error";
			}
		}
	}
	else {
		
		// If no files selected
		echo "No files selected.";
	}


    /// upload image in img dir //////

    // Check if file was uploaded without errors
    if(isset($_FILES["uploadfile"]) && $_FILES["uploadfile"]["error"] == 0){
        $allowed = array("jpg" => "image/jpg", "jpeg" => "image/jpeg", "gif" => "image/gif", "png"          => "image/png", "mp4"=> "video/mp4");
        $renamefile = $_POST["rename_file"];
        if($renamefile != null){
            //   $tmp = rand(1,9);
            $filename =   $renamefile.$_FILES["uploadfile"]["name"];
        }else{
            $filename = $_FILES["uploadfile"]["name"];  
        }
        $filetype = $_FILES["uploadfile"]["type"];
        $filesize = $_FILES["uploadfile"]["size"];

        // Validate file extension
        $ext = pathinfo($filename, PATHINFO_EXTENSION);

        if(!array_key_exists($ext, $allowed)) die("Error: Please select a valid file format.");
        // Validate file size - 10MB maximum
        $maxsize = 10 * 1024 * 1024;
        if($filesize > $maxsize) die("Error: File size is larger than the allowed limit.");
            // Validate type of the file
            if(in_array($filetype, $allowed)){

                    if($ext == 'jpg' || $ext == 'png'|| $ext == 'jpeg'){
                        $path = 'Img_dir';
                    }else{
                        $path = 'Video_dir';
                    }
                if(move_uploaded_file($_FILES["uploadfile"]["tmp_name"], "upload/" . $filename)){
                    $bucket = 'animal-drawing';
                    $file_Path = __DIR__ . '/upload/'. $filename;
                    
                    $key = basename($file_Path);

                    try {
                        $result = $s3Client->putObject([
                        'Bucket' => $bucket,
                        'Key'    => $path. '/' .$key,
                        'Body'   => fopen($file_Path, 'r'),
                        'ACL'    => 'public-read', // make file 'public'
                        ]);
                        $status = true;
                    } catch (Aws\S3\Exception\S3Exception $e) {
                        echo "There was an error uploading the file.\n";
                        echo $e->getMessage();
                        $status = false;
                    }  
                    //    echo "<script> location.href='list.php'; </script>";
                    
                }else{
                    $status = false;
                    echo "File is not uploaded";
                }
        } else{
            echo "Error: There was a problem uploading your file. Please try again."; 
            $status = false;
        }
    } else{
        $status = false;
        echo "Error: " . $_FILES["uploadfile"]["error"];
    }

    if($status == true){
        echo "<script> location.href='list.php'; </script>";
    }
}

?>
