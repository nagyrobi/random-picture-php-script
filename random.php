<?php

$root = '/var/www/html/utils/random_img/';
//$root = '';
// use if specifying path from root
//$root = $_SERVER['DOCUMENT_ROOT'];

// this many pictures to remember no to show soon
$noRepeatLast = 30;


$clientip = getUserIpAddr();

switch ($clientip) {
    case "192.168.1.11":
        $path = $root . 'images-1/'; 			// the directory containing the JPG files
    	$prev = '/tmp/piclog-display-1.txt';	// a temporary logfile to remember the last pictures thrown
        break;
    case "192.168.1.12":
        $path = $root . 'images-2/';
	    $prev = '/tmp/piclog-display-2.txt';
        break;
    case "192.168.1.13":
        $path = $root . 'images/';
        $prev = '/tmp/piclog-display-3.txt';
        break;
    default:
        $path = $root . 'images/';
    	$prev = '/tmp/piclog-others.txt';
        break;
}


function getUserIpAddr(){
    if(!empty($_SERVER['HTTP_CLIENT_IP'])){
        //ip from share internet
        $ip = $_SERVER['HTTP_CLIENT_IP'];
    }elseif(!empty($_SERVER['HTTP_X_FORWARDED_FOR'])){
        //ip pass from proxy
        $ip = $_SERVER['HTTP_X_FORWARDED_FOR'];
    }else{
        $ip = $_SERVER['REMOTE_ADDR'];
    }
    return $ip;
}

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
$imgList = getImagesFromDir($path);

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

//date_default_timezone_set("Europe/Amsterdam");
//file_put_contents('/tmp/piclogtime.txt', date("Y-m-d H:i:s") . " : " . getUserIpAddr() . " : " . $img . "\n", FILE_APPEND);

//print_r ($img);
//echo "\n";
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
