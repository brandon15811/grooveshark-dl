<?php
include 'exec.php';
echo "<br><br>";
$listdata = playlist($_GET['playlist']);
$songlist = json_decode($listdata, true);
#echo $songlist[result][Songs][SongName][0];
foreach($songlist[result][Songs] as $val) {
	$formmvalue = str_shuffle($val[Name]);
	$formvalue = str_replace(" ", "", "$formmvalue");
    echo "Song:".$val[Name]."<br><br>".
    "Artist:".$val[ArtistName]."<br><br>";
	#$streamjson = streamKey($val[SongID]);
	#$streamdata = json_decode($streamjson, true);
	#$buttonurl = $streamdata[result][result][streamServer];
	#$buttonkey = $streamdata[result][result][streamKey];
	echo "<a href=stream.php?songid=".$val[SongID].">Download</a><br><br><br> ";
};
?>
