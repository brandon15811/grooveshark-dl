<?php
#session_start();
include 'newexec.php';
#include "notice.html";
#$session = $_SESSION['sessionid'];
echo '<form name="input" action="search.php" method="get"> Search: <input type="text" name="name" /> <input type="submit" value="Search" /> </form> ';
echo "<br><br>";
if (isset($_GET['name'])) {
$searchdata = searchSongs($_GET['name']);
$songlist = json_decode($searchdata, true);
#echo $songlist[result][Songs][SongName][0];
foreach($songlist["result"]["songs"] as $val) {
	#$formmvalue = str_shuffle($val[Name]);
	#$formvalue = str_replace(" ", "", "$formmvalue");
    echo "Song:".$val["songName"]."<br>".
    echo "Artist: <a href=artist.php?artistid=".$val['artistID'].">".$val['artistName']."</a><br>";
	echo "Album: <a href=album.php?albumid=".$val['albumID'].">".$val['albumName']."</a><br>";

	#$streamjson = streamKey($val[SongID]);
	#$streamdata = json_decode($streamjson, true);
	#$buttonurl = $streamdata[result][result][streamServer];
	#$buttonkey = $streamdata[result][result][streamKey];
	echo "<a href=stream.php?songid=".$val["songID"].">Download</a><br><br><br> ";
	#echo "<a href=".$val[SongID].".mp3>Download</a><br><br><br> ";
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
