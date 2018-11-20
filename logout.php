<?php   
session_start();
$_SESSION['loginStatus'] = 'loggedout';
header("location: index.php");
?> 