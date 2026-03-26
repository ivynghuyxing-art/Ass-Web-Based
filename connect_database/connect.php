<?php
$host = "localhost";
$user = "root";
$password = "";
$database = "stationary_shop"; 
$conn = new mysqli($host, $user, $password, $database);

//check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

echo "Connected successfully!";
?>