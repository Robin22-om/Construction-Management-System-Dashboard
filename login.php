<?php
session_start();
$error = ""; // Initialize error message variable
$successMessage = ""; // Initialize success message variable

// Include the database connection file
include 'db.php'; // Ensure this file connects to your 'construction' database

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Handle login logic here
    $username = $_POST['username'];
    $password = $_POST['password'];

    // Prepare and execute the SQL statement to fetch user data
    $stmt = $conn->prepare("SELECT password, role FROM users WHERE username = ?");
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $stmt->store_result();
    
    // Check if the user exists
    if ($stmt->num_rows > 0) {
        $stmt->bind_result($hashedPassword, $role);
        $stmt->fetch();

        // Verify the password
        if (password_verify($password, $hashedPassword)) {
            $_SESSION['username'] = $username; // Store username in session
            $_SESSION['role'] = $role; // Store role in session
            $successMessage = "Login successful! Welcome, $username.";
            header("Location: dashboard.php"); // Redirect to dashboard
            exit();
        } else {
            $error = "Invalid password. Please try again.";
        }
    } else {
        $error = "No account found with that username. Don't have an account? <a href='signup.php'>Sign up here</a>";
    }
    $stmt->close();
}

$conn->close(); // Close the database connection
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Login</title>
<link rel="stylesheet" type="text/css" href="log_styles.css">
</head>
<body>
    <div class="login-form">
        <h2>Login</h2>
        <form method="POST" action="">
            <label for="username">Username:</label>
            <input type="text" id="username" name="username" required>
            <br>
            <label for="password">Password:</label>
            <input type="password" id="password" name="password" required>
            <br>
            <input type="submit" value="Login">
        </form>
        <?php if ($error) echo "<p class='error'>$error</p>"; ?>
        <?php if ($successMessage) echo "<p class='success'>$successMessage</p>"; ?>
        <p>
            Don't have an account? <a href="signup.php">Sign up here</a>
        </p>
    </div>
</body>
</html>
