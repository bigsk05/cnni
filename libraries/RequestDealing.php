<?php 
//The Main Requests Dealing File.
require_once 'AipImageClassify.php';
const APP_ID = '';
const API_KEY = '';
const SECRET_KEY = '';

$image = file_get_contents($_GET['image_address']);//Get Image From URL.
$client = new AipImageClassify(APP_ID, API_KEY, SECRET_KEY);// Start A New Object.

if($_GET['type_plant']=="on"){//Detect Image.
    $result = $client->plantDetect($image);
}elseif($_GET['type_animal']=="on"){
    $result = $client->animalDetect($image);
}

foreach($result['result'] as $line){
    echo '<h2>'.$line['score'].'-'.$line['name'].'</h2>';
}
