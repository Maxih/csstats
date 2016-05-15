<?php
require_once("./config.php");

// Create connection
$mysqlConn = new mysqli($config["dbhost"], $config["dbuser"], $config["dbpass"]);

// Check connection
if ($mysqlConn->connect_error) {
    die("Connection failed: " . $mysqlConn->connect_error);
}

$mysqlConn->select_db($config["dbname"]);
 ?>
