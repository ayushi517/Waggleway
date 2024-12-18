<?php
// Start session and retrieve user ID
session_start();
$userId = isset($_SESSION['userid']) ? $_SESSION['userid'] : ''; // or fetch from database
?>
<!DOCTYPE html>
<html>
<head>
    <title>Home - Pet Care</title>
</head>
<body>
    <h1>Welcome to Pet Care</h1>
    <nav>
        <ul>
            <li><a href="grooming.php">Grooming Services</a></li>
            <li><a href="boardingSitting.php">Boarding & Sitting Services</a></li>
            <li><a href="training.php">Training Services</a></li>
        </ul>
    </nav>
</body>
</html>
