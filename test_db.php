<?php
$connection = mysqli_connect('localhost', 'root', '', 'manajemen_laboratorium');
if (!$connection) {
    die('Connection failed: ' . mysqli_connect_error());
} else {
    echo 'Database connected successfully!';
}
mysqli_close($connection);
?>