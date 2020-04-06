<?php

function resize_image($file, $w, $h, $crop = false) {


	// Extension Check			
	$finfo = finfo_open(FILEINFO_MIME_TYPE);
	$mime_type = finfo_file($finfo, $file);
	finfo_close($finfo);
	if ($mime_type != "image/jpeg" && $mime_type != "image/png") return false;


	list($width, $height) = getimagesize($file);
	

	// Early exit if image size is smaller than the crop
	if ( $w >= $width && $h >= $height ) return "Smaller image";


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


	if ($mime_type == "image/jpeg") {

		$src = imagecreatefromjpeg($file);
		$dst = imagecreatetruecolor($newwidth, $newheight);
		imagecopyresampled($dst, $src, 0, 0, 0, 0, $newwidth, $newheight, $width, $height);
		imagejpeg($dst, $file, 100);

		return $dst;

	}


	if ($mime_type == "image/png") {

		$src = imagecreatefrompng($file);
		$dst = imagecreatetruecolor($newwidth, $newheight);
		imagecopyresampled($dst, $src, 0, 0, 0, 0, $newwidth, $newheight, $width, $height);
		imagepng($dst, $file, 9);

		return $dst;

	}


	return false;

}