<?php
session_start();

// Check if the user is logged in
if (!isset($_SESSION['username'])) {
    header("Location: login.php"); // Redirect to login if not logged in
    exit();
}

// Include the database connection file
include 'db.php'; // Ensure this file connects to your 'construction' database

// Initialize variables
$successMessage = "";
$errorMessage = "";

// Check if the form is submitted for adding a new project
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['add_project'])) {
    // Debugging line to check submitted data
    // var_dump($_POST); // Remove this line in production

    $projectName = $_POST['project_name'];
    $status = $_POST['status'];
    $deadline = $_POST['deadline'];
    $workerId = $_POST['worker_id']; // Get the assigned worker ID

    // Validate input
    if (empty($projectName) || empty($status) || empty($deadline) || empty($workerId)) {
        $errorMessage = "All fields are required.";
    } else {
        // Check if worker_id exists in the users table
        $stmt = $conn->prepare("SELECT COUNT(*) FROM users WHERE id = ?");
        $stmt->bind_param("i", $workerId);
        $stmt->execute();
        $stmt->bind_result($count);
        $stmt->fetch();
        $stmt->close();

        if ($count == 0) {
            $errorMessage = "Invalid worker ID. Please select a valid worker.";
        } else {
            // Prepare and execute the SQL statement to insert the new project
            $stmt = $conn->prepare("INSERT INTO projects (name, status, deadline, worker_id) VALUES (?, ?, ?, ?)");
            $stmt->bind_param("sssi", $projectName, $status, $deadline, $workerId);

            if ($stmt->execute()) {
                $successMessage = "Project added successfully!";
            } else {
                $errorMessage = "Error: " . $stmt->error; // This will show the specific error
            }
            $stmt->close();
        }
    }
}

// Handle form submission for adding a new team member
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['add_member'])) {
    $username = $_POST['username'];
    $email = $_POST['email'];
    $role = $_POST['role'];

    // Validate input
    if (empty($username) || empty($email) || empty($role)) {
        $errorMessage = "All fields are required.";
    } else {
        // Check if the email already exists
        $stmt = $conn->prepare("SELECT COUNT(*) FROM users WHERE email = ?");
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $stmt->bind_result($count);
        $stmt->fetch();
        $stmt->close();

        if ($count > 0) {
            $errorMessage = "This email is already in use. Please use a different email.";
        } else {
            // Prepare and execute the SQL statement to insert the new team member
            $stmt = $conn->prepare("INSERT INTO users (username, email, role) VALUES (?, ?, ?)");
            $stmt->bind_param("sss", $username, $email, $role);

            if ($stmt->execute()) {
                $successMessage = "Team member added successfully!";
            } else {
                $errorMessage = "Error: " . $stmt->error;
            }
            $stmt->close();
        }
    }
}

// Fetch projects from the database
$projects = [];
$stmt = $conn->prepare("SELECT name, status, deadline FROM projects ORDER BY created_at DESC");
$stmt->execute();
$result = $stmt->get_result();

while ($row = $result->fetch_assoc()) {
    $projects[] = $row; // Add each project to the projects array
}
$stmt->close();

// Fetch team members from the database
$teamMembers = [];
$stmt = $conn->prepare("SELECT username, role FROM users");
$stmt->execute();
$result = $stmt->get_result();

while ($row = $result->fetch_assoc()) {
    $teamMembers[] = $row; // Add each user to the team members array
}
$stmt->close();

// Fetch recent activities (latest added projects)
$recentActivities = [];
$stmt = $conn->prepare("SELECT name, status, deadline FROM projects ORDER BY created_at DESC LIMIT 5");
$stmt->execute();
$result = $stmt->get_result();

while ($row = $result->fetch_assoc()) {
    $recentActivities[] = "New project added: " . $row['name'] . " (Status: " . $row['status'] . ", Deadline: " . $row['deadline'] . ")";
}
$stmt->close();

// Fetch the number of members
$stmt = $conn->prepare("SELECT COUNT(*) FROM users");
$stmt->execute();
$stmt->bind_result($numberOfMembers);
$stmt->fetch();
$stmt->close();

