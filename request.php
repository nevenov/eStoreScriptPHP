<?php 
session_start();
$installed = '';
if(!isset($configs_are_set)) {
	include( dirname(__FILE__). "/configs.php");
}
//$thisPage = $_SERVER['PHP_SELF'];
$phpSelf = filter_input(INPUT_SERVER, 'PHP_SELF', FILTER_SANITIZE_URL);
$thisPage = $phpSelf;

$sql = "SELECT * FROM ".$TABLE["Options"];
$sql_result = sql_result($sql);
$Options = mysqli_fetch_assoc($sql_result);
$OptionsVis = unserialize($Options['visual']);
$OptionsVisC = unserialize($Options['visual_comm']);
$OptionsLang = unserialize($Options['language']);

$sql = "SELECT * FROM ".$TABLE["Ads"]." WHERE status='Online' AND id='".SafetyDB($_REQUEST["id"])."'";
$sql_result = sql_result($sql);
$Ads = mysqli_fetch_assoc($sql_result);

$error='';

if (isset($_REQUEST["act"]) and $_REQUEST["act"]=='send') {  

/////////////////////////////////////////////////
////// checking for correct captcha starts //////
	if($Options['captcha']=='nocap') { // if the option is set to no Captcha
		$testvariable = true;	// test variable is set to true
	} else {
		$testvariable = false;	// test variable is set to false
	}
		
	if($Options['captcha']=='recap') { // if the option is set to php captcha		
		
		if (isset($_POST['captcha_code']) and $securimage->check($_POST['captcha_code']) == true) { // test variable is set	to true
			$testvariable = true; // the captcha_code was correct	
		} else {		
			$message = $OptionsLang['Incorrect_anti_spam_code'];
		} 
		
	} elseif($Options['captcha']=='capmath' or $Options['captcha']=='cap' or $Options['captcha']=='vsc') { // if is set to math or simple captcha option
	
		if (preg_match('/^'.$_SESSION['key'].'$/i', $_REQUEST['string'])) { // test variable is set	to true	
			$testvariable = true;			
		} else {		
			$message = $OptionsLang['Incorrect_anti_spam_code'];
		}
	}
////// checking for correct captcha ends //////
///////////////////////////////////////////////


  if ($testvariable==true) { // if test variable is set to true, then go to update database and send emails
		
		$mailheader  = "From: ".ReadDB($Options['email'])."\n";
		$mailheader .= "Reply-To: ".ReadDB($Ads['email'])."\n";
		$mailheader .= "MIME-Version: 1.0\n";
		$mailheader .= "Content-type: text/html; charset=UTF-8\r\n"; 
		
		$msg  = "<strong>" . ReadDB($Ads["title"]) . "</strong> <br />"; 
		$msg .= $OptionsLang['Listed'] . ": " . date($OptionsVis["date_format"], strtotime($Ads["publish_date"])) . "<br />";
		$msg .= $OptionsLang['Price'] . ": " . CurrFormat($Options["currency"], $CurrSign[$Options["currency"]], ReadDB($Ads["price"])) . "<br />";
		$msg .= ReadDB($Ads["description"]) . " <br /><br />"; 
		$msg .= "<strong>" . $OptionsLang['Request'] . "</strong><br />". nl2br(stripslashes($_REQUEST["request"])) . "<br /><br />";
		$msg .= stripslashes($_REQUEST["name"]) . " <br />";
		$msg .= stripslashes($_REQUEST["email"]) . " <br /><br />";
		
		
		//mail(stripslashes($Ads["email"]), '=?UTF-8?B?'.base64_encode($OptionsLang['Email_request_subject']).'?=', $msg, $mailheader) or die ("Failed to send email");
		
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
			
			$mail->addAddress($Ads["email"], $Ads["email"]);		// Add a recipient
			
			$mail->AddReplyTo(ReadDB($_REQUEST["email"]), ReadDB($_REQUEST["email"]));// Add reply To email
			
			$mail->Subject = ReadDB($OptionsLang["Email_request_subject"]); // Set email subject	
	
			$mail->MsgHTML($msg);
			//$mail->Body    = $Message_body;
			$mail->AltBody = strip_tags($msg);
			
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
			//Set an alternative reply-to address
			$mail->addReplyTo(ReadDB($_REQUEST["email"]), ReadDB($_REQUEST["email"]));
			//Set who the message is to be sent to
			$mail->addAddress($Ads["email"], $Ads["email"]);
			$mail->CharSet = "UTF-8"; // force setting charset UTF-8
			//Set the subject line
			$mail->Subject = ReadDB($OptionsLang["Email_request_subject"]); // Set email subject
			//Read an HTML message body from an external file, convert referenced images to embedded,
			//convert HTML into a basic plain-text alternative body
			$mail->msgHTML($msg);
			//Replace the plain text body with one created manually
			$mail->AltBody = strip_tags($msg);
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
			   
		
		
		$message = $OptionsLang['Request_successfully_sent'];
		
		unset($_REQUEST["request"]);
		unset($_REQUEST["name"]);
		unset($_REQUEST["email"]);

	} else {		
	  $message = $OptionsLang['Incorrect_anti_spam_code'];
	}
}

