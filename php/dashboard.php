<?php
session_start();
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Debugging session variables
if (!isset($_SESSION['userid']) || !isset($_SESSION['username'])) {
    echo "Session variables are not set.";
    exit();
}

echo "Session Variables:<br>";
echo "User ID: " . htmlspecialchars($_SESSION['userid']) . "<br>";
echo "Username: " . htmlspecialchars($_SESSION['username']) . "<br>";
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>WaggleWay - Dashboard</title>
    <link rel="stylesheet" href="../css/style.css">
    <link rel="stylesheet" href="../css/services.css">
    <link rel="stylesheet" href="../css/contact.css">
    <link rel="stylesheet" href="../css/register.css">
    <link rel="stylesheet" href="../css/dashboard.css">
    <script src="../js/login.js" defer></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <script>
    document.addEventListener('DOMContentLoaded', function() {
    const btn = document.querySelector('.btn');
    if (btn) {
        btn.addEventListener('click', function() {
            window.location.href = '../pages/services.html'; // Redirect to services page
        });
    }
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

    <main>
        <div class="welcome-card">
            <h2>Welcome back, <?php echo htmlspecialchars($_SESSION['userid']); ?>!</h2>
            <p>We're thrilled to have you with us again. Explore our services and take advantage of everything we have to offer. If you need any assistance, don't hesitate to contact our support team.</p>
            <button class="btn">Explore Services</button>
        </div>
    </main>
    
</body>
</html>
