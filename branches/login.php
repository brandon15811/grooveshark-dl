<?php 
include 'newexec.php';
if ($_SESSION['loggedin']) {
	echo "You are logged in";
	exit;
}
?>
<?php
if (isset($_POST['userone']) and isset($_POST['passtwo'])) {
$user = $_POST['userone'];
$pass = $_POST['passtwo'];
$userID = login($user, $pass);

$userID = json_decode($userID, true);
if (@$userID['fault']['code'] == 32) {
    echo "Wrong password";
} elseif (@$userID['fault']['code'] == 4096) {
    echo "Wrong Username";
} elseif (isset($userID['fault'])) {
    echo "Unknown error";
} else {
   	$_SESSION['loggedin'] = true;
   	$userID = $userID['result']['userID'];
	// echo $userID;
	$_SESSION['userID'] = $userID;
	$file1 = str_replace("/login.php", "", $_SERVER['PHP_SELF']);
	header("Location: http://".$_SERVER['HTTP_HOST'].$file1."/index.php");
	exit;
}
}
?>
<html>
<head>
<script src="md5.js" type="text/javascript"></script>
</head>
<body>
<p>
Your password will be encrypted before being sent over the internet. <br>
Javascript must be enabled for login to work. <br>
If you do not have an account, go to grooveshark.com and register one<br>
</p>
<form name="one" onsubmit="document.forms['one']['passone'].value=''" method='post'>
Username:
<input type="text" name="userone" />
<br>
Password:
<input type="password" name="passone" onkeyup="document.forms['one']['passtwo'].value=hex_md5(document.forms['one']['passone'].value)" />
<input type="hidden" name="passtwo" value=""/>
<br>
<input type="submit" value="Submit" />
</form>