// Fetch the number of activities (projects)
$stmt = $conn->prepare("SELECT COUNT(*) FROM projects");
$stmt->execute();
$stmt->bind_result($numberOfActivities);
$stmt->fetch();
$stmt->close();

// Remove the messages count if the table does not exist
// $stmt = $conn->prepare("SELECT COUNT(*) FROM messages"); // This line is removed
// $stmt->execute();
// $stmt->bind_result($numberOfMessages);
// $stmt->fetch();
// $stmt->close();

// Set a default value for messages if the table does not exist
$numberOfMessages = 0; // Default value or adjust as needed

// Fetch the user's profile picture from the database
$stmt = $conn->prepare("SELECT profile_picture FROM users WHERE id = ?");
$stmt->bind_param("i", $_SESSION['user_id']);
$stmt->execute();
$stmt->bind_result($profilePicture);
$stmt->fetch();
$stmt->close();

// Prepare data for the chart
$statusCounts = [
    'Pending' => 0,
    'In Progress' => 0,
    'Completed' => 0,
];

foreach ($projects as $project) {
    $statusCounts[$project['status']]++;
}

// Fetch the total number of team members
$stmt = $conn->prepare("SELECT COUNT(*) FROM users");
$stmt->execute();
$stmt->bind_result($totalTeamMembers);
$stmt->fetch();
$stmt->close();

// Fetch the total number of projects
$stmt = $conn->prepare("SELECT COUNT(*) FROM projects");
$stmt->execute();
$stmt->bind_result($totalProjects);
$stmt->fetch();
$stmt->close();

// Fetch the total number of completed registrations
try {
    $stmt = $conn->prepare("SELECT COUNT(*) FROM registrations WHERE status = 'Completed'");
    $stmt->execute();
    $stmt->bind_result($totalCompletedRegistrations);
    $stmt->fetch();
    $stmt->close();
} catch (mysqli_sql_exception $e) {
    $totalCompletedRegistrations = 0; // Default value if the table doesn't exist
}

// Fetch the total number of activities (if applicable)
try {
    $stmt = $conn->prepare("SELECT COUNT(*) FROM activities"); // Adjust the table and column names as necessary
    $stmt->execute();
    $stmt->bind_result($totalActivities);
    $stmt->fetch();
    $stmt->close();
} catch (mysqli_sql_exception $e) {
    $totalActivities = 0; // Default value if the table doesn't exist
}

// Initialize the variable
$numberOfPendingRegistrations = 0; // Default value

// Fetch the number of pending registrations
try {
    $stmt = $conn->prepare("SELECT COUNT(*) FROM registrations WHERE status = 'Pending'"); // Adjust the table and column names as necessary
    $stmt->execute();
    $stmt->bind_result($numberOfPendingRegistrations);
    $stmt->fetch();
    $stmt->close();
} catch (mysqli_sql_exception $e) {
    // Handle the error if the table doesn't exist
    $numberOfPendingRegistrations = 0; // Default value if the table doesn't exist
}

// Define events with dates
$events = [
    '2023-08-01' => 'Project Kickoff Meeting',
]
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Dashboard - Generic Construction Company</title>
    <link rel="stylesheet" type="text/css" href="styles.css">
    <script src="script.js" defer></script> <!-- Link to JavaScript file -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script> <!-- Include Chart.js -->
    <style>
        /* Add the CSS for the sidebar and toggle button here */
        .toggle-button {
            position: absolute;
            top: 20px;
            left: 20px;
            background-color: #007bff;
            color: white;
            border: none;
            padding: 10px;
            cursor: pointer;
            z-index: 1000; /* Ensure the button is above other elements */
        }

        .sidebar {
            width: 250px; /* Width of the sidebar */
            height: 100%; /* Full height */
            background-color: #f8f9fa; /* Light background */
            position: fixed; /* Fixed position */
            left: 0; /* Align to the left */
            top: 0; /* Align to the top */
            transition: transform 0.3s ease; /* Smooth transition */
            transform: translateX(0); /* Default position */
            z-index: 999; /* Ensure the sidebar is above other elements */
        }

        .sidebar.hidden {
            transform: translateX(-100%); /* Hide the sidebar */
        }
    </style>
