<?php
session_start();
#include "notice.html";
?>
<br><br>
<form name="input" action="search.php" method="get"> Search: <input type="text" name="name" />
<input type="submit" value="Search" />
</form>
<br>
<!-- <form name="input" action="playlist.php" method="get"> Playlist ID: <input type="text" name="playlist" /> 
<input type="submit" value="Go" /> 
</form> -->
<a href="popular.php">Popular Songs</a>

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
