<?php
$installed = '';
if(!isset($configs_set_cl)) {
	include( dirname(__FILE__). "/configs.php");
}

$sql = "SELECT * FROM ".$TABLE["Options"];
$sql_result = sql_result($sql);
$Options = mysqli_fetch_assoc($sql_result);


	//////////// STARTING PHPMailer /////////////////////
	//adding PHPMailer library
	include( dirname(__FILE__). '/phpmailer/PHPMailerAutoload.php');

	/* if ($_SERVER['REQUEST_METHOD'] != 'POST') {
		header('Location: ' . $CONFIG["full_url"] . 'preview.php');
		exit();
	} */

	$ch = curl_init();
	curl_setopt($ch, CURLOPT_URL, 'https://ipnpb.sandbox.paypal.com/cgi-bin/webscr');
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
	curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
	curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
	curl_setopt($ch, CURLOPT_POST, 1);
	curl_setopt($ch, CURLOPT_POSTFIELDS, "cmd=_notify-validate&" . http_build_query($_POST));
	$response = curl_exec($ch);
	curl_close($ch);

	// Just for testing purposes
	// file_put_contents('test.txt', $response);

	if ($response == "VERIFIED" && $_POST['receiver_email'] == ReadDB($Options["paypal_email"])) {
		
		// Just for testing purposes in test.txt
		$handle = fopen('test.txt', 'w');
		foreach ($_POST as $key => $value) {
		 	fwrite($handle, "$key => $value \r\n");
		}
		fclose($handle);

		// those releted to customer
		$customerEmail = $_POST['payer_email'];
		$name = $_POST['first_name'] . " " . $_POST['last_name']; 

		// those are releted to purchase
		$price = $_POST['mc_gross'];
		$currency = $_POST['mc_currency'];
		$item_name = $_POST['item_name'];
		$item_num = $_POST['item_number'];
		$paymentStatus = $_POST['payment_status'];

		if ($currency == $CurrAbr[$Options["currency"]] && $paymentStatus == "Completed") {
			
			// for testing purposes in test.txt should say 'All verified'
			// file_put_contents('test.txt', 'All verified');

			$mail = new PHPMailer();

			$mail->setFrom("info@simplephpscripts.com", "Sales");
			//$mail->addAttachment("attachment/wordpress-plugin.zip", "WordPress Plugin");
			$mail->addAddress($customerEmail, $name);
			$mail->isHTML(true);
			$mail->Subject = "Your Purchase Details";
			$mail->Body = "
				Hi {$name}, <br><br>
				Thank you for purchasing '{$item_name}'. <br> 
				We will contact you as soon as possible in order to ship the item or arrange local pickup.<br><br>
				
				Kind regards,<br> 
				Admin, <br> 
				{$Options['email']}
			";

			$mail->send();
			
			$sql = "INSERT INTO ".$TABLE["Orders"]." 
					SET `orderDate` 	= now(),
						`status`		= 'Paid',    
						`name` 			= '".SaveDB($_POST['first_name'])."', 
						`products` 		= '".SaveDB($item_name)."',
						`amount` 		= '".SaveDB($_POST['mc_gross'])."',
						`last_name` 	= '".SaveDB($_POST['last_name'])."',
						`email` 		= '".SaveDB($_POST['payer_email'])."', 
						`address` 		= '".SaveDB($_POST["address_street"])."', 
						`city`			= '".SaveDB($_POST["address_city"])."', 
						`state` 		= '".SaveDB($_POST["address_state"])."',  
						`zip` 			= '".SaveDB($_POST["address_zip"])."',   
						`country` 		= '".SaveDB($_POST["address_country"])."',   
						`notes` 		= '".SaveDB($_POST["address_country"])."'";
			$sql_result = sql_result($sql);
			
		} else {
			// check errors in test.txt
			// $errordata = "$price \r\n $currency \r\n $item_num \r\n $paymentStatus \r\n";
			// file_put_contents("test.txt", "$errordata");
		} 
	}
	

?>