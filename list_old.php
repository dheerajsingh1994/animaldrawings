<?php 
try{
    include("includes/config.php");
    //global arrays declared for being used later while sending json as response
    $Materialdata= Array();
    $Videodata= Array();
    
    echo "<pre>";
    //Data for MaterialData starts
    // print_r($contents);
    
    // $cmd = $s3Client->getCommand('GetObject',[
    //     'Bucket' => BUCKET,
    // ]);

    // $request = $s3Client->createPresignedRequest($cmd, '+20 minutes');
    // $presignedUrl = (string)$request->getUri();
    // print_r($presignedUrl);
    // die();
    $imgMainUrl = ''; $imgThumbUrl = ''; $uniqueImgName = '';
    $MaterialSubdata = [];
    foreach ($contents['Contents'] as $key => $content) {
        // print_r($content);
        //looping through all the items in s3 bucket
        $cmd = $s3Client->getCommand('GetObject',[
            'Bucket' => BUCKET,
            'Key'=>$content['Key'],
            'Prefix' => 'thumbs',
            'Delimiter' => '/'
        ]);

        $request = $s3Client->createPresignedRequest($cmd, '+20 minutes');
        $presignedUrl = (string)$request->getUri();

        $directory = substr($presignedUrl,51,53);
        $newArry = explode("?", $directory)[0];
        // echo $newArry; echo "<br>";
        $ttt = explode("/", $newArry);
        // print_r($ttt); echo "<br>";
        
        if( $newArry != "GifImg_dir/"&& $newArry != "Video_dir/"&& $newArry != "Img_dir/"){ 
            //for removing the directories in response as we want only files
            if($ttt[0]=="Img_dir"){
                //filtering the response as we want only the images here which are present in Img_dir
                $imgName = str_replace(".png","",$ttt[1]);
                if($imgName != 'thumbs') {

                    // $MaterialSubdata = array(
                    //     "MaterialPreviewImageURL"=>$presignedUrl,
                    //     "ArtBoardImgURL"=>$presignedUrl,
                    //     "AnimalName"=> $imgName,
                    // );

                    foreach ($Materialdata as $key => $value) {
                        if(array_key_exists('AnimalName', $Materialdata) && strtolower($value['AnimalName']) == strtolower($imgName)) {
                            // $MaterialSubdata = array(
                            //     "MaterialPreviewImageURL"=>$presignedUrl,
                            //     "ArtBoardImgURL"=>$presignedUrl,
                            //     "AnimalName"=> $imgName,
                            // );
                        } else {
                            $MaterialSubdata = array(
                                "MaterialPreviewImageURL"=>$presignedUrl,
                                "ArtBoardImgURL"=>$presignedUrl,
                                "AnimalName"=> $imgName,
                            );
                        }
                    }

                }
                //pushing the array into the global array so that we can forward the data in the header as json
                array_push($Materialdata,$MaterialSubdata);    
                // foreach ($Materialdata as $key => $value) {
                //     echo strtolower($imgName); echo "<br>";
                //     echo strtolower($value['AnimalName']); echo "<br><br>";
                // }
            }
        }



        


        // $inner = explode('/', $presignedUrl);
        // echo "<br><br>"; print_r($inner);echo "<br>";
        
        // // $indexOfImgeName = ($inner[4] != "" && $inner[4] !== "thumbs") ? $inner[4] : $inner[5];
        // // $imageItem = explode("?", $indexOfImgeName);
        // // $imageName = $imageItem[0];
        // // if($uniqueImgName == ""){
        // //     $uniqueImgName = $imageName;
        // // }
        // // echo "Image Name: " . $imageName; echo "<br>";
        // // if(strtolower($uniqueImgName) == strtolower($imageName)) {
        // //     echo "Image: " . $imageName; echo "<br>";
        // //     echo "checked"."<br>";
        // // } else {
        // //     echo "Image: " . $imageName; echo "<br>";
        // //     echo "not checked"."<br>";
        // // }

        // // $imgMainUrl = ''; $imgThumbUrl = '';
        // if($inner[3] == "Img_dir") {
        //     $indexOfImgeName = ($inner[4] != "" && $inner[4] !== "thumbs") ? $inner[4] : $inner[5];
        //     $imageItem = explode("?", $indexOfImgeName);
        //     $imageName = $imageItem[0];

        //     if($uniqueImgName == ""){
        //         $uniqueImgName = $imageName;
        //     }

        //     $imgMainUrl = implode('/', $inner);
        //     $imgMainUrl = $presignedUrl;

        //     if($inner[4] == "thumbs") {
        //         echo "I am here"."<br>"; 
        //         unset($contents['content'][$key]);
        //         // echo "thumb exists"."<br>";
        //         // $imgThumbUrl = implode('/', $inner);
        //     } else {
        //         $imgThumbUrl = $imgMainUrl;
        //     }
            
        //     // echo "new"; echo "<br>";
        //     // print_r($imgMainUrl); echo "<br>";

        //     $MaterialSubdata = [];
        //     if($imageName != "") {
        //         // echo $imageName; echo "<br>";
        //         $MaterialSubdata = array(
        //             "MaterialPreviewImageURL" => $imgMainUrl,
        //             "ArtBoardImgURL" => $imgThumbUrl,
        //             "AnimalName"=> $imageName
        //         );
        //     }
        //     print_r($MaterialSubdata); echo "<br>";


        //     //pushing the array into the global array so that we can forward the data in the header as json
        //     array_push($Materialdata,$MaterialSubdata);
        // }


        // if(explode("?",substr($presignedUrl,51,53))[0]!="GifImg_dir/"&&explode("?",substr($presignedUrl,51,53))[0]!="Video_dir/"&&explode("?",substr($presignedUrl,51,53))[0]!="Img_dir/"){ 
        //     //for removing the directories in response as we want only files
        //     if(explode("/",explode("?", substr($presignedUrl,51,53))[0])[0]=="Img_dir"){
        //         // echo "presigned:- " . $presignedUrl; echo "<br><br>";
        //         // $inner = explode('/', $presignedUrl);
        //         // // print_r($inner);echo "<br>";
        //         // if($inner[4] != "thumbs") {
        //         //     $imgMainUrl = implode('/', $inner);
        //         //     $imgThumbUrl = $imgMainUrl;
        //         // }
        //         // echo "new"; echo "<br>";
        //         // print_r($imgMainUrl); echo "<br>";
        //         //filtering the response as we want only the images here which are present in Img_dir
        //         $MaterialSubdata = array(
        //             "MaterialPreviewImageURL"=>$presignedUrl,
        //             "ArtBoardImgURL"=>$presignedUrl,
        //             // "MaterialPreviewImageURL" => $imgMainUrl,
        //             // "ArtBoardImgURL" => $imgThumbUrl,
        //             "AnimalName"=> str_replace(".png","",explode("/",explode("?", substr($presignedUrl,51,53))[0])[1])
        //         );
        //         //pushing the array into the global array so that we can forward the data in the header as json
        //         array_push($Materialdata,$MaterialSubdata);    
        //     }
        // }
    } 
    // die('dfdfd');
    
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
