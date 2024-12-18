<?php
session_start(); // Start the session

// Destroy the session
session_unset();
session_destroy();

// Redirect to the login page or homepage
header("Location: ../pages/login.html");
exit();
