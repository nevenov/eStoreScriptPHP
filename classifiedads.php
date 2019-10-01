<?php 
$installed = '';
if(!isset($configs_set_cl)) {
	include( dirname(__FILE__). "/configs.php");
}
//$thisPage = $_SERVER['PHP_SELF'];
$phpSelf = filter_input(INPUT_SERVER, 'PHP_SELF', FILTER_SANITIZE_URL);
$thisPage = $phpSelf;

$sql = "SELECT * FROM ".$TABLE["Options"];
$sql_result = sql_result($sql);
$Options = mysqli_fetch_assoc($sql_result);
mysqli_free_result($sql_result);
$OptionsVis = unserialize($Options['visual']);
$OptionsLang = unserialize($Options['language']);

if(isset($_REQUEST["cat_id"]) and $_REQUEST["cat_id"]!='') {
	$_REQUEST["cat_id"] = (int) SafetyDB($_REQUEST["cat_id"]);
}
if(!isset($_REQUEST["search"])) $_REQUEST["search"] = ''; 
if(!isset($_REQUEST["p"])) $_REQUEST["p"] = ''; 
?>
<meta name="viewport" content="width=device-width, initial-scale=1">
<!-- Add jQuery library -->
<script>
window.jQuery || document.write("<script src='<?php echo $CONFIG["full_url"]; ?>include/jquery-1.11.2.min.js'><\/script>");
</script>

<!-- Add mousewheel plugin (this is optional) -->
<script type="text/javascript" src="<?php echo $CONFIG["full_url"]; ?>fancybox/lib/jquery.mousewheel-3.0.6.pack.js"></script>

<!-- Add fancyBox -->
<link rel="stylesheet" href="<?php echo $CONFIG["full_url"]; ?>fancybox/source/jquery.fancybox.css?v=2.1.6" type="text/css" media="screen" />
<script type="text/javascript" src="<?php echo $CONFIG["full_url"]; ?>fancybox/source/jquery.fancybox.pack.js?v=2.1.6"></script>

<script type="text/javascript">
	var $g= jQuery.noConflict();
	$g(document).ready(function() {
		$g(".classif_image").fancybox();
	});
</script>

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

<a name="onads" id="onads"></a>

<div style="background-color:<?php echo $OptionsVis["gen_bgr_color"];?>;">
<div style="font-family:<?php echo $OptionsVis["gen_font_family"];?>; font-size:<?php echo $OptionsVis["gen_font_size"];?>;margin:0 auto;max-width:<?php echo $OptionsVis["gen_width"];?>px; color:<?php echo $OptionsVis["gen_font_color"];?>;line-height:<?php echo $OptionsVis["gen_line_height"];?>;word-wrap:break-word;">


