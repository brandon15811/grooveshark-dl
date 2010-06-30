<?php include 'newexec.php'; ?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">

<head>
<meta content="yes" name="apple-mobile-web-app-capable" />
<meta name="apple-mobile-web-app-status-bar-style" content="default"/>
<meta content="text/html; charset=iso-8859-1" http-equiv="Content-Type" />
<meta content="minimum-scale=1.0, width=device-width, maximum-scale=0.6667, user-scalable=no" name="viewport" />
<link href="css/style.css" rel="stylesheet" media="screen" type="text/css" />
<script src="javascript/functions.js" type="text/javascript"></script>
<title>Grooveshark</title>
<meta content="keyword1,keyword2,keyword3" name="keywords" />
<meta content="Description of your page" name="description" />
</head>

<body>

<div id="topbar">
<div id="title">Grooveshark</div>
<div id="leftnav">
<a href="index.php"><img alt="home" src="images/home.png" /></a></div>
</div>
<div class="searchbox"><form action="playlist.php" method="get"><fieldset><input
id="search" placeholder="Playlist ID" type="text" name="playlist" /><input id="submit"
type="hidden" /></fieldset></form></div>

<div id="content">
	<span class="graytitle">Playlist</span>
		<ul class="pageitem">
<?php
if (isset($_GET['playlist'])) {
	$playlistdata = playlistGetSongs($_GET['playlist']);
	$songlist = json_decode($playlistdata, true);
	foreach($songlist['result']['songs'] as $val) {
	echo "<li class='menu'><a href='stream.php?songid=".$val['songID']."'>
	<img alt='list' src='thumbs/music.png' /><span class='name'>".$val['songName']." by ".$val['artistName']."</span><span class='arrow'></span></a></li>";

}
}
else {
echo "Please type in a Playlist ID";
}

?>

		</ul>
</div>

<div id="footer">
	<!-- Support iWebKit by sending us traffic; please keep this footer on your page, consider it a thank you for our work :-) -->
	<a class="noeffect" href="http://iwebkit.net">Powered by iWebKit</a></div>

</body>

</html>
