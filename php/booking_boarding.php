<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
session_start();

// Debug: Check session variables
var_dump($_SESSION['userid']); 

// Check if session variables are set
if (!isset($_SESSION['username'])) {
    echo "Username is not set in the session.";
}
if (!isset($_SESSION['userid'])) {
    echo "User ID is not set in the session.";
}

// Ensure amount is set in the session when the form is submitted
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $_SESSION['amount'] = $_POST['amount'];
}

// Proceed if session variables are set
if (isset($_SESSION['username']) && isset($_SESSION['userid'])) {
    $username = $_SESSION['username'];
    $userId = $_SESSION['userid'];
} else {
    // Handle case where session variables are not set
    $username = '';
    $userId = '';
}

// Check for any session messages
$loginMessage = isset($_SESSION['login_message']) ? $_SESSION['login_message'] : '';
unset($_SESSION['login_message']);

// Check if the user is logged in
if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true) {
    // Set a session message to indicate login is required
    $_SESSION['login_message'] = "You need to log in first.";

    // Capture the current page URL for redirection after login
    $current_url = urlencode($_SERVER['REQUEST_URI']);

    // Redirect to the login page with the message and the redirect URL
    header("Location: ../pages/login.html?message=Please%20log%20in%20to%20access%20this%20page&redirect=" . $current_url);
    exit();
}

// Set the username from the session
$username = $_SESSION['username'];
$userId = $_SESSION['userid']; //
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>WaggleWay - Booking Form</title>
    <link rel="stylesheet" href="../css/style.css">
    <link rel="stylesheet" href="../css/services.css">
    <link rel="stylesheet" href="../css/bookingForm.css">
    <link rel="stylesheet" href="../css/about.css">
    <script src="../js/login.js"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <script>
    // JavaScript function to update the amount field based on selected service
    function updateAmount() {
        const serviceSelect = document.getElementById('boarding-service');
        const durationInput = document.getElementById('duration');
        const amountDisplayInput = document.getElementById('amount-display');
        const amountHiddenInput = document.getElementById('amount');

        // Define service prices per night or visit
        const servicePrices = {
            'standard-boarding': 2199, // ₹2,199 per night
            'luxury-boarding': 3799,  // ₹3,799 per night
            'in-home-sitting': 2099    // ₹2,099 per visit
        };

        // Get selected service and duration
        const selectedService = serviceSelect.value;
        const duration = parseInt(durationInput.value) || 0;

        // Calculate amount based on selected service and duration
        const pricePerUnit = servicePrices[selectedService] || 0;
        const totalAmount = pricePerUnit * duration;

        // Update amount display field
        amountDisplayInput.value = totalAmount ? `₹${totalAmount}` : '';

        // Update hidden amount field for form submission
        amountHiddenInput.value = totalAmount ? totalAmount : '';
    }

    // Add event listeners to update amount on change
    document.addEventListener('DOMContentLoaded', () => {
        document.getElementById('boarding-service').addEventListener('change', updateAmount);
        document.getElementById('duration').addEventListener('input', updateAmount);
    });
    </script>

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
   
<div class="booking-form-container">
    <h2>Book Your Boarding or Sitting Service</h2>
    <?php if ($loginMessage): ?>
        <p class="message"><?php echo htmlspecialchars($loginMessage); ?></p>
    <?php endif; ?>
    <form action="../php/submit_boarding.php" method="POST">
        <!-- Hidden input field to carry the logged-in user's ID -->
        <input type="hidden" id="user_id" name="user_id" value="<?php echo htmlspecialchars($userId); ?>">
        <input type="hidden" id="amount" name="amount">
        <input type="hidden" id="service_type" name="service_type" value="boarding">
        <input type="hidden" name="payment_status" value="unpaid"> 

        <div class="form-group">
            <label for="email">Email:</label>
            <input type="email" id="email" name="email" required>
        </div>
        <div class="form-group">
            <label for="phone">Phone Number:</label>
            <input type="tel" id="phone" name="phone" required>
        </div>
        <div class="form-group">
            <label for="boarding-service">Select Boarding or Sitting Service:</label>
            <select id="boarding-service" name="boarding-service" required>
                <option value="">Select a service</option>
                <option value="standard-boarding">Standard Boarding (₹2,199/night)</option>
                <option value="luxury-boarding">Luxury Boarding (₹3,799/night)</option>
                <option value="in-home-sitting">In-Home Pet Sitting (₹2,099/visit)</option>
            </select>
        </div>
        <div class="form-group">
            <label for="duration">Duration of Stay (in days/visits):</label>
            <input type="number" id="duration" name="duration" min="1" required>
        </div>
        <div class="form-group">
            <label for="amount-display">Amount:</label>
            <input type="text" id="amount-display" name="amount-display" readonly>
        </div>
        <div class="form-group">
            <label for="start_date">Start Date:</label>
            <input type="date" id="start_date" name="start_date" required>
        </div>
        <div class="form-group">
            <label for="end_date">End Date:</label>
            <input type="date" id="end_date" name="end_date" required>
        </div>
        <div class="form-group">
            <label for="message">Additional Notes:</label>
            <textarea id="message" name="message" rows="4"></textarea>
        </div>
        <button type="submit" class="submit-btn">Book Now</button>
    </form>
</div>
</body>
</html>
