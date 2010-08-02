<?php
#session_start();
include 'newexec.php';
include "header.php";
#include "notice.html";

if (@!$_SESSION['loggedin']) {
	echo "<br>Please login to see playlists";
	exit;
}
$listlistdata = userGetPlaylists($_SESSION['userID']);
$playlistlist = json_decode($listlistdata, true);
#echo $songlist[result][Songs][SongName][0];
echo "Here are your playlists";
echo "<br><br>";
foreach($playlistlist['result']["playlists"] as $vall) {
	#$formmvalue = str_shuffle($val[Name]);
	#$formvalue = str_replace(" ", "", "$formmvalue");
	#echo "Song:".$val["songName"]."<br><br>".
	#"Artist:".$val["artistName"]."<br><br>";
	echo "<a href=playlist.php?id=".$vall["playlistID"].">".$vall['playlistName']."</a><br><br>";
}
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
echo "Playlist "."\"".$listname."\""." has ".$playlist['result']['pager']['totalCount']." songs<br><br>";
foreach($playlist['result']['songs'] as $val) {
    echo "Song:".$val['songName']."<br>";
    echo "Artist: <a href=artist.php?artistid=".$val['artistID'].">".$val['artistName']."</a><br>";
	echo "Album: <a href=album.php?albumid=".$val['albumID'].">".$val['albumName']."</a><br>";
	echo "<a href=stream.php?songid=".$val["songID"].">Play</a><br><br><br>";
}
?>
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
