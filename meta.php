<?php 
$installed = '';
if(!isset($configs_are_set_news)) {
	include( dirname(__FILE__). "/configs.php");
}

$sql = "SELECT * FROM ".$TABLE["Options"];
$sql_result = sql_result($sql);
$Options = mysqli_fetch_assoc($sql_result);
mysqli_free_result($sql_result);
$OptionsLang = unserialize($Options['language']);

if (isset($_REQUEST["id"]) and $_REQUEST["id"]>0) {
	$_REQUEST["id"]= (int) SafetyDB($_REQUEST["id"]);
	?>
	<?php 
	$sql = "SELECT * FROM ".$TABLE["Ads"]." WHERE id='".SafetyDB($_REQUEST["id"])."' and status='Online'";
	$sql_result = sql_result($sql);
	if(mysqli_num_rows($sql_result)>0) {	
	  $Meta = mysqli_fetch_assoc($sql_result);
	?>
	<title><?php echo ReadHTML($Meta["title"]); ?></title>
	<meta name="description" content="<?php echo cutText(ReadHTML(strip_tags($Meta["description"])), 160); ?>" />
    <meta property="og:title" content="<?php echo ReadHTML($Meta["title"]); ?>" />
    <meta property="og:type" content="article" />
    <meta property="og:image" content="<?php echo $CONFIG["full_url"].$CONFIG["upload_folder"].ReadHTML($Meta["image1"]); ?>" />
    <meta property="og:description" content="<?php echo ReadHTML(strip_tags($Meta["description"])); ?>" />
	<?php 
	} 
} else {
?>
	<title><?php echo ReadHTML($OptionsLang["metatitle"]); ?></title>
	<meta name="description" content="<?php echo ReadHTML($OptionsLang["metadescription"]); ?>" />
<?php 
}
?>