<?php 
try{
    include("includes/config.php");
    //global arrays declared for being used later while sending json as response
    $Materialdata= Array();
    $Videodata= Array();
    
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
                $rr = explode("/",explode("?", substr($presignedUrl,51,53))[0]);
                $imagesItem = explode('Img_dir', $presignedUrl);

                $parentDir = $rr[0];
                $child = $rr[1];
                if($child !== "thumbs") {
                    $thmPath = "/".$parentDir.'/thumbs/'.$child;
                    $thmImg = $imagesItem[0].$parentDir.'/thumbs/'.$child;
                    $result = $s3Client->getCommand('GetObject', [
                        'Bucket' => BUCKET,
                        'Key'    => $parentDir. '/thumbs/' .$child
                    ]);

                    //The period of availability
                    $request = $s3Client->createPresignedRequest($cmd, '+10 minutes');

                    //Get the pre-signed URL
                    $signedUrl = (string) $request->getUri();
                    
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
    //Data for MaterialData Ends


    // Data for GIFdata starts
    $gifArray = array();
    $gifArrayList = array();
    $GifData=array();

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

    $gifArray = array_unique($gifArray); 

    $dec= [];
    foreach($gifArrayList as $key => $gifArrayItem){   //looping through each animal name
        $displyGifName = ucwords(str_ireplace(["-", "_", ".png", ".jpg", ".jpeg", ".gif", ".mp4"], [" ", "", "", "", "", "", ""], preg_replace('/\d/', '', str_replace(".png","",explode("/",explode("?", substr($gifArrayItem,51,53))[0])[1]))));

        // if(array_key_exists($displyGifName, $GifData)) {
        //     $GifData[$key]['AnimalName'] = $displyGifName;
        //     $GifData[$key]['AnimalList'][] = $gifArrayItem;
        // } else {
        //     $GifData[$key]['AnimalName'] = $displyGifName;
        //     $GifData[$key]['AnimalList'][] = $gifArrayItem;
        // }

        // $GifData[$key]['AnimalList'] = [];
        if(sizeof($GifData)) {
            if($displyGifName == $ttt) {
                $GifData[$itemno]['AnimalList'][] = $gifArrayItem;
                unset($GifData[$key]);
            } else {
                $GifData[$key]['AnimalName'] = $displyGifName;
                $GifData[$key]['AnimalList'][] = $gifArrayItem;
                $itemno = $key;
            }
            $ttt = $displyGifName;
        } else {
            $GifData[$key]['AnimalName'] = $displyGifName;
            $GifData[$key]['AnimalList'][] = $gifArrayItem;

            $ttt = $GifData[$key]['AnimalName'];
            $itemno = $key;
        }
    }
    $GifData = array_values($GifData);
    //Data for GIFdata ends

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
                $imagesItem = explode('Video_dir', $presignedUrl);

                $parentDir = $rr[0];
                $child = $rr[1];
                if($child !== "thumbnail") {
                    $thumbPath = $imagesItem[0].$parentDir.'/thumbnail/';
                    $child = str_ireplace('.mp4', '.jpg', $child);
                    $thmbnailImg = $thumbPath.$child;
                    // $thmbnailImg = '';
                    // $tItem = basename($child, '.mp4');
                    // if($thumbPath.$tItem.'.jpg') {
                    //     $thmbnailImg = $thumbPath.$tItem.'.jpg';
                    // } else if($thumbPath.$tItem.'.jpeg') {
                    //     $thmbnailImg = $thumbPath.$tItem.'.jpeg';
                    // } else if($thumbPath.$tItem.'.png') {
                    //     $thmbnailImg = $thumbPath.$tItem.'.png';
                    // }
                    
                    $displyVideoName = ucwords(str_ireplace(["-", "_", ".png", ".jpg", ".jpeg", ".gif", ".mp4"], [" ", "", "", "", "", "", ""], preg_replace('/\d/', '', str_replace(".mp4","",explode("/",explode("?", substr($presignedUrl,51,53))[0])[1]))));
                    
                    //filtering the response as we want only the images here which are present in Img_dir
                    $VideoSubdata = array(
                        "AnimalName"=> $displyVideoName,
                        "PreviewImageURL"=> $thmbnailImg,
                        "YTB_VideoURL"=> $presignedUrl
                    );
                    array_push($Videodata,$VideoSubdata);  
                }
            }
        }
    }
    //Data for Videodata ends

    //sending response with json content type
    header('Content-Type: application/json; charset=utf-8');
    echo json_encode(array(
            'MaterialListPreviewScreen'=>$Materialdata,
            "GIFScreen"=>$GifData,
            'VideoPreviewScreen'=>$Videodata
        )
    );
} catch(Exception $Ex){
    echo $Ex;
}
?>
