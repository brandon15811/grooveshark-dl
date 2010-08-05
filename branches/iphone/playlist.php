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
<?php
if ($_SESSION['loggedin']) {
	echo '<div id="bluerightbutton"><a href="logout.php">Logout</a></div>';
} else {
	echo '<div id="bluerightbutton"><a href="login.php">Login</a></div>';
}
?>
<div id="leftnav">
<a href="index.php"><img alt="home" src="images/home.png" /></a></div>
</div>
<div class="searchbox"><form action="search.php" method="get">
<fieldset><input id="search" placeholder="Search" type="text" name="name" />
<input id="submit" type="hidden" />
</fieldset>
</div>
<div class="searchbox">
<li class="select"><select name="type">
<option value="songs" selected="selected">Songs</option>
<option value="artists">Artists</option>
<option value="albums">Albums</option>
</select><span class="arrow">
</span></li></form></div>

	<span class="graytitle">Playlists</span>
<div id="content">
		<ul class="pageitem">
<?php
if (@!$_SESSION['loggedin']) {
	echo "<br>Please login to see playlists";
	exit;
}
$listlistdata = userGetPlaylists($_SESSION['userID']);
$playlistlist = json_decode($listlistdata, true);
#echo $songlist[result][Songs][SongName][0];
//echo "Here are your playlists";
//echo "<br><br>";
foreach($playlistlist['result']["playlists"] as $vall) {
		echo "<li class='menu'><a href='playlist.php?id=".$vall['playlistID']."'>
	<img alt='list' src='thumbs/music.png' /><span class='name'>".$vall['playlistName']."</span><span class='arrow'></span></a></li>";
}
echo "</ul>";
if (!isset($_GET['id'])) {
	exit;
}
foreach($playlistlist['result']['playlists'] as $vval) {
	if ($vval['playlistID'] == $_GET['id']) {
		$listname = $vval['playlistName'];
		
	}
}
$listdata = playlistGetSongs($_GET['id']);
$playlist = json_decode($listdata, true);
echo '<span class="graytitle">';
echo "Playlist "."\"".$listname."\""." has ".$playlist['result']['pager']['totalCount']." songs<br><br>";
echo '</span>
	<ul class="pageitem">';
foreach($playlist['result']['songs'] as $val) {
	echo "<li class='menu'><a href='stream.php?songid=".$val['songID']."'>
	<img alt='list' src='thumbs/music.png' /><span class='name'>".$val['songName']." by ".$val['artistName']."</span><span class='arrow'></span></a></li>";
}
?>

		</ul>
</div>

<div id="footer">
	<!-- Support iWebKit by sending us traffic; please keep this footer on your page, consider it a thank you for our work :-) -->
	<a class="noeffect" href="http://iwebkit.net">Powered by iWebKit</a></div>
<!-- Start 1FreeCounter.com code -->
  
  <script language="JavaScript">
  var data = '&r=' + escape(document.referrer)
	+ '&n=' + escape(navigator.userAgent)
	+ '&p=' + escape(navigator.userAgent)
	+ '&g=' + escape(document.location.href);

  if (navigator.userAgent.substring(0,1)>'3')
    data = data + '&sd=' + screen.colorDepth 
	+ '&sw=' + escape(screen.width+'x'+screen.height);

  document.write('<img alt="" border=0 hspace=0 '+'vspace=0 src="http://www.1freecounter.com/counter.php?i=53197' + data + '">');
  </script>

<!-- End 1FreeCounter.com code -->
</body>

</html>
