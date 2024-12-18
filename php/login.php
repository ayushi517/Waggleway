<?php
session_start();

// Check if there's a login message to display
if (isset($_SESSION['login_message'])) {
    // Use JavaScript to show an alert with the message
    echo '<script type="text/javascript">';
    echo 'alert("' . $_SESSION['login_message'] . '");';
    echo '</script>';
    
    // Clear the message after displaying it
    unset($_SESSION['login_message']);
}

// Database connection parameters
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "waggleway";

// Create a connection to the database
$conn = new mysqli($servername, $username, $password, $dbname);

// Check if the connection is successful
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error); 
}

// Initialize variable for alert message
$alertMessage = '';

// Check if the form is submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Retrieve and sanitize form data
    $userId = $conn->real_escape_string($_POST['userid'] ?? '');
    $username = $conn->real_escape_string($_POST['username'] ?? '');
    $email = $conn->real_escape_string($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';
    $confirmPassword = $_POST['confirm_password'] ?? '';

    // Validate input
    if (empty($userId) || empty($username) || empty($email) || empty($password) || empty($confirmPassword)) {
        $alertMessage = "All fields are required!";
    } elseif ($password !== $confirmPassword) {
        $alertMessage = "Passwords do not match!";
    } else {
        // Query to fetch the user's hashed password from the database
        $sql = "SELECT * FROM users WHERE userid = ? AND username = ? AND email = ?";
        $stmt = $conn->prepare($sql);
        if (!$stmt) {
            $alertMessage = "Statement preparation failed: " . $conn->error;
        } else {
            $stmt->bind_param("sss", $userId, $username, $email);
            $stmt->execute();
            $result = $stmt->get_result();

            if ($result->num_rows > 0) {
                // User found, verify the password
                $row = $result->fetch_assoc();
                $hashedPassword = $row['password'];

                if (password_verify($password, $hashedPassword)) {
                    // Password is correct, set session variables
                    $_SESSION['userid'] = $userId;
                    $_SESSION['username'] = $username;
                    $_SESSION['loggedin'] = true;

                    // Check if a redirect URL is set
                    if (isset($_GET['redirect'])) {
                        $redirect_url = urldecode($_GET['redirect']);
                        
                        // Ensure the URL starts with a valid path (e.g., within your domain)
                        if (strpos($redirect_url, '/') === 0) {
                            header("Location: " . $redirect_url);
                        } else {
                            header("Location: ../php/dashboard.php"); // Fallback to a safe default page
                        }
                    } else {
                        header("Location: ../php/dashboard.php"); // Default page after login
                    }
                    exit();
                } else {
                    $alertMessage = "Invalid credentials!";
                }
            } else {
                $alertMessage = "Invalid credentials!";
            }
        }
    }
    // Redirect with message query parameter
    header("Location: ../pages/login.html?message=" . urlencode($alertMessage));
    exit();
}

// Close the database connection
$conn->close();
?>
