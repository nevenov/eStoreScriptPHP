<?php 
//error_reporting(0);

include( dirname(__FILE__). "/allinfo.php");

$sql_prefix = "estore";

//////////////////////////////////////////
////////// DO NOT CHANGE BELOW ///////////
//////////////////////////////////////////

$CONFIG["upload_folder"]='upload/';
$CONFIG["upload_thumbs"]='upload/thumbs/';

$TABLE["Ads"] 		= $sql_prefix.'_ads';
$TABLE["Categories"]= $sql_prefix.'_categories';
$TABLE["Users"]		= $sql_prefix.'_users';
$TABLE["Orders"]	= $sql_prefix.'_orders';
$TABLE["Options"] 	= $sql_prefix.'_options';

$Version = "1.0";

$php_version_min = "5.3.0";
$mysql_version_min = "5.0.0";

if ($installed!='yes') {	
	$connCl = mysqli_connect($CONFIG["hostname"], $CONFIG["mysql_user"], $CONFIG["mysql_password"], $CONFIG["mysql_database"]);
	if (mysqli_connect_errno()) {
		die('MySQL connection error: .'.mysqli_connect_error());
	}
	mysqli_set_charset($connCl, "utf8");
}



// curency abbreviations

// currency abbreviations before the amount
$CurrAbr["1"] = "USD";
$CurrAbr["2"] = "GBP";
$CurrAbr["3"] = "EUR";
$CurrAbr["4"] = "CAD";
$CurrAbr["5"] = "AUD";
$CurrAbr["6"] = "NZD";
$CurrAbr["7"] = "HKD";
$CurrAbr["8"] = "BRL";
$CurrAbr["9"] = "MXN";
$CurrAbr["10"] = "MYR";
$CurrAbr["11"] = "INR";
$CurrAbr["12"] = "FJD";

// currency abbreviations after the amount
$CurrAbr["13"] = "CHF";
$CurrAbr["14"] = "SEK";
$CurrAbr["15"] = "NOK";
$CurrAbr["16"] = "DKK";
$CurrAbr["17"] = "ILS";
$CurrAbr["18"] = "ZAR";
$CurrAbr["19"] = "CZK";
$CurrAbr["20"] = "PLN";
$CurrAbr["21"] = "HUF";
$CurrAbr["22"] = "RUB";
$CurrAbr["23"] = "KRW";
$CurrAbr["24"] = "JPY";
$CurrAbr["25"] = "VND";
$CurrAbr["26"] = "XOF";



// curency signs

// currency sign before the amount
$CurrSign["1"] = "$";
$CurrSign["2"] = "&pound;";
$CurrSign["3"] = "&euro;";
$CurrSign["4"] = "$";
$CurrSign["5"] = "$";
$CurrSign["6"] = "$";
$CurrSign["7"] = "$";
$CurrSign["8"] = "R$";
$CurrSign["9"] = "Mex$";
$CurrSign["10"] = "RM";
$CurrSign["11"] = "Rs.";
$CurrSign["12"] = "FJ$";

// currency sign after the amount
$CurrSign["13"] = "CHF";
$CurrSign["14"] = "SEK";
$CurrSign["15"] = "NOK";
$CurrSign["16"] = "DKK";
$CurrSign["17"] = "ILS";
$CurrSign["18"] = "ZAR";
$CurrSign["19"] = "CZK";
$CurrSign["20"] = "PLN";
$CurrSign["21"] = "HUF";
$CurrSign["22"] = "RUB";
$CurrSign["23"] = "KRW";
$CurrSign["24"] = "JPY";
$CurrSign["25"] = "VND";
$CurrSign["26"] = "FCFA";

////////////////////////////////////////////////
//////////// functions /////////////////////////
////////////////////////////////////////////////

// function that replace SELECT, INSERT, UPDATE and DELETE sql statements
if(!function_exists('sql_result')){ 
	function sql_result($sql) {
		global $connCl;
		$sql_result = mysqli_query ($connCl, $sql) or die ('Could not execute MySQL query: '.$sql.' . Error: '.mysqli_error($connCl));
		return $sql_result;
	}
}

// function for safety SELECT, INSERT, UPDATE and DELETE sql statements
if(!function_exists('SafetyDB')){ 
	function SafetyDB($str) {
		global $connCl;
		return mysqli_real_escape_string($connCl, $str); 
	}
}

// function for escaping quotes in INSERT and UPDATE sql statements
if(!function_exists('SaveDB')){ 
	function SaveDB($str) {
		if (!get_magic_quotes_gpc()) {	
			return addslashes($str); 
		} else {
			return $str;
		}
	}
}

