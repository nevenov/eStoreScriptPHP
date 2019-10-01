<?php
//error_reporting(E_ALL & ~E_WARNING);
$installed = 'yes';
include("configs.php");

if (isset($_GET["install"]) and $_GET["install"]==1) {
	$message = '';
	$connCl = mysqli_connect($_REQUEST["hostname"], $_REQUEST["mysql_user"], $_REQUEST["mysql_password"]);
	if (mysqli_connect_errno()) {
		$message = "MySQL database details are incorrect. Please, check the database details(MySQL server, username and password) and/or contact your hosting company to verify them. If you have troubles just send us login details for your hosting account control panel and we will do the installation of the script for you for free.
		<br /> Error message: " . mysqli_connect_error();
	} else {
		if (!mysqli_select_db($connCl, $_REQUEST["mysql_database"])) {
			$message = "Unable to select database. Database name is incorrect or is not created. Please check database details - MySQL server, Database name, Username and Password and try again. If you have troubles just send us login details for your hosting account control panel and we will do the installation of the script for you for free.";
		} else {
					
			$sql = "DROP TABLE IF EXISTS `".$TABLE["Ads"]."`;";
			$sql_result = sql_result($sql);
			
			$sql = "CREATE TABLE `".$TABLE["Ads"]."` (
					  `id` int(11) NOT NULL auto_increment,
					  `user_id` varchar(20) default NULL,
					  `publish_date` datetime default NULL,
					  `expire_date` datetime default NULL,
					  `status` varchar(50) default NULL,
					  `cat_id` varchar(10) default NULL,
					  `topads` varchar(50) default NULL,
					  `highlight` varchar(50) default NULL,
					  `title` varchar(250) default NULL,
					  `description` text,
					  `price` varchar(50),
					  `sale_price` varchar(50),
					  `shipping` varchar(50),
					  `name` varchar(250),
					  `address` varchar(250),
					  `location` varchar(250),
					  `email` varchar(250),
					  `phone` varchar(250),
					  `website` varchar(250),
					  `paypal_email` varchar(250),
					  `image1` varchar(250) default NULL,
					  `image2` varchar(250) default NULL,
					  `image3` varchar(250) default NULL,
					  `image4` varchar(250) default NULL,
					  `image5` varchar(250) default NULL,
					  `ads_comments` varchar(50) default NULL,
					  `reviews` int(11) default NULL, 
					  PRIMARY KEY  (`id`))
					  CHARACTER SET utf8 COLLATE utf8_unicode_ci";
  			$sql_result = sql_result($sql);
			
			
			$sql = "DROP TABLE IF EXISTS `".$TABLE["Categories"]."`;";
			$sql_result = sql_result($sql);
			
			$sql = "CREATE TABLE `".$TABLE["Categories"]."` (
					  `id` int(11) NOT NULL auto_increment,
					  `cat_name` varchar(250) default NULL,
					  PRIMARY KEY  (`id`))
					  CHARACTER SET utf8 COLLATE utf8_unicode_ci";
  			$sql_result = sql_result($sql);
			
			$sql = 'INSERT INTO `'.$TABLE["Categories"].'` 
					SET `cat_name`="Buy/Sell"';
			$sql_result = sql_result($sql);	
			
			
			$sql = "DROP TABLE IF EXISTS `".$TABLE["Users"]."`;";
			$sql_result = sql_result($sql);
			
			$sql = "CREATE TABLE `".$TABLE["Users"]."` (
					  `id` int(11) NOT NULL auto_increment,
					  `reg_date` datetime default NULL,
					  `user_ip` varchar(250) default NULL,
					  `status` varchar(20) default NULL,
					  `user_email` varchar(250) default NULL,
					  `user_password` varchar(250) default NULL,
					  `user_name` varchar(250) default NULL,
					  `user_address` varchar(250) default NULL,
					  `user_location` varchar(250) default NULL,
					  `user_phone` varchar(250) default NULL,
					  `user_url` varchar(250) default NULL,
					  PRIMARY KEY  (`id`))
					  CHARACTER SET utf8 COLLATE utf8_unicode_ci";
  			$sql_result = sql_result($sql);
			
			
			$sql = "DROP TABLE IF EXISTS `".$TABLE["Orders"]."`;";
			$sql_result = sql_result($sql);
			
			$sql = "CREATE TABLE `".$TABLE["Orders"]."` (
					  `id` int(11) NOT NULL auto_increment,
					  `order_id` varchar(250) default NULL,
					  `amount` varchar(250) default NULL,
					  `products` text default NULL,
					  `orderDate` datetime default NULL,
					  `payment_processing` varchar(250) default NULL,
					  `name` varchar(250) default NULL,
					  `last_name` varchar(250) default NULL,
					  `phone` varchar(250) default NULL,
					  `email` varchar(250) default NULL,
					  `address` varchar(250) default NULL,
					  `address2` varchar(250) default NULL,
					  `city` varchar(250) default NULL,
					  `state` varchar(250) default NULL,
					  `zip` varchar(250) default NULL,
					  `country` varchar(250) default NULL,
					  `notes` varchar(250) default NULL,
					  `status` varchar(250) default NULL,
					  PRIMARY KEY  (`id`))
					  CHARACTER SET utf8 COLLATE utf8_unicode_ci";
  			$sql_result = sql_result($sql);
			
			
					
			$sql = "DROP TABLE IF EXISTS `".$TABLE["Options"]."`;";
			$sql_result = sql_result($sql);
			
			$sql = "CREATE TABLE `".$TABLE["Options"]."` (
					  `options_id` int(11) NOT NULL auto_increment,
					  `per_page` varchar(10) default NULL,
					  `char_num` varchar(10) default NULL,	  
					  `email` varchar(250) default NULL,  
					  `paypal_email` varchar(250) default NULL,
					  `ads_approve` varchar(10) default NULL,
					  `expire_days` varchar(10) default NULL,
					  `del_after_expire` varchar(10) default NULL,
					  `email_after_expire` varchar(10) default NULL,
					  `currency` varchar(10) default NULL,		
					  `default_cat` varchar(10) default NULL,		
					  `feat_order` varchar(20) default NULL,	
					  `showallads_cat` varchar(10) default NULL,
					  `imgwidth` varchar(10) default NULL,
					  `char_limit` varchar(10) default NULL,
					  `show_comments` varchar(10) default NULL,					  
					  `approval` varchar(10) default NULL,
					  `comments_order` varchar(10) default NULL,
					  `captcha` varchar(10) default NULL,
					  `captcha_theme` varchar(20) default NULL,
					  `submit_open_mode` varchar(20) default NULL,					  
					  `smtp_auth` varchar(20),
					  `smtp_server` varchar(250),
					  `smtp_port` varchar(20),
					  `smtp_email` varchar(250),
					  `smtp_pass` varchar(250),
					  `smtp_secure` varchar(250),
					  `ban_words` text,
					  `visual` text,
					  `visual_top` text,
					  `visual_comm` text,
					  `language` text,
					  PRIMARY KEY  (`options_id`))
					  CHARACTER SET utf8 COLLATE utf8_unicode_ci";
  			$sql_result = sql_result($sql);
			
			$sql = 'INSERT INTO `'.$TABLE["Options"].'` 
					SET `per_page`="10",
						`char_num`="160",				
						`email`="admin@email.com", 			
						`paypal_email`="admin@email.com", 
						`ads_approve`="true",
						`expire_days`="60", 
						`del_after_expire`="hidden", 
						`email_after_expire`="true", 						 
						`currency`="1", 
						`default_cat`="0", 
						`feat_order`="RAND()",
						`showallads_cat`="yes", 
						`imgwidth`="300px", 
						`char_limit`="550",
						`show_comments`="true",
						`approval`="true",
						`comments_order`="AtBottom", 
						`captcha`="recap", 
						`captcha_theme`="white",  
						`smtp_auth`="no",  
						`smtp_server`="smtp.server.com",  
						`smtp_port`=587,  
						`smtp_email`="test@server.com",  
						`smtp_pass`="password",  
						`smtp_secure`="tls",
						
						`visual`=\'a:89:{s:15:"gen_font_family";s:5:"Arial";s:13:"gen_font_size";s:4:"12px";s:14:"gen_font_color";s:7:"#000000";s:13:"gen_bgr_color";s:7:"#FFFFFF";s:15:"gen_line_height";s:7:"inherit";s:9:"gen_width";s:3:"650";s:12:"column_color";s:7:"#000000";s:11:"column_font";s:5:"Arial";s:11:"column_size";s:4:"12px";s:18:"column_font_weight";s:4:"bold";s:17:"column_font_style";s:6:"italic";s:11:"title_color";s:8:"#00476c ";s:10:"title_font";s:7:"Georgia";s:10:"title_size";s:4:"18px";s:17:"title_font_weight";s:6:"normal";s:16:"title_font_style";s:6:"italic";s:16:"title_text_align";s:7:"justify";s:16:"summ_title_color";s:8:"#00476c ";s:15:"summ_title_font";s:5:"Arial";s:15:"summ_title_size";s:4:"12px";s:22:"summ_title_font_weight";s:4:"bold";s:21:"summ_title_font_style";s:6:"normal";s:21:"summ_title_text_align";s:7:"justify";s:11:"price_color";s:7:"#666666";s:10:"price_font";s:7:"inherit";s:10:"price_size";s:4:"12px";s:17:"price_font_weight";s:6:"normal";s:16:"price_font_style";s:6:"normal";s:16:"price_text_align";s:4:"left";s:10:"date_color";s:7:"#666666";s:9:"date_font";s:7:"inherit";s:9:"date_size";s:4:"12px";s:16:"date_font_weight";s:4:"bold";s:15:"date_font_style";s:6:"normal";s:15:"date_text_align";s:4:"left";s:11:"date_format";s:5:"m/d/y";s:12:"showing_time";s:0:"";s:16:"summ_price_color";s:7:"#000000";s:15:"summ_price_font";s:7:"inherit";s:15:"summ_price_size";s:4:"12px";s:22:"summ_price_font_weight";s:4:"bold";s:21:"summ_price_font_style";s:6:"normal";s:21:"summ_price_text_align";s:6:"center";s:15:"summ_date_color";s:7:"#000000";s:14:"summ_date_font";s:7:"inherit";s:14:"summ_date_size";s:4:"12px";s:21:"summ_date_font_weight";s:6:"normal";s:20:"summ_date_font_style";s:6:"normal";s:20:"summ_date_text_align";s:5:"right";s:16:"summ_date_format";s:5:"m/d/y";s:17:"summ_showing_time";s:0:"";s:10:"cont_color";s:7:"#333333";s:9:"cont_font";s:7:"inherit";s:9:"cont_size";s:4:"12px";s:15:"cont_font_style";s:6:"normal";s:15:"cont_text_align";s:7:"justify";s:16:"cont_line_height";s:7:"inherit";s:10:"summ_color";s:7:"#333333";s:9:"summ_font";s:7:"inherit";s:9:"summ_size";s:4:"12px";s:15:"summ_font_style";s:6:"normal";s:15:"summ_text_align";s:7:"justify";s:16:"summ_line_height";s:7:"inherit";s:15:"summ_show_image";s:3:"yes";s:14:"summ_img_width";s:2:"85";s:12:"hl_bgr_color";s:7:"#f4f2e5";s:13:"pag_font_size";s:4:"12px";s:9:"pag_color";s:7:"#00476c";s:15:"pag_font_weight";s:4:"bold";s:9:"pag_align";s:6:"center";s:14:"link_font_size";s:7:"inherit";s:10:"link_color";s:7:"#00476c";s:16:"link_font_weight";s:4:"bold";s:10:"link_align";s:4:"left";s:15:"email_font_size";s:4:"12px";s:11:"email_color";s:7:"#00476c";s:17:"email_font_weight";s:6:"normal";s:16:"email_font_style";s:6:"normal";s:15:"show_share_this";s:3:"yes";s:16:"share_this_align";s:5:"right";s:12:"Buttons_size";s:0:"";s:15:"dist_title_date";s:3:"3px";s:15:"dist_date_price";s:3:"3px";s:15:"dist_price_text";s:3:"6px";s:20:"summ_dist_title_text";s:3:"0px";s:14:"dist_date_text";s:3:"6px";s:16:"dist_btw_entries";s:3:"4px";s:15:"dist_link_title";s:4:"12px";s:10:"dist_image";s:4:"10px";}\',
						
						`visual_comm`=\'a:21:{s:15:"comm_bord_sides";s:10:"top_bottom";s:15:"comm_bord_style";s:5:"solid";s:15:"comm_bord_width";s:3:"1px";s:15:"comm_bord_color";s:7:"#dddddd";s:12:"comm_padding";s:4:"10px";s:14:"comm_bgr_color";s:7:"#F8F8F8";s:15:"name_font_color";s:7:"#0066cc";s:14:"name_font_size";s:4:"14px";s:15:"name_font_style";s:6:"normal";s:16:"name_font_weight";s:4:"bold";s:14:"comm_date_font";s:12:"Trebuchet MS";s:15:"comm_date_color";s:7:"#0066cc";s:14:"comm_date_size";s:4:"11px";s:20:"comm_date_font_style";s:6:"normal";s:16:"comm_date_format";s:7:"F jS, Y";s:17:"comm_showing_time";s:5:"g:i a";s:15:"comm_font_color";s:7:"#000000";s:14:"comm_font_size";s:7:"inherit";s:15:"comm_font_style";s:6:"normal";s:16:"comm_font_weight";s:6:"normal";s:13:"dist_btw_comm";s:4:"14px";}\',
												 
						`language`=\'a:30:{s:12:"Back_to_home";s:4:"Back";s:20:"Submit_Classified_Ad";s:20:"Login and Submit Ads";s:8:"Category";s:8:"Category";s:7:"All_Ads";s:7:"ALL Ads";s:9:"Read_more";s:7:"more...";s:6:"Paging";s:5:"Pages";s:27:"No_Classified_Ads_published";s:29:"No Classified Ads published! ";s:13:"Search_button";s:6:"Search";s:6:"Listed";s:6:"Listed";s:5:"Price";s:5:"Price";s:4:"Name";s:4:"Name";s:8:"Location";s:8:"Location";s:18:"Email_to_publisher";s:26:"Email enquiry to publisher";s:5:"Phone";s:5:"Phone";s:7:"Website";s:7:"Website";s:9:"Your_Name";s:9:"Your Name";s:10:"Your_Email";s:10:"Your Email";s:7:"Request";s:7:"Request";s:14:"Anti_spam_code";s:14:"Anti-spam code";s:19:"the_required_fields";s:15:"required fields";s:12:"Send_Request";s:12:"Send Request";s:21:"Email_request_subject";s:31:"You have Classified Ads request";s:25:"Request_successfully_sent";s:27:"Request successfully sent! ";s:24:"Incorrect_anti_spam_code";s:26:"Incorrect anti spam code! ";s:28:"Fill_all_the_required_fields";s:30:"Fill all the required fields! ";s:22:"Incorrect_mail_address";s:25:"Incorrect email address! ";s:10:"field_code";s:33:"Please, enter verification code! ";s:22:"Classified_Ads_expired";s:36:"Your Classified Ad has been expired!";s:9:"metatitle";s:23:"Classified Ads Listings";s:15:"metadescription";s:47:"List of Classified Ads default meta description";}\'';
			
			$sql_result = sql_result($sql);
						
			
			$ConfigFile = "allinfo.php";
			$CONFIG='$CONFIG';
			
			$handle = @fopen($ConfigFile, "r");
			
			if ($handle) {
				$buffer = fgets($handle, 4096);
	  			$buffer .=fgets($handle, 4096);	
				$buffer .=fgets($handle, 4096);	
				
				$buffer .=$CONFIG."[\"hostname\"]='".$_REQUEST["hostname"]."';\n";
				
				$buffer .=$CONFIG."[\"mysql_user\"]='".$_REQUEST["mysql_user"]."';\n";
				
				$buffer .=$CONFIG."[\"mysql_password\"]='".$_REQUEST["mysql_password"]."';\n";
				
				$buffer .=$CONFIG."[\"mysql_database\"]='".addslashes($_REQUEST["mysql_database"])."';\n";
				
				$buffer .=$CONFIG."[\"server_path\"]='".$_REQUEST["server_path"]."';\n";
				
				$buffer .=$CONFIG."[\"full_url\"]='".addslashes($_REQUEST["full_url"])."';\n";
								
				$buffer .=$CONFIG."[\"folder_name\"]='".addslashes($_REQUEST["folder_name"])."';\n";
				
				$buffer .=$CONFIG."[\"admin_user\"]='".$_REQUEST["admin_user"]."';\n";
				
				$buffer .=$CONFIG."[\"admin_pass\"]='".$_REQUEST["admin_pass"]."';\n";
				
				while (!feof($handle)) {
					$buffer .= fgets($handle, 4096);
				}
				
				fclose($handle);
				
				$handle = @fopen($ConfigFile, "w");
				
				if (!$handle) {
					echo "Configuration file $ConfigFile is missing or the permissions does not allow to be changed. Please upload the file and/or set the right permissions (CHMOD 777).";
					exit();
				}
				
				if (!fwrite($handle,$buffer)) {
				  	echo "Configuration file $ConfigFile is missing or the permissions does not allow to be changed. Please upload the file and/or set the right permissions (CHMOD 777).";
					exit();
				}
				
				fclose($handle);
				
			} else {
				echo "Error opening file.";
				exit();
			}
			
			$message = 'Script successfully installed';	
