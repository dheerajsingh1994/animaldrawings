<?php 
try{
    include("includes/config.php");
    //global arrays declared for being used later while sending json as response
    $Materialdata= Array();
    $Videodata= Array();
    
//Data for MaterialData starts
// echo "<pre>";
// print_r($contents);
// die;
   foreach ($contents['Contents'] as $content) {
       //looping through all the items in s3 bucket
        $cmd = $s3Client->getCommand('GetObject',[
        'Bucket' => BUCKET,
        'Key'=>$content['Key']
        ]);
        $request = $s3Client->createPresignedRequest($cmd, '+20 minutes');
        $presignedUrl = (string)$request->getUri();
        // echo $presignedUrl; echo "<br><br>";
        if(explode("?",substr($presignedUrl,51,53))[0]!="GifImg_dir/"&&explode("?",substr($presignedUrl,51,53))[0]!="Video_dir/"&&explode("?",substr($presignedUrl,51,53))[0]!="Img_dir/"){ 
            //for removing the directories in response as we want only files
            if(explode("/",explode("?", substr($presignedUrl,51,53))[0])[0]=="Img_dir"){
                $rr = explode("/",explode("?", substr($presignedUrl,51,53))[0]);
                // echo "<pre>";
                $imagesItem = explode('Img_dir', $presignedUrl); //echo "<br>";
                // print_r($imagesItem); echo "<br>";
                // echo "<br>";
                $parentDir = $rr[0];
                $child = $rr[1];
                if($child !== "thumbs") {
                    // echo "Child:- " . $child; echo "<br>";
                    $thmPath = "/".$parentDir.'/thumbs/'.$child;
                    // echo "Thumb path:- " . $thmPath; echo "<br>";
                    $thmImg = $imagesItem[0].$parentDir.'/thumbs/'.$child;
                    // echo "Thumb Image:- " . $thmImg; echo "<br>";
                    $result = $s3Client->getCommand('GetObject', [
                        'Bucket' => BUCKET,
                        'Key'    => $parentDir. '/thumbs/' .$child
                    ]);
                    // print_r($result);

                    //The period of availability
                    $request = $s3Client->createPresignedRequest($cmd, '+10 minutes');

                    //Get the pre-signed URL
                    $signedUrl = (string) $request->getUri();
                    // echo 'Signed:-  ' . $signedUrl; echo "<br>";
                    // if(file_exists($thmPath)) {
                    //     echo "found"."<br>";
                    //     echo "thumb image: " . $thmPath;
                    // }
                    // die;
                    
                    $displyName = ucwords(str_ireplace(["-", "_", ".png", ".jpg", ".jpeg", ".gif", ".mp4"], [" ", "", "", "", "", "", ""], preg_replace('/\d/', '', str_replace(".png","",explode("/",explode("?", substr($presignedUrl,51,53))[0])[1]))));

                    //filtering the response as we want only the images here which are present in Img_dir
                    $MaterialSubdata = array(
                        "MaterialPreviewImageURL"=> $presignedUrl,
                        "ArtBoardImgURL"=> $thmImg,
                        // "AnimalName"=> str_replace(".png","",explode("/",explode("?", substr($presignedUrl,51,53))[0])[1]),
                        "AnimalName"=> $displyName
                    );
                    //pushing the array into the global array so that we can forward the data in the header as json
                    array_push($Materialdata,$MaterialSubdata);
                }
            }
        }
    }
    // echo "<pre>";
    // echo "<br><br>";
    // print_r($Materialdata); 
    // die('end'); 

//Data for MaterialData Ends

// //Data for GIFdata starts

    $gifArray = array();
    $gifArrayList = array();
    $GifData=array();
    // echo "<pre>";
    $arrProject = [];
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
    // die;
    // echo "<pre>"; print_r($gifArrayList); die;
    // echo "<pre>";
    $gifArray = array_unique($gifArray); 
    // echo "<pre>"; 
    // print_r($gifArrayList);//animal distinct names

    // echo "<br><br>";
    // $dec = [
    //     "AnimalName" => '',
    //     "AnimalList" => []
    // ];
    $dec= [];
    // echo '<pre>';
    foreach($gifArrayList as $key => $gifArrayItem){   //looping through each animal name
        $displyGifName = ucwords(str_ireplace(["-", "_", ".png", ".jpg", ".jpeg", ".gif", ".mp4"], [" ", "", "", "", "", "", ""], preg_replace('/\d/', '', str_replace(".png","",explode("/",explode("?", substr($gifArrayItem,51,53))[0])[1]))));

        if(array_key_exists($displyGifName, $GifData)) {
            $GifData[$displyGifName]['AnimalList'][] = $gifArrayItem;
        } else {
            $GifData[$displyGifName]['AnimalList'][] = $gifArrayItem;
        }
        // print_r($dec); echo '<br><br>';


        // print_r($GifData); echo '<br>';
        // $key = array_search($displyGifName, array_column($GifData, 'AnimalName'));
        // echo $key; echo "<br>";

        // if($key) {
        //     // $dec[$key]['AnimalList'][] = $gifArrayItem;
        //     array_push($dec[$key]['AnimalList'], $gifArrayItem);
        // } else {
        //     // echo 'Animal Name : ' . $displyGifName; echo "<br>";
        //     // echo 'Link : ' . $gifArrayItem; echo '<br><br>';
        //     // unset($dec['AnimalList']);
        //     $dec["AnimalName"] = $displyGifName;
        //     // $dec['AnimalList'][] = $gifArrayItem;
        //     array_push($dec['AnimalList'], $gifArrayItem);
        // }
        // array_push($GifData,$dec);
    }
    // print_r($GifData);echo '<br>';

    // die('GIF DEAD');



    // foreach($gifArray as $gifElement){   //looping through each animal name
    //     $displyGifName = strtolower(str_ireplace(["-", "_", ".png", ".jpg", ".jpeg", ".gif", ".mp4"], [" ", "", "", "", "", "", ""], preg_replace('/\d/', '', str_replace(".png","",$gifElement))));
    //     // echo $displyGifName ;echo "<br>";

    //     // $dec = array("AnimalName"=>$gifElement,"AnimalList"=>array());
    //     for($j=0;$j<count($gifArrayList);$j++){
    //         // die($gifArrayList[$j]);
    //         $pushGifName = strtolower(str_ireplace(["-", "_", ".png", ".jpg", ".jpeg", ".gif", ".mp4"], [" ", "", "", "", "", "", ""], preg_replace('/\d/', '', str_replace(".png","", explode("-",explode("/",explode("?", substr($gifArrayList[$j],51,53))[0])[1])[0]))));

    //         if(strtolower($displyGifName) == strtolower($pushGifName)){
    //             $key = array_search($pushGifName, array_column($dec, 'AnimalName'));
    //             echo $key; echo "<br>";
    
    //             if($key) {
    //                 // array_push($dec[$key]['AnimalList']['GIF'], $gifArrayList[$j]);
    //                 $dec[$key]['AnimalList'][] = $gifArrayList[$j];
    //             } else {
    //                 //else make a new array again by continuing
    //                 // $dec = [
    //                 //     "AnimalName" => $displyGifName
    //                 // ];
    
    //                 $dec["AnimalName"] = $displyGifName;
    //                 $dec['AnimalList'][] = $gifArrayList[$j];
    //             }

    //         }

    //         print_r($dec); echo "<br><br>";
    //     }
    //     array_push($GifData,$dec);
    // }
    // echo "<pre>";
    // print_r($dec);
    // die;
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
                $rr = explode("/",explode("?", substr($presignedUrl,51,53))[0]);
                // echo "<pre>";
                $imagesItem = explode('Video_dir', $presignedUrl); //echo "<br>";
                // print_r($rr); echo "<br>";
                // echo "<br>";
                $parentDir = $rr[0];
                $child = $rr[1];
                if($child !== "thumbnail") {
                    // $thmPath = "/".$parentDir.'/thumbnail/'.$child;
                    $thmbnailImg = $imagesItem[0].$parentDir.'/thumbnail/'.$child;
                    // if($child === "thumbnail") { 
                    //     $thmbnailImg = $imagesItem[0].$parentDir.'/thumbnail/'.$rr[2];
                    //     $displyVideoName = ucwords(str_ireplace(["-", "_", ".png", ".jpg", ".jpeg", ".gif", ".mp4"], [" ", "", "", "", "", "", ""], preg_replace('/\d/', '', str_replace(".mp4","",explode("/",explode("?", substr($presignedUrl,51,53))[0])[2]))));
                    // } else {
                    //     $thmbnailImg = '';
                    //     $displyVideoName = ucwords(str_ireplace(["-", "_", ".png", ".jpg", ".jpeg", ".gif", ".mp4"], [" ", "", "", "", "", "", ""], preg_replace('/\d/', '', str_replace(".mp4","",explode("/",explode("?", substr($presignedUrl,51,53))[0])[1]))));
                    // }
                    
                    $displyVideoName = ucwords(str_ireplace(["-", "_", ".png", ".jpg", ".jpeg", ".gif", ".mp4"], [" ", "", "", "", "", "", ""], preg_replace('/\d/', '', str_replace(".mp4","",explode("/",explode("?", substr($presignedUrl,51,53))[0])[1]))));

                    //filtering the response as we want only the images here which are present in Img_dir
                    $VideoSubdata = array(
                        "AnimalName"=> $displyVideoName,
                        // "thumnail"=> $thmbnailImg,
                        "PreviewImageURL"=> $presignedUrl,
                        "YTB_VideoURL"=> $presignedUrl
                    );
                    array_push($Videodata,$VideoSubdata);  
                }



                // $VideoSubdata = array(
                //     "AnimalName"=>str_replace(".mp4","",explode("/",explode("?", substr($presignedUrl,51,53))[0])[1]),
                //     "PreviewImageURL"=> $presignedUrl,
                //     "YTB_VideoURL"=> $presignedUrl
                // );
                // array_push($Videodata,$VideoSubdata);  
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
