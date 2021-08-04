<?
header( "Access-Control-Allow-Origin: *");

$wrapped = 0;

if (isset($_GET['wrapped']))
    $wrapped = 1;

if (!$wrapped)
    echo "<html>\n";

$skipJSsettings = 1;
require_once("common.php");

DisableOutputBuffering();

if (!$wrapped) {
?>
<head>
<title>
FPP OS Uprade
</title>
</head>
<body>
<h2>FPP OS Upgrade</h2>
Image: <? echo strip_tags($_GET['os']); ?><br>
<pre>
<?
} else {
    echo "FPP OS Upgrade\n";
    echo "Image: " . strip_tags($_GET['os']) . "\n";
}

if (preg_match('/^https?:/', $_GET['os'])) {
    echo "==========================================================================\n";
    $baseFile = escapeshellcmd(preg_replace('/.*\/([^\/]*)$/', '$1', $_GET['os']));
    echo "Downloading OS Image:\n";
    $cmd = "curl -f --fail-early " . escapeshellcmd($_GET['os']) . " --output /home/fpp/media/upload/$baseFile 2>&1";
    system($cmd);
    $_GET['os'] = $baseFile;
}
?>
==========================================================================
Upgrading OS:
<?
copy("$fppDir/SD/upgradeOS-part1.sh", "/home/fpp/media/tmp/upgradeOS-part1.sh");
chmod("/home/fpp/media/tmp/upgradeOS-part1.sh", 0775);
system($SUDO . " stdbuf --output=L --error=L /home/fpp/media/tmp/upgradeOS-part1.sh /home/fpp/media/upload/" . escapeshellcmd($_GET['os']));
?>
==========================================================================
<?
if (!$wrapped) {
?>
</pre>
<b>Rebooting.... please wait for FPP to reboot.</b>
<a href='index.php'>Go to FPP Main Status Page</a><br>
<a href='about.php'>Go back to FPP About page</a><br>
</body>
</html>
<?
} else {
    echo "Rebooting.... Please wait for FPP to reboot.\n";
}
flush();
while (@ob_end_flush());
session_write_close();

system($SUDO . " shutdown -r now");
?>
