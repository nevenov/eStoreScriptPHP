<?php
error_reporting(0);
session_start();
include("configs.php");
include("language_user.php");


if(isset($_REQUEST["act"])) {
		
/////////////////////////////////////////////////
////// checking for correct captcha starts //////
	$sql = "SELECT * FROM ".$TABLE["Options"];
	$sql_result = sql_result($sql);
	$Options = mysqli_fetch_assoc($sql_result);

	if($Options['captcha']=='nocap') { // if the option is set to no Captcha
		$testvariable = true;	// test variable is set to true
	} else {
		$testvariable = false;	// test variable is set to false
	}
		
	if($Options['captcha']=='recap') { // if the option is set to phpcaptcha
		
		if (isset($_POST['captcha_code']) and $securimage->check($_POST['captcha_code']) == true) { // test variable is set	to true			
			$testvariable = true; // the captcha_code was correct	
		} else {		
			$message = $OptionsLang['Incorrect_anti_spam_code'];
		} 
		
	} elseif($Options['captcha']=='capmath' or $Options['captcha']=='cap' or $Options['captcha']=='vsc') { // if is set to math or simple captcha or very simple captcha option	
		if (preg_match('/^'.$_SESSION['key'].'$/i', $_REQUEST['string'])) { // test variable is set	to true	
			$testvariable = true;			
		} 
	}
////// checking for correct captcha ends //////
///////////////////////////////////////////////


////////////////////////////////////////////////////////////////////////////////////	
//========================== REGISTRATION PROCESS START ==========================//	
	if ($_REQUEST["act"]=='send_registration') {
		
		$sql = "SELECT * FROM ".$TABLE["Options"];
		$sql_result = sql_result($sql);
		$Options = mysqli_fetch_assoc($sql_result);
		
		if($_REQUEST["address"]=='') {
			$Registration = true; 
			$PassFilled = true;
		}
		
		if(preg_match("/[.+a-zA-Z0-9_-]+@[a-zA-Z0-9-_]+/i", $_REQUEST["user_email"]) == 0 ) { // validate email address
			$reqMessEmail = $lang['Reg_message_valid_email'];
			$_REQUEST["act"]='register';
			$Registration = false; 
		}
		if($_REQUEST["user_email"]=='') { // check for empty email
			$reqMessEmail = $lang['Reg_message_no_email'];
			$_REQUEST["act"]='register';
			$Registration = false; 
		} 	
		if(strlen($_REQUEST["user_password"])<3) { // check for at least 3 chars in password
			$reqMessPass = $lang['Reg_message_at_least3'];
			$_REQUEST["act"]='register'; 
			$Registration = false; 
			$PassFilled = false;
		} 	
		if($_REQUEST["user_password"]=='') { // check for empty password
			$reqMessPass = $lang['Reg_message_no_pass'];
			$_REQUEST["act"]='register'; 
			$Registration = false; 
			$PassFilled = false;
		} 
		if($_REQUEST["user_password2"]=='') { // check for empty password 2
			$reqMessPass2 = $lang['Reg_message_no_pass2'];
			$_REQUEST["act"]='register';
			$Registration = false; 
			$PassFilled = false; 
		} 		
		if($_REQUEST["user_password2"]!=$_REQUEST["user_password"] and $PassFilled==true) { // check for same emails
			$reqMessPass2 = $lang['Reg_message_pass_not_same'];
			$_REQUEST["act"]='register';
			unset($_REQUEST["user_password"]);
			unset($_REQUEST["user_password2"]);
			$Registration = false;  
		}
		if($testvariable!=true) { // if captcha is not verified
			$messageCaptcha = $lang['Incorrect_anti_spam_code'];
			$_REQUEST["act"]='register';
			$Registration = false; 
		} 
		
		$sql = "SELECT * FROM ".$TABLE["Users"]." WHERE user_email='".$_REQUEST["user_email"]."'";
		$sql_result = sql_result($sql);
		if(mysqli_num_rows($sql_result)>0) { // check for same emails
			$_REQUEST["act"]='register';
			$reqMessEmail = $lang['Reg_message_email_used'];
			$Registration = false;  
		}
		
		if($Registration == true) {	// registration successfull. now inset into database and send reg details to the provided email
			
			list($user_name,$domain) = explode('@',trim($_REQUEST["user_email"]) . "@"); 
			
			$sql = "INSERT INTO ".$TABLE["Users"]." 
					SET `status` 		= 'Active',  
						`user_ip` 		= '".SaveDB(getRealIpAddr())."', 
						`reg_date` 		= now(),
						`user_email` 	= '".SaveDB($_REQUEST["user_email"])."', 
						`user_name` 	= '".SaveDB($user_name)."', 
						`user_password` = '".SaveDB($_REQUEST["user_password"])."'";
			$sql_result = sql_result($sql);
						
			$sql = "SELECT * FROM ".$TABLE["Options"];
			$sql_result = sql_result($sql);
			$Options = mysqli_fetch_assoc($sql_result);
			
			// sending the details to user email
			$mailheader = "From: ".ReadDB($Options["email"])."\r\n";
			$mailheader .= "Reply-To: ".ReadDB($Options["email"])."\r\n";
			$mailheader .= "Content-type: text/html; charset=UTF-8\r\n";
			$Message_body = $lang['Reg_Email_Body']." <br />\r\n";
			$Message_body .= $lang['Useremail']." ".ReadDB($_REQUEST["user_email"])."<br />\r\n";
			$Message_body .= $lang['Password']." ".ReadDB($_REQUEST["user_password"])."<br />\r\n";
			
			//mail($_REQUEST["user_email"], $lang['Reg_Email_Subject'], $Message_body, $mailheader); // sending the email
			
			
			
			//////////// STARTING PHPMailer /////////////////////
			//adding PHPMailer library
			include( dirname(__FILE__). '/phpmailer/PHPMailerAutoload.php');
			
			//Create a new PHPMailer instance
			$mail = new PHPMailer;	
			
			
			if($Options["smtp_auth"]=="yes") {
					
				//$mail->SMTPDebug = 3; // enables SMTP debug information: 1 = errors and messages; 2 = messages only; 3 = Enable verbose debug output
				
				$mail->isSMTP();								// Set mailer to use SMTP
				$mail->Host = $Options["smtp_server"];			// Specify main and backup SMTP servers
				$mail->SMTPAuth = true;							// Enable SMTP authentication
				$mail->Username = $Options["smtp_email"];		// SMTP username
				$mail->Password = $Options["smtp_pass"];		// SMTP password
				$mail->SMTPSecure = $Options["smtp_secure"];	// Enable `tls` encryption, `ssl` also accepted
				$mail->Port = (int)$Options["smtp_port"];		// TCP port to connect to
				
				$mail->CharSet = "UTF-8";						// force setting charset UTF-8
				
								
				$mail->SetFrom(ReadDB($Options["email"]), ReadDB($Options["email"]));	// email from
				
				$mail->addAddress($_REQUEST["user_email"], $_REQUEST["user_email"]);	// Add a recipient
				
				$mail->AddReplyTo(ReadDB($Options["email"]), ReadDB($Options["email"]));// Add reply To email
				
				$mail->Subject = ReadDB($lang['Reg_Email_Subject']); // Set email subject	
		
				$mail->MsgHTML($Message_body);
				//$mail->Body    = $Message_body;
				$mail->AltBody = strip_tags($Message_body);
				
				if(!$mail->send()) {
					//$message .= ' Message could not be sent.';
					$message .= ' Mailer Error: ' . $mail->ErrorInfo;
				} else {
					//$message .= ' Message has been sent to admin!';
				}
				
				// Clear all and ready for next email sending
				$mail->ClearAddresses();
				//$mail->ClearAttachments();
				$mail->ClearReplyTos();
				$mail->ClearAllRecipients();
				//$mail->ClearCustomHeaders();
				
			} else {				
				
				//Set who the message is to be sent from
				$mail->setFrom(ReadDB($Options["email"]), ReadDB($Options["email"]));
				
				//Set who the message is to be sent to
				$mail->addAddress($_REQUEST["user_email"], $_REQUEST["user_email"]);
				
				//Set an alternative reply-to address
				$mail->addReplyTo(ReadDB($Options["email"]), ReadDB($Options["email"]));
				
				$mail->CharSet = "UTF-8"; // force setting charset UTF-8
				//Set the subject line
				$mail->Subject = ReadDB($lang["Reg_Email_Subject"]); // Set email subject
				//Read an HTML message body from an external file, convert referenced images to embedded,
				//convert HTML into a basic plain-text alternative body
				$mail->msgHTML($Message_body);
				//Replace the plain text body with one created manually
				$mail->AltBody = strip_tags($Message_body);
				//Attach an image file
				//$mail->addAttachment('images/phpmailer_mini.png');
				
				//send the message, check for errors
				if (!$mail->send()) {
					$message .= " Mailer Error: " . $mail->ErrorInfo;
				} else {
					//$message .= " Message sent to admin!";
				}
				
				// Clear all and ready for next email sending
				$mail->ClearAddresses();
				//$mail->ClearAttachments();
				$mail->ClearReplyTos();
				$mail->ClearAllRecipients();
				//$mail->ClearCustomHeaders();
				
			}
			
			//////////// ENDING PHPMailer /////////////////////	
			
			
			
			$regMessSuccess = $lang['Reg_message_success'];
			$_REQUEST["act"]='login';
		}


////////////////////////////////////////////////////////////////////////////////////	
//========================== FORGOT PASSWORD PROCESS START ==========================//	
	} elseif ($_REQUEST["act"]=='send_pass') {
		
		$Correct_email = true; 		
		
		if($_REQUEST["user_email"]=='') { // check for empty email
			$reqMessEmail = $lang['Forg_message_email_mandatory'];
			$_REQUEST["act"]='forgot_pass';
			$Correct_email = false; 
		} elseif(preg_match("/[.+a-zA-Z0-9_-]+@[a-zA-Z0-9-_]+/i", $_REQUEST["user_email"]) == 0 ) { // validate email address
			$reqMessEmail = $lang['Forg_message_valid_email'];
			$_REQUEST["act"]='forgot_pass';
			$Correct_email = false; 
		} 
		
		if($Correct_email == true) {
			
			$sql = "SELECT * FROM ".$TABLE["Users"]." WHERE user_email='".$_REQUEST["user_email"]."'";
			$sql_result = sql_result($sql);
			
			if(mysqli_num_rows($sql_result)==1) { // check for same emails

				$User = mysqli_fetch_assoc($sql_result);	
				
				$sql = "SELECT * FROM ".$TABLE["Options"];
				$sql_result = sql_result($sql);
				$Options = mysqli_fetch_assoc($sql_result);
				
				// sending the details to user email
				$mailheader = "From: ".ReadDB($Options["email"])."\r\n";
				$mailheader .= "Reply-To: ".ReadDB($Options["email"])."\r\n";
				$mailheader .= "Content-type: text/html; charset=UTF-8\r\n";
				$Message_body = "Your login details are: <br />\r\n";
				$Message_body .= $lang['Useremail']." ".ReadDB($User["user_email"])."<br />\r\n";
				$Message_body .= $lang['Password']." ".ReadDB($User["user_password"])."<br />\r\n";
				
				//mail($User["user_email"], $lang['Forg_Email_Subject'], $Message_body, $mailheader); // sending the email
				
				
				//////////// STARTING PHPMailer /////////////////////
				//adding PHPMailer library
				include( dirname(__FILE__). '/phpmailer/PHPMailerAutoload.php');
				
				//Create a new PHPMailer instance
				$mail = new PHPMailer;	
				
				
				if($Options["smtp_auth"]=="yes") {
						
					//$mail->SMTPDebug = 3; // enables SMTP debug information: 1 = errors and messages; 2 = messages only; 3 = Enable verbose debug output
					
					$mail->isSMTP();								// Set mailer to use SMTP
					$mail->Host = $Options["smtp_server"];			// Specify main and backup SMTP servers
					$mail->SMTPAuth = true;							// Enable SMTP authentication
					$mail->Username = $Options["smtp_email"];		// SMTP username
					$mail->Password = $Options["smtp_pass"];		// SMTP password
					$mail->SMTPSecure = $Options["smtp_secure"];	// Enable `tls` encryption, `ssl` also accepted
					$mail->Port = (int)$Options["smtp_port"];		// TCP port to connect to
					
					$mail->CharSet = "UTF-8";						// force setting charset UTF-8
					
									
					$mail->SetFrom(ReadDB($Options["email"]), ReadDB($Options["email"]));	// email from
					
					$mail->addAddress($User["user_email"], $User["user_email"]);	// Add a recipient
					
					$mail->AddReplyTo(ReadDB($Options["email"]), ReadDB($Options["email"]));// Add reply To email
					
					$mail->Subject = ReadDB($lang["Forg_Email_Subject"]); // Set email subject	
			
					$mail->MsgHTML($Message_body);
					//$mail->Body    = $Message_body;
					$mail->AltBody = strip_tags($Message_body);
					
					if(!$mail->send()) {
						//$message .= ' Message could not be sent.';
						$message .= ' Mailer Error: ' . $mail->ErrorInfo;
					} else {
						//$message .= ' Message has been sent to admin!';
					}
					
					// Clear all and ready for next email sending
					$mail->ClearAddresses();
					//$mail->ClearAttachments();
					$mail->ClearReplyTos();
					$mail->ClearAllRecipients();
					//$mail->ClearCustomHeaders();
					
				} else {
										
					//Set who the message is to be sent from
					$mail->setFrom(ReadDB($Options["email"]), ReadDB($Options["email"]));
					
					//Set who the message is to be sent to
					$mail->addAddress($User["user_email"], $User["user_email"]);
					
					//Set an alternative reply-to address
					$mail->addReplyTo(ReadDB($Options["email"]), ReadDB($Options["email"]));
					
					$mail->CharSet = "UTF-8"; // force setting charset UTF-8
					//Set the subject line
					$mail->Subject = ReadDB($lang["Forg_Email_Subject"]); // Set email subject
					//Read an HTML message body from an external file, convert referenced images to embedded,
					//convert HTML into a basic plain-text alternative body
					$mail->msgHTML($Message_body);
					//Replace the plain text body with one created manually
					$mail->AltBody = strip_tags($Message_body);
					//Attach an image file
					//$mail->addAttachment('images/phpmailer_mini.png');
					
					//send the message, check for errors
					if (!$mail->send()) {
						$message .= " Mailer Error: " . $mail->ErrorInfo;
					} else {
						//$message .= " Message sent to admin!";
					}
					
					// Clear all and ready for next email sending
					$mail->ClearAddresses();
					//$mail->ClearAttachments();
					$mail->ClearReplyTos();
					$mail->ClearAllRecipients();
					//$mail->ClearCustomHeaders();
					
				}
				
				//////////// ENDING PHPMailer /////////////////////	
				
				
				$regMessSuccess = $lang['Forg_message_success'];
				$_REQUEST["act"]='login';	
			
			} elseif(mysqli_num_rows($sql_result)<1) {
				$_REQUEST["act"]='forgot_pass';
				$reqMessEmail = $lang['Forg_message_no_email'];	
			} elseif(mysqli_num_rows($sql_result)>1) {
				$_REQUEST["act"]='forgot_pass';
				$reqMessEmail = $lang['Forg_message_2emails'];
			} 
			
		}


////////////////////////////////////////////////////////////////////////////////////	
//========================== LOGOUT PROCESS START ==========================//	
	} elseif ($_REQUEST["act"]=='logout') {
		$_SESSION["ProFiAnTsUsErAdsLoGin"] = "";
		unset($_SESSION["ProFiAnTsUsErAdsLoGin"]);
		$_SESSION["UserId"] = "";
		unset($_SESSION["UserId"]);
		
		
////////////////////////////////////////////////////////////////////////////////////	
//========================== LOGIN PROCESS START ==========================//
	} elseif ($_REQUEST["act"]=='login') {
		$sql = "SELECT * FROM ".$TABLE["Users"]." WHERE user_email='".SafetyDB($_REQUEST["user_email"])."' AND user_password='".SafetyDB($_REQUEST["user_password"])."' AND status='Active'";
		$sql_result = sql_result($sql);
		
		if (mysqli_num_rows($sql_result)==1) {
			$UserId = mysqli_fetch_assoc($sql_result);
			$_SESSION["ProFiAnTsUsErAdsLoGin"] = "LoggedIn";	
			$_SESSION["UserId"] = $UserId['id'];
			
			$sql = "UPDATE ".$TABLE["Users"]." 
					SET `user_ip` = '".SaveDB(getRealIpAddr())."' 
					WHERE `id` = '".$UserId["id"]."'";
			$sql_result = sql_result($sql);
					
			$_REQUEST["act"]='ads';
		} else {
			$logMessage = $lang['Login_message'];
		}
	}
}
?><!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title><?php echo $lang['Script_UserArea_Header']; ?></title>

