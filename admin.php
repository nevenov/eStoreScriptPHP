<?php
//error_reporting(0);
$installed = '';
session_start();
include("configs.php");
include("language_admin.php");

if(isset($_REQUEST["act"])) {
  if ($_REQUEST["act"]=='logout') {
	$_SESSION["ProFiAnTsClassiFiedLoGin"] = "";
	unset($_SESSION["ProFiAnTsClassiFiedLoGin"]);
		
	//setcookie("ProFiAnTsClassiFiedLoGin", "", 0);
	//$_COOKIE["ProFiAnTsClassiFiedLoGin"] = "";	
		
 } elseif ($_REQUEST["act"]=='login') {
  	if ($_REQUEST["user"] == $CONFIG["admin_user"] and $_REQUEST["pass"] == $CONFIG["admin_pass"]) {
		$_SESSION["ProFiAnTsClassiFiedLoGin"] = "LoggedIn";	
		
		//setcookie("ProFiAnTsClassiFiedLoGin", "LoggedIn", time()+8*3600);
		//$_COOKIE["ProFiAnTsClassiFiedLoGin"] = "LoggedIn";			
			
 		$_REQUEST["act"]='ads';
  	} else {
		$logMessage = $lang['Login_message'];
  	}
  }
}
?><!DOCTYPE html>
<html>
<head>
<meta charset="utf-8" />
<title><?php echo $lang['Script_Administration_Header']; ?></title>

<script language="javascript" src="include/jquery-1.11.2.min.js"></script>
<script type="text/javascript" src="accordion/javascript/prototype.js"></script>
<script type="text/javascript" src="accordion/javascript/effects.js"></script>
<script type="text/javascript" src="accordion/javascript/accordion.js"></script>
<script language="javascript" src="include/functions.js"></script>
<script language="javascript" src="include/color_pick.js"></script>
<script type="text/javascript" src="include/datetimepicker_css.js"></script>
<link href="styles/admin.css" rel="stylesheet" type="text/css" />
</head>

<body>

<div class="logo">
	<div class="script_name"><?php echo $lang['Script_Administration_Header']; ?></div>
	<div class="logout_button"><a href="admin.php?act=logout"><img src="images/logout1.png" width="32" alt="Logout" border="0" /></a></div>
    <div class="clear"></div>
</div>

<div style="clear:both"></div>

