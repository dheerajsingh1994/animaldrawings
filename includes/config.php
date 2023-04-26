<?php 
ini_set ( 'display_errors',1 );
require 'aws/aws-autoloader.php';

use Aws\S3\S3Client;

    // get application environment
    if ($_SERVER['HTTP_HOST'] == "localhost") define('LOCAL_MODE', true);
    else define('LOCAL_MODE', false);

    // File system path
    $tmp = dirname(__FILE__);
    $tmp = str_replace('\\' ,'/',$tmp);
    $tmp = substr($tmp, 0, strrpos($tmp, '/'));
    define('SITE_DIR_PATH', $tmp); // define site directory
    include_once($tmp.'/php/helpers.php'); // include helper functions

    define('AWS_KEY', 'AKIARU2XTBX5RZGGWYYV');
    define('AWS_SECRET_KEY', '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');
    define('HOST', 'https://s3.ap-south-1.amazonaws.com');
    define('REGION', 'ap-south-1');
    define('BUCKET', 'animal-drawing');

    // // Establish connection with DreamObjects with an S3 client.
    $s3Client = new Aws\S3\S3Client([
        'version'     => '2006-03-01',
        'region'      => REGION,
        'endpoint'    => HOST,
            'credentials' => [
            'key'      => AWS_KEY,
            'secret'   => AWS_SECRET_KEY,
        ],
    ]);
    
    $contents = $s3Client->listObjects([
        'Bucket' => BUCKET,
    ]);
?>