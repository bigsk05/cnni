<?php 
//The Main Requests Dealing File.
require_once 'AipImageClassify.php';

$image = file_get_contents($_GET['image_address']);//Get Image From URL.
if(!empty($image)){
    $hash=sha1($image);

    //$link=mysqli_connect("localhost","root","root","ghinkai");
    mysqli_query($link,"set names utf8;");

    if($_GET['type_plant']=="on"){//Detect Image.
        $result=mysqli_query($link,"SELECT * FROM `plant_data` WHERE `hash`='".$hash."'");
    }elseif($_GET['type_animal']=="on"){
        $result=mysqli_query($link,"SELECT * FROM `animal_data` WHERE `hash`='".$hash."'");
    }

    if(!empty(mysqli_fetch_array($result))){//There's already a record in database.
        $before_score=0;
        $before_name=0;

        echo '<h1>结果为：</h1>';

        while($res = mysqli_fetch_array($result)){ 
            echo '<h3>'.$res['score'].'-'.$res['name'].'</h3>';
            if($before_score < $res['score']){
                $last_score=$res['score'];
                $last_name=$res['name'];
            }
            $before_score=$res['score'];
            $before_name=$res['name'];
        }

        echo '<h2>最有可能为：</h2>';
        echo '<h3>'.$last_score.'-'.$last_name.'</h3>';

        echo '<h2>其他信息：</h2>';
        echo '<h3>SHA1：'.$hash.'</h3>';
    }else{
        if($_GET['type_plant']=="on"){//Detect Image.
            $result=mysqli_query($link,"SELECT * FROM `plant_application_info` WHERE `date`='".date("Y-m-d")."'");
        }elseif($_GET['type_animal']=="on"){
            $result=mysqli_query($link,"SELECT * FROM `animal_application_info` WHERE `date`='".date("Y-m-d")."'");
        }
        if(!empty(mysqli_fetch_array($result))){
            if($_GET['type_plant']=="on"){//Detect Image.
                $result=mysqli_query($link,"SELECT * FROM `plant_application_info`");
            }elseif($_GET['type_animal']=="on"){
                $result=mysqli_query($link,"SELECT * FROM `animal_application_info`");
            }

            $whiletimes=0;
            while($whiletimes = mysqli_num_rows($result)){
                $whiletimes=$whiletimes+1;
                if($_GET['type_plant']=="on"){//Detect Image.
                    $result=mysqli_query($link,"SELECT * FROM `plant_application_info` WHERE `id`='".$whiletimes."'");
                }elseif($_GET['type_animal']=="on"){
                    $result=mysqli_query($link,"SELECT * FROM `animal_application_info` WHERE `id`='".$whiletimes."'");
                }
                while($res = mysqli_fetch_array($result)){ 
                    if($res['times']>0){
                        $times=$res['times'];
                    }
                }
                if(!empty($times)){
                    $id=$whiletimes;
                    break;
                }
            }
            
            if(!empty($times)){
                $result=mysqli_query($link,"SELECT * FROM `user` WHERE `name`='".$_GET['auth_id']."'");
                if(!empty($result)){
                    if($_GET['type_plant']=="on"){//Detect Image.
                        $result=mysqli_query($link,"SELECT * FROM `plant_application_info` WHERE `id`='".$id."'");
                    }elseif($_GET['type_animal']=="on"){
                        $result=mysqli_query($link,"SELECT * FROM `animal_application_info` WHERE `id`='".$id."'");
                    }
                    while($res = mysqli_fetch_array($result)){ 
                        $APP_ID=$res['APP_ID'];
                        $API_KEY=$res['API_KEY'];
                        $SECRET_KEY=$res['SECRET_KEY'];
                    }

                    $client = new AipImageClassify($APP_ID, $API_KEY, $SECRET_KEY);// Start A New Object.

                    if($_GET['type_plant']=="on"){//Detect Image.
                        $result = $client->plantDetect($image);
                    }elseif($_GET['type_animal']=="on"){
                        $result = $client->animalDetect($image);
                    }

                    $before_score=0;
                    $before_name=0;

                    echo '<h1>结果为：</h1>';

                    foreach($result['result'] as $line){//Foreach the array and output the result.
                        echo '<h3>'.$line['score'].'-'.$line['name'].'</h3>';
                        if($before_score < $line['score']){
                            $last_score=$line['score'];
                            $last_name=$line['name'];
                        }
                        $before_score=$line['score'];
                        $before_name=$line['name'];
                    }

                    if($_GET['type_plant']=="on"){//Detect Image.
                        $file="../data/plant/".$last_name."-P/".$hash.'.'.substr(strrchr($_GET['image_address'], '.'), 1);
                        mysqli_query($link,"INSERT INTO `plant_data` (`name`, `score`, `hash`, `path`) VALUES ('".$last_name."','".$last_score."','".$hash."','".$file."')");
                        mysqli_query($link,"UPDATE `plant_application_info` SET `times`='".($times-1)."' WHERE `date`='".date("Y-m-d")."'");
                    }elseif($_GET['type_animal']=="on"){
                        $file="../data/animal/".$last_name."-A/".$hash.'.'.substr(strrchr($_GET['image_address'], '.'), 1);
                        mysqli_query($link,"INSERT INTO `animal_data` (`name`, `score`, `hash`, `path`) VALUES ('".$last_name."','".$last_score."','".$hash."','".$file."')");
                        mysqli_query($link,"UPDATE `animal_application_info` SET `times`='".($times-1)."' WHERE `date`='".date("Y-m-d")."'");
                    }

                    echo '<h2>最有可能为：</h2>';
                    echo '<h3>'.$last_score.'-'.$last_name.'</h3>';

                    echo '<h2>其他信息：</h2>';
                    echo '<h3>SHA1：'.$hash.'</h3>';

                    if(!file_exists(str_replace('/'.basename($file),'',$file))){
                        mkdir(str_replace('/'.basename($file),'',$file),0777,true);
                    }
                    $fp = @fopen($file, "a");
                    fwrite($fp, $image);
                    fclose($fp);

                }else{
                    echo '<h1>您没有使用权限！</h1>';
                }
            }else{
                echo '<h1>今日请求次数已达上限！</h1>';
            }
        }else{
            if($_GET['type_plant']=="on"){//Detect Image.
                $result=mysqli_query($link,"SELECT * FROM `plant_application_info`");
                $whiletimes=0;
                while($whiletimes = mysqli_num_rows($result)){
                    $whiletimes=$whiletimes+1;
                    mysqli_query($link,"UPDATE `plant_application_info` SET `date`='".date("Y-m-d")."' WHERE `id`='".$whiletimes."'");
                }
            }elseif($_GET['type_animal']=="on"){
                $result=mysqli_query($link,"SELECT * FROM `animal_application_info`");
                $whiletimes=0;
                while($whiletimes = mysqli_num_rows($result)){
                    $whiletimes=$whiletimes+1;
                    mysqli_query($link,"UPDATE `animal_application_info` SET `date`='".date("Y-m-d")."' WHERE `id`='".$whiletimes."'");
                }
            }
        }
    }
}else{
    echo '<h1>图片获取失败！</h1>';
}