<?php
if (isset($_REQUEST["id"]) and $_REQUEST["id"]>0) {	
	$_REQUEST["id"]= (int) SafetyDB($_REQUEST["id"]);
?>
	<div style="clear: both; height:0px;"></div>
	<?php if(trim($OptionsLang["Back_to_home"])!='') { ?>
    <div style="text-align:<?php echo $OptionsVis["link_align"]; ?>"><a href="<?php echo $thisPage; ?>?cat_id=<?php echo $_REQUEST["cat_id"]; ?>&amp;p=<?php echo $_REQUEST["p"]; ?>&amp;search=<?php echo urlencode($_REQUEST["search"]); ?>#onads" style='font-weight:<?php echo $OptionsVis["link_font_weight"]; ?>;color:<?php echo $OptionsVis["link_color"]; ?>;font-size:<?php echo $OptionsVis["link_font_size"]; ?>;text-decoration:underline'><?php echo $OptionsLang["Back_to_home"]; ?></a></div>    
    <div style="clear:both; padding-bottom:<?php echo $OptionsVis["dist_link_title"];?>;"></div>    
    <?php } ?>

	<?php 
	$sql = "SELECT * FROM ".$TABLE["Ads"]." WHERE status='Online' AND id='".SafetyDB($_REQUEST["id"])."'";
	$sql_result = sql_result($sql);
	if(mysqli_num_rows($sql_result)>0) {	
	  $Ads = mysqli_fetch_assoc($sql_result);
	?>
	
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
            	<a class="classif_image" rel="group" href="<?php echo $CONFIG["full_url"].$CONFIG["upload_folder"].ReadDB($Ads["image".$i]);?>">
        		<img class="image_full" src="<?php echo $CONFIG["full_url"].$CONFIG["upload_folder"].ReadDB($Ads["image".$i]); ?>" alt="<?php echo ReadHTML($Ads["title"]); ?>" style="padding-bottom:6px; padding-top:2px;" />    
                </a>                     
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
    

                <div style="color:<?php echo $OptionsVis["cont_color"];?>; font-family:<?php echo $OptionsVis["cont_font"];?>; font-size:<?php echo $OptionsVis["cont_size"];?>;font-style: <?php echo $OptionsVis["cont_font_style"];?>;text-align:<?php echo $OptionsVis["cont_text_align"];?>;line-height:<?php echo $OptionsVis["cont_line_height"];?>;padding-top:2px;"><?php echo nl2br(ReadHTML($Ads["description"])); ?> </div>
                
                <div style="clear:both; padding-bottom:<?php echo $OptionsVis["dist_date_text"];?>;"></div>
                
                <div style="color:<?php echo $OptionsVis["cont_color"];?>; font-family:<?php echo $OptionsVis["cont_font"];?>; font-size:<?php echo $OptionsVis["cont_size"];?>;font-style: <?php echo $OptionsVis["cont_font_style"];?>;text-align:<?php echo $OptionsVis["cont_text_align"];?>;line-height:<?php echo $OptionsVis["cont_line_height"];?>;padding-top:2px;">
					<!-- <?php if(trim(ReadHTML($Ads["name"]))) {?><?php echo $OptionsLang["Name"]; ?>: <?php echo ReadHTML($Ads["name"]); ?><br /> <?php } ?> -->
                    <!-- <?php if(trim(ReadHTML($Ads["location"]))) {?><?php echo $OptionsLang["Location"]; ?>: <?php echo ReadHTML($Ads["location"]); ?><br /> <?php } ?> -->
                    <a href="javascript: void();" onClick="window.open('<?php echo $CONFIG["full_url"]; ?>request.php?id=<?php echo ReadDB($Ads["id"]); ?>','Send_Email_to_publisher','width=600,height=450')" style='font-weight:<?php echo $OptionsVis["email_font_weight"]; ?>;color:<?php echo $OptionsVis["email_color"]; ?>;font-size:<?php echo $OptionsVis["email_font_size"]; ?>; font-style: <?php echo $OptionsVis["email_font_style"]; ?>;text-decoration:underline'><?php echo $OptionsLang["Email_to_publisher"]; ?></a> 
                    <!-- <?php if(trim(ReadHTML($Ads["phone"]))) {?><br /><?php echo $OptionsLang["Phone"]; ?>: <?php echo ReadHTML($Ads["phone"]); ?> <?php } ?> --> 
                    <!-- <?php if(trim(ReadHTML($Ads["website"]))) {?>
                    <br /><?php echo $OptionsLang["Website"]; ?>: 
					<a href="<?php echo addhttp(ReadDB($Ads["website"])); ?>" rel="nofollow" target="_blank">
						<?php echo ReadHTML($Ads["website"]); ?> 
                    </a>
					<?php } ?>  -->
                </div>
                
                
                
                <div style="clear:both; padding-bottom:<?php echo $OptionsVis["dist_date_price"];?>;"></div>
                
                <div style="color:<?php echo $OptionsVis["price_color"];?>; font-family:<?php echo $OptionsVis["price_font"];?>; font-size:<?php echo $OptionsVis["price_size"];?>;font-weight:<?php echo $OptionsVis["price_font_weight"];?>;font-style: <?php echo $OptionsVis["price_font_style"];?>;text-align:<?php echo $OptionsVis["price_text_align"];?>;">
                    <?php echo $OptionsLang["Price"]; ?> 
                    
                    <?php 
					$salePrice = 0;
					if(strlen(trim($Ads["sale_price"]))>0) {
						$salePrice = 1;
					}
					$shipPrice = 0;
					if(strlen(trim($Ads["shipping"]))>0) {
						$shipPrice = 1;
					}
					?>
                    <?php if($salePrice>0) echo "<strike>"; ?>
						<?php echo CurrFormat($Options["currency"], $CurrSign[$Options["currency"]], ReadDB($Ads["price"])); ?>
                    <?php if($salePrice>0) echo "</strike>"; ?>
                    &nbsp;&nbsp;&nbsp;
                    <span style="color:<?php echo $OptionsVis["date_color"];?>; font-family:<?php echo $OptionsVis["date_font"];?>; font-size:<?php echo $OptionsVis["date_size"];?>;font-weight:<?php echo $OptionsVis["date_font_weight"];?>;font-style: <?php echo $OptionsVis["date_font_style"];?>;text-align:<?php echo $OptionsVis["date_text_align"];?>;">
						<?php echo $OptionsLang["Listed"]; ?> 
						<?php if($salePrice>0) {?>
						<?php echo CurrFormat($Options["currency"], $CurrSign[$Options["currency"]], ReadDB($Ads["sale_price"])); ?>
                        <?php } else {
								echo "n/a";
							  }
						?>
                        &nbsp;&nbsp;&nbsp; 
                        Shipping: 
						<?php if($shipPrice>0) {?>
						<?php echo CurrFormat($Options["currency"], $CurrSign[$Options["currency"]], ReadDB($Ads["shipping"])); ?>
                        <?php } else {
								echo "n/a";
							  }
						?>
                    </span>
                </div>   
                <div style="clear:both; padding-bottom:<?php echo $OptionsVis["dist_price_text"];?>;"></div>
                
                
                <div>
                  <label>
                  	Pay & Reserve Pickup
                    <input type="radio" class="shipping-input" name="add_shipping" id="add_shipping_0" checked onclick="addShipping(this.value)" value="0">
                    (no shipping charge)</label>
                  <br>
                  <?php if($shipPrice>0) {?>
                  <label>
                  	Pay & Ship
                    <input type="radio" class="shipping-input" name="add_shipping" id="add_shipping_1" onclick="addShipping(this.value)" value="<?php echo ReadDB($Ads["shipping"]); ?>">
                    (shipping cost will be added)</label>
                  <br>
                  <?php } ?>
                </div>
                
                
                <?php 
					if(strlen(trim($Ads["sale_price"]))>0) {
						$ppPrice = ReadDB($Ads["sale_price"]);
					} else {
						$ppPrice = ReadDB($Ads["price"]);
					}
				?>
                
                <script>
				
				
                function addShipping(shipping) {
					
					const formatter = new Intl.NumberFormat('en-US', {
					  style: 'currency',
					  currency: "<?php echo $CurrAbr[$Options["currency"]]?>",
					  minimumFractionDigits: 2
					});
					
					var shipping_id = document.getElementById("shipping_id");
					
					var shipping_inputs = document.getElementsByClassName("shipping-input");
					
					var total_price = document.getElementById("total_price");
					
					for(var i = 0; i < shipping_inputs.length; i++) {
						shipping_id.value = shipping;
						var total_formated = formatter.format(parseInt(<?php echo $ppPrice; ?>)+parseInt(shipping));
						total_price.innerHTML = total_formated;
					}
				} 
                </script>
                
                <div>Total Price: <span id="total_price"><?php echo CurrFormat($Options["currency"], $CurrSign[$Options["currency"]], ReadDB($Ads["sale_price"])); ?></span></div>
                
                
                
                <div style="clear:both; padding: 16px 0;">
                	<form action="https://www.sandbox.paypal.com/cgi-bin/webscr" method="post" name="paypalform" id="paypalform">
                        <input type="hidden" name="cmd" value="_xclick" />
                        <input type="hidden" name="charset" value="utf-8" />
                        <input type="hidden" name="business" value="<?php echo ReadDB($Options["paypal_email"]);?>" />
                        <input type="hidden" name="item_name" value="<?php echo ReadHTML($Ads["title"]); ?>" />
                        <input type="hidden" name="item_number" value="<?php echo ReadDB($Ads["id"]); ?>" />
                        <input type="hidden" name="amount" value="<?php echo $ppPrice; ?>" />
                        <input type="hidden" name="custom" value="<?php echo ReadDB($Ads["id"]); ?>">
                        <input type="hidden" id="shipping_id" name="shipping" value="0" />
                        <input type="hidden" name="currency_code" value="<?php echo $CurrAbr[$Options["currency"]]; ?>" />
                        <input type="hidden" name="bn" value="PP-BuyNowBF:btn_buynowCC_LG.gif:NonHosted" />
                        <input type="hidden" name="return" value="<?php echo $CONFIG["full_url"]; ?>preview.php" />
                        <input type="hidden" name="notify_url" value="<?php echo $CONFIG["full_url"]; ?>listener.php?ad_id=<?php echo ReadDB($Ads["id"]); ?>" />
                        <input type="hidden" name="cbt" value="Please return back to our website and download the product" />
                        
                        <input type="image" name="submit" border="0" src="https://www.paypal.com/en_US/i/btn/btn_buynow_LG.gif" alt="PayPal - The safer, easier way to pay online" />
                        <img alt="" border="0" width="1" height="1" src="https://www.paypal.com/en_US/i/scr/pixel.gif" />  
                    </form>
                </div>
                
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
    
    <div style="clear:both"></div>
    
    <?php 
	$sql = "UPDATE ".$TABLE["Ads"]." 
			SET reviews = reviews + 1 
			WHERE id='".$_REQUEST["id"]."'";
	$sql_result = sql_result($sql);
	?>
    
	<?php 
	} // end if mysql num rows 
	?>
    
    
<?php
} else {
?>
	<div style="padding-bottom:<?php echo $OptionsVis["dist_link_title"];?>;">
    	<table width="100%" border="0" cellspacing="0" cellpadding="0">
          <tr>
            <td style="text-align:<?php echo $OptionsVis["link_align"]; ?>; width:50%;">
            	<a href="<?php echo $CONFIG["full_url"]."user.php"; ?>" style='font-weight:<?php echo $OptionsVis["link_font_weight"]; ?>;color:<?php echo $OptionsVis["link_color"]; ?>;font-size:<?php echo $OptionsVis["link_font_size"]; ?>;text-decoration:underline'<?php if($Options["submit_open_mode"]!="_self") echo ' target="_blank"' ?>><?php echo $OptionsLang["Submit_Classified_Ad"]; ?></a>
            </td>
            <td style="text-align:right;">
            	<?php 		
				if(!isset($_REQUEST['cat_id']) or $_REQUEST['cat_id']=='') {
					$sql = "SELECT * FROM ".$TABLE["Categories"]." ORDER BY id ASC LIMIT 0,1";
					$sql_result = sql_result($sql);
					$Cat = mysqli_fetch_assoc($sql_result);
					$_REQUEST['cat_id'] = $Cat['id'];
					
					$sql = "SELECT * FROM ".$TABLE["Ads"]." WHERE status='Online' AND publish_date <= NOW() AND expire_date >= NOW() AND cat_id='".$Options['default_cat']."'";
					$sql_result = sql_result($sql);
					$numrows = mysqli_num_rows($sql_result);
					if($numrows>0 or $Options['default_cat']=="0") {
						$_REQUEST['cat_id'] = $Options['default_cat'];
					}
				}
				
				
				$sql = "SELECT * FROM ".$TABLE["Categories"]." ORDER BY cat_name ASC";
				$sql_result = sql_result($sql);
				if (mysqli_num_rows($sql_result)>0) {
				?>
				<?php echo $OptionsLang["Category"]; ?>:
				<select name="cat_id" onchange="window.location.href='<?php echo $thisPage; ?>?cat_id='+this.value">
                	<?php if($Options['showallads_cat']=="yes") {?>
                	<option value="0"<?php if ($_REQUEST["cat_id"]=="0") echo ' selected="selected"'?>><?php if($OptionsLang["All_Ads"]!="") echo $OptionsLang["All_Ads"]; else echo "ALL";?></option>
                    <?php } ?>
					<?php
					$sql = "SELECT * FROM ".$TABLE["Categories"]." ORDER BY cat_name ASC";
					$sql_result = sql_result($sql);
					while ($Cat = mysqli_fetch_assoc($sql_result)) { ?>
						<option value="<?php echo $Cat["id"]; ?>"<?php if ($Cat["id"]==$_REQUEST["cat_id"]) echo ' selected="selected"'?>><?php echo ReadDB($Cat["cat_name"]); ?></option>
					<?php 
					}
					?>
				</select>
				<?php 
				} 
				?>
            </td>
          </tr>
        </table>    	
    </div>
    

  	<div>  	
    	<table width="100%" border="0" cellpadding="4" cellspacing="0">
          <tr>
          	<td style="padding-left:0;" class="mobilehide480">
            	<?php
				$sql = "SELECT * FROM ".$TABLE["Categories"]." WHERE id='".SafetyDB(htmlentities($_REQUEST["cat_id"]))."'";
				$sql_result = sql_result($sql);
				$Cat = mysqli_fetch_assoc($sql_result); ?>
            	<div style="text-align:left; color:<?php echo $OptionsVis["column_color"]; ?>; font-family:<?php echo $OptionsVis["title_font"]; ?>; font-size:<?php echo $OptionsVis["column_size"]; ?>; font-weight:<?php echo $OptionsVis["column_font_weight"]; ?>; font-style:<?php echo $OptionsVis["column_font_style"]; ?>;">
					<?php if($Cat['cat_name']!="") echo ReadDB($Cat['cat_name']); else echo $OptionsLang['All_Ads']; ?>
                </div>
            </td>
            <td>
            	<div style="text-align:left;">
                <form action="<?php echo $thisPage; ?>?cat_id=<?php echo $_REQUEST["cat_id"]; ?>" method="post" name="form" style="margin:0; padding:0;">
                  <input type="text" name="search" value="<?php if(isset($_REQUEST["search"]) and $_REQUEST["search"]!='') echo htmlspecialchars(urldecode($_REQUEST["search"]), ENT_QUOTES); ?>" />
                  <input name="SearchName" type="submit" value="<?php echo $OptionsLang["Search_button"]; ?>" />
                </form>
                </div>
            </td>
            <!-- <td><div style="text-align:<?php echo $OptionsVis["summ_price_text_align"];?>; color:<?php echo $OptionsVis["column_color"]; ?>; font-family:<?php echo $OptionsVis["title_font"]; ?>; font-size:<?php echo $OptionsVis["column_size"]; ?>; font-weight:<?php echo $OptionsVis["column_font_weight"]; ?>; font-style:<?php echo $OptionsVis["column_font_style"]; ?>;"><?php echo $OptionsLang["Price"]; ?></div></td>
            <td class="mobilehide480" style="padding-right:0;"><div style="text-align:<?php echo $OptionsVis["summ_date_text_align"];?>; color:<?php echo $OptionsVis["column_color"]; ?>; font-family:<?php echo $OptionsVis["title_font"]; ?>; font-size:<?php echo $OptionsVis["column_size"]; ?>; font-weight:<?php echo $OptionsVis["column_font_weight"]; ?>; font-style:<?php echo $OptionsVis["column_font_style"]; ?>;"><?php echo $OptionsLang["Listed"]; ?></div></td> -->
          </tr>
    	<?php 
		if(isset($_REQUEST["p"]) and $_REQUEST["p"]!='') { 
			$pageNum = (int) SafetyDB(urldecode($_REQUEST["p"]));
			if($pageNum<=0) $pageNum = 1;
		} else { 
			$pageNum = 1;
		}
		
		$search = "";
		
		$ANDCat = "";
		
		if ($_REQUEST["cat_id"]>0) $ANDCat = " AND cat_id='".SafetyDB(htmlentities($_REQUEST["cat_id"]))."'";
		
		// send email to user for Classified Ads expiration
		if($Options['email_after_expire']=='true') {
			$sql = "SELECT * FROM ".$TABLE["Ads"]."  
					WHERE status='Online' AND expire_date <= NOW()";	
			$sql_result = sql_result($sql);			
			while ($Ads = mysqli_fetch_assoc($sql_result)) {
				$mailheader = "From: ".ReadDB($Options["email"])."\r\n";
				$mailheader .= "Reply-To: ".ReadDB($Options["email"])."\r\n";
				$mailheader .= "Content-type: text/html; charset=UTF-8\r\n";
				$Message_body = "<strong>".$OptionsLang["Classified_Ads_expired"]."</strong><br /><br />";
				$Message_body .= "Product/Service: <strong>".ReadDB($Ads["title"])."</strong><br />";
				$Message_body .= "Description: <br />".$Ads["description"]."<br /><br />";
				$Message_body .= "Price: ".$Ads["price"]."<br />";
				$Message_body .= "Name: ".$Ads["name"]."<br />";
				$Message_body .= "Email: ".$Ads["email"]."<br />";
				$Message_body .= "Phone: ".$Ads["phone"]."<br />";
				// Click on the link below to renew the classified ad with another period //
				$Message_body .= "Click on the link below to renew(extend) the classified ad with another period: <br /><br />";
				$Message_body .= '<a href="'.$CONFIG["full_url"].'extend.php?i='.$Ads["id"].'&amp;u='.$Ads["user_id"].'&amp;s='.md5($Ads["id"].' - '.$Ads["user_id"]).'">'.$CONFIG["full_url"].'extend.php?i='.$Ads["id"].'&amp;u='.$Ads["user_id"].'&amp;s='.md5($Ads["id"].' - '.$Ads["user_id"]).'</a><br /><br />';
				//mail(ReadDB($Ads["email"]), $OptionsLang["Classified_Ads_expired"], $Message_body, $mailheader);
				
				
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
					
					$mail->AddReplyTo(ReadDB($Options["email"]), ReadDB($Options["email"]));// Add reply To email
					
					$mail->Subject = ReadDB($OptionsLang["Classified_Ads_expired"]); // Set email subject	
			
					$mail->MsgHTML($Message_body);
					//$mail->Body    = $Message_body;
					$mail->AltBody = strip_tags($Message_body);
					
					if(!$mail->send()) {
						//$message .= ' Message could not be sent.';
						//$message .= ' Mailer Error: ' . $mail->ErrorInfo;
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
					$mail->addAddress($Ads["email"], $Ads["email"]);					
					
					//Set an alternative reply-to address
					$mail->addReplyTo(ReadDB($Options["email"]), ReadDB($Options["email"]));
					
					$mail->CharSet = "UTF-8"; // force setting charset UTF-8
					//Set the subject line
					$mail->Subject = ReadDB($OptionsLang["Classified_Ads_expired"]); // Set email subject
					//Read an HTML message body from an external file, convert referenced images to embedded,
					//convert HTML into a basic plain-text alternative body
					$mail->msgHTML($Message_body);
					//Replace the plain text body with one created manually
					$mail->AltBody = strip_tags($Message_body);
					//Attach an image file
					//$mail->addAttachment('images/phpmailer_mini.png');
					
					//send the message, check for errors
					if (!$mail->send()) {
						//$message .= " Mailer Error: " . $mail->ErrorInfo;
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
				
				
			}
		}
		
		if($Options['del_after_expire']=='hidden') { // set status to 'Hidden' when Classified Ads expired
			
			$sql = "UPDATE ".$TABLE["Ads"]."  
					SET status='Expired' 
					WHERE status='Online' AND expire_date <= NOW()";	
			$sql_result = sql_result($sql);
			
		} elseif($Options['del_after_expire']=='del') { // delete the entry when Classified Ads expired
		
			$sql = "DELETE FROM ".$TABLE["Ads"]." 
					WHERE status='Online' AND expire_date <= NOW()";	
			$sql_result = sql_result($sql);
		
		}
		
		
		if(isset($_REQUEST["search"]) and ($_REQUEST["search"]!="")) {
			$find = SafetyDB(urldecode($_REQUEST["search"]));
			$search .= " AND (title LIKE '%".$find."%' OR description LIKE '%".$find."%' OR location LIKE '%".$find."%')";
			//$find = str_ireplace(" ", "|", $find);
			//$search = " AND title REGEXP '(".$find.")' OR location REGEXP '(".$find.")' OR description REGEXP '(".$find.")' ";
		}
		
		$per_page = $Options["per_page"];
		$Offset = $Options["per_page"];	
		$numOffset = 0;
		
		//-------------------------------------------------------------------------------------------------------//
		
		// query for HightLighted Online Ads		
		$sql = "SELECT * FROM ".$TABLE["Ads"]."  
				WHERE status='Online' AND `highlight`='true' " . $ANDCat . $search . "  
				ORDER BY RAND()";		
		$sql_result = sql_result($sql);	
		$numHighLight = mysqli_num_rows($sql_result);
		
		if (mysqli_num_rows($sql_result)>0) {	
			if($_REQUEST["p"]=='' or $_REQUEST["p"]==1) {			
				while ($Ads = mysqli_fetch_assoc($sql_result)) {
					include($CONFIG["server_path"].'classilist.php');
				}
				$per_page = $Options["per_page"] - $numHighLight;	
			}
		}
		
		//-------------------------------------------------------------------------------------------------------//
		
		if($per_page < 0) {
			$per_page = 0;
		}
		
		
		if (isset($_REQUEST["p"]) and $_REQUEST["p"]>1) {
			$numOffset = $numHighLight;
			if($numHighLight>$Options["per_page"]) {
				$numOffset = $Options["per_page"];
			}
		}
		
		//-------------------------------------------------------------------------------------------------------//
		
		// query for the total number of Online Ads		
		$sql   = "SELECT count(*) as total FROM ".$TABLE["Ads"]." WHERE status='Online' " .$ANDCat . $search;
		$sql_result = sql_result($sql);
		$row   = mysqli_fetch_array($sql_result);
		$count = $row["total"];
		$pages = ceil($count/$Options["per_page"]);
		
		// query for Online Ads and not HightLighted
		$sql = "SELECT * FROM ".$TABLE["Ads"]."  
				WHERE status='Online' AND `highlight`<>'true' " . $ANDCat . $search . "  
				ORDER BY publish_date DESC 
				LIMIT " . (($pageNum-1)*$Offset - $numOffset) . "," . $per_page;
		$sql_result = sql_result($sql);
		
		if (mysqli_num_rows($sql_result)>0 or $count>0) {	
		  	while ($Ads = mysqli_fetch_assoc($sql_result)) {
				include($CONFIG["server_path"].'classilist.php');
		  	}
		?>
        </table>
        
        <div style="padding-top:14px;clear:both;text-align:<?php echo $OptionsVis["pag_align"];?>;font-size:<?php echo $OptionsVis["pag_font_size"];?>;">
		<?php
        if ($pages>0) {
            echo "<span style='font-weight:".$OptionsVis["pag_font_weight"].";color:".$OptionsVis["pag_color"]."'>".$OptionsLang['Paging']." </span>";
            for($i=1;$i<=$pages;$i++){ 
                if($i == $pageNum ) echo "<strong style='font-weight:".$OptionsVis["pag_font_weight"].";color:".$OptionsVis["pag_color"]."'>" .$i. "</strong>";
                else echo "<a href='".$thisPage."?p=".$i."&amp;cat_id=".$_REQUEST["cat_id"]."&amp;search=".urlencode($_REQUEST["search"])."#onads' style='font-weight:".$OptionsVis["pag_font_weight"].";color:".$OptionsVis["pag_color"]."'>".$i."</a>"; 
                echo "&nbsp; ";
            }
        }
        ?>    
   	  </div>
              
        <?php 
        } else {
		?>
        </table>
        <div style="line-height:20px; padding-bottom:20px;"><?php echo $OptionsLang['No_Classified_Ads_published'] ?></div>
        <?php	
		}
		mysqli_free_result($sql_result);
		?>   
              
	</div>

<?php
}
?>
</div>
</div>