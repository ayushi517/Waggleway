<?php
session_start();
$servername = "localhost"; // Replace with your server name
$username = "root"; // Replace with your MySQL username
$password = ""; // Replace with your MySQL password
$dbname = "waggleway"; // Replace with your database name

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Fetch user data
$userid = $_SESSION['userid']; // Assuming user ID is stored in session after login
$query = "SELECT username, email ,userid FROM users WHERE userid = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param("i", $userid);
$stmt->execute();
$result = $stmt->get_result();
$user = $result->fetch_assoc();

// Fetch active subscriptions with payments
$subscriptionQuery = "
    SELECT 'grooming' AS service_type, g.id, g.amount, g.start_date, g.end_date 
    FROM grooming_bookings g 
    JOIN payments p ON g.id = p.booking_id 
    WHERE g.userid = ?
    UNION ALL
    SELECT 'boarding' AS service_type, b.id, b.amount, b.start_date, b.end_date 
    FROM pet_boarding b 
    JOIN payments p ON b.id = p.booking_id 
    WHERE b.userid = ?
    UNION ALL
    SELECT 'training' AS service_type, t.id, t.amount, t.start_date, t.end_date 
    FROM pet_training_bookings t 
    JOIN payments p ON t.id = p.booking_id 
    WHERE t.userid = ?
";
$subscriptionStmt = $conn->prepare($subscriptionQuery);
$subscriptionStmt->bind_param("iii", $userid, $userid, $userid);
$subscriptionStmt->execute();
$subscriptions = $subscriptionStmt->get_result();

// Fetch recent bookings
$bookingQuery = "
    SELECT * FROM (
        SELECT 'grooming' AS service_type, id, amount, start_date, end_date, message 
        FROM grooming_bookings WHERE userid = ?
        UNION ALL
        SELECT 'boarding' AS service_type, id, amount, start_date, end_date, message 
        FROM pet_boarding WHERE userid = ?
        UNION ALL
        SELECT 'training' AS service_type, id, amount, start_date, end_date, message 
        FROM pet_training_bookings WHERE userid = ?
    ) AS all_bookings 
    ORDER BY start_date DESC 
    LIMIT 5
";
$bookingStmt = $conn->prepare($bookingQuery);
$bookingStmt->bind_param("iii", $userid, $userid, $userid);
$bookingStmt->execute();
$bookings = $bookingStmt->get_result();

// Fetch payment history
$paymentQuery = "
    SELECT amount, created_at, payment_status 
    FROM payments 
    WHERE booking_id IN (
        SELECT id FROM grooming_bookings WHERE userid = ? 
        UNION 
        SELECT id FROM pet_boarding WHERE userid = ? 
        UNION 
        SELECT id FROM pet_training_bookings WHERE userid = ?
    ) 
    ORDER BY created_at DESC
";
$paymentStmt = $conn->prepare($paymentQuery);
$paymentStmt->bind_param("iii", $userid, $userid, $userid);
$paymentStmt->execute();
$payments = $paymentStmt->get_result();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>User Profile</title>
    <link rel="stylesheet" href="../css/profile.css">
    <link rel="stylesheet" href="../css/style.css">
    <script src="../js/login.js"></script>

    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
 
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
    
    <div class="profile-container">
        <h1>Welcome, <?php echo htmlspecialchars($user['username']); ?>!</h1>

        <div class="profile-section">
            <h2>Personal Information</h2>
            <p><strong>Username:</strong> <?php echo htmlspecialchars($user['username']); ?></p>
            <p><strong>Email:</strong> <?php echo htmlspecialchars($user['email']); ?></p>
            <p><strong>UserId:</strong> <?php echo htmlspecialchars($user['userid']); ?></p>
            <a href="../php/edit_profile.php">Edit Details</a>
        </div>

        <div class="subscription-section">
            <h2>Your Subscriptions</h2>
            <?php if ($subscriptions->num_rows > 0) { ?>
                <ul>
                <?php while ($subscription = $subscriptions->fetch_assoc()) { ?>
                    <li>
                        <p><strong>Service Type:</strong> <?php echo htmlspecialchars($subscription['service_type']); ?></p>
                        <p><strong>Booking ID:</strong> <?php echo htmlspecialchars($subscription['id']); ?></p>
                        <p><strong>Amount:</strong> ₹<?php echo number_format($subscription['amount'], 2); ?></p>
                        <p><strong>Start Date:</strong> <?php echo htmlspecialchars($subscription['start_date']); ?></p>
                        <p><strong>End Date:</strong> <?php echo htmlspecialchars($subscription['end_date']); ?></p>
                    </li>
                <?php } ?>
                </ul>
            <?php } else { ?>
                <p>No active subscriptions found with payments.</p>
            <?php } ?>
        </div>

        <div class="booking-section">
            <h2>Your Recent Bookings</h2>
            <?php if ($bookings->num_rows > 0) { ?>
                <ul>
                <?php while ($booking = $bookings->fetch_assoc()) { ?>
                    <li>
                        <p><strong>Service Type:</strong> <?php echo htmlspecialchars($booking['service_type']); ?></p>
                        <p><strong>Booking ID:</strong> <?php echo htmlspecialchars($booking['id']); ?></p>
                        <p><strong>Amount:</strong> ₹<?php echo number_format($booking['amount'], 2); ?></p>
                        <p><strong>Start Date:</strong> <?php echo htmlspecialchars($booking['start_date']); ?></p>
                        <p><strong>End Date:</strong> <?php echo htmlspecialchars($booking['end_date']); ?></p>
                        <p><strong>Message:</strong> <?php echo htmlspecialchars($booking['message']); ?></p>
                    </li>
                <?php } ?>
                </ul>
            <?php } else { ?>
                <p>No recent bookings found.</p>
            <?php } ?>
        </div>

        <div class="payment-section">
            <h2>Payment History</h2>
            <?php if ($payments->num_rows > 0) { ?>
                <ul>
                <?php while ($payment = $payments->fetch_assoc()) { ?>
                    <li>
                        <p><strong>Amount:</strong> ₹<?php echo number_format($payment['amount'], 2); ?></p>
                        <p><strong>Date:</strong> <?php echo htmlspecialchars($payment['created_at']); ?></p>
                        <p><strong>Status:</strong> <?php echo htmlspecialchars($payment['payment_status']); ?></p>
                    </li>
                <?php } ?>
                </ul>
            <?php } else { ?>
                <p>No payment history found.</p>
            <?php } ?>
        </div>
    </div>
</body>
</html>
