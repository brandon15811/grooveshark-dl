<?php include 'newexec.php'; 
function html(){
echo '<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">

<head>
<meta content="yes" name="apple-mobile-web-app-capable" />
<meta name="apple-mobile-web-app-status-bar-style" content="default"/>
<meta content="text/html; charset=iso-8859-1" http-equiv="Content-Type" />
<meta content="minimum-scale=1.0, width=device-width, maximum-scale=0.6667, user-scalable=no" name="viewport" />
<link href="css/style.css" rel="stylesheet" media="screen" type="text/css" />
<script src="javascript/functions.js" type="text/javascript"></script>
<script src="../md5.js" type="text/javascript"></script>
<title>Grooveshark</title>
<meta content="keyword1,keyword2,keyword3" name="keywords" />
<meta content="Description of your page" name="description" />
</head>

<body>

<div id="topbar">
<div id="title">Grooveshark</div>';
if ($_SESSION['loggedin']) {
	echo '<div id="bluerightbutton"><a href="logout.php">Logout</a></div>';
} else {
	echo '<div id="bluerightbutton"><a href="login.php">Login</a></div>';
}
echo '<div id="leftnav">
<a href="index.php"><img alt="home" src="images/home.png" /></a></div>
</div>
<div class="searchbox"><form action="search.php" method="get"><fieldset><input
id="search" placeholder="Search" type="text" name="name" /><input id="submit"
type="hidden" /></fieldset></form></div>

<div id="content">
	<span class="graytitle">Login(Account At grooveshark.com Required)</span>
		<ul class="pageitem">';
		
}
		 
		if ($_SESSION['loggedin']) {
			html();
			echo "You are logged in";
			exit;
		}
if (isset($_POST['userone']) and isset($_POST['passtwo'])) {
	if (empty($_POST['userone']) or empty($_POST['passtwo']))
	{
		echo "Username and/or Password missing";
	} else {
		$user = $_POST['userone'];
		$pass = $_POST['passtwo'];
		$userID = authenticateUser($user, $pass);
		$userID = json_decode($userID, true);
		if (@$userID['errors']['0']['code'] == 200) {
			echo "Wrong Username";
		} elseif (@$userID['result']['UserID'] == 0) {
			echo "Wrong Username and/or Password";
		} elseif (isset($userID['errors'])) {
			echo "Unknown error";
		} else {
			$_SESSION['loggedin'] = true;
			$userID = $userID['result']['UserID'];
			$_SESSION['userID'] = $userID;
			$file1 = str_replace("/login.php", "", $_SERVER['PHP_SELF']);
			header("Location: http://".$_SERVER['HTTP_HOST'].$file1."/index.php");
			exit;
		}
	}
}
html();
?>
			<form name="one" action="login.php" method="post" onsubmit="document.forms['one']['passone'].value=''">
				<li class="bigfield"><input name="userone" placeholder="Username" type="text" /></li>
				<li class="bigfield"><input name="passone" placeholder="Password" type="password" onkeyup="document.forms['one']['passtwo'].value=hex_md5(document.forms['one']['passone'].value)" /></li>
				<input type="hidden" name="passtwo" value=""/>
				<li class="button"><input name="name" type="submit" value="Submit input" /></li> 
			</form>
		</ul>
</div>

<div id="footer">
	<!-- Support iWebKit by sending us traffic; please keep this footer on your page, consider it a thank you for our work :-) -->
	<a class="noeffect" href="http://iwebkit.net">Powered by iWebKit</a></div>
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
</body>

</html>
