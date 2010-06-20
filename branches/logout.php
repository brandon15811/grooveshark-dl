<?php
session_start();
unset($_SESSION['loggedin']);
echo "You are logged out";
?>
