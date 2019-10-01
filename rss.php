<?php
header("Content-type: text/xml"); 
$installed = '';
include("configs.php");

echo '<?xml version="1.0" encoding="utf-8"?>';
?>
<rss version="2.0" xmlns:atom="http://www.w3.org/2005/Atom">
<channel>
	<title>Classified Ads RSS</title>
	<description>latest 10 Classified Ads</description>
	<link><?php echo $CONFIG["full_url"]; ?></link>
    <atom:link href="<?php echo $CONFIG["full_url"]; ?>rss.php" rel="self" type="application/rss+xml" />
<?php
	$sql = "SELECT * FROM ".$TABLE["Ads"]." WHERE status='Online' ORDER BY publish_date DESC LIMIT 0,10";
	$sql_result = sql_result($sql);
	while ($Ads = mysqli_fetch_assoc($sql_result)) {
		$isPermaLink = substr(str_shuffle('abcdefghijklmnopqrstuvwxyzABCDEFG1234567890'), 0, 20);
?>
	<item>
		<guid isPermaLink='false'><?php echo $isPermaLink.$Ads["id"]; ?></guid>
		<title><![CDATA[<?php echo ReadDB($Ads["title"]); ?>]]></title>
        <link><?php echo $CONFIG["full_url"]; ?>preview.php?id=<?php echo $Ads["id"]; ?></link>
		<description><![CDATA[<?php echo ReadDB($Ads["description"]); ?>]]></description>
        <pubDate><?php echo date("D, d M Y H:i:s O",strtotime($Ads["publish_date"])); ?></pubDate>
        <?php if($Ads["image"]!='') { ?>
        <enclosure url="<?php echo $CONFIG["full_url"].$CONFIG["upload_folder"].$Ads["image"]; ?>" length="0" type="image/jpeg" />
        <?php } ?>
	</item>
<?php } ?>
</channel>
</rss>