<?php
session_start();
include 'newexec.php';
include "notice.html";
#$session = $_SESSION['sessionid'];
$populardata = popularGetSongs();
$songlist = json_decode($populardata, true);
#echo $songlist[result][Songs][SongName][0];
echo "<br>";
foreach($songlist["result"]["songs"] as $val) {
	#$formmvalue = str_shuffle($val[Name]);
	#$formvalue = str_replace(" ", "", "$formmvalue");
	echo "Song:".$val["songName"]."<br><br>".
	"Artist:".$val["artistName"]."<br><br>";
	#$streamjson = streamKey($val[SongID]);
	#$streamdata = json_decode($streamjson, true);
	#$buttonurl = $streamdata[result][result][streamServer];
	#$buttonkey = $streamdata[result][result][streamKey];
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