<script language="javascript" src="include/jquery-1.11.2.min.js"></script>
<script language="javascript" src="include/functions.js"></script>
<script language="javascript" src="include/color_pick.js"></script>
<script type="text/javascript" src="include/datetimepicker_css.js"></script>
<link href="styles/user.css" rel="stylesheet" type="text/css" />

<script type="text/javascript">

function checkAds(form){

	var title, description, name, isOk = true;
	var message = "";
	
	message = "<?php echo $lang["Message_Please_fill_required_fields"]; ?>";
	
	title		= form.title.value;	
	description	= form.description.value;
	name		= form.name.value;

	if (title.length==0){
		form.title.focus();
		isOk=false;
	}
	else if (description.length==0){
		form.description.focus();
		isOk=false;
	}
	else if (name.length==0){
		form.name.focus();
		isOk=false;
	}

	if (!isOk){			   
		alert(message);
		return isOk;
	} else {
		return isOk;
	}
}

function limitText(limitField, limitNum) {
	if (limitField.value.length > limitNum) {
		limitField.value = limitField.value.substring(0, limitNum);
	} 
}
</script>

</head>

<body>

<?php 
$Logged = false;

if ((isset($_SESSION["ProFiAnTsUsErAdsLoGin"])) and ($_SESSION["ProFiAnTsUsErAdsLoGin"]=="LoggedIn")) {
	$Logged = true;
	$UserId = $_SESSION['UserId'];
	
	$sql = "SELECT * FROM ".$TABLE["Users"]." WHERE id='".$UserId."'";
	$sql_result = sql_result($sql);
	$User = mysqli_fetch_assoc($sql_result);	
}
?>

<div class="logo">
	<div class="script_name"><?php echo $lang['Script_UserArea_Header']; ?></div>
    <?php if ( $Logged ){ ?>
	<div class="logout_button"><a href="user.php?act=logout"><img src="images/logout1.png" width="32" alt="Logout" border="0" /></a></div>
    <?php } ?>
    <div class="clear"></div>
</div>

<div style="clear:both"></div>

