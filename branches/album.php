<?php
session_start();
include 'newexec.php';
include "header.php";
if (isset($_GET['albumid'])) {
$json = getAlbumSongsEx($_GET['albumid']);
$jsona = json_decode($json, true);

$albumClass = $apiClass->getPackage($auth, 'album', $config);
$methodVars = array(
	'artist' => $jsona['result']['songs']['0']['ArtistName'],
	'album' => $jsona['result']['songs']['0']['AlbumName']
);
	/*$methodVars = array(
	'artist' => 'Cher',
	'album' => 'Believe'
);*/
if ( $album = $albumClass->getInfo($methodVars) ) {
	echo "<img src=".$album['image']['extralarge'].">";
	echo "<br><br>";
	echo strip_tags(nl2br($album['summary']), "<br>");
	echo "<br><br>";
}
else {
	die('<b>Error '.$albumClass->error['code'].' - </b><i>'.$albumClass->error['desc'].'</i>');
}
}

/*echo "<pre>";
print_r($jsona);
echo "</pre>";*/
foreach ($jsona['result']['songs'] as $val) {
echo "Song: <a href=stream.php?songid=".$val['SongID'].">".$val['SongName']."</a><br>";
echo "Artist: <a href=artist.php?artistid=".$val['ArtistID'].">".$val['ArtistName']."</a><br>";
echo "Album: <a href=album.php?albumid=".$val['AlbumID'].">".$val['AlbumName']."</a><br>";
echo "<br>";
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
