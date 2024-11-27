<?php
// Include database connection
include 'db.php'; // Ensure this file connects to your database

// Check if the form is submitted
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['add_project'])) {
    $projectName = $_POST['project_name'];
    $status = $_POST['status'];
    $deadline = $_POST['deadline'];
    
    // Retrieve the user ID from the form submission
    $userId = $_POST['user_id']; // Ensure this matches the name in the form

    // Validate input
    if (empty($projectName) || empty($status) || empty($deadline) || empty($userId)) {
        echo "All fields are required.";
        exit();
    }

    // Prepare and execute the SQL statement to insert the new project
    $stmt = $conn->prepare("INSERT INTO projects (name, status, deadline, user_id) VALUES (?, ?, ?, ?)");
    $stmt->bind_param("sssi", $projectName, $status, $deadline, $userId);

    // Execute the statement and check for errors
    if ($stmt->execute()) {
        echo "Project added successfully!";
    } else {
        echo "Error executing query: " . $stmt->error; // Error handling
    }
    $stmt->close();
}
?>