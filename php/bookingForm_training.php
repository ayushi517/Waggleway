<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
session_start();
var_dump($_SESSION['userid']); 

// Debug: Check if session variables are set
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
$userId = $_SESSION['userid'];

$loginMessage = $_SESSION['login_message'] ?? '';
unset($_SESSION['login_message']);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>WaggleWay - Book Pet Training</title>
    <link rel="stylesheet" href="../css/style.css">
    <link rel="stylesheet" href="../css/bookingForm.css">
    <link rel="stylesheet" href="../css/about.css">
    <script src="../js/login.js"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <script>
        
     window.onload = function() {
    updateAmount(); // Call this function to set the correct amount on page load
}
    // JavaScript function to update the amount field based on selected service
    function updateAmount() {
        const serviceSelect = document.getElementById('training-service');
        const durationInput = document.getElementById('duration');
        const amountDisplayInput = document.getElementById('amount-display');
        const amountHiddenInput = document.getElementById('amount');

        // Define service prices per week for training
        const servicePrices = {
            'basic-training': 8199,
            'advanced-training': 16599,
            'specialized-training': 24999
        };

        // Get selected service and duration (in weeks)
        const selectedService = serviceSelect.value;
        const duration = parseInt(durationInput.value) || 0;

        console.log('Selected Service:', selectedService);
        console.log('Duration:', duration);

        // Calculate amount based on selected service and duration
        const pricePerUnit = servicePrices[selectedService] || 0;
        const totalAmount = pricePerUnit * duration;

        console.log('Price Per Unit:', pricePerUnit);
        console.log('Total Amount:', totalAmount);

        // Update amount display field
        amountDisplayInput.value = totalAmount ? `₹${totalAmount}` : '';

        // Update hidden amount field for form submission
        amountHiddenInput.value = totalAmount ? totalAmount : '';
    }
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
                    <a href="../pages/pricing1.html">Pet Grooming</a>
                    <a href="../pages/pricing2.html">Pet Boarding & Sitting</a>
                    <a href="../pages/pricing3.html">Pet Training</a>
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
        <h2>Book Your Pet Training Service</h2>
        <form action="../php/submit_training.php" method="POST">
            <input type="hidden" id="user_id" name="user_id" value="<?php echo htmlspecialchars($userId); ?>">
            <input type="hidden" id="amount" name="amount"> <!-- Hidden amount field for form submission -->
            <input type="hidden" id="service_type" name="service_type" value="training"> 
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
                <label for="training-service">Select Training Service:</label>
                <select id="training-service" name="training-service" required onchange="updateAmount()">
                    <option value="">Select a service</option>
                    <option value="basic-training">Basic Training (₹8,199/course)</option>
                    <option value="advanced-training">Advanced Training (₹16,599/course)</option>
                    <option value="specialized-training">Specialized Training (₹24,999/course)</option>
                </select>
            </div>
            <div class="form-group">
                <label for="duration">Duration of Training (in weeks):</label>
                <input type="number" id="duration" name="duration" min="1" required oninput="updateAmount()">
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
