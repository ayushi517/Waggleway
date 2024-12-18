<?php
session_start();

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
$userId = $_SESSION['userid']; // Assuming user ID is stored in the session

// Check for any session messages
$loginMessage = $_SESSION['login_message'] ?? '';
unset($_SESSION['login_message']);
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
        window.onload = function() {
    updateAmount(); // Call this function to set the correct amount on page load
}

function updateAmount() {
    const serviceSelect = document.getElementById('grooming-service');
    const amountInput = document.getElementById('amount');
    const serviceAmounts = {
        'basic-grooming': 1499,
        'deluxe-grooming': 3299,
        'premium-grooming': 4999
    };

    const selectedService = serviceSelect.value;
    amountInput.value = serviceAmounts[selectedService] || '';
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
        <h2>Book Your Grooming Service</h2>
        <?php if ($loginMessage): ?>
            <p class="message"><?php echo htmlspecialchars($loginMessage); ?></p>
        <?php endif; ?>
        <form action="../php/submit_booking.php" method="POST">
            <!-- Hidden input field to carry the logged-in user's name -->
            <input type="hidden" id="userid" name="userid" value="<?php echo htmlspecialchars($userId); ?>">
            <input type="hidden" id="service_type" name="service_type" value="grooming"> <!-- This can change -->
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
                <label for="grooming-service">Select Grooming Service:</label>
                <select id="grooming-service" name="grooming-service" required onchange="updateAmount()">
                    <option value="">Select a service</option>
                    <option value="basic-grooming">Basic Grooming (₹1,499)</option>
                    <option value="deluxe-grooming">Deluxe Grooming (₹3,299)</option>
                    <option value="premium-grooming">Premium Grooming (₹4,999)</option>
                </select>
            </div>
            <div class="form-group">
                <label for="amount">Amount:</label>
                <input type="text" id="amount" name="amount" readonly>
            </div>
            <div class="form-group">
                <label for="startdate">Select Date :</label>
                <input type="date" id="startdate" name="startdate" required>
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
