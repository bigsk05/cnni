<?php 
//The Main Requests Dealing File.
require_once 'libraries/AipImageClassify.php';
require_once 'setting/baiduapi.php';
const APP_ID = $APP_ID;
const API_KEY = $API_KEY;
const SECRET_KEY = $SECRET_KEY;

$image = file_get_contents($_GET['image_address']);//Get Image From URL.
$client = new AipImageClassify(APP_ID, API_KEY, SECRET_KEY);// Start A New Object.

$client->plantDetect($image);//Detect Image.