?>
		<script type="text/javascript">
			window.document.location.href='installation.php?install=2'
		</script>           		
<?php		
		}
	}
}
?><!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>Script installation</title>
<link href="styles/installation.css" rel="stylesheet" type="text/css" />
</head>

<body>

<div class="install_wrap">

<?php if (isset($_GET["install"]) && $_GET["install"]==2) { ?>
	<table border="0" class="form_table" align="center" cellpadding="4">
	  <tr>
      	<td>
			Script successfully installed. <a href='admin.php'>Login here</a>.
        </td>
      </tr>
    </table>
<?php } else {?>

	<form action="installation.php" method="get" name="installform">
    <input name="install" type="hidden" value="1" />
	<table border="0" class="form_table" align="center" cellpadding="4">
      
      
      <tr>
      	<td colspan="3">
        	<?php 
			if (isset($message) and $message!='') { 
				echo "<span class='alerts'>".$message."</span>";
			} else {
				echo 'These are the details that script will use to install and run: ';
			}
			?>
	  	</td>
      </tr>
      
      <tr>
        <td align="left" colspan="3" class="head_row">Minimum version required (PHP <?php echo $php_version_min; ?>, MySQL <?php echo $mysql_version_min; ?>): </td>
      </tr>
      
      	<?php 
		
		$error_msg = "";
		
		//////////////// CHECKING FOR PHP VERSION REQUIRED //////////////////
		
		$curr_php_version = phpversion();
		$check_php_version=true;
		
		
		if (version_compare($curr_php_version, $php_version_min, "<")) {
			//echo 'I am using PHP 5.4, my version: ' . phpversion() . "\n. Minimum is ".$php_version_min;
			$check_php_version=false;
		}
		
		if($check_php_version==false) {
			$not = "<span style='color:red;'>not</span>";
			$error_msg .= "PHP requirement checks failed and the script may not work properly. You have version ".$curr_php_version." but the required version is ".$php_version_min.". Please contact your hosting company or system administrator for assistance. <br />";
		} else {
			$not = "";
		}
		?>
        
      <tr>
        <td width="30%" align="left">PHP: </td>
        <td><?php echo "Server version of PHP '".$curr_php_version."' is ".$not." ok!"; ?> </td>
      </tr>
      
      
      	<?php 	
	  	//////////////// CHECKING FOR MYSQL VERSION REQUIRED //////////////////	
		$curr_mysql_version = '-.-.--';
		$not = "";		
		
		$check_mysql_version=true;		
		
		ob_start(); 
		phpinfo(INFO_MODULES); 
		$info = ob_get_contents(); 
		ob_end_clean(); 
		$info = stristr($info, 'Client API version'); 
		preg_match('/[1-9].[0-9].[1-9][0-9]/', $info, $match); 
		$gd = $match[0]; 
		//echo '</br>MySQL:  '.$gd.' <br />';
		$curr_mysql_version = $gd;
		
		
		if (version_compare($curr_mysql_version, $mysql_version_min, "<")) {
			$check_mysql_version=false;
			$not = "<span style='color:red;'>not</span>";
		} else if(trim($curr_mysql_version)=="-.-.--") {
			$error_msg .= "Information about MySQL version is missing or is incomplete. Please ask your hosting company or system administrator for the version. The minimum required version of MySQL is ".$mysql_version_min.". <br />";
			$not = "<span style='color:red;'>not</span>";
		}
		
		if($check_mysql_version==false) {
			$not = "<span style='color:red;'>not</span>";
			$error_msg .= "MySQL requirement checks failed and the script may not work properly. You have version ".$curr_mysql_version." but the required version is ".$mysql_version_min.". Please contact your hosting company or system administrator for assistance. <br />";
		} 
		?>
        
      <tr>
        <td align="left">MySQL: </td>
        <td><?php echo "Server version of MySQL '".$curr_mysql_version."' is ".$not." ok!"; ?></td>
      </tr> 
      
      <?php if(isset($error_msg) and $error_msg!='') {?>
      <tr>
        <td colspan="2" style="color:#FF0000;"><?php echo $error_msg; ?></td>
      </tr>       
      <?php } ?>
      
      <tr>
        <td>&nbsp;</td>
        <td>&nbsp;</td>
      </tr>
      
      <tr>
        <td align="left" colspan="3" class="head_row">MySQL login details: <span style="font-weight:normal; font-size:11px; font-style:italic;">(In case you don't have database yet, you should enter your hosting control panel and create it)</span></td>
      </tr>
      
      <tr>
        <td align="left">MySQL Server:</td>
        <td align="left"><input type="text" name="hostname" value="<?php if(isset($_REQUEST['hostname'])) echo $_REQUEST['hostname']; else echo 'localhost'; ?>" size="30" /></td>
      </tr>
      <tr>
        <td align="left">MySQL Username: </td>
        <td align="left"><input name="mysql_user" type="text" size="30" maxlength="50" value="<?php if(isset($_REQUEST['mysql_user'])) echo $_REQUEST['mysql_user']; ?>" /></td>
      </tr>
      <tr>
        <td align="left">MySQL Password: </td>
        <td align="left"><input name="mysql_password" type="text" size="30" maxlength="50" value="<?php if(isset($_REQUEST['mysql_password'])) echo $_REQUEST['mysql_password']; ?>" /></td>
      </tr>
      <tr>
        <td align="left">Database name:</td>
        <td align="left"><input name="mysql_database" type="text" size="30" maxlength="50" value="<?php if(isset($_REQUEST['mysql_database'])) echo $_REQUEST['mysql_database']; ?>" /></td>
      </tr>
      
      <tr>
        <td>&nbsp;</td>
        <td>&nbsp;</td>
      </tr>
      
      <tr>
        <td align="left" colspan="3" class="head_row">Installation paths to script directory: </td>
      </tr>
      
      	<?php 
	  	$server_path=$_SERVER['SCRIPT_FILENAME'];
		if (preg_match("/(.*)\//",$server_path,$matches)) {
			$server_path=$matches[0];
		}
		
		$server_path = str_replace("\\","/",$server_path);
		$server_path = str_replace("installation.php","",$server_path);
			
	  	?>
      <tr>
        <td align="left" valign="top">Server path to script directory:</td>
        <td align="left" colspan="2">
        	<input name="server_path" type="text" value="<?php echo $server_path; ?>" style="width:95%" /><br />
        	<span style="font-size:11px;font-style:italic;">Example: /home/server/public_html/SCRIPTFOLDER/ -  for Linux host</span><br />
            <span style="font-size:11px;font-style:italic;">Example: D:/server/www/websitedir/SCRIPTFOLDER/ -  for Windows host</span>
        </td>
      </tr>
      
      <?php 
	  	$full_url = 'http';
		if (array_key_exists('HTTPS', $_SERVER) && $_SERVER["HTTPS"] == "on") {$full_url .= "s";}
		$full_url .= "://";
		if ($_SERVER["SERVER_PORT"] != "80") {
			$full_url .= $_SERVER["SERVER_NAME"].":".$_SERVER["SERVER_PORT"].$_SERVER["REQUEST_URI"];
		} else {
			$full_url .= $_SERVER["SERVER_NAME"].$_SERVER["REQUEST_URI"];
		}
		if (preg_match("/(.*)\//",$full_url,$matches)) {
			$full_url=$matches[0];
		}
		//$full_url = str_replace("installation.php","",$full_url);
		?>
      <tr>
        <td align="left" valign="top">Full URL to script directory:</td>
        <td align="left" colspan="2">
        	<input name="full_url" type="text" value="<?php echo $full_url; ?>" style="width:95%" /><br />
        	<span style="font-size:11px;font-style:italic;">Example: http://yourdomain.com/SCRIPTFOLDER/</span>
        </td>
      </tr>      
      
      	<?php 
	  	$url = $_SERVER['PHP_SELF']; 
		if (preg_match("/(.*)\//",$url,$matches)) {
			$folder_name=$matches[0];
		}
	  	?>
      <tr>
        <td align="left" valign="top">Script directory name:</td>
        <td align="left" colspan="2">
        	<input name="folder_name" type="text" value="<?php echo $folder_name; ?>" style="width:95%" /><br />
            <span style="font-size:11px;font-style:italic;">Example: /SCRIPTFOLDER/</span>
        </td>
      </tr>
      
      	
      
      <tr>
        <td>&nbsp;</td>
        <td>&nbsp;</td>
      </tr>
      <tr>
        <td align="left" colspan="3" class="head_row">Administrator login details: <span style="font-weight:normal; font-size:11px; font-style:italic;">(Choose Username and Password you should use later when log in admin area)</span></td>
      </tr>
      <tr>
        <td align="left">Admin Username:</td>
        <td align="left"><input name="admin_user" type="text" size="30" maxlength="50" value="<?php if(isset($_REQUEST['admin_user'])) echo $_REQUEST['admin_user']; ?>" /></td>
      </tr>
      <tr>
        <td align="left">Admin Password:</td>
        <td align="left"><input name="admin_pass" type="text" size="30" maxlength="50" value="<?php if(isset($_REQUEST['admin_pass'])) echo $_REQUEST['admin_pass']; ?>" /></td>
      </tr>
      <tr>
        <td>&nbsp;</td>
        <td>&nbsp;</td>
      </tr>
      <tr>
        <td>&nbsp;</td>
        <td><input name="installScript" type="submit" value="Install Script"></td>
      </tr>
    </table>
	</form>
<?php } ?>    

</div>

</body>
</html>
