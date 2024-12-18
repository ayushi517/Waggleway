<?php
session_start();
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Initialize variables and error messages
$card_number = $expiry_date = $cvv = $billing_address = $amount = $booking_id = $service_type = $card_name = "";
$card_number_err = $expiry_date_err = $cvv_err = $billing_address_err = $amount_err = $booking_id_err = $service_type_err = $card_name_err = "";

// Add debugging log
error_log("POST Data: " . print_r($_POST, true));

// Process form submission
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Cardholder Name
    if (empty(trim($_POST["card_name"]))) {
        $card_name_err = "Please enter the cardholder's name.";
    } else {
        $card_name = trim($_POST['card_name']);
        error_log("Cardholder Name: " . $card_name);
    }

    // Card Number
    if (empty(trim($_POST["card_number"]))) {
        $card_number_err = "Please enter your card number.";
    } elseif (!preg_match("/^\d{16}$/", trim($_POST["card_number"]))) {
        $card_number_err = "Invalid card number format.";
    } else {
        $card_number = htmlspecialchars(trim($_POST["card_number"]));
        error_log("Card Number: " . $card_number);
    }

    // Expiry Date
   // Expiry Date
if (empty(trim($_POST["expiry_date"]))) {
    $expiry_date_err = "Please enter the expiry date.";
} elseif (!preg_match("/^(0[1-9]|1[0-2])\/\d{2}$/", trim($_POST["expiry_date"]))) {
    $expiry_date_err = "Invalid expiry date format.";
} else {
    $expiry_date = htmlspecialchars(trim($_POST["expiry_date"]));
    
    // Extract the month and year from the expiry date
    list($month, $year) = explode("/", $expiry_date);
    $currentYear = date("y"); // Last two digits of the current year
    $currentMonth = date("m"); // Current month

    // Check if the expiry year is less than the current year
    if ($year < $currentYear || ($year == $currentYear && $month < $currentMonth)) {
        $expiry_date_err = "The expiry date cannot be in the past.";
    } else {
        error_log("Expiry Date: " . $expiry_date);
    }
}


    // CVV
    if (empty(trim($_POST["cvv"]))) {
        $cvv_err = "Please enter your CVV.";
    } elseif (!preg_match("/^\d{3}$/", trim($_POST["cvv"]))) {
        $cvv_err = "Invalid CVV format.";
    } else {
        $cvv = htmlspecialchars(trim($_POST["cvv"]));
        error_log("CVV: " . $cvv);
    }

    // Billing Address
    if (empty(trim($_POST["billing_address"]))) {
        $billing_address_err = "Please enter your billing address.";
    } else {
        $billing_address = htmlspecialchars(trim($_POST["billing_address"]));
        error_log("Billing Address: " . $billing_address);
    }

    // Amount
    if (empty(trim($_POST["amount"]))) {
        $amount_err = "Please enter the amount.";
    } elseif (!preg_match("/^\d+(\.\d{2})?$/", trim($_POST["amount"]))) {
        $amount_err = "Invalid amount format.";
    } else {
        $amount = htmlspecialchars(trim($_POST["amount"]));
        error_log("Amount: " . $amount);
    }

    // Booking ID
    if (empty(trim($_POST["booking_id"]))) {
        $booking_id_err = "Booking ID is missing.";
    } else {
        $booking_id = intval(trim($_POST["booking_id"]));
        error_log("Booking ID: " . $booking_id);
    }

    // Service Type
    if (empty(trim($_POST["service_type"]))) {
        $service_type_err = "Service type is missing.";
    } else {
        $service_type = htmlspecialchars(trim($_POST["service_type"]));
        error_log("Service Type: " . $service_type);
    }

    // Check if there are any errors
    if (empty($card_name_err) && empty($card_number_err) && empty($expiry_date_err) && empty($cvv_err) && empty($billing_address_err) && empty($amount_err) && empty($booking_id_err) && empty($service_type_err)) {
        // Database connection
        $servername = "localhost"; // Adjust with your server info
        $username = "root"; // Adjust with your DB username
        $password = ""; // Adjust with your DB password
        $dbname = "waggleway"; // Your database name

        // Create connection
        $conn = new mysqli($servername, $username, $password, $dbname);

        // Check connection
        if ($conn->connect_error) {
            error_log("Connection failed: " . $conn->connect_error);
            die("Connection failed: " . $conn->connect_error);
        }

        // Prepare an insert statement
        $sql = "INSERT INTO payments (card_name, card_number, expiry_date, cvv, billing_address, amount, booking_id, service_type, payment_status) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)";

        if ($stmt = $conn->prepare($sql)) {
            // Bind variables to the prepared statement as parameters
            $payment_status = "Completed"; // Set status before binding
            $stmt->bind_param("sssssssss", $card_name, $card_number, $expiry_date, $cvv, $billing_address, $amount, $booking_id, $service_type, $payment_status);
            
            // Execute the statement
            if ($stmt->execute()) {
                error_log("Payment successfully inserted into database.");
                
                // Clear the amount from session
                unset($_SESSION['amount']);
                
                // Redirect to a confirmation page
                $redirectUrl = "../pages/thankyou.html?booking_id=" . urlencode($booking_id) . "&service_type=" . urlencode($service_type) . "&payment_status=" . urlencode($payment_status);
                header("Location: " . $redirectUrl);
                exit();
            } else {
                error_log("Execute failed: " . $stmt->error);
                $_SESSION['payment_errors'] = "Something went wrong. Please try again later.";
                exit();
            }
        } else {
            error_log("SQL preparation failed: " . $conn->error);
            $_SESSION['payment_errors'] = "Could not prepare the SQL statement.";
            exit();
        }
    } else {
        // Store error messages in session and redirect back to the payment page
        $_SESSION['payment_errors'] = [
            'card_name_err' => $card_name_err,
            'card_number_err' => $card_number_err,
            'expiry_date_err' => $expiry_date_err,
            'cvv_err' => $cvv_err,
            'billing_address_err' => $billing_address_err,
            'amount_err' => $amount_err,
            'booking_id_err' => $booking_id_err,
            'service_type_err' => $service_type_err,
        ];

        error_log("Validation errors occurred: " . print_r($_SESSION['payment_errors'], true));
        
        // Handle the redirect back to the payment page
       
        exit();
    }
    
}

// Close connection
$conn->close();
?>
