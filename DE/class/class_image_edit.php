<?php 

class imageEdit{


public function machAvatar($pfadIn, $PfadOut, $pic, $maxwidth=""){ 
	//////macht miniaturen////////////////////////////////////////////////////////////////////////////////////////////////////////////////
				
				$PicPathIn=$pfadIn; 
				$PicPathOut=$PfadOut; 
				if(!isset($maxwidth)){
					$maxwidth = 200;
				}
				// Orginalbild 
				$bildtotmb=$pic; 
				//echo "$PicPathIn"."$bildtotmb".$pic;
				
				// Bilddaten ermitteln 
				$size=getimagesize("$PicPathIn"."$bildtotmb"); 
				$breite=$size[0]; 
				$hoehe=$size[1]; 
				$ratio = $breite/$hoehe; 
				
				$neueBreite=$maxwidth;
				$neueHoehe=$neueBreite/$ratio;
				
				
				if($size[2]==1) { 
				// GIF 
				$altesBild=imagecreatefromgif("$PicPathIn"."$bildtotmb"); 
				$neuesBild=imagecreatetruecolor($neueBreite,$neueHoehe); 
				imageCopyResized($neuesBild,$altesBild,0,0,0,0,$neueBreite,$neueHoehe,$breite,$hoehe);
				imageGIF($neuesBild,"$PicPathOut"."TN"."$bildtotmb"); 
				} 
				
				if($size[2]==2) { 
				// JPG 
				$altesBild=imagecreatefromjpeg("$PicPathIn"."$bildtotmb");
				$neuesBild=imagecreatetruecolor($neueBreite,$neueHoehe); 
				imageCopyResized($neuesBild,$altesBild,0,0,0,0,$neueBreite,$neueHoehe,$breite,$hoehe);
				ImageJPEG($neuesBild,"$PicPathOut"."TN"."$bildtotmb"); 
				} 
				
				if($size[2]==3) { 
				// PNG 
				$altesBild=imagecreatefrompng("$PicPathIn"."$bildtotmb"); 
				$neuesBild=imagecreatetruecolor($neueBreite,$neueHoehe); 
				imageCopyResized($neuesBild,$altesBild,0,0,0,0,$neueBreite,$neueHoehe,$breite,$hoehe);
				ImagePNG($neuesBild,"$PicPathOut"."TN"."$bildtotmb"); 
				} 
				
				return $PicPathOut."".$bildtotmb;
	
	}
public function resize_image($file, $w, $h, $crop=FALSE) {

    list($width, $height) = getimagesize($file);
    $r = $width / $height;
    if ($crop) {
        if ($width > $height) {
            $width = ceil($width-($width*abs($r-$w/$h)));
        } else {
            $height = ceil($height-($height*abs($r-$w/$h)));
        }
        $newwidth = $w;
        $newheight = $h;
    } else {
        if ($w/$h > $r) {
            $newwidth = $h*$r;
            $newheight = $h;
        } else {
            $newheight = $w/$r;
            $newwidth = $w;
        }
    }
    $src = imagecreatefromjpeg($file);
    $dst = imagecreatetruecolor($newwidth, $newheight);
    imagecopyresampled($dst, $src, 0, 0, 0, 0, $newwidth, $newheight, $width, $height);
	ImageJPEG($dst,$file);
    return $dst;
}
public function deleteOldImages($path,$rangetage){
	$scanned = array_diff(scandir($path), array('..', '.'));
	$stamp = $rangetage*86400;
	foreach($scanned as $file){
		$time=filemtime($path.$file)+$stamp;
		if(time()>$time){
			unlink($path.$file);
		}

	}
}
}