</head>
<body>
    
    <nav id="sidebar" class="sidebar">
        <h2>Navigation</h2>
        <ul>
            <li><a href="dashboard.php"><i class="fas fa-tachometer-alt"></i> Dashboard</a></li>
            <li><a href="javascript:void(0);" onclick="toggleProjectSummary()"><i class="fas fa-briefcase"></i> Projects</a></li>
            <li><a href="javascript:void(0);" onclick="toggleManageInventory()"><i class="fas fa-box"></i> Manage Inventory</a></li>
            <li><a href="javascript:void(0);" onclick="toggleAddProject()"><i class="fas fa-add"></i> Add Project</a></li>
            <li><a href="javascript:void(0);" onclick="toggleTeamMembers()"><i class="fas fa-users"></i> Team</a></li>
            <li><a href="javascript:void(0);" onclick="toggleRecentActivities()"><i class="fas fa-list-alt"></i> Activities</a></li>
            <li><a href="javascript:void(0);" onclick="toggleAddTeamMember()"><i class="fas fa-user-plus"></i> Add New Member</a></li>
            <li><a href="javascript:void(0);" onclick="toggleUpdateProfile()"><i class="fas fa-user-cog"></i> Update Profile</a></li>
            <li><a href="logout.php"><i class="fas fa-sign-out-alt"></i> Logout</a></li>
        </ul>
    </nav>

    <div class="container">
        <div class="main-content">
            
            <h1>Welcome To <?php echo $_SESSION['role']; ?> Dashboard</h1>
            <p>Hello, <?php echo $_SESSION['username']; ?>!</p>
            <div class="statistics-cards">
                <div class="card card-1">
                    <h3>Pending Projects</h3>
                    <p><?php echo $statusCounts['Pending']; ?></p>
                </div>
                <div class="card card-2">
                    <h3>In Progress Projects</h3>
                    <p><?php echo $statusCounts['In Progress']; ?></p>
                </div>
                <div class="card card-3">
                    <h3>Completed Projects</h3>
                    <p><?php echo $statusCounts['Completed']; ?></p>
                </div>
                <div class="card card-4">
                    <h3>Pending Registrations</h3>
                    <p><?php echo $numberOfPendingRegistrations; ?></p>
                </div>
                <div class="card card-5">
                    <h3>Total Team Members</h3>
                    <p><?php echo $totalTeamMembers; ?></p>
                </div>
                <div class="card card-6">
                    <h3>Total Projects</h3>
                    <p><?php echo $totalProjects; ?></p>
                </div>
            </div>

            <style>
                .statistics-cards {
                    display: grid; /* Use grid layout */
                    grid-template-columns: repeat(3, 1fr); /* Three columns */
                    gap: 20px; /* Space between cards */
                    margin: 20px 0; /* Margin for spacing */
                }

                .card {
                    color: #333; /* Dark text color for readability */
                    border-radius: 15px; /* Rounded corners */
                    padding: 20px; /* Padding inside the card */
                    text-align: center; /* Center text */
                    box-shadow: 0 4px 20px rgba(0, 0, 0, 0.1); /* Subtle shadow for depth */
                    transition: transform 0.3s, box-shadow 0.3s, background-color 0.3s; /* Smooth transition for hover effect */
                    border: 1px solid #e0e0e0; /* Light border for definition */
                }
                .card:hover {
                    transform: translateY(-5px); /* Lift effect on hover */
                    box-shadow: 0 8px 30px rgba(0, 0, 0, 0.2); /* Darker shadow on hover */
                    color: #ffffff; /* Change text color to white on hover */
                }
                .card h3 {
                    margin: 10px 0 5px; /* Adjusted margin for better spacing */
                    font-size: 1.5em; /* Font size for the title */
                    color: #ffffff; /* White color for the title */
                    font-weight: bold; /* Bold font for emphasis */
                    text-shadow: 1px 1px 2px rgba(0, 0, 0, 0.3); /* Subtle text shadow for depth */
                }
                .card p {
                    font-size: 2em; /* Font size for the count */
                    margin: 0; /* Remove default margin */
                    font-weight: bold; /* Bold font for emphasis */
                    color: #333; /* Dark text color */
                }
                .card-icon {
                    font-size: 3em; /* Larger icon size */
                    color: #ffffff; /* White color for icons */
                    margin-bottom: 10px; /* Space between icon and text */
                }

                /* Unique card colors */
                .card-1 {
                    background-color: #ff9999; /* Light red */
                }
                .card-2 {
                    background-color: #99ff99; /* Light green */
                }
                .card-3 {
                    background-color: #9999ff; /* Light blue */
                }
                .card-4 {
                    background-color: #ffff99; /* Light yellow */
                }
                .card-5 {
                    background-color: #ffcc99; /* Light orange */
                }
                .card-6 {
                    background-color: #cc99ff; /* Light purple */
                }

                @media (max-width: 768px) {
                    .card {
                        width: 100%; /* Full width on smaller screens */
                        min-width: 250px; /* Minimum width for cards */
                    }
                }
            </style>       
              <!-- Add this section below the statistics cards -->
                <div class="card chart-card">
                    <h2>Time vs Project Phase Statistics</h2>
                    <canvas id="projectPhaseChart"></canvas>
                </div>
                <style>
    .chart-card {
        margin-top: 20px; /* Space above the chart card */
        padding: 20px; /* Padding inside the card */
        border-radius: 10px; /* Rounded corners */
        box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1); /* Subtle shadow for depth */
        background-color: #ffffff; /* White background for the card */
        width: 100%; /* Full width of the container */
        max-width: 800px; /* Maximum width for larger screens */
        margin-left: auto; /* Center the card horizontally */
        margin-right: auto; /* Center the card horizontally */
    }
