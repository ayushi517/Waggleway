<?php
$servername = "localhost";  // Your server name
$username = "root";         // Your database username
$password = "";             // Your database password (leave blank for XAMPP)
$dbname = "waggleway";      // Your database name

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = $_POST['email'];
    $new_password = $_POST['new_password'];
    $confirm_password = $_POST['confirm_password'];

    // Check if passwords match
    if ($new_password !== $confirm_password) {
        // Redirect with an error message
        header('Location: forgot_password.html?message=' . urlencode('Passwords do not match!'));
        exit();
    }

    // Check if email exists in the users table
    $query = "SELECT * FROM users WHERE email = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        // If email exists, hash the new password
        $hashed_password = password_hash($new_password, PASSWORD_DEFAULT);

        // Update the password in the users table
        $update_query = "UPDATE users SET password = ? WHERE email = ?";
        $stmt = $conn->prepare($update_query);
        $stmt->bind_param("ss", $hashed_password, $email);
        
        if ($stmt->execute()) {
            // Redirect to login page with success message
            header('Location:../pages/login.html?message=' . urlencode('Password has been successfully updated!'));
            exit();
        } else {
            // Redirect with an error message
            header('Location:../pages/forgot_password.html?message=' . urlencode('Error updating password. Please try again.'));
            exit();
        }
    } else {
        // Redirect with an error message
        header('Location: ../pages/forgot_password.html?message=' . urlencode('No account found with that email.'));
        exit();
    }
}
?>
