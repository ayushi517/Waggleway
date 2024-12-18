<?php
// Start the session
session_start();

// Check if the user is logged in
if (!isset($_SESSION['userid'])) {
    // If not logged in, redirect to the login page
    header("Location: login.php");
    exit();
}

// Get the user ID from the session
$userId = $_SESSION['userid'];

// Debug the session to check userId
echo "Session User ID in Welcome Page: " . $userId . "<br>";
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Welcome to Waggle Way</title>
    <link rel="stylesheet" href="css/style.css">
    <style>
        .welcome-container {
            margin-top: 50px;
            text-align: center;
        }
        .welcome-message {
            font-size: 24px;
            margin-bottom: 20px;
            color: #333;
        }
        .navigation-buttons {
            margin-top: 20px;
        }
        .nav-button {
            padding: 10px 20px;
            background-color: #4CAF50;
            color: white;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            font-size: 16px;
            text-decoration: none;
            margin: 5px;
        }
        .nav-button:hover {
            background-color: #45a049;
        }
    </style>
</head>
<body>
    <div class="welcome-container">
        <div class="welcome-message">
            Welcome to Waggle Way, User ID: <?php echo htmlspecialchars($userId); ?>!
        </div>
        <div class="navigation-buttons">
            <a href="dashboard.php" class="nav-button">Go to Dashboard</a>
            <a href="profile.php" class="nav-button">View Profile</a>
            <a href="logout.php" class="nav-button">Logout</a>
        </div>
    </div>
</body>
</html>
