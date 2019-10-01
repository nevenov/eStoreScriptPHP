<?php 
error_reporting(0);
include("configs.php");
include("language_user.php");

$sql = "SELECT * FROM ".$TABLE["Options"];
$sql_result = sql_result($sql);
$Options = mysqli_fetch_assoc($sql_result);
mysqli_free_result($sql_result);

$errorMessage = $lang["Error_renew_classified_ad"];

$successMessage = $lang["Classified_ad_successfully_renewed"];

?><!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>Renewing Classified Ad</title>
</head>

<body>

<div style="width: 550px; margin: 20px auto; padding: 20px; color: #000; background-color:#FFFF99; border: solid 1px #000000; text-align: center;">
<?php 
if($_REQUEST["i"]>0){

	$sql = "SELECT * FROM ".$TABLE["Ads"]." 
			WHERE `id` = '".SafetyDB($_REQUEST["i"])."' AND `user_id` = '".SafetyDB($_REQUEST["u"])."'";
	$sql_result = sql_result($sql);
	if(mysqli_num_rows($sql_result)>0){

		if(md5(ReadDB($_REQUEST["i"])." - ".ReadDB($_REQUEST["u"])) == $_REQUEST["s"]){
			
			$expire_date= date("Y-m-d H:i:s", mktime(date("H"), date("i"), date("s"), date("m"), date("d")+$Options['expire_days'], date("Y")));
			
			$sql = "UPDATE ".$TABLE["Ads"]." 
					SET `status` 		= 'Online',
						`publish_date`	= now(),
						`expire_date`	= '".SaveDB($expire_date)."' 
					WHERE `id` = '".$_REQUEST["i"]."' AND `user_id` = " . SafetyDB($_REQUEST["u"]);
			$sql_result = sql_result($sql);
			echo $successMessage;	
		} else {
			echo $errorMessage;
		}
	} else {
		echo $errorMessage;
	}
} else {
	echo $errorMessage."1";
}	
?>
</div>
</body>
</html>