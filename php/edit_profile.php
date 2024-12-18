<?php
session_start();
$servername = "localhost"; // Replace with your server name
$username = "root"; // Replace with your MySQL username
$password = ""; // Replace with your MySQL password
$dbname = "waggleway"; // Replace with your database name

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Fetch user data
$userid = $_SESSION['userid']; // Assuming user ID is stored in session after login
$query = "SELECT userid, username, email FROM users WHERE userid = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param("i", $userid);
$stmt->execute();
$result = $stmt->get_result();
$user = $result->fetch_assoc();

// Check if form is submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = $_POST['username'];
    $email = $_POST['email'];

    // Update user data
    $updateQuery = "UPDATE users SET username = ?, email = ? WHERE userid = ?";
    $updateStmt = $conn->prepare($updateQuery);
    $updateStmt->bind_param("ssi", $username, $email, $userid);
    if ($updateStmt->execute()) {
        $message = "Details updated successfully!";
    } else {
        $message = "Error updating details: " . $conn->error;
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Profile</title>
    <link rel="stylesheet" href="../css/edit_profile.css"> <!-- Your CSS file -->

</head>
<body>
    <div class="edit-profile-container">
        <h1>Edit Profile</h1>

        <?php if (isset($message)) { ?>
            <p><?php echo htmlspecialchars($message); ?></p>
        <?php } ?>

        <form action="../php/edit_profile.php" method="post">
            <label for="username">Username:</label>
            <input type="text" id="username" name="username" value="<?php echo htmlspecialchars($user['username']); ?>" required>
            <br>
            <label for="email">Email:</label>
            <input type="email" id="email" name="email" value="<?php echo htmlspecialchars($user['email']); ?>" required>
            <br>
            <input type="submit" value="Update Details">
        </form>

        <a href="../php/profile.php">Back to Profile</a>
    </div>
</body>
</html>