// function for escaping quotes in SELECT sql statements
if(!function_exists('ReadDB')){ 
	function ReadDB($str) {
		return stripslashes($str);
	}
}

// function for escaping quotes in SELECT sql statements with showing the quotes
if(!function_exists('ReadHTML')){ 
	function ReadHTML($str) {
		return htmlspecialchars(stripslashes($str), ENT_QUOTES);
	}
}

// function that formatting date and time in admin area
if(!function_exists('admin_date')){ 
	function admin_date($db_date) {
		return date("M j, Y",strtotime($db_date));
	}
}

// function that formatting date and time in admin area -> orders
if(!function_exists('orders_date')){ 
	function orders_date($db_date) {
		return date("M j, Y h:i A",strtotime($db_date));
	}
}

// function that cut plain text to any number of characters
if(!function_exists('cutText')){ 
	function cutText($strMy, $maxLength)
	{
		$ret = substr($strMy, 0, $maxLength);
		if (substr($ret, strlen($ret)-1,1) != " " && strlen($strMy) > $maxLength) {
			$ret1 = substr($ret, 0, strrpos($ret," "))." ...";
		} elseif(substr($ret, strlen($ret)-1,1) == " " && strlen($strMy) > $maxLength) {
			$ret1 = $ret." ...";
		} else {
			$ret1 = $ret;
		}
		return $ret1;
	}
}

// function that invert colors in HTML
if(!function_exists('invert_colour')){ 
	function invert_colour($start_colour) {
		if($start_colour!='') {
			$colour_red = hexdec(substr($start_colour, 1, 2));
			$colour_green = hexdec(substr($start_colour, 3, 2));
			$colour_blue = hexdec(substr($start_colour, 5, 2));
			
			$new_red = dechex(255 - $colour_red);
			$new_green = dechex(255 - $colour_green);
			$new_blue = dechex(255 - $colour_blue);
			
			if (strlen($new_red) == 1) {$new_red .= '0';}
			if (strlen($new_green) == 1) {$new_green .= '0';}
			if (strlen($new_blue) == 1) {$new_blue .= '0';}
			
			$new_colour = '#'.$new_red.$new_green.$new_blue;
		} else {
			$new_colour = '#000000';
		}
		return $new_colour;
	} 
}

// function for resize image. If $thumbnail is not set then creates the full description image
if(!function_exists('Resize_File')){ 
	function Resize_File($full_file, $max_width, $max_height, $thumbnail="") {
		
		if (preg_match("/\.png$/i", $full_file)) {
			$img = imagecreatefrompng($full_file);
		}
		
		if (preg_match("/\.(jpg|jpeg)$/i", $full_file)) {
			$img = imagecreatefromjpeg($full_file);
		}
		
		if (preg_match("/\.gif$/i", $full_file)) {
			$img = imagecreatefromgif($full_file);
		}
		
		$FullImage_width = imagesx($img);
		$FullImage_height = imagesy($img);
		
		if (isset($max_width) && isset($max_height) && $max_width != 0 && $max_height != 0 && $FullImage_width>$max_width && $FullImage_height>$max_height) {
			$new_width = $max_width;
			$new_height = $max_height;
		} elseif (isset($max_width) && $max_width != 0 && $FullImage_width>$max_width) {
			$new_width = $max_width;
			$new_height = ((int)($new_width * $FullImage_height) / $FullImage_width);
		} elseif (isset($max_height) && $max_height != 0 && $FullImage_height>$max_height) {
			$new_height = $max_height;
			$new_width = ((int)($new_height * $FullImage_width) / $FullImage_height);
		} else {
			$new_height = $FullImage_height;
			$new_width = $FullImage_width;
		}
		
		$full_id = imagecreatetruecolor((int)$new_width, (int)$new_height);
		if (preg_match("/\.png$/i", $full_file) or preg_match("/\.gif$/i", $full_file)) {
			imagecolortransparent($full_id, imagecolorallocatealpha($full_id, 0, 0, 0, 0));
		}
		imagecopyresampled($full_id, $img, 0, 0, 0, 0, (int)$new_width, (int)$new_height, $FullImage_width, $FullImage_height);
		
		
		if (preg_match("/\.(jpg|jpeg)$/i", $full_file)) {
			if($thumbnail!="") {
				imagejpeg($full_id, $thumbnail, 99);
			} else {
				imagejpeg($full_id, $full_file, 99);
			}
		}
		
		if (preg_match("/\.png$/i", $full_file)) {		
			if($thumbnail!="") {
				imagepng($full_id, $thumbnail);
			} else {
				imagepng($full_id, $full_file);
			}
		}
		
		if (preg_match("/\.gif$/i", $full_file)) {		
			if($thumbnail!="") {
				imagegif($full_id, $thumbnail);
			} else {
				imagegif($full_id, $full_file);
			}
		}
		
		imagedestroy($full_id);
		unset($max_width);
		unset($max_height);
	}
}


