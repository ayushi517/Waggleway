<?php
// Start the session
session_start();

// Enable error reporting for debugging
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Database connection parameters
$servername = "localhost";
$dbUsername = "root";
$dbPassword = "";
$dbname = "waggleway";

// Create a connection to the database
$conn = new mysqli($servername, $dbUsername, $dbPassword, $dbname);

// Check if the connection is successful
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Check if the form is submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Retrieve and sanitize form data
    $userId = $conn->real_escape_string($_POST['userid']);
    $username = $conn->real_escape_string($_POST['username']);
    $email = $conn->real_escape_string($_POST['email']);
    $password = $_POST['password'];
    $confirmPassword = $_POST['confirm_password'];

    // Basic validation
    if ($password !== $confirmPassword) {
        $_SESSION['error_message'] = 'Passwords do not match!';
        header("Location: ../pages/register.php"); // Redirect back to registration
        exit();
    }

    // Check if the userid already exists
    $stmt = $conn->prepare("SELECT * FROM users WHERE userid = ?");
    $stmt->bind_param("s", $userId);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $_SESSION['error_message'] = 'User with this User ID already exists!';
        header("Location: ../pages/register.php");
        exit();
    } 

    // Check if the user with the email already exists
    $stmt = $conn->prepare("SELECT * FROM users WHERE email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $_SESSION['error_message'] = 'User with this email already exists!';
        header("Location: ../pages/register.php");
        exit();
    }

    // Hash the password before storing it
    $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

    // Insert the new user into the database
    $stmt = $conn->prepare("INSERT INTO users (userid, username, email, password) VALUES (?, ?, ?, ?)");
    $stmt->bind_param("ssss", $userId, $username, $email, $hashedPassword);

    if ($stmt->execute()) {
        // Store the success message in the session
        $_SESSION['success_message'] = "Registration successful!";
        header("Location: ../pages/login.html");
        exit();
    } else {
        $_SESSION['error_message'] = "Error: " . $stmt->error;
        header("Location: ../pages/register.php");
        exit();
    }

    
}

// Close the database connection
$conn->close();