</style>
 </div>

<!--handles project summary-->
            <section id="project-summary" class="project-summary" style="display: none;">
                <h2>Project Summary</h2>
                <table id="projectTable">
                    <thead>
                        <tr>
                            <th>Project Name</th>
                            <th>Status</th>
                            <th>Deadline</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($projects as $project): ?>
                            <tr>
                                <td><?php echo $project['name']; ?></td>
                                <td><?php echo $project['status']; ?></td>
                                <td><?php echo $project['deadline']; ?></td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </section>

<!--handles team members-->
            <section id="team-members" class="team-members" style="display: none;">
                <h2>Team Members</h2>
                <div class="team-member-list">
                    <?php foreach ($teamMembers as $member): ?>
                        <div class="team-member-card">
                            <div class="member-info">
                                <h3><?php echo $member['username']; ?></h3>
                                <p><?php echo $member['role']; ?></p>
                            </div>
                            <i class="fas fa-user-circle"></i> <!-- Example icon -->
                        </div>
                    <?php endforeach; ?>
                </div>
            </section>

<!--handles recent activities-->
            <section id="recent-activities" class="recent-activities" style="display: none;">
                <h2>Recent Activities</h2>
                <div class="activity-list">
                    <?php foreach ($recentActivities as $activity): ?>
                        <div class="activity-card">
                            <i class="fas fa-check-circle"></i> <!-- Example icon -->
                            <p><?php echo $activity; ?></p>
                        </div>
                    <?php endforeach; ?>
                </div>
            </section>

<!--handles adding new members-->
            <section id="add-team-member" class="add-team-member" style="display: none;">
                <h2>Add Team Member</h2>
                <form method="POST" action="">
                    <label for="username">Username:</label>
                    <input type="text" id="username" name="username" required>
                    
                    <label for="email">Email:</label>
                    <input type="email" id="email" name="email" required>
                    
                    <label for="role">Role:</label>
                    <select id="role" name="role" required>
                        <option value="Developer">Developer</option>
                        <option value="Designer">Designer</option>
                        <option value="Project Manager">Project Manager</option>
                        <option value="Tester">Tester</option>
                    </select>
                    
                    <input type="submit" name="add_member" value="Add Member">
                </form>
            </section>
            
