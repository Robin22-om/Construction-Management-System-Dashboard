<?php
session_start();
include 'db.php'; // Ensure this file connects to your 'construction' database

if (!isset($_SESSION['username']) || $_SESSION['role'] !== 'admin') {
    header("Location: login.php"); // Redirect to login if not logged in or not an admin
    exit();
}

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['username'])) {
    $usernameToRemove = $_POST['username'];

    // Prepare and execute the SQL statement to remove the user
    $stmt = $conn->prepare("DELETE FROM users WHERE username = ?");
    $stmt->bind_param("s", $usernameToRemove);
    if ($stmt->execute()) {
        header("Location: reset_profile.php"); // Redirect back to the profile reset page
        exit();
    } else {
        echo "Error removing member: " . $stmt->error;
    }
    $stmt->close();
}
?>