<?php

$host = 'localhost';
$username = 'root';
$pass = '';
$dbName = 'login_system';

// establish database connection
$conn = mysqli_connect($host, $username, $pass, $dbName);

// verify connection
if (!$conn) {
    die('Failed to connect to database: ' . mysqli_connect_error());
}

?>