<?php
session_start(); // Start the session
unset($_SESSION['amount']);

// Enable error reporting for debugging
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
$selectedService = $_POST['grooming-service'] ?? '';
$serviceAmounts = [
    'basic-grooming' => 1499,
    'deluxe-grooming' => 3299,
    'premium-grooming' => 4999
];

// Set session amount based on selected service
$_SESSION['amount'] = $serviceAmounts[$selectedService] ?? 0;

// Debug output to verify the amount
echo "<h3>Amount set in session:</h3>";
var_dump($_SESSION['amount']);

// Database connection parameters
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "waggleway";

// Create a connection to the database
$conn = new mysqli($servername, $username, $password, $dbname);

// Check if the connection is successful
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Check if the user is logged in
if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true) {
    $_SESSION['login_message'] = "You need to log in first.";
    header("Location: ../pages/login.html?redirect=" . urlencode($_SERVER['REQUEST_URI']));
    exit();
}

// Get user information from the session
$username = $_SESSION['username'];
$userId = $_SESSION['userid']; // Retrieve user ID from session

// Initialize error array
$errors = [];

// Check if the form is submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Retrieve and sanitize form data
    $name = $conn->real_escape_string($_POST['name'] ?? $username);
    $email = $conn->real_escape_string($_POST['email'] ?? '');
    $phone = $conn->real_escape_string($_POST['phone'] ?? '');
    $groomingService = $conn->real_escape_string($_POST['grooming-service'] ?? '');
    $amount = $conn->real_escape_string($_POST['amount'] ?? '');
    $startDate = $conn->real_escape_string($_POST['startdate'] ?? '');
    $message = $conn->real_escape_string($_POST['message'] ?? '');
    $serviceType = $conn->real_escape_string($_POST['service_type'] ?? '');
    $paymentStatus = $conn->real_escape_string($_POST['payment_status'] ?? 'unpaid'); // Handle payment status

    // Validate inputs
    if (empty($name) || empty($email) || empty($phone) || empty($groomingService) || empty($amount) || empty($startDate)) {
        $errors[] = "All fields are required!";
    }

    // Validate email format
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors[] = "Invalid email format!";
    }

    // Validate phone number format (simple validation for demonstration)
    if (!preg_match('/^\d{10}$/', $phone)) {
        $errors[] = "Invalid phone number format! Must be 10 digits.";
    }

    // Validate start date
    $startDateObj = DateTime::createFromFormat('Y-m-d', $startDate);
    $currentYear = (new DateTime())->format('Y');

    // Ensure the start date is within the current year
    if ($startDateObj === false || $startDateObj->format('Y') !== $currentYear) {
        $errors[] = "Start date must be within the current year.";
    }

    // Check for validation errors
    if (!empty($errors)) {
        $_SESSION['booking_message'] = implode(" ", $errors);
        header("Location: ../php/bookingForm.php");
        exit();
    }

    // Insert data into the database
    $sql = "INSERT INTO grooming_bookings (name, email, phone, service_type, grooming_service, amount, start_date, message, payment_status, userid) 
        VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
    $stmt = $conn->prepare($sql);

    if ($stmt === false) {
        $_SESSION['booking_message'] = "Statement preparation failed: " . $conn->error;
        header("Location: ../php/bookingForm.php");
        exit();
    }

    // Bind parameters
    $stmt->bind_param("ssssssssss", $name, $email, $phone, $serviceType, $groomingService, $amount, $startDate, $message, $paymentStatus, $userId);


    // Execute statement
    if ($stmt->execute()) {
        // Get the last inserted ID
        $booking_id = $conn->insert_id;

        // Booking successful, redirect to confirmation page with booking_id
        header("Location: ../pages/confirmation.html?booking_id=$booking_id&service_type=$serviceType&amount=" . urlencode($amount)."&payment_status=$paymentStatus");
        exit(); // Ensure no further code is executed after the redirection
        
    } else {
        $_SESSION['booking_message'] = "Booking failed: " . $stmt->error;
        header("Location: ../php/bookingForm.php");
        exit();
    }
}

// Close the database connection
$conn->close();
