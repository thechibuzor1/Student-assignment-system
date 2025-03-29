<?php
$host = 'localhost';
$username = 'root';
$password = '';
$database = 'assignment';

$connection = mysqli_connect($host, $username, $password, $database);

if (!$connection) {
    die("Database connection failed: " . mysqli_connect_error());
}

define('SMTP_USER', 'noreplycsc415grp3@gmail.com');
define('SMTP_PASS', 'tsqo vzst jmpa zblp'); 
?>