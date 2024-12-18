<?php
session_start();

// Check if the user is logged in
if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true) {
    header("Location: login.php?message=Please%20log%20in%20to%20view%20your%20subscription");
    exit();
}

$servername = "localhost";
$username = "root";
$password = "";
$dbname = "waggleway";

// Create connection
$conn = mysqli_connect($servername, $username, $password, $dbname);

// Check connection
if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
}

// Assuming user ID is stored in session
$user_id = $_SESSION['userid'];

// Fetch user subscriptions with payment status from three tables
$boarding_query = "
    SELECT pb.*, p.payment_status
    FROM pet_boarding pb
    JOIN payments p ON pb.id = p.booking_id
    WHERE pb.userid = ?";
$grooming_query = "
    SELECT gb.*, p.payment_status
    FROM grooming_bookings gb
    JOIN payments p ON gb.id = p.booking_id
    WHERE gb.userid = ?";
$training_query = "
    SELECT tb.*, p.payment_status
    FROM pet_training_bookings tb
    JOIN payments p ON tb.id = p.booking_id
    WHERE tb.userid = ?";

$subscriptions = array();

function fetch_subscription_data($conn, $query, $user_id) {
    $stmt = mysqli_prepare($conn, $query);
    if (!$stmt) {
        die('Prepare failed: ' . mysqli_error($conn));
    }
    mysqli_stmt_bind_param($stmt, "s", $user_id);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    $data = mysqli_fetch_all($result, MYSQLI_ASSOC);
    mysqli_stmt_close($stmt);
    return $data;
}

// Fetch data for each service
$subscriptions['boarding'] = fetch_subscription_data($conn, $boarding_query, $user_id);
$subscriptions['grooming'] = fetch_subscription_data($conn, $grooming_query, $user_id);
$subscriptions['training'] = fetch_subscription_data($conn, $training_query, $user_id);

mysqli_close($conn);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Subscription Page</title>
    <link rel="stylesheet" href="../css/subscription.css">
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
    

    <div class="subscription-container">
        <h1>Welcome, <?php echo htmlspecialchars($_SESSION['username']); ?></h1>
        <h2>Your Subscriptions</h2>
        
        <div class="subscription-section">
            <h3>Pet Boarding</h3>
            <?php if (!empty($subscriptions['boarding'])): ?>
                <ul>
                    <?php foreach ($subscriptions['boarding'] as $board): ?>
                        <li>Booking Date: <?php echo htmlspecialchars($board['created_at']); ?>, Service Type: <?php echo htmlspecialchars($board['boarding_service']); ?>, Duration: <?php echo htmlspecialchars($board['start_date']); ?> to <?php echo htmlspecialchars($board['end_date']); ?>, Payment Status: <?php echo htmlspecialchars($board['payment_status']); ?></li>
                    <?php endforeach; ?>
                </ul>
            <?php else: ?>
                <p>No active subscriptions for Pet Boarding.</p>
            <?php endif; ?>
        </div>
        
        <div class="subscription-section">
            <h3>Pet Grooming</h3>
            <?php if (!empty($subscriptions['grooming'])): ?>
                <ul>
                    <?php foreach ($subscriptions['grooming'] as $groom): ?>
                        <li>Booking Date: <?php echo htmlspecialchars($groom['booking_date']); ?>, Service Type: <?php echo htmlspecialchars($groom['grooming_service']); ?>, Payment Status: <?php echo htmlspecialchars($groom['payment_status']); ?></li>
                    <?php endforeach; ?>
                </ul>
            <?php else: ?>
                <p>No active grooming appointments.</p>
            <?php endif; ?>
        </div>
        
        <div class="subscription-section">
            <h3>Pet Training</h3>
            <?php if (!empty($subscriptions['training'])): ?>
                <ul>
                    <?php foreach ($subscriptions['training'] as $train): ?>
                        <li>Booking Date: <?php echo htmlspecialchars($train['booking_date']); ?>, Service Type: <?php echo htmlspecialchars($train['training_level']); ?>, Payment Status: <?php echo htmlspecialchars($train['payment_status']); ?></li>
                    <?php endforeach; ?>
                </ul>
            <?php else: ?>
                <p>No active training programs.</p>
            <?php endif; ?>
        </div>
    </div>
    

</body>
</html>