// Returns filesystem-safe string after cleaning, filtering, and trimming input
if (!function_exists('str_file_filter')) { 
	function str_file_filter($str, $sep = '_', $strict = false, $trim = 248) {
	
		$str = strip_tags(htmlspecialchars_decode(strtolower($str))); // lowercase -> decode -> strip tags
		$str = str_replace("%20", ' ', $str); // convert rogue %20s into spaces
		$str = preg_replace("/%[a-z0-9]{1,2}/i", '', $str); // remove hexy things
		$str = str_replace("&nbsp;", ' ', $str); // convert all nbsp into space
		$str = preg_replace("/&#?[a-z0-9]{2,8};/i", '', $str); // remove the other non-tag things
		$str = preg_replace("/\s+/", $sep, $str); // filter multiple spaces
		$str = preg_replace("/\.+/", '.', $str); // filter multiple periods
		$str = preg_replace("/^\.+/", '', $str); // trim leading period
	
		if ($strict) {
			$str = preg_replace("/([^\w\d\\" . $sep . ".])/", '', $str); // only allow words and digits
		} else {
			$str = preg_replace("/([^\w\d\\" . $sep . "\[\]\(\).])/", '', $str); // allow words, digits, [], and ()
		}
	
		$str = preg_replace("/\\" . $sep . "+/", $sep, $str); // filter multiple separators
		$str = substr($str, 0, $trim); // trim filename to desired length, note 255 char limit on windows
	
		return $str;
	}
}


if(!function_exists('CurrFormat')){ 
	function CurrFormat($curr, $sign, $amount) {
		if(is_int($amount) or is_float($amount) or is_numeric($amount)) {
		  if ($curr>=1 and $curr<=12) {
			return $sign.number_format($amount, 2, '.', ' ');
		  } else {
			return number_format($amount, 2, '.', ',').'&nbsp;'.$sign;
		  }
		} else {
			if ($curr>=1 and $curr<=12 and trim($amount)!="") {
			return $sign.$amount;
		  } elseif($curr>12 and trim($amount)!="") {
			return $amount.'&nbsp;'.$sign;
		  } else {
			return $amount;
		  }
		}
	}
}

if(!function_exists('breakLongWords')){ 
	function breakLongWords($str, $maxLength, $char=" "){
		$wordEndChars = array(" ", "\n", "\r", "\f", "\v", "\0");
		$count = 0;
		$newStr = "";
		$openTag = false;
		for($i=0; $i<strlen($str); $i++){
			$newStr .= $str{$i};   
		   
			if($str{$i} == "<"){
				$openTag = true;
				continue;
			}
			if(($openTag) && ($str{$i} == ">")){
				$openTag = false;
				continue;
			}
		   
			if(!$openTag){
				if(!in_array($str{$i}, $wordEndChars)){//If not word ending char
					$count++;
					if($count==$maxLength){//if current word max length is reached
						$newStr .= $char;//insert word break char
						$count = 0;
					}
				}else{//Else char is word ending, reset word char count
						$count = 0;
				}
			}
		   
		}//End for   
		return $newStr;
	}
}

// function that get Real Ip Address
if(!function_exists('getRealIpAddr')){ 
	function getRealIpAddr() {
	  if(!empty($_SERVER['HTTP_CLIENT_IP'])) {
		$ip=$_SERVER['HTTP_CLIENT_IP']; // share internet
	  } elseif(!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {
		$ip=$_SERVER['HTTP_X_FORWARDED_FOR']; // pass from proxy
	  } else {
		$ip=$_SERVER['REMOTE_ADDR'];
	  }
	  return $ip;
	}
}

if(!function_exists('addhttp')){ 
	function addhttp($url) {
		if (!preg_match("~^(?:f|ht)tps?://~i", $url)) {
			$url = "http://" . $url;
		}
		return $url;
	}
}

// include php captcha class
include_once (dirname(__FILE__).'/securimage/securimage.php');
// creating an object for phpcaptcha
$securimage = new Securimage();

$configs_set_cl = 1;
?>