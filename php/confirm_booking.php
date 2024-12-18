<?php
session_start();

// Check if booking details are available
if (!isset($_SESSION['booking_details'])) {
    header("Location: bookingForm.php");
    exit();
}

// Retrieve booking details from the session
$bookingDetails = $_SESSION['booking_details'];
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Booking Confirmation</title>
    <link rel="stylesheet" href="../css/style.css">
    <link rel="stylesheet" href="../css/confirmation.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
</head>
<body>
    <header class="header">
        <a href="../index.html" class="logos"><i class="fas fa-dog"></i>Waggle<span>Way</span></a>
        <nav class="nav-bar">
            <!-- Navigation links -->
        </nav>
    </header>

    <div class="confirmation-container">
        <h2>Booking Confirmation</h2>
        <p>Thank you for booking with us!</p>
        <p>Here are the details of your booking:</p>
        <ul>
            <li><strong>Service ID:</strong> <?php echo htmlspecialchars($bookingDetails['service_id']); ?></li>
            <li><strong>Service Name:</strong> <?php echo htmlspecialchars($bookingDetails['service_name']); ?></li>
            <li><strong>Service Date:</strong> <?php echo htmlspecialchars($bookingDetails['service_date']); ?></li>
            <li><strong>Service Time:</strong> <?php echo htmlspecialchars($bookingDetails['service_time']); ?></li>
        </ul>
        <p>Your booking is being processed. Please proceed to the payment page to complete the transaction.</p>
        <a href="payment_confirmation.php" class="btn">Proceed to Payment</a>
    </div>

    <footer class="footer">
        <!-- Footer content -->
    </footer>
</body>
</html>
