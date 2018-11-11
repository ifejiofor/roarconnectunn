<?php
error_reporting(0);

$markupIndicatingDatabaseQueryFailure = '
   <p style="color: red; font-family: serif;">Unable to query database.</p>
';

$host='127.0.0.1';
$username='roartfce_hero';
$database='roartfce_roar_data';
$password='hero97';
$db=mysqli_connect($host, $username, $password, $database)or die( $markupIndicatingDatabaseQueryFailure );
?>


