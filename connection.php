<?php
// Include configuration file
require_once 'config.php';

// Create connection using config.php settings
$connection = new mysqli(
    CONFIG['db_host'],
    CONFIG['db_user'],
    CONFIG['db_password'],
    CONFIG['db_name']
);

// Check connection
if ($connection->connect_error) {
    die("Connection failed: " . $connection->connect_error);
}

// Set character set to utf8
$connection->set_charset("utf8");

// Return the connection object
?>
