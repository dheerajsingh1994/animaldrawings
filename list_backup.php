<?php 
try{
    include("includes/config.php");
    //global arrays declared for being used later while sending json as response
    $Materialdata= Array();
    $Videodata= Array();
    
//Data for MaterialData starts
   foreach ($contents['Contents'] as $content) {
       //looping through all the items in s3 bucket
        $cmd = $s3Client->getCommand('GetObject',[
        'Bucket' => BUCKET,
        'Key'=>$content['Key']
        ]);
        $request = $s3Client->createPresignedRequest($cmd, '+20 minutes');
        $presignedUrl = (string)$request->getUri();
        if(explode("?",substr($presignedUrl,51,53))[0]!="GifImg_dir/"&&explode("?",substr($presignedUrl,51,53))[0]!="Video_dir/"&&explode("?",substr($presignedUrl,51,53))[0]!="Img_dir/"){ 
            //for removing the directories in response as we want only files
            if(explode("/",explode("?", substr($presignedUrl,51,53))[0])[0]=="Img_dir"){
                //filtering the response as we want only the images here which are present in Img_dir
                $MaterialSubdata = array(
                "MaterialPreviewImageURL"=>$presignedUrl,
                "ArtBoardImgURL"=>$presignedUrl,
                "AnimalName"=> str_replace(".png","",explode("/",explode("?", substr($presignedUrl,51,53))[0])[1])
            );
            //pushing the array into the global array so that we can forward the data in the header as json
            array_push($Materialdata,$MaterialSubdata);    
            }
        }
    } 
    
//Data for MaterialData Ends

// //Data for GIFdata starts

    $gifArray = array();
    $gifArrayList = array();
    $GifData=array();
    foreach ($contents['Contents'] as $content) {
        $cmd = $s3Client->getCommand('GetObject',[
        'Bucket' => BUCKET,
        'Key'=>$content['Key']
        ]);
        $request = $s3Client->createPresignedRequest($cmd, '+20 minutes');
        $presignedUrl = (string)$request->getUri(); 
        if(explode("?",substr($presignedUrl,51,53))[0]!="GifImg_dir/"&&explode("?",substr($presignedUrl,51,53))[0]!="Video_dir/"&&explode("?",substr($presignedUrl,51,53))[0]!="Img_dir/")
        { 
            if(explode("/",explode("?", substr($presignedUrl,51,53))[0])[0]=="GifImg_dir"){
                array_push($gifArray,explode("-",explode("/",explode("?", substr($presignedUrl,51,53))[0])[1])[0]);
                array_push($gifArrayList,$presignedUrl);
            }
            
        }        
    }
    $gifArray = array_unique($gifArray); //animal distinct names
    foreach($gifArray as $gifElement){   //looping through each animal name
         $dec = array("AnimalName"=>$gifElement,"AnimalList"=>array());
        for($j=0;$j<count($gifArrayList);$j++){
            if($gifElement==explode("-",explode("/",explode("?", substr($gifArrayList[$j],51,53))[0])[1])[0]){ //if the animal name is same as the $gifElement(animal) push 
                                                                //else make a new array again by continuing
                array_push($dec["AnimalList"],array("GIF"=>$gifArrayList[$j]));    
            }
            else{
                continue;
            }
            
        }
        array_push($GifData,$dec);
    }
// //Data for GIFdata ends
//Data for Videodata starts
    foreach ($contents['Contents'] as $content) {
        $cmd = $s3Client->getCommand('GetObject',[
        'Bucket' => BUCKET,
        'Key'=>$content['Key']
        ]);
        $request = $s3Client->createPresignedRequest($cmd, '+20 minutes');
        $presignedUrl = (string)$request->getUri();
        if(explode("?",substr($presignedUrl,51,53))[0]!="GifImg_dir/"&&explode("?",substr($presignedUrl,51,53))[0]!="Video_dir/"&&explode("?",substr($presignedUrl,51,53))[0]!="Img_dir/"){ 
            if(explode("/",explode("?", substr($presignedUrl,51,53))[0])[0]=="Video_dir"){
                $VideoSubdata = array(
                    "AnimalName"=>str_replace(".mp4","",explode("/",explode("?", substr($presignedUrl,51,53))[0])[1]),
                    "PreviewImageURL"=> $presignedUrl,
                    "YTB_VideoURL"=> $presignedUrl
                );
                array_push($Videodata,$VideoSubdata);  
            }
        }
    }
//Data for Videodata ends

//sending response with json content type
    header('Content-Type: application/json; charset=utf-8');
    echo json_encode(array('MaterialListPreviewScreen'=>$Materialdata,
        "GIFScreen"=>$GifData,
        'VideoPreviewScreen'=>$Videodata)
    );
}
catch(Exception $Ex){
    echo $Ex;
}
    
?>
