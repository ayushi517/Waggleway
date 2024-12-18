<?php
session_start();

// Database connection (replace these values with your actual database credentials)
$host = 'localhost'; // Replace with your DB host
$username = 'root';  // Replace with your DB username
$password = '';      // Replace with your DB password
$database = 'waggleway';  // Replace with your DB name

// Create a new connection to the database
$conn = new mysqli($host, $username, $password, $database);

// Check if the connection was successful
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Assuming booking_id and service_type are passed via GET
$booking_id = isset($_GET['booking_id']) ? intval($_GET['booking_id']) : 0;
$service_type = isset($_GET['service_type']) ? $_GET['service_type'] : '';

// Function to fetch booking details from the database
function fetchBookingDetails($conn, $table_name, $booking_id, $service_type) {
    // Modify query based on the service type
    if ($service_type === 'grooming') {
        // Grooming includes a specific service (e.g., Basic, Deluxe)
        $query = "SELECT id, amount, start_date, end_date, message, grooming_service FROM $table_name WHERE id = ?";
    } elseif ($service_type === 'boarding') {
        // Boarding includes a specific service (e.g., Standard, Luxury)
        $query = "SELECT id, amount, start_date, end_date, message, boarding_service FROM $table_name WHERE id = ?";
    } elseif ($service_type === 'training') {
        // Training includes a service level (e.g., Basic, Advanced)
        $query = "SELECT id, amount, start_date, end_date, message, training_level FROM $table_name WHERE id = ?";
    } else {
        // Default query for other services
        $query = "SELECT id, amount, start_date, end_date, message FROM $table_name WHERE id = ?";
    }

    $stmt = $conn->prepare($query);
    $stmt->bind_param('i', $booking_id);
    $stmt->execute();
    $result = $stmt->get_result();
    
    return $result->fetch_assoc();
}

// Map service type to the correct table name
$table_map = [
    'grooming' => 'grooming_bookings',
    'boarding' => 'pet_boarding',
    'training' => 'pet_training_bookings',
];

// Ensure that the service type is valid and fetch the booking details
if (array_key_exists($service_type, $table_map)) {
    $table_name = $table_map[$service_type];
    $bookingDetails = fetchBookingDetails($conn, $table_name, $booking_id, $service_type);
} else {
    echo "Invalid service type.";
    exit();
}

// Close the database connection
$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Receipt</title>
    <link rel="stylesheet" href="../css/receipt.css">
    <link rel="stylesheet" href="../css/style.css">
</head>
<body>
<header class="header">
        <a href="../index.html" class="logos"><i class="fas fa-dog"></i>Waggle<span>Way</span></a>
        <nav class="nav-bar">
            <a href="../index.html">Home</a>
            <a href="../pages/service.html">Services</a>
            <div class="dropdown">
                <a href="#" class="dropbtn" id="pricingBtn">Pricing</a>
                <div class="dropdown-content" id="pricingOptions">
                    <a href="../pages/pricing1.html" onclick="showPricing('basic')">Pet Grooming</a>
                    <a href="../pages/pricing2.html" onclick="showPricing('premium')">Pet Boarding & Sitting</a>
                    <a href="../pages/pricing3.html" onclick="showPricing('enterprise')">Pet Training</a>
                </div>
            </div>
            <a href="../pages/about.html">About Us</a>
            <a href="../pages/contact.html">Contact Us</a>
        </nav>
        <div class="accounts">
            <i id="menu-btn" class="fa-solid fa-user login-icon"></i>
            <div id="accountDropdown" class="dropdown-content-login">
                <!-- This will be dynamically populated by JavaScript -->
            </div>
        </div>
    </header>
       


    <div class="receipt-container">
        <h1>Receipt</h1>
        <p><strong>Service Type:</strong> <?php echo ucfirst($service_type); ?></p>
        
        <!-- Display specific service details based on the service type -->
        <?php if ($service_type === 'grooming') { ?>
            <p><strong>Grooming Service:</strong> <?php echo htmlspecialchars($bookingDetails['grooming_service']); ?></p>
        <?php } elseif ($service_type === 'boarding') { ?>
            <p><strong>Boarding Service:</strong> <?php echo htmlspecialchars($bookingDetails['boarding_service']); ?></p>
        <?php } elseif ($service_type === 'training') { ?>
            <p><strong>Training Service Level:</strong> <?php echo htmlspecialchars($bookingDetails['training_level']); ?></p>
        <?php } ?>
        
        <p><strong>Start Date:</strong> <?php echo htmlspecialchars($bookingDetails['start_date']); ?></p>
        <p><strong>End Date:</strong> <?php echo htmlspecialchars($bookingDetails['end_date']); ?></p>
        <p><strong>Message:</strong> <?php echo htmlspecialchars($bookingDetails['message']); ?></p>
        <p><strong>Total Amount:</strong> ₹<?php echo number_format($bookingDetails['amount'], 2); ?></p>
    </div>
</body>
</html>
