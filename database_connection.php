<?php
$host = "noveldb.crusyaucwrfk.ap-southeast-1.rds.amazonaws.com";
$port = 3306;
$dbname = "noveldb";
$username = "admin";
$password = "password-NovelDB";
$conn = new mysqli($host, $username, $password, $dbname, $port);    
if ($conn->connect_error) {
    die("dbconnect failed" . $conn->connect_error);
}
?>
