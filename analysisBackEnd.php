<?php
date_default_timezone_set('UTC');



$action = $_REQUEST['action'];

$project = $_REQUEST['project'];



$data= array();

if(file_exists("../cam-images/".$project."/data.json")){
    $data = json_decode(file_get_contents("../cam-images/".$project."/data.json"),TRUE);
}


$i=0;

 if($action == 'saveData'){
     $json = json_decode($_REQUEST['json'],TRUE);
     $proj = $json['meta']['project'];
     #copy("../cam-images/".$proj."/data.json","../cam-images/".$proj."/data.json.".date('YMDhm');
     $from = "../cam-images/".$proj."/data.json";
     $to = "../cam-images/".$proj."/data.json.".date('YmdHi');
     if(copy($from,$to)) echo "Copied file from: $from To:$to  ";
     
     if(file_put_contents("../cam-images/".$proj."/data.json",json_encode($json,JSON_PRETTY_PRINT))){
        echo "saved";
     }

 }


if($action == 'setParam'){
    $param = $_GET['param'];
    $value = $_GET['value'];
    $path = explode("_",$param);
    if(count($path) == 2){
        $data[$path[0]][$path[1]] = intval($value);
    }
    if(count($path) == 3){
        $data[$path[0]][$path[1]][$path[2]] = intval($value);
    }
    file_put_contents("../cam-images/".$project."/data.json",json_encode($data,JSON_PRETTY_PRINT));
}


if($action == 'getImageList'){

    $directory = "../cam-images/".$project."/";
    $scanned_directory = scandir($directory);
    foreach($scanned_directory as $file){

        if(@is_array(getimagesize($directory.$file))){
            $image = true;
        } else {
            continue;
        }


        preg_match_all('!\d+!', $file, $matches);

        $fileDate = strtotime($matches[0][0]);
        if(array_key_exists($fileDate,$data['img'])){
            continue;
        }
        $img = ImageCreateFromJpeg($directory.$file);

        $fileData = array(
            "filename" => $file,
            "obsTime" => date('c',$fileDate),
            "width" =>imagesx($img),
            "height" => imagesy($img),
            "pickX" => null,
            "pickY" => null,
            "pixelElev" => null,
            "vOffset" => 0,
            "hOffset" => 0);
        if(!isset($data['img'][intval($fileDate)]))  $data['img'][intval($fileDate)] = $fileData;
        if(!isset($data['meta'])) $data['meta'] = array(
            "project"=> $project,
            "nwslid" => '',
            "pe" => '',
            "lastEdit"=> '',
            "vertLineRef" => 0,
            "horzLineRef" => 0,
            "pixelLength" => null,
            "note" => '');
    }
    ksort($data);
    file_put_contents("../cam-images/".$project."/data.json",json_encode($data,JSON_PRETTY_PRINT));
    chmod("../cam-images/".$project."/data.json",0777);
    echo json_encode($data);
}
?>