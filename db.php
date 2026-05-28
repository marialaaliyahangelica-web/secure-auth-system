<?php
$host = "sql300.infinityfree.com";
$dbname = "if0_42032934_secureauth";
$username = "if0_42032934";
$password = "M2wDEkr4Te2";

$conn = new mysqli($host, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Database connection failed: " . $conn->connect_error);
}
?>