<!--handles updating profile-->
          

<!--handles adding new projects-->
  <section id="add-project-section" class="add-project-section" style="display: none;">
    <h2>Add New Project</h2>
    <form method="POST" action="">
        <label for="project_name">Project Name:</label>
        <input type="text" id="project_name" name="project_name" required>

        <label for="status">Status:</label>
        <select id="status" name="status" required>
            <option value="Pending">Pending</option>
            <option value="In Progress">In Progress</option>
            <option value="Completed">Completed</option>
        </select>

        <label for="deadline">Deadline:</label>
        <input type="date" id="deadline" name="deadline" required>

        <label for="worker_id">Assign to Worker:</label>
        <select id="worker_id" name="worker_id" required>
            <?php
            // Fetch users from the database
            $stmt = $conn->prepare("SELECT id, username FROM users");
            if ($stmt->execute()) {
                $result = $stmt->get_result();
                while ($row = $result->fetch_assoc()) {
                    echo "<option value='{$row['id']}'>{$row['username']}</option>";
                }
            } else {
                echo "Error executing query: " . $stmt->error; // Error handling
            }
            $stmt->close();
            ?>
        </select>

        <input type="submit" name="add_project" value="Add Project">
    </form>
</section>
      <!-- New section for updating projects -->
<section id="update-project-section" class="update-project-section" style="display: none;">
    <h2>Update Project</h2>
    <form method="POST" action="">
        <label for="project_id">Select Project:</label>
        <select id="project_id" name="project_id" required>
            <option value="">Select a project</option>
            <?php
            // Fetch projects from the database
            $stmt = $conn->prepare("SELECT id, name FROM projects");
            $stmt->execute();
            $result = $stmt->get_result();

            while ($row = $result->fetch_assoc()) {
                echo "<option value='{$row['id']}'>{$row['name']}</option>";
            }
            $stmt->close();
            ?>
        </select>

        <label for="project_name">Project Name:</label>
        <input type="text" id="project_name" name="project_name" required>

        <label for="status">Status:</label>
        <select id="status" name="status" required>
            <option value="Pending">Pending</option>
            <option value="In Progress">In Progress</option>
            <option value="Completed">Completed</option>
        </select>

        <label for="deadline">Deadline:</label>
        <input type="date" id="deadline" name="deadline" required>

        <input type="submit" name="update_project" value="Update Project">
    </form>
</section>

<?php
// Handle project update submission
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['update_project'])) {
    $projectId = $_POST['project_id'];
    $projectName = $_POST['project_name'];
    $status = $_POST['status'];
    $deadline = $_POST['deadline'];

    // Prepare and execute the SQL statement to update the project
    $stmt = $conn->prepare("UPDATE projects SET name = ?, status = ?, deadline = ? WHERE id = ?");
    $stmt->bind_param("sssi", $projectName, $status, $deadline, $projectId);

    if ($stmt->execute()) {
        $successMessage = "Project updated successfully!";
    } else {
        $errorMessage = "Error: " . $stmt->error;
    }
    $stmt->close();
}
?>
            <!-- New section for managing inventory -->
<section id="manage-inventory" class="manage-inventory" style="display: none;">
    <h2>Manage Inventory</h2>
    <form method="POST" action="">
        <label for="item_name">Item Name:</label>
        <input type="text" id="item_name" name="item_name" required>

        <label for="quantity">Quantity:</label>
        <input type="number" id="quantity" name="quantity" required>

        <label for="price">Price:</label>
        <input type="text" id="price" name="price" required>

        <input type="submit" name="add_inventory" value="Add Item">
    </form>

    <!-- Current Inventory Table -->
