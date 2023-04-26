<?php

include("../includes/config.php");
echo BUCKET;die;
// try {
// 	// 2. List the objects and get the keys.
// 	$keys = $s3->listObjects([
// 		'Bucket' => BUCKET
// 	]); 

// 	// 3. Delete the objects.
// 	// foreach ($keys['Contents'] as $key)
// 	// {
// 	// 	$s3->deleteObjects([
// 	// 	    'Bucket'  => BUCKET,
// 	// 	    'Delete' => [
// 	// 	        'Objects' => [
// 	// 	            [
// 	// 	                'Key' => $key['Key']
// 	// 	            ]
// 	// 	        ]
// 	// 	    ]
// 	// 	]);
// 	// }
// 	echo BUCKET;
// 	header('Content-Type: application/json;');
// 	return json_encode(array('status' => 200, 'msg' => 'All items deleted successfully.'));
// } catch (Exception $e) {
// 	header('Content-Type: application/json;');
//     echo 'and the error is: ',  $e->getMessage(), "\n";
// }

?>