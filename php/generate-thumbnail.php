<?php
 
// $video = $_FILES["uploadfile"]["name"];
 
$command = "/usr/local/bin/ffmpeg -i " . $uploadedfile . " -vf fps=1/60 thumbnail-%03d.png";
system($command);
 
echo "Thumbnail has been generated";