<?php
extract($_REQUEST);

  function saveCanvasImage($imageData, $imageName, $path){
        list($type, $data) = explode(';', $imageData);
        list(, $data)      = explode(',', $data);
        $data = base64_decode($data);
        return file_put_contents($path.$imageName, $data);
    }
function ajaxSaveCanvasImage($capture){
        extract($_REQUEST);
        $bild = "image.jpg";
        $imagePath = "../cam_image/";
        echo saveCanvasImage($capture,$bild,$imagePath);
    }
function checkStream(){
    $streamPath = "../cam_image/";
    $streamPic = "image.jpg";
    if(!file_exists($streamPath.$streamPic)){
        echo "notactive";
        exit;
    }
    $streamTime =  filemtime($streamPath.$streamPic);
    if($streamTime<time()-5){
        echo "notactive";
        exit;
    }
    echo "active";
}
if(isset($capture)){
    ajaxSaveCanvasImage($capture);
}
if(isset($checkStream)){
    checkStream();
}