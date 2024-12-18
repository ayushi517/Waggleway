<?php
session_start();

// Enable error reporting
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Fetch `service_type` from session or GET parameters
$serviceType = isset($_SESSION['service_type']) ? htmlspecialchars($_SESSION['service_type']) : (isset($_GET['service_type']) ? htmlspecialchars($_GET['service_type']) : '');

// Fetch `amount` from session or GET parameters
$amount = isset($_SESSION['amount']) ? htmlspecialchars($_SESSION['amount']) : (isset($_GET['amount']) ? htmlspecialchars($_GET['amount']) : '');

// Fetch `booking_id` from GET parameters
$booking_id = isset($_GET['booking_id']) ? intval($_GET['booking_id']) : '';
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Payment Gateway</title>
    <link rel="stylesheet" href="../css/style.css">
    <link rel="stylesheet" href="../css/payment_gateway.css">
    <style>
        .error {
            color: red;
            font-size: 0.9em;
            display: block;
            margin-top: 5px;
        }
    </style>
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

<div class="payment-container">
    <h2>Payment Gateway</h2>
    <p>Please complete your payment to finalize the booking.</p>

    <!-- Payment Form -->
    <form id="paymentForm" action="../php/payment_confirmation.php" method="POST">
        <input type="hidden" name="booking_id" value="<?php echo htmlspecialchars($booking_id); ?>">
        <input type="hidden" name="service_type" value="<?php echo htmlspecialchars($serviceType); ?>">
        <input type="hidden" name="amount" value="<?php echo htmlspecialchars($amount); ?>">

        <div class="form-group">
            <label for="card-name">Cardholder Name:</label>
            <input type="text" id="card-name" name="card_name" required>
            <span class="error" id="card-name-error"></span>
        </div>

        <div class="form-group">
            <label for="card-number">Card Number:</label>
            <input type="text" id="card-number" name="card_number" required>
            <span class="error" id="card-number-error"></span>
        </div>

        <div class="form-group">
            <label for="expiry-date">Expiry Date:</label>
            <input type="text" id="expiry-date" name="expiry_date" placeholder="MM/YY" required>
            <span class="error" id="expiry-date-error"></span>
        </div>

        <div class="form-group">
            <label for="cvv">CVV:</label>
            <input type="password" id="cvv" name="cvv" required>
            <span class="error" id="cvv-error"></span>
        </div>

        <div class="form-group">
            <label for="amount">Amount:</label>
            <input type="text" id="amount" name="amount" value="<?php echo htmlspecialchars($amount); ?>" readonly>
        </div>

        <div class="form-group">
            <label for="billing-address">Billing Address:</label>
            <input type="text" id="billing-address" name="billing_address" required>
            <span class="error" id="billing-address-error"></span>
        </div>

        <button type="submit" class="submit-btn">Pay Now</button>
    </form>
</div>

<script>
    document.getElementById('paymentForm').addEventListener('submit', function (event) {
        let isValid = true;

        // Get form values
        let cardNumber = document.getElementById('card-number').value;
        let expiryDate = document.getElementById('expiry-date').value;
        let cvv = document.getElementById('cvv').value;
        let cardholderName = document.getElementById('card-name').value;
        let billingAddress = document.getElementById('billing-address').value;

        // Get error spans
        let cardNumberError = document.getElementById('card-number-error');
        let expiryDateError = document.getElementById('expiry-date-error');
        let cvvError = document.getElementById('cvv-error');
        let cardNameError = document.getElementById('card-name-error');
        let billingAddressError = document.getElementById('billing-address-error');

        // Reset previous error messages
        cardNumberError.textContent = '';
        expiryDateError.textContent = '';
        cvvError.textContent = '';
        cardNameError.textContent = '';
        billingAddressError.textContent = '';

        // Validate Card Number
        if (!/^\d{16}$/.test(cardNumber)) {
            cardNumberError.textContent = 'Please enter a valid 16-digit card number.';
            isValid = false;
        }

        // Validate Expiry Date (MM/YY format)
        if (!/^(0[1-9]|1[0-2])\/\d{2}$/.test(expiryDate)) {
            expiryDateError.textContent = 'Please enter a valid expiry date in MM/YY format.';
            isValid = false;
        } else {
            // Check if expiry date is in the past
            let parts = expiryDate.split('/');
            let expMonth = parseInt(parts[0], 10);
            let expYear = parseInt('20' + parts[1], 10); // Convert YY to YYYY format

            let currentYear = new Date().getFullYear();
            let currentMonth = new Date().getMonth() + 1; // Months are 0-based

            if (expYear < currentYear || (expYear === currentYear && expMonth < currentMonth)) {
                expiryDateError.textContent = 'Expiry date cannot be in the past.';
                isValid = false;
            }
        }

        // Validate CVV
        if (!/^\d{3}$/.test(cvv)) {
            cvvError.textContent = 'Please enter a valid 3-digit CVV.';
            isValid = false;
        }

        // Validate Cardholder Name
        if (cardholderName.trim() === '') {
            cardNameError.textContent = 'Please enter the cardholder name.';
            isValid = false;
        }

        // Validate Billing Address
        if (billingAddress.trim() === '') {
            billingAddressError.textContent = 'Please enter the billing address.';
            isValid = false;
        }

        if (!isValid) {
            event.preventDefault(); // Prevent form submission if validation fails
        }
    });
</script>

</body>
</html>
