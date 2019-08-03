<?php

$servername = "wcsdb.c244jcopnv7v.eu-west-3.rds.amazonaws.com";
$username = "admin";
$password = "adminWCSDB";
$dbname = "wcsdb";

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);
// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
} 
