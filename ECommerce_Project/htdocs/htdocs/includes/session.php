<?php
session_start();

// Function to protect private pages (Account, Dashboard, etc.)
function protect_page() {
    if (!isset($_SESSION['user_id'])) {
        $_SESSION['redirect_url'] = $_SERVER['REQUEST_URI']; 
        header("Location: signup.php");
        exit();
    }
}

// Function to redirect logged-in users away from Login/Signup
function redirect_if_logged_in() {
    if (isset($_SESSION['user_id'])) {
        header("Location: profile.php");
        exit();
    }
}
?>