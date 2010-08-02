<?php 
include 'newexec.php';
if ($_SESSION['loggedin']) {
	echo "You are logged in";
	exit;
}
?>
<?php
if (isset($_POST['usertwo'])) {
$user = $_POST['usertwo'];
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
<form name="one" onkeyup='whichButton(event)'>
Username:
<input type="text" name="userone" onkeyup="document.forms['input']['usertwo'].value=document.forms['one']['userone'].value"/>
<br>
Password:
<input type="password" name="passone" onkeyup="document.forms['input']['passtwo'].value=hex_md5(document.forms['one']['passone'].value)" />
</form>
<form name="input" action="login.php" method="post" id='formtwo'>
<input type="submit" value="Submit" />
<input type="hidden" name="usertwo" value="" />
<input type="hidden" name="passtwo" value=""/>

</form>