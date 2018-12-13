<?php
ob_start();
//error_reporting(0);

$globalHandleToDatabase = mysqli_connect('127.0.0.1', 'roartfce_hero', 'hero97', 'roartfce_roar_data') or
   die( '<p style="color: red; font-family: serif;">Unable to connect to database.</p>' );
$globalDatabaseErrorMarkup = '<p style="color: red; font-family: serif;">Unable to query database.</p>';
$globalBlogPostsDisplayedInCurrentPage = array();

session_start();
if (isset($_SESSION['user_id'])) {
   $query = 'UPDATE cached_users SET date_of_last_visit = CURRENT_DATE() WHERE id = "' . $_SESSION['user_id'] . '"';
   mysqli_query($globalHandleToDatabase, $query) or die($globalDatabaseErrorMarkup.$query);
}
else {
   $query = 'INSERT INTO cached_users (date_of_last_visit) VALUES (CURRENT_DATE())';
   mysqli_query($globalHandleToDatabase, $query) or die($globalDatabaseErrorMarkup.$query);
   $_SESSION['user_id'] = mysqli_insert_id($globalHandleToDatabase);
}
?>


