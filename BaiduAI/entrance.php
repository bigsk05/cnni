<?php 
/*
Entrance of application programming interface
Author: Bigsk(https://xiaxinzhe.cn)[xiaxinzhe@xiaxinzhe.cn]
*/
//--------------------------//
$APP_ID='';
$API_KEY='';
$SECRET_KEY='';
//--------------------------//
require_once 'AipImageClassify.php';
switch $_GET['type']{
	$output='';
	case "upload":
		$image=file_get_contents($_FILES['image']['tmp_name']);
		if(!empty($image)){
			$hash=md5($image);
			if(!file_exists("data/".$hash.".html")){
				echo file_get_contents("data/".$hash.".html");
			}else{
				$client = new AipImageClassify($APP_ID, $API_KEY, $SECRET_KEY);
				if($_GET['choice']=="plant"){
					$result = $client->plantDetect($image);
				}elseif($_GET['choice']=="animal"){
					$result = $client->animalDetect($image);
				}
                $before_score=0.0;
                $before_name="";
				echo '<h1>Result:</h1>';
				$output=$output.'<h1>Result:</h1>';
				foreach($result['result'] as $line){
					echo '<h3>'.$line['score'].'-'.$line['name'].'</h3>';
					$output=$output.'<h3>'.$line['score'].'-'.$line['name'].'</h3>';
					if($before_score < $line['score']){
						$last_score=$line['score'];
						$last_name=$line['name'];
					}
					$before_score=$line['score'];
					$before_name=$line['name'];
				}
				echo '<h2>It might be:</h2>'.'<h3>'.$last_score.'-'.$last_name.'</h3>'.'<h2>Other infomation:</h2>'.'<h3>MD5:'.$hash.'</h3>';
				$output=$output.'<h2>It might be:</h2>'.'<h3>'.$last_score.'-'.$last_name.'</h3>'.'<h2>Other infomation:</h2>'.'<h3>MD5:'.$hash.'</h3>';
				$fp = @fopen("data/".$hash.".html", "w+");
				fwrite($fp, $output);
				fclose($fp);
			}
		}else{
			echo '<h1>Fail to get the image.</h1>';
		}
	case "online":
		$image = file_get_contents($_GET['image_address']);
		if(!empty($image)){
			$hash=md5($image);
			if(!file_exists("data/".$hash.".html")){
				echo file_get_contents("data/".$hash.".html");
			}else{
				$client = new AipImageClassify($APP_ID, $API_KEY, $SECRET_KEY);
				if($_GET['choice']=="plant"){
					$result = $client->plantDetect($image);
				}elseif($_GET['choice']=="animal"){
					$result = $client->animalDetect($image);
				}
                $before_score=0.0;
                $before_name="";
				echo '<h1>Result：</h1>';
				$output=$output.'<h1>Result:</h1>';
				foreach($result['result'] as $line){
					echo '<h3>'.$line['score'].'-'.$line['name'].'</h3>';
					if($before_score < $line['score']){
						$last_score=$line['score'];
						$last_name=$line['name'];
					}
					$before_score=$line['score'];
					$before_name=$line['name'];
				}
				echo '<h2>It might be:</h2>'.'<h3>'.$last_score.'-'.$last_name.'</h3>'.'<h2>Other infomation:</h2>'.'<h3>MD5:'.$hash.'</h3>';
				$output=$output.'<h2>It might be:</h2>'.'<h3>'.$last_score.'-'.$last_name.'</h3>'.'<h2>Other infomation:</h2>'.'<h3>MD5:'.$hash.'</h3>';
				$fp = @fopen("data/".$hash.".html", "w+");
				fwrite($fp, $output);
				fclose($fp);
			}
		}else{
			echo '<h1>Fail to get the image.</h1>';
		}
	default:
		echo '<h1>Wrong request type.It should be "upload" or "online".</h1>';
}