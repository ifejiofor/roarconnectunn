<?php
ob_start();
//error_reporting(0);

$db = mysqli_connect('127.0.0.1', 'roartfce_hero', 'hero97', 'roartfce_roar_data') or
   die( '<p style="color: red; font-family: serif;">Unable to connect to database.</p>' );
$markupIndicatingDatabaseQueryFailure = '<p style="color: red; font-family: serif;">Unable to query database.</p>';

session_start();
if (isset($_SESSION['user_id'])) {
   $query = 'UPDATE cached_users SET date_of_last_visit = CURRENT_DATE() WHERE id = "' . $_SESSION['user_id'] . '"';
   mysqli_query($db, $query) or die($markupIndicatingDatabaseQueryFailure.$query);
}
else {
   $query = 'INSERT INTO cached_users (date_of_last_visit) VALUES (CURRENT_DATE())';
   mysqli_query($db, $query) or die($markupIndicatingDatabaseQueryFailure.$query);
   $_SESSION['user_id'] = mysqli_insert_id($db);
}
?>


