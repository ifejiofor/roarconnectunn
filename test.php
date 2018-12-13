<?php
$globalHandleToDatabase = mysqli_connect('127.0.0.1', 'roartfce_hero', 'hero97', 'roartfce_roar_data') or
   die( '<p style="color: red; font-family: serif;">Unable to connect to database.</p>' );

$query = 'SELECT id FROM users';
$result = mysqli_query($globalHandleToDatabase, $query) or die('<p style="color: red; font-family: serif;">Unable to query database.</p>'.$query);
echo 'Number of rows: ' . mysqli_num_rows($result);

$query = 'INSERT INTO cached_users (id, date_of_last_visit) VALUES';

for ($row = mysqli_fetch_assoc($result); $row != NULL; $row = mysqli_fetch_assoc($result)) {
    $query .= ' ("' . $row['id'] . '", "2018-01-01"),';
}

$query[strlen($query) - 1] = ' ';

mysqli_query($globalHandleToDatabase, $query) or die('<p style="color: red; font-family: serif;">Unable to query database.</p>'.$query);
echo '<br/>Done';
?>