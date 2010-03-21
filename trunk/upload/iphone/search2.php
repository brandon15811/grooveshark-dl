<?php
include 'exec.php';
echo '<form name="input" action="search.php" method="get"> Search: <input type="text" name="name" /> <input type="submit" value="Search" /> </form> ';
echo "<br><br>";
if (isset($_GET['name'])) {
$searchdata = search($_GET['name']);
$songlist = json_decode($searchdata, true);
foreach($songlist[result] as $val) {
    echo "Song: ".$val[Name]."<br>".
    "Artist: ".$val[ArtistName]."<br>";
	echo "<a href=stream.php?songid=".$val[SongID].">Download</a><br><br> ";
}
                          }
?>
