<?php
#session_start();
include 'newexec.php';
include "header.php";
#include "notice.html";
echo '<form name="input" action="search.php" method="get"> Search: <input type="text" name="name" /> <select name="type">
<option value="songs" selected="selected">Songs</option>
<option value="artists">Artists</option>
<option value="albums">Albums</option>
</select>
<input type="submit" value="Search" /> </form> ';
echo "<br><br>";
if (isset($_GET['name'])) {
	if (empty($_GET["type"])) {
		$gettype = "songs";
	} else {
		$gettype = $_GET['type'];
	}
	#$session = $_SESSION['sessionid'];
	switch($gettype) {
		case "artists":
			$searchartistdata =getArtistSearchResults($_GET['name']);
			$artistlist = json_decode($searchartistdata, true);
			#echo $songlist[result][Songs][SongName][0];
			echo "<br>";
			foreach($artistlist["result"]["artists"] as $val) {
				#$formmvalue = str_shuffle($val[Name]);
				#$formvalue = str_replace(" ", "", "$formmvalue");
				#echo "Song:".$val["songName"]."<br><br>".
				echo "Artist: <a href=artist.php?artistid=".$val['ArtistID'].">".$val['ArtistName']."</a><br>";
			}
			break;
		case "albums":
			$searchalbumdata = getAlbumSearchResults($_GET['name']);
			$albumlist = json_decode($searchalbumdata, true);
			#echo $songlist[result][Songs][SongName][0];
			echo "<br>";
			foreach($albumlist["result"]["albums"] as $val) {
				#$formmvalue = str_shuffle($val[Name]);
				#$formvalue = str_replace(" ", "", "$formmvalue");
				echo "Artist: <a href=artist.php?artistid=".$val['ArtistID'].">".$val['ArtistName']."</a><br>";
				echo "Album: <a href=album.php?albumid=".$val['AlbumID'].">".$val['AlbumName']."</a><br><br>";
				#echo "<a href=stream.php?songid=".$val["songID"].">Play</a><br><br><br>";
			}
			break;
		case "songs":
			if ($_GET['type'] !== "albums" and $_GET['type'] !== "artists") {
			$searchsongdata = getSongSearchResultsEx($_GET['name']);
			$songlist = json_decode($searchsongdata, true);
			#echo $songlist[result][Songs][SongName][0];
			echo "<br>";
			foreach($songlist["result"]["songs"] as $val) {
				#$formmvalue = str_shuffle($val[Name]);
				#$formvalue = str_replace(" ", "", "$formmvalue");
				echo "Song:".$val["SongName"]."<br>";
				echo "Artist: <a href=artist.php?artistid=".$val['ArtistID'].">".$val['ArtistName']."</a><br>";
				echo "Album: <a href=album.php?albumid=".$val['AlbumID'].">".$val['AlbumName']."</a><br>";
				echo "<a href=stream.php?songid=".$val["SongID"].">Play</a><br><br><br>";
				}
			}
			break;
		default:
		if (empty($_GET["type"])) {
		echo "Please enter a valid type";
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
