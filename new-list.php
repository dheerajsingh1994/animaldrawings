<?php 
error_reporting();
try{
    include("includes/config.php");
    //global arrays declared for being used later while sending json as response
    $Materialdata= Array();
    $Videodata= Array();

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
    // echo '<pre>';
    // print_r($gifArrayList);
    // die;

    $gifArray = array_unique($gifArray); 

    $dec= [];
    $ttt = '';
    foreach($gifArrayList as $key => $gifArrayItem){   //looping through each animal name
        $displyGifName = ucwords(str_ireplace(["-", "_", ".png", ".jpg", ".jpeg", ".gif", ".mp4"], [" ", "", "", "", "", "", ""], preg_replace('/\d/', '', str_replace(".png","",explode("/",explode("?", substr($gifArrayItem,51,53))[0])[1]))));
        // echo $ttt; echo '<br>';

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
                // $GifData[$key]['AnimalName'] = $displyGifName;
                $GifData[$itemno]['AnimalList'][] = $gifArrayItem;
                // $itemno[$AnimalName] = $key;

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
    // $ddd = '';
    // $newdata = [];

    // foreach ($GifData as $key2 => $value) {
    //     // echo '<pre>';
    //     // print_r($value);
    //     // die('dead');

    //     if($ddd == $value['AnimalName']) {
    //         // $GifData[$key]['AnimalName'] = $displyGifName;
    //         // $GifData[$key2]['AnimalList'][] = $value['AnimalList'];
    //         array_push($GifData[$key2]['AnimalList'], $value['AnimalList']);
    //     } else {
    //         $GifData[$key2]['AnimalName'] = $value['AnimalName'];
    //         // $GifData[$key2]['AnimalList'][] = $value['AnimalList'];
    //         array_push($GifData[$key2]['AnimalList'], $value['AnimalList']);
    //     }
    //     $ddd = $value['AnimalName'];
    // }
    // echo '<pre>';
    // print_r($GifData);
    // die;
    //Data for GIFdata ends

    //sending response with json content type
    header('Content-Type: application/json; charset=utf-8');
    echo json_encode(array('MaterialListPreviewScreen'=>$Materialdata,
        "GIFScreen"=>$GifData,
        'VideoPreviewScreen'=>$Videodata)
    );
} catch(Exception $Ex){
    echo $Ex;
}
?>
