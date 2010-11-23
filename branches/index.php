<?php
session_start();
include "header.php";
#include "notice.html";
?>
<form name="input" action="search.php" method="get"> Search: <input type="text" name="name" />
<select name="type">
<option value="songs" selected="selected">Songs</option>
<option value="artists">Artists</option>
<option value="albums">Albums</option>
</select>
<input type="submit" value="Search" />
</form>
<br>
<?php
#<form name="input" action="playlist.php" method="get"> Playlist ID: <input type="text" name="id" /> 
if (@!$_SESSION['loggedin']) {
	echo "You must be logged in to see Playlists";
} else {
	echo "<a href="playlist.php">Playlists</a><br><br>";
}
?>
<a href="popular.php?type=songs">Popular Songs</a><br><br>


<!-- Start 1FreeCounter.com code -->
  
  <script language="JavaScript">
  var data = '&r=' + escape(document.referrer)
	+ '&n=' + escape(navigator.userAgent)
	+ '&p=' + escape(navigator.userAgent)
	+ '&g=' + escape(document.location.href);

  if (navigator.userAgent.substring(0,1)>'3')
    data = data + '&sd=' + screen.colorDepth 
	+ '&sw=' + escape(screen.width+'x'+screen.height);

  document.write('<a href="http://www.1freecounter.com/stats.php?i=53197" target=\"_blank\" >');
  document.write('<img alt="Free Counter" border=0 hspace=0 '+'vspace=0 src="http://www.1freecounter.com/counter.php?i=53197' + data + '">');
  document.write('</a>');
  </script>

<!-- End 1FreeCounter.com code -->
