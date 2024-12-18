document.addEventListener('DOMContentLoaded', function() {
    function checkLoginStatus() {
        fetch('../php/checkLoginStatus.php')
            .then(response => response.json())
            .then(data => {
                const accountDropdown = document.getElementById('accountDropdown');
                if (accountDropdown) {
                    if (data.loggedIn) {
                        accountDropdown.innerHTML = `
                            <a href="../php/profile.php">My Profile</a>
                            <a href="../php/subscription.php">My Subscriptions</a>
                            <a href="../php/logout.php" id="logoutButton">Logout</a>
                        `;

                        // Add logout functionality
                        document.getElementById('logoutButton').addEventListener('click', function() {
                            fetch('../php/logout.php', { 
                                method: 'POST'
                            })
                            .then(response => {
                                if (response.ok) {
                                    window.location.href = '../pages/login.html'; // Redirect to login page or wherever appropriate
                                }
                            })
                            .catch(error => {
                                console.error('Error during logout:', error);
                            });
                        });
                    } else {
                        accountDropdown.innerHTML = `
                            <a href="../pages/login.html">Login</a>
                            <a href="../pages/register.html">Register</a>
                        `;
                    }
                } else {
                    console.error('Element with ID "accountDropdown" not found.');
                }
            })
            .catch(error => {
                console.error('Error checking login status:', error);
            });
    }

    checkLoginStatus();
});
