<?php
$servername = "localhost";
$usernameDB = "root";
$passwordDB = "";
$dbname = "construction";

// Create connection
$conn = new mysqli($servername, $usernameDB, $passwordDB, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
?>
