<?php

$root = '';
// use if specifying path from root
//$root = $_SERVER['DOCUMENT_ROOT'];


// the directory containing the JPG files
//$path = 'images/';
$path = '/var/www/html/utils/random_img/images/';

// a temporary logfile to remember the last pictures thrown
$prev = '/tmp/piclog.txt';

// this many pictures to remember no to show soon
$noRepeatLast = 60;

function getImagesFromDir($path) {
    $images = array();
    if ( $img_dir = @opendir($path) ) {
        while ( false !== ($img_file = readdir($img_dir)) ) {
            if ( preg_match("/(\.JPG|\.jpg)$/", $img_file) ) {
                $images[] = $img_file;
            }
        }
        closedir($img_dir);
    }
    return $images;
}

function getRandomFromArray($ar) {
    mt_srand( (double)microtime() * 1000000 ); // php 4.2+ not needed
    $num = array_rand($ar);
    return $ar[$num];
}


// Obtain list of images from directory
$imgList = getImagesFromDir($root . $path);

// Fill the temporary logfile with some data
if (file_exists($prev)) {
        $previmgss = file_get_contents($prev);
} else {
        for($i = 0; $i < $noRepeatLast; ++$i) {
                $img = getRandomFromArray($imgList);
                file_put_contents($prev, "|" . $img, FILE_APPEND);;
        }
        $previmgss = file_get_contents($prev);
}

// Get the list of the last pictures displayed so far
$previmgs = array_filter(array_slice(explode("|", $previmgss), 0, $noRepeatLast));

// Remove these from the list
$imgListClean = array_diff($imgList, $previmgs);

// Pick a random image from the rest
shuffle($imgListClean);
$img = getRandomFromArray($imgListClean);

// Save to the logfile the picked image
file_put_contents($prev, $img . "|");
file_put_contents($prev, implode("|", $previmgs), FILE_APPEND);

//print_r ($img);
//print_r ($previmgs);
//print_r ($imgListClean);
//print_r ($imgList);


// Serve out the picture as JPG
header("Content-type: image/jpg");
header("Expires: Mon, 1 Jan 2099 05:00:00 GMT");
header("Last-Modified: " . gmdate("D, d M Y H:i:s") . " GMT");
header("Cache-Control: no-store, no-cache, must-revalidate");
header("Cache-Control: post-check=0, pre-check=0", false);
header("Pragma: no-cache");
readfile($path . $img);
?>
