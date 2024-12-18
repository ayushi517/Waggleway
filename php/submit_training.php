<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
session_start();

// Temporarily remove the unset session for debugging
// unset($_SESSION['amount']);

// Retrieve the amount from the session
if (isset($_SESSION['amount'])) {
    echo "Session Amount: " . htmlspecialchars($_SESSION['amount']);
} else {
    echo "Session Amount: Amount not available";
}

// Get booking ID from the GET request
if (isset($_GET['booking_id'])) {
    $booking_id = intval($_GET['booking_id']);
    echo "<br>Booking ID from GET: " . $booking_id;
}

// Retrieve service type from the session or GET request
$service_type = $_SESSION['service_type'] ?? $_GET['service_type'] ?? 'Not available';
echo "<br>Service Type from session or GET: " . htmlspecialchars($service_type);

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

// Debug: Print the current session variables
echo "<pre>Session Variables:";
print_r($_SESSION);
echo "</pre>";

// Ensure userId is set from the session
if (!isset($_SESSION['userid']) || $_SESSION['userid'] == 0) {
    $_SESSION['login_message'] = "User ID is not set or invalid. Please log in again.";
    header("Location: ../pages/login.html?redirect=" . urlencode($_SERVER['REQUEST_URI']));
    exit();
}

$userId = $_SESSION['userid'];
echo "User ID from session: " . htmlspecialchars($userId) . "<br>";

// Check if the form is submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Retrieve and sanitize form data
    $name = $conn->real_escape_string($_POST['name'] ?? '');
    $email = $conn->real_escape_string($_POST['email'] ?? '');
    $phone = $conn->real_escape_string($_POST['phone'] ?? '');
    $trainingType = $conn->real_escape_string($_POST['training-service'] ?? ''); // Correct field name
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
    if (empty($email) || empty($phone) || empty($trainingType) || empty($startDate) || empty($endDate) || empty($amount)) {
        echo "Validation error: All fields are required!";
        exit();
    }

    // Validate email format
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        echo "Validation error: Invalid email format!";
        exit();
    }

    // Validate phone number format (simple validation for demonstration)
    if (!preg_match('/^\d{10}$/', $phone)) {
        echo "Validation error: Invalid phone number format! Must be 10 digits.";
        exit();
    }

    // Validate date range
    $startDateObj = DateTime::createFromFormat('Y-m-d', $startDate);
    $endDateObj = DateTime::createFromFormat('Y-m-d', $endDate);
    $currentYear = (new DateTime())->format('Y');

    // Ensure dates are within the current year
    if ($startDateObj === false || $endDateObj === false || $startDateObj->format('Y') !== $currentYear || $endDateObj->format('Y') !== $currentYear) {
        echo "Validation error: Dates must be within the current year.";
        exit();
    }

    // Ensure start date is before end date
    if ($startDateObj > $endDateObj) {
        echo "Validation error: End date must be after start date!";
        exit();
    }

    // Validate amount (make sure it’s a number)
    if (!is_numeric($amount) || $amount <= 0) {
        echo "Validation error: Invalid amount!";
        exit();
    }

    // Insert data into the database
    $sql = "INSERT INTO pet_training_bookings (email, phone, service_type, training_level, start_date, end_date, message, amount, payment_status,userid, booking_date) 
            VALUES (?, ?, ?, ?, ?, ?, ?, ?,?, ?, CURRENT_TIMESTAMP)";

    $stmt = $conn->prepare($sql);
    if (!$stmt) {
        echo "Statement preparation failed: " . $conn->error;
        exit();
    }

    // Bind parameters
    $stmt->bind_param("ssssssssss", $email, $phone, $serviceType, $trainingType, $startDate, $endDate, $message, $amount,$paymentStatus, $userId);

    // Execute statement
    if ($stmt->execute()) {
        // Get the last inserted ID
        $booking_id = $conn->insert_id;

        // Booking successful, redirect to confirmation page with booking_id and service_type
        header("Location: ../pages/confirmation.html?booking_id=$booking_id&service_type=$serviceType&amount=" . urlencode($amount)."&payment_status=$paymentStatus");
        exit();
    } else {
        $_SESSION['booking_message'] = "Booking failed: " . $stmt->error;
        exit();
    }
}

// Close the database connection
$conn->close();
?>