<h3>Current Inventory</h3>
<table>
    <thead>
        <tr>
            <th>Item Name</th>
            <th>Quantity</th>
            <th>Price</th>
        </tr>
    </thead>
    <tbody>
        <?php
        // Fetch inventory items from the database
        $stmt = $conn->prepare("SELECT item_name, quantity, price FROM inventory");
        if ($stmt) { // Check if the statement was prepared successfully
            $stmt->execute();
            $result = $stmt->get_result();

            // Check if there are any results
            if ($result->num_rows > 0) {
                while ($row = $result->fetch_assoc()) {
                    echo "<tr>
                            <td>{$row['item_name']}</td>
                            <td>{$row['quantity']}</td>
                            <td>{$row['price']}</td>
                          </tr>";
                }
            } else {
                echo "<tr><td colspan='3'>No inventory items found.</td></tr>"; // Message if no items are found
            }
            $stmt->close();
        } else {
            echo "<tr><td colspan='3'>Error preparing statement: " . $conn->error . "</td></tr>"; // Error message if statement preparation fails
        }
        ?>
    </tbody>
</table>
<style>
    table {
        width: 100%; /* Full width of the container */
        border-collapse: collapse; /* Collapse borders for a cleaner look */
        margin-top: 20px; /* Space above the table */
        box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1); /* Subtle shadow for depth */
    }

    th, td {
        border: 1px solid #ddd; /* Light border for table cells */
        padding: 12px; /* Padding inside cells */
        text-align: left; /* Align text to the left */
    }

    th {
        background-color: #f2f2f2; /* Light gray background for header */
        color: #333; /* Dark text color for header */
        font-weight: bold; /* Bold font for header */
    }

    tr:nth-child(even) {
        background-color: #f9f9f9; /* Light background for even rows */
    }

    tr:hover {
        background-color: #e0e0e0; /* Change background on hover */
        cursor: pointer; /* Pointer cursor on hover */
    }

    /* Responsive adjustments */
    @media (max-width: 768px) {
        table {
            font-size: 14px; /* Smaller font size for mobile */
        }

        th, td {
            padding: 8px; /* Reduced padding for mobile */
        }
    }
</style>
<?php
// Handle inventory item submission
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['add_inventory'])) {
    $itemName = $_POST['item_name'];
    $quantity = $_POST['quantity'];
    $price = $_POST['price'];

    // Prepare and execute the SQL statement to add the inventory item
    $stmt = $conn->prepare("INSERT INTO inventory (item_name, quantity, price) VALUES (?, ?, ?)");
    $stmt->bind_param("sis", $itemName, $quantity, $price);

    if ($stmt->execute()) {
        $successMessage = "Item added to inventory successfully!";
    } else {
        $errorMessage = "Error: " . $stmt->error;
    }
    $stmt->close();
}
?>
<!-- Container to load reset_profile.php -->
<div id="update-profile-container" style="display: none;">
    <!-- You can use an iframe to load the content -->
    <iframe src="reset_profile.php" style="width: 100%; height: 600px; border: none;"></iframe>
</div>



            <footer>
                <p>&copy; <?php echo date("Y"); ?> Generic Construction Company</p>
            </footer>
        </div>
    </div>

    
    <script>
         // Sample data for the chart (you can replace this with dynamic data from your database)
    const labels = ['January', 'February', 'March', 'April', 'May', 'June', 'July'];
    const data = {
        labels: labels,
        datasets: [
            {
                label: 'Planning',
                data: [12, 19, 3, 5, 2, 3, 7],
                borderColor: 'rgba(255, 99, 132, 1)',
                backgroundColor: 'rgba(255, 99, 132, 0.2)',
                fill: true,
            },
            {
                label: 'Development',
                data: [2, 3, 20, 5, 1, 4, 8],
                borderColor: 'rgba(54, 162, 235, 1)',
                backgroundColor: 'rgba(54, 162, 235, 0.2)',
                fill: true,
            },
            {
                label: 'Testing',
                data: [3, 10, 13, 15, 22, 30, 25],
                borderColor: 'rgba(75, 192, 192, 1)',
                backgroundColor: 'rgba(75, 192, 192, 0.2)',
                fill: true,
            },
            {
                label: 'Deployment',
                data: [1, 2, 1, 1, 1, 1, 1],
                borderColor: 'rgba(255, 206, 86, 1)',
                backgroundColor: 'rgba(255, 206, 86, 0.2)',
                fill: true,
            }
        ]
    };

    const config = {
        type: 'line',
        data: data,
        options: {
            responsive: true,
            plugins: {
                legend: {
                    position: 'top',
                },
                title: {
                    display: true,
                    text: 'Time vs Project Phases'
                }
            },
            scales: {
                y: {
                    beginAtZero: true,
                    title: {
                        display: true,
                        text: 'Number of Projects'
                    }
                },
                x: {
                    title: {
                        display: true,
                        text: 'Time (Months)'
                    }
                }
            }
        },
    };

    // Render the chart
    const projectPhaseChart = new Chart(
        document.getElementById('projectPhaseChart'),
        config
    );
        //handles project.......
    function toggleProjectSummary() {
        var projectSummary = document.getElementById('project-summary');
        if (projectSummary.style.display === 'none' || projectSummary.style.display === '') {
            projectSummary.style.display = 'block'; // Show the project summary
        } else {
            projectSummary.style.display = 'none'; // Hide the project summary
        }
    }
