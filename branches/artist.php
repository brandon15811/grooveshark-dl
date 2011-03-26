<?php
session_start();
include 'newexec.php';
include "header.php";
if (isset($_GET['artistid'])) {
$json = getArtistAlbums($_GET['artistid']);
$jsona = json_decode($json, true);

$artistClass = $apiClass->getPackage($auth, 'artist', $config);
$methodVars = array('artist' => $jsona['result']['albums']['0']['ArtistName']);
if ( $artist = $artistClass->getInfo($methodVars) ) {
	echo "<img src=".$artist['image']['extralarge'].">";
	echo "<br><br>";
	echo strip_tags(nl2br($artist['bio']['summary']), "<br>");
	echo "<br><br>";
}
else {
	die('<b>Error '.$artistClass->error['code'].' - </b><i>'.$artistClass->error['desc'].'</i>');
}


/*echo "<pre>";
print_r($jsona);
echo "</pre>";*/
foreach ($jsona['result']['0']['albums'] as $val) {
echo "Artist: <a href=artist.php?artistid=".$val['ArtistID'].">".$val['ArtistName']."</a><br>";
echo "Album: <a href=album.php?albumid=".$val['AlbumID'].">".$val['AlbumName']."</a><br>";
echo "<br>";
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
