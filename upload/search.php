<?php
include 'exec.php';
echo '<form name="input" action="search.php" method="get"> Search: <input type="text" name="name" /> <input type="submit" value="Search" /> </form> ';
echo "<br><br>";
if (isset($_GET['name'])) {
$searchdata = search($_GET['name']);
$songlist = json_decode($searchdata, true);
#echo $songlist[result][Songs][SongName][0];
foreach($songlist[result] as $val) {
	#$formmvalue = str_shuffle($val[Name]);
	#$formvalue = str_replace(" ", "", "$formmvalue");
    echo "Song:".$val[Name]."<br><br>".
    "Artist:".$val[ArtistName]."<br><br>";
	#$streamjson = streamKey($val[SongID]);
	#$streamdata = json_decode($streamjson, true);
	#$buttonurl = $streamdata[result][result][streamServer];
	#$buttonkey = $streamdata[result][result][streamKey];
	echo "<a href=stream.php?songid=".$val[SongID].">Download</a><br><br><br> ";
	#echo "<a href=".$val[SongID].".mp3>Download</a><br><br><br> ";
}
                          }
?>
