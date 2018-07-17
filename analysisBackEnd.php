<?php

$action = $_GET['action'];
$project = $_GET['project'];

$data= array();

if(file_exists("./cams-archive/".$project."/data.json")){
    $data = json_decode(file_get_contents("./cams-archive/".$project."/data.json"),TRUE);
}


$i=0;

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
    file_put_contents("./cams-archive/".$project."/data.json",json_encode($data,JSON_PRETTY_PRINT));
}


if($action == 'getImageList'){

    $directory = "./cams-archive/".$project."/";
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
        $data['img'][intval($fileDate)] = $fileData;
        $data['meta'] = array(
            "nwslid" => '',
            "pe" => '',
            "lastEdit"=> '',
            "vertLineRef" => 0,
            "horzLineRef" => 0,
            "pixelLength" => null,
            "note" => '');
    }
    ksort($data);
    file_put_contents("./cams-archive/".$project."/data.json",json_encode($data,JSON_PRETTY_PRINT));
    echo json_encode($data);
}
?>