<?php  
$Logged = false;
//if(isset($_COOKIE["ProFiAnTsClassiFiedLoGin"]) and ($_COOKIE["ProFiAnTsClassiFiedLoGin"]=="LoggedIn")) {
if(isset($_SESSION["ProFiAnTsClassiFiedLoGin"]) and ($_SESSION["ProFiAnTsClassiFiedLoGin"]=="LoggedIn")) {
	$Logged = true;
}
if ( $Logged ){
	
$message = "";

if (isset($_REQUEST["act"]) and $_REQUEST["act"]=='updateOptionsAds') {
	
	$sql = "SELECT * FROM ".$TABLE["Categories"]." ORDER BY cat_name ASC";
	$sql_result = sql_result($sql);
	if (mysqli_num_rows($sql_result)>0) {
		while ($Cat = mysqli_fetch_assoc($sql_result)) {
		
			$sqlA = "SELECT * FROM ".$TABLE["Ads"]."  
					WHERE status='Online' AND `highlight`='true' AND cat_id='".$Cat['id']."'";	
			$sql_resultA = sql_result($sqlA);
			$numHighLight = mysqli_num_rows($sql_resultA);
			if ($numHighLight>$_REQUEST["per_page"]) {
				$message  = 'You have '.$numHighLight.' featured listings in '. $Cat['cat_name'];
				$message .= '. The number of Classified Ads per page can not be less than the number of Featured listings by category.';
				$_REQUEST["per_page"] = $numHighLight;
				break;
			}
		}
	}
	
	if (!isset($_REQUEST["ads_approve"]) or $_REQUEST["ads_approve"]=='') $_REQUEST["ads_approve"] = 'false';
	if (!isset($_REQUEST["del_after_expire"]) or $_REQUEST["del_after_expire"]=='') $_REQUEST["del_after_expire"] = 'hidden';
	if (!isset($_REQUEST["email_after_expire"]) or $_REQUEST["email_after_expire"]=='') $_REQUEST["email_after_expire"] = 'false';
	if (!isset($_REQUEST["showallads_cat"]) or $_REQUEST["showallads_cat"]=='') $_REQUEST["showallads_cat"] = 'no';
	
	$sql = "UPDATE ".$TABLE["Options"]." 
			SET `email`				='".SaveDB($_REQUEST["email"])."',	
				`ads_approve`		='".SaveDB($_REQUEST["ads_approve"])."',	
				`expire_days`		='".SaveDB($_REQUEST["expire_days"])."',	
				`del_after_expire`	='".SaveDB($_REQUEST["del_after_expire"])."',	
				`email_after_expire`='".SaveDB($_REQUEST["email_after_expire"])."',	
				`per_page`			='".SaveDB($_REQUEST["per_page"])."', 
				`char_num`			='".SaveDB($_REQUEST["char_num"])."',
				`paypal_email`		='".SaveDB($_REQUEST["paypal_email"])."',	
				`currency`			='".SaveDB($_REQUEST["currency"])."',
				`default_cat`		='".SaveDB($_REQUEST["default_cat"])."', 
				`showallads_cat`	='".SaveDB($_REQUEST["showallads_cat"])."',
				`char_limit`		='".SaveDB($_REQUEST["char_limit"])."',
				`imgwidth`			='".SaveDB($_REQUEST["imgwidth"])."', 
				`captcha`			='".SaveDB($_REQUEST["captcha"])."', 
				`submit_open_mode`	='".SaveDB($_REQUEST["submit_open_mode"])."', 
				`ban_words`			= '".SafetyDB($_REQUEST["ban_words"])."',
				`smtp_auth`			='".SaveDB($_REQUEST["smtp_auth"])."',
				`smtp_server`		='".SaveDB($_REQUEST["smtp_server"])."',
				`smtp_port`			='".SaveDB($_REQUEST["smtp_port"])."',
				`smtp_email`		='".SaveDB($_REQUEST["smtp_email"])."',
				`smtp_pass`			='".SaveDB($_REQUEST["smtp_pass"])."',
				`smtp_secure`		='".SaveDB($_REQUEST["smtp_secure"])."'";
	$sql_result = sql_result($sql);
	$_REQUEST["act"]='ads_options'; 
	$message = $lang['Message_Main_options_saved'];
	

} elseif (isset($_REQUEST["act"]) and $_REQUEST["act"]=='updateOptionsVisual') {
	
	// general style
	$visual['gen_font_family'] 	= $_REQUEST['gen_font_family']; 
	$visual['gen_font_size'] 	= $_REQUEST['gen_font_size']; 
	$visual['gen_font_color'] 	= $_REQUEST['gen_font_color'];
	$visual['gen_bgr_color'] 	= $_REQUEST['gen_bgr_color'];
	$visual['gen_line_height'] 	= $_REQUEST['gen_line_height'];
	$visual['gen_width'] 		= $_REQUEST['gen_width'];
	
	// column headings on the Classified Ads listing
	$visual['column_color'] 	= $_REQUEST['column_color']; 
	$visual['column_font'] 		= $_REQUEST['column_font']; 
	$visual['column_size'] 		= $_REQUEST['column_size']; 
	$visual['column_font_weight'] = $_REQUEST['column_font_weight']; 
	$visual['column_font_style'] = $_REQUEST['column_font_style']; 
	
	// title in the Classified Ads content
	$visual['title_color'] 		= $_REQUEST['title_color']; 
	$visual['title_font'] 		= $_REQUEST['title_font']; 
	$visual['title_size'] 		= $_REQUEST['title_size']; 
	$visual['title_font_weight'] = $_REQUEST['title_font_weight']; 
	$visual['title_font_style'] = $_REQUEST['title_font_style']; 
	$visual['title_text_align'] = $_REQUEST['title_text_align']; 
	
	// title in the Classified Ads listing
	$visual['summ_title_color'] = $_REQUEST['summ_title_color']; 
	$visual['summ_title_font'] 	= $_REQUEST['summ_title_font']; 
	$visual['summ_title_size'] 	= $_REQUEST['summ_title_size']; 
	$visual['summ_title_font_weight'] = $_REQUEST['summ_title_font_weight']; 
	$visual['summ_title_font_style'] = $_REQUEST['summ_title_font_style']; 
	$visual['summ_title_text_align'] = $_REQUEST['summ_title_text_align']; 
	
	// Classified Ads full description price style 
	$visual['price_color'] 		= $_REQUEST['price_color']; 
	$visual['price_font'] 		= $_REQUEST['price_font']; 
	$visual['price_size'] 		= $_REQUEST['price_size']; 
	$visual['price_font_weight'] = $_REQUEST['price_font_weight'];
	$visual['price_font_style'] = $_REQUEST['price_font_style']; 
	$visual['price_text_align'] = $_REQUEST['price_text_align'];
	
	// Classified Ads full description date style 
	$visual['date_color'] 		= $_REQUEST['date_color']; 
	$visual['date_font'] 		= $_REQUEST['date_font']; 
	$visual['date_size'] 		= $_REQUEST['date_size']; 
	$visual['date_font_weight'] = $_REQUEST['date_font_weight'];
	$visual['date_font_style'] 	= $_REQUEST['date_font_style']; 
	$visual['date_text_align'] 	= $_REQUEST['date_text_align']; 
	$visual['date_format'] 		= $_REQUEST['date_format']; 
	$visual['showing_time'] 	= $_REQUEST['showing_time']; 
	
	// listing price style 
	$visual['summ_price_color'] = $_REQUEST['summ_price_color']; 
	$visual['summ_price_font'] 	= $_REQUEST['summ_price_font']; 
	$visual['summ_price_size'] 	= $_REQUEST['summ_price_size']; 
	$visual['summ_price_font_weight'] = $_REQUEST['summ_price_font_weight'];
	$visual['summ_price_font_style'] = $_REQUEST['summ_price_font_style']; 
	$visual['summ_price_text_align'] = $_REQUEST['summ_price_text_align']; 
	
	// listing date style 
	$visual['summ_date_color'] 	= $_REQUEST['summ_date_color']; 
	$visual['summ_date_font'] 	= $_REQUEST['summ_date_font']; 
	$visual['summ_date_size'] 	= $_REQUEST['summ_date_size']; 
	$visual['summ_date_font_weight'] = $_REQUEST['summ_date_font_weight'];
	$visual['summ_date_font_style'] = $_REQUEST['summ_date_font_style']; 
	$visual['summ_date_text_align'] = $_REQUEST['summ_date_text_align']; 
	$visual['summ_date_format'] = $_REQUEST['summ_date_format']; 
	$visual['summ_showing_time'] = $_REQUEST['summ_showing_time']; 
	
	// visual options for the Classified Ads description 
	$visual['cont_color'] 		= $_REQUEST['cont_color']; 
	$visual['cont_font'] 		= $_REQUEST['cont_font']; 
	$visual['cont_size'] 		= $_REQUEST['cont_size']; 
	$visual['cont_font_style'] 	= $_REQUEST['cont_font_style']; 
	$visual['cont_text_align'] 	= $_REQUEST['cont_text_align']; 
	$visual['cont_line_height'] = $_REQUEST['cont_line_height'];
	
	// visual options in the Classified Ads listing 
	$visual['summ_color'] 		= $_REQUEST['summ_color']; 
	$visual['summ_font'] 		= $_REQUEST['summ_font']; 
	$visual['summ_size'] 		= $_REQUEST['summ_size']; 
	$visual['summ_font_style'] 	= $_REQUEST['summ_font_style']; 
	$visual['summ_text_align'] 	= $_REQUEST['summ_text_align']; 
	$visual['summ_line_height'] = $_REQUEST['summ_line_height']; 
	$visual['summ_show_image'] 	= $_REQUEST['summ_show_image'];
	$visual['summ_img_width'] 	= $_REQUEST['summ_img_width']; 
	
	// highlighted listings
	$visual['hl_bgr_color'] 	= $_REQUEST['hl_bgr_color'];
	
	// pagination style
	$visual['pag_font_size'] 	= $_REQUEST['pag_font_size']; 
	$visual['pag_color'] 		= $_REQUEST['pag_color']; 
	$visual['pag_font_weight'] 	= $_REQUEST['pag_font_weight']; 
	$visual['pag_align'] 		= $_REQUEST['pag_align'];
	
	// navigation links style
	$visual['link_font_size'] 	= $_REQUEST['link_font_size']; 
	$visual['link_color'] 		= $_REQUEST['link_color']; 
	$visual['link_font_weight'] = $_REQUEST['link_font_weight']; 
	$visual['link_align'] 		= $_REQUEST['link_align'];
	
	// 'Email to publisher' link style
	$visual['email_font_size'] 	= $_REQUEST['email_font_size']; 
	$visual['email_color'] 		= $_REQUEST['email_color']; 
	$visual['email_font_weight'] = $_REQUEST['email_font_weight']; 
	$visual['email_font_style'] = $_REQUEST['email_font_style'];
	
	// share this button style
	$visual['show_share_this'] 	= $_REQUEST['show_share_this'];
	$visual['share_this_align'] = $_REQUEST['share_this_align']; 
	$visual['Buttons_size'] 	= $_REQUEST['Buttons_size'];
	
	// distances
	$visual['dist_title_date'] 	= $_REQUEST['dist_title_date'];
	$visual['dist_date_price'] 	= $_REQUEST['dist_date_price'];
	$visual['dist_price_text'] 	= $_REQUEST['dist_price_text'];
	$visual['summ_dist_title_text'] = $_REQUEST['summ_dist_title_text'];
	$visual['dist_date_text'] 	= $_REQUEST['dist_date_text'];
	$visual['dist_btw_entries'] = $_REQUEST['dist_btw_entries'];	
	$visual['dist_link_title'] 	= $_REQUEST['dist_link_title'];
	$visual['dist_image'] 		= $_REQUEST['dist_image'];
	
		
	$visual = serialize($visual);
	
	$sql = "UPDATE ".$TABLE["Options"]." 
			SET `visual`='".SafetyDB($visual)."'";
	$sql_result = sql_result($sql);
	$_REQUEST["act"]='visual_options'; 
	$message = $lang['Message_Visual_options_saved']; 

} elseif (isset($_REQUEST["act"]) and $_REQUEST["act"]=='updateOptionsLanguage') {
	
	// summary listing page
	$language['Back_to_home'] 			= $_REQUEST['Back_to_home']; 
	$language['Submit_Classified_Ad'] 	= $_REQUEST['Submit_Classified_Ad'];
	$language['Category'] 				= $_REQUEST['Category'];
	$language['All_Ads'] 				= $_REQUEST['All_Ads'];
	$language['Read_more'] 				= $_REQUEST['Read_more'];
	$language['Paging'] 				= $_REQUEST['Paging']; 
	$language['No_Classified_Ads_published'] = $_REQUEST['No_Classified_Ads_published']; 
	$language['Search_button'] 			= $_REQUEST['Search_button'];
	
	// main Classified Ad page
	$language['Listed'] 			= $_REQUEST['Listed']; 
	$language['Price'] 				= $_REQUEST['Price'];
	$language['Name'] 				= $_REQUEST['Name']; 
	$language['Location'] 			= $_REQUEST['Location'];
	$language['Email_to_publisher'] = $_REQUEST['Email_to_publisher']; 
	$language['Phone'] 				= $_REQUEST['Phone']; 
	$language['Website'] 			= $_REQUEST['Website'];
	
	// request page
	$language['Your_Name'] 			= $_REQUEST['Your_Name']; 
	$language['Your_Email'] 		= $_REQUEST['Your_Email'];
	$language['Request'] 			= $_REQUEST['Request']; 
	$language['Anti_spam_code'] 	= $_REQUEST['Anti_spam_code']; 
	$language['the_required_fields'] = $_REQUEST['the_required_fields'];
	$language['Send_Request'] 		= $_REQUEST['Send_Request']; 
	$language['Email_request_subject'] = $_REQUEST['Email_request_subject'];
	$language['Request_successfully_sent'] = $_REQUEST['Request_successfully_sent']; 
	$language['Incorrect_anti_spam_code'] = $_REQUEST['Incorrect_anti_spam_code']; 
	$language['Fill_all_the_required_fields'] = $_REQUEST['Fill_all_the_required_fields'];
	$language['Incorrect_mail_address'] = $_REQUEST['Incorrect_mail_address'];
	$language['field_code'] 		= $_REQUEST['field_code']; 	
	
	$language['Classified_Ads_expired'] = $_REQUEST['Classified_Ads_expired']; 	
	
	$language['metatitle'] 			= $_REQUEST['metatitle']; 
	$language['metadescription'] 	= $_REQUEST['metadescription'];
	
	
	$language = serialize($language);
	
	$sql = "UPDATE ".$TABLE["Options"]." 
			SET `language`='".SafetyDB($language)."'";
	$sql_result = sql_result($sql);
	$_REQUEST["act"]='language_options'; 
  	$message = $lang['Message_Language_options_saved'];
 

} elseif (isset($_REQUEST["act"]) and $_REQUEST["act"] == "addAds"){
	
	$sql = "SELECT * FROM ".$TABLE["Options"];
	$sql_result = sql_result($sql);
	$Options = mysqli_fetch_assoc($sql_result);
	$OptionsVis = unserialize($Options['visual']);
	
	if (!isset($_REQUEST["topads"]) or $_REQUEST["topads"]=='') $_REQUEST["topads"] = 'false';
	if (!isset($_REQUEST["highlight"]) or $_REQUEST["highlight"]=='') $_REQUEST["highlight"] = 'false';
	
	if($_REQUEST["highlight"]=='true') {		
		$sqlA = "SELECT * FROM ".$TABLE["Ads"]."  
				 WHERE status='Online' AND `highlight`='true' AND cat_id='".$_REQUEST["cat_id"]."'";	
		$sql_resultA = sql_result($sqlA);
		$numHighLight = mysqli_num_rows($sql_resultA);
		if ($numHighLight==$Options['per_page']) {
			$message  = 'You have '.$numHighLight.' featured listings in that category. ';
			$message .= ' The number of Featured listings in the category can not exceed the number of Classified Ads per page. ';
			$message .= ' Encrease the listings per page first, then add the listing to featured. ';
			$_REQUEST["highlight"] = 'false';
		}
	}
	
	$price = str_replace(" ", "", $_REQUEST["price"]);
	$salePrice = str_replace(" ", "", $_REQUEST["sale_price"]);
	$shipping = str_replace(" ", "", $_REQUEST["shipping"]);
		
	$sql = "INSERT INTO ".$TABLE["Ads"]." 
			SET `publish_date`	= '".SaveDB($_REQUEST["publish_date"])."',
				`expire_date`	= '".SaveDB($_REQUEST["expire_date"])."',
				`status` 		= '".SaveDB($_REQUEST["status"])."',	
				`user_id` 		= '".SaveDB($_REQUEST["user_id"])."',		
				`cat_id` 		= '".SaveDB($_REQUEST["cat_id"])."',		
				`topads` 		= '".SaveDB($_REQUEST["topads"])."',
				`highlight` 	= '".SaveDB($_REQUEST["highlight"])."',
				`title` 		= '".SaveDB($_REQUEST["title"])."',
				`description` 	= '".SaveDB($_REQUEST["description"])."',
				`price` 		= '".SaveDB($price)."',
				`sale_price` 	= '".SaveDB($salePrice)."',
				`shipping` 		= '".SaveDB($shipping)."',
				`name`			= '".SaveDB($_REQUEST["name"])."',
				`address` 		= '".SaveDB($_REQUEST["address"])."',
				`location` 		= '".SaveDB($_REQUEST["location"])."',
				`email` 		= '".SaveDB($_REQUEST["email"])."',
				`phone` 		= '".SaveDB($_REQUEST["phone"])."',
				`website` 		= '".SaveDB($_REQUEST["website"])."',
				`reviews` 		= '0'";
	$sql_result = sql_result($sql);
	
	$index_id = mysqli_insert_id($connCl);
	
	
	//// upload up to 3 images start
	for($i=1; $i<=3; $i++) {
		if (is_uploaded_file($_FILES["image".$i]['tmp_name'])) {
			
			$format_explode = explode(".", $_FILES["image".$i]['name']);
			$format = end($format_explode);					
			$formats = array("jpg","jpeg","JPG","png","PNG","gif","GIF");
			
			if(in_array($format, $formats) and getimagesize($_FILES['image'.$i]['tmp_name'])) { 
				
				$name = str_file_filter($_FILES['image'.$i]['name']);
				$name = $index_id . "_".$i."_" . $name;		
				
				$filePath = $CONFIG["upload_folder"] . $name;
				$thumbPath = $CONFIG["upload_thumbs"] . $name;
				
				if (move_uploaded_file($_FILES["image".$i]['tmp_name'], $filePath)) {				
					chmod($filePath, 0777);
					Resize_File($filePath, 900, 0); 
					Resize_File($filePath, $OptionsVis["summ_img_width"], 0, $thumbPath);
	
					$sql = "UPDATE ".$TABLE["Ads"]."  
							SET `image".$i."` = '".$name."'  
							WHERE id='".$index_id."'";
					$sql_result = sql_result($sql);
					$message .= '';
				} else {
					$message .= 'Cannot copy uploaded file to '.$filePath.'. Try to set the right permissions (CHMOD 777) to '.$CONFIG["upload_folder"];  
				}
			} else {
				$message .= "File ".$i." must be in image format - jpg, png or gif. "; 
			}
		} else { 
			//$message .= "Image ".$i." is not uploaded! ";  
		}
	}
		
	$_REQUEST["act"] = "ads";		
	$message .= $lang['Message_Entry_created'];

} elseif (isset($_REQUEST["act"]) and $_REQUEST["act"]=='updateAds') {
	
	$sql = "SELECT * FROM ".$TABLE["Options"];
	$sql_result = sql_result($sql);
	$Options = mysqli_fetch_assoc($sql_result);
	$OptionsVis = unserialize($Options['visual']);
	
	if (!isset($_REQUEST["topads"]) or $_REQUEST["topads"]=='') $_REQUEST["topads"] = 'false';
	if (!isset($_REQUEST["highlight"]) or $_REQUEST["highlight"]=='') $_REQUEST["highlight"] = 'false';
	
	if($_REQUEST["highlight"]=='true') {		
		$sqlA = "SELECT * FROM ".$TABLE["Ads"]."  
				 WHERE status='Online' AND `highlight`='true' AND cat_id='".$_REQUEST["cat_id"]."'";
		$sql_resultA = sql_result($sqlA);
		$numHighLight = mysqli_num_rows($sql_resultA);
		if ($numHighLight==$Options['per_page']) {
			$message  = 'You have '.$numHighLight.' featured listings in that category. ';
			$message .= ' The number of Featured listings in the category can not exceed the number of Classified Ads per page. ';
			$message .= ' Encrease the listings per page first, then add the listing to featured. ';
			$_REQUEST["highlight"] = 'false';
		}
	}	
	
	$price 	= str_replace(" ", "", $_REQUEST["price"]);
	$salePrice = str_replace(" ", "", $_REQUEST["sale_price"]);
	$shipping = str_replace(" ", "", $_REQUEST["shipping"]);

	$sql = "UPDATE ".$TABLE["Ads"]." 
			SET `publish_date`	= '".SaveDB($_REQUEST["publish_date"])."',
				`expire_date`	= '".SaveDB($_REQUEST["expire_date"])."',
				`status` 		= '".SaveDB($_REQUEST["status"])."',	
				`user_id` 		= '".SaveDB($_REQUEST["user_id"])."',		
				`cat_id` 		= '".SaveDB($_REQUEST["cat_id"])."',		
				`topads` 		= '".SaveDB($_REQUEST["topads"])."',
				`highlight` 	= '".SaveDB($_REQUEST["highlight"])."',
				`title` 		= '".SaveDB($_REQUEST["title"])."',
				`description` 	= '".SaveDB($_REQUEST["description"])."',
				`price` 		= '".SaveDB($price)."',
				`sale_price` 	= '".SaveDB($salePrice)."',
				`shipping` 		= '".SaveDB($shipping)."',
				`name` 			= '".SaveDB($_REQUEST["name"])."',
				`address` 		= '".SaveDB($_REQUEST["address"])."', 
				`location` 		= '".SaveDB($_REQUEST["location"])."',
				`email` 		= '".SaveDB($_REQUEST["email"])."',
				`phone` 		= '".SaveDB($_REQUEST["phone"])."',
				`website` 		= '".SaveDB($_REQUEST["website"])."'  
			WHERE id='".SafetyDB($_REQUEST["id"])."'";
	$sql_result = sql_result($sql);
	
	$sql = "SELECT * FROM ".$TABLE["Ads"]." WHERE id = '".$_REQUEST["id"]."'";
	$sql_result = sql_result($sql);
	$imageArr = mysqli_fetch_assoc($sql_result);
	$imageArr["image1"] = stripslashes($imageArr["image1"]);
	$imageArr["image2"] = stripslashes($imageArr["image2"]);
	$imageArr["image3"] = stripslashes($imageArr["image3"]);
	$imageArr["image4"] = stripslashes($imageArr["image4"]);
	$imageArr["image5"] = stripslashes($imageArr["image5"]);
	
	$index_id = SafetyDB($_REQUEST["id"]);
		
	//// upload up to 3 images start
	for($i=1; $i<=3; $i++) {
		if (is_uploaded_file($_FILES["image".$i]['tmp_name'])) { 
		
			$format_explode = explode(".", $_FILES["image".$i]['name']);
			$format = end($format_explode);					
			$formats = array("jpg","jpeg","JPG","png","PNG","gif","GIF");
				
			if(in_array($format, $formats) and getimagesize($_FILES['image'.$i]['tmp_name'])) {		
			
				if($imageArr["image".$i] != "") unlink($CONFIG["upload_folder"].$imageArr["image".$i]);
				if($imageArr["image".$i] != "") unlink($CONFIG["upload_thumbs"].$imageArr["image".$i]);
				
				$name = str_file_filter($_FILES['image'.$i]['name']);
				$name = $index_id . "_".$i."_" . $name;
				
				$filename = $CONFIG["upload_folder"] . $name;
				$thumbPath = $CONFIG["upload_thumbs"] . $name;
				
				if (move_uploaded_file($_FILES["image".$i]['tmp_name'], $filename)) {
					chmod($filename,0777); 
					Resize_File($filename, 900, 0); 
					Resize_File($filename, $OptionsVis["summ_img_width"], 0, $thumbPath);
	
					
					$sql = "UPDATE `".$TABLE["Ads"]."` 
							SET `image".$i."` = '".SafetyDB($name) ."' 
							WHERE id = '".$index_id."'";
					$sql_result = sql_result($sql);
				} else {
					$message .= 'Cannot copy uploaded file to '.$filePath.'. Try to set the right permissions (CHMOD 777) to '.$CONFIG["upload_folder"];  
				}
			} else {
				$message .= "File ".$i." must be in image format - jpg, png or gif. "; 
			}
		}
	}
	
	if(isset($_REQUEST["updatepreview"]) and $_REQUEST["updatepreview"]!='') {
		$_REQUEST["act"]='viewAds'; 		
	} else {
		$_REQUEST["act"]='ads'; 
	}
	$message .= $lang['Message_Entry_updated'];
	

} elseif (isset($_REQUEST["act"]) and $_REQUEST["act"]=='renewEx') {	
	
	$sql = "SELECT * FROM ".$TABLE["Options"];
	$sql_result = sql_result($sql);
	$Options = mysqli_fetch_assoc($sql_result);
	
	$expire_date = date("Y-m-d H:i:s", mktime(date("H"), date("i"), date("s"), date("m"), date("d")+$Options['expire_days'], date("Y")));
	
	$sql = "UPDATE ".$TABLE["Ads"]." 
			SET `publish_date`	= now(),
				`expire_date`	= '".SaveDB($expire_date)."',
				`status`		= 'Online' 
			WHERE id='".SafetyDB($_REQUEST["id"])."'";
	$sql_result = sql_result($sql);
	
	$_REQUEST["act"]='ads'; 
	$message = $lang['Message_Entry_renewed'];
	
	
} elseif (isset($_REQUEST["act"]) and $_REQUEST["act"]=='delAds') {
	
	$sql = "SELECT * FROM ".$TABLE["Ads"]." WHERE id = '".$_REQUEST["id"]."'";
	$sql_result = sql_result($sql);
	$imageArr = mysqli_fetch_assoc($sql_result);
	for($i=1; $i<=5; $i++) {
		$imageArr["image".$i] = stripslashes($imageArr["image".$i]);
		if($imageArr["image".$i] != "") unlink($CONFIG["upload_folder"].$imageArr["image".$i]);
		if($imageArr["image".$i] != "") unlink($CONFIG["upload_thumbs"].$imageArr["image".$i]);
	}

	$sql = "DELETE FROM ".$TABLE["Ads"]." WHERE id='".$_REQUEST["id"]."'";
   	$sql_result = sql_result($sql);	
 	$_REQUEST["act"]='ads'; 
	$message = $lang['Message_Entry_deleted'];
	
} elseif (isset($_REQUEST["act"]) and $_REQUEST["act"]=="delImage") { 
	
	$sql = "SELECT * FROM ".$TABLE["Ads"]." WHERE id = '".$_REQUEST["id"]."'";
	$sql_result = sql_result($sql);
	$imageArr = mysqli_fetch_assoc($sql_result);
	
	$i = SafetyDB($_REQUEST["imgnum"]);
	
	$imageArr["image".$i] = stripslashes($imageArr["image".$i]);
	if($imageArr["image".$i] != "") unlink($CONFIG["upload_folder"].$imageArr["image".$i]);
	if($imageArr["image".$i] != "") unlink($CONFIG["upload_thumbs"].$imageArr["image".$i]);
		
	$sql = "UPDATE `".$TABLE["Ads"]."` SET `image".$i."` = '' WHERE id = '".SafetyDB($_REQUEST["id"])."'";
	$sql_result = sql_result($sql);
	
	$_REQUEST["act"] = "editAds";
	$message = $lang['Message_Image_deleted'];
	
} elseif (isset($_REQUEST["act2"]) and $_REQUEST["act2"]=="change_status_ads") { 
	
	$sql = "UPDATE ".$TABLE["Ads"]." 
			SET `status` = '".SaveDB($_REQUEST["status"])."' 
			WHERE id='".$_REQUEST["id"]."'";
	$sql_result = sql_result($sql);
	
	$_REQUEST["act"] = "ads";
	$message = $lang['Message_Entry_Status_Updated'];


} elseif (isset($_REQUEST["act"]) and $_REQUEST["act"] == "addCat"){
	
	$sql = "INSERT INTO ".$TABLE["Categories"]." 
			SET `cat_name` = '".SaveDB($_REQUEST["cat_name"])."'";
	$sql_result = sql_result($sql);
		
	$_REQUEST["act"] = "cats";		
	$message = $lang['Message_Category_created'];
	
	
} elseif (isset($_REQUEST["act"]) and $_REQUEST["act"] == "updateCat"){
	
	$sql = "UPDATE ".$TABLE["Categories"]." 
			SET `cat_name` = '".SaveDB($_REQUEST["cat_name"])."' 
			WHERE id='".$_REQUEST["id"]."'";
	$sql_result = sql_result($sql);
		
	$_REQUEST["act"] = "cats";		
	$message = $lang['Message_Category_updated'];
	
} elseif (isset($_REQUEST["act"]) and $_REQUEST["act"]=='delCat') {
	
	$sql = "DELETE FROM ".$TABLE["Categories"]." WHERE id='".$_REQUEST["id"]."'";
   	$sql_result = sql_result($sql);
 	$_REQUEST["act"]='cats'; 
	$message = $lang['Message_Category_deleted'];


///////////////// MANAGE USERS //////////////////	
} elseif (isset($_REQUEST["act"]) and $_REQUEST["act"] == "addUser"){
	
	$sql = "SELECT * FROM ".$TABLE["Users"]." WHERE user_email='".trim($_REQUEST["user_email"])."'";
	$sql_result = sql_result($sql);
	if(mysqli_num_rows($sql_result)==0) {
	
		$sql = "INSERT INTO ".$TABLE["Users"]." 
				SET `reg_date` 		= now(),
					`status`		= '".SaveDB($_REQUEST["status"])."', 
					`user_ip` 		= '".SaveDB(getRealIpAddr())."', 
					`user_email` 	= '".SaveDB($_REQUEST["user_email"])."', 
					`user_password` = '".SaveDB($_REQUEST["user_password"])."', 
					`user_name` 	= '".SaveDB($_REQUEST["user_name"])."',
					`user_address`	= '".SaveDB($_REQUEST["user_address"])."', 
					`user_location` = '".SaveDB($_REQUEST["user_location"])."',  
					`user_phone` 	= '".SaveDB($_REQUEST["user_phone"])."',   
					`user_url` 		= '".SaveDB($_REQUEST["user_url"])."'";
		$sql_result = sql_result($sql);
			
		$_REQUEST["act"] = "users";	
		$message = $lang['Message_User_created'];
	} else {
		$_REQUEST["act"] = "newUser";		
		$message = $lang['Message_User_exist'];
	}
	
	
} elseif (isset($_REQUEST["act"]) and $_REQUEST["act"] == "updateUser"){
		
	$sql = "UPDATE ".$TABLE["Users"]." 
			SET `status`		= '".SaveDB($_REQUEST["status"])."', 
				`user_email` 	= '".SaveDB($_REQUEST["user_email"])."', 
				`user_password` = '".SaveDB($_REQUEST["user_password"])."', 
				`user_name` 	= '".SaveDB($_REQUEST["user_name"])."',
				`user_address`	= '".SaveDB($_REQUEST["user_address"])."', 
				`user_location` = '".SaveDB($_REQUEST["user_location"])."',  
				`user_phone` 	= '".SaveDB($_REQUEST["user_phone"])."',   
				`user_url` 		= '".SaveDB($_REQUEST["user_url"])."'   
			WHERE id='".$_REQUEST["id"]."'";
	$sql_result = sql_result($sql);
		
	$_REQUEST["act"] = "users";	
	$message = $lang['Message_User_updated'];

} elseif (isset($_REQUEST["act"]) and $_REQUEST["act"]=='delUser') {
	
	$sql = "DELETE FROM ".$TABLE["Users"]." WHERE id='".$_REQUEST["id"]."'";
   	$sql_result = sql_result($sql);
 	$_REQUEST["act"]='users'; 
	$message = $lang['Message_User_deleted'];

}

if (!isset($_REQUEST["act"]) or $_REQUEST["act"]=='') $_REQUEST["act"]='ads';
?> 
    

<div class="menuButtons">
    <div class="menuButton"><a<?php if($_REQUEST['act']=='ads' or $_REQUEST['act']=='newAds' or $_REQUEST['act']=='viewAds' or $_REQUEST['act']=='editAds' or $_REQUEST["act"]=='exads' or $_REQUEST['act']=='rss') echo ' class="selected"'; ?> href="admin.php?act=ads"><span><?php echo $lang['menu_Button_1']; ?></span></a></div>
    <div class="menuButton"><a<?php if($_REQUEST['act']=='cats' or $_REQUEST['act']=='newCat' or $_REQUEST['act']=='editCat') echo ' class="selected"'; ?> href="admin.php?act=cats"><span><?php echo $lang['menu_Button_2']; ?></span></a></div>
    <!-- <div class="menuButton"><a<?php if($_REQUEST['act']=='users' or $_REQUEST['act']=='newUser' or $_REQUEST['act']=='editUser') echo ' class="selected"'; ?> href="admin.php?act=users"><span><?php echo $lang['menu_Button_3']; ?></span></a></div> -->
    
    <div class="menuButton"><a<?php if($_REQUEST['act']=='orders' or $_REQUEST['act']=='editOrder' or $_REQUEST['act']=='viewOrder') echo ' class="selected"'; ?> href="admin.php?act=orders"><span><?php echo $lang['menu_Button_6']; ?></span></a></div>
    
    <!-- <div class="menuButton"><a<?php if($_REQUEST['act']=='users' or $_REQUEST['act']=='newUser' or $_REQUEST['act']=='editUser') echo ' class="selected"'; ?> href="admin.php?act=users"><span><?php echo $lang['menu_Button_3']; ?></span></a></div> -->
    <div class="menuButton"><a<?php if($_REQUEST['act']=='ads_options' or $_REQUEST['act']=='visual_options' or $_REQUEST['act']=='language_options') echo ' class="selected"'; ?> href="admin.php?act=ads_options"><span><?php echo $lang['menu_Button_4']; ?></span></a></div>
    <div class="menuButton"><a<?php if($_REQUEST['act']=='html') echo ' class="selected"'; ?> href="admin.php?act=html"><span><?php echo $lang['menu_Button_5']; ?></span></a></div>
    <div class="clear"></div>        
</div>


<div class="admin_wrapper">

	<?php
    if ($_REQUEST["act"]=='ads' or $_REQUEST["act"]=='newAds' or $_REQUEST["act"]=='editAds' or $_REQUEST["act"]=='viewAds' or $_REQUEST["act"]=='exads' or $_REQUEST["act"]=='rss') {
		$sqlExp   = "SELECT id FROM ".$TABLE["Ads"]." WHERE status='Expired'";
		$sql_resultExp = sql_result($sqlExp);
		$AdsExp = mysqli_num_rows($sql_resultExp);
		if($AdsExp>0) { $numExAds = "(".$AdsExp.")"; } else { $numExAds = ""; }
    ?>	
    <div class="menuSubButton"><a<?php if($_REQUEST['act']=='ads' or $_REQUEST['act']=='editAds' or $_REQUEST["act"]=='viewAds') echo ' class="selected"'; ?> href="admin.php?act=ads"><?php echo $lang['submenu1_button1']; ?></a></div>
    <div class="menuSubButton"><a<?php if($_REQUEST['act']=='newAds') echo ' class="selected"'; ?> href="admin.php?act=newAds"><?php echo $lang['submenu1_button2']; ?></a></div>
    <div class="menuSubButton"><a<?php if($_REQUEST['act']=='exads' or $_REQUEST['act']=='editAds' or $_REQUEST["act"]=='viewAds') echo ' class="selected"'; ?> href="admin.php?act=exads"><?php echo $lang['submenu1_button5']; ?>  <?php echo $numExAds; ?></a></div>
    <div class="menuSubButton"><a href="preview.php" target="_blank"><?php echo $lang['submenu1_button3']; ?></a></div>
    <div class="menuSubButton"><a<?php if($_REQUEST['act']=='rss') echo ' class="selected"'; ?> href="admin.php?act=rss" style="background:none;"><?php echo $lang['submenu1_button4']; ?></a></div>
    <div class="clear"></div>        

    <?php
    } elseif ($_REQUEST["act"]=='cats' or $_REQUEST["act"]=='newCat' or $_REQUEST["act"]=='editCat') {
    ?>	
    <div class="menuSubButton"><a<?php if($_REQUEST['act']=='cats') echo ' class="selected"'; ?> href="admin.php?act=cats"><?php echo $lang['submenu2_button1']; ?></a></div>
    <div class="menuSubButton"><a<?php if($_REQUEST['act']=='newCat') echo ' class="selected"'; ?> href="admin.php?act=newCat" style="background:none;"><?php echo $lang['submenu2_button2']; ?></a></div>
    <div class="clear"></div>        
	
    
    <?php
	} elseif ($_REQUEST["act"]=='users' or $_REQUEST["act"]=='newUser' or $_REQUEST["act"]=='editUser') {
	?>
    <div class="menuSubButton"><a<?php if($_REQUEST['act']=='users') echo ' class="selected"'; ?> href="admin.php?act=users"><?php echo $lang['submenu3_button1']; ?></a></div>
    <div class="menuSubButton"><a<?php if($_REQUEST['act']=='newUser') echo ' class="selected"'; ?> href="admin.php?act=newUser"><?php echo $lang['submenu3_button2']; ?></a></div>
    <div class="menuSubButton"><a href="user.php" target="_blank" style="background:none;"><?php echo $lang['submenu3_button3']; ?></a></div>
    <div class="clear"></div>        
    
           
    <?php
	} elseif ($_REQUEST["act"]=='orders' or $_REQUEST["act"]=='editOrder' or $_REQUEST["act"]=='viewOrder') {
	?>
    <div class="menuSubButton"><a<?php if($_REQUEST['act']=='orders') echo ' class="selected"'; ?> href="admin.php?act=orders"><?php echo $lang['submenu6_button1']; ?></a></div>
    <div class="clear"></div>        


    <?php
    } elseif ($_REQUEST["act"]=='ads_options' or $_REQUEST["act"]=='visual_options' or $_REQUEST["act"]=='visual_options_top' or $_REQUEST["act"]=='language_options') { 
    ?>	
    <div class="menuSubButton"><a<?php if($_REQUEST['act']=='ads_options') echo ' class="selected"'; ?> href="admin.php?act=ads_options"><?php echo $lang['submenu4_button1']; ?></a></div>
    <div class="menuSubButton"><a<?php if($_REQUEST['act']=='visual_options') echo ' class="selected"'; ?> href="admin.php?act=visual_options"><?php echo $lang['submenu4_button2']; ?></a></div>
    <div class="menuSubButton"><a<?php if($_REQUEST['act']=='language_options') echo ' class="selected"'; ?> href="admin.php?act=language_options" style="background:none;"><?php echo $lang['submenu4_button3']; ?></a></div>
    <div class="clear"></div>        
    <?php } ?>
    

	<?php if(isset($message) and $message!='') {?>
    <div class="message<?php if($_REQUEST['act']=='comments' or $_REQUEST['act']=='editComment') echo ' comm_marg'; ?>"><?php echo $message; ?></div>
    <?php } ?>
    <script type="text/javascript">	
	jQuery.noConflict();
	jQuery(document).ready(function(){
		setTimeout(function(){
			jQuery("div.message").fadeOut("slow", function () {
				jQuery("div.message").remove();
			});
	 
		}, 3500);
	 });
	</script>
    

<?php 
if ($_REQUEST["act"]=='ads') {
	
	if(isset($_REQUEST["search"]) and $_REQUEST["search"]!='') {
		$_REQUEST["search"] = htmlspecialchars(urldecode($_REQUEST["search"]), ENT_QUOTES);
	} else { 
		$_REQUEST["search"] = ''; 
	}
	
	$sql = "SELECT * FROM ".$TABLE["Options"];
	$sql_result = sql_result($sql);
	$Options = mysqli_fetch_assoc($sql_result);
	$OptionsVis = unserialize($Options['visual']); 
	$OptionsLang = unserialize($Options['language']);
		
	if(isset($_REQUEST["p"]) and $_REQUEST["p"]!='') { 
		$pageNum = (int) SafetyDB(urldecode($_REQUEST["p"]));
		if($pageNum<=0) $pageNum = 1;
	} else { 
		$pageNum = 1;
	}
	
	$orderByArr = array("title", "publish_date", "status", "cat_id", "user_id", "reviews");
	if(isset($_REQUEST["orderBy"]) and $_REQUEST["orderBy"]!='' and in_array($_REQUEST["orderBy"], $orderByArr)) { 
		$orderBy = $_REQUEST["orderBy"];
	} else { 
		$orderBy = "publish_date";
	}
	
    $orderTypeArr = array("DESC", "ASC");	
    if(isset($_REQUEST["orderType"]) and $_REQUEST["orderType"]!='' and in_array($_REQUEST["orderType"], $orderTypeArr)) { 
		$orderType = $_REQUEST["orderType"];
	} else {
		$orderType = "DESC";
	}
	if ($orderType == 'DESC') { $norderType = 'ASC'; } else { $norderType = 'DESC'; }
	
	$sqlOnline   = "SELECT id FROM ".$TABLE["Ads"]." WHERE status='Online'";
	$sql_resultOnline = sql_result($sqlOnline);
	$AdsOnline = mysqli_num_rows($sql_resultOnline);
	
	$sqlExp   = "SELECT id FROM ".$TABLE["Ads"]." WHERE status='Expired'";
	$sql_resultExp = sql_result($sqlExp);
	$AdsExp = mysqli_num_rows($sql_resultExp);
	
	$sqlCount   = "SELECT id FROM ".$TABLE["Ads"]; 
	$sql_resultCount = sql_result($sqlCount);
	$AdsCount = mysqli_num_rows($sql_resultCount);
	
	$AdsWaitApproval = $AdsCount - $AdsExp - $AdsOnline;
?>
	<div class="pageDescr"><?php echo $lang['List_Dashboard1']; ?> <strong style="font-size:16px"><?php echo $AdsOnline; ?></strong> <?php echo $lang['List_Dashboard2']; ?> <strong style="font-size:16px"><?php echo $AdsWaitApproval; ?></strong>.</div>
    
    <div class="searchForm">
    <form action="admin.php?act=ads" method="post" name="form" class="formStyle">
      <input type="text" name="search" value="<?php echo $_REQUEST["search"]; ?>" class="searchfield" placeholder="Product/Service title" />
      <input type="submit" value="<?php echo $lang['List_Search_Button']; ?>" class="searchButton" />
    </form>
    </div>
    
	<table border="0" cellspacing="0" cellpadding="8" class="allTables">
  	  <tr>
        <td class="headlist"><a href="admin.php?act=ads&orderType=<?php echo $norderType; ?>&search=<?php if(isset($_REQUEST["search"])) echo urlencode($_REQUEST["search"]); ?>&orderBy=title&p=<?php if(isset($_REQUEST["p"])) echo $_REQUEST['p']; ?>"><?php echo $lang['List_Product_Service']; ?></a></td>
        <td width="16%" class="headlist"><a href="admin.php?act=ads&orderType=<?php echo $norderType; ?>&search=<?php if(isset($_REQUEST["search"])) echo urlencode($_REQUEST["search"]); ?>&orderBy=publish_date&p=<?php if(isset($_REQUEST["p"])) echo $_REQUEST['p']; ?>"><?php echo $lang['List_Date_Activity']; ?></a></td>
        <td width="9%" class="headlist"><a href="admin.php?act=ads&orderType=<?php echo $norderType; ?>&search=<?php if(isset($_REQUEST["search"])) echo urlencode($_REQUEST["search"]); ?>&orderBy=status&p=<?php if(isset($_REQUEST["p"])) echo $_REQUEST['p']; ?>"><?php echo $lang['List_Status']; ?></a></td>
        <td width="10%" class="headlist"><a href="admin.php?act=ads&orderType=<?php echo $norderType; ?>&search=<?php if(isset($_REQUEST["search"])) echo urlencode($_REQUEST["search"]); ?>&orderBy=cat_id&p=<?php if(isset($_REQUEST["p"])) echo $_REQUEST['p']; ?>"><?php echo $lang['List_Category']; ?></a></td>
        <td width="10%" class="headlist"><a href="admin.php?act=ads&orderType=<?php echo $norderType; ?>&search=<?php if(isset($_REQUEST["search"])) echo urlencode($_REQUEST["search"]); ?>&orderBy=user_id&p=<?php if(isset($_REQUEST["p"])) echo $_REQUEST['p']; ?>"><?php echo $lang['List_User']; ?></a></td>
        <td width="6%" class="headlist"><a href="admin.php?act=ads&orderType=<?php echo $norderType; ?>&search=<?php if(isset($_REQUEST["search"])) echo urlencode($_REQUEST["search"]); ?>&orderBy=reviews&p=<?php if(isset($_REQUEST["p"])) echo $_REQUEST['p']; ?>"><?php echo $lang['List_Views']; ?></a></td>
        <td class="headlist" colspan="3">&nbsp;</td>
  	  </tr>
      
  	<?php 
	if(isset($_REQUEST["search"]) and ($_REQUEST["search"]!="")) {
	  $findMe = SafetyDB($_REQUEST["search"]);
	  $search = " AND title LIKE '%".$findMe."%'";
	} else {
	  $search = '';
	}

	$sql   = "SELECT count(*) as total FROM ".$TABLE["Ads"]." WHERE status<>'Expired' ".$search;
	$sql_result = sql_result($sql);
	$row   = mysqli_fetch_array($sql_result);
	$count = $row["total"];
	$pages = ceil($count/30);

	$sql = "SELECT * FROM ".$TABLE["Ads"]." WHERE status<>'Expired' ".$search." 
			ORDER BY " . $orderBy . " " . $orderType."  
			LIMIT " . ($pageNum-1)*30 . ",30";
	$sql_result = sql_result($sql);
	
	if (mysqli_num_rows($sql_result)>0) {
		$i=1;	
		while ($Ads = mysqli_fetch_assoc($sql_result)) {			
	?>
  	  <tr>
        <td class="bodylist" style="font-weight:normal; <?php if($Ads["highlight"]=='true') { echo ' border-left:solid 4px #3877b8;'; }?>"><?php echo ReadDB($Ads["title"]); ?></td>
        <td class="bodylist">
        	From: <?php echo admin_date($Ads["publish_date"]); ?><br />
        	To: <?php echo admin_date($Ads["expire_date"]); ?>
		</td>
        <td class="bodylist">
			<form action="admin.php?act=ads" method="post" name="form<?php echo $i; ?>" class="formStyle">
            <input type="hidden" name="act2" value="change_status_ads" />
            <input type="hidden" name="id" value="<?php echo $Ads["id"]; ?>" />
            <select name="status" onChange="document.form<?php echo $i; ?>.submit()">
				<option value="Online" <?php if($Ads['status']=='Online') echo "selected='selected'"; ?>><?php echo $lang['List_Online']; ?></option>
				<option value="Waiting" <?php if($Ads['status']=='Waiting') echo "selected='selected'"; ?>><?php echo $lang['List_Waiting']; ?></option>
                <option value="Expired" <?php if($Ads['status']=='Expired') echo "selected='selected'"; ?>><?php echo $lang['List_Expired']; ?></option>
            </select>
            </form>			
        </td>        
        <td class="bodylist">
        	<?php 
			$sqlCat = "SELECT * FROM ".$TABLE["Categories"]." WHERE id='".$Ads["cat_id"]."'";
			$sql_resultCat = sql_result($sqlCat);
			$Cat = mysqli_fetch_assoc($sql_resultCat);	
			if($Cat["id"]>0) echo ReadDB($Cat["cat_name"]); else echo "------"; ?>
        </td>
        <td class="bodylist">
        	<?php 
			if(isset($Ads["user_id"]) and $Ads["user_id"]>0) {
			$sqlU = "SELECT * FROM ".$TABLE["Users"]." WHERE id='".$Ads["user_id"]."'";
			$sql_resultU = sql_result($sqlU);
			$User = mysqli_fetch_assoc($sql_resultU);	
			if($User["id"]>0) echo ReadDB($User["user_name"]); 
			} else {
				echo "Admin"; 	
			}
			?>
        </td>
        <td class="bodylist"><?php if($Ads["reviews"]=='') echo "0"; else echo $Ads["reviews"]; ?></td>
        <td class="bodylistAct"><a class="view" href='admin.php?act=viewAds&id=<?php echo $Ads["id"]; ?>' title="Preview"><img class="act" src="images/preview.png" alt="Preview" /></a></td>
        <td class="bodylistAct"><a href='admin.php?act=editAds&id=<?php echo $Ads["id"]; ?>' title="Edit"><img class="act" src="images/edit.png" alt="Edit" /></a></td>
        <td class="bodylistAct"><a class="delete" href="admin.php?act=delAds&id=<?php echo $Ads["id"]; ?>" onclick="return confirm('Are you sure you want to delete it?');" title="DELETE"><img class="act" src="images/delete.png" alt="DELETE" /></a></td>
  	  </tr>
  	<?php 
			$i++;
		}
	} else {
	?>
      <tr>
      	<td colspan="11" style="border-bottom:1px solid #CCCCCC"><?php echo $lang['List_No_Entries']; ?></td>
      </tr>
    <?php	
	}
	?>
    
	<?php
    if ($pages>0) {
    ?>
  	  <tr>
      	<td colspan="11" class="bottomlist"><div class='paging'><?php echo $lang['List_Page']; ?> </div>
		<?php
        for($i=1;$i<=$pages;$i++){ 
            if($i == $pageNum ) echo "<div class='paging'>" .$i. "</div>";
            else echo "<a href='admin.php?act=ads&p=".$i."&search=".$_REQUEST["search"]."&amp;orderBy=".$_REQUEST["orderBy"]."&amp;orderType=".$_REQUEST["orderType"]."' class='paging'>".$i."</a>"; 
            echo "&nbsp; ";
        }
        ?>
      	</td>
      </tr>
	<?php
    }
    ?>
      <tr>
      	<td colspan="9">
            <table class="table_bottom">
              <tr>
                <td width="12px" style="background-color:#3877b8;">&nbsp;</td>
                <td style="padding-left:4px; text-align:left;"> <?php echo $lang['List_featured']; ?></td>
              </tr>
            </table>
    	</td>
      </tr>
	</table>


<?php 
} elseif ($_REQUEST["act"]=='newAds') { 
	$sql = "SELECT * FROM ".$TABLE["Options"];
	$sql_result = sql_result($sql);
	$Options = mysqli_fetch_assoc($sql_result);
?>
	<form action="admin.php" method="post" name="form" enctype="multipart/form-data">
  	<input type="hidden" name="act" value="addAds" />
  	<div class="pageDescr"><?php echo $lang['Create_Listing_Dashboard']; ?></div>
	<table border="0" cellspacing="0" cellpadding="8" class="fieldTables">
      <tr>
      	<td colspan="2" valign="top" class="headlist"><?php echo $lang['Create_Listing_Header']; ?></td>
      </tr>
      <tr>
      	<td class="formLeft"><?php echo $lang['Create_Listing_Status']; ?></td>
      	<td class="formRight">
            <select name="status">
              <option value="Online"><?php echo $lang['Create_Listing_Online']; ?></option>
              <option value="Waiting"><?php echo $lang['Create_Listing_Waiting']; ?></option>
            </select>
      	</td>
      </tr>
      
      <?php
	  $sql = "SELECT * FROM ".$TABLE["Users"]." ORDER BY user_name ASC";
	  $sql_result = sql_result($sql);
	  ?> 
      <tr>
      	<td><?php echo $lang['Create_Listing_User']; ?> </td>
      	<td>
        	<select name="user_id">
            	<option value="0">Admin</option>
			<?php
            while ($User = mysqli_fetch_assoc($sql_result)) {
            ?>
         		<option value="<?php echo $User["id"]; ?>"><?php if(trim($User["user_name"])!='') {echo ReadDB($User["user_name"]);} else {echo ReadDB($User["user_email"]);} ?></option>
            <?php
			} 
			?>
      		</select>
		</td>
      </tr>   
      
      <tr>
        <td class="formLeft"><?php echo $lang['Create_Listing_Title']; ?> </td>
        <td class="formRight"><input class="input_post" type="text" name="title" maxlength="120" required /></td>
      </tr>
      
      <tr>
      	<td class="formLeft"><?php echo $lang['Create_Listing_Category']; ?> </td>
      	<td class="formRight">
        	<select name="cat_id">
			<?php
            $sql = "SELECT * FROM ".$TABLE["Categories"]." ORDER BY cat_name ASC";
            $sql_result = sql_result($sql);
            if (mysqli_num_rows($sql_result)>0) {
              while ($Cat = mysqli_fetch_assoc($sql_result)) {
            ?>
         		<option value="<?php echo $Cat["id"]; ?>"><?php echo ReadDB($Cat["cat_name"]); ?></option>
            <?php
			  }
			} 
			?>
      		</select>
		</td>
      </tr>  
      <tr>
        <td class="formLeft"><?php echo $lang['Create_Listing_Featured']; ?> </td>
        <td class="formRight"><input name="highlight" id="Featured" type="checkbox" value="true" /> <label for="Featured"><?php echo $lang['Create_Listing_Info']; ?></label></td>
      </tr> 
      
      <tr>
        <td class="formLeft"><?php echo $lang['Create_Listing_Price']; ?></td>
        <td class="formRight"><input type="number" min="0.00" max="1000000.00" step="0.01" name="price" size="12" required /> <?php echo $CurrAbr[$Options["currency"]]; ?> </td>
      </tr> 
      
      <tr>
        <td class="formLeft"><?php echo $lang['Create_Listing_Shipping']; ?></td>
        <td class="formRight"><input type="number" min="0.00" max="1000000.00" step="0.01" name="shipping" size="12" /> <?php echo $CurrAbr[$Options["currency"]]; ?> </td>
      </tr>      
      
      <tr>
        <td class="formLeft"><?php echo $lang['Create_Listing_sPrice']; ?></td>
        <td class="formRight"><input type="number" min="0.00" max="1000000.00" step="0.01" name="sale_price" size="12" /> <?php echo $CurrAbr[$Options["currency"]]; ?> </td>
      </tr>
      
      <tr>
        <td class="formLeft" valign="top"><?php echo $lang['Create_Listing_Description']; ?></td>
        <td class="formRight"><textarea class="post_text" name="description" rows="8"></textarea></td>
      </tr>
      <?php for($i=1; $i<=3; $i++) { ?>      
      <tr>
        <td class="formLeft"><?php echo $lang['Create_Listing_Image'.$i]; ?></td>
        <td class="formRight"><input type="file" name="image<?php echo $i; ?>" size="80" /> <sub><?php echo $lang['Create_Listing_Limit_Mb']; ?> </sub></td>
      </tr> 
      <?php } ?>
      
      <tr>
        <td colspan="2"><div class="border_bottom"></div></td>
      </tr> 
         
      <tr>
        <td class="formLeft"><?php echo $lang['Create_Listing_Name']; ?></td>
        <td class="formRight"><input class="input_details" type="text" name="name" maxlength="250" /></td>
      </tr>      
      <tr>
        <td class="formLeft"><?php echo $lang['Create_Listing_Address']; ?></td>
        <td class="formRight"><input class="input_details" type="text" name="address" maxlength="250" /></td>
      </tr>      
      <tr>
        <td class="formLeft"><?php echo $lang['Create_Listing_Location']; ?></td>
        <td class="formRight"><input class="input_details" type="text" name="location" maxlength="250" /></td>
      </tr>
      <tr>
        <td class="formLeft"><?php echo $lang['Create_Listing_Email']; ?></td>
        <td class="formRight"><input class="input_details" type="text" name="email" maxlength="250" /></td>
      </tr>
      <tr>
        <td class="formLeft"><?php echo $lang['Create_Listing_Phone']; ?></td>
        <td class="formRight"><input class="input_details" type="text" name="phone" maxlength="250" /></td>
      </tr> 
      <tr>
        <td class="formLeft"><?php echo $lang['Create_Listing_Website']; ?></td>
        <td class="formRight"><input class="input_details" type="text" name="website" maxlength="250" /></td>
      </tr> 
      
      <tr>
        <td colspan="2"><div class="border_bottom"></div></td>
      </tr> 
            
      <tr>
        <td class="formLeft"><?php echo $lang['Create_Listing_PDate']; ?></td>
        <td class="formRight">
      		<input type="text" name="publish_date" id="publish_date" maxlength="25" size="25" value="<?php echo date("Y-m-d H:i:s"); ?>" readonly /> <a href="javascript:NewCssCal('publish_date','yyyymmdd','dropdown',true,24,false)"><img src="images/cal.gif" width="16" height="16" alt="Pick a date" border="0" /></a>
        </td>
      </tr>
      <tr>
        <td class="formLeft"><?php echo $lang['Create_Listing_EDate']; ?></td>
        <td class="formRight">
      		<input type="text" name="expire_date" id="expire_date" maxlength="25" size="25" value="<?php echo date("Y-m-d H:i:s", mktime(date("H"), date("i"), date("s"), date("m"), date("d")+$Options['expire_days'], date("Y"))); ?>" readonly /> <a href="javascript:NewCssCal('expire_date','yyyymmdd','dropdown',true,24,false)"><img src="images/cal.gif" width="16" height="16" alt="Pick a date" border="0" /></a>
        </td>
      </tr>      
      <tr>
        <td>&nbsp;</td>
        <td class="formRight"><input name="submit" type="submit" value="<?php echo $lang['Create_Listing_button']; ?>" class="submitButton" /></td>
      </tr>
  	</table>
	</form>
    

<?php 
} elseif ($_REQUEST["act"]=='editAds') {
	$sql = "SELECT * FROM ".$TABLE["Options"];
	$sql_result = sql_result($sql);
	$Options = mysqli_fetch_assoc($sql_result);
	
	$_REQUEST["id"]= (int) SafetyDB($_REQUEST["id"]);
	$sql = "SELECT * FROM ".$TABLE["Ads"]." WHERE id='".$_REQUEST["id"]."'";
	$sql_result = sql_result($sql);
	$Ads = mysqli_fetch_assoc($sql_result);
?>
	<form action="admin.php" method="post" name="form" enctype="multipart/form-data">
  	<input type="hidden" name="act" value="updateAds" />
  	<input type="hidden" name="id" value="<?php echo $Ads["id"]; ?>" />
  	<div class="pageDescr"><?php echo $lang['Edit_Listing_Dashboard']; ?></div>
	<table border="0" cellspacing="0" cellpadding="8" class="fieldTables">
      <tr>
      	<td colspan="2" valign="top" class="headlist"><?php echo $lang['Edit_Listing_Header']; ?></td>
      </tr>
      <tr>
      	<td class="formLeft"><?php echo $lang['Edit_Listing_Status']; ?></td>
      	<td class="formRight">
        <select name="status">
          <option value="Online"<?php if ($Ads["status"]=='Online') echo ' selected="selected"'; ?>><?php echo $lang['Edit_Listing_Online']; ?></option>
          <option value="Waiting"<?php if ($Ads["status"]=='Waiting') echo ' selected="selected"'; ?>><?php echo $lang['Edit_Listing_Waiting']; ?></option>
          <option value="Expired"<?php if ($Ads["status"]=='Expired') echo ' selected="selected"'; ?>><?php echo $lang['Edit_Listing_Expired']; ?></option>
        </select>
      	</td>
      </tr>
      
      <?php
	  $sql = "SELECT * FROM ".$TABLE["Users"]." ORDER BY user_name ASC";
	  $sql_result = sql_result($sql);
	  ?> 
      <tr>
      	<td><?php echo $lang['Edit_Listing_User']; ?> </td>
      	<td>
        	<select name="user_id">
            	<option value="0">Admin</option>
			<?php
            while ($User = mysqli_fetch_assoc($sql_result)) {
            ?>
         		<option value="<?php echo $User["id"]; ?>"<?php if ($Ads["user_id"]==$User["id"]) echo ' selected="selected"'; ?>><?php if(trim($User["user_name"])!='') {echo ReadDB($User["user_name"]);} else {echo ReadDB($User["user_email"]);} ?></option>
            <?php
			} 
			?>
      		</select>
		</td>
      </tr>   
      
      <tr>
        <td class="formLeft"><?php echo $lang['Edit_Listing_Title']; ?></td>
        <td class="formRight"><input class="input_post" type="text" name="title" maxlength="250" value="<?php echo ReadDB($Ads["title"]); ?>" required /></td>
      </tr>
      
      <tr>
      	<td class="formLeft"><?php echo $lang['Edit_Listing_Category']; ?> </td>
      	<td class="formRight">
        	<select name="cat_id">
			<?php
            $sql = "SELECT * FROM ".$TABLE["Categories"]." ORDER BY cat_name ASC";
            $sql_result = sql_result($sql);
            if (mysqli_num_rows($sql_result)>0) {
              while ($Cat = mysqli_fetch_assoc($sql_result)) {
            ?>
         		<option value="<?php echo $Cat["id"]; ?>"<?php if($Cat["id"]==$Ads["cat_id"]) echo ' selected="selected"'; ?>><?php echo ReadDB($Cat["cat_name"]); ?></option>
            <?php
			  }
			} 
			?>
      		</select>
		</td>
      </tr> 
      
      <tr>
        <td class="formLeft"><?php echo $lang['Edit_Listing_Featured']; ?></td>
        <td class="formRight">
        	<input name="highlight" type="checkbox" id="Featured" value="true"<?php if($Ads["highlight"]=='true') echo ' checked="checked"'; ?> /> <label for="Featured"><?php echo $lang['Edit_Listing_Info']; ?></label>
        </td>
      </tr> 
      
      <tr>
        <td class="formLeft"><?php echo $lang['Edit_Listing_Price']; ?></td>
        <td class="formRight"><input type="number" min="0.00" max="1000000.00" step="0.01" name="price" size="12" value="<?php echo ReadDB($Ads["price"]); ?>" required /> <?php echo $CurrAbr[$Options["currency"]]; ?> </td>
      </tr>        
      
      <tr>
        <td class="formLeft"><?php echo $lang['Edit_Listing_sPrice']; ?></td>
        <td class="formRight"><input type="number" min="0.00" max="10000.00" step="0.01" name="sale_price" size="12" value="<?php echo ReadDB($Ads["sale_price"]); ?>" /> <?php echo $CurrAbr[$Options["currency"]]; ?> </td>
      </tr>        
      
      <tr>
        <td class="formLeft"><?php echo $lang['Edit_Listing_Shipping']; ?></td>
        <td class="formRight"><input type="number" min="0.00" max="10000.00" step="0.01" name="shipping" size="12" value="<?php echo ReadDB($Ads["shipping"]); ?>" /> <?php echo $CurrAbr[$Options["currency"]]; ?> </td>
      </tr> 
      
      <tr>
        <td class="formLeft" valign="top"><?php echo $lang['Edit_Listing_Description']; ?></td>
        <td class="formRight"><textarea class="post_text" name="description" rows="8"><?php echo ReadDB($Ads["description"]); ?></textarea></td>
      </tr> 
      
      <?php for($i=1; $i<=3; $i++) { ?>                    
      <tr>
        <td class="formLeft"><?php echo $lang['Edit_Listing_Image'.$i]; ?></td>
        <td class="formRight">
        	<?php if(stripslashes($Ads["image".$i]) != "") { ?>
			<img src="<?php echo $CONFIG["upload_folder"].ReadDB($Ads["image".$i]); ?>" border="0" width="160" /> &nbsp; &nbsp; <a href="<?php $_SERVER["PHP_SELF"]; ?>?act=delImage&imgnum=<?php echo $i; ?>&id=<?php echo $Ads["id"]; ?>"><?php echo $lang['Edit_Listing_Img_del']; ?></a><br /> 
            <?php echo $lang['Edit_Listing_Img_info']; ?> <br />
            <?php } ?>
          	<input type="file" name="image<?php echo $i; ?>" size="70" /> <sub><?php echo $lang['Edit_Listing_Limit_Mb']; ?></sub>
        </td>
      </tr> 
      <?php } ?>
      
      <tr>
        <td colspan="2"><div class="border_bottom"></div></td>
      </tr> 
               
      <tr>
      	<td class="formLeft"><?php echo $lang['Edit_Listing_Name']; ?> </td>
      	<td class="formRight"><input class="input_details" type="text" name="name" maxlength="250" value="<?php echo ReadDB($Ads["name"]); ?>" /></td>
      </tr>       
      <tr>
        <td class="formLeft"><?php echo $lang['Edit_Listing_Address']; ?></td>
        <td class="formRight"><input class="input_details" type="text" name="address" maxlength="250" value="<?php echo ReadDB($Ads["address"]); ?>" /></td>
      </tr>     
      <tr>
        <td class="formLeft"><?php echo $lang['Edit_Listing_Location']; ?></td>
        <td class="formRight"><input class="input_details" type="text" name="location" maxlength="250" value="<?php echo ReadDB($Ads["location"]); ?>" /></td>
      </tr>   
      <tr>
      	<td class="formLeft"><?php echo $lang['Edit_Listing_Email']; ?> </td>
      	<td class="formRight"><input class="input_details" type="text" name="email" maxlength="250" value="<?php echo ReadDB($Ads["email"]); ?>" /></td>
      </tr> 
      <tr>
      	<td class="formLeft"><?php echo $lang['Edit_Listing_Phone']; ?> </td>
      	<td class="formRight"><input class="input_details" type="text" name="phone" maxlength="250" value="<?php echo ReadDB($Ads["phone"]); ?>" /></td>
      </tr> 
      <tr>
        <td class="formLeft"><?php echo $lang['Edit_Listing_Website']; ?></td>
        <td class="formRight"><input class="input_details" type="text" name="website" maxlength="250" value="<?php echo ReadDB($Ads["website"]); ?>" /></td>
      </tr>   
      
      <tr>
        <td colspan="2"><div class="border_bottom"></div></td>
      </tr>    
           
      <tr>
        <td class="formLeft"><?php echo $lang['Edit_Listing_PDate']; ?></td>
        <td class="formRight">
      		<input type="text" name="publish_date" id="publish_date" maxlength="25" size="25" value="<?php echo $Ads["publish_date"]; ?>" readonly /> <a href="javascript:NewCssCal('publish_date','yyyymmdd','dropdown',true,24,false)"><img src="images/cal.gif" width="16" height="16" alt="Pick a date" border="0" ></a>
        </td>
      </tr>
      <tr>
        <td class="formLeft"><?php echo $lang['Edit_Listing_EDate']; ?></td>
        <td class="formRight">
      		<input type="text" name="expire_date" id="expire_date" maxlength="25" size="25" value="<?php echo $Ads["expire_date"]; ?>" readonly /> <a href="javascript:NewCssCal('expire_date','yyyymmdd','dropdown',true,24,false)"><img src="images/cal.gif" width="16" height="16" alt="Pick a date" border="0" ></a>
        </td>
      </tr>
      
      <tr>
        <td>&nbsp;</td>
        <td class="formRight">
        	<input name="submit" type="submit" value="<?php echo $lang['Edit_Listing_button']; ?>" class="submitButton" />
        </td>
      </tr>
  	</table>
	</form>
    
    
<?php 
} elseif ($_REQUEST["act"]=='viewAds') {
	
	$sql = "SELECT * FROM ".$TABLE["Options"];
	$sql_result = sql_result($sql);
	$Options = mysqli_fetch_assoc($sql_result);
	$OptionsVis = unserialize($Options['visual']);
	$OptionsLang = unserialize($Options['language']);
	
	$sql = "SELECT * FROM ".$TABLE["Ads"]." WHERE id='".$_REQUEST["id"]."'";
	$sql_result = sql_result($sql);
	$Ads = mysqli_fetch_assoc($sql_result);
?>

<style type="text/css">
/* css for mobile devices start */
/* Portrait and Landscape */
.image_full {  
	max-width:<?php echo $Options['imgwidth']; ?>;
}
.td_img {
	padding-right:14px; 
	/* max-width:<?php echo $Options['imgwidth']; ?>; */
}
.mobileshow480 {
	display: none;
}
@media only screen and (max-width: 480px) {

	.image_full {  
		max-width: 100% !important;
	}
	
	.td_img {
		width: 50% !important;
	}
	.mobilehide480 {
		display: none;
	}
	.mobileshow480 {
		display: block;
	}

}
</style>

	<div style="clear:both;padding-left:40px;padding-top:10px;padding-bottom:10px;"><a href="admin.php?act=editAds&id=<?php echo ReadDB($Ads['id']); ?>"><?php echo $lang['Preview_Edit_Item']; ?></a></div>
    
	<div style="font-family:<?php echo $OptionsVis["gen_font_family"];?>; font-size:<?php echo $OptionsVis["gen_font_size"];?>;margin:0 auto;width:<?php echo $OptionsVis["gen_width"];?>px; color:<?php echo $OptionsVis["gen_font_color"];?>;line-height:<?php echo $OptionsVis["gen_line_height"];?>;">
    
    
	<?php if($OptionsLang["Back_to_home"]!='') { ?>
    <div style="text-align:<?php echo $OptionsVis["link_align"]; ?>">
    	<a href="admin.php?act=ads" style='font-weight:<?php echo $OptionsVis["link_font_weight"]; ?>;color:<?php echo $OptionsVis["link_color"]; ?>;font-size:<?php echo $OptionsVis["link_font_size"]; ?>;text-decoration:underline'><?php echo $OptionsLang["Back_to_home"]; ?></a>
    </div>    
    <div style="clear:both; height:<?php echo $OptionsVis["dist_link_title"];?>;"></div>    
    <?php } ?>
    
	<div style="color:<?php echo $OptionsVis["title_color"];?>;font-family:<?php echo $OptionsVis["title_font"];?>;font-size:<?php echo $OptionsVis["title_size"];?>;font-weight:<?php echo $OptionsVis["title_font_weight"];?>;font-style:<?php echo $OptionsVis["title_font_style"];?>;text-align:<?php echo $OptionsVis["title_text_align"];?>;">	  
            <?php echo ReadHTML($Ads["title"]); ?>     
    </div>
    
    <div style="clear:both; padding-bottom:<?php echo $OptionsVis["dist_title_date"];?>;"></div>    
       
    <div style="color:<?php echo $OptionsVis["cont_color"];?>; font-family:<?php echo $OptionsVis["cont_font"];?>; font-size:<?php echo $OptionsVis["cont_size"];?>;font-style: <?php echo $OptionsVis["cont_font_style"];?>;text-align:<?php echo $OptionsVis["cont_text_align"];?>;line-height:<?php echo $OptionsVis["cont_line_height"];?>;">
      	<table width="100%" border="0" cellspacing="0" cellpadding="0">
          <tr>
            <?php if(ReadDB($Ads["image1"])!='' or ReadDB($Ads["image2"])!='' or ReadDB($Ads["image3"])!='' or ReadDB($Ads["image4"])!='' or ReadDB($Ads["image5"])!='') { ?>
          	<td valign="top" class="td_img mobilehide480">
            
          	<?php 
          	for($i=1; $i<=5; $i++) {
          		if(ReadDB($Ads["image".$i])!='') { 
			?>            
            <div style="clear:both; padding-bottom:<?php echo $OptionsVis["dist_image"];?>;">
        		<img class="image_full" src="<?php echo $CONFIG["full_url"].$CONFIG["upload_folder"].ReadDB($Ads["image".$i]); ?>" alt="<?php echo ReadHTML($Ads["title"]); ?>" style="padding-bottom:6px; padding-top:2px;" />                         
        	</div>
            <?php 
				}
			} 
			?>            
        	</td>
           <?php 
		   }
		   ?>
            <td valign="top" align="left" style="width: 92%;">
            <?php if(ReadDB($Ads["image1"])!='' or ReadDB($Ads["image2"])!='' or ReadDB($Ads["image3"])!='' or ReadDB($Ads["image4"])!='' or ReadDB($Ads["image5"])!='') { ?>                
                <?php 
                for($i=1; $i<=5; $i++) {
                    if(ReadDB($Ads["image".$i])!='') { 
                ?>            
                <div style="clear:both; padding-bottom:<?php echo $OptionsVis["dist_image"];?>;" class="mobileshow480">
                    <img class="image_full" src="<?php echo $CONFIG["full_url"].$CONFIG["upload_folder"].ReadDB($Ads["image".$i]); ?>" alt="<?php echo ReadHTML($Ads["title"]); ?>" style="padding-bottom:6px; padding-top:2px;" />                         
                </div>
                <?php 
                    }
                } 
                ?> 
		   <?php 
           }
           ?>
            	
            	<div style="color:<?php echo $OptionsVis["date_color"];?>; font-family:<?php echo $OptionsVis["date_font"];?>; font-size:<?php echo $OptionsVis["date_size"];?>;font-weight:<?php echo $OptionsVis["date_font_weight"];?>;font-style: <?php echo $OptionsVis["date_font_style"];?>;text-align:<?php echo $OptionsVis["date_text_align"];?>;">
				<?php echo $OptionsLang["Listed"]; ?>: <?php echo date($OptionsVis["date_format"],strtotime($Ads["publish_date"])); ?> <?php if($OptionsVis["showing_time"]!='') echo date($OptionsVis["showing_time"],strtotime($Ads["publish_date"])); ?>
   				</div>
    
				<?php if(ReadHTML($Ads["price"])!='') { ?>
                <div style="clear:both; padding-bottom:<?php echo $OptionsVis["dist_date_price"];?>;"></div>
                
                <div style="color:<?php echo $OptionsVis["price_color"];?>; font-family:<?php echo $OptionsVis["price_font"];?>; font-size:<?php echo $OptionsVis["price_size"];?>;font-weight:<?php echo $OptionsVis["price_font_weight"];?>;font-style: <?php echo $OptionsVis["price_font_style"];?>;text-align:<?php echo $OptionsVis["price_text_align"];?>;">
                    <?php echo $OptionsLang["Price"]; ?>: 
                    <?php echo CurrFormat($Options["currency"], $CurrSign[$Options["currency"]], ReadHTML($Ads["price"])); ?>
                </div>   
                <div style="clear:both; padding-bottom:<?php echo $OptionsVis["dist_price_text"];?>;"></div>
                <?php } ?>
                
                <div style="color:<?php echo $OptionsVis["cont_color"];?>; font-family:<?php echo $OptionsVis["cont_font"];?>; font-size:<?php echo $OptionsVis["cont_size"];?>;font-style: <?php echo $OptionsVis["cont_font_style"];?>;text-align:<?php echo $OptionsVis["cont_text_align"];?>;line-height:<?php echo $OptionsVis["cont_line_height"];?>;padding-top:2px;">
					<?php if(trim(ReadHTML($Ads["name"]))) {?><?php echo $OptionsLang["Name"]; ?>: <?php echo ReadHTML($Ads["name"]); ?><br /> <?php } ?>
                    <?php if(trim(ReadHTML($Ads["location"]))) {?><?php echo $OptionsLang["Location"]; ?>: <?php echo ReadHTML($Ads["location"]); ?><br /> <?php } ?>
                    <a href="javascript: void();" onClick="window.open('<?php echo $CONFIG["full_url"]; ?>request.php?id=<?php echo ReadDB($Ads["id"]); ?>','Send_Email_to_publisher','width=600,height=450')" style='font-weight:<?php echo $OptionsVis["email_font_weight"]; ?>;color:<?php echo $OptionsVis["email_color"]; ?>;font-size:<?php echo $OptionsVis["email_font_size"]; ?>; font-style: <?php echo $OptionsVis["email_font_style"]; ?>;text-decoration:underline'><?php echo $OptionsLang["Email_to_publisher"]; ?></a>
                    <?php if(trim(ReadHTML($Ads["phone"]))) {?><br /><?php echo $OptionsLang["Phone"]; ?>: <?php echo ReadHTML($Ads["phone"]); ?> <?php } ?> 
                    <?php if(trim(ReadHTML($Ads["website"]))) {?>
                    <br /><?php echo $OptionsLang["Website"]; ?>: 
					<a href="<?php echo addhttp(ReadDB($Ads["website"])); ?>" rel="nofollow" target="_blank">
						<?php echo ReadHTML($Ads["website"]); ?> 
                    </a>
					<?php } ?> 
                </div>
                
                <div style="clear:both; padding-bottom:<?php echo $OptionsVis["dist_date_text"];?>;"></div>
                <div style="color:<?php echo $OptionsVis["cont_color"];?>; font-family:<?php echo $OptionsVis["cont_font"];?>; font-size:<?php echo $OptionsVis["cont_size"];?>;font-style: <?php echo $OptionsVis["cont_font_style"];?>;text-align:<?php echo $OptionsVis["cont_text_align"];?>;line-height:<?php echo $OptionsVis["cont_line_height"];?>;padding-top:2px;"><?php echo nl2br(ReadHTML($Ads["description"])); ?> </div>
                
                <?php if($OptionsVis["show_share_this"]=='yes') { ?>
                <div style="padding-top:12px; float:<?php echo $OptionsVis["share_this_align"];?>;">
                <!-- AddToAny BEGIN -->
                <div class="a2a_kit<?php if($OptionsVis["Buttons_size"]!='') echo " ".$OptionsVis["Buttons_size"];?> a2a_default_style">
                <a class="a2a_dd" href="https://www.addtoany.com/share_save"></a>
                <a class="a2a_button_facebook"></a>
                <a class="a2a_button_twitter"></a>
                <a class="a2a_button_google_plus"></a>
                <a class="a2a_button_email"></a>
                </div>
                <script type="text/javascript" src="//static.addtoany.com/menu/page.js"></script>
                <!-- AddToAny END -->
                </div>
                <?php } ?>
                <div style="clear:both"></div>
            </td>
          </tr>
        </table>
		
      	<div style="clear:both"></div>
    </div>
    
    <div style="clear:both; height: 12px;"></div>
    </div>
 
 
<?php 
} elseif ($_REQUEST["act"]=='exads') {
	
	$sql = "SELECT * FROM ".$TABLE["Options"];
	$sql_result = sql_result($sql);
	$Options = mysqli_fetch_assoc($sql_result);
	$OptionsVis = unserialize($Options['visual']);
		
	if(isset($_REQUEST["p"]) and $_REQUEST["p"]!='') { 
		$pageNum = (int) SafetyDB(urldecode($_REQUEST["p"]));
		if($pageNum<=0) $pageNum = 1;
	} else { 
		$pageNum = 1;
	}
	
	$orderByArr = array("title", "publish_date", "cat_id", "user_id", "reviews");
	if(isset($_REQUEST["orderBy"]) and $_REQUEST["orderBy"]!='' and in_array($_REQUEST["orderBy"], $orderByArr)) { 
		$orderBy = $_REQUEST["orderBy"];
	} else { 
		$orderBy = "publish_date";
	}
	
    $orderTypeArr = array("DESC", "ASC");	
    if(isset($_REQUEST["orderType"]) and $_REQUEST["orderType"]!='' and in_array($_REQUEST["orderType"], $orderTypeArr)) { 
		$orderType = $_REQUEST["orderType"];
	} else {
		$orderType = "DESC";
	}
	if ($orderType == 'DESC') { $norderType = 'ASC'; } else { $norderType = 'DESC'; }
	
	$sqlOnline   = "SELECT id FROM ".$TABLE["Ads"]." WHERE status='Expired'";
	$sql_resultOnline = sql_result($sqlOnline);
	$AdsExpired = mysqli_num_rows($sql_resultOnline);
?>
	<div class="pageDescr"><?php echo $lang['Expired_Dashboard1']; ?> <strong style="font-size:16px"><?php echo $AdsExpired; ?></strong> <?php echo $lang['Expired_Dashboard2']; ?></div>
    
	<table border="0" cellspacing="0" cellpadding="8" class="allTables">
  	  <tr>
        <td class="headlist"><a href="admin.php?act=exads&orderType=<?php echo $norderType; ?>&search=<?php if(isset($_REQUEST["search"])) echo $_REQUEST["search"]; ?>&orderBy=title&p=<?php if(isset($_REQUEST["p"])) echo $_REQUEST['p']; ?>"><?php echo $lang['Expired_Title']; ?></a></td>
        <td width="18%" class="headlist"><a href="admin.php?act=exads&orderType=<?php echo $norderType; ?>&search=<?php if(isset($_REQUEST["search"])) echo $_REQUEST["search"]; ?>&orderBy=publish_date&p=<?php if(isset($_REQUEST["p"])) echo $_REQUEST['p']; ?>"><?php echo $lang['Expired_Date_Activity']; ?></a></td>
        <td width="12%" class="headlist"><?php echo $lang['Expired_Status']; ?></td>
        <td width="16%" class="headlist"><a href="admin.php?act=exads&orderType=<?php echo $norderType; ?>&search=<?php if(isset($_REQUEST["search"])) echo $_REQUEST["search"]; ?>&orderBy=cat_id&p=<?php if(isset($_REQUEST["p"])) echo $_REQUEST['p']; ?>"><?php echo $lang['Expired_Category']; ?></a></td>
        <td width="10%" class="headlist"><a href="admin.php?act=exads&orderType=<?php echo $norderType; ?>&search=<?php if(isset($_REQUEST["search"])) echo urlencode($_REQUEST["search"]); ?>&orderBy=user_id&p=<?php if(isset($_REQUEST["p"])) echo $_REQUEST['p']; ?>"><?php echo $lang['Expired_User']; ?></a></td>
        <td width="8%" class="headlist"><a href="admin.php?act=exads&orderType=<?php echo $norderType; ?>&search=<?php if(isset($_REQUEST["search"])) echo $_REQUEST["search"]; ?>&orderBy=reviews&p=<?php if(isset($_REQUEST["p"])) echo $_REQUEST['p']; ?>"><?php echo $lang['Expired_Views']; ?></a></td>
        <td class="headlist" colspan="3">&nbsp;</td>
  	  </tr>
      
  	<?php 
	$sql   = "SELECT count(*) as total FROM ".$TABLE["Ads"]." WHERE status='Expired'";
	$sql_result = sql_result($sql);
	$row   = mysqli_fetch_array($sql_result);
	$count = $row["total"];
	$pages = ceil($count/30);

	$sql = "SELECT * FROM ".$TABLE["Ads"]." WHERE `status`='Expired' 
			ORDER BY " . $orderBy . " " . $orderType."  
			LIMIT " . ($pageNum-1)*30 . ",30";
	$sql_result = sql_result($sql);
	
	if (mysqli_num_rows($sql_result)>0) {
		while ($Ads = mysqli_fetch_assoc($sql_result)) {			
	?>
  	  <tr>
        <td class="bodylist"><?php echo ReadDB($Ads["title"]); ?></td>
        <td class="bodylist">
        	From: <?php echo admin_date($Ads["publish_date"]); ?><br />
        	To: <?php echo admin_date($Ads["expire_date"]); ?>
		</td>
        <td class="bodylist"><?php if(ReadDB($Ads["status"])=='Expired') echo $lang['Expired_Expired']; ?></td>        
        <td class="bodylist">
        	<?php 
			$sqlCat = "SELECT * FROM ".$TABLE["Categories"]." WHERE id='".$Ads["cat_id"]."'";
			$sql_resultCat = sql_result($sqlCat);
			$Cat = mysqli_fetch_assoc($sql_resultCat);	
			if($Cat["id"]>0) echo ReadDB($Cat["cat_name"]); else echo "------"; ?>
        </td>
        <td class="bodylist">
        	<?php 
			if(isset($Ads["user_id"]) and $Ads["user_id"]>0) {
			$sqlU = "SELECT * FROM ".$TABLE["Users"]." WHERE id='".$Ads["user_id"]."'";
			$sql_resultU = sql_result($sqlU);
			$User = mysqli_fetch_assoc($sql_resultU);	
			if($User["id"]>0) echo ReadDB($User["user_name"]); 
			} else {
				echo "Admin"; 	
			}
			?>
        </td>
        <td class="bodylist"><?php if($Ads["reviews"]=='') echo "0"; else echo $Ads["reviews"]; ?></td>
		<td class="bodylistAct"><a class="view" href='admin.php?act=renewEx&id=<?php echo $Ads["id"]; ?>'  onclick="return confirm('Are you sure you want to renew with another period of <?php echo $Options["expire_days"]; ?> days?');" title="Renew"><img class="act" src="images/renew-icon.png" alt="Renew" /></a></td>
        <td class="bodylistAct"><a href='admin.php?act=editAds&id=<?php echo $Ads["id"]; ?>' title="Edit"><img class="act" src="images/edit.png" alt="Edit" /></a></td>
        <td class="bodylistAct"><a class="delete" href="admin.php?act=delAds&id=<?php echo $Ads["id"]; ?>" onclick="return confirm('Are you sure you want to delete it?');" title="DELETE"><img class="act" src="images/delete.png" alt="DELETE" /></a></td>
  	  </tr>
  	<?php 
		}
	} else {
	?>
      <tr>
      	<td colspan="11" style="border-bottom:1px solid #CCCCCC"><?php echo $lang['Expired_No_Entries']; ?></td>
      </tr>
    <?php	
	}
	?>
    
	<?php
    if ($pages>0) {
    ?>
  	  <tr>
      	<td colspan="11" class="bottomlist"><div class='paging'><?php echo $lang['Expired_Page']; ?> </div>
		<?php
        for($i=1;$i<=$pages;$i++){ 
            if($i == $pageNum ) echo "<div class='paging'>" .$i. "</div>";
            else echo "<a href='admin.php?act=exads&p=".$i."&search=".$_REQUEST["search"]."&amp;orderBy=".$_REQUEST["orderBy"]."&amp;orderType=".$_REQUEST["orderType"]."' class='paging'>".$i."</a>"; 
            echo "&nbsp; ";
        }
        ?>
      	</td>
      </tr>
	<?php
    }
    ?>
	</table>

  
    
<?php 
} elseif ($_REQUEST["act"]=='cats') {
	
	if(isset($_REQUEST["p"]) and $_REQUEST["p"]!='') { 
		$pageNum = (int) SafetyDB(urldecode($_REQUEST["p"]));
		if($pageNum<=0) $pageNum = 1;
	} else { 
		$pageNum = 1;
	}
	
	
	$orderByArr = array("cat_name");
	if(isset($_REQUEST["orderBy"]) and $_REQUEST["orderBy"]!='' and in_array($_REQUEST["orderBy"], $orderByArr)) { 
		$orderBy = $_REQUEST["orderBy"];
	} else { 
		$orderBy = "cat_name";
	}	
	
	$orderTypeArr = array("DESC", "ASC");	
    if(isset($_REQUEST["orderType"]) and $_REQUEST["orderType"]!='' and in_array($_REQUEST["orderType"], $orderTypeArr)) { 
		$orderType = $_REQUEST["orderType"];
	} else {
		$orderType = "ASC";
	}
	if ($orderType == 'DESC') { $norderType = 'ASC'; } else { $norderType = 'DESC'; }
?>
	<div class="pageDescr"><?php echo $lang['Category_Dashboard']; ?></div>
        
	<table border="0" cellspacing="0" cellpadding="8" class="allTables">
  	  <tr>
        <td width="66%" class="headlist"><a href="admin.php?act=cats&orderType=<?php echo $norderType; ?>&orderBy=cat_name&p=<?php if(isset($_REQUEST["p"])) echo $_REQUEST['p']; ?>"><?php echo $lang['Category_Category']; ?></a></td>
        <td class="headlist" colspan="2">&nbsp;</td>
  	  </tr>
      
  	<?php 
	$sql   = "SELECT count(*) as total FROM ".$TABLE["Categories"];
	$sql_result = sql_result($sql);
	$row   = mysqli_fetch_array($sql_result);
	$count = $row["total"];
	$pages = ceil($count/20);

	$sql = "SELECT * FROM ".$TABLE["Categories"]."   
			ORDER BY " . $orderBy . " " . $orderType."  
			LIMIT " . ($pageNum-1)*20 . ",20";	
	$sql_result = sql_result($sql);
	
	$numCat = mysqli_num_rows($sql_result);
	$i = 0;
	if ($numCat>0) {	
		while ($Cat = mysqli_fetch_assoc($sql_result)) {			
	?>
  	  <tr>
        <td class="bodylist"><?php echo ReadDB($Cat["cat_name"]); ?></td>
        <td class="bodylistAct"><a href='admin.php?act=editCat&id=<?php echo $Cat["id"]; ?>' title="Edit"><img class="act" src="images/edit.png" alt="Edit" /></a></td>
        <td class="bodylistAct"><?php if($i>0) {?><a class="delete" href="admin.php?act=delCat&id=<?php echo $Cat["id"]; ?>" onclick="return confirm('Are you sure you want to delete it?');" title="DELETE"><img class="act" src="images/delete.png" alt="DELETE" /></a><?php } ?></td>
  	  </tr>
  	<?php 
		$i++;
		}
	} else {
	?>
      <tr>
      	<td colspan="8" style="border-bottom:1px solid #CCCCCC"><?php echo $lang['Category_No_Categories']; ?></td>
      </tr>
    <?php	
	}
	?>
    
	<?php
    if ($pages>0) {
    ?>
  	  <tr>
      	<td colspan="8" class="bottomlist"><div class='paging'><?php echo $lang['Category_Page']; ?></div>
		<?php
        for($i=1;$i<=$pages;$i++){ 
            if($i == $pageNum ) echo "<div class='paging'>" .$i. "</div>";
            else echo "<a href='admin.php?act=cats&p=".$i."&amp;orderBy=".$_REQUEST["orderBy"]."&amp;orderType=".$_REQUEST["orderType"]."' class='paging'>".$i."</a>"; 
            echo "&nbsp; ";
        }
        ?>
      	</td>
      </tr>
	<?php
    }
    ?>
	</table>


<?php 
} elseif ($_REQUEST["act"]=='newCat') { 
?>
	<form action="admin.php" method="post" name="form">
  	<input type="hidden" name="act" value="addCat" />
  	<div class="pageDescr"><?php echo $lang['Create_Category_Dashboard']; ?></div>
	<table border="0" cellspacing="0" cellpadding="8" class="fieldTables">
      <tr>
      	<td colspan="2" valign="top" class="headlist"><?php echo $lang['Create_Category_Header']; ?></td>
      </tr>
      
      <tr>
        <td class="formLeft"><?php echo $lang['Category_Category_name']; ?></td>
        <td><input class="input_details" type="text" name="cat_name" maxlength="250" /></td>
      </tr>      
            
      <tr>
        <td>&nbsp;</td>
        <td><input name="submit" type="submit" value="<?php echo $lang['Create_Category_button']; ?>" class="submitButton" /></td>
      </tr>
  	</table>
	</form>
    
<?php 
} elseif ($_REQUEST["act"]=='editCat') {
	$_REQUEST["id"]= (int) SafetyDB($_REQUEST["id"]);
	$sql = "SELECT * FROM ".$TABLE["Categories"]." WHERE id='".$_REQUEST["id"]."'";
	$sql_result = sql_result($sql);
	$Cat = mysqli_fetch_assoc($sql_result);	
?>
	<form action="admin.php" method="post" name="form">
  	<input type="hidden" name="act" value="updateCat" />
  	<input type="hidden" name="id" value="<?php echo $Cat["id"]; ?>" />
  	<div class="pageDescr"><?php echo $lang['Edit_Category_Dashboard']; ?></div>
	<table border="0" cellspacing="0" cellpadding="8" class="fieldTables">
      <tr>
      	<td colspan="2" valign="top" class="headlist"><?php echo $lang['Edit_Category_Header']; ?></td>
      </tr>
       
      <tr>
        <td class="formLeft"><?php echo $lang['Edit_Category_name']; ?></td>
        <td><input class="input_details" type="text" name="cat_name" maxlength="250" value="<?php echo ReadDB($Cat["cat_name"]); ?>" /></td>
      </tr>
      
      <tr>
        <td>&nbsp;</td>
        <td>
        	<input name="submit" type="submit" value="<?php echo $lang['Edit_Category_button']; ?>" class="submitButton" />
        </td>
      </tr>
  	</table>
	</form>
    
    
    
<?php 
} elseif ($_REQUEST["act"]=='orders') {
	
	$sql = "SELECT * FROM ".$TABLE["Options"];
	$sql_result = sql_result($sql);
	$Options = mysqli_fetch_assoc($sql_result);
	mysqli_free_result($sql_result);
	$OptionsVis = unserialize($Options['visual']);
	$OptionsLang = unserialize($Options['language']);

	if(isset($_REQUEST["p"]) and $_REQUEST["p"]!='') { 
		$pageNum = (int) SafetyDB(urldecode($_REQUEST["p"]));
		if($pageNum<=0) $pageNum = 1;
	} else { 
		$pageNum = 1;
	}
	
	$orderByArr = array("name", "email", "status", "orderDate", "products");
	if(isset($_REQUEST["orderBy"]) and $_REQUEST["orderBy"]!='' and in_array($_REQUEST["orderBy"], $orderByArr)) { 
		$orderBy = $_REQUEST["orderBy"];
	} else { 
		$orderBy = "orderDate";
	}
	
    $orderTypeArr = array("DESC", "ASC");	
    if(isset($_REQUEST["orderType"]) and $_REQUEST["orderType"]!='' and in_array($_REQUEST["orderType"], $orderTypeArr)) { 
		$orderType = $_REQUEST["orderType"];
	} else {
		$orderType = "DESC";
	}
	if ($orderType == 'DESC') { $norderType = 'ASC'; } else { $norderType = 'DESC'; }
?>
	<div class="pageDescr"><?php echo $lang['Orders_Dashboard']; ?></div>
        
	<table border="0" cellspacing="0" cellpadding="8" class="allTables">
  	  <tr>
        <td width="15%" class="headlist"><a href="admin.php?act=orders&orderType=<?php echo $norderType; ?>&orderBy=name"><?php echo $lang['Orders_name']; ?></a></td>
        <td width="18%" class="headlist"><?php echo $lang['Orders_email']; ?></td>
        <td width="18%" class="headlist"><a href="admin.php?act=orders&orderType=<?php echo $norderType; ?>&orderBy=orderDate"><?php echo $lang['Orders_date']; ?></a></td>
        <td class="headlist"><a href="admin.php?act=orders&orderType=<?php echo $norderType; ?>&orderBy=products"><?php echo $lang['Orders_products']; ?></a></td>
        <td width="10%" class="headlist"><a href="admin.php?act=orders&orderType=<?php echo $norderType; ?>&orderBy=amount"><?php echo $lang['Orders_amount']; ?></a></td>
        <td width="8%" class="headlist"><a href="admin.php?act=orders&orderType=<?php echo $norderType; ?>&orderBy=status"><?php echo $lang['Orders_status']; ?></a></td>
        <td class="headlist" colspan="2">&nbsp;</td>
  	  </tr>
      
  	<?php 
	$sql   = "SELECT count(*) as total FROM ".$TABLE["Orders"];
	$sql_result = sql_result($sql);
	$row   = mysqli_fetch_array($sql_result);
	$count = $row["total"];
	$pages = ceil($count/100);

	$sql = "SELECT * FROM ".$TABLE["Orders"]."   
			ORDER BY " . $orderBy . " " . $orderType."  
			LIMIT " . ($pageNum-1)*100 . ",100";
	$sql_result = sql_result($sql);
	
	if (mysqli_num_rows($sql_result)>0) {	
		while ($Order = mysqli_fetch_assoc($sql_result)) {			
	?>
  	  <tr>
        <td class="bodylist"><?php echo ReadDB($Order["name"]) ." ". ReadDB($Order["last_name"]); ?></td>
        <td class="bodylist"><?php echo ReadDB($Order["email"]); ?></td>
        <td class="bodylist"><?php echo orders_date(ReadDB($Order["orderDate"])); ?></td>
        <td class="bodylist"><?php echo ReadDB($Order["products"]); ?></td> 
        <td class="bodylist"><?php echo CurrFormat($Options["currency"], $CurrSign[$Options["currency"]], ReadDB($Order["amount"])); ?></td>
        <td class="bodylist"><?php echo ReadDB($Order["status"]); ?></td>        
        <td class="bodylistAct"><a href='admin.php?act=editOrder&id=<?php echo $Order["id"]; ?>' title="Edit"><img class="act" src="images/edit.png" alt="Edit" /></a></td>
        <td class="bodylistAct"><a class="delete" href="admin.php?act=delOrder&id=<?php echo $Order["id"]; ?>" onclick="return confirm('Are you sure you want to delete it?');" title="DELETE"><img class="act" src="images/delete.png" alt="DELETE" /></a></td>
  	  </tr>
  	<?php 
		}
	} else {
	?>
      <tr>
      	<td colspan="9" style="border-bottom:1px solid #CCCCCC"><?php echo $lang['Orders_No_Orders']; ?></td>
      </tr>
    <?php	
	}
	?>
    
	<?php
    if ($pages>0) {
    ?>
  	  <tr>
      	<td colspan="9" class="bottomlist"><div class='paging'><?php echo $lang['Orders_Page']; ?></div>
		<?php
        for($i=1;$i<=$pages;$i++){ 
            if($i == $pageNum ) echo "<div class='paging'>" .$i. "</div>";
            else echo "<a href='admin.php?act=orders&p=".$i."' class='paging'>".$i."</a>"; 
            echo "&nbsp; ";
        }
        ?>
      	</td>
      </tr>
	<?php
    }
    ?>
	</table>
    
<?php 
} elseif ($_REQUEST["act"]=='editOrder') {
	$_REQUEST["id"]= (int) SafetyDB($_REQUEST["id"]);
	$sql = "SELECT * FROM ".$TABLE["Orders"]." WHERE id='".$_REQUEST["id"]."'";
	$sql_result = sql_result($sql);
	$Order = mysqli_fetch_assoc($sql_result);	
	
	$sql = "SELECT * FROM ".$TABLE["Options"];
	$sql_result = sql_result($sql);
	$Options = mysqli_fetch_assoc($sql_result);
	mysqli_free_result($sql_result);
	$OptionsVis = unserialize($Options['visual']);
	$OptionsLang = unserialize($Options['language']);
?>
	<form action="admin.php" method="post" name="form">
  	<input type="hidden" name="act" value="updateOrder" />
  	<input type="hidden" name="id" value="<?php echo $Order["id"]; ?>" />
  	<div class="pageDescr"><?php echo $lang['Edit_Order_Dashboard']; ?></div>
	<table border="0" cellspacing="0" cellpadding="8" class="fieldTables">
      <tr>
      	<td colspan="2" valign="top" class="headlist"><?php echo $lang['Edit_Order_Header']; ?></td>
      </tr>     
      
      <tr>
      	<td class="formLeft"><?php echo $lang['Edit_Order_Status']; ?></td>
      	<td class="formRight">
            <select name="status">
              <option value="Paid"<?php if($Order["status"]=='Paid') echo ' selected="selected"'; ?>><?php echo $lang['Edit_Order_Paid']; ?></option>
              <option value="Shipped"<?php if($Order["status"]=='Shipped') echo ' selected="selected"'; ?>><?php echo $lang['Edit_Order_Shipped']; ?></option>
              <option value="Completed"<?php if($Order["status"]=='Completed') echo ' selected="selected"'; ?>><?php echo $lang['Edit_Order_Completed']; ?></option>
              <option value="Returned"<?php if($Order["status"]=='Returned') echo ' selected="selected"'; ?>><?php echo $lang['Edit_Order_Returned']; ?></option>
            </select>
            &nbsp;&nbsp;&nbsp;&nbsp;
            <?php echo $lang['Edit_Order_date']; ?> <b><?php echo orders_date(ReadDB($Order["orderDate"])); ?></b>
      	</td>
      </tr>     
      
      <tr>
      	<td class="formLeft"><?php echo $lang['Edit_Order_Product']; ?></td>
      	<td class="formRight">
            <?php echo ReadDB($Order["products"]); ?>
            &nbsp;&nbsp;&nbsp;&nbsp;
            <?php echo $lang['Edit_Order_Amount']; ?> <b><?php echo CurrFormat($Options["currency"], $CurrSign[$Options["currency"]], ReadDB($Order["amount"])); ?></b>
      	</td>
      </tr>
       
      <tr>
        <td class="formLeft"><?php echo $lang['Edit_Order_name']; ?></td>
        <td><input type="text" name="name" maxlength="250" value="<?php echo ReadDB($Order["name"]); ?>" placeholder="First name" /> &nbsp; <input type="text" name="last_name" maxlength="250" value="<?php echo ReadDB($Order["last_name"]); ?>" placeholder="Last name" /></td>
      </tr>     
       
      <tr>
        <td class="formLeft"><?php echo $lang['Edit_Order_email']; ?></td>
        <td><input class="input_details" type="text" name="email" maxlength="250" value="<?php echo ReadDB($Order["email"]); ?>" placeholder="Email" /> </td>
      </tr>   
       
      <tr>
        <td class="formLeft"><?php echo $lang['Edit_Order_address']; ?></td>
        <td><input class="input_details" type="text" name="address" maxlength="250" value="<?php echo ReadDB($Order["address"]); ?>" placeholder="Address" /> </td>
      </tr>       
       
      <tr>
        <td class="formLeft"><?php echo $lang['Edit_Order_Location']; ?></td>
        <td>
        	<label class="location_label"> City<br>
            <input type="text" name="city" maxlength="250" value="<?php echo ReadDB($Order["city"]); ?>" placeholder="City" />
            </label>
            
            <label class="location_label"> State<br>
            <input type="text" name="state" maxlength="250" value="<?php echo ReadDB($Order["state"]); ?>" placeholder="State" />			
            </label>
            
            <label class="location_label"> Zip<br>
            <input type="text" name="zip" maxlength="250" value="<?php echo ReadDB($Order["zip"]); ?>" placeholder="Zip" />
            </label>
            
            <label class="location_label"> Country<br>
            <input type="text" name="country" maxlength="250" value="<?php echo ReadDB($Order["country"]); ?>" placeholder="Country" />
            </label>
        </td>
      </tr>
      
      <tr>
        <td>&nbsp;</td>
        <td>
        	<input name="submit" type="submit" value="<?php echo $lang['Edit_Order_button']; ?>" class="submitButton" />
        </td>
      </tr>
  	</table>
	</form>


<?php 
} elseif ($_REQUEST["act"]=='users') {

	if(isset($_REQUEST["p"]) and $_REQUEST["p"]!='') { 
		$pageNum = (int) SafetyDB(urldecode($_REQUEST["p"]));
		if($pageNum<=0) $pageNum = 1;
	} else { 
		$pageNum = 1;
	}
	
	$orderByArr = array("user_name", "user_email", "status", "reg_date");
	if(isset($_REQUEST["orderBy"]) and $_REQUEST["orderBy"]!='' and in_array($_REQUEST["orderBy"], $orderByArr)) { 
		$orderBy = $_REQUEST["orderBy"];
	} else { 
		$orderBy = "user_name";
	}
	
    $orderTypeArr = array("DESC", "ASC");	
    if(isset($_REQUEST["orderType"]) and $_REQUEST["orderType"]!='' and in_array($_REQUEST["orderType"], $orderTypeArr)) { 
		$orderType = $_REQUEST["orderType"];
	} else {
		$orderType = "ASC";
	}
	if ($orderType == 'DESC') { $norderType = 'ASC'; } else { $norderType = 'DESC'; }
?>
	<div class="pageDescr"><?php echo $lang['Users_Dashboard']; ?></div>
        
	<table border="0" cellspacing="0" cellpadding="8" class="allTables">
  	  <tr>
        <td class="headlist"><a href="admin.php?act=users&orderType=<?php echo $norderType; ?>&orderBy=user_email"><?php echo $lang['Users_Email']; ?></a></td>
        <td width="13%" class="headlist"><?php echo $lang['Users_Pass']; ?></td>
        <td width="12%" class="headlist"><a href="admin.php?act=users&orderType=<?php echo $norderType; ?>&orderBy=reg_date"><?php echo $lang['Users_Date']; ?></a></td>
        <td width="14%" class="headlist"><a href="admin.php?act=users&orderType=<?php echo $norderType; ?>&orderBy=user_name"><?php echo $lang['Users_Name']; ?></a></td>
        <td width="8%" class="headlist"><a href="admin.php?act=users&orderType=<?php echo $norderType; ?>&orderBy=status"><?php echo $lang['Users_Status']; ?></a></td>
        <td width="21%" class="headlist"><?php echo $lang['Users_NumAds']; ?></td>
        <td width="5%" class="headlist"><?php echo $lang['Users_IP']; ?></td>
        <td class="headlist" colspan="2">&nbsp;</td>
  	  </tr>
      
  	<?php 
	$sql   = "SELECT count(*) as total FROM ".$TABLE["Users"];
	$sql_result = sql_result($sql);
	$row   = mysqli_fetch_array($sql_result);
	$count = $row["total"];
	$pages = ceil($count/20);

	$sql = "SELECT * FROM ".$TABLE["Users"]."   
			ORDER BY " . $orderBy . " " . $orderType."  
			LIMIT " . ($pageNum-1)*20 . ",20";
	$sql_result = sql_result($sql);
	
	if (mysqli_num_rows($sql_result)>0) {	
		while ($User = mysqli_fetch_assoc($sql_result)) {			
	?>
  	  <tr>
        <td class="bodylist"><?php echo ReadDB($User["user_email"]); ?></td>
        <td class="bodylist"><?php echo ReadDB($User["user_password"]); ?></td>
        <td class="bodylist"><?php echo admin_date(ReadDB($User["reg_date"])); ?></td>
        <td class="bodylist"><?php echo ReadDB($User["user_name"]); ?></td>
        <td class="bodylist"><?php echo ReadDB($User["status"]); ?></td>
        <?php 
		$sqlCO   = "SELECT count(*) as total FROM ".$TABLE["Ads"]." WHERE user_id='".$User["id"]."' AND status='Online'";
		$sql_resultCO = sql_result($sqlCO);
		$row   = mysqli_fetch_array($sql_resultCO);
		$countOnline = $row["total"];
		
		$sqlCW   = "SELECT count(*) as total FROM ".$TABLE["Ads"]." WHERE user_id='".$User["id"]."' AND status='Waiting'";
		$sql_resultCW = sql_result($sqlCW);
		$row   = mysqli_fetch_array($sql_resultCW);
		$countWaiting = $row["total"];
		
		$sqlCE   = "SELECT count(*) as total FROM ".$TABLE["Ads"]." WHERE user_id='".$User["id"]."' AND status='Expired'";
		$sql_resultCE = sql_result($sqlCE);
		$row   = mysqli_fetch_array($sql_resultCE);
		$countExpired = $row["total"];
		?>
        <td class="bodylist">
			<?php echo $lang['Users_Online']." ".$countOnline; ?>, 
        	<?php echo $lang['Users_Waiting']." ".$countWaiting; ?>, 
        	<?php echo $lang['Users_Expired']." ".$countExpired; ?>
        </td>
        <td class="bodylist">
        	<?php if(trim($User["user_ip"])!="") {?>
        	<a href="https://ipinfo.io/<?php echo ReadDB($User["user_ip"]); ?>" target="_blank"><?php echo ReadDB($User["user_ip"]); ?></a>
            <?php } else { ?>
        	<a href="https://ipinfo.io/8.8.8.8" target="_blank">8.8.8.8</a>
            <?php } ?>
        </td>
        <td class="bodylistAct"><a href='admin.php?act=editUser&id=<?php echo $User["id"]; ?>' title="Edit"><img class="act" src="images/edit.png" alt="Edit" /></a></td>
        <td class="bodylistAct"><a class="delete" href="admin.php?act=delUser&id=<?php echo $User["id"]; ?>" onclick="return confirm('Are you sure you want to delete it?');" title="DELETE"><img class="act" src="images/delete.png" alt="DELETE" /></a></td>
  	  </tr>
  	<?php 
		}
	} else {
	?>
      <tr>
      	<td colspan="9" style="border-bottom:1px solid #CCCCCC"><?php echo $lang['Users_No_Users']; ?></td>
      </tr>
    <?php	
	}
	?>
    
	<?php
    if ($pages>0) {
    ?>
  	  <tr>
      	<td colspan="9" class="bottomlist"><div class='paging'><?php echo $lang['Users_Page']; ?></div>
		<?php
        for($i=1;$i<=$pages;$i++){ 
            if($i == $pageNum ) echo "<div class='paging'>" .$i. "</div>";
            else echo "<a href='admin.php?act=users&p=".$i."' class='paging'>".$i."</a>"; 
            echo "&nbsp; ";
        }
        ?>
      	</td>
      </tr>
	<?php
    }
    ?>
	</table>
    
    
<?php 
} elseif ($_REQUEST["act"]=='newUser') { 
?>
	<form action="admin.php" method="post" name="form">
  	<input type="hidden" name="act" value="addUser" />
  	<div class="pageDescr"><?php echo $lang['Users_Page']; ?></div>
	<table border="0" cellspacing="0" cellpadding="8" class="fieldTables">
    
      <tr>
      	<td colspan="2" valign="top" class="headlist"><?php echo $lang['Add_User_Header']; ?></td>
      </tr>
      
      <tr>
      	<td class="formLeft"><?php echo $lang['Add_User_Status']; ?></td>
      	<td class="formRight">
            <select name="status">
              <option value="Active"><?php echo $lang['Add_User_Active']; ?></option>
              <option value="Inactive"><?php echo $lang['Add_User_Inactive']; ?></option>
            </select>
      	</td>
      </tr>
      
      <tr>
        <td class="formLeft"><?php echo $lang['Add_User_Email']; ?></td>
        <td class="formRight"><input class="input_details" type="text" name="user_email" maxlength="250" value="<?php if(isset($_REQUEST['user_email'])) echo $_REQUEST["user_email"]; ?>" /></td>
      </tr> 
         
      <tr>
        <td class="formLeft"><?php echo $lang['Add_User_Pass']; ?></td>
        <td class="formRight"><input class="input_details" type="text" name="user_password" maxlength="90" value="<?php if(isset($_REQUEST['user_password'])) echo $_REQUEST["user_password"]; ?>" /></td>
      </tr>
      
      <tr>
        <td class="formLeft"><?php echo $lang['Add_User_Name']; ?></td>
        <td class="formRight"><input class="input_details" type="text" name="user_name" maxlength="90" value="<?php if(isset($_REQUEST['user_name'])) echo $_REQUEST["user_name"]; ?>" /></td>
      </tr>
      
      <tr>
        <td class="formLeft"><?php echo $lang['Add_User_Address']; ?></td>
        <td class="formRight"><input class="input_details" type="text" name="user_address" maxlength="90" value="<?php if(isset($_REQUEST['user_address'])) echo $_REQUEST["user_address"]; ?>" /></td>
      </tr>
      
      <tr>
        <td class="formLeft"><?php echo $lang['Add_User_Location']; ?></td>
        <td class="formRight"><input class="input_details" type="text" name="user_location" maxlength="90" value="<?php if(isset($_REQUEST['user_location'])) echo $_REQUEST["user_location"]; ?>" /></td>
      </tr>
      
      <tr>
        <td class="formLeft"><?php echo $lang['Add_User_Phone']; ?></td>
        <td class="formRight"><input class="input_details" type="text" name="user_phone" maxlength="90" value="<?php if(isset($_REQUEST['user_phone'])) echo $_REQUEST["user_phone"]; ?>" /></td>
      </tr>
      
      <tr>
        <td class="formLeft"><?php echo $lang['Add_User_Website']; ?></td>
        <td class="formRight"><input class="input_details" type="text" name="user_url" maxlength="90" value="<?php if(isset($_REQUEST['user_url'])) echo $_REQUEST["user_url"]; ?>" /></td>
      </tr>
           
      <tr>
        <td>&nbsp;</td>
        <td class="formRight"><input name="submit" type="submit" value="<?php echo $lang['Add_User_button']; ?>" class="submitButton" /></td>
      </tr>
  	</table>
	</form>
    

<?php 
} elseif ($_REQUEST["act"]=='editUser') { 
	$_REQUEST["id"]= (int) SafetyDB($_REQUEST["id"]);
	$sql = "SELECT * FROM ".$TABLE["Users"]." WHERE id='".$_REQUEST["id"]."'";
	$sql_result = sql_result($sql);
	$User = mysqli_fetch_assoc($sql_result);
?>
	<form action="admin.php" method="post" name="form">
  	<input type="hidden" name="act" value="updateUser" />
  	<input type="hidden" name="id" value="<?php echo $User["id"]; ?>" />
  	<div class="pageDescr"><?php echo $lang['Edit_User_Dashboard']; ?></div>
	<table border="0" cellspacing="0" cellpadding="8" class="fieldTables">
    
      <tr>
      	<td colspan="2" valign="top" class="headlist"><?php echo $lang['Edit_User_Header']; ?></td>
      </tr>
      
      <tr>
      	<td class="formLeft"><?php echo $lang['Edit_User_Status']; ?></td>
      	<td class="formRight">
            <select name="status">
              <option value="Active"<?php if($User["status"]=='Active') echo ' selected="selected"'; ?>><?php echo $lang['Edit_User_Active']; ?></option>
              <option value="Inactive"<?php if($User["status"]=='Inactive') echo ' selected="selected"'; ?>><?php echo $lang['Edit_User_Inactive']; ?></option>
            </select>
      	</td>
      </tr>
      
      <tr>
        <td class="formLeft"><?php echo $lang['Edit_User_Email']; ?></td>
        <td class="formRight"><input class="input_details" type="text" name="user_email" maxlength="250" value="<?php echo ReadDB($User["user_email"]); ?>" /></td>
      </tr> 
         
      <tr>
        <td class="formLeft"><?php echo $lang['Edit_User_Pass']; ?></td>
        <td class="formRight"><input class="input_details" type="text" name="user_password" maxlength="250" value="<?php echo ReadDB($User["user_password"]); ?>" /></td>
      </tr>
      
      <tr>
        <td class="formLeft"><?php echo $lang['Edit_User_Name']; ?></td>
        <td class="formRight"><input class="input_details" type="text" name="user_name" maxlength="250" value="<?php echo ReadDB($User["user_name"]); ?>" /></td>
      </tr>
      
      <tr>
        <td class="formLeft"><?php echo $lang['Edit_User_Address']; ?></td>
        <td class="formRight"><input class="input_details" type="text" name="user_address" maxlength="250" value="<?php echo ReadDB($User["user_address"]); ?>" /></td>
      </tr>
      
      <tr>
        <td class="formLeft"><?php echo $lang['Edit_User_Location']; ?></td>
        <td class="formRight"><input class="input_details" type="text" name="user_location" maxlength="250" value="<?php echo ReadDB($User["user_location"]); ?>" /></td>
      </tr>
      
      <tr>
        <td class="formLeft"><?php echo $lang['Edit_User_Phone']; ?></td>
        <td class="formRight"><input class="input_details" type="text" name="user_phone" maxlength="250" value="<?php echo ReadDB($User["user_phone"]); ?>" /></td>
      </tr>
      
      <tr>
        <td class="formLeft"><?php echo $lang['Edit_User_Website']; ?></td>
        <td class="formRight"><input class="input_details" type="text" name="user_url" maxlength="250" value="<?php echo ReadDB($User["user_url"]); ?>" /></td>
      </tr>
      
      <tr>
        <td class="formLeft"><?php echo $lang['Add_User_IP']; ?></td>
        <td class="formRight">
			<?php if(trim($User["user_ip"])!="") {?>
        	<a href="https://ipinfo.io/<?php echo ReadDB($User["user_ip"]); ?>" target="_blank"><?php echo ReadDB($User["user_ip"]); ?></a>
            <?php } else { ?>
        	<a href="https://ipinfo.io/8.8.8.8" target="_blank">8.8.8.8</a>
            <?php } ?></td>
      </tr>
           
      <tr>
        <td>&nbsp;</td>
        <td class="formRight"><input name="submit" type="submit" value="<?php echo $lang['Edit_User_button']; ?>" class="submitButton" /></td>
      </tr>
  	</table>
	</form>



<?php 
} elseif ($_REQUEST["act"]=='ads_options') {
	$sql = "SELECT * FROM ".$TABLE["Options"];
	$sql_result = sql_result($sql);
	$Options = mysqli_fetch_assoc($sql_result);
	$OptionsLang = unserialize($Options['language']);
?>
	
    <div class="paddingtop"></div>
    
    <form action="admin.php" method="post" name="form">
	<input type="hidden" name="act" value="updateOptionsAds" />
    <table border="0" cellspacing="0" cellpadding="8" class="allTables">
      <tr>
        <td colspan="3" class="headlist">Admin options</td>
      </tr>
      <tr>
        <td width="45%" class="left_top">Administrator email:<br />
          <span style="font-size:11px"><em>all new Classified Ads notifications will be sent to this email address</em></span></td>
        <td class="left_top">
          <input class="input_opt" name="email" type="text" value="<?php echo ReadDB($Options["email"]); ?>" />
        </td>
      </tr>
      <tr>
        <td class="left_top">Number of Classified Ads per page: </td>
        <td class="left_top"><input name="per_page" type="text" size="3" value="<?php echo ReadDB($Options["per_page"]); ?>" /></td>
      </tr>
      <tr>
        <td class="left_top">Number of characters in the short description on Classified Ads listing: </td>
        <td class="left_top"><input name="char_num" type="text" size="5" value="<?php echo ReadDB($Options["char_num"]); ?>" /></td>
      </tr> 
      <tr>
        <td width="45%" class="left_top">Paypal email:<br />
          <span style="font-size:11px"><em>email address registered in Paypal for receiving payments</em></span></td>
        <td class="left_top">
          <input class="input_opt" name="paypal_email" type="text" value="<?php echo ReadDB($Options["paypal_email"]); ?>" />
        </td>
      </tr>
      <tr>
        <td>&nbsp;</td>
        <td><input name="submit1" type="submit" value="Save" class="submitButton" /></td>
      </tr>      
    </table>
    
    
    <table border="0" cellspacing="0" cellpadding="8" class="allTables">       
      <tr>
        <td colspan="3" class="headlist">Publishing options</td>
      </tr>  
      <tr>
        <td width="45%" class="left_top">Approval:<br />
          <span style="font-size:11px"><em>check if you want to approve Classified Ads before having them listed</em></span></td>
        <td class="left_top"><input name="ads_approve" type="checkbox" value="true"<?php if ($Options["ads_approve"]=='true') echo ' checked="checked"'; ?> /></td>
      </tr>
      <tr>
        <td class="left_top">Number of days to expire:<br />
          <span style="font-size:11px"><em>Set the number of the days after that the Classified Ads will expire.</em></span></td>
        <td class="left_top">
        <select name="expire_days"> 
             <option value="1"<?php if ($Options["expire_days"]=="1") echo ' selected="selected"'; ?>>1 day</option>
             <option value="2"<?php if ($Options["expire_days"]=="2") echo ' selected="selected"'; ?>>2 days</option>
             <option value="3"<?php if ($Options["expire_days"]=="3") echo ' selected="selected"'; ?>>3 days</option>
             <option value="4"<?php if ($Options["expire_days"]=="4") echo ' selected="selected"'; ?>>4 days</option>
             <option value="5"<?php if ($Options["expire_days"]=="5") echo ' selected="selected"'; ?>>5 days</option>
             <option value="6"<?php if ($Options["expire_days"]=="6") echo ' selected="selected"'; ?>>6 days</option>
             <option value="7"<?php if ($Options["expire_days"]=="7") echo ' selected="selected"'; ?>>7 days</option>
             <option value="8"<?php if ($Options["expire_days"]=="8") echo ' selected="selected"'; ?>>8 days</option>
             <option value="9"<?php if ($Options["expire_days"]=="9") echo ' selected="selected"'; ?>>9 days</option>
        <?php for($i=10; $i<=1095; $i+=5) { ?>         
          	<option value="<?php echo $i; ?>"<?php if ($Options["expire_days"]==$i) echo ' selected="selected"'; ?>>
				<?php if($i==365) { echo " -- 1 year -- "; } elseif($i==730) { echo " -- 2 years -- "; } elseif($i==1095) { echo " -- 3 years -- "; } else { echo $i." days"; } ?> 
            </option>
        <?php } ?>
        </select>        
        </td>
      </tr>  
      <tr>
        <td class="left_top">Action after Classified Ads expire:<br />
          <span style="font-size:11px"><em>select the action after Classified Ads expire</em></span></td>
        <td class="left_top">
        <select name="del_after_expire">          
          <option value="del"<?php if ($Options["del_after_expire"]=='del') echo ' selected="selected"'; ?>>Delete after expire</option>
          <option value="hidden"<?php if ($Options["del_after_expire"]=='hidden') echo ' selected="selected"'; ?>>Move to expired and option to extend with another period</option>
        </select>
        </td>
      </tr>
      <tr>
        <td class="left_top">Email after expire:<br />
          <span style="font-size:11px"><em>check if you want to send an notification email to the publisher for the Classified Ads expiration</em></span></td>
        <td class="left_top"><input name="email_after_expire" type="checkbox" value="true"<?php if ($Options["email_after_expire"]=='true') echo ' checked="checked"'; ?> /></td>
      </tr> 
      
      <tr>
        <td>&nbsp;</td>
        <td><input name="submit2" type="submit" value="Save" class="submitButton" /></td>
      </tr> 
    </table>
    
    
    <table border="0" cellspacing="0" cellpadding="8" class="allTables">
      <tr>
        <td colspan="3" class="headlist">SMTP Authentication options</td>
      </tr> 
      <tr>
        <td class="left_top" width="40%">Use SMTP Authentication for sending email newsletters: </td>
        <td class="left_top">
          <select name="smtp_auth"> 
            <option value="no"<?php if($Options["smtp_auth"]=='no') echo ' selected="selected"'; ?>>no</option>
            <option value="yes"<?php if($Options["smtp_auth"]=='yes') echo ' selected="selected"'; ?>>yes</option>
          </select>
        </td>
      </tr>             
      <tr>
        <td class="left_top">SMTP server:</td>
        <td class="left_top"><input name="smtp_server" type="text" size="30" value="<?php echo ReadDB($Options["smtp_server"]); ?>" /></td>
      </tr>             
      <tr>
        <td class="left_top">SMTP port:</td>
        <td class="left_top"><input name="smtp_port" type="text" size="5" value="<?php echo ReadDB($Options["smtp_port"]); ?>" /></td>
      </tr> 
                 
      <tr>
        <td class="left_top">SMTP email(SMTP account username):</td>
        <td class="left_top"><input name="smtp_email" type="text" size="30" value="<?php echo ReadDB($Options["smtp_email"]); ?>" /></td>
      </tr>  
                 
      <tr>
        <td class="left_top">SMTP password:</td>
        <td class="left_top"><input name="smtp_pass" type="text" size="30" value="<?php echo ReadDB($Options["smtp_pass"]); ?>" /></td>
      </tr> 
      <tr>
        <td class="left_top">Use SMTP secure: </td>
        <td class="left_top">
          <select name="smtp_secure"> 
            <option value=""<?php if($Options["smtp_secure"]=='') echo ' selected="selected"'; ?>>none</option>
            <option value="tls"<?php if($Options["smtp_secure"]=='tls') echo ' selected="selected"'; ?>>tls</option>
            <option value="ssl"<?php if($Options["smtp_secure"]=='ssl') echo ' selected="selected"'; ?>>ssl</option>
          </select>
        </td>
      </tr>          
      <tr>
        <td>&nbsp;</td>
        <td><input name="submit" type="submit" value="Save" class="submitButton" /></td>
      </tr>
    </table>
    
    
    <table border="0" cellspacing="0" cellpadding="8" class="allTables"> 
      <tr>
        <td colspan="3" class="headlist">Other options</td>
      </tr> 
      <tr>
        <td width="45%" class="left_top">Currency:</td>
        <td class="left_top">
        <select name="currency"> 
        <?php for($i=1; $i<=count($CurrAbr); $i++) { ?>         
            <option value="<?php echo $i; ?>"<?php if ($Options["currency"]==$i) echo ' selected="selected"'; ?>><?php echo $CurrAbr[$i]; ?></option>
        <?php } ?>
        </select>        
        </td>
      </tr>  
      <tr>
        <td class="left_top">Default category<br />
        <span style="font-size:11px"><em>Set the default category that will be shown when the Classified Ads loaded</em></span></td>
        <td class="left_top">
        <select name="default_cat"> 
        	<option value="0"<?php if($Options["default_cat"]=="0") echo ' selected="selected"';?>><?php if($OptionsLang["All_Ads"]!="") echo $OptionsLang["All_Ads"]; else echo "ALL";?></option>
        <?php 
		$sql = "SELECT * FROM ".$TABLE["Categories"]." ORDER BY cat_name ASC";
		$sql_result = sql_result($sql);
		while ($Cat = mysqli_fetch_assoc($sql_result)) { ?>  
          	<option value="<?php echo $Cat["id"]; ?>"<?php if($Options["default_cat"]==$Cat["id"]) echo ' selected="selected"'?>><?php echo ReadDB($Cat["cat_name"]); ?></option>
        <?php } ?>
        </select>          
        </td>
      </tr> 
      
      <tr>
        <td class="left_top">Show "ALL Ads" in category dropdown menu:</td>
        <td class="left_top">
        <select name="showallads_cat">          
          	<option value="yes"<?php if ($Options["showallads_cat"]=='yes') echo ' selected="selected"'; ?>>yes</option>
          	<option value="no"<?php if ($Options["showallads_cat"]=='no') echo ' selected="selected"'; ?>>no</option>
        </select>
        </td>
      </tr>
      
      <tr>
        <td class="left_top">Maximum number of characters in "Description" when submit listing:<br />
          <span style="font-size:11px"><em>Set the limit of characters in the description area</em></span> </td>
        <td class="left_top"><input name="char_limit" type="text" size="3" value="<?php echo ReadDB($Options["char_limit"]); ?>" /></td>
      </tr>
       
      <tr>
        <td class="left_top">Image size:<br />
          <span style="font-size:11px"><em>Set the width of the uploaded photo in px.</em></span></td>
        <td class="left_top">
        <select name="imgwidth">
        	<option value="500px"<?php if($Options["imgwidth"]=='500px') echo ' selected="selected"' ?>>500px</option>
            <option value="480px"<?php if($Options["imgwidth"]=='480px') echo ' selected="selected"' ?>>480px</option>
            <option value="460px"<?php if($Options["imgwidth"]=='460px') echo ' selected="selected"' ?>>460px</option>
            <option value="440px"<?php if($Options["imgwidth"]=='440px') echo ' selected="selected"' ?>>440px</option>
            <option value="420px"<?php if($Options["imgwidth"]=='420px') echo ' selected="selected"' ?>>420px</option>
            <option value="400px"<?php if($Options["imgwidth"]=='400px') echo ' selected="selected"' ?>>400px</option>
            <option value="380px"<?php if($Options["imgwidth"]=='380px') echo ' selected="selected"' ?>>380px</option>
            <option value="360px"<?php if($Options["imgwidth"]=='360px') echo ' selected="selected"' ?>>360px</option>
            <option value="340px"<?php if($Options["imgwidth"]=='340px') echo ' selected="selected"' ?>>340px</option>
            <option value="320px"<?php if($Options["imgwidth"]=='320px') echo ' selected="selected"' ?>>320px</option>
            <option value="300px"<?php if($Options["imgwidth"]=='300px') echo ' selected="selected"' ?>>300px</option>
            <option value="280px"<?php if($Options["imgwidth"]=='280px') echo ' selected="selected"' ?>>280px</option>
            <option value="260px"<?php if($Options["imgwidth"]=='260px') echo ' selected="selected"' ?>>260px</option>
            <option value="240px"<?php if($Options["imgwidth"]=='240px') echo ' selected="selected"' ?>>240px</option>
            <option value="220px"<?php if($Options["imgwidth"]=='220px') echo ' selected="selected"' ?>>220px</option>
            <option value="200px"<?php if($Options["imgwidth"]=='200px') echo ' selected="selected"' ?>>200px</option>
            <option value="180px"<?php if($Options["imgwidth"]=='180px') echo ' selected="selected"' ?>>180px</option>
            <option value="160px"<?php if($Options["imgwidth"]=='160px') echo ' selected="selected"' ?>>160px</option>
            <option value="140px"<?php if($Options["imgwidth"]=='140px') echo ' selected="selected"' ?>>140px</option>
            <option value="120px"<?php if($Options["imgwidth"]=='120px') echo ' selected="selected"' ?>>120px</option>
            <option value="100px"<?php if($Options["imgwidth"]=='100px') echo ' selected="selected"' ?>>100px</option>
        </select>
        </td>
      </tr>
      <tr>
        <td class="left_top">Type of the Captcha Verification Code:</td>
        <td class="left_top">
        <select name="captcha">          
            <option value="recap"<?php if ($Options["captcha"]=='recap') echo ' selected="selected"'; ?>>phpcaptcha (highly secured)</option>
            <option value="capmath"<?php if ($Options["captcha"]=='capmath') echo ' selected="selected"'; ?>>Mathematical Captcha</option>
            <option value="cap"<?php if ($Options["captcha"]=='cap') echo ' selected="selected"'; ?>>Simple Captcha</option>
            <option value="vsc"<?php if ($Options["captcha"]=='vsc') echo ' selected="selected"'; ?>>Very Simple Captcha</option>
            <option value="nocap"<?php if ($Options["captcha"]=='nocap') echo ' selected="selected"'; ?>>No Captcha(unsecured)</option>
        </select>
        </td>
      </tr>
      <tr>
        <td class="left_top">"Login & Submit Ads" link open mode:</td>
        <td class="left_top">
        <select name="submit_open_mode">
            <option value="_blank"<?php if($Options["submit_open_mode"]=='_blank') echo ' selected="selected"'; ?>>_blank</option>  
            <option value="_self"<?php if($Options["submit_open_mode"]=='_self') echo ' selected="selected"'; ?>>_self</option> 
        </select>
        </td>
      </tr>
      <tr>
        <td>&nbsp;</td>
        <td><input name="submit3" type="submit" value="Save" class="submitButton" /></td>
      </tr>
    </table>  
    
    <table border="0" cellspacing="0" cellpadding="8" class="allTables">
      <tr>
        <td colspan="3" class="headlist">Create a list with banned words</td>
      </tr>
      <tr>
        <td width="45%" class="left_top">Make a list of words and classified ads containing any of these words can not be submitted.<br />
          <br />
          For example: word1,word2, word3<br />
          <br />
          /<span style="font-size:11px; font-style:italic;">Note that the words are not case sensitive. Does not matter if you type 'Word' or 'word'.</span>/
        </td>
        <td class="left_top"><textarea class="input_opt" name="ban_words" id="ban_words" cols="60" rows="5"><?php echo ReadDB($Options["ban_words"]); ?></textarea></td>
      </tr>
      <tr>
        <td>&nbsp;</td>
        <td><input name="submit2" type="submit" value="Save" class="submitButton" /></td>
      </tr>
    </table>  
	</form>


<?php
} elseif ($_REQUEST["act"]=='visual_options') {
	$sql = "SELECT * FROM ".$TABLE["Options"];
	$sql_result = sql_result($sql);
	$Options = mysqli_fetch_assoc($sql_result);
	$OptionsVis = unserialize($Options['visual']);
?>
	
    <script type="text/javascript">
		Event.observe(window, 'load', loadAccordions, false);
		function loadAccordions() {
			var bottomAccordion = new accordion('accordion_container');	
			// Open first one
			//bottomAccordion.activate($$('#accordion_container .accordion_toggle')[0]);
		}	
	</script>
	
    <div class="pageDescr">Click on any of the styles to see the options.</div>
    
    <form action="admin.php" method="post" name="form">
	<input type="hidden" name="act" value="updateOptionsVisual" />
    
    <div class="opt_headlist">Set front-end visual style </div>
	
    <div id="accordion_container"> 
    
    <div class="accordion_toggle">General style</div>
    <div class="accordion_content">
    <table border="0" cellspacing="0" cellpadding="8" class="allTables">  
      <tr>
        <td class="langLeft">Font-family:</td>
        <td class="left_top">
        	<select name="gen_font_family">
            	<option value="Arial"<?php if($OptionsVis['gen_font_family']=='Arial') echo ' selected="selected"'; ?>>Arial</option>
                <option value="Arial Black"<?php if($OptionsVis['gen_font_family']=='Arial Black') echo ' selected="selected"'; ?>>Arial Black</option>
                <option value="Book Antiqua"<?php if($OptionsVis['gen_font_family']=='Book Antiqua') echo ' selected="selected"'; ?>>Book Antiqua</option>
                <option value="Comic Sans MS"<?php if($OptionsVis['gen_font_family']=='Comic Sans MS') echo ' selected="selected"'; ?>>Comic Sans MS</option>
                <option value="Courier New"<?php if($OptionsVis['gen_font_family']=='Courier New') echo ' selected="selected"'; ?>>Courier New</option>
                <option value="Gadget"<?php if($OptionsVis['gen_font_family']=='Gadget') echo ' selected="selected"'; ?>>Gadget</option>
            	<option value="Georgia"<?php if($OptionsVis['gen_font_family']=='Georgia') echo ' selected="selected"'; ?>>Georgia</option>
                <option value="Helvetica"<?php if($OptionsVis['gen_font_family']=='Helvetica') echo ' selected="selected"'; ?>>Helvetica</option>
                <option value="Impact"<?php if($OptionsVis['gen_font_family']=='Impact') echo ' selected="selected"'; ?>>Impact</option>
                <option value="Lucida Console"<?php if($OptionsVis['gen_font_family']=='Lucida Console') echo ' selected="selected"'; ?>>Lucida Console</option>
                <option value="Lucida Sans Unicode"<?php if($OptionsVis['gen_font_family']=='Lucida Sans Unicode') echo ' selected="selected"'; ?>>Lucida Sans Unicode</option>
                <option value="Palatino Linotype"<?php if($OptionsVis['gen_font_family']=='Palatino Linotype') echo ' selected="selected"'; ?>>Palatino Linotype</option>
                <option value="Tahoma"<?php if($OptionsVis['gen_font_family']=='Tahoma') echo ' selected="selected"'; ?>>Tahoma</option>
                <option value="Times New Roman"<?php if($OptionsVis['gen_font_family']=='Times New Roman') echo ' selected="selected"';?>>Times New Roman</option>
                <option value="Trebuchet MS"<?php if($OptionsVis['gen_font_family']=='Trebuchet MS') echo ' selected="selected"'; ?>>Trebuchet MS</option>
                <option value="Verdana"<?php if($OptionsVis['gen_font_family']=='Verdana') echo ' selected="selected"'; ?>>Verdana</option>
                <option value="inherit"<?php if($OptionsVis['gen_font_family']=='inherit') echo ' selected="selected"'; ?>>inherit</option>
            </select>
        </td>
      </tr>
      <tr>
        <td class="langLeft">Font-size:</td>
        <td class="left_top">
        	<select name="gen_font_size">
            	<option value="inherit"<?php if($OptionsVis['gen_font_size']=='inherit') echo ' selected="selected"'; ?>>inherit</option>
            	<?php for($i=8; $i<=22; $i++) {?>
            	<option value="<?php echo $i;?>px"<?php if($OptionsVis['gen_font_size']==$i.'px') echo ' selected="selected"'; ?>><?php echo $i;?>px</option>
                <?php } ?>
            </select>
        </td>
      </tr> 
      
      <tr>
        <td class="langLeft">Font-color:</td>
        <td class="left_top"><input name="gen_font_color" type="text" size="7" value="<?php echo $OptionsVis["gen_font_color"]; ?>" style="color:<?php echo invert_colour($OptionsVis["gen_font_color"]); ?>;background-color:<?php echo $OptionsVis["gen_font_color"]; ?>" /><a href="javascript:void(0)" onClick="cp.select(form.gen_font_color,'pickcolor');return false;" id="pickcolor"><img src="images/color_picker.jpg" alt="pick color" width="20" height="20" border="0" align="absmiddle" /></a> &nbsp; <sub> - you can pick the color from pallette or you can put it manualy</sub></td>
      </tr>   
      <tr>
        <td class="langLeft">Background-color:</td>
        <td class="left_top"><input name="gen_bgr_color" type="text" size="7" value="<?php echo $OptionsVis["gen_bgr_color"]; ?>" style="color:<?php echo invert_colour($OptionsVis["gen_bgr_color"]); ?>;background-color:<?php echo $OptionsVis["gen_bgr_color"]; ?>" /><a href="javascript:void(0)" onClick="cp.select(form.gen_bgr_color,'pickcolor');return false;" id="pickcolor"><img src="images/color_picker.jpg" alt="pick color" width="20" height="20" border="0" align="absmiddle" /></a> &nbsp; <sub> - you can pick the color from pallette or you can put it manualy</sub></td>
      </tr>  
      <tr>
        <td class="langLeft">Line-height:</td>
        <td class="left_top">
        	<select name="gen_line_height">
            	<option value="inherit"<?php if($OptionsVis['gen_line_height']=='inherit') echo ' selected="selected"'; ?>>inherit</option>
            	<?php for($i=10; $i<=40; $i++) {?>
            	<option value="<?php echo $i;?>px"<?php if($OptionsVis['gen_line_height']==$i.'px') echo ' selected="selected"'; ?>><?php echo $i;?>px</option>
                <?php } ?>
            </select>
        </td>
      </tr>         
      <tr>
        <td class="langLeft">Classified Ads front-end width:</td>
        <td class="left_top">
        	<input name="gen_width" type="text" size="4" value="<?php echo ReadDB($OptionsVis["gen_width"]); ?>" />px
            <sub>(leave blank if you need a responsive width, so the blog will fit the resolution on all screens)</sub>
        </td>
      </tr>  
      <tr>
        <td>&nbsp;</td>
        <td><input name="submit1" type="submit" value="Save" class="submitButton" /></td>
      </tr>
      <tr>
        <td colspan="3" height="8"></td>
      </tr>
    </table> 
    </div> 
    
    
    
    <div class="accordion_toggle">Classified Ads column headings(Category, Price, Listed) style</div>
    <div class="accordion_content">   
    <table border="0" cellspacing="0" cellpadding="8" class="allTables">   
      <tr>
        <td class="langLeft">Color:</td>
        <td class="left_top"><input name="column_color" type="text" size="7" value="<?php echo $OptionsVis["column_color"]; ?>" style="color:<?php echo invert_colour($OptionsVis["column_color"]); ?>;background-color:<?php echo $OptionsVis["column_color"]; ?>" /><a href="javascript:void(0)" onClick="cp.select(form.column_color,'pickcolor');return false;" id="pickcolor"><img src="images/color_picker.jpg" alt="select color" width="20" height="20" border="0" align="absmiddle" /></a> &nbsp; <sub> - you can pick the color from pallette or you can put it manualy</sub></td>
      </tr>
      <tr>
        <td class="langLeft">Font-family:</td>
        <td class="left_top">
        	<select name="column_font">
            	<option value="Arial"<?php if($OptionsVis['column_font']=='Arial') echo ' selected="selected"'; ?>>Arial</option>
                <option value="Arial Black"<?php if($OptionsVis['column_font']=='Arial Black') echo ' selected="selected"'; ?>>Arial Black</option>
                <option value="Book Antiqua"<?php if($OptionsVis['column_font']=='Book Antiqua') echo ' selected="selected"'; ?>>Book Antiqua</option>
                <option value="Comic Sans MS"<?php if($OptionsVis['column_font']=='Comic Sans MS') echo ' selected="selected"'; ?>>Comic Sans MS</option>
                <option value="Courier New"<?php if($OptionsVis['column_font']=='Courier New') echo ' selected="selected"'; ?>>Courier New</option>
                <option value="Gadget"<?php if($OptionsVis['column_font']=='Gadget') echo ' selected="selected"'; ?>>Gadget</option>
            	<option value="Georgia"<?php if($OptionsVis['column_font']=='Georgia') echo ' selected="selected"'; ?>>Georgia</option>
                <option value="Helvetica"<?php if($OptionsVis['column_font']=='Helvetica') echo ' selected="selected"'; ?>>Helvetica</option>
                <option value="Impact"<?php if($OptionsVis['column_font']=='Impact') echo ' selected="selected"'; ?>>Impact</option>
                <option value="Lucida Console"<?php if($OptionsVis['column_font']=='Lucida Console') echo ' selected="selected"'; ?>>Lucida Console</option>
                <option value="Lucida Sans Unicode"<?php if($OptionsVis['column_font']=='Lucida Sans Unicode') echo ' selected="selected"'; ?>>Lucida Sans Unicode</option>
                <option value="Palatino Linotype"<?php if($OptionsVis['column_font']=='Palatino Linotype') echo ' selected="selected"'; ?>>Palatino Linotype</option>
                <option value="Tahoma"<?php if($OptionsVis['column_font']=='Tahoma') echo ' selected="selected"'; ?>>Tahoma</option>
                <option value="Times New Roman"<?php if($OptionsVis['column_font']=='Times New Roman') echo ' selected="selected"';?>>Times New Roman</option>
                <option value="Trebuchet MS"<?php if($OptionsVis['column_font']=='Trebuchet MS') echo ' selected="selected"'; ?>>Trebuchet MS</option>
                <option value="Verdana"<?php if($OptionsVis['column_font']=='Verdana') echo ' selected="selected"'; ?>>Verdana</option>
                <option value="inherit"<?php if($OptionsVis['column_font']=='inherit') echo ' selected="selected"'; ?>>inherit</option>
            </select>
        </td>
      </tr>
      <tr>
        <td class="langLeft">Font-size:</td>
        <td class="left_top">
        	<select name="column_size">
            	<option value="inherit"<?php if($OptionsVis['column_size']=='inherit') echo ' selected="selected"'; ?>>inherit</option>
            	<?php for($i=9; $i<=30; $i++) {?>
            	<option value="<?php echo $i;?>px"<?php if($OptionsVis['column_size']==$i.'px') echo ' selected="selected"'; ?>><?php echo $i;?>px</option>
                <?php } ?>
            </select>
        </td>
      </tr>
      <tr>
        <td class="langLeft">Font-weight:</td>
        <td class="left_top">
        	<select name="column_font_weight">
            	<option value="normal"<?php if($OptionsVis['column_font_weight']=='normal') echo ' selected="selected"'; ?>>normal</option>
            	<option value="bold"<?php if($OptionsVis['column_font_weight']=='bold') echo ' selected="selected"'; ?>>bold</option>
                <option value="inherit"<?php if($OptionsVis['column_font_weight']=='inherit') echo ' selected="selected"'; ?>>inherit</option>
            </select>
        </td>
      </tr>
      <tr>
        <td class="langLeft">Font-style:</td>
        <td class="left_top">
        	<select name="column_font_style">
            	<option value="normal"<?php if($OptionsVis['column_font_style']=='normal') echo ' selected="selected"'; ?>>normal</option>
            	<option value="italic"<?php if($OptionsVis['column_font_style']=='italic') echo ' selected="selected"'; ?>>italic</option>
                <option value="oblique"<?php if($OptionsVis['column_font_style']=='oblique') echo ' selected="selected"'; ?>>oblique</option>
                <option value="inherit"<?php if($OptionsVis['column_font_style']=='inherit') echo ' selected="selected"'; ?>>inherit</option>
            </select>
        </td>
      </tr>
      <tr>
        <td>&nbsp;</td>
        <td><input name="submit2" type="submit" value="Save" class="submitButton" /></td>
      </tr>         
      <tr>
        <td colspan="3" height="8"></td>
      </tr>
    </table> 
    </div> 
      
    
    
    <div class="accordion_toggle">Classified Ads Product/Service/Title style</div>
    <div class="accordion_content">   
    <table border="0" cellspacing="0" cellpadding="8" class="allTables">   
      <tr>
        <td class="langLeft">Color:</td>
        <td class="left_top"><input name="title_color" type="text" size="7" value="<?php echo $OptionsVis["title_color"]; ?>" style="color:<?php echo invert_colour($OptionsVis["title_color"]); ?>;background-color:<?php echo $OptionsVis["title_color"]; ?>" /><a href="javascript:void(0)" onClick="cp.select(form.title_color,'pickcolor');return false;" id="pickcolor"><img src="images/color_picker.jpg" alt="select color" width="20" height="20" border="0" align="absmiddle" /></a> &nbsp; <sub> - you can pick the color from pallette or you can put it manualy</sub></td>
      </tr>
      <tr>
        <td class="langLeft">Font-family:</td>
        <td class="left_top">
        	<select name="title_font">
            	<option value="Arial"<?php if($OptionsVis['title_font']=='Arial') echo ' selected="selected"'; ?>>Arial</option>
                <option value="Arial Black"<?php if($OptionsVis['title_font']=='Arial Black') echo ' selected="selected"'; ?>>Arial Black</option>
                <option value="Book Antiqua"<?php if($OptionsVis['title_font']=='Book Antiqua') echo ' selected="selected"'; ?>>Book Antiqua</option>
                <option value="Comic Sans MS"<?php if($OptionsVis['title_font']=='Comic Sans MS') echo ' selected="selected"'; ?>>Comic Sans MS</option>
                <option value="Courier New"<?php if($OptionsVis['title_font']=='Courier New') echo ' selected="selected"'; ?>>Courier New</option>
                <option value="Gadget"<?php if($OptionsVis['title_font']=='Gadget') echo ' selected="selected"'; ?>>Gadget</option>
            	<option value="Georgia"<?php if($OptionsVis['title_font']=='Georgia') echo ' selected="selected"'; ?>>Georgia</option>
                <option value="Helvetica"<?php if($OptionsVis['title_font']=='Helvetica') echo ' selected="selected"'; ?>>Helvetica</option>
                <option value="Impact"<?php if($OptionsVis['title_font']=='Impact') echo ' selected="selected"'; ?>>Impact</option>
                <option value="Lucida Console"<?php if($OptionsVis['title_font']=='Lucida Console') echo ' selected="selected"'; ?>>Lucida Console</option>
                <option value="Lucida Sans Unicode"<?php if($OptionsVis['title_font']=='Lucida Sans Unicode') echo ' selected="selected"'; ?>>Lucida Sans Unicode</option>
                <option value="Palatino Linotype"<?php if($OptionsVis['title_font']=='Palatino Linotype') echo ' selected="selected"'; ?>>Palatino Linotype</option>
                <option value="Tahoma"<?php if($OptionsVis['title_font']=='Tahoma') echo ' selected="selected"'; ?>>Tahoma</option>
                <option value="Times New Roman"<?php if($OptionsVis['title_font']=='Times New Roman') echo ' selected="selected"';?>>Times New Roman</option>
                <option value="Trebuchet MS"<?php if($OptionsVis['title_font']=='Trebuchet MS') echo ' selected="selected"'; ?>>Trebuchet MS</option>
                <option value="Verdana"<?php if($OptionsVis['title_font']=='Verdana') echo ' selected="selected"'; ?>>Verdana</option>
                <option value="inherit"<?php if($OptionsVis['title_font']=='inherit') echo ' selected="selected"'; ?>>inherit</option>
            </select>
        </td>
      </tr>
      <tr>
        <td class="langLeft">Font-size:</td>
        <td class="left_top">
        	<select name="title_size">
            	<option value="inherit"<?php if($OptionsVis['title_size']=='inherit') echo ' selected="selected"'; ?>>inherit</option>
            	<?php for($i=9; $i<=30; $i++) {?>
            	<option value="<?php echo $i;?>px"<?php if($OptionsVis['title_size']==$i.'px') echo ' selected="selected"'; ?>><?php echo $i;?>px</option>
                <?php } ?>
            </select>
        </td>
      </tr>
      <tr>
        <td class="langLeft">Font-weight:</td>
        <td class="left_top">
        	<select name="title_font_weight">
            	<option value="normal"<?php if($OptionsVis['title_font_weight']=='normal') echo ' selected="selected"'; ?>>normal</option>
            	<option value="bold"<?php if($OptionsVis['title_font_weight']=='bold') echo ' selected="selected"'; ?>>bold</option>
                <option value="inherit"<?php if($OptionsVis['title_font_weight']=='inherit') echo ' selected="selected"'; ?>>inherit</option>
            </select>
        </td>
      </tr>
      <tr>
        <td class="langLeft">Font-style:</td>
        <td class="left_top">
        	<select name="title_font_style">
            	<option value="normal"<?php if($OptionsVis['title_font_style']=='normal') echo ' selected="selected"'; ?>>normal</option>
            	<option value="italic"<?php if($OptionsVis['title_font_style']=='italic') echo ' selected="selected"'; ?>>italic</option>
                <option value="oblique"<?php if($OptionsVis['title_font_style']=='oblique') echo ' selected="selected"'; ?>>oblique</option>
                <option value="inherit"<?php if($OptionsVis['title_font_style']=='inherit') echo ' selected="selected"'; ?>>inherit</option>
            </select>
        </td>
      </tr>
      <tr>
        <td class="langLeft">Text-align:</td>
        <td class="left_top">
        	<select name="title_text_align">
            	<option value="center"<?php if($OptionsVis['title_text_align']=='center') echo ' selected="selected"'; ?>>center</option>
            	<option value="justify"<?php if($OptionsVis['title_text_align']=='justify') echo ' selected="selected"'; ?>>justify</option>
                <option value="left"<?php if($OptionsVis['title_text_align']=='left') echo ' selected="selected"'; ?>>left</option>right
                <option value="right"<?php if($OptionsVis['title_text_align']=='right') echo ' selected="selected"'; ?>>right</option>
                <option value="inherit"<?php if($OptionsVis['title_text_align']=='inherit') echo ' selected="selected"'; ?>>inherit</option>
            </select>
        </td>
      </tr>
      <tr>
        <td>&nbsp;</td>
        <td><input name="submit3" type="submit" value="Save" class="submitButton" /></td>
      </tr> 
      <tr>
        <td colspan="3" height="8"></td>
      </tr>
    </table> 
    </div> 
      
    
    <div class="accordion_toggle">Product/Service/Title style in the Classified Ads listing</div>
    <div class="accordion_content">   
    <table border="0" cellspacing="0" cellpadding="8" class="allTables">   
      <tr>
        <td class="langLeft">Listing color:</td>
        <td class="left_top"><input name="summ_title_color" type="text" size="7" value="<?php echo $OptionsVis["summ_title_color"]; ?>" style="color:<?php echo invert_colour($OptionsVis["summ_title_color"]); ?>;background-color:<?php echo $OptionsVis["summ_title_color"]; ?>" /><a href="javascript:void(0)" onClick="cp.select(form.summ_title_color,'pickcolor');return false;" id="pickcolor"><img src="images/color_picker.jpg" alt="select color" width="20" height="20" border="0" align="absmiddle" /></a> &nbsp; <sub> - you can pick the color from pallette or you can put it manualy</sub></td>
      </tr>
      <tr>
        <td class="langLeft">Listing font-family:</td>
        <td class="left_top">
        	<select name="summ_title_font">
            	<option value="Arial"<?php if($OptionsVis['summ_title_font']=='Arial') echo ' selected="selected"'; ?>>Arial</option>
                <option value="Arial Black"<?php if($OptionsVis['summ_title_font']=='Arial Black') echo ' selected="selected"'; ?>>Arial Black</option>
                <option value="Book Antiqua"<?php if($OptionsVis['summ_title_font']=='Book Antiqua') echo ' selected="selected"'; ?>>Book Antiqua</option>
                <option value="Comic Sans MS"<?php if($OptionsVis['summ_title_font']=='Comic Sans MS') echo ' selected="selected"'; ?>>Comic Sans MS</option>
                <option value="Courier New"<?php if($OptionsVis['summ_title_font']=='Courier New') echo ' selected="selected"'; ?>>Courier New</option>
                <option value="Gadget"<?php if($OptionsVis['summ_title_font']=='Gadget') echo ' selected="selected"'; ?>>Gadget</option>
            	<option value="Georgia"<?php if($OptionsVis['summ_title_font']=='Georgia') echo ' selected="selected"'; ?>>Georgia</option>
                <option value="Helvetica"<?php if($OptionsVis['summ_title_font']=='Helvetica') echo ' selected="selected"'; ?>>Helvetica</option>
                <option value="Impact"<?php if($OptionsVis['summ_title_font']=='Impact') echo ' selected="selected"'; ?>>Impact</option>
                <option value="Lucida Console"<?php if($OptionsVis['summ_title_font']=='Lucida Console') echo ' selected="selected"'; ?>>Lucida Console</option>
                <option value="Lucida Sans Unicode"<?php if($OptionsVis['summ_title_font']=='Lucida Sans Unicode') echo ' selected="selected"'; ?>>Lucida Sans Unicode</option>
                <option value="Palatino Linotype"<?php if($OptionsVis['summ_title_font']=='Palatino Linotype') echo ' selected="selected"'; ?>>Palatino Linotype</option>
                <option value="Tahoma"<?php if($OptionsVis['summ_title_font']=='Tahoma') echo ' selected="selected"'; ?>>Tahoma</option>
                <option value="Times New Roman"<?php if($OptionsVis['summ_title_font']=='Times New Roman') echo ' selected="selected"';?>>Times New Roman</option>
                <option value="Trebuchet MS"<?php if($OptionsVis['summ_title_font']=='Trebuchet MS') echo ' selected="selected"'; ?>>Trebuchet MS</option>
                <option value="Verdana"<?php if($OptionsVis['summ_title_font']=='Verdana') echo ' selected="selected"'; ?>>Verdana</option>
                <option value="inherit"<?php if($OptionsVis['summ_title_font']=='inherit') echo ' selected="selected"'; ?>>inherit</option>
            </select>
        </td>
      </tr>
      <tr>
        <td class="langLeft">Listing font-size:</td>
        <td class="left_top">
        	<select name="summ_title_size">
            	<option value="inherit"<?php if($OptionsVis['summ_title_size']=='inherit') echo ' selected="selected"'; ?>>inherit</option>       	
                <?php for($i=9; $i<=30; $i++) {?>
            	<option value="<?php echo $i;?>px"<?php if($OptionsVis['summ_title_size']==$i.'px') echo ' selected="selected"'; ?>><?php echo $i;?>px</option>
                <?php } ?>
            </select>
        </td>
      </tr>
      <tr>
        <td class="langLeft">Listing font-weight:</td>
        <td class="left_top">
        	<select name="summ_title_font_weight">
            	<option value="normal"<?php if($OptionsVis['summ_title_font_weight']=='normal') echo ' selected="selected"'; ?>>normal</option>
            	<option value="bold"<?php if($OptionsVis['summ_title_font_weight']=='bold') echo ' selected="selected"'; ?>>bold</option>
                <option value="inherit"<?php if($OptionsVis['summ_title_font_weight']=='inherit') echo ' selected="selected"'; ?>>inherit</option>
            </select>
        </td>
      </tr>
      <tr>
        <td class="langLeft">Listing font-style:</td>
        <td class="left_top">
        	<select name="summ_title_font_style">
            	<option value="normal"<?php if($OptionsVis['summ_title_font_style']=='normal') echo ' selected="selected"'; ?>>normal</option>
            	<option value="italic"<?php if($OptionsVis['summ_title_font_style']=='italic') echo ' selected="selected"'; ?>>italic</option>
                <option value="oblique"<?php if($OptionsVis['summ_title_font_style']=='oblique') echo ' selected="selected"'; ?>>oblique</option>
                <option value="inherit"<?php if($OptionsVis['summ_title_font_style']=='inherit') echo ' selected="selected"'; ?>>inherit</option>
            </select>
        </td>
      </tr>
      <tr>
        <td class="langLeft">Listing text-align:</td>
        <td class="left_top">
        	<select name="summ_title_text_align">
            	<option value="center"<?php if($OptionsVis['summ_title_text_align']=='center') echo ' selected="selected"'; ?>>center</option>
            	<option value="justify"<?php if($OptionsVis['summ_title_text_align']=='justify') echo ' selected="selected"'; ?>>justify</option>
                <option value="left"<?php if($OptionsVis['summ_title_text_align']=='left') echo ' selected="selected"'; ?>>left</option>right
                <option value="right"<?php if($OptionsVis['summ_title_text_align']=='right') echo ' selected="selected"'; ?>>right</option>
                <option value="inherit"<?php if($OptionsVis['summ_title_text_align']=='inherit') echo ' selected="selected"'; ?>>inherit</option>
            </select>
        </td>
      </tr>
      <tr>
        <td>&nbsp;</td>
        <td><input name="submit4" type="submit" value="Save" class="submitButton" /></td>
      </tr> 
      <tr>
        <td colspan="3" height="8"></td>
      </tr>
    </table> 
    </div> 
      
   	
    <div class="accordion_toggle">Classified Ads price style</div>
    <div class="accordion_content">   
    <table border="0" cellspacing="0" cellpadding="8" class="allTables"> 
      <tr>
        <td class="langLeft">Price color:</td>
        <td class="left_top"><input name="price_color" type="text" size="7" value="<?php echo $OptionsVis["price_color"]; ?>" style="color:<?php echo invert_colour($OptionsVis["price_color"]); ?>;background-color:<?php echo $OptionsVis["price_color"]; ?>" /><a href="javascript:void(0)" onClick="cp.select(form.price_color,'pickcolor');return false;" id="pickcolor"><img src="images/color_picker.jpg" alt="select color" width="20" height="20" border="0" align="absmiddle" /></a> &nbsp; <sub> - you can pick the color from pallette or you can put it manualy</sub></td>
      </tr>
      <tr>
        <td class="langLeft">Price font-family:</td>
        <td class="left_top">
        	<select name="price_font">
            	<option value="Arial"<?php if($OptionsVis['price_font']=='Arial') echo ' selected="selected"'; ?>>Arial</option>
                <option value="Arial Black"<?php if($OptionsVis['price_font']=='Arial Black') echo ' selected="selected"'; ?>>Arial Black</option>
                <option value="Book Antiqua"<?php if($OptionsVis['price_font']=='Book Antiqua') echo ' selected="selected"'; ?>>Book Antiqua</option>
                <option value="Comic Sans MS"<?php if($OptionsVis['price_font']=='Comic Sans MS') echo ' selected="selected"'; ?>>Comic Sans MS</option>
                <option value="Courier New"<?php if($OptionsVis['price_font']=='Courier New') echo ' selected="selected"'; ?>>Courier New</option>
                <option value="Gadget"<?php if($OptionsVis['price_font']=='Gadget') echo ' selected="selected"'; ?>>Gadget</option>
            	<option value="Georgia"<?php if($OptionsVis['price_font']=='Georgia') echo ' selected="selected"'; ?>>Georgia</option>
                <option value="Helvetica"<?php if($OptionsVis['price_font']=='Helvetica') echo ' selected="selected"'; ?>>Helvetica</option>
                <option value="Impact"<?php if($OptionsVis['price_font']=='Impact') echo ' selected="selected"'; ?>>Impact</option>
                <option value="Lucida Console"<?php if($OptionsVis['price_font']=='Lucida Console') echo ' selected="selected"'; ?>>Lucida Console</option>
                <option value="Lucida Sans Unicode"<?php if($OptionsVis['price_font']=='Lucida Sans Unicode') echo ' selected="selected"'; ?>>Lucida Sans Unicode</option>
                <option value="Palatino Linotype"<?php if($OptionsVis['price_font']=='Palatino Linotype') echo ' selected="selected"'; ?>>Palatino Linotype</option>
                <option value="Tahoma"<?php if($OptionsVis['price_font']=='Tahoma') echo ' selected="selected"'; ?>>Tahoma</option>
                <option value="Times New Roman"<?php if($OptionsVis['price_font']=='Times New Roman') echo ' selected="selected"';?>>Times New Roman</option>
                <option value="Trebuchet MS"<?php if($OptionsVis['price_font']=='Trebuchet MS') echo ' selected="selected"'; ?>>Trebuchet MS</option>
                <option value="Verdana"<?php if($OptionsVis['price_font']=='Verdana') echo ' selected="selected"'; ?>>Verdana</option>
                <option value="inherit"<?php if($OptionsVis['price_font']=='inherit') echo ' selected="selected"'; ?>>inherit</option>
            </select>
        </td>
      </tr>
      <tr>
        <td class="langLeft">Price font-size:</td>
        <td class="left_top">
        	<select name="price_size">
            	<option value="inherit"<?php if($OptionsVis['price_size']=='inherit') echo ' selected="selected"'; ?>>inherit</option>
            	<?php for($i=9; $i<=30; $i++) {?>
            	<option value="<?php echo $i;?>px"<?php if($OptionsVis['price_size']==$i.'px') echo ' selected="selected"'; ?>><?php echo $i;?>px</option>
                <?php } ?>
            </select>
        </td>
      </tr>
      <tr>
        <td class="langLeft">Price font-weight:</td>
        <td class="left_top">
        	<select name="price_font_weight">
            	<option value="normal"<?php if($OptionsVis['price_font_weight']=='normal') echo ' selected="selected"'; ?>>normal</option>
            	<option value="bold"<?php if($OptionsVis['price_font_weight']=='bold') echo ' selected="selected"'; ?>>bold</option>
                <option value="inherit"<?php if($OptionsVis['price_font_weight']=='inherit') echo ' selected="selected"'; ?>>inherit</option>
            </select>
        </td>
      </tr>
      <tr>
        <td class="langLeft">Price font-style:</td>
        <td class="left_top">
        	<select name="price_font_style">
            	<option value="normal"<?php if($OptionsVis['price_font_style']=='normal') echo ' selected="selected"'; ?>>normal</option>
            	<option value="italic"<?php if($OptionsVis['price_font_style']=='italic') echo ' selected="selected"'; ?>>italic</option>
                <option value="oblique"<?php if($OptionsVis['price_font_style']=='oblique') echo ' selected="selected"'; ?>>oblique</option>
                <option value="inherit"<?php if($OptionsVis['price_font_style']=='inherit') echo ' selected="selected"'; ?>>inherit</option>
            </select>
        </td>
      </tr>
      <tr>
        <td class="langLeft">Price text-align:</td>
        <td class="left_top">
        	<select name="price_text_align">
            	<option value="center"<?php if($OptionsVis['price_text_align']=='center') echo ' selected="selected"'; ?>>center</option>
            	<option value="justify"<?php if($OptionsVis['price_text_align']=='justify') echo ' selected="selected"'; ?>>justify</option>
                <option value="left"<?php if($OptionsVis['price_text_align']=='left') echo ' selected="selected"'; ?>>left</option>
                <option value="right"<?php if($OptionsVis['price_text_align']=='right') echo ' selected="selected"'; ?>>right</option>
                <option value="inherit"<?php if($OptionsVis['price_text_align']=='inherit') echo ' selected="selected"'; ?>>inherit</option>
            </select>
        </td>
      </tr>           
      <tr>
        <td>&nbsp;</td>
        <td><input name="submit5" type="submit" value="Save" class="submitButton" /></td>
      </tr> 
      <tr>
        <td colspan="3" height="8"></td>
      </tr>
    </table> 
    </div> 
      
    
    <div class="accordion_toggle">Classified Ads date style</div>
    <div class="accordion_content">   
    <table border="0" cellspacing="0" cellpadding="8" class="allTables">   
      <tr>
        <td class="langLeft">Date color:</td>
        <td class="left_top"><input name="date_color" type="text" size="7" value="<?php echo $OptionsVis["date_color"]; ?>" style="color:<?php echo invert_colour($OptionsVis["date_color"]); ?>;background-color:<?php echo $OptionsVis["date_color"]; ?>" /><a href="javascript:void(0)" onClick="cp.select(form.date_color,'pickcolor');return false;" id="pickcolor"><img src="images/color_picker.jpg" alt="select color" width="20" height="20" border="0" align="absmiddle" /></a> &nbsp; <sub> - you can pick the color from pallette or you can put it manualy</sub></td>
      </tr>
      <tr>
        <td class="langLeft">Date font-family:</td>
        <td class="left_top">
        	<select name="date_font">
            	<option value="Arial"<?php if($OptionsVis['date_font']=='Arial') echo ' selected="selected"'; ?>>Arial</option>
                <option value="Arial Black"<?php if($OptionsVis['date_font']=='Arial Black') echo ' selected="selected"'; ?>>Arial Black</option>
                <option value="Book Antiqua"<?php if($OptionsVis['date_font']=='Book Antiqua') echo ' selected="selected"'; ?>>Book Antiqua</option>
                <option value="Comic Sans MS"<?php if($OptionsVis['date_font']=='Comic Sans MS') echo ' selected="selected"'; ?>>Comic Sans MS</option>
                <option value="Courier New"<?php if($OptionsVis['date_font']=='Courier New') echo ' selected="selected"'; ?>>Courier New</option>
                <option value="Gadget"<?php if($OptionsVis['date_font']=='Gadget') echo ' selected="selected"'; ?>>Gadget</option>
            	<option value="Georgia"<?php if($OptionsVis['date_font']=='Georgia') echo ' selected="selected"'; ?>>Georgia</option>
                <option value="Helvetica"<?php if($OptionsVis['date_font']=='Helvetica') echo ' selected="selected"'; ?>>Helvetica</option>
                <option value="Impact"<?php if($OptionsVis['date_font']=='Impact') echo ' selected="selected"'; ?>>Impact</option>
                <option value="Lucida Console"<?php if($OptionsVis['date_font']=='Lucida Console') echo ' selected="selected"'; ?>>Lucida Console</option>
                <option value="Lucida Sans Unicode"<?php if($OptionsVis['date_font']=='Lucida Sans Unicode') echo ' selected="selected"'; ?>>Lucida Sans Unicode</option>
                <option value="Palatino Linotype"<?php if($OptionsVis['date_font']=='Palatino Linotype') echo ' selected="selected"'; ?>>Palatino Linotype</option>
                <option value="Tahoma"<?php if($OptionsVis['date_font']=='Tahoma') echo ' selected="selected"'; ?>>Tahoma</option>
                <option value="Times New Roman"<?php if($OptionsVis['date_font']=='Times New Roman') echo ' selected="selected"';?>>Times New Roman</option>
                <option value="Trebuchet MS"<?php if($OptionsVis['date_font']=='Trebuchet MS') echo ' selected="selected"'; ?>>Trebuchet MS</option>
                <option value="Verdana"<?php if($OptionsVis['date_font']=='Verdana') echo ' selected="selected"'; ?>>Verdana</option>
                <option value="inherit"<?php if($OptionsVis['date_font']=='inherit') echo ' selected="selected"'; ?>>inherit</option>
            </select>
        </td>
      </tr>
      <tr>
        <td class="langLeft">Date font-size:</td>
        <td class="left_top">
        	<select name="date_size">
            	<option value="inherit"<?php if($OptionsVis['date_size']=='inherit') echo ' selected="selected"'; ?>>inherit</option>
            	<?php for($i=9; $i<=30; $i++) {?>
            	<option value="<?php echo $i;?>px"<?php if($OptionsVis['date_size']==$i.'px') echo ' selected="selected"'; ?>><?php echo $i;?>px</option>
                <?php } ?>
            </select>
        </td>
      </tr>
      <tr>
        <td class="langLeft">Date font-weight:</td>
        <td class="left_top">
        	<select name="date_font_weight">
            	<option value="normal"<?php if($OptionsVis['date_font_weight']=='normal') echo ' selected="selected"'; ?>>normal</option>
            	<option value="bold"<?php if($OptionsVis['date_font_weight']=='bold') echo ' selected="selected"'; ?>>bold</option>
                <option value="inherit"<?php if($OptionsVis['date_font_weight']=='inherit') echo ' selected="selected"'; ?>>inherit</option>
            </select>
        </td>
      </tr>
      <tr>
        <td class="langLeft">Date font-style:</td>
        <td class="left_top">
        	<select name="date_font_style">
            	<option value="normal"<?php if($OptionsVis['date_font_style']=='normal') echo ' selected="selected"'; ?>>normal</option>
            	<option value="italic"<?php if($OptionsVis['date_font_style']=='italic') echo ' selected="selected"'; ?>>italic</option>
                <option value="oblique"<?php if($OptionsVis['date_font_style']=='oblique') echo ' selected="selected"'; ?>>oblique</option>
                <option value="inherit"<?php if($OptionsVis['date_font_style']=='inherit') echo ' selected="selected"'; ?>>inherit</option>
            </select>
        </td>
      </tr>
      <tr>
        <td class="langLeft">Date text-align:</td>
        <td class="left_top">
        	<select name="date_text_align">
            	<option value="center"<?php if($OptionsVis['date_text_align']=='center') echo ' selected="selected"'; ?>>center</option>
            	<option value="justify"<?php if($OptionsVis['date_text_align']=='justify') echo ' selected="selected"'; ?>>justify</option>
                <option value="left"<?php if($OptionsVis['date_text_align']=='left') echo ' selected="selected"'; ?>>left</option>
                <option value="right"<?php if($OptionsVis['date_text_align']=='right') echo ' selected="selected"'; ?>>right</option>
                <option value="inherit"<?php if($OptionsVis['date_text_align']=='inherit') echo ' selected="selected"'; ?>>inherit</option>
            </select>
        </td>
      </tr>
      <tr>
        <td class="langLeft">Date format:</td>
        <td class="left_top">
        	<select name="date_format">
            	<option value="l - F j, Y"<?php if($OptionsVis['date_format']=='l - F j, Y') echo ' selected="selected"'; ?>>Monday - January 18, 2015</option>
                <option value="l - F j Y"<?php if($OptionsVis['date_format']=='l - F j Y') echo ' selected="selected"'; ?>>Monday - January 18 2015</option>
                <option value="l, F j Y"<?php if($OptionsVis['date_format']=='l, F j Y') echo ' selected="selected"'; ?>>Monday, January 18 2015</option>
            	<option value="l, F j, Y"<?php if($OptionsVis['date_format']=='l, F j, Y') echo ' selected="selected"'; ?>>Monday, January 18, 2015</option>
                <option value="l F j Y"<?php if($OptionsVis['date_format']=='l F j Y') echo ' selected="selected"'; ?>>Monday January 18 2015</option>
                <option value="l F j, Y"<?php if($OptionsVis['date_format']=='l F j Y') echo ' selected="selected"'; ?>>Monday January 18, 2015</option>
                <option value="F j Y"<?php if($OptionsVis['date_format']=='F j Y') echo ' selected="selected"'; ?>>January 18 2015</option>
                <option value="F j, Y"<?php if($OptionsVis['date_format']=='F j, Y') echo ' selected="selected"'; ?>>January 18, 2015</option>
                <option value="F jS, Y"<?php if($OptionsVis['date_format']=='F jS, Y') echo ' selected="selected"'; ?>>January 4th, 2015</option>
                <option value="F Y"<?php if($OptionsVis['date_format']=='F Y') echo ' selected="selected"'; ?>>January 2015</option>
                <option value="m-d-Y"<?php if($OptionsVis['date_format']=='m-d-Y') echo ' selected="selected"'; ?>>MM-DD-YYYY</option>
                <option value="m.d.Y"<?php if($OptionsVis['date_format']=='m.d.Y') echo ' selected="selected"'; ?>>MM.DD.YYYY</option>
                <option value="m/d/Y"<?php if($OptionsVis['date_format']=='m/d/Y') echo ' selected="selected"'; ?>>MM/DD/YYYY</option>
                <option value="m-d-y"<?php if($OptionsVis['date_format']=='m-d-y') echo ' selected="selected"'; ?>>MM-DD-YY</option>
                <option value="m.d.y"<?php if($OptionsVis['date_format']=='m.d.y') echo ' selected="selected"'; ?>>MM.DD.YY</option>
                <option value="m/d/y"<?php if($OptionsVis['date_format']=='m/d/y') echo ' selected="selected"'; ?>>MM/DD/YY</option>
                <option value="l - j F, Y"<?php if($OptionsVis['date_format']=='l - j F, Y') echo ' selected="selected"'; ?>>Monday - 18 January, 2015</option>
                <option value="l - j F Y"<?php if($OptionsVis['date_format']=='l - j F Y') echo ' selected="selected"'; ?>>Monday - 18 January 2015</option>
                <option value="l, j F Y"<?php if($OptionsVis['date_format']=='l, j F Y') echo ' selected="selected"'; ?>>Monday, 18 January 2015</option>
                <option value="l, j F, Y"<?php if($OptionsVis['date_format']=='l, j F, Y') echo ' selected="selected"'; ?>>Monday, 18 January, 2015</option>
                <option value="l j F Y"<?php if($OptionsVis['date_format']=='l j F Y') echo ' selected="selected"'; ?>>Monday 18 January 2015</option>
                <option value="l j F, Y"<?php if($OptionsVis['date_format']=='l j F, Y') echo ' selected="selected"'; ?>>Monday 18 January, 2015</option>
                <option value="d F Y"<?php if($OptionsVis['date_format']=='d F Y') echo ' selected="selected"'; ?>>18 January 2015</option>
                <option value="d F, Y"<?php if($OptionsVis['date_format']=='d F, Y') echo ' selected="selected"'; ?>>18 January, 2015</option>
                <option value="d-m-Y"<?php if($OptionsVis['date_format']=='d-m-Y') echo ' selected="selected"'; ?>>DD-MM-YYYY</option>
                <option value="d.m.Y"<?php if($OptionsVis['date_format']=='d.m.Y') echo ' selected="selected"'; ?>>DD.MM.YYYY</option>
                <option value="d/m/Y"<?php if($OptionsVis['date_format']=='d/m/Y') echo ' selected="selected"'; ?>>DD/MM/YYYY</option>
                <option value="d-m-y"<?php if($OptionsVis['date_format']=='d-m-y') echo ' selected="selected"'; ?>>DD-MM-YY</option>
                <option value="d.m.y"<?php if($OptionsVis['date_format']=='d.m.y') echo ' selected="selected"'; ?>>DD.MM.YY</option>
                <option value="d/m/y"<?php if($OptionsVis['date_format']=='d/m/y') echo ' selected="selected"'; ?>>DD/MM/YY</option>
            </select>
        </td>
      </tr>
      
      <tr>
        <td class="langLeft">Showing the time:</td>
        <td class="left_top">
        	<select name="showing_time">
            	<option value=""<?php if($OptionsVis['showing_time']=='') echo ' selected="selected"'; ?>>without time</option>
            	<option value="G:i"<?php if($OptionsVis['showing_time']=='G:i') echo ' selected="selected"'; ?>>24h format</option>
            	<option value="g:i a"<?php if($OptionsVis['showing_time']=='g:i a') echo ' selected="selected"'; ?>>12h format</option>
            </select>
        </td>
      </tr>      
      <tr>
        <td>&nbsp;</td>
        <td><input name="submit6" type="submit" value="Save" class="submitButton" /></td>
      </tr>
      <tr>
        <td colspan="3" height="8"></td>
      </tr>
    </table> 
    </div> 
      
    
    
    <div class="accordion_toggle">Classified Ads listing price style</div>
    <div class="accordion_content">   
    <table border="0" cellspacing="0" cellpadding="8" class="allTables">  
      <tr>
        <td class="langLeft">Listing price color:</td>
        <td class="left_top"><input name="summ_price_color" type="text" size="7" value="<?php echo $OptionsVis["summ_price_color"]; ?>" style="color:<?php echo invert_colour($OptionsVis["summ_price_color"]); ?>;background-color:<?php echo $OptionsVis["summ_price_color"]; ?>" /><a href="javascript:void(0)" onClick="cp.select(form.summ_price_color,'pickcolor');return false;" id="pickcolor"><img src="images/color_picker.jpg" alt="select color" width="20" height="20" border="0" align="absmiddle" /></a> &nbsp; <sub> - you can pick the color from pallette or you can put it manualy</sub></td>
      </tr>
      <tr>
        <td class="langLeft">Listing price font-family:</td>
        <td class="left_top">
        	<select name="summ_price_font">
            	<option value="Arial"<?php if($OptionsVis['summ_price_font']=='Arial') echo ' selected="selected"'; ?>>Arial</option>
                <option value="Arial Black"<?php if($OptionsVis['summ_price_font']=='Arial Black') echo ' selected="selected"'; ?>>Arial Black</option>
                <option value="Book Antiqua"<?php if($OptionsVis['summ_price_font']=='Book Antiqua') echo ' selected="selected"'; ?>>Book Antiqua</option>
                <option value="Comic Sans MS"<?php if($OptionsVis['summ_price_font']=='Comic Sans MS') echo ' selected="selected"'; ?>>Comic Sans MS</option>
                <option value="Courier New"<?php if($OptionsVis['summ_price_font']=='Courier New') echo ' selected="selected"'; ?>>Courier New</option>
                <option value="Gadget"<?php if($OptionsVis['summ_price_font']=='Gadget') echo ' selected="selected"'; ?>>Gadget</option>
            	<option value="Georgia"<?php if($OptionsVis['summ_price_font']=='Georgia') echo ' selected="selected"'; ?>>Georgia</option>
                <option value="Helvetica"<?php if($OptionsVis['summ_price_font']=='Helvetica') echo ' selected="selected"'; ?>>Helvetica</option>
                <option value="Impact"<?php if($OptionsVis['summ_price_font']=='Impact') echo ' selected="selected"'; ?>>Impact</option>
                <option value="Lucida Console"<?php if($OptionsVis['summ_price_font']=='Lucida Console') echo ' selected="selected"'; ?>>Lucida Console</option>
                <option value="Lucida Sans Unicode"<?php if($OptionsVis['summ_price_font']=='Lucida Sans Unicode') echo ' selected="selected"'; ?>>Lucida Sans Unicode</option>
                <option value="Palatino Linotype"<?php if($OptionsVis['summ_price_font']=='Palatino Linotype') echo ' selected="selected"'; ?>>Palatino Linotype</option>
                <option value="Tahoma"<?php if($OptionsVis['summ_price_font']=='Tahoma') echo ' selected="selected"'; ?>>Tahoma</option>
                <option value="Times New Roman"<?php if($OptionsVis['summ_price_font']=='Times New Roman') echo ' selected="selected"';?>>Times New Roman</option>
                <option value="Trebuchet MS"<?php if($OptionsVis['summ_price_font']=='Trebuchet MS') echo ' selected="selected"'; ?>>Trebuchet MS</option>
                <option value="Verdana"<?php if($OptionsVis['summ_price_font']=='Verdana') echo ' selected="selected"'; ?>>Verdana</option>
                <option value="inherit"<?php if($OptionsVis['summ_price_font']=='inherit') echo ' selected="selected"'; ?>>inherit</option>
            </select>
        </td>
      </tr>
      <tr>
        <td class="langLeft">Listing price font-size:</td>
        <td class="left_top">
        	<select name="summ_price_size">
            	<option value="inherit"<?php if($OptionsVis['summ_price_size']=='inherit') echo ' selected="selected"'; ?>>inherit</option>
            	<?php for($i=9; $i<=30; $i++) {?>
            	<option value="<?php echo $i;?>px"<?php if($OptionsVis['summ_price_size']==$i.'px') echo ' selected="selected"'; ?>><?php echo $i;?>px</option>
                <?php } ?>
            </select>
        </td>
      </tr>
      <tr>
        <td class="langLeft">Listing price font-weight:</td>
        <td class="left_top">
        	<select name="summ_price_font_weight">
            	<option value="normal"<?php if($OptionsVis['summ_price_font_weight']=='normal') echo ' selected="selected"'; ?>>normal</option>
            	<option value="bold"<?php if($OptionsVis['summ_price_font_weight']=='bold') echo ' selected="selected"'; ?>>bold</option>
                <option value="inherit"<?php if($OptionsVis['summ_price_font_weight']=='inherit') echo ' selected="selected"'; ?>>inherit</option>
            </select>
        </td>
      </tr>
      <tr>
        <td class="langLeft">Listing price font-style:</td>
        <td class="left_top">
        	<select name="summ_price_font_style">
            	<option value="normal"<?php if($OptionsVis['summ_price_font_style']=='normal') echo ' selected="selected"'; ?>>normal</option>
            	<option value="italic"<?php if($OptionsVis['summ_price_font_style']=='italic') echo ' selected="selected"'; ?>>italic</option>
                <option value="oblique"<?php if($OptionsVis['summ_price_font_style']=='oblique') echo ' selected="selected"'; ?>>oblique</option>
                <option value="inherit"<?php if($OptionsVis['summ_price_font_style']=='inherit') echo ' selected="selected"'; ?>>inherit</option>
            </select>
        </td>
      </tr>
      <tr>
        <td class="langLeft">Listing price text-align:</td>
        <td class="left_top">
        	<select name="summ_price_text_align">
            	<option value="center"<?php if($OptionsVis['summ_price_text_align']=='center') echo ' selected="selected"'; ?>>center</option>
            	<option value="justify"<?php if($OptionsVis['summ_price_text_align']=='justify') echo ' selected="selected"'; ?>>justify</option>
                <option value="left"<?php if($OptionsVis['summ_price_text_align']=='left') echo ' selected="selected"'; ?>>left</option>
                <option value="right"<?php if($OptionsVis['summ_price_text_align']=='right') echo ' selected="selected"'; ?>>right</option>
                <option value="inherit"<?php if($OptionsVis['summ_price_text_align']=='inherit') echo ' selected="selected"'; ?>>inherit</option>
            </select>
        </td>
      </tr>      
      
      <tr>
        <td>&nbsp;</td>
        <td><input name="submit7" type="submit" value="Save" class="submitButton" /></td>
      </tr>
      <tr>
        <td colspan="3" height="8"></td>
      </tr>
    </table> 
    </div>
      
    
    
    <div class="accordion_toggle">Classified Ads listing date style</div>
    <div class="accordion_content">   
    <table border="0" cellspacing="0" cellpadding="8" class="allTables">  
	  <tr>
        <td class="langLeft">Listing date color:</td>
        <td class="left_top"><input name="summ_date_color" type="text" size="7" value="<?php echo $OptionsVis["summ_date_color"]; ?>" style="color:<?php echo invert_colour($OptionsVis["summ_date_color"]); ?>;background-color:<?php echo $OptionsVis["summ_date_color"]; ?>" /><a href="javascript:void(0)" onClick="cp.select(form.summ_date_color,'pickcolor');return false;" id="pickcolor"><img src="images/color_picker.jpg" alt="select color" width="20" height="20" border="0" align="absmiddle" /></a> &nbsp; <sub> - you can pick the color from pallette or you can put it manualy</sub></td>
      </tr>
      <tr>
        <td class="langLeft">Listing date font-family:</td>
        <td class="left_top">
        	<select name="summ_date_font">
            	<option value="Arial"<?php if($OptionsVis['summ_date_font']=='Arial') echo ' selected="selected"'; ?>>Arial</option>
                <option value="Arial Black"<?php if($OptionsVis['summ_date_font']=='Arial Black') echo ' selected="selected"'; ?>>Arial Black</option>
                <option value="Book Antiqua"<?php if($OptionsVis['summ_date_font']=='Book Antiqua') echo ' selected="selected"'; ?>>Book Antiqua</option>
                <option value="Comic Sans MS"<?php if($OptionsVis['summ_date_font']=='Comic Sans MS') echo ' selected="selected"'; ?>>Comic Sans MS</option>
                <option value="Courier New"<?php if($OptionsVis['summ_date_font']=='Courier New') echo ' selected="selected"'; ?>>Courier New</option>
                <option value="Gadget"<?php if($OptionsVis['summ_date_font']=='Gadget') echo ' selected="selected"'; ?>>Gadget</option>
            	<option value="Georgia"<?php if($OptionsVis['summ_date_font']=='Georgia') echo ' selected="selected"'; ?>>Georgia</option>
                <option value="Helvetica"<?php if($OptionsVis['summ_date_font']=='Helvetica') echo ' selected="selected"'; ?>>Helvetica</option>
                <option value="Impact"<?php if($OptionsVis['summ_date_font']=='Impact') echo ' selected="selected"'; ?>>Impact</option>
                <option value="Lucida Console"<?php if($OptionsVis['summ_date_font']=='Lucida Console') echo ' selected="selected"'; ?>>Lucida Console</option>
                <option value="Lucida Sans Unicode"<?php if($OptionsVis['summ_date_font']=='Lucida Sans Unicode') echo ' selected="selected"'; ?>>Lucida Sans Unicode</option>
                <option value="Palatino Linotype"<?php if($OptionsVis['summ_date_font']=='Palatino Linotype') echo ' selected="selected"'; ?>>Palatino Linotype</option>
                <option value="Tahoma"<?php if($OptionsVis['summ_date_font']=='Tahoma') echo ' selected="selected"'; ?>>Tahoma</option>
                <option value="Times New Roman"<?php if($OptionsVis['summ_date_font']=='Times New Roman') echo ' selected="selected"';?>>Times New Roman</option>
                <option value="Trebuchet MS"<?php if($OptionsVis['summ_date_font']=='Trebuchet MS') echo ' selected="selected"'; ?>>Trebuchet MS</option>
                <option value="Verdana"<?php if($OptionsVis['summ_date_font']=='Verdana') echo ' selected="selected"'; ?>>Verdana</option>
                <option value="inherit"<?php if($OptionsVis['summ_date_font']=='inherit') echo ' selected="selected"'; ?>>inherit</option>
            </select>
        </td>
      </tr>
      <tr>
        <td class="langLeft">Listing date font-size:</td>
        <td class="left_top">
        	<select name="summ_date_size">
            	<option value="inherit"<?php if($OptionsVis['summ_date_size']=='inherit') echo ' selected="selected"'; ?>>inherit</option>
            	<?php for($i=9; $i<=30; $i++) {?>
            	<option value="<?php echo $i;?>px"<?php if($OptionsVis['summ_date_size']==$i.'px') echo ' selected="selected"'; ?>><?php echo $i;?>px</option>
                <?php } ?>
            </select>
        </td>
      </tr>
      <tr>
        <td class="langLeft">Listing date font-weight:</td>
        <td class="left_top">
        	<select name="summ_date_font_weight">
            	<option value="normal"<?php if($OptionsVis['summ_date_font_weight']=='normal') echo ' selected="selected"'; ?>>normal</option>
            	<option value="bold"<?php if($OptionsVis['summ_date_font_weight']=='bold') echo ' selected="selected"'; ?>>bold</option>
                <option value="inherit"<?php if($OptionsVis['summ_date_font_weight']=='inherit') echo ' selected="selected"'; ?>>inherit</option>
            </select>
        </td>
      </tr>
      <tr>
        <td class="langLeft">Listing date font-style:</td>
        <td class="left_top">
        	<select name="summ_date_font_style">
            	<option value="normal"<?php if($OptionsVis['summ_date_font_style']=='normal') echo ' selected="selected"'; ?>>normal</option>
            	<option value="italic"<?php if($OptionsVis['summ_date_font_style']=='italic') echo ' selected="selected"'; ?>>italic</option>
                <option value="oblique"<?php if($OptionsVis['summ_date_font_style']=='oblique') echo ' selected="selected"'; ?>>oblique</option>
                <option value="inherit"<?php if($OptionsVis['summ_date_font_style']=='inherit') echo ' selected="selected"'; ?>>inherit</option>
            </select>
        </td>
      </tr>
      <tr>
        <td class="langLeft">Listing date text-align:</td>
        <td class="left_top">
        	<select name="summ_date_text_align">
            	<option value="center"<?php if($OptionsVis['summ_date_text_align']=='center') echo ' selected="selected"'; ?>>center</option>
            	<option value="justify"<?php if($OptionsVis['summ_date_text_align']=='justify') echo ' selected="selected"'; ?>>justify</option>
                <option value="left"<?php if($OptionsVis['summ_date_text_align']=='left') echo ' selected="selected"'; ?>>left</option>
                <option value="right"<?php if($OptionsVis['summ_date_text_align']=='right') echo ' selected="selected"'; ?>>right</option>
                <option value="inherit"<?php if($OptionsVis['summ_date_text_align']=='inherit') echo ' selected="selected"'; ?>>inherit</option>
            </select>
        </td>
      </tr>
      <tr>
        <td class="langLeft">Listing date format:</td>
        <td class="left_top">
        	<select name="summ_date_format">
            	<option value="l - F j, Y"<?php if($OptionsVis['summ_date_format']=='l - F j, Y') echo ' selected="selected"'; ?>>Monday - January 18, 2015</option>
                <option value="l - F j Y"<?php if($OptionsVis['summ_date_format']=='l - F j Y') echo ' selected="selected"'; ?>>Monday - January 18 2015</option>
                <option value="l, F j Y"<?php if($OptionsVis['summ_date_format']=='l, F j Y') echo ' selected="selected"'; ?>>Monday, January 18 2015</option>
            	<option value="l, F j, Y"<?php if($OptionsVis['summ_date_format']=='l, F j, Y') echo ' selected="selected"'; ?>>Monday, January 18, 2015</option>
                <option value="l F j Y"<?php if($OptionsVis['summ_date_format']=='l F j Y') echo ' selected="selected"'; ?>>Monday January 18 2015</option>
                <option value="l F j, Y"<?php if($OptionsVis['summ_date_format']=='l F j Y') echo ' selected="selected"'; ?>>Monday January 18, 2015</option>
                <option value="F j Y"<?php if($OptionsVis['summ_date_format']=='F j Y') echo ' selected="selected"'; ?>>January 18 2015</option>
                <option value="F j, Y"<?php if($OptionsVis['summ_date_format']=='F j, Y') echo ' selected="selected"'; ?>>January 18, 2015</option>
                <option value="F jS, Y"<?php if($OptionsVis['summ_date_format']=='F jS, Y') echo ' selected="selected"'; ?>>January 4th, 2015</option>
                <option value="F Y"<?php if($OptionsVis['summ_date_format']=='F Y') echo ' selected="selected"'; ?>>January 2015</option>
                <option value="m-d-Y"<?php if($OptionsVis['summ_date_format']=='m-d-Y') echo ' selected="selected"'; ?>>MM-DD-YYYY</option>
                <option value="m.d.Y"<?php if($OptionsVis['summ_date_format']=='m.d.Y') echo ' selected="selected"'; ?>>MM.DD.YYYY</option>
                <option value="m/d/Y"<?php if($OptionsVis['summ_date_format']=='m/d/Y') echo ' selected="selected"'; ?>>MM/DD/YYYY</option>
                <option value="m-d-y"<?php if($OptionsVis['summ_date_format']=='m-d-y') echo ' selected="selected"'; ?>>MM-DD-YY</option>
                <option value="m.d.y"<?php if($OptionsVis['summ_date_format']=='m.d.y') echo ' selected="selected"'; ?>>MM.DD.YY</option>
                <option value="m/d/y"<?php if($OptionsVis['summ_date_format']=='m/d/y') echo ' selected="selected"'; ?>>MM/DD/YY</option>
                <option value="l - j F, Y"<?php if($OptionsVis['summ_date_format']=='l - j F, Y') echo ' selected="selected"'; ?>>Monday - 18 January, 2015</option>
                <option value="l - j F Y"<?php if($OptionsVis['summ_date_format']=='l - j F Y') echo ' selected="selected"'; ?>>Monday - 18 January 2015</option>
                <option value="l, j F Y"<?php if($OptionsVis['summ_date_format']=='l, j F Y') echo ' selected="selected"'; ?>>Monday, 18 January 2015</option>
                <option value="l, j F, Y"<?php if($OptionsVis['summ_date_format']=='l, j F, Y') echo ' selected="selected"'; ?>>Monday, 18 January, 2015</option>
                <option value="l j F Y"<?php if($OptionsVis['summ_date_format']=='l j F Y') echo ' selected="selected"'; ?>>Monday 18 January 2015</option>
                <option value="l j F, Y"<?php if($OptionsVis['summ_date_format']=='l j F, Y') echo ' selected="selected"'; ?>>Monday 18 January, 2015</option>
                <option value="d F Y"<?php if($OptionsVis['summ_date_format']=='d F Y') echo ' selected="selected"'; ?>>18 January 2015</option>
                <option value="d F, Y"<?php if($OptionsVis['summ_date_format']=='d F, Y') echo ' selected="selected"'; ?>>18 January, 2015</option>
                <option value="d-m-Y"<?php if($OptionsVis['summ_date_format']=='d-m-Y') echo ' selected="selected"'; ?>>DD-MM-YYYY</option>
                <option value="d.m.Y"<?php if($OptionsVis['summ_date_format']=='d.m.Y') echo ' selected="selected"'; ?>>DD.MM.YYYY</option>
                <option value="d/m/Y"<?php if($OptionsVis['summ_date_format']=='d/m/Y') echo ' selected="selected"'; ?>>DD/MM/YYYY</option>
                <option value="d-m-y"<?php if($OptionsVis['summ_date_format']=='d-m-y') echo ' selected="selected"'; ?>>DD-MM-YY</option>
                <option value="d.m.y"<?php if($OptionsVis['summ_date_format']=='d.m.y') echo ' selected="selected"'; ?>>DD.MM.YY</option>
                <option value="d/m/y"<?php if($OptionsVis['summ_date_format']=='d/m/y') echo ' selected="selected"'; ?>>DD/MM/YY</option>
            </select>
        </td>
      </tr>
      
      <tr>
        <td class="langLeft">Listing showing the time:</td>
        <td class="left_top">
        	<select name="summ_showing_time">
            	<option value=""<?php if($OptionsVis['summ_showing_time']=='') echo ' selected="selected"'; ?>>without time</option>
            	<option value="G:i"<?php if($OptionsVis['summ_showing_time']=='G:i') echo ' selected="selected"'; ?>>24h format</option>
            	<option value="g:i a"<?php if($OptionsVis['summ_showing_time']=='g:i a') echo ' selected="selected"'; ?>>12h format</option>
            </select>
        </td>
      </tr>
      
      <tr>
        <td>&nbsp;</td>
        <td><input name="submit8" type="submit" value="Save" class="submitButton" /></td>
      </tr>
      <tr>
        <td colspan="3" height="8"></td>
      </tr>
    </table> 
    </div> 
      
   	
    <div class="accordion_toggle">Description style</div>
    <div class="accordion_content">   
    <table border="0" cellspacing="0" cellpadding="8" class="allTables">    
      <tr>
        <td class="langLeft">Description text color:</td>
        <td class="left_top"><input name="cont_color" type="text" size="7" value="<?php echo $OptionsVis["cont_color"]; ?>" style="color:<?php echo invert_colour($OptionsVis["cont_color"]); ?>;background-color:<?php echo $OptionsVis["cont_color"]; ?>" /><a href="javascript:void(0)" onClick="cp.select(form.cont_color,'pickcolor');return false;" id="pickcolor"><img src="images/color_picker.jpg" alt="select color" width="20" height="20" border="0" align="absmiddle" /></a> &nbsp; <sub> - you can pick the color from pallette or you can put it manualy</sub></td>
      </tr>
      <tr>
        <td class="langLeft">Description text font-family:</td>
        <td class="left_top">
        	<select name="cont_font">
            	<option value="Arial"<?php if($OptionsVis['cont_font']=='Arial') echo ' selected="selected"'; ?>>Arial</option>
                <option value="Arial Black"<?php if($OptionsVis['cont_font']=='Arial Black') echo ' selected="selected"'; ?>>Arial Black</option>
                <option value="Book Antiqua"<?php if($OptionsVis['cont_font']=='Book Antiqua') echo ' selected="selected"'; ?>>Book Antiqua</option>
                <option value="Comic Sans MS"<?php if($OptionsVis['cont_font']=='Comic Sans MS') echo ' selected="selected"'; ?>>Comic Sans MS</option>
                <option value="Courier New"<?php if($OptionsVis['cont_font']=='Courier New') echo ' selected="selected"'; ?>>Courier New</option>
                <option value="Gadget"<?php if($OptionsVis['cont_font']=='Gadget') echo ' selected="selected"'; ?>>Gadget</option>
            	<option value="Georgia"<?php if($OptionsVis['cont_font']=='Georgia') echo ' selected="selected"'; ?>>Georgia</option>
                <option value="Helvetica"<?php if($OptionsVis['cont_font']=='Helvetica') echo ' selected="selected"'; ?>>Helvetica</option>
                <option value="Impact"<?php if($OptionsVis['cont_font']=='Impact') echo ' selected="selected"'; ?>>Impact</option>
                <option value="Lucida Console"<?php if($OptionsVis['cont_font']=='Lucida Console') echo ' selected="selected"'; ?>>Lucida Console</option>
                <option value="Lucida Sans Unicode"<?php if($OptionsVis['cont_font']=='Lucida Sans Unicode') echo ' selected="selected"'; ?>>Lucida Sans Unicode</option>
                <option value="Palatino Linotype"<?php if($OptionsVis['cont_font']=='Palatino Linotype') echo ' selected="selected"'; ?>>Palatino Linotype</option>
                <option value="Tahoma"<?php if($OptionsVis['cont_font']=='Tahoma') echo ' selected="selected"'; ?>>Tahoma</option>
                <option value="Times New Roman"<?php if($OptionsVis['cont_font']=='Times New Roman') echo ' selected="selected"';?>>Times New Roman</option>
                <option value="Trebuchet MS"<?php if($OptionsVis['cont_font']=='Trebuchet MS') echo ' selected="selected"'; ?>>Trebuchet MS</option>
                <option value="Verdana"<?php if($OptionsVis['cont_font']=='Verdana') echo ' selected="selected"'; ?>>Verdana</option>
                <option value="inherit"<?php if($OptionsVis['cont_font']=='inherit') echo ' selected="selected"'; ?>>inherit</option>
            </select>
        </td>
      </tr>
      <tr>
        <td class="langLeft">Description text font-size:</td>
        <td class="left_top">
        	<select name="cont_size">
            	<option value="inherit"<?php if($OptionsVis['cont_size']=='inherit') echo ' selected="selected"'; ?>>inherit</option>
            	<?php for($i=9; $i<=30; $i++) {?>
            	<option value="<?php echo $i;?>px"<?php if($OptionsVis['cont_size']==$i.'px') echo ' selected="selected"'; ?>><?php echo $i;?>px</option>
                <?php } ?>
            </select>
        </td>
      </tr>
      <tr>
        <td class="langLeft">Description text font-style:</td>
        <td class="left_top">
        	<select name="cont_font_style">
            	<option value="normal"<?php if($OptionsVis['cont_font_style']=='normal') echo ' selected="selected"'; ?>>normal</option>
            	<option value="italic"<?php if($OptionsVis['cont_font_style']=='italic') echo ' selected="selected"'; ?>>italic</option>
                <option value="oblique"<?php if($OptionsVis['cont_font_style']=='oblique') echo ' selected="selected"'; ?>>oblique</option>
                <option value="inherit"<?php if($OptionsVis['cont_font_style']=='inherit') echo ' selected="selected"'; ?>>inherit</option>
            </select>
        </td>
      </tr>
      <tr>
        <td class="langLeft">Description text text-align:</td>
        <td class="left_top">
        	<select name="cont_text_align">
            	<option value="center"<?php if($OptionsVis['cont_text_align']=='center') echo ' selected="selected"'; ?>>center</option>
            	<option value="justify"<?php if($OptionsVis['cont_text_align']=='justify') echo ' selected="selected"'; ?>>justify</option>
                <option value="left"<?php if($OptionsVis['cont_text_align']=='left') echo ' selected="selected"'; ?>>left</option>
                <option value="right"<?php if($OptionsVis['cont_text_align']=='right') echo ' selected="selected"'; ?>>right</option>
                <option value="inherit"<?php if($OptionsVis['cont_text_align']=='inherit') echo ' selected="selected"'; ?>>inherit</option>
            </select>
        </td>
      </tr>
      <tr>
        <td class="langLeft">Description text line-height:</td>
        <td class="left_top">
        	<select name="cont_line_height">
            	<option value="inherit"<?php if($OptionsVis['cont_line_height']=='inherit') echo ' selected="selected"'; ?>>inherit</option>
            	<?php for($i=10; $i<=40; $i++) {?>
            	<option value="<?php echo $i;?>px"<?php if($OptionsVis['cont_line_height']==$i.'px') echo ' selected="selected"'; ?>><?php echo $i;?>px</option>
                <?php } ?>
            </select>
        </td>
      </tr>  
      <tr>
        <td>&nbsp;</td>
        <td><input name="submit9" type="submit" value="Save" class="submitButton" /></td>
      </tr>
      <tr>
        <td colspan="3" height="8"></td>
      </tr>
    </table> 
    </div> 
       
    
    <div class="accordion_toggle">Listing short description style</div>
    <div class="accordion_content">   
    <table border="0" cellspacing="0" cellpadding="8" class="allTables">   
      <tr>
        <td class="langLeft">Short description color:</td>
        <td class="left_top"><input name="summ_color" type="text" size="7" value="<?php echo $OptionsVis["summ_color"]; ?>" style="color:<?php echo invert_colour($OptionsVis["summ_color"]); ?>;background-color:<?php echo $OptionsVis["summ_color"]; ?>" /><a href="javascript:void(0)" onClick="cp.select(form.summ_color,'pickcolor');return false;" id="pickcolor"><img src="images/color_picker.jpg" alt="select color" width="20" height="20" border="0" align="absmiddle" /></a> &nbsp; <sub> - you can pick the color from pallette or you can put it manualy</sub></td>
      </tr>
      <tr>
        <td class="langLeft">Short description font-family:</td>
        <td class="left_top">
        	<select name="summ_font">
            	<option value="Arial"<?php if($OptionsVis['summ_font']=='Arial') echo ' selected="selected"'; ?>>Arial</option>
                <option value="Arial Black"<?php if($OptionsVis['summ_font']=='Arial Black') echo ' selected="selected"'; ?>>Arial Black</option>
                <option value="Book Antiqua"<?php if($OptionsVis['summ_font']=='Book Antiqua') echo ' selected="selected"'; ?>>Book Antiqua</option>
                <option value="Comic Sans MS"<?php if($OptionsVis['summ_font']=='Comic Sans MS') echo ' selected="selected"'; ?>>Comic Sans MS</option>
                <option value="Courier New"<?php if($OptionsVis['summ_font']=='Courier New') echo ' selected="selected"'; ?>>Courier New</option>
                <option value="Gadget"<?php if($OptionsVis['summ_font']=='Gadget') echo ' selected="selected"'; ?>>Gadget</option>
            	<option value="Georgia"<?php if($OptionsVis['summ_font']=='Georgia') echo ' selected="selected"'; ?>>Georgia</option>
                <option value="Helvetica"<?php if($OptionsVis['summ_font']=='Helvetica') echo ' selected="selected"'; ?>>Helvetica</option>
                <option value="Impact"<?php if($OptionsVis['summ_font']=='Impact') echo ' selected="selected"'; ?>>Impact</option>
                <option value="Lucida Console"<?php if($OptionsVis['summ_font']=='Lucida Console') echo ' selected="selected"'; ?>>Lucida Console</option>
                <option value="Lucida Sans Unicode"<?php if($OptionsVis['summ_font']=='Lucida Sans Unicode') echo ' selected="selected"'; ?>>Lucida Sans Unicode</option>
                <option value="Palatino Linotype"<?php if($OptionsVis['summ_font']=='Palatino Linotype') echo ' selected="selected"'; ?>>Palatino Linotype</option>
                <option value="Tahoma"<?php if($OptionsVis['summ_font']=='Tahoma') echo ' selected="selected"'; ?>>Tahoma</option>
                <option value="Times New Roman"<?php if($OptionsVis['summ_font']=='Times New Roman') echo ' selected="selected"';?>>Times New Roman</option>
                <option value="Trebuchet MS"<?php if($OptionsVis['summ_font']=='Trebuchet MS') echo ' selected="selected"'; ?>>Trebuchet MS</option>
                <option value="Verdana"<?php if($OptionsVis['summ_font']=='Verdana') echo ' selected="selected"'; ?>>Verdana</option>
                <option value="inherit"<?php if($OptionsVis['summ_font']=='inherit') echo ' selected="selected"'; ?>>inherit</option>
            </select>
        </td>
      </tr>
      <tr>
        <td class="langLeft">Short description font-size:</td>
        <td class="left_top">
        	<select name="summ_size">
            	<option value="inherit"<?php if($OptionsVis['summ_size']=='inherit') echo ' selected="selected"'; ?>>inherit</option>
            	<?php for($i=9; $i<=30; $i++) {?>
            	<option value="<?php echo $i;?>px"<?php if($OptionsVis['summ_size']==$i.'px') echo ' selected="selected"'; ?>><?php echo $i;?>px</option>
                <?php } ?>
            </select>
        </td>
      </tr>
      <tr>
        <td class="langLeft">Short description font-style:</td>
        <td class="left_top">
        	<select name="summ_font_style">
            	<option value="normal"<?php if($OptionsVis['summ_font_style']=='normal') echo ' selected="selected"'; ?>>normal</option>
            	<option value="italic"<?php if($OptionsVis['summ_font_style']=='italic') echo ' selected="selected"'; ?>>italic</option>
                <option value="oblique"<?php if($OptionsVis['summ_font_style']=='oblique') echo ' selected="selected"'; ?>>oblique</option>
                <option value="inherit"<?php if($OptionsVis['summ_font_style']=='inherit') echo ' selected="selected"'; ?>>inherit</option>
            </select>
        </td>
      </tr>
      <tr>
        <td class="langLeft">Short description text-align:</td>
        <td class="left_top">
        	<select name="summ_text_align">
            	<option value="center"<?php if($OptionsVis['summ_text_align']=='center') echo ' selected="selected"'; ?>>center</option>
            	<option value="justify"<?php if($OptionsVis['summ_text_align']=='justify') echo ' selected="selected"'; ?>>justify</option>
                <option value="left"<?php if($OptionsVis['summ_text_align']=='left') echo ' selected="selected"'; ?>>left</option>
                <option value="right"<?php if($OptionsVis['summ_text_align']=='right') echo ' selected="selected"'; ?>>right</option>
                <option value="inherit"<?php if($OptionsVis['summ_text_align']=='inherit') echo ' selected="selected"'; ?>>inherit</option>
            </select>
        </td>
      </tr>
      <tr>
        <td class="langLeft">Short description line-height:</td>
        <td class="left_top">
        	<select name="summ_line_height">
            	<option value="inherit"<?php if($OptionsVis['summ_line_height']=='inherit') echo ' selected="selected"'; ?>>inherit</option>
            	<?php for($i=10; $i<=40; $i++) {?>
            	<option value="<?php echo $i;?>px"<?php if($OptionsVis['summ_line_height']==$i.'px') echo ' selected="selected"'; ?>><?php echo $i;?>px</option>
                <?php } ?>
            </select>
        </td>
      </tr>  
      <tr>
        <td>&nbsp;</td>
        <td><input name="submit9" type="submit" value="Save" class="submitButton" /></td>
      </tr>
      <tr>
        <td colspan="3" height="8"></td>
      </tr>
    </table> 
    </div> 
      
    
    <div class="accordion_toggle">Image style in Classified Ads listing</div>
    <div class="accordion_content">   
    <table border="0" cellspacing="0" cellpadding="8" class="allTables">   
      <tr>
        <td class="langLeft">Show image in the Classified Ads listing(Short description):</td>
        <td class="left_top">
        	<select name="summ_show_image">
            	<option value="yes"<?php if($OptionsVis['summ_show_image']=='yes') echo ' selected="selected"'; ?>>yes</option>
            	<option value="no"<?php if($OptionsVis['summ_show_image']=='no') echo ' selected="selected"'; ?>>no</option>
            </select>
        </td>
      </tr>  
      <tr>
        <td class="langLeft">Summary image width:</td>
        <td class="left_top"><input name="summ_img_width" type="text" size="4" value="<?php echo ReadDB($OptionsVis["summ_img_width"]); ?>" />px</td>
      </tr>  
      <tr>
        <td>&nbsp;</td>
        <td><input name="submit10" type="submit" value="Save" class="submitButton" /></td>
      </tr>
      <tr>
        <td colspan="3" height="8"></td>
      </tr>
    </table> 
    </div> 
      
    
    <div class="accordion_toggle">Featured Classified Ads style</div>
    <div class="accordion_content">   
    <table border="0" cellspacing="0" cellpadding="8" class="allTables">   
      <tr>
        <td class="langLeft">Featured background-color:</td>
        <td class="left_top"><input name="hl_bgr_color" type="text" size="7" value="<?php echo $OptionsVis["hl_bgr_color"]; ?>" style="color:<?php echo invert_colour($OptionsVis["hl_bgr_color"]); ?>;background-color:<?php echo $OptionsVis["hl_bgr_color"]; ?>" /><a href="javascript:void(0)" onClick="cp.select(form.hl_bgr_color,'pickcolor');return false;" id="pickcolor"><img src="images/color_picker.jpg" alt="pick color" width="20" height="20" border="0" align="absmiddle" /></a> &nbsp; <sub> - you can pick the color from pallette or you can put it manualy</sub></td>
      </tr>
      <tr>
        <td>&nbsp;</td>
        <td><input name="submit11" type="submit" value="Save" class="submitButton" /></td>
      </tr>
      <tr>
        <td colspan="3" height="8"></td>
      </tr>
    </table> 
    </div> 
      
   	
    
    <div class="accordion_toggle">Classified Ads pagination</div>
    <div class="accordion_content">   
    <table border="0" cellspacing="0" cellpadding="8" class="allTables">      
      <tr>
        <td class="langLeft">Pagination font-size:</td>
        <td class="left_top">
        	<select name="pag_font_size">
            	<option value="inherit"<?php if($OptionsVis['pag_font_size']=='inherit') echo ' selected="selected"'; ?>>inherit</option>
            	<?php for($i=9; $i<=30; $i++) {?>
            	<option value="<?php echo $i;?>px"<?php if($OptionsVis['pag_font_size']==$i.'px') echo ' selected="selected"'; ?>><?php echo $i;?>px</option>
                <?php } ?>
            </select>
        </td>
      </tr>    
      <tr>
        <td class="langLeft">Pagination font color:</td>
        <td class="left_top"><input name="pag_color" type="text" size="7" value="<?php echo $OptionsVis["pag_color"]; ?>" style="color:<?php echo invert_colour($OptionsVis["pag_color"]); ?>;background-color:<?php echo $OptionsVis["pag_color"]; ?>" /><a href="javascript:void(0)" onClick="cp.select(form.pag_color,'pickcolor');return false;" id="pickcolor"><img src="images/color_picker.jpg" alt="select color" width="20" height="20" border="0" align="absmiddle" /></a> &nbsp; <sub> - you can pick the color from pallette or you can put it manualy</sub></td>
      </tr> 
      <tr>
        <td class="langLeft">Pagination font-weight:</td>
        <td class="left_top">
        	<select name="pag_font_weight">
            	<option value="normal"<?php if($OptionsVis['pag_font_weight']=='normal') echo ' selected="selected"'; ?>>normal</option>
            	<option value="bold"<?php if($OptionsVis['pag_font_weight']=='bold') echo ' selected="selected"'; ?>>bold</option>
                <option value="inherit"<?php if($OptionsVis['pag_font_weight']=='inherit') echo ' selected="selected"'; ?>>inherit</option>
            </select>
        </td>
      </tr>   
      <tr>
        <td class="langLeft">Pagination alignment:</td>
        <td class="left_top">
        	<select name="pag_align">
            	<option value="left"<?php if($OptionsVis['pag_align']=='left') echo ' selected="selected"'; ?>>left</option>
            	<option value="center"<?php if($OptionsVis['pag_align']=='center') echo ' selected="selected"'; ?>>center</option>
                <option value="right"<?php if($OptionsVis['pag_align']=='right') echo ' selected="selected"'; ?>>right</option>
                <option value="inherit"<?php if($OptionsVis['pag_align']=='inherit') echo ' selected="selected"'; ?>>inherit</option>
            </select>
        </td>
      </tr>            
      <tr>
        <td>&nbsp;</td>
        <td><input name="submit12" type="submit" value="Save" class="submitButton" /></td>
      </tr>
      <tr>
        <td colspan="3" height="8"></td>
      </tr>
    </table> 
    </div>
      
    
    <div class="accordion_toggle">Navigation - 'Back' and 'Submit Classified Ad' links</div>
    <div class="accordion_content">   
    <table border="0" cellspacing="0" cellpadding="8" class="allTables"> 
      <tr>
        <td class="langLeft">Navigation font-size:</td>
        <td class="left_top">
        	<select name="link_font_size">
            	<option value="inherit"<?php if($OptionsVis['link_font_size']=='inherit') echo ' selected="selected"'; ?>>inherit</option>
            	<?php for($i=9; $i<=30; $i++) {?>
            	<option value="<?php echo $i;?>px"<?php if($OptionsVis['link_font_size']==$i.'px') echo ' selected="selected"'; ?>><?php echo $i;?>px</option>
                <?php } ?>
            </select>
        </td>
      </tr>    
      <tr>
        <td class="langLeft">Navigation font color:</td>
        <td class="left_top"><input name="link_color" type="text" size="7" value="<?php echo $OptionsVis["link_color"]; ?>" style="color:<?php echo invert_colour($OptionsVis["link_color"]); ?>;background-color:<?php echo $OptionsVis["link_color"]; ?>" /><a href="javascript:void(0)" onClick="cp.select(form.link_color,'pickcolor');return false;" id="pickcolor"><img src="images/color_picker.jpg" alt="select color" width="20" height="20" border="0" align="absmiddle" /></a> &nbsp; <sub> - you can pick the color from pallette or you can put it manualy</sub></td>
      </tr> 
      <tr>
        <td class="langLeft">Navigation font-weight:</td>
        <td class="left_top">
        	<select name="link_font_weight">
            	<option value="normal"<?php if($OptionsVis['link_font_weight']=='normal') echo ' selected="selected"'; ?>>normal</option>
            	<option value="bold"<?php if($OptionsVis['link_font_weight']=='bold') echo ' selected="selected"'; ?>>bold</option>
                <option value="inherit"<?php if($OptionsVis['link_font_weight']=='inherit') echo ' selected="selected"'; ?>>inherit</option>
            </select>
        </td>
      </tr>   
      <tr>
        <td class="langLeft">Navigation alignment:</td>
        <td class="left_top">
        	<select name="link_align">
            	<option value="left"<?php if($OptionsVis['link_align']=='left') echo ' selected="selected"'; ?>>left</option>
            	<option value="center"<?php if($OptionsVis['link_align']=='center') echo ' selected="selected"'; ?>>center</option>
                <option value="right"<?php if($OptionsVis['link_align']=='right') echo ' selected="selected"'; ?>>right</option>
                <option value="inherit"<?php if($OptionsVis['link_align']=='inherit') echo ' selected="selected"'; ?>>inherit</option>
            </select>
        </td>
      </tr>            
      <tr>
        <td>&nbsp;</td>
        <td><input name="submit13" type="submit" value="Save" class="submitButton" /></td>
      </tr>
      <tr>
        <td colspan="3" height="8"></td>
      </tr>
    </table> 
    </div>
      
      
    <div class="accordion_toggle">'Email to publisher' link style</div>
    <div class="accordion_content">   
    <table border="0" cellspacing="0" cellpadding="8" class="allTables">   
      <tr>
        <td class="langLeft">'Email to publisher' font-size:</td>
        <td class="left_top">
        	<select name="email_font_size">
            	<option value="inherit"<?php if($OptionsVis['email_font_size']=='inherit') echo ' selected="selected"'; ?>>inherit</option>
            	<?php for($i=9; $i<=30; $i++) {?>
            	<option value="<?php echo $i;?>px"<?php if($OptionsVis['email_font_size']==$i.'px') echo ' selected="selected"'; ?>><?php echo $i;?>px</option>
                <?php } ?>
            </select>
        </td>
      </tr>    
      <tr>
        <td class="langLeft">'Email to publisher' font color:</td>
        <td class="left_top"><input name="email_color" type="text" size="7" value="<?php echo $OptionsVis["email_color"]; ?>" style="color:<?php echo invert_colour($OptionsVis["email_color"]); ?>;background-color:<?php echo $OptionsVis["email_color"]; ?>" /><a href="javascript:void(0)" onClick="cp.select(form.email_color,'pickcolor');return false;" id="pickcolor"><img src="images/color_picker.jpg" alt="select color" width="20" height="20" border="0" align="absmiddle" /></a> &nbsp; <sub> - you can pick the color from pallette or you can put it manualy</sub></td>
      </tr> 
      <tr>
        <td class="langLeft">'Email to publisher' font-weight:</td>
        <td class="left_top">
        	<select name="email_font_weight">
            	<option value="normal"<?php if($OptionsVis['email_font_weight']=='normal') echo ' selected="selected"'; ?>>normal</option>
            	<option value="bold"<?php if($OptionsVis['email_font_weight']=='bold') echo ' selected="selected"'; ?>>bold</option>
                <option value="inherit"<?php if($OptionsVis['email_font_weight']=='inherit') echo ' selected="selected"'; ?>>inherit</option>
            </select>
        </td>
      </tr>   
      <tr>
        <td class="langLeft">'Email to publisher' font-style:</td>
        <td class="left_top">
        	<select name="email_font_style">
            	<option value="normal"<?php if($OptionsVis['email_font_style']=='normal') echo ' selected="selected"'; ?>>normal</option>
            	<option value="italic"<?php if($OptionsVis['email_font_style']=='italic') echo ' selected="selected"'; ?>>italic</option>
                <option value="oblique"<?php if($OptionsVis['email_font_style']=='oblique') echo ' selected="selected"'; ?>>oblique</option>
                <option value="inherit"<?php if($OptionsVis['email_font_style']=='inherit') echo ' selected="selected"'; ?>>inherit</option>
            </select>
        </td>
      </tr>            
      <tr>
        <td>&nbsp;</td>
        <td><input name="submit14" type="submit" value="Save" class="submitButton" /></td>
      </tr>
      <tr>
        <td colspan="3" height="8"></td>
      </tr>
    </table> 
    </div>
      
      
    
    <div class="accordion_toggle">Print Page', 'MySpace', 'FaceBook', 'Twitter', 'Send to Friend' and 'More Options' buttons below the articles</div>
    <div class="accordion_content">   
    <table border="0" cellspacing="0" cellpadding="8" class="allTables">   
      <tr>
        <td class="langLeft">Show buttons below the articles:</td>
        <td class="left_top">
        	<select name="show_share_this">
            	<option value="yes"<?php if($OptionsVis['show_share_this']=='yes') echo ' selected="selected"'; ?>>yes</option>
            	<option value="no"<?php if($OptionsVis['show_share_this']=='no') echo ' selected="selected"'; ?>>no</option>
            </select>
        </td>
      </tr>  
      <tr>
        <td class="langLeft">Buttons alignment:</td>
        <td class="left_top">
        	<select name="share_this_align">
            	<option value="left"<?php if($OptionsVis['share_this_align']=='left') echo ' selected="selected"'; ?>>left</option>
                <option value="right"<?php if($OptionsVis['share_this_align']=='right') echo ' selected="selected"'; ?>>right</option>
            </select>
        </td>
      </tr>    
      <tr>
        <td class="langLeft">Buttons size:</td>
        <td class="left_top">
        	<select name="Buttons_size">
            	<option value="a2a_kit_size_32"<?php if($OptionsVis['Buttons_size']=='a2a_kit_size_32') echo ' selected="selected"'; ?>>Big</option>
                <option value=""<?php if($OptionsVis['Buttons_size']=='') echo ' selected="selected"'; ?>>Small</option>
            </select>
        </td>
      </tr>  
      <tr>
        <td>&nbsp;</td>
        <td><input name="submit15" type="submit" value="Save" class="submitButton" /></td>
      </tr>
      <tr>
        <td colspan="3" height="8"></td>
      </tr>
    </table> 
    </div> 
      
    
    <div class="accordion_toggle">Distances</div>
    <div class="accordion_content">   
    <table border="0" cellspacing="0" cellpadding="8" class="allTables">   
      <tr>
        <td class="langLeft">Distance between Product/Service/Title and date:</td>
        <td class="left_top">
        	<select name="dist_title_date">
            	<?php for($i=0; $i<=50; $i++) {?>
            	<option value="<?php echo $i;?>px"<?php if($OptionsVis['dist_title_date']==$i.'px') echo ' selected="selected"'; ?>><?php echo $i;?>px</option>
                <?php } ?>
            </select>
        </td>
      </tr>   
      <tr>
        <td class="langLeft">Distance between date and price:</td>
        <td class="left_top">
        	<select name="dist_date_price">
            	<?php for($i=0; $i<=50; $i++) {?>
            	<option value="<?php echo $i;?>px"<?php if($OptionsVis['dist_date_price']==$i.'px') echo ' selected="selected"'; ?>><?php echo $i;?>px</option>
                <?php } ?>
            </select>
        </td>
      </tr>   
       <tr>
        <td class="langLeft">Distance between price and publisher info:</td>
        <td class="left_top">
        	<select name="dist_price_text">
            	<?php for($i=0; $i<=50; $i++) {?>
            	<option value="<?php echo $i;?>px"<?php if($OptionsVis['dist_price_text']==$i.'px') echo ' selected="selected"'; ?>><?php echo $i;?>px</option>
                <?php } ?>
            </select>
        </td>
      </tr>
      <tr>
        <td class="langLeft">Distance between publisher info and description text:</td>
        <td class="left_top">
        	<select name="dist_date_text">
            	<?php for($i=0; $i<=50; $i++) {?>
            	<option value="<?php echo $i;?>px"<?php if($OptionsVis['dist_date_text']==$i.'px') echo ' selected="selected"'; ?>><?php echo $i;?>px</option>
                <?php } ?>
            </select>
        </td>
      </tr>    
      <tr>
        <td class="langLeft">Distance between Product/Service/Title and description in the listing:</td>
        <td class="left_top">
        	<select name="summ_dist_title_text">
            	<?php for($i=0; $i<=50; $i++) {?>
            	<option value="<?php echo $i;?>px"<?php if($OptionsVis['summ_dist_title_text']==$i.'px') echo ' selected="selected"'; ?>><?php echo $i;?>px</option>
                <?php } ?>
            </select>
        </td>
      </tr>   
      <tr>
        <td class="langLeft">Distance between Classified Ads in the listing:</td>
        <td class="left_top">
        	<select name="dist_btw_entries">
            	<?php for($i=0; $i<=100; $i++) {?>
            	<option value="<?php echo $i;?>px"<?php if($OptionsVis['dist_btw_entries']==$i.'px') echo ' selected="selected"'; ?>><?php echo $i;?>px</option>
                <?php } ?>
            </select>
        </td>
      </tr>  
      <tr>
        <td class="langLeft">Distance between 'Back' link and Product/Service/Title:</td>
        <td class="left_top">
        	<select name="dist_link_title">
            	<?php for($i=0; $i<=50; $i++) {?>
            	<option value="<?php echo $i;?>px"<?php if($OptionsVis['dist_link_title']==$i.'px') echo ' selected="selected"'; ?>><?php echo $i;?>px</option>
                <?php } ?>
            </select>
        </td>
      </tr>  
      <tr>
        <td class="langLeft">Distance between Images:</td>
        <td class="left_top">
        	<select name="dist_image">
            	<?php for($i=0; $i<=50; $i++) {?>
            	<option value="<?php echo $i;?>px"<?php if($OptionsVis['dist_image']==$i.'px') echo ' selected="selected"'; ?>><?php echo $i;?>px</option>
                <?php } ?>
            </select>
        </td>
      </tr>  
      <tr>
        <td>&nbsp;</td>
        <td><input name="submit16" type="submit" value="Save" class="submitButton" /></td>
      </tr>
      <tr>
        <td colspan="3" height="8"></td>
      </tr>
    </table> 
    </div>
	
    
    </div>
	</form> 
    


<?php
} elseif ($_REQUEST["act"]=='language_options') {
	$sql = "SELECT * FROM ".$TABLE["Options"];
	$sql_result = sql_result($sql);
	$Options = mysqli_fetch_assoc($sql_result);
	$OptionsLang = unserialize($Options['language']);
?>

	<script type="text/javascript">
		Event.observe(window, 'load', loadAccordions, false);
		function loadAccordions() {
			var bottomAccordion = new accordion('accordion_container');	
			// Open first one
			//bottomAccordion.activate($$('#accordion_container .accordion_toggle')[0]);
		}	
	</script>
	
     <div class="pageDescr">Click on any of the line to see the options.</div>
    
    <form action="admin.php" method="post" name="frm">
	<input type="hidden" name="act" value="updateOptionsLanguage" />
    
    <div class="opt_headlist">Translate front-end in your own language and wordings. </div>

    <div id="accordion_container"> 
    <div class="accordion_toggle">Classified Ads navigation and paging</div>
    <div class="accordion_content">
    <table border="0" cellspacing="0" cellpadding="8" class="allTables">       
      <tr>
        <td class="langLeft">'Back' link:</td>
        <td class="left_top"><input class="input_lan" name="Back_to_home" type="text" value="<?php echo ReadDB($OptionsLang["Back_to_home"]); ?>" /> &nbsp; <sub> - leave blank if you do not want 'Back' link </sub></td>
      </tr>  
      <tr>
        <td class="langLeft">'Submit Classified Ad' link:</td>
        <td class="left_top"><input class="input_lan" name="Submit_Classified_Ad" type="text" value="<?php echo ReadDB($OptionsLang["Submit_Classified_Ad"]); ?>" /> &nbsp; <sub> - leave blank if you do not want 'Submit Ad' link </sub></td>
      </tr>  
      <tr>
        <td class="langLeft">Category:</td>
        <td class="left_top"><input class="input_lan" name="Category" type="text" value="<?php echo ReadDB($OptionsLang["Category"]); ?>" /></td>
      </tr>	
      <tr>
        <td class="langLeft">'All Ads' category:</td>
        <td class="left_top"><input class="input_lan" name="All_Ads" type="text" value="<?php echo ReadDB($OptionsLang["All_Ads"]); ?>" /></td>
      </tr>
      <tr>
        <td class="langLeft">'Read more' link:</td>
        <td class="left_top"><input class="input_lan" name="Read_more" type="text" value="<?php echo ReadDB($OptionsLang["Read_more"]); ?>" /></td>
      </tr>  
      <tr>
        <td class="langLeft">Pages:</td>
        <td class="left_top"><input class="input_lan" name="Paging" type="text" value="<?php echo ReadDB($OptionsLang["Paging"]); ?>" /></td>
      </tr>    
      <tr>
        <td class="langLeft">No Classified Ads published:</td>
        <td class="left_top"><input class="input_lan" name="No_Classified_Ads_published" type="text" value="<?php echo ReadDB($OptionsLang["No_Classified_Ads_published"]); ?>" /></td>
      </tr>      
      <tr>
        <td class="langLeft">Button 'Search':</td>
        <td class="left_top"><input class="input_lan" name="Search_button" type="text" value="<?php echo ReadDB($OptionsLang["Search_button"]); ?>" /></td>
      </tr>             
      <tr>
        <td>&nbsp;</td>
        <td><input name="submit1" type="submit" value="Save" class="submitButton" /></td>
      </tr>
      <tr>
        <td colspan="3" height="8"></td>
      </tr>
    </table> 
    </div>
      
    
    <div class="accordion_toggle">Classified Ads words - Listed, Price, Name, Location, Email, Phone, Website</div>
    <div class="accordion_content">  
	<table border="0" cellspacing="0" cellpadding="8" class="allTables">         
      <tr>
        <td class="langLeft">Listed:</td>
        <td class="left_top"><input class="input_lan" name="Listed" type="text" value="<?php echo ReadDB($OptionsLang["Listed"]); ?>" /> </td>
      </tr>  
      <tr>
        <td class="langLeft">Price:</td>
        <td class="left_top"><input class="input_lan" name="Price" type="text" value="<?php echo ReadDB($OptionsLang["Price"]); ?>" /></td>
      </tr>  
      <tr>
        <td class="langLeft">Name:</td>
        <td class="left_top"><input class="input_lan" name="Name" type="text" value="<?php echo ReadDB($OptionsLang["Name"]); ?>" /></td>
      </tr>   
      <tr>
        <td class="langLeft">Location:</td>
        <td class="left_top"><input class="input_lan" name="Location" type="text" value="<?php echo ReadDB($OptionsLang["Location"]); ?>" /></td>
      </tr>   
      <tr>
        <td class="langLeft">Send Email to publisher:</td>
        <td class="left_top"><input class="input_lan" name="Email_to_publisher" type="text" value="<?php echo ReadDB($OptionsLang["Email_to_publisher"]); ?>" /></td>
      </tr>      
      <tr>
        <td class="langLeft">Phone:</td>
        <td class="left_top"><input class="input_lan" name="Phone" type="text" value="<?php echo ReadDB($OptionsLang["Phone"]); ?>" /></td>
      </tr>        
      <tr>
        <td class="langLeft">Website:</td>
        <td class="left_top"><input class="input_lan" name="Website" type="text" value="<?php echo ReadDB($OptionsLang["Website"]); ?>" /></td>
      </tr>             
      <tr>
        <td>&nbsp;</td>
        <td><input name="submit2" type="submit" value="Save" class="submitButton" /></td>
      </tr>
      <tr>
        <td colspan="3" height="8"></td>
      </tr>
    </table> 
    </div>
    
            
    
    <div class="accordion_toggle">Classified Ads email request page</div>
    <div class="accordion_content">  
	<table border="0" cellspacing="0" cellpadding="8" class="allTables">    
      <tr>
        <td class="langLeft">Your Name:</td>
        <td class="left_top">
          <input class="input_lan" name="Your_Name" type="text" value="<?php echo ReadDB($OptionsLang["Your_Name"]); ?>" /></td>
      </tr>
      <tr>
        <td class="langLeft">Your Email:</td>
        <td class="left_top">
          <input class="input_lan" name="Your_Email" type="text" value="<?php echo ReadDB($OptionsLang["Your_Email"]); ?>" /></td>
      </tr>
      <tr>
        <td class="langLeft">Request:</td>
        <td class="left_top">
          <input class="input_lan" name="Request" type="text" value="<?php echo ReadDB($OptionsLang["Request"]); ?>" /></td>
      </tr>
      <tr>
        <td class="langLeft">Anti-spam code:</td>
        <td class="left_top"><input class="input_lan" name="Anti_spam_code" type="text" value="<?php echo ReadDB($OptionsLang["Anti_spam_code"]); ?>" /></td>
      </tr>
      <tr>
        <td class="langLeft">required fields:</td>
        <td class="left_top"><input class="input_lan" name="the_required_fields" type="text" value="<?php echo ReadDB($OptionsLang["the_required_fields"]); ?>" /></td>
      </tr>
      <tr>
        <td class="langLeft">'Send Request' button:</td>
        <td class="left_top"><input class="input_lan" name="Send_Request" type="text" value="<?php echo ReadDB($OptionsLang["Send_Request"]); ?>" /></td>
      </tr>
      <tr>
        <td class="langLeft">Email request subject:</td>
        <td class="left_top"><input class="input_lan" name="Email_request_subject" type="text" value="<?php echo ReadDB($OptionsLang["Email_request_subject"]); ?>" /></td>
      </tr>
      <tr>
        <td class="langLeft">'Incorrect anti spam code' message:</td>
        <td class="left_top"><input class="input_lan" name="Incorrect_anti_spam_code" type="text" value="<?php echo ReadDB($OptionsLang["Incorrect_anti_spam_code"]); ?>" /> </td>
      </tr>
      <tr>
        <td class="langLeft">'Request successfully sent' message:</td>
        <td class="left_top"><input class="input_lan" name="Request_successfully_sent" type="text" value="<?php echo ReadDB($OptionsLang["Request_successfully_sent"]); ?>" /> </td>
      </tr>
      <tr>
        <td class="langLeft">'Fill all the required fields' popup message:</td>
        <td class="left_top"><input class="input_lan" name="Fill_all_the_required_fields" type="text" value="<?php echo ReadDB($OptionsLang["Fill_all_the_required_fields"]); ?>" /> </td>
      </tr>
      <tr>
        <td class="langLeft">'Incorrect email address' message:</td>
        <td class="left_top"><input class="input_lan" name="Incorrect_mail_address" type="text" value="<?php echo ReadDB($OptionsLang["Incorrect_mail_address"]); ?>" /> </td>
      </tr>
      <tr>
        <td class="langLeft">Please, enter verification code:</td>
        <td class="left_top"><input class="input_lan" name="field_code" type="text" value="<?php echo ReadDB($OptionsLang["field_code"]); ?>" /></td>
      </tr>
      <tr>
        <td>&nbsp;</td>
        <td><input name="submit7" type="submit" value="Save" class="submitButton" /></td>
      </tr>
      <tr>
        <td colspan="3" height="8"></td>
      </tr>
    </table> 
    </div>
    
    
    <div class="accordion_toggle">Email subjects and messages</div>
    <div class="accordion_content">  
	<table border="0" cellspacing="0" cellpadding="8" class="allTables">     
      <tr>
        <td class="langLeft">Subject of email sent to user when Classified Ads expired:</td>
        <td class="left_top"><input name="Classified_Ads_expired" type="text" size="50" value="<?php echo ReadDB($OptionsLang["Classified_Ads_expired"]); ?>" /></td>
      </tr>      
      <tr>
        <td>&nbsp;</td>
        <td><input name="submit6" type="submit" value="Save" class="submitButton" /></td>
      </tr>
      <tr>
        <td colspan="3" height="8"></td>
      </tr>
    </table> 
    </div>
    
    
    <div class="accordion_toggle">Default meta tags for classifieds page</div>
    <div class="accordion_content">  
	<table border="0" cellspacing="0" cellpadding="8" class="allTables">        
      <tr>
        <td class="langLeft">Meta title:</td>
        <td class="left_top"><input class="input_lan" name="metatitle" type="text" value="<?php echo ReadHTML($OptionsLang["metatitle"]); ?>" /></td>
      </tr>
      <tr>
        <td class="langLeft">Meta description:</td>
        <td class="left_top"><input class="input_lan" name="metadescription" type="text" value="<?php echo ReadHTML($OptionsLang["metadescription"]); ?>" /></td>
      </tr>
      <tr>
        <td>&nbsp;</td>
        <td><input name="submit8" type="submit" value="Save" class="submitButton" /></td>
      </tr>
      <tr>
        <td colspan="3" height="8"></td>
      </tr>
    </table> 
    </div>      
      
      
    </div>
	</form>

<?php
} elseif ($_REQUEST["act"]=='html') {
?>
	<div class="pageDescr">There are two easy ways to put the Classified Ads on your website.</div>

	<table border="0" cellspacing="0" cellpadding="8" class="allTables">
      <tr>
        <td class="copycode">1) <strong>Using iframe code</strong> - just copy the code below and put it on your web page where you want the Classified Ads to appear.</td>
      </tr>
      <tr>
      	<td class="putonwebpage">        	
        	<div class="divCode">&lt;iframe src=&quot;<?php echo $CONFIG["full_url"]; ?>preview.php&quot; width=&quot;100%&quot; height=&quot;700px&quot; frameborder=&quot;0&quot; scrolling=&quot;auto&quot;&gt;&lt;/iframe&gt;   </div>     
        </td>
      </tr>
    </table>
    
    <table border="0" cellspacing="0" cellpadding="8" class="allTables">
    
      <tr>
        <td class="copycode">2) <strong>Using PHP include()</strong> - you can use a PHP include() in any of your PHP pages. Edit your .php page and put the code below where you want the Classified Ads to be.</td>
      </tr>
      
      <tr>
        <td class="putonwebpage">        	
        	<div class="divCode">&lt;?php include(&quot;<?php echo $CONFIG["server_path"]; ?>classifiedads.php&quot;); ?&gt; </div>     
        </td>
      </tr>
            
    </table>
    
    
    <table border="0" cellspacing="0" cellpadding="8" class="allTables">
      <tr>
      	<td>
        	Optionally in the head section of the php page you could put(or replace your meta tags) this line of code, so meta title and meta description will work for better searching engine optimization.
        </td>
      </tr>
      
      <tr>
        <td class="putonwebpage">        	
        	<div class="divCode">&lt;?php include(&quot;<?php echo $CONFIG["server_path"]; ?>meta.php&quot;); ?&gt; </div>     
        </td>
      </tr>
            
    </table>
    
        

