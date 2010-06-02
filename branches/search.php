<?php
#session_start();
include 'newexec.php';
#include "notice.html";
echo '<form name="input" action="search.php" method="get"> Search: <input type="text" name="name" /> <select name="type">
<option value="songs" selected="selected">Songs</option>
<option value="artists">Artists</option>
<option value="albums">Albums</option>
</select>
<input type="submit" value="Search" /> </form> ';
echo "<br><br>";
if (isset($_GET['name'])) {
	if (!isset($_GET["type"])) {
		$gettype = "songs";
	} else {
		$gettype = $_GET['type'];
	}
	#$session = $_SESSION['sessionid'];
	switch($gettype) {
		case "artists":
			$searchartistdata = searchArtists($_GET['name']);
			$artistlist = json_decode($searchartistdata, true);
			#echo $songlist[result][Songs][SongName][0];
			echo "<br>";
			foreach($artistlist["result"]["artists"] as $val) {
				#$formmvalue = str_shuffle($val[Name]);
				#$formvalue = str_replace(" ", "", "$formmvalue");
				#echo "Song:".$val["songName"]."<br><br>".
				echo "Artist:".$val["artistName"]."<br><br><br>";
				#echo "<a href=stream.php?songid=".$val["songID"].">Play</a><br><br><br>";
			}
		case "albums":
			$searchalbumdata = searchAlbums($_GET['name']);
			$albumlist = json_decode($searchalbumdata, true);
			#echo $songlist[result][Songs][SongName][0];
			echo "<br>";
			foreach($albumlist["result"]["albums"] as $val) {
				#$formmvalue = str_shuffle($val[Name]);
				#$formvalue = str_replace(" ", "", "$formmvalue");
				echo "Album:".$val["albumName"]."<br><br>".
				"Artist:".$val["artistName"]."<br><br><br>";
				#echo "<a href=stream.php?songid=".$val["songID"].">Play</a><br><br><br>";
			}
	default:
		$searchsongdata = searchSongs($_GET['name']);
		$songlist = json_decode($searchsongdata, true);
		#echo $songlist[result][Songs][SongName][0];
		echo "<br>";
		foreach($songlist["result"]["songs"] as $val) {
			#$formmvalue = str_shuffle($val[Name]);
			#$formvalue = str_replace(" ", "", "$formmvalue");
			echo "Song:".$val["songName"]."<br><br>".
			"Artist:".$val["artistName"]."<br><br>";
			echo "<a href=stream.php?songid=".$val["songID"].">Play</a><br><br><br>";
		}
	}

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