<?php  
if ( $Logged ){

if ($_REQUEST["act"]=='updateProfile') {
	
	$sql = "SELECT * FROM ".$TABLE["Options"];
	$sql_result = sql_result($sql);
	$Options = mysqli_fetch_assoc($sql_result);
	
	$sql = "SELECT * FROM ".$TABLE["Users"]." WHERE id='".$UserId."'";
	$sql_result = sql_result($sql);
	$User = mysqli_fetch_assoc($sql_result);	
		
	$sendLogDetails = '';
	if(trim($_REQUEST["user_password"])!=trim($User['user_password'])) { // send email to user with the new login details
		$sendLogDetails = 'yes';
	}
	
	if(trim($_REQUEST["user_name"])=='') {
		list($user_name,$domain) = explode('@',trim($User["user_email"]) . "@"); 	
		$_REQUEST["user_name"] = $user_name;
	}
	
	$sql = "UPDATE ".$TABLE["Users"]." 
			SET `user_password` = '".SaveDB(trim($_REQUEST["user_password"]))."', 
				`user_name` 	= '".SaveDB($_REQUEST["user_name"])."', 
				`user_address`	= '".SaveDB($_REQUEST["user_address"])."', 
				`user_location` = '".SaveDB($_REQUEST["user_location"])."',  
				`user_phone` 	= '".SaveDB($_REQUEST["user_phone"])."',   
				`user_url` 		= '".SaveDB($_REQUEST["user_url"])."'  
			WHERE id='".$User["id"]."'";
	$sql_result = sql_result($sql);
	
	
	if($sendLogDetails == 'yes') {
		$mailheader = "From: ".ReadDB($Options["email"])."\r\n";
		$mailheader .= "Reply-To: ".ReadDB($Options["email"])."\r\n";
		$mailheader .= "Content-type: text/html; charset=UTF-8\r\n";
		$Message_body = "You have updated your password. Your new login details are: <br />\r\n";
		$Message_body .= "Login Email: ".ReadDB($User["user_email"])."<br />\r\n";
		$Message_body .= "Login Password: ".SaveDB(trim($_REQUEST["user_password"]))."<br />\r\n";
		//mail($User["user_email"], "Password changed! ", $Message_body, $mailheader);
		
		
		//////////// STARTING PHPMailer /////////////////////
		//adding PHPMailer library
		include( dirname(__FILE__). '/phpmailer/PHPMailerAutoload.php');
		
		//Create a new PHPMailer instance
		$mail = new PHPMailer;	
		
		
		if($Options["smtp_auth"]=="yes") {
				
			//$mail->SMTPDebug = 3; // enables SMTP debug information: 1 = errors and messages; 2 = messages only; 3 = Enable verbose debug output
			
			$mail->isSMTP();								// Set mailer to use SMTP
			$mail->Host = $Options["smtp_server"];			// Specify main and backup SMTP servers
			$mail->SMTPAuth = true;							// Enable SMTP authentication
			$mail->Username = $Options["smtp_email"];		// SMTP username
			$mail->Password = $Options["smtp_pass"];		// SMTP password
			$mail->SMTPSecure = $Options["smtp_secure"];	// Enable `tls` encryption, `ssl` also accepted
			$mail->Port = (int)$Options["smtp_port"];		// TCP port to connect to
			
			$mail->CharSet = "UTF-8";						// force setting charset UTF-8
			
							
			$mail->SetFrom(ReadDB($Options["email"]), ReadDB($Options["email"]));	// email from
			
			$mail->addAddress($User["user_email"], $User["user_email"]);	// Add a recipient
			
			$mail->AddReplyTo(ReadDB($Options["email"]), ReadDB($Options["email"]));// Add reply To email
			
			$mail->Subject = "Password changed! "; // Set email subject	
	
			$mail->MsgHTML($Message_body);
			//$mail->Body    = $Message_body;
			$mail->AltBody = strip_tags($Message_body);
			
			if(!$mail->send()) {
				//$message .= ' Message could not be sent.';
				$message .= ' Mailer Error: ' . $mail->ErrorInfo;
			} else {
				//$message .= ' Message has been sent to admin!';
			}
			
			// Clear all and ready for next email sending
			$mail->ClearAddresses();
			//$mail->ClearAttachments();
			$mail->ClearReplyTos();
			$mail->ClearAllRecipients();
			//$mail->ClearCustomHeaders();
			
		} else {
			
			
			//Set who the message is to be sent from
			$mail->setFrom(ReadDB($Options["email"]), ReadDB($Options["email"]));
			
			//Set who the message is to be sent to
			$mail->addAddress($User["user_email"], $User["user_email"]);
			
			//Set an alternative reply-to address
			$mail->addReplyTo(ReadDB($Options["email"]), ReadDB($Options["email"]));
			
			$mail->CharSet = "UTF-8"; // force setting charset UTF-8
			//Set the subject line
			$mail->Subject = "Password changed! "; // Set email subject
			//Read an HTML message body from an external file, convert referenced images to embedded,
			//convert HTML into a basic plain-text alternative body
			$mail->msgHTML($Message_body);
			//Replace the plain text body with one created manually
			$mail->AltBody = strip_tags($Message_body);
			//Attach an image file
			//$mail->addAttachment('images/phpmailer_mini.png');
			
			//send the message, check for errors
			if (!$mail->send()) {
				$message .= " Mailer Error: " . $mail->ErrorInfo;
			} else {
				//$message .= " Message sent to admin!";
			}
			
			// Clear all and ready for next email sending
			$mail->ClearAddresses();
			//$mail->ClearAttachments();
			$mail->ClearReplyTos();
			$mail->ClearAllRecipients();
			//$mail->ClearCustomHeaders();
			
		}
		
		//////////// ENDING PHPMailer /////////////////////	
		
		$message = $lang['Message_Profile_Email_sent'];
	}
	
	$_REQUEST["act"]='profile'; 
	$message .= $lang['Message_Profile_saved'];
	

} elseif ($_REQUEST["act"] == "addAds"){
	
	$sql = "SELECT * FROM ".$TABLE["Options"];
	$sql_result = sql_result($sql);
	$Options = mysqli_fetch_assoc($sql_result);
	$OptionsVis = unserialize($Options['visual']);
	
	if ($Options["ads_approve"]=='true') {			
		$status = 'Waiting';
	} else {
		$status = 'Online';
	}
	
	$WordAllowed = true; // all is correct and all the words are not banned
	// adding banned words into array	
	$BannedWords = explode(",", ReadDB($Options["ban_words"]));
	
	if (count($BannedWords)>0) {
		$checkComment = strtolower($_REQUEST["title"]).strtolower($_REQUEST["description"]);
		for($i=0;$i<count($BannedWords);$i++){
		  $banWord = trim($BannedWords[$i]);
		  if (trim($BannedWords[$i])<>'') {
			  if(preg_match("/".$banWord."/i", $checkComment)){ 
				  $WordAllowed = false; //  banned word used in title or description
				  break;
			  }
		  }
		}
	}
	
	if($WordAllowed==false) {
		$message =  $lang['Message_Entry_banned_word']; 
		$_REQUEST["act"]='newAds';
	} else {
		$expire_date = date("Y-m-d H:i:s", mktime(date("H"), date("i"), date("s"), date("m"), date("d")+$Options['expire_days'], date("Y")));
		$title = breakLongWords($_REQUEST["title"], 20, " ");
		$description = breakLongWords($_REQUEST["description"], 20, " ");
		$name = breakLongWords($_REQUEST["name"], 20, " ");
		$location = breakLongWords($_REQUEST["location"], 20, " ");
		$price = str_replace(" ", "", $_REQUEST["price"]);
			
		$sql = "INSERT INTO ".$TABLE["Ads"]." 
				SET `publish_date`	= now(),
					`expire_date`	= '".SaveDB($expire_date)."',
					`status` 		= '".$status."', 
					`user_id` 		= '".$UserId."',		
					`cat_id` 		= '".SaveDB($_REQUEST["cat_id"])."',		
					`topads` 		= 'false',
					`highlight` 	= 'false',
					`title` 		= '".SaveDB($_REQUEST["title"])."',
					`description` 	= '".SaveDB($_REQUEST["description"])."',
					`price` 		= '".SaveDB($price)."',
					`name`			= '".SaveDB($_REQUEST["name"])."',
					`address`		= '".SaveDB($_REQUEST["address"])."',
					`location` 		= '".SaveDB($_REQUEST["location"])."',
					`email` 		= '".SaveDB($User["user_email"])."',
					`phone` 		= '".SaveDB($_REQUEST["phone"])."',
					`website` 		= '".SaveDB($_REQUEST["website"])."',
					`reviews` 		= '0'";
		$sql_result = sql_result($sql);
		
		$index_id = mysqli_insert_id($connCl);
		
		//// upload up to 5 images start
		for($i=1; $i<=5; $i++) {
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
			} else { $message .= "Image ".$i." is not uploaded! ";  }
		}
		
		$mailheader = "From: ".ReadDB($Options["email"])."\r\n";
		$mailheader .= "Reply-To: ".ReadDB($Options["email"])."\r\n";
		$mailheader .= "Content-type: text/html; charset=UTF-8\r\n";
		$Message_body = $lang["Email_Body_Ad_Title"]." <strong>".strip_tags(ReadDB($_REQUEST["title"]))."</strong> <br />";
		$Message_body .= $lang["Email_Body_Ad_Name"]." <strong>".strip_tags(ReadDB($_REQUEST["name"]))."</strong> <br />";
		$Message_body .= $lang["Email_Body_Ad_Email"]." <strong>".strip_tags(ReadDB($User["user_email"]))."</strong> <br /><br />";
		$Message_body .= "<a href='".$CONFIG["full_url"]."admin.php'>".$CONFIG["full_url"]."admin.php</a>";
		//mail(ReadDB($Options["email"]), $lang["Email_Subject_New_Ad_posted"], $Message_body, $mailheader);	
		
		
		
		//////////// STARTING PHPMailer /////////////////////
		//adding PHPMailer library
		include( dirname(__FILE__). '/phpmailer/PHPMailerAutoload.php');
		
		//Create a new PHPMailer instance
		$mail = new PHPMailer;	
		
		
		if($Options["smtp_auth"]=="yes") {
				
			//$mail->SMTPDebug = 3; // enables SMTP debug information: 1 = errors and messages; 2 = messages only; 3 = Enable verbose debug output
			
			$mail->isSMTP();								// Set mailer to use SMTP
			$mail->Host = $Options["smtp_server"];			// Specify main and backup SMTP servers
			$mail->SMTPAuth = true;							// Enable SMTP authentication
			$mail->Username = $Options["smtp_email"];		// SMTP username
			$mail->Password = $Options["smtp_pass"];		// SMTP password
			$mail->SMTPSecure = $Options["smtp_secure"];	// Enable `tls` encryption, `ssl` also accepted
			$mail->Port = (int)$Options["smtp_port"];		// TCP port to connect to
			
			$mail->CharSet = "UTF-8";						// force setting charset UTF-8
			
							
			$mail->SetFrom(ReadDB($Options["email"]), ReadDB($Options["email"]));	// email from
			
			$mail->addAddress($Options["email"], $Options["email"]);	// Add a recipient
			
			$mail->AddReplyTo(ReadDB($Options["email"]), ReadDB($Options["email"]));// Add reply To email
			
			$mail->Subject = $lang["Email_Subject_New_Ad_posted"]; // Set email subject	
	
			$mail->MsgHTML($Message_body);
			//$mail->Body    = $Message_body;
			$mail->AltBody = strip_tags($Message_body);
			
			if(!$mail->send()) {
				//$message .= ' Message could not be sent.';
				$message .= ' Mailer Error: ' . $mail->ErrorInfo;
			} else {
				//$message .= ' Message has been sent to admin!';
			}
			
			// Clear all and ready for next email sending
			$mail->ClearAddresses();
			//$mail->ClearAttachments();
			$mail->ClearReplyTos();
			$mail->ClearAllRecipients();
			//$mail->ClearCustomHeaders();
			
		} else {
			
			
			//Set who the message is to be sent from
			$mail->setFrom(ReadDB($Options["email"]), ReadDB($Options["email"]));
			
			//Set who the message is to be sent to
			$mail->addAddress($Options["email"], $Options["email"]);
			
			//Set an alternative reply-to address
			$mail->addReplyTo(ReadDB($Options["email"]), ReadDB($Options["email"]));
			
			$mail->CharSet = "UTF-8"; // force setting charset UTF-8
			//Set the subject line
			$mail->Subject = $lang["Email_Subject_New_Ad_posted"]; // Set email subject
			//Read an HTML message body from an external file, convert referenced images to embedded,
			//convert HTML into a basic plain-text alternative body
			$mail->msgHTML($Message_body);
			//Replace the plain text body with one created manually
			$mail->AltBody = strip_tags($Message_body);
			//Attach an image file
			//$mail->addAttachment('images/phpmailer_mini.png');
			
			//send the message, check for errors
			if (!$mail->send()) {
				$message .= " Mailer Error: " . $mail->ErrorInfo;
			} else {
				//$message .= " Message sent to admin!";
			}
			
			// Clear all and ready for next email sending
			$mail->ClearAddresses();
			//$mail->ClearAttachments();
			$mail->ClearReplyTos();
			$mail->ClearAllRecipients();
			//$mail->ClearCustomHeaders();
			
		}
		
		
		
		
		
		$Message_body = $lang["EmailUser_Ad_Title"]." <strong>".strip_tags(ReadDB($_REQUEST["title"]))."</strong> <br />";
		$Message_body .= $lang["EmailUser_Ad_Name"]." <strong>".strip_tags(ReadDB($_REQUEST["name"]))."</strong> <br />";
		$Message_body .= $lang["EmailUser_Ad_Email"]." <strong>".strip_tags(ReadDB($User["user_email"]))."</strong> <br /><br />";
		//mail(ReadDB($User["user_email"]), $lang["EmailUser_Subject"], $Message_body, $mailheader);	
		
		if($Options["smtp_auth"]=="yes") {
				
			//$mail->SMTPDebug = 3; // enables SMTP debug information: 1 = errors and messages; 2 = messages only; 3 = Enable verbose debug output
			
			$mail->isSMTP();								// Set mailer to use SMTP
			$mail->Host = $Options["smtp_server"];			// Specify main and backup SMTP servers
			$mail->SMTPAuth = true;							// Enable SMTP authentication
			$mail->Username = $Options["smtp_email"];		// SMTP username
			$mail->Password = $Options["smtp_pass"];		// SMTP password
			$mail->SMTPSecure = $Options["smtp_secure"];	// Enable `tls` encryption, `ssl` also accepted
			$mail->Port = (int)$Options["smtp_port"];		// TCP port to connect to
			
			$mail->CharSet = "UTF-8";						// force setting charset UTF-8
			
							
			$mail->SetFrom(ReadDB($Options["email"]), ReadDB($Options["email"]));	// email from
			
			$mail->addAddress(ReadDB($User["user_email"]), ReadDB($User["user_email"]));	// Add a recipient
			
			$mail->AddReplyTo(ReadDB($Options["email"]), ReadDB($Options["email"]));// Add reply To email
			
			$mail->Subject = $lang["EmailUser_Subject"]; // Set email subject	
	
			$mail->MsgHTML($Message_body);
			//$mail->Body    = $Message_body;
			$mail->AltBody = strip_tags($Message_body);
			
			if(!$mail->send()) {
				//$message .= ' Message could not be sent.';
				$message .= ' Mailer Error: ' . $mail->ErrorInfo;
			} else {
				//$message .= ' Message has been sent to admin!';
			}
			
			// Clear all and ready for next email sending
			$mail->ClearAddresses();
			//$mail->ClearAttachments();
			$mail->ClearReplyTos();
			$mail->ClearAllRecipients();
			//$mail->ClearCustomHeaders();
			
		} else {
			
			
			//Set who the message is to be sent from
			$mail->setFrom(ReadDB($Options["email"]), ReadDB($Options["email"]));
			
			//Set who the message is to be sent to
			$mail->addAddress(ReadDB($User["user_email"]), ReadDB($User["user_email"]));
			
			//Set an alternative reply-to address
			$mail->addReplyTo(ReadDB($Options["email"]), ReadDB($Options["email"]));
			
			$mail->CharSet = "UTF-8"; // force setting charset UTF-8
			//Set the subject line
			$mail->Subject = $lang["EmailUser_Subject"]; // Set email subject
			//Read an HTML message body from an external file, convert referenced images to embedded,
			//convert HTML into a basic plain-text alternative body
			$mail->msgHTML($Message_body);
			//Replace the plain text body with one created manually
			$mail->AltBody = strip_tags($Message_body);
			//Attach an image file
			//$mail->addAttachment('images/phpmailer_mini.png');
			
			//send the message, check for errors
			if (!$mail->send()) {
				$message .= " Mailer Error: " . $mail->ErrorInfo;
			} else {
				//$message .= " Message sent to admin!";
			}
			
			// Clear all and ready for next email sending
			$mail->ClearAddresses();
			//$mail->ClearAttachments();
			$mail->ClearReplyTos();
			$mail->ClearAllRecipients();
			//$mail->ClearCustomHeaders();
			
		}
		
		
		
		//$_REQUEST["act"] = "ads";		
		$message .= $lang['Message_Entry_created'];
		
		if($status == 'Waiting') {
			$message .= $lang['Message_Waiting_for_approval'];
		}
	
		echo '<script type="text/javascript">window.location.href="'.$thisPage.'?act=ads&message='.urlencode($message).'";</script>';
	}

} elseif ($_REQUEST["act"]=='updateAds') {
	
	$sql = "SELECT * FROM ".$TABLE["Options"];
	$sql_result = sql_result($sql);
	$Options = mysqli_fetch_assoc($sql_result);
	$OptionsVis = unserialize($Options['visual']);
	
	
	$WordAllowed = true; // all is correct and all the words are not banned
	// adding banned words into array	
	$BannedWords = explode(",", ReadDB($Options["ban_words"]));
	
	if (count($BannedWords)>0) {
		$checkComment = strtolower($_REQUEST["title"]).strtolower($_REQUEST["description"]);
		for($i=0;$i<count($BannedWords);$i++){
		  $banWord = trim($BannedWords[$i]);
		  if (trim($BannedWords[$i])<>'') {
			  if(preg_match("/".$banWord."/i", $checkComment)){ 
				  $WordAllowed = false; //  banned word used in title or description
				  break;
			  }
		  }
		}
	}
	
	if($WordAllowed==false) {
		$message =  $lang['Message_Entry_banned_word']; 
		$_REQUEST["act"]='editAds';
	} else {
	
		$price = str_replace(" ", "", $_REQUEST["price"]);
	
		$sql = "UPDATE ".$TABLE["Ads"]." 
				SET `cat_id` 		= '".SaveDB($_REQUEST["cat_id"])."', 		
					`title` 		= '".SaveDB($_REQUEST["title"])."',
					`description` 	= '".SaveDB($_REQUEST["description"])."',
					`price` 		= '".SaveDB($price)."',
					`name` 			= '".SaveDB($_REQUEST["name"])."',
					`address`		= '".SaveDB($_REQUEST["address"])."',
					`location` 		= '".SaveDB($_REQUEST["location"])."',
					`email` 		= '".SaveDB($User["user_email"])."',
					`phone` 		= '".SaveDB($_REQUEST["phone"])."',
					`website` 		= '".SaveDB($_REQUEST["website"])."'  
				WHERE id='".SafetyDB($_REQUEST["id"])."' AND user_id='".$UserId."'";
		$sql_result = sql_result($sql);
		
		$sql = "SELECT * FROM ".$TABLE["Ads"]." WHERE id = '".$_REQUEST["id"]."' AND user_id='".$UserId."'";
		$sql_result = sql_result($sql);
		$imageArr = mysqli_fetch_assoc($sql_result);
		$imageArr["image1"] = stripslashes($imageArr["image1"]);
		$imageArr["image2"] = stripslashes($imageArr["image2"]);
		$imageArr["image3"] = stripslashes($imageArr["image3"]);
		$imageArr["image4"] = stripslashes($imageArr["image4"]);
		$imageArr["image5"] = stripslashes($imageArr["image5"]);
		
		$index_id = SafetyDB($_REQUEST["id"]);
			
		//// upload up to 5 images start
		for($i=1; $i<=5; $i++) {
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
		
		if($_REQUEST["updatepreview"]=='Update and Preview') {
			$_REQUEST["act"]='viewAds'; 		
		} else {
			$_REQUEST["act"]='ads'; 
		}
		$message .= $lang['Message_Entry_updated'];
	}
	
} elseif ($_REQUEST["act"]=='renew') {	
	
	$sql = "SELECT * FROM ".$TABLE["Options"];
	$sql_result = sql_result($sql);
	$Options = mysqli_fetch_assoc($sql_result);
	
	$expire_date = date("Y-m-d H:i:s", mktime(date("H"), date("i"), date("s"), date("m"), date("d")+$Options['expire_days'], date("Y")));
	
	$sql = "UPDATE ".$TABLE["Ads"]." 
			SET `publish_date`	= now(),
				`expire_date`	= '".SaveDB($expire_date)."' 
			WHERE id='".SafetyDB($_REQUEST["id"])."' AND user_id='".$UserId."'";
	$sql_result = sql_result($sql);
	
	$_REQUEST["act"]='ads'; 
	$message = $lang['Message_Entry_renewed'];
	
} elseif ($_REQUEST["act"]=='renewEx') {	
	
	$sql = "SELECT * FROM ".$TABLE["Options"];
	$sql_result = sql_result($sql);
	$Options = mysqli_fetch_assoc($sql_result);
	
	$expire_date = date("Y-m-d H:i:s", mktime(date("H"), date("i"), date("s"), date("m"), date("d")+$Options['expire_days'], date("Y")));
	
	$sql = "UPDATE ".$TABLE["Ads"]." 
			SET `publish_date`	= now(),
				`expire_date`	= '".SaveDB($expire_date)."',
				`status`		= 'Online' 
			WHERE id='".SafetyDB($_REQUEST["id"])."' AND user_id='".$UserId."'";
	$sql_result = sql_result($sql);
	
	$_REQUEST["act"]='ads'; 
	$message = $lang['Message_Entry_renewed'];	

} elseif ($_REQUEST["act"]=='delAds') {
	
	$sql = "SELECT * FROM ".$TABLE["Ads"]." WHERE id = '".$_REQUEST["id"]."' AND user_id='".$UserId."'";
	$sql_result = sql_result($sql);
	$imageArr = mysqli_fetch_assoc($sql_result);
	for($i=1; $i<=5; $i++) {
		$imageArr["image".$i] = stripslashes($imageArr["image".$i]);
		if($imageArr["image".$i] != "") unlink($CONFIG["upload_folder"].$imageArr["image".$i]);
		if($imageArr["image".$i] != "") unlink($CONFIG["upload_thumbs"].$imageArr["image".$i]);
	}

	$sql = "DELETE FROM ".$TABLE["Ads"]." WHERE id='".$_REQUEST["id"]."' and user_id='".$UserId."'";
   	$sql_result = sql_result($sql);
	
 	$_REQUEST["act"]='ads'; 
	$message = $lang['Message_Entry_deleted'];
	
} elseif ($_REQUEST["act"]=="delImage") { 
	
	$sql = "SELECT * FROM ".$TABLE["Ads"]." WHERE id = '".$_REQUEST["id"]."' AND user_id='".$UserId."'";
	$sql_result = sql_result($sql);
	$imageArr = mysqli_fetch_assoc($sql_result);
	
	$i = SafetyDB($_REQUEST["imgnum"]);
	
	$imageArr["image".$i] = stripslashes($imageArr["image".$i]);
	if($imageArr["image".$i] != "") unlink($CONFIG["upload_folder"].$imageArr["image".$i]);
	if($imageArr["image".$i] != "") unlink($CONFIG["upload_thumbs"].$imageArr["image".$i]);
	
	$sql = "UPDATE `".$TABLE["Ads"]."` SET `image".$i."` = '' WHERE id = '".$_REQUEST["id"]."' AND user_id='".$UserId."'";
	$sql_result = sql_result($sql);
	
	$_REQUEST["act"] = "editAds";
	$message = $lang['Message_Image_deleted'];

}

if ($_REQUEST["act"]=='' or !isset($_REQUEST["act"])) $_REQUEST["act"]='ads';

$sql = "SELECT * FROM ".$TABLE["Users"]." WHERE id='".$UserId."'";
$sql_result = sql_result($sql);
$User = mysqli_fetch_assoc($sql_result);	
?> 

<div class="welcome_user"><?php echo $lang['Welcome_User']; ?> <?php echo $User['user_name']; ?></div> 

<div class="menuButtons">
    <div class="menuButton"><a<?php if($_REQUEST['act']=='ads' or $_REQUEST['act']=='newAds' or $_REQUEST['act']=='viewAds' or $_REQUEST['act']=='editAds' or $_REQUEST["act"]=='exads') echo ' class="selected"'; ?> href="user.php?act=ads"><span><?php echo $lang['menu_Button_1']; ?></span></a></div>
    <div class="menuButton"><a<?php if($_REQUEST['act']=='profile') echo ' class="selected"'; ?> href="user.php?act=profile"><span><?php echo $lang['menu_Button_2']; ?></span></a></div>
    <div class="clear"></div>        
</div>


<div class="admin_wrapper">

	<?php
    if ($_REQUEST["act"]=='ads' or $_REQUEST["act"]=='newAds' or $_REQUEST["act"]=='editAds' or $_REQUEST["act"]=='viewAds' or $_REQUEST["act"]=='exads') {
		$sqlExp   = "SELECT id FROM ".$TABLE["Ads"]." WHERE status='Expired' AND user_id='".$UserId."'";
		$sql_resultExp = sql_result($sqlExp);
		$AdsExp = mysqli_num_rows($sql_resultExp);
		if($AdsExp>0) { $numExAds = "(".$AdsExp.")"; } else { $AdsExp = ""; }
    ?>	
    <div class="menuSubButton"><a<?php if($_REQUEST['act']=='ads' or $_REQUEST['act']=='editAds' or $_REQUEST["act"]=='viewAds') echo ' class="selected"'; ?> href="user.php?act=ads"><?php echo $lang['submenu1_button1']; ?></a></div>
    <div class="menuSubButton"><a<?php if($_REQUEST['act']=='newAds') echo ' class="selected"'; ?> href="user.php?act=newAds"><?php echo $lang['submenu1_button2']; ?></a></div>
    <div class="menuSubButton"><a<?php if($_REQUEST['act']=='exads' or $_REQUEST['act']=='editAds' or $_REQUEST["act"]=='viewAds') echo ' class="selected"'; ?> href="user.php?act=exads"><?php echo $lang['submenu1_button3']; ?> <?php echo $numExAds; ?></a></div>
    <div class="clear"></div>        
    
    <?php } ?>
    
    
    <?php if(isset($message)) { ?>
    <div class="message<?php if($_REQUEST['act']=='profile') echo ' comm_marg'; ?>"><?php echo $message; ?></div>
    <?php } elseif(isset($_REQUEST['message'])) { ?>
    <div class="message<?php if($_REQUEST['act']=='profile') echo ' comm_marg'; ?>"><?php echo urldecode($_REQUEST['message']); ?></div>
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
		
	if(isset($_REQUEST["p"]) and $_REQUEST["p"]!='') { 
		$pageNum = (int) SafetyDB(urldecode($_REQUEST["p"]));
		if($pageNum<=0) $pageNum = 1;
	} else { 
		$pageNum = 1;
	}
	
	$orderByArr = array("title", "publish_date", "status", "cat_id", "reviews");
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
	
	$sqlOnline   = "SELECT id FROM ".$TABLE["Ads"]." WHERE status='Online' AND user_id='".$UserId."'";
	$sql_resultOnline = sql_result($sqlOnline);
	$AdsOnline = mysqli_num_rows($sql_resultOnline);
	
	$sqlExp   = "SELECT id FROM ".$TABLE["Ads"]." WHERE status='Expired' AND user_id='".$UserId."'";
	$sql_resultExp = sql_result($sqlExp);
	$AdsExp = mysqli_num_rows($sql_resultExp);
	
	$sqlCount   = "SELECT id FROM ".$TABLE["Ads"]." WHERE user_id='".$UserId."'";
	$sql_resultCount = sql_result($sqlCount);
	$AdsCount = mysqli_num_rows($sql_resultCount);
	
	$AdsWaitApproval = $AdsCount - $AdsExp - $AdsOnline;
?>
	<div class="pageDescr"><?php echo $lang['List_Dashboard1']; ?> <strong style="font-size:16px"><?php echo $AdsOnline; ?></strong> <?php echo $lang['List_Dashboard2']; ?> <strong style="font-size:16px"><?php echo $AdsWaitApproval; ?></strong>.</div>
    
    <div class="searchForm">
    <form action="user.php?act=ads" method="post" name="form" class="formStyle">
      <input type="text" name="search" value="<?php echo $_REQUEST["search"]; ?>" class="searchfield" placeholder="<?php echo $lang['List_Search_Placeholder']; ?>" />
      <input type="submit" value="<?php echo $lang['List_Search_Button']; ?>" class="searchButton" />
    </form>
    </div>
    
	<table border="0" cellspacing="0" cellpadding="8" class="allTables">
  	  <tr>
        <td class="headlist"><a href="user.php?act=ads&orderType=<?php echo $norderType; ?>&search=<?php echo urlencode($_REQUEST["search"]); ?>&orderBy=title&p=<?php echo $_REQUEST['p']; ?>"><?php echo $lang['List_Title']; ?></a></td>
        <td width="17%" class="headlist"><a href="user.php?act=ads&orderType=<?php echo $norderType; ?>&search=<?php echo urlencode($_REQUEST["search"]); ?>&orderBy=publish_date&p=<?php echo $_REQUEST['p']; ?>"><?php echo $lang['List_Date_Activity']; ?></a></td>
        <td width="12%" class="headlist"><a href="user.php?act=ads&orderType=<?php echo $norderType; ?>&search=<?php echo urlencode($_REQUEST["search"]); ?>&orderBy=status&p=<?php echo $_REQUEST['p']; ?>"><?php echo $lang['List_Status']; ?></a></td>
        <td width="13%" class="headlist"><a href="user.php?act=ads&orderType=<?php echo $norderType; ?>&search=<?php echo urlencode($_REQUEST["search"]); ?>&orderBy=cat_id&p=<?php echo $_REQUEST['p']; ?>"><?php echo $lang['List_Category']; ?></a></td>
        <td width="7%" class="headlist"><a href="user.php?act=ads&orderType=<?php echo $norderType; ?>&search=<?php echo urlencode($_REQUEST["search"]); ?>&orderBy=reviews&p=<?php echo $_REQUEST['p']; ?>"><?php echo $lang['List_Views']; ?></a></td>
        <td class="headlist" colspan="4">&nbsp;</td>
  	  </tr>
      
  	<?php 
	if(isset($_REQUEST["search"]) and ($_REQUEST["search"]!="")) {
	  $findMe = SafetyDB($_REQUEST["search"]);
	  $search = "AND title LIKE '%".$findMe."%'";
	} else {
	  $search = '';
	}

	$sql   = "SELECT count(*) as total FROM ".$TABLE["Ads"]." WHERE user_id='".$UserId."' AND status<>'Expired' ".$search;
	$sql_result = sql_result($sql);
	$row   = mysqli_fetch_array($sql_result);
	$count = $row["total"];
	$pages = ceil($count/30);

	$sql = "SELECT * FROM ".$TABLE["Ads"]." WHERE user_id='".$UserId."' AND status<>'Expired' ".$search." 
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
        <td class="bodylist"><?php if(ReadDB($Ads["status"])=="Online"){ echo $lang['List_Online'];} else { echo $lang['List_Waiting'];}?></td>        
        <td class="bodylist">
        	<?php 
			$sqlCat = "SELECT * FROM ".$TABLE["Categories"]." WHERE id='".$Ads["cat_id"]."'";
			$sql_resultCat = sql_result($sqlCat);
			$Cat = mysqli_fetch_assoc($sql_resultCat);	
			if($Cat["id"]>0) echo ReadDB($Cat["cat_name"]); else echo "------"; ?>
        </td>
        <td class="bodylist"><?php if($Ads["reviews"]=='') echo "0"; else echo $Ads["reviews"]; ?></td>
        <td class="bodylistAct"><a class="view" href='user.php?act=renew&id=<?php echo $Ads["id"]; ?>'  onclick="return confirm('Are you sure you want to renew with another period of <?php echo $Options["expire_days"]; ?> days?');" title="Renew"><img class="act" src="images/renew-icon.png" alt="Renew" /></a></td>
        <td class="bodylistAct"><a class="view" href='user.php?act=viewAds&id=<?php echo $Ads["id"]; ?>' title="Preview"><img class="act" src="images/preview.png" alt="Preview" /></a></td>
        <td class="bodylistAct"><a href='user.php?act=editAds&id=<?php echo $Ads["id"]; ?>' title="Edit"><img class="act" src="images/edit.png" alt="Edit" /></a></td>
        <td class="bodylistAct"><a class="delete" href="user.php?act=delAds&id=<?php echo $Ads["id"]; ?>" onclick="return confirm('Are you sure you want to delete it?');" title="DELETE"><img class="act" src="images/delete.png" alt="DELETE" /></a></td>
  	  </tr>
  	<?php 
		}
	} else {
	?>
      <tr>
      	<td colspan="9" style="border-bottom:1px solid #CCCCCC"><?php echo $lang['List_No_Entries']; ?></td>
      </tr>
    <?php	
	}
	?>
    
	<?php
    if ($pages>0) {
    ?>
  	  <tr>
      	<td colspan="9" class="bottomlist"><div class='paging'><?php echo $lang['List_Page']; ?> </div>
		<?php
        for($i=1;$i<=$pages;$i++){ 
            if($i == $pageNum ) echo "<div class='paging'>" .$i. "</div>";
            else echo "<a href='user.php?act=ads&p=".$i."&search=".$_REQUEST["search"]."&amp;orderBy=".$_REQUEST["orderBy"]."&amp;orderType=".$_REQUEST["orderType"]."' class='paging'>".$i."</a>"; 
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
} elseif ($_REQUEST["act"]=='newAds') { 
	$sql = "SELECT * FROM ".$TABLE["Options"];
	$sql_result = sql_result($sql);
	$Options = mysqli_fetch_assoc($sql_result);
	$OptionsVis = unserialize($Options['visual']);
?>
	<form action="user.php" method="post" name="AdsForm" enctype="multipart/form-data">
  	<input type="hidden" name="act" value="addAds" />
  	<div class="pageDescr"><?php echo $lang['Create_Listing_Dashboard']; ?></div>
	<table border="0" cellspacing="0" cellpadding="8" class="fieldTables">
      <tr>
      	<td colspan="2" valign="top" class="headlist"><?php echo $lang['Create_Listing_Header']; ?></td>
      </tr>
      
      <tr>
      	<td class="formLeft"><?php echo $lang['Create_Listing_Status']; ?></td>
      	<td class="formRight">
            <?php if ($Options["ads_approve"]=='true') { echo $lang['List_Waiting']; } else { echo $lang['List_Online']; } ?>
      	</td>
      </tr>
      
      <tr>
        <td colspan="2"><div class="border_bottom"></div></td>
      </tr> 
      
      <tr>
        <td class="formLeft"><?php echo $lang['Create_Listing_Title']; ?> </td>
        <td class="formRight"><input class="input_post" type="text" name="title" maxlength="250" value="<?php if(isset($_REQUEST["title"])) echo $_REQUEST["title"]; ?>" /></td>
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
        <td class="formLeft"><?php echo $lang['Create_Listing_Price']; ?></td>
        <td class="formRight"><input type="text" name="price" size="12" onkeypress='return isNumberKey(event);' value="<?php if(isset($_REQUEST["price"])) echo $_REQUEST["price"]; ?>" /> <?php echo $CurrAbr[$Options["currency"]]; ?> </td>
      </tr>
      
      <tr>
        <td class="formLeft" valign="top"><?php echo $lang['Create_Listing_Description']; ?></td>
        <td class="formRight"><textarea class="post_text" name="description" rows="8" onKeyDown="limitText(this,<?php echo ReadDB($Options["char_limit"]);?>);" onKeyUp="limitText(this,<?php echo ReadDB($Options["char_limit"]);?>);" onclick="limitText(this,<?php echo ReadDB($Options["char_limit"]);?>);" onmousemove="limitText(this,<?php echo ReadDB($Options["char_limit"]);?>);"><?php if(isset($_REQUEST["description"])) echo $_REQUEST["description"]; ?></textarea>
        <div class="limit_chars"><?php echo $lang['Edit_Listing_CharLimit']; ?> <?php echo ReadDB($Options["char_limit"]);?></div>
        </td>
      </tr>
            
      <?php for($i=1; $i<=5; $i++) { ?>      
      <tr>
        <td class="formLeft"><?php echo $lang['Create_Listing_Image'.$i]; ?></td>
        <td class="formRight"><input type="file" name="image<?php echo $i; ?>" size="80" /> <sub><?php echo $lang['Create_Listing_Limit_Mb']; ?> </sub></td>
      </tr> 
      <?php } ?> 
      
      <tr>
        <td colspan="2"><div class="border_bottom"></div></td>
      </tr> 
      
      <tr>
        <td class="formLeft"><?php echo $lang['Create_Listing_Email']; ?></td>
        <td class="formRight"><?php echo ReadDB($User["user_email"]) ?></td>
      </tr>
        
      <tr>
        <td class="formLeft"><?php echo $lang['Create_Listing_Name']; ?></td>
        <td class="formRight"><input class="input_details" type="text" name="name" maxlength="250" value="<?php if(trim($User["user_name"]!='')) echo ReadDB($User["user_name"]) ?>" /></td>
      </tr>
           
      <tr>
        <td class="formLeft"><?php echo $lang['Create_Listing_Address']; ?></td>
        <td class="formRight"><input class="input_details" type="text" name="address" maxlength="250" value="<?php if(trim($User["user_address"]!='')) echo ReadDB($User["user_address"]) ?>" /></td>
      </tr>    
      <tr>
        <td class="formLeft"><?php echo $lang['Create_Listing_Location']; ?></td>
        <td class="formRight"><input class="input_details" type="text" name="location" maxlength="250" value="<?php if(trim($User["user_location"]!='')) echo ReadDB($User["user_location"]) ?>" /></td>
      </tr>
      <tr>
        <td class="formLeft"><?php echo $lang['Create_Listing_Phone']; ?></td>
        <td class="formRight"><input class="input_details" type="text" name="phone" maxlength="250" value="<?php if(trim($User["user_phone"]!='')) echo ReadDB($User["user_phone"]) ?>" /></td>
      </tr>  
      <tr>
        <td class="formLeft"><?php echo $lang['Create_Listing_Website']; ?></td>
        <td class="formRight"><input class="input_details" type="text" name="website" maxlength="250" value="<?php if(trim($User["user_url"]!='')) echo ReadDB($User["user_url"]) ?>" /></td>
      </tr> 
      
      <tr>
        <td colspan="2"><div class="border_bottom"></div></td>
      </tr>    
      <tr>
        <td class="formLeft"><?php echo $lang['Create_Listing_EDate']; ?></td>
        <td class="formRight"><?php echo date($OptionsVis["date_format"], mktime(date("H"), date("i"), date("s"), date("m"), date("d")+$Options['expire_days'], date("Y"))); ?> </td>
      </tr>
           
      <tr>
        <td>&nbsp;</td>
        <td class="formRight"><input name="submit" type="submit" value="<?php echo $lang['Create_Listing_button'];?>" class="submitButton" onclick="return checkAds(this.form)" /></td>
      </tr>
  	</table>
	</form>
    

<?php 
} elseif ($_REQUEST["act"]=='editAds') {
	$sql = "SELECT * FROM ".$TABLE["Options"];
	$sql_result = sql_result($sql);
	$Options = mysqli_fetch_assoc($sql_result);
	$OptionsVis = unserialize($Options['visual']);
	
	$_REQUEST["id"]= (int) SafetyDB($_REQUEST["id"]);
	$sql = "SELECT * FROM ".$TABLE["Ads"]." WHERE id='".$_REQUEST["id"]."'";
	$sql_result = sql_result($sql);
	$Ads = mysqli_fetch_assoc($sql_result);
?>
	<form action="user.php" method="post" name="form" enctype="multipart/form-data">
  	<input type="hidden" name="act" value="updateAds" />
  	<input type="hidden" name="id" value="<?php echo $Ads["id"]; ?>" />
  	<div class="pageDescr"><?php echo $lang['Edit_Listing_Dashboard']; ?></div>
	<table border="0" cellspacing="0" cellpadding="8" class="fieldTables">
      <tr>
      	<td colspan="2" valign="top" class="headlist"><?php echo $lang['Edit_Listing_Header']; ?></td>
      </tr> 
      
      <tr>
      	<td class="formLeft"><?php echo $lang['Create_Listing_Status']; ?></td>
      	<td class="formRight">
            <?php if($Ads["status"]=="Online"){ echo $lang['List_Online'];} elseif($Ads["status"]=="Waiting") { echo $lang['List_Waiting'];} else { echo $lang['List_Expired'];} ?>
      	</td>
      </tr>
      
      <tr>
        <td colspan="2"><div class="border_bottom"></div></td>
      </tr> 
      
      <tr>
        <td class="formLeft"><?php echo $lang['Edit_Listing_Title']; ?></td>
        <td class="formRight"><input class="input_post" type="text" name="title"  maxlength="250" value="<?php echo ReadDB($Ads["title"]); ?>" /></td>
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
        <td class="formLeft"><?php echo $lang['Edit_Listing_Price']; ?></td>
        <td class="formRight"><input type="text" name="price" size="12" value="<?php echo ReadDB($Ads["price"]); ?>" onkeypress='return isNumberKey(event);' /> <?php echo $CurrAbr[$Options["currency"]]; ?> </td>
      </tr> 
      
      <tr>
        <td class="formLeft" valign="top"><?php echo $lang['Edit_Listing_Description']; ?></td>
        <td class="formRight"><textarea class="post_text" name="description" rows="8" onKeyDown="limitText(this,<?php echo ReadDB($Options["char_limit"]);?>);" onKeyUp="limitText(this,<?php echo ReadDB($Options["char_limit"]);?>);" onclick="limitText(this,<?php echo ReadDB($Options["char_limit"]);?>);" onmousemove="limitText(this,<?php echo ReadDB($Options["char_limit"]);?>);"><?php echo ReadDB($Ads["description"]); ?></textarea>
        <div class="limit_chars"><?php echo $lang['Edit_Listing_CharLimit']; ?> <?php echo ReadDB($Options["char_limit"]);?></div>
        </td>
      </tr> 
                       
      <?php for($i=1; $i<=5; $i++) { ?>                    
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
        <td class="formLeft"><?php echo $lang['Edit_Listing_Email']; ?></td>
        <td class="formRight"><?php echo ReadDB($User["user_email"]) ?></td>
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
        <td class="formRight"><?php echo date($OptionsVis["date_format"],strtotime($Ads["publish_date"])); ?> </td>
      </tr>
      <tr>
        <td class="formLeft"><?php echo $lang['Edit_Listing_EDate']; ?></td>
        <td class="formRight"><?php echo date($OptionsVis["date_format"],strtotime($Ads["expire_date"])); ?> </td>
      </tr>
      
      <tr>
        <td>&nbsp;</td>
        <td class="formRight">
        	<input name="submit" type="submit" value="<?php echo $lang['Edit_Listing_button']; ?>" class="submitButton" onclick="return checkAds(this.form)" />
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

	<div style="clear:both;padding-left:40px;padding-top:10px;padding-bottom:10px;"><a href="user.php?act=editAds&id=<?php echo ReadDB($Ads['id']); ?>"><?php echo $lang['Preview_Edit_Item']; ?></a></div>
    
	<div style="font-family:<?php echo $OptionsVis["gen_font_family"];?>; font-size:<?php echo $OptionsVis["gen_font_size"];?>;margin:0 auto;width:<?php echo $OptionsVis["gen_width"];?>px; color:<?php echo $OptionsVis["gen_font_color"];?>;line-height:<?php echo $OptionsVis["gen_line_height"];?>;">
    
    
	<?php if($OptionsLang["Back_to_home"]!='') { ?>
    <div style="text-align:<?php echo $OptionsVis["link_align"]; ?>">
    	<a href="user.php?act=ads" style='font-weight:<?php echo $OptionsVis["link_font_weight"]; ?>;color:<?php echo $OptionsVis["link_color"]; ?>;font-size:<?php echo $OptionsVis["link_font_size"]; ?>;text-decoration:underline'><?php echo $OptionsLang["Back_to_home"]; ?></a>
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
	
	$orderByArr = array("title", "publish_date", "cat_id", "reviews");
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
	
	$sqlOnline   = "SELECT id FROM ".$TABLE["Ads"]." WHERE status='Expired' AND user_id='".$UserId."'";
	$sql_resultOnline = sql_result($sqlOnline);
	$AdsExpired = mysqli_num_rows($sql_resultOnline);
?>
	<div class="pageDescr"><?php echo $lang['Expired_Dashboard1']; ?> <strong style="font-size:16px"><?php echo $AdsExpired; ?></strong> <?php echo $lang['Expired_Dashboard2']; ?></div>
    
	<table border="0" cellspacing="0" cellpadding="8" class="allTables">
  	  <tr>
        <td class="headlist"><a href="user.php?act=exads&orderType=<?php echo $norderType; ?>&search=<?php echo urlencode($_REQUEST["search"]); ?>&orderBy=title&p=<?php echo $_REQUEST['p']; ?>"><?php echo $lang['Expired_Title']; ?></a></td>
        <td width="18%" class="headlist"><a href="user.php?act=exads&orderType=<?php echo $norderType; ?>&search=<?php echo urlencode($_REQUEST["search"]); ?>&orderBy=publish_date&p=<?php echo $_REQUEST['p']; ?>"><?php echo $lang['Expired_Date_Activity']; ?></a></td>
        <td width="12%" class="headlist"><?php echo $lang['Expired_Status']; ?></td>
        <td width="16%" class="headlist"><a href="user.php?act=exads&orderType=<?php echo $norderType; ?>&search=<?php echo urlencode($_REQUEST["search"]); ?>&orderBy=cat_id&p=<?php echo $_REQUEST['p']; ?>"><?php echo $lang['Expired_Category']; ?></a></td>
        <td width="8%" class="headlist"><a href="user.php?act=exads&orderType=<?php echo $norderType; ?>&search=<?php echo urlencode($_REQUEST["search"]); ?>&orderBy=reviews&p=<?php echo $_REQUEST['p']; ?>"><?php echo $lang['Expired_Views']; ?></a></td>
        <td class="headlist" colspan="3">&nbsp;</td>
  	  </tr>
      
  	<?php 
	$sql   = "SELECT count(*) as total FROM ".$TABLE["Ads"]." WHERE user_id='".$UserId."' AND status='Expired'";
	$sql_result = sql_result($sql);
	$row   = mysqli_fetch_array($sql_result);
	$count = $row["total"];
	$pages = ceil($count/30);

	$sql = "SELECT * FROM ".$TABLE["Ads"]." WHERE user_id='".$UserId."' AND status='Expired' 
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
        <td class="bodylist"><?php if(ReadDB($Ads["status"])=='Expired') echo $lang['List_Expired']; ?></td>        
        <td class="bodylist">
        	<?php 
			$sqlCat = "SELECT * FROM ".$TABLE["Categories"]." WHERE id='".$Ads["cat_id"]."'";
			$sql_resultCat = sql_result($sqlCat);
			$Cat = mysqli_fetch_assoc($sql_resultCat);	
			if($Cat["id"]>0) echo ReadDB($Cat["cat_name"]); else echo "------"; ?>
        </td>
        <td class="bodylist"><?php if($Ads["reviews"]=='') echo "0"; else echo $Ads["reviews"]; ?></td>
		<td class="bodylistAct"><a class="view" href='user.php?act=renewEx&id=<?php echo $Ads["id"]; ?>'  onclick="return confirm('Are you sure you want to renew with another period of <?php echo $Options["expire_days"]; ?> days?');" title="Renew"><img class="act" src="images/renew-icon.png" alt="Renew" /></a></td>
        <td class="bodylistAct"><a href='user.php?act=editAds&id=<?php echo $Ads["id"]; ?>' title="Edit"><img class="act" src="images/edit.png" alt="Edit" /></a></td>
        <td class="bodylistAct"><a class="delete" href="user.php?act=delAds&id=<?php echo $Ads["id"]; ?>" onclick="return confirm('Are you sure you want to delete it?');" title="DELETE"><img class="act" src="images/delete.png" alt="DELETE" /></a></td>
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
            else echo "<a href='user.php?act=exads&p=".$i."&search=".$_REQUEST["search"]."&amp;orderBy=".$_REQUEST["orderBy"]."&amp;orderType=".$_REQUEST["orderType"]."' class='paging'>".$i."</a>"; 
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
} elseif ($_REQUEST["act"]=='profile') {
	$sql = "SELECT * FROM ".$TABLE["Options"];
	$sql_result = sql_result($sql);
	$Options = mysqli_fetch_assoc($sql_result);
	
	$sql = "SELECT * FROM ".$TABLE["Users"]." WHERE id='".$UserId."'";
	$sql_result = sql_result($sql);
	$User = mysqli_fetch_assoc($sql_result);	
?>
	
    <div class="paddingtop"></div>
    
    <form action="user.php" method="post" name="form">
	<input type="hidden" name="act" value="updateProfile" />
    <div class="pageDescr"><?php echo $lang['Edit_Profile_Dashboard']; ?></div>
    <table border="0" cellspacing="0" cellpadding="8" class="allTables">
      <tr>
        <td colspan="3" class="headlist"><?php echo $lang['Edit_Profile_Header']; ?></td>
      </tr>
      <tr>
        <td class="profileLeft"><?php echo $lang['Edit_Profile_Email']; ?><br />
          <span style="font-size:11px;font-style:italic"><?php echo $lang['Edit_Profile_Email_info']; ?></span></td>
        <td class="profileRight"><?php echo ReadDB($User["user_email"]); ?> </td>
      </tr>
      <tr>
        <td class="profileLeft"><?php echo $lang['Edit_Profile_Pass']; ?> <br />
          <span style="font-size:11px;font-style:italic"><?php echo $lang['Edit_Profile_Pass_info']; ?></span></td>
        <td class="profileRight"><input class="input_details" name="user_password" type="text" value="<?php echo ReadDB($User["user_password"]); ?>" /></td>
      </tr>
      <tr>
        <td class="profileLeft"><?php echo $lang['Edit_Profile_RegDate']; ?></td>
        <td class="profileRight"><?php echo ReadDB($User["reg_date"]); ?> </td>
      </tr>
      <tr>
        <td class="profileLeft"><?php echo $lang['Edit_Profile_Name']; ?> </td>
        <td class="profileRight"><input class="input_details" name="user_name" type="text" value="<?php echo ReadDB($User["user_name"]); ?>" /></td>
      </tr>
      <tr>
        <td class="profileLeft"><?php echo $lang['Edit_Profile_Address']; ?> </td>
        <td class="profileRight"><input class="input_details" name="user_address" type="text" value="<?php echo ReadDB($User["user_address"]); ?>" /></td>
      </tr> 
      <tr>
        <td class="profileLeft"><?php echo $lang['Edit_Profile_Location']; ?> </td>
        <td class="profileRight"><input class="input_details" name="user_location" type="text" value="<?php echo ReadDB($User["user_location"]); ?>" /></td>
      </tr> 
      <tr>
        <td class="profileLeft"><?php echo $lang['Edit_Profile_Phone']; ?> </td>
        <td class="profileRight"><input class="input_details" name="user_phone" type="text" value="<?php echo ReadDB($User["user_phone"]); ?>" /></td>
      </tr> 
      <tr>
        <td class="profileLeft"><?php echo $lang['Edit_Profile_Website']; ?> </td>
        <td class="profileRight"><input class="input_details" name="user_url" type="text" value="<?php echo ReadDB($User["user_url"]); ?>" /></td>
      </tr> 
      <tr>
        <td>&nbsp;</td>
        <td class="profileRight"><input name="submit1" type="submit" value="<?php echo $lang['Edit_Profile_button']; ?>" class="submitButton" /></td>
      </tr>      
    </table>    
	</form>


<?php
}
?>
</div>


<?php 
} elseif($_REQUEST["act"]=='register') { ///// Register Form /////
	$sql = "SELECT * FROM ".$TABLE["Options"];
	$sql_result = sql_result($sql);
	$Options = mysqli_fetch_assoc($sql_result);
?>
<div class="admin_wrapper login_wrapper">
	<div class="login_head"><?php echo $lang['USER_REGISTRATION']; ?></div>
	
	<div class="login_sub"><?php echo $lang['Reg_context']; ?></div>
    <form action="user.php" method="post">
    <input type="hidden" name="act" value="send_registration">
    <table border="0" cellspacing="0" cellpadding="0" class="loginTable">
      <tr>
        <td class="userpass"><?php echo $lang['Reg_Useremail']; ?> </td>
        <td class="userpassfield">
      		<input name="address" type="text" style="height:0;width:0;font-size:0;border:0;margin:0;padding:0;" value="" />
        	<input name="user_email" type="text" class="loginfield" style="float:left;" value="<?php echo $_REQUEST['user_email']; ?>" /> 
			<?php if(isset($reqMessEmail) and $reqMessEmail!='') {?><div class="attentionMessage"><?php echo $reqMessEmail; ?></div><?php } ?>
        </td>
      </tr>
      <tr>
        <td class="userpass"><?php echo $lang['Reg_Password']; ?> </td>
        <td class="userpassfield">
        	<input name="user_password" type="password" class="loginfield" style="float:left;" value="<?php echo $_REQUEST['user_password']; ?>" /> 
			<?php if(isset($reqMessPass) and $reqMessPass!='') {?><div class="attentionMessage"><?php echo $reqMessPass; ?></div><?php } ?>
        </td>
      </tr>
      <tr>
        <td class="userpass"><?php echo $lang['Reg_Password_conf']; ?> </td>
        <td class="userpassfield"><input name="user_password2" type="password" class="loginfield" style="float:left;" /> <?php if(isset($reqMessPass2) and $reqMessPass2!='') {?><div class="attentionMessage"><?php echo $reqMessPass2; ?></div><?php } ?></td>
      </tr>
      <?php 
	  if($Options['captcha']!='nocap') { // if the option is set to no Captcha
	  ?> 
      <tr>
        <td class="userpass"><?php echo $lang['Reg_Captcha_code']; ?> </td>
        <td class="userpassfield">
            <?php 
			if($Options['captcha']=='recap') { // if the option is set to phpcaptcha
                 	$options = array();
                    $options['input_text'] = "Type the captcha"; // change placeholder
					//$options['namespace']  = 'EventScriptPHP20';
					//$options['show_text_input'] = false; // change placeholder
					//$options['disable_flash_fallback'] = false; // allow flash fallback
					
                    echo "<div id='captcha_container_1'>\n";
                    echo Securimage::getCaptchaHtml($options);
                    echo "\n</div>\n"; 

			} elseif($Options['captcha']=='capmath') { ?> 
                <img src="<?php echo $CONFIG["folder_name"]; ?>captchamath.php" id="captcha" style="display:block;float:left;" />
          		<div style="float:left;padding-top:9px;padding-left:3px;padding-right:3px;font-size:20px;color:#666;font-weight:bold;"> = </div>
				<input type="text" name="string" style="width:40px;display:block;float:left;margin-top:0px;height:24px;font-size:17px; text-align:center;" maxlength="6" /> 
			<?php 
			} elseif($Options['captcha']=='cap') { ?>
            	<img src="<?php echo $CONFIG["folder_name"]; ?>captcha.php" style="display:block;float:left;padding-right:10px;" />
                <input type="text" name="string" style="width:66px;display:block;float:left;margin-top:6px;" /> 
            <?php 
			} else { ?>
            	<img src="<?php echo $CONFIG["folder_name"]; ?>captchasimple.php" style="display:block;float:left;padding-right:10px;" />
                <input type="text" name="string" style="width:66px;display:block;float:left;margin-top:6px;" /> 
            <?php 
			} ?>   
                   
            <?php if(isset($messageCaptcha) and $messageCaptcha!='') {?><div class="attentionMessage"><?php echo $messageCaptcha; ?></div><?php } ?>
        </td>
      </tr>
      <?php 
	  }
	  ?>
      <tr>
        <td class="userpass">&nbsp;</td>
        <td class="userpassfield"><input type="submit" name="button" value="<?php echo $lang['Reg_Register_button']; ?>" class="loginButon" /></td>
      </tr>
      <tr>
        <td class="userpass">&nbsp;</td>
        <td class="userpassfield">
        	<p><a href="user.php"><?php echo $lang['Login']; ?> »</a></p>
			<p><a href="user.php?act=forgot_pass"><?php echo $lang['Forgot_Password']; ?> »</a></p>
        </td>
      </tr>
    </table>
    </form>
</div>


<?php 
} elseif($_REQUEST["act"]=='forgot_pass') { ///// Forgot Password Form /////
?>
<div class="admin_wrapper login_wrapper">
	<div class="login_head"><?php echo $lang['USER_FORGOT_PASS']; ?></div>
	
	<div class="login_sub"><?php echo $lang['Forg_context']; ?></div>
    <form action="user.php" method="post">
    <input type="hidden" name="act" value="send_pass">
    <table border="0" cellspacing="0" cellpadding="0" class="loginTable">
      <tr>
        <td class="userpass"><?php echo $lang['Forg_Useremail']; ?> </td>
        <td class="userpassfield">
        	<input name="user_email" type="text" class="loginfield" style="float:left;" value="<?php echo $_REQUEST['user_email']; ?>" /> 
			<?php if(isset($reqMessEmail) and $reqMessEmail!='') {?><div class="attentionMessage"><?php echo $reqMessEmail; ?></div><?php } ?>
        </td>
      </tr>

      <tr>
        <td class="userpass">&nbsp;</td>
        <td class="userpassfield"><input type="submit" name="button" value="<?php echo $lang['Forg_Send_button']; ?>" class="loginButon" /></td>
      </tr>
      <tr>
        <td class="userpass">&nbsp;</td>
        <td class="userpassfield">
        	<p><a href="user.php"><?php echo $lang['Login']; ?> »</a></p>
			<p><a href="user.php?act=register"><?php echo $lang['Register']; ?> »</a></p>
        </td>
      </tr>
    </table>
    </form>
</div>


<?php 
} else { ///// Login Form /////
?>
<div class="admin_wrapper login_wrapper">
	<div class="login_head"><?php echo $lang['USER_LOGIN']; ?></div>
	
	<div class="login_sub"><?php echo $lang['Login_context']; ?></div>
    <?php if(isset($regMessSuccess) and $regMessSuccess!='') {?><div class="MessageSuccess"><?php echo $regMessSuccess; ?></div><?php } ?>
    <form action="user.php" method="post">
    <input type="hidden" name="act" value="login">
    <table border="0" cellspacing="0" cellpadding="0" class="loginTable">
      <tr>
        <td class="userpass"><?php echo $lang['Useremail']; ?> </td>
        <td class="userpassfield"><input name="user_email" type="text" class="loginfield" style="float:left;" value="<?php echo $_REQUEST['user_email']; ?>" /> <?php if(isset($logMessage) and $logMessage!='') {?><div class="logMessage"><?php echo $logMessage; ?></div><?php } ?></td>
      </tr>
      <tr>
        <td class="userpass"><?php echo $lang['Password']; ?> </td>
        <td class="userpassfield"><input name="user_password" type="password" class="loginfield" /></td>
      </tr>
      
      <tr>
        <td class="userpass">&nbsp;</td>
        <td class="userpassfield"><input type="submit" name="button" value="<?php echo $lang['Login_button']; ?>" class="loginButon" /></td>
      </tr>
      <tr>
        <td class="userpass">&nbsp;</td>
        <td class="userpassfield">
        	<p><a href="user.php?act=register"><?php echo $lang['Register']; ?> »</a></p>
			<p><a href="user.php?act=forgot_pass"><?php echo $lang['Forgot_Password']; ?> »</a></p>
        </td>
      </tr>
    </table>
    </form>
</div>
<?php 
}
?>

<div class="clearfooter"></div>
<!--<div class="divProfiAnts"> <a class="footerlink" href="http://simplephpscripts.com" target="_blank">Product of ProfiAnts - SimplePHPscripts.com</a></div> -->

</body>
</html>