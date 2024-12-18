document.addEventListener("DOMContentLoaded", function() {
    fetch('./php/checkLoginStatus.php')
        .then(response => response.json())
        .then(data => {
            console.log('Fetched data:', data); // Debugging line
            const accountDropdown = document.getElementById("accountDropdown");
            if (data.loggedIn) {
                // User is logged in, show account menu
                accountDropdown.innerHTML = `
                    <a href="./php/profile.php">My Profile</a>
                    <a href="./php/subscription.php">My Subscriptions</a>
                    <a href="./php/logout.php" id="logout">Logout</a>
                `;

                document.getElementById("logout").addEventListener("click", function() {
                    fetch('./php/logout.php') // Adjust path if needed
                        .then(() => {
                            window.location.href = 'index.html'; // Redirect after logout
                        })
                        .catch(error => console.error('Logout Error:', error));
                });
            } else {
                // User is not logged in, show login and register links
                accountDropdown.innerHTML = `
                    <a href="./pages/login.html">Login</a>
                    <a href="./pages/register.html">Register</a>
                `;
            }
        })
        .catch(error => console.error('Fetch Error:', error));
});