//hands the teams ..............
    function toggleTeamMembers() {
        var teamMembersSection = document.getElementById('team-members');
        if (teamMembersSection.style.display === 'none' || teamMembersSection.style.display === '') {
            teamMembersSection.style.display = 'block'; // Show the team members
        } else {
            teamMembersSection.style.display = 'none'; // Hide the team members
        }
    }
//handle Activities
    function toggleRecentActivities() {
        var recentActivitiesSection = document.getElementById('recent-activities');
        if (recentActivitiesSection.style.display === 'none' || recentActivitiesSection.style.display === '') {
            recentActivitiesSection.style.display = 'block'; // Show the recent activities
        } else {
            recentActivitiesSection.style.display = 'none'; // Hide the recent activities
        }
    }
//handles adding new members
    function toggleAddTeamMember() {
        var addTeamMemberSection = document.getElementById('add-team-member');
        if (addTeamMemberSection.style.display === 'none' || addTeamMemberSection.style.display === '') {
            addTeamMemberSection.style.display = 'block'; // Show the add team member section
        } else {
            addTeamMemberSection.style.display = 'none'; // Hide the add team member section
        }
    }


function toggleAddProject() {
    var projectSection = document.getElementById('add-project-section');
    if (projectSection.style.display === 'none' || projectSection.style.display === '') {
        projectSection.style.display = 'block'; // Show the project section
    } else {
        projectSection.style.display = 'none'; // Hide the project section
    }
}
function toggleUpdateProfile() {
    var updateProfilesSection = document.getElementById('update-profile');
    if (updatePprofileSection.style.display === 'none' || updateProfileSection.style.display === '') {
        updateProfileSection.style.display = 'block'; // Show the  update profile section
    } else {
        updateProfileSection.style.display = 'none'; // Hide the update profile section
    }
}

    // Function to toggle the visibility of the Manage Inventory section
    function toggleManageInventory() {
        var inventorySection = document.getElementById('manage-inventory');
        if (inventorySection.style.display === 'none' || inventorySection.style.display === '') {
            inventorySection.style.display = 'block'; // Show the manage inventory section
        } else {
            inventorySection.style.display = 'none'; // Hide the manage inventory section
        }
    }

    //handles updating profile
    function toggleUpdateProfile() {
        var updateProfilesSection = document.getElementById('update-profile-container');
        if (updateProfilesSection.style.display === 'none' || updateProfilesSection.style.display === '') {
            updateProfilesSection.style.display = 'block'; // Show the update profile section
        } else {
            updateProfilesSection.style.display = 'none'; // Hide the update profile section
        }
    }

</script>

<!-- Display success or error messages -->
<?php if (!empty($successMessage)): ?>
    <script>
        alert("<?php echo $successMessage; ?>");
    </script>
<?php endif; ?>

<?php if (!empty($errorMessage)): ?>
    <script>
        alert("<?php echo $errorMessage; ?>");
    </script>
<?php endif; ?>
</body>
</html>
