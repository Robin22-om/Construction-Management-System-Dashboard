<?php
session_start();
$adminsRegistered = 0; // Placeholder for counting registered admins
$successMessage = ""; // Initialize success message variable
$error = ""; // Initialize error message variable

// Include the database connection file
include 'db.php'; // Ensure this file connects to your 'construction' database

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = $_POST['username'];
    $password = $_POST['password'];
    $confirmPassword = $_POST['confirm_password'];
    $email = $_POST['email'];
    $role = $_POST['role'];

    // Validate email format
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error = "Invalid email format.";
    } elseif ($password !== $confirmPassword) {
        $error = "Passwords do not match.";
    } else {
        // Check if the email already exists
        $stmt = $conn->prepare("SELECT COUNT(*) FROM users WHERE email = ?");
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $stmt->bind_result($emailCount);
        $stmt->fetch();
        $stmt->close();

        if ($emailCount > 0) {
            $error = "Email is already registered. Please use a different email.";
        } else {
            // Check if the role is admin and limit to 2 admins
            if ($role == 'admin') {
                // Check the number of registered admins
                $result = $conn->query("SELECT COUNT(*) as count FROM users WHERE role='admin'");
                $row = $result->fetch_assoc();
                $adminsRegistered = $row['count'];

                if ($adminsRegistered >= 2) {
                    $error = "Only 2 admins can register.";
                } else {
                    // Insert into database
                    $stmt = $conn->prepare("INSERT INTO users (username, password, email, role) VALUES (?, ?, ?, ?)");
                    $hashedPassword = password_hash($password, PASSWORD_DEFAULT); // Hash the password
                    $stmt->bind_param("ssss", $username, $hashedPassword, $email, $role);
                    if ($stmt->execute()) {
                        $successMessage = "Registration successful! Welcome, $username. You can now <a href='login.php'>login here</a>";
                    } else {
                        $error = "Error: " . $stmt->error;
                    }
                    $stmt->close();
                }
            } else {
                // Insert into database for other roles
                $stmt = $conn->prepare("INSERT INTO users (username, password, email, role) VALUES (?, ?, ?, ?)");
                $hashedPassword = password_hash($password, PASSWORD_DEFAULT); // Hash the password
                $stmt->bind_param("ssss", $username, $hashedPassword, $email, $role);
                if ($stmt->execute()) {
                    $successMessage = "Registration successful! Welcome, $username. You can now <a href='login.php'>login here</a>";
                } else {
                    $error = "Error: " . $stmt->error;
                }
                $stmt->close();
            }
        }
    }
}

$conn->close(); // Close the database connection
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Signup</title>
    <link rel="stylesheet" type="text/css" href="sign_styles.css">
</head>
<body>
    <div class="container">
        <h2>Signup</h2>
        <form method="POST" action="">
            <label for="username">Username:</label>
            <input type="text" id="username" name="username" required>
            <label for="email">Email:</label>
            <input type="email" id="email" name="email" required>
            <label for="password">Password:</label>
            <input type="password" id="password" name="password" required>
            <label for="confirm_password">Confirm Password:</label>
            <input type="password" id="confirm_password" name="confirm_password" required>
            <label for="role">Select Role:</label>
            <select id="role" name="role" required>
                <option value="project_manager">Project Manager</option>
                <option value="site_supervisor">Site Supervisor</option>
                <option value="estimator">Estimator</option>
                <option value="architect">Architect</option>
                <option value="engineer">Engineer</option>
                <option value="laborer">Laborer</option>
                <option value="safety_officer">Safety Officer</option>
                <option value="admin">Admin</option>
            </select>
            <input type="submit" value="Register">
        </form>
        <?php if ($error) echo "<p class='error'>$error</p>"; ?>
        <?php if ($successMessage) echo "<p class='success'>$successMessage</p>"; ?>
    </div>
</body>
</html>