?><!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>Send email to publisher</title>
<script type="text/javascript">
 
function checkForm(form){
	var chekmail = /([0-9a-zA-Z\.-_]+)@([0-9a-zA-Z\.-_]+)/;

	var name, email, request, isOk = true;
	<?php if($Options['captcha']!='recap' and $Options['captcha']!='nocap') { // if the option is not set to phpcaptcha or no Captcha ?>
	var string;
	<?php } ?>
	var message = "";
	
	message = "<?php echo $OptionsLang['Fill_all_the_required_fields']; ?>";
	
	name	= form.name.value;	
	email	= form.email.value;	
	request	= form.request.value;	
	<?php if($Options['captcha']!='recap' and $Options['captcha']!='nocap') { // if the option is NOT set to phpcaptcha or no Captcha ?>
	string	= form.string.value;
	<?php } ?>

	if (name.length == 0){
		form.name.focus();
		isOk=false;
	}
	else if (email.length < 5){
		form.email.focus();
		isOk=false;
	}	
	else if (email.length >= 5 && email.match(chekmail) == null){
		message ="<?php echo $OptionsLang['Incorrect_mail_address']; ?>";
		form.email.focus();
	   	isOk=false;
	}
	else if (request.length == 0){
		form.request.focus();
		isOk=false;
	}
	<?php if($Options['captcha']!='recap' and $Options['captcha']!='nocap') { // if the option is NOT set to phpcaptcha or no Captcha ?>
	else if (string.length==0){
		message ="<?php echo $OptionsLang["field_code"]; ?>";
		form.string.focus();
		isOk=false;
	}
	<?php } ?>

	if (!isOk){			   
	   	alert(message);
	   	return isOk;
	} else {
		return isOk;
	}
}

</script>
</head>

<body>

<div style="padding-top:8px; font-family:<?php echo $OptionsVis["gen_font_family"];?>; font-size:<?php echo $OptionsVis["gen_font_size"];?>; color:<?php echo $OptionsVis["gen_font_color"];?>; line-height:<?php echo $OptionsVis["gen_line_height"];?>;">
  
  <div style="padding-bottom:8px; text-align:center; font-weight:bold;"><?php echo $OptionsLang["Email_to_publisher"]; ?></div>
        
  <form action="<?php echo $thisPage; ?>?id=<?php echo $_REQUEST["id"]; ?>" name="fоrm" method="post" style="margin:0; padding:0;">
    <input type="hidden" name="act" value="send" />
    <table width="100%" border="0" cellspacing="0" cellpadding="4">
      <?php if(isset($message)) {?>
      <tr>
        <td colspan="2" style="color:#FF0000; font-weight:bold"><?php echo $message; ?></td>
      </tr>
      <?php } ?>
      <tr>
        <td align="left" width="25%"><?php echo $OptionsLang['Your_Name']; ?>*: </td>
        <td align="left"><input name="name" type="text" maxlength="60" style="width:70%" value="<?php if(isset($_REQUEST["name"])) echo $_REQUEST["name"]; ?>" /></td>
      </tr>
      <tr>
        <td align="left"><?php echo $OptionsLang['Your_Email']; ?>*: </td>
        <td align="left"><input name="email" type="text" maxlength="60" style="width:70%" value="<?php if(isset($_REQUEST["email"])) echo $_REQUEST["email"]; ?>" /></td>
      </tr>
      <tr>
        <td align="left"><?php echo $OptionsLang['Request']; ?>*: </td>
        <td align="left"><textarea name="request" rows="6" cols="" style="width:95%"><?php if(isset($_REQUEST["request"])) echo $_REQUEST["request"]; ?></textarea></td>
      </tr>
      <?php 
	  if($Options['captcha']!='nocap') { // if the option is set to no Captcha
	  ?> 
      <tr>
        <td align="left"><?php echo $OptionsLang['Anti_spam_code']; ?>*:</td>
        <td align="left">
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
				<input type="text" name="string" style="width:40px;display:block;float:left;margin-top:4px;height:28px;font-size:17px; text-align:center;" maxlength="6" /> 
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
        </td>
      </tr>
      <?php 
	  }
	  ?>
      <tr>
        <td align="left" colspan="2">* - <?php echo $OptionsLang['the_required_fields']; ?></td>
      </tr>
      <tr>
        <td>&nbsp;</td>
        <td align="left"><input name="sendmessage" type="submit" value="<?php echo $OptionsLang['Send_Request']; ?>" onclick="return checkForm(this.form)" /></td>
      </tr>
    </table>
    </form>
    
</div>



</body>
</html>