<?php
session_start();

// Check if user is logged in
if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true) {
    header("Location: ../pages/login.html?message=Please%20log%20in%20to%20access%20this%20page");
    exit();
}

// Retrieve the booking ID from query parameters
$booking_id = isset($_GET['booking_id']) ? $_GET['booking_id'] : null;

// Retrieve the username from session
$username = $_SESSION['username'] ?? 'Guest'; // Use 'Guest' as a fallback if username is not set
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Booking Confirmation - Waggle Way</title>
    <link rel="stylesheet" href="../css/confirmation.css">
</head>
<body>
    <div class="confirmation-container">
        <h1>Booking Confirmed</h1>
        <p>Thank you, <?php echo htmlspecialchars($username); ?>! Your booking has been received.</p>

        <!-- Button to proceed to payment -->
        <a href="#" id="paymentLink" class="btn btn-primary">Proceed to Payment</a>
    </div>

    <script>
        // Retrieve the booking ID from PHP
        const bookingId = "<?php echo htmlspecialchars($booking_id); ?>";
        
        // Function to redirect to the payment page after a delay
        function redirectToPayment() {
            if (bookingId) {
                const paymentUrl = `../pages/payment_gateway.php?booking_id=${encodeURIComponent(bookingId)}`;
                window.location.href = paymentUrl;
            } else {
                alert("Booking ID is missing.");
            }
        }

        // Set a timeout to redirect after 5 seconds (5000 milliseconds)
        setTimeout(redirectToPayment, 5000); // Adjust the delay as needed
    </script>
</body>
</html>