<?php
} elseif ($_REQUEST["act"]=='rss') {
?>
    
    <div class="pageDescr">The RSS feed allows other people to keep track of your Classified Ads listing using rss readers and to use your Classified Ads on their websites. <br />
Every time when Classified Ads is published will appear on your RSS feed and every one using it will be informed about it.</div>
    
    <table border="0" cellspacing="0" cellpadding="8" class="allTables">
    
      <tr>
        <td class="copycode">You can view the RSS feed <a href="rss.php" target="_blank">here</a> or use the code below to place it on your website as RSS link.</td>
      </tr>
      
      <tr>
        <td class="putonwebpage">        	
        	<div class="divCode">&lt;a href=&quot;<?php echo $CONFIG["full_url"]; ?>rss.php&quot; target=&quot;_blank&quot;&gt;RSS feed&lt;/a&gt;</div>     
        </td>
      </tr>
            
    </table>
    
<?php
}
?>
</div>


<?php 
} else { ///// Login Form /////
?>
<div class="admin_wrapper login_wrapper">
	<div class="login_head"><?php echo $lang['ADMIN_LOGIN']; ?></div>
	
	<div class="login_sub"><?php echo $lang['Login_context']; ?></div>
    <form action="admin.php" method="post">
    <input type="hidden" name="act" value="login">
    <table border="0" cellspacing="0" cellpadding="0" class="loginTable">
      <tr>
        <td class="userpass"><?php echo $lang['Username']; ?> </td>
        <td class="userpassfield"><input name="user" type="text" class="loginfield" style="float:left;" /> <?php if(isset($logMessage) and $logMessage!='') {?><div class="logMessage"><?php echo $logMessage; ?></div><?php } ?></td>
      </tr>
      <tr>
        <td class="userpass"><?php echo $lang['Password']; ?> </td>
        <td class="userpassfield"><input name="pass" type="password" class="loginfield" /></td>
      </tr>
      <tr>
        <td class="userpass">&nbsp;</td>
        <td class="userpassfield"><input type="submit" name="button" value="<?php echo $lang['Login']; ?>" class="loginButon" /></td>
      </tr>
    </table>
    </form>
</div>
<?php 
}
?>

<div class="clearfooter"></div>
<div class="divProfiAnts"> <a class="footerlink" href="http://simplephpscripts.com" target="_blank">Product of SimplePHPscripts.com</a></div>

</body>
</html>