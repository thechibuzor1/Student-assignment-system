<?php
$host = 'localhost';
$username = 'your_username';
$password = 'your_password';
$database = 'your_database';

$mysqli = new mysqli($host, $username, $password, $database);

if ($mysqli->connect_error) {
    die('Connection Error (' . $mysqli->connect_errno . '): ' . $mysqli->connect_error);
}
?>