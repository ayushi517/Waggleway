<?php
session_start();
unset($_SESSION['amount']);

echo "User ID from session: " . htmlspecialchars($userId) . "<br>";



// Enable error reporting
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Database connection parameters
$servername = "localhost";

$dbusername = "root";
$dbpassword = "";
$dbname = "waggleway";

// Create a connection to the database
$conn = new mysqli($servername, $dbusername, $dbpassword, $dbname);

// Check if the connection is successful
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Get the current URL
$current_url = $_SERVER['REQUEST_URI'];

// Check if the user is logged in
if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true) {
    $_SESSION['login_message'] = "You need to log in first.";
    header("Location: ../pages/login.html?redirect=" . urlencode($current_url));
    exit();
}

// Ensure userId is set from the session
if (!isset($_SESSION['userid']) || $_SESSION['userid'] == 0) {
    $_SESSION['login_message'] = "User ID is not set or invalid. Please log in again.";
    header("Location: ../pages/login.html?redirect=" . urlencode($current_url));
    exit();
}

$username = $_SESSION['username'];
$userId = $_SESSION['userid'];
$errors = [];
// Debugging user ID
echo "User ID from session: " . htmlspecialchars($userId) . "<br>";

// Check if the form is submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Set the amount in session
    $_SESSION['amount'] = $_POST['amount'] ?? '';

    // Retrieve and sanitize form data
    $name = $conn->real_escape_string($_POST['name'] ?? $username);
    $email = $conn->real_escape_string($_POST['email'] ?? '');
    $phone = $conn->real_escape_string($_POST['phone'] ?? '');
    $boardingService = $conn->real_escape_string($_POST['boarding-service'] ?? '');
    $amount = $conn->real_escape_string($_POST['amount'] ?? ''); // Ensure amount is retrieved
    $startDate = $conn->real_escape_string($_POST['start_date'] ?? '');
    $endDate = $conn->real_escape_string($_POST['end_date'] ?? '');
    $message = $conn->real_escape_string($_POST['message'] ?? '');
    $serviceType = $conn->real_escape_string($_POST['service_type'] ?? '');
    $paymentStatus = $conn->real_escape_string($_POST['payment_status'] ?? 'unpaid'); // Handle payment status


    // Debug output
    echo "<pre>";
    print_r($_POST);
    echo "</pre>";

    // Validate inputs
    if (empty($email) || empty($phone) || empty($boardingService) || empty($startDate) || empty($endDate) || empty($amount)) {
        $_SESSION['booking_message'] = "All fields are required!";
        header("Location: ../php/booking_boarding.php");
        exit();
    }

    // Validate email format
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $_SESSION['booking_message'] = "Invalid email format!";
        header("Location: ../php/booking_boarding.php");
        exit();
    }

    // Validate phone number format (simple validation for demonstration)
    if (!preg_match('/^\d{10}$/', $phone)) {
        $_SESSION['booking_message'] = "Invalid phone number format! Must be 10 digits.";
        header("Location: ../php/booking_boarding.php");
        exit();
    }

    // Validate date range
    $startDateObj = DateTime::createFromFormat('Y-m-d', $startDate);
    $endDateObj = DateTime::createFromFormat('Y-m-d', $endDate);
    $currentYear = (new DateTime())->format('Y');
    
    // Ensure dates are within the current year
    if ($startDateObj === false || $endDateObj === false || $startDateObj->format('Y') !== $currentYear || $endDateObj->format('Y') !== $currentYear) {
        $_SESSION['booking_message'] = "Dates must be within the current year.";
        header("Location: ../php/booking_boarding.php");
        exit();
    }

    // Ensure start date is before end date
    if ($startDateObj > $endDateObj) {
        $_SESSION['booking_message'] = "End date must be after start date!";
        header("Location: ../php/booking_boarding.php");
        exit();
    }

    // Validate amount (make sure it’s a number)
    if (!is_numeric($amount) || $amount <= 0) {
        $_SESSION['booking_message'] = "Invalid amount!";
        header("Location: ../php/booking_boarding.php");
        exit();
    }

    // Insert data into the database
    $sql = "INSERT INTO pet_boarding (email, phone,service_type, boarding_service, start_date, end_date, message, amount, payment_status,userid) VALUES (?, ?,?,?, ?, ?, ?, ?, ?,?)";

    $stmt = $conn->prepare($sql);
    if (!$stmt) {
        $_SESSION['booking_message'] = "Statement preparation failed: " . $conn->error;
        header("Location: ../php/booking_boarding.php");
        exit();
    }

    // Bind parameters
    $stmt->bind_param("ssssssssss", $email, $phone, $serviceType, $boardingService, $startDate, $endDate, $message, $amount,$paymentStatus, $userId);

    // Execute statement
    // Execute statement
    if ($stmt->execute()) {
        // Get the last inserted ID
        $booking_id = $conn->insert_id;

        // Booking successful, redirect to confirmation page with booking_id
        header("Location: ../pages/confirmation.html?booking_id=$booking_id&service_type=$serviceType&amount=" . urlencode($amount)."&payment_status=$paymentStatus");

        exit();
    } else {
        $_SESSION['booking_message'] = "Booking failed: " . $stmt->error;
        header("Location: ../php/bookingForm.php");
        exit();
    }
}

// Close the database connection
$conn->close();

