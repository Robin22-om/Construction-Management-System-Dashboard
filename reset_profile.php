<?php
session_start();

// Check if the user is logged in and is an admin
if (!isset($_SESSION['username']) || $_SESSION['role'] !== 'admin') {
    header("Location: login.php"); // Redirect to login if not logged in or not an admin
    exit();
}

// Include the database connection file
include 'db.php'; // Ensure this file connects to your 'construction' database

// Initialize variables
$successMessage = "";
$errorMessage = "";

// Handle form submission for resetting username, password, and email
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $newUsername = $_POST['new_username'];
    $newPassword = $_POST['new_password'];
    $newEmail = $_POST['new_email'];
    $currentUsername = $_SESSION['username']; // Get the current username from the session

    // Validate input
    if (empty($newUsername) && empty($newPassword) && empty($newEmail)) {
        $errorMessage = "Please enter a new username, password, or email.";
    } else {
        // Update username if provided
        if (!empty($newUsername)) {
            $stmt = $conn->prepare("UPDATE users SET username = ? WHERE username = ?");
            $stmt->bind_param("ss", $newUsername, $currentUsername);
            if ($stmt->execute()) {
                $_SESSION['username'] = $newUsername; // Update session with new username
                $successMessage = "Username updated successfully!";
            } else {
                $errorMessage = "Error updating username: " . $stmt->error;
            }
            $stmt->close();
        }

        // Update password if provided
        if (!empty($newPassword)) {
            // Hash the new password for security
            $hashedPassword = password_hash($newPassword, PASSWORD_DEFAULT);
            $stmt = $conn->prepare("UPDATE users SET password = ? WHERE username = ?");
            $stmt->bind_param("ss", $hashedPassword, $currentUsername);
            if ($stmt->execute()) {
                $successMessage = "Password updated successfully!";
            } else {
                $errorMessage = "Error updating password: " . $stmt->error;
            }
            $stmt->close();
        }

        // Update email if provided
        if (!empty($newEmail)) {
            $stmt = $conn->prepare("UPDATE users SET email = ? WHERE username = ?");
            $stmt->bind_param("ss", $newEmail, $currentUsername);
            if ($stmt->execute()) {
                $successMessage = "Email updated successfully!";
            } else {
                $errorMessage = "Error updating email: " . $stmt->error;
            }
            $stmt->close();
        }
    }
}

// Fetch all team members for management
$teamMembers = [];
$stmt = $conn->prepare("SELECT username, email FROM users WHERE role != 'admin'");
$stmt->execute();
$result = $stmt->get_result();
while ($row = $result->fetch_assoc()) {
    $teamMembers[] = $row;
}
$stmt->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Reset Profile - Generic Construction Company</title>
    <link rel="stylesheet" type="text/css" href="profile_styles.css">
</head>
<body>
<section id="update-profile" class="update-profile" style="display: block;">
    <h2>Update Profile</h2>
    <div class="form-container">
        <?php if (isset($successMessage)): ?>
            <p class="success"><?php echo $successMessage; ?></p>
        <?php endif; ?>
        <?php if (isset($errorMessage)): ?>
            <p class="error"><?php echo $errorMessage; ?></p>
        <?php endif; ?>
        <form method="POST" action="" enctype="multipart/form-data">
            <label for="new_username">New Username:</label>
            <input type="text" id="new_username" name="new_username" placeholder="Enter new username">
            
            <label for="new_password">New Password:</label>
            <input type="password" id="new_password" name="new_password" placeholder="Enter new password">
            
            <label for="new_email">New Email:</label>
            <input type="email" id="new_email" name="new_email" placeholder="Enter new email">
            
            <input type="submit" value="Update">
        </form>
    </div>
</section>

<!-- Manage Team Members Section -->
<section id="manage-team" class="manage-team" style="display: block;">
    <h2>Manage Team Members</h2>
    <table>
        <thead>
            <tr>
                <th>Username</th>
                <th>Email</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($teamMembers as $member): ?>
                <tr>
                    <td><?php echo htmlspecialchars($member['username']); ?></td>
                    <td><?php echo htmlspecialchars($member['email']); ?></td>
                    <td>
                        <form method="POST" action="remove_member.php">
                            <input type="hidden" name="username" value="<?php echo htmlspecialchars($member['username']); ?>">
                            <input type="submit" value="Remove">
                        </form>
                    </td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</section>

</body>
</html>