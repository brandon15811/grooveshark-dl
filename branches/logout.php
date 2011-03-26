<?php
session_start();
unset($_SESSION['loggedin']);
unset($_SESSION['sessionid']);
$file1 = str_replace("/logout.php", "", $_SERVER['PHP_SELF']);
header("Location: http://".$_SERVER['HTTP_HOST'].$file1."/index.php");
//echo "You are logged out";
?>