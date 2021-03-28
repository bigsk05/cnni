<!DOCTYPE html>
<html>
<head>
  <meta charset="UTF-8">
  <title>你走错地方了</title>
    <link rel="stylesheet" href="../css/style.css" media="screen" type="text/css" />
    <link rel="icon" href="https://i.loli.net/2020/09/26/1qFYLIAkUzrPEgt.png" type="image/png">
    <link rel="shortcut icon" href='https://i.loli.net/2020/09/26/1qFYLIAkUzrPEgt.png' type="image/png">
    <meta name="viewport" content="width=device-width,initial-scale=1.0">
    <script>
      function online(){
        document.getElementById("control").innerHTML = '<h5>选择图片存在的地方：</h5><input type="radio" name="type" value="online" onClick="online()" checked/><strong>在线图片</strong><input type="radio" name="type" value="upload" onClick="upload()"/><strong>上传图片</strong><input type="text" name="image"/><br/>';
      }
      function upload(){
        document.getElementById("control").innerHTML = '<h5>选择图片存在的地方：</h5><input type="radio" name="type" value="online" onClick="online()"/><strong>在线图片</strong><input type="radio" name="type" value="upload" onClick="upload()" checked/><strong>上传图片</strong><input type="file" name="image" placeholder="图片链接"/><br/>';
      }
    </script>
</head>
<body>
    <div class="main">
        <form id="mainform" action="entrance.php" method="post">
            <h3>普思创识</h3>
            <h4>结果来了~客官这边请~</h4>
			<h4><a href="index.html">返回</a></h4>
<?php
//--------------------------//
require_once '../libraries/AipImageClassify.php';
$APP_ID='';
$API_KEY='';
$SECRET_KEY='';
//--------------------------//
switch ($_POST['type']){
	case "upload":
		$output='';
		$image=file_get_contents($_FILES['image']['tmp_name']);
		if(!empty($image)){
			$hash=md5($image);
			if(file_exists("../data/".$hash.".html")){
				echo file_get_contents("../data/".$hash.".html");
			}else{
				$client = new AipImageClassify($APP_ID, $API_KEY, $SECRET_KEY);
				if($_POST['choice']=="plant"){
					$result = $client->plantDetect($image);
				}elseif($_POST['choice']=="animal"){
					$result = $client->animalDetect($image);
				}
                $before_score=0.0;
                $before_name="";
				echo '<h4>结果列表：</h4>';
				$output=$output.'<h4>结果列表：</h4>';
				foreach($result['result'] as $line){
					echo '<h5>'.$line['score'].'->'.$line['name'].'</h5>';
					$output=$output.'<h5>'.$line['score'].'->'.$line['name'].'</h5>';
					if($before_score < $line['score']){
						$last_score=$line['score'];
						$last_name=$line['name'];
					}
					$before_score=$line['score'];
					$before_name=$line['name'];
				}
				echo '<h4>我认为它应该是：</h4>'.'<h5>'.$last_score.'->'.$last_name.'</h5>'.'<h4>更多信息：</h4>'.'<h5>MD5:'.$hash.'</h5>';
				$output=$output.'<h4>我认为它应该是：</h4>'.'<h5>'.$last_score.'->'.$last_name.'</h5>'.'<h4>更多信息：</h4>'.'<h5>MD5:'.$hash.'</h5>';
				$fp = @fopen("../data/".$_POST['choice'].'/'.$hash.".html", "w+");
				fwrite($fp, $output);
				fclose($fp);
			}
		}else{
			echo '<h4>奥...加载图片时好像遇到些问题，稍后再试试吧！</h4>';
		}
		break;
	case "online":
		$output='';
		$image = file_get_contents($_POST['image']);
		if(!empty($image)){
			$hash=md5($image);
			if(file_exists("../data/".$hash.".html")){
				echo file_get_contents("../data/".$hash.".html");
			}else{
				$client = new AipImageClassify($APP_ID, $API_KEY, $SECRET_KEY);
				if($_POST['choice']=="plant"){
					$result = $client->plantDetect($image);
				}elseif($_POST['choice']=="animal"){
					$result = $client->animalDetect($image);
				}
                $before_score=0.0;
                $before_name="";
				echo '<h4>结果列表：</h4>';
				$output=$output.'<h4>结果列表：</h4>';
				foreach($result['result'] as $line){
					echo '<h5>'.$line['score'].'->'.$line['name'].'</h5>';
					$output=$output.'<h5>'.$line['score'].'->'.$line['name'].'</h5>';
					if($before_score < $line['score']){
						$last_score=$line['score'];
						$last_name=$line['name'];
					}
					$before_score=$line['score'];
					$before_name=$line['name'];
				}
				echo '<h4>我认为它应该是：</h4>'.'<h5>'.$last_score.'->'.$last_name.'</h5>'.'<h4>不准确？更多信息：</h4>'.'<h5>MD5:'.$hash.'</h5>';
				$output=$output.'<h4>我认为它应该是：</h4>'.'<h5>'.$last_score.'->'.$last_name.'</h5>'.'<h4>不准确？更多信息：</h4>'.'<h5>MD5:'.$hash.'</h5>';
				$fp = @fopen("../data/".$_POST['choice'].'/'.$hash.".html", "w+");
				fwrite($fp, $output);
				fclose($fp);
			}
		}else{
			echo '<h4>奥...加载图片时好像遇到些问题，稍后再试试吧！</h4>';
		}
		break;
	default:
		echo '<h4>我没理解您的请求，劳烦您再确认一下呢！</h4>';
}
?>
			<h4>版权所有 &copy; <a href="https://www.xiaxinzhe.cn">夏歆哲</a> <a href="https://www.ghink.net">极科网络工作室</a></h4>
        </form>
    </div>
</body>